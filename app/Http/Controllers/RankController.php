<?php

    namespace App\Http\Controllers;

    use App\Http\Requests\Rank\RankCreateRequest;
    use App\Http\Resources\RankResource;
    use App\Models\Domain;
    use App\Models\Language;
    use App\Models\Location;
    use App\Models\Rank;
    use Illuminate\Http\Request;
    use GuzzleHttp\Client;
    use Illuminate\Support\Facades\Log;
    use Illuminate\Validation\ValidationException;

    class RankController extends Controller
    {
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
            $locationModel = Location::query()->findOrFail($validated['location_id']);
            $languageModel = Language::query()->findOrFail($validated['language_id']);

            $client = new Client([
                'timeout' => 60,
                'connect_timeout' => 10,
            ]);

            $depth = config('services.dataforseo.depth');

            try {
                $postResponse = $client->request('POST', 'https://api.dataforseo.com/v3/serp/google/organic/task_post', [
                    'auth' => [config('services.dataforseo.api_login'), config('services.dataforseo.api_key')],
                    'json' => [
                        [
                            'keyword' => (string) $validated['keyword'],
                            'location_code' => (int) $locationModel->location_code,
                            'language_code' => (string) $languageModel->language_code,
                            'depth' => $depth,
                        ],
                    ],
                ]);

                $postData = json_decode($postResponse->getBody()->getContents(), true);

                if (!is_array($postData) || (int)($postData['status_code'] ?? 0) !== 20000) {
                    throw ValidationException::withMessages([
                        'api' => [(string)($postData['status_message'] ?? 'DataForSEO: failed to create task.')],
                    ]);
                }

                $taskId = $postData['tasks'][0]['id'] ?? null;
                if (!$taskId) {
                    throw ValidationException::withMessages([
                        'api' => ['DataForSEO: task id not returned.'],
                    ]);
                }

                $rank = Rank::create([
                    'domain_id' => $validated['domain_id'],
                    'location_id' => $validated['location_id'],
                    'language_id' => $validated['language_id'],

                    'keyword' => (string) $validated['keyword'],

                    'task_id' => (string) $taskId,
                    'status' => 'queued',
                ]);

                if ($request->expectsJson()) {
                    return (new RankResource($rank))->response()->setStatusCode(201);
                }

                return redirect()
                    ->route('ranks.index')
                    ->with('success', 'Task created. Click "Get results" when it is ready.');
            } catch (ValidationException $e) {
                throw $e; // піде в форму через $errors
            } catch (\Throwable $e) {
                throw ValidationException::withMessages([
                    'api' => ['Request to DataForSEO failed. Please try again later.'],
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
                return redirect()
                    ->route('ranks.index')
                    ->with('error', 'This rank has no task id.');
            }

            $rank->update(['status' => 'running', 'error_message' => null]);

            $client = new Client([
                'timeout' => 60,
                'connect_timeout' => 10,
            ]);

            try {
                $response = $client->request('GET', "https://api.dataforseo.com/v3/serp/google/organic/task_get/regular/{$rank->task_id}", [
                    'auth' => [config('services.dataforseo.api_login'), config('services.dataforseo.api_key')],
                ]);

                $data = json_decode($response->getBody()->getContents(), true);

                if (!is_array($data) || (int)($data['status_code'] ?? 0) !== 20000) {
                    $msg = (string)($data['status_message'] ?? 'DataForSEO: failed to get task result.');
                    $rank->update(['status' => 'failed', 'error_message' => $msg]);
                    return redirect()->route('ranks.index')->with('error', $msg);
                }

                $task = $data['tasks'][0] ?? [];
                $items = $task['result'][0]['items'] ?? null;

                // Ще не готово (task_post повертає 20100, а тут може бути result null деякий час)
                if (!is_array($items)) {
                    $rank->update(['status' => 'queued']);
                    return redirect()
                        ->route('ranks.index')
                        ->with('info', 'Result is not ready yet. Try again in a moment.');
                }

                // Parse results
                $domain_value = (string) optional($rank->domain)->domain;

                $ranks = [];
                foreach ($items as $item) {
                    if (!is_array($item)) continue;

                    if ((string)($item['type'] ?? '') !== 'organic') continue;

                    $itemDomain = (string)($item['domain'] ?? '');
                    $abs = $item['rank_absolute'] ?? null;

                    if ($domain_value !== '' && $itemDomain !== '' && stripos($itemDomain, $domain_value) !== false && is_numeric($abs)) {
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

                    return redirect()
                        ->route('ranks.index')
                        ->with('info', 'Result ready, but the website was not found in top results.');
                }

                $rank_min = min($ranks);
                $rank_max = max($ranks);
                $rank_avg = round(array_sum($ranks) / count($ranks), 2);

                $rank->update([
                    'status' => 'done',
                    'rank_min' => $rank_min,
                    'rank_max' => $rank_max,
                    'rank_avg' => $rank_avg,
                    'results_fetched_at' => now(),
                    'error_message' => null,
                ]);

                return redirect()
                    ->route('ranks.index')
                    ->with('success', 'Results fetched and saved.');
            } catch (\Throwable $e) {
                $rank->update(['status' => 'failed', 'error_message' => 'Fetch failed.']);
                return redirect()
                    ->route('ranks.index')
                    ->with('error', 'Failed to fetch results. Try again later.');
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

        /**
         * @param Request $request
         * @return \Illuminate\Http\RedirectResponse
         */
        public function refreshFromService(Request $request)
        {
            $ranks = $this->getRanks();

            if (empty($ranks)) {
                return redirect()
                    ->route('ranks.index')
                    ->with('error', 'Failed to fetch ranks from service.');
            }

            $now = now();
            $rows = [];

            foreach ($ranks as $item) {
                $rows[] = [
                    'rank_code' => $item['rank_code'] ?? null,
                    'rank_name' => $item['rank_name'] ?? null,
                    'rank_code_parent' => $item['rank_code_parent'] ?? null,
                    'country_iso_code' => $item['country_iso_code'] ?? null,
                    'rank_type' => $item['rank_type'] ?? null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            // Приберемо биті рядки без обов'язкових полів
            $rows = array_values(array_filter($rows, function (array $row) {
                return $row['rank_code'] !== null
                    && $row['rank_name'] !== null
                    && $row['country_iso_code'] !== null;
            }));

            if (empty($rows)) {
                return redirect()
                    ->route('ranks.index')
                    ->with('error', 'Service returned no valid rank rows.');
            }

            $chunk_size = 1000;
            foreach (array_chunk($rows, $chunk_size) as $chunk) {
                Rank::upsert(
                    $chunk,
                    ['rank_code'],
                    ['rank_name', 'rank_code_parent', 'country_iso_code', 'rank_type', 'updated_at']
                );
            }

            return redirect()
                ->route('ranks.index')
                ->with('success', 'Ranks refreshed: ' . count($rows) . ' rows processed.');
        }

        /**
         * @return array
         */
        private function getRanks(): array
        {
            $client = new Client([
                'timeout' => 30,
                'connect_timeout' => 10,
            ]);

            $api_key = config('services.dataforseo.api_key');
            $api_login = config('services.dataforseo.api_login');

            try {
                $response = $client->get('https://api.dataforseo.com/v3/serp/google/ranks', [
                    'auth' => [$api_login, $api_key],
                ]);

                $payload = json_decode($response->getBody()->getContents(), true);

                return $payload['tasks'][0]['result'] ?? [];
            } catch (\Throwable $e) {
                Log::error('Locales getting error: ' . $e->getMessage());

                return [];
            }
        }

    }