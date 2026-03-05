<?php

    namespace App\Http\Controllers;

    use App\Contracts\RankProviderInterface;
    use App\DTO\Rank\RankQuery;
    use App\Http\Requests\Rank\RankCreateRequest;
    use App\Models\Domain;
    use App\Models\Language;
    use App\Models\Location;
    use App\Models\Rank;
    use App\Models\RankDetail;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\DB;
    use Illuminate\Validation\ValidationException;

    class RankController extends Controller
    {
        public function __construct(private RankProviderInterface $provider)
        {
        }

        /**
         * @param Request $request
         * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Illuminate\View\View
         */
        public function index(Request $request)
        {
            $ranks = Rank::query()
                ->with(['domain', 'location', 'language'])
                ->withCount(['rankDetails as matches_count' => function ($q) {
                    $q->where('is_match', true);
                }])
                ->latest()
                ->paginate(10)
                ->withQueryString();

            return view('pages.ranks.index', compact('ranks'));
        }

        /**
         * @param Request $request
         * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\View\View
         */
        public function create(Request $request)
        {
            $domains = Domain::query()
                ->where('user_id', $request->user()->id)
                ->orderBy('domain')
                ->get(['id', 'domain']);

            return view('pages.ranks.form', compact('domains'));
        }

        /**
         * @param RankCreateRequest $request
         * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
         */
        public function store(RankCreateRequest $request)
        {
            $validated = $request->validated();

            $domainModel = Domain::query()->findOrFail($validated['domain_id']);
            $location_model = Location::query()->findOrFail($validated['location_id']);
            $language_model = Language::query()->findOrFail($validated['language_id']);

            try {
                $query = new RankQuery(
                    keyword: (string) $validated['keyword'],
                    location_code: (int) $location_model->location_code,
                    language_code: (string) $language_model->language_code,
                    depth: (int) config('services.dataforseo.depth')
                );

                $task = $this->provider->createTask($query);

                $rank = Rank::create([
                    'domain_id' => $validated['domain_id'],
                    'location_id' => $validated['location_id'],
                    'language_id' => $validated['language_id'],
                    'keyword' => (string) $validated['keyword'],
                    'task_id' => $task->task_id,
                    'status' => 'queued',
                    'error_message' => null,
                ]);

                return redirect()
                    ->route('ranks.index')
                    ->with('success', 'Task created. Click "Get results" when it is ready.');
            } catch (ValidationException $e) {
                throw $e; // піде в форму як $errors
            } catch (\Throwable $e) {
                throw ValidationException::withMessages([
                    'api' => ['Failed to create rank task. Please try again later.'],
                ]);
            }
        }

        /**
         * @param Request $request
         * @param Rank $rank
         * @return \Illuminate\Http\RedirectResponse
         */
        public function fetchResults(Request $request, Rank $rank)
        {
            if (!$rank->task_id) {
                return redirect()->route('ranks.index')->with('error', 'This rank has no task id.');
            }

            $rank->load('domain');

            $rank->update(['status' => 'running', 'error_message' => null]);

            try {
                $result = $this->provider->fetchTaskResult($rank->task_id);

                $error_message = $result->error_message ?? null;

                if ($error_message) {
                    $rank->update(['status' => 'failed', 'error_message' => $error_message]);
                    return redirect()->route('ranks.index')->with('error', $error_message);
                }

                if (!($result->ready ?? false)) {
                    $rank->update(['status' => 'queued']);
                    return redirect()->route('ranks.index')->with('info', 'Result is not ready yet. Try again in a moment.');
                }

                $items = $result->items ?? [];
                if (!is_array($items)) {
                    $rank->update(['status' => 'queued']);
                    return redirect()->route('ranks.index')->with('info', 'Result is not ready yet. Try again in a moment.');
                }

                $now = now();
                $domain_value = (string) ($rank->domain->domain ?? '');

                $rows = [];
                $organic_matches = [];

                foreach ($items as $item) {
                    if (!is_array($item)) {
                        continue;
                    }

                    $type = (string) ($item['type'] ?? '');
                    $item_domain = (string) ($item['domain'] ?? '');
                    $rank_absolute = $item['rank_absolute'] ?? null;

                    $is_match = false;
                    $match_reason = null;

                    if ($type === 'organic' && $domain_value !== '' && $item_domain !== '' && stripos($item_domain, $domain_value) !== false) {
                        $is_match = true;
                        $match_reason = 'domain';
                    }

                    // 1) Готуємо запис для rank_details (зберігаємо ВСІ items з типом)
                    $rows[] = [
                        'rank_id' => $rank->id,
                        'type' => $type !== '' ? $type : 'unknown',
                        'rank_group' => isset($item['rank_group']) && is_numeric($item['rank_group']) ? (int)$item['rank_group'] : null,
                        'rank_absolute' => is_numeric($rank_absolute) ? (int)$rank_absolute : null,
                        'page' => isset($item['page']) && is_numeric($item['page']) ? (int)$item['page'] : null,
                        'domain' => $item_domain !== '' ? $item_domain : null,
                        'title' => isset($item['title']) ? (string)$item['title'] : null,
                        'description' => isset($item['description']) ? (string)$item['description'] : null,
                        'url' => isset($item['url']) ? (string)$item['url'] : null,
                        'breadcrumb' => isset($item['breadcrumb']) ? (string)$item['breadcrumb'] : null,
                        'is_match' => $is_match,
                        'match_reason' => $match_reason,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];

                    if ($is_match && is_numeric($rank_absolute)) {
                        $organic_matches[] = (int) $rank_absolute;
                    }

                    // 2) Для rank_min/max/avg беремо тільки organic + співпадіння домену
                    if ($type === 'organic' && $domain_value !== '' && $item_domain !== '' && stripos($item_domain, $domain_value) !== false) {
                        if (is_numeric($rank_absolute)) {
                            $organic_matches[] = (int) $rank_absolute;
                        }
                    }
                }

                DB::transaction(function () use ($rank, $rows, $organic_matches, $now) {
                    // Якщо перезапитуємо результати — перезаписуємо деталі
                    RankDetail::query()->where('rank_id', $rank->id)->delete();
                   
                    $chunkSize = 1000;
                    foreach (array_chunk($rows, $chunkSize) as $chunk) {
                        RankDetail::query()->insert($chunk);
                    }

                    $itemCount = count($rows);

                    if (empty($organic_matches)) {
                        $rank->update([
                            'status' => 'done',
                            'items_count' => $itemCount,
                            'rank_min' => null,
                            'rank_max' => null,
                            'rank_avg' => null,
                            'results_fetched_at' => $now,
                            'error_message' => 'Website not found in organic results.',
                        ]);
                        return;
                    }

                    $rank_min = min($organic_matches);
                    $rank_max = max($organic_matches);
                    $rank_avg = round(array_sum($organic_matches) / count($organic_matches), 2);

                    $rank->update([
                        'status' => 'done',
                        'items_count' => $itemCount,
                        'rank_min' => $rank_min,
                        'rank_max' => $rank_max,
                        'rank_avg' => $rank_avg,
                        'results_fetched_at' => $now,
                        'error_message' => null,
                    ]);
                });

                return redirect()
                    ->route('ranks.index')
                    ->with('success', 'Results fetched and saved.');
            } catch (\Throwable $e) {
                $rank->update(['status' => 'failed', 'error_message' => 'Fetch failed.']);
                return redirect()->route('ranks.index')->with('error', 'Failed to fetch results. Error: ' . $e->getMessage() . '. Try again later.');
            }
        }

        public function show(Request $request, Rank $rank)
        {
            $rank->load(['domain', 'location', 'language']);

            $type = $request->query('type');

            $detailsQuery = RankDetail::query()
                ->where('rank_id', $rank->id)
                ->orderByRaw('rank_absolute is null, rank_absolute asc')
                ->orderBy('id');

            if (is_string($type) && $type !== '') {
                $detailsQuery->where('type', $type);
            }

            $details = $detailsQuery->paginate(50)->withQueryString();

            $types = RankDetail::query()
                ->where('rank_id', $rank->id)
                ->select('type')
                ->groupBy('type')
                ->orderBy('type')
                ->pluck('type');

            return view('pages.ranks.show', compact('rank', 'details', 'types', 'type'));
        }

        public function edit(Request $request, Rank $rank)
        {
            //
            return null;
        }

        public function update(Request $request, Rank $rank)
        {
            //
            return null;
        }

        /**
         * @param Request $request
         * @param Rank $rank
         * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
         */
        public function destroy(Request $request, Rank $rank)
        {
            $rank->delete();

            return redirect()
                ->route('ranks.index')
                ->with('success', 'Rank deleted successfully.');
        }

    }