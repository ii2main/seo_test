<?php

    namespace App\Http\Controllers;

    use App\Contracts\RankProviderInterface;
    use App\DTO\Rank\RankQuery;
    use App\Http\Requests\Rank\RankCreateRequest;
    use App\Http\Resources\RankResource;
    use App\Models\Domain;
    use App\Models\Language;
    use App\Models\Location;
    use App\Models\Rank;
    use Illuminate\Http\Request;
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
                ->latest()
                ->paginate(10)
                ->withQueryString();

            if ($request->expectsJson()) {
                return RankResource::collection($ranks);
            }

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

                if ($request->expectsJson()) {
                    return (new RankResource($rank))->response()->setStatusCode(201);
                }

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

            $rank->update(['status' => 'running', 'error_message' => null]);

            try {
                $result = $this->provider->fetchTaskResult($rank->task_id);

                if ($result->error_message) {
                    $rank->update(['status' => 'failed', 'error_message' => $result->error_message]);
                    return redirect()->route('ranks.index')->with('error', $result->error_message);
                }

                if (!$result->ready) {
                    $rank->update(['status' => 'queued']);
                    return redirect()->route('ranks.index')->with('info', 'Result is not ready yet. Try again in a moment.');
                }

                $items = $result->items ?? [];
                $domainValue = (string) optional($rank->domain)->domain;

                $ranks = [];
                foreach ($items as $item) {
                    if (!is_array($item)) continue;
                    if ((string)($item['type'] ?? '') !== 'organic') continue;

                    $itemDomain = (string)($item['domain'] ?? '');
                    $abs = $item['rank_absolute'] ?? null;

                    if ($domainValue !== '' && $itemDomain !== '' && stripos($itemDomain, $domainValue) !== false && is_numeric($abs)) {
                        $ranks[] = (int) $abs;
                    }
                }

                if (empty($ranks)) {
                    $rank->update([
                        'status' => 'done',
                        'rank_min' => null,
                        'rank_max' => null,
                        'rank_avg' => null,
                        'results_fetched_at' => now(),
                        'error_message' => 'Website not found in top results.',
                    ]);

                    return redirect()->route('ranks.index')->with('info', 'Result ready, but the website was not found in top results.');
                }

                $rankMin = min($ranks);
                $rankMax = max($ranks);
                $rankAvg = round(array_sum($ranks) / count($ranks), 2);

                $rank->update([
                    'status' => 'done',
                    'rank_min' => $rankMin,
                    'rank_max' => $rankMax,
                    'rank_avg' => $rankAvg,
                    'results_fetched_at' => now(),
                    'error_message' => null,
                ]);

                return redirect()->route('ranks.index')->with('success', 'Results fetched and saved.');
            } catch (\Throwable $e) {
                $rank->update(['status' => 'failed', 'error_message' => 'Fetch failed.']);
                return redirect()->route('ranks.index')->with('error', 'Failed to fetch results. Try again later.');
            }
        }

        public function show(Request $request, Rank $rank)
        {
            //
            return null;
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

            if ($request->expectsJson()) {
                return response()->noContent();
            }

            return redirect()
                ->route('ranks.index')
                ->with('success', 'Rank deleted successfully.');
        }

    }