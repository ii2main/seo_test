<?php

    namespace App\Http\Controllers;

    use App\Http\Requests\Language\LanguageCreateRequest;
    use App\Http\Requests\Language\LanguageUpdateRequest;
    use App\Models\Language;
    use GuzzleHttp\Client;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Log;

    class LanguageController extends Controller
    {
        /**
         * @param Request $request
         * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\View\View
         */
        public function index(Request $request)
        {
            $languages = Language::query()
                ->latest()
                ->paginate(10)
                ->withQueryString();

            return view('pages.languages.index', compact('languages'));
        }

        /**
         * @param Request $request
         * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\View\View
         */
        public function create(Request $request)
        {
            return view('pages.languages.form');
        }

        /**
         * @param LanguageCreateRequest $request
         * @return \Illuminate\Http\RedirectResponse
         */
        public function store(LanguageCreateRequest $request)
        {
            $validated = $request->validated();

            $language = Language::create($validated);

            return redirect()
                ->route('languages.index')
                ->with('success', 'Language created successfully.');
        }

        /**
         * @param Request $request
         * @param Language $language
         * @return \Illuminate\Http\RedirectResponse
         */
        public function show(Request $request, Language $language)
        {

            return redirect()->route('languages.index');
        }

        /**
         * @param Request $request
         * @param Language $language
         * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\View\View
         */
        public function edit(Request $request, Language $language)
        {

            return view('pages.languages.form', compact('language'));
        }

        /**
         * @param LanguageUpdateRequest $request
         * @param Language $language
         * @return \Illuminate\Http\RedirectResponse
         */
        public function update(LanguageUpdateRequest $request, Language $language)
        {
            $validated = $request->validated();

            $language->update($validated);

            return redirect()
                ->route('languages.index')
                ->with('success', 'Language updated successfully.');
        }

        /**
         * @param Request $request
         * @param Language $language
         * @return \Illuminate\Http\RedirectResponse
         */
        public function destroy(Request $request, Language $language)
        {
            $language->delete();

            return redirect()
                ->route('languages.index')
                ->with('success', 'Language deleted successfully.');
        }

        /**
         * @param Request $request
         * @return \Illuminate\Http\RedirectResponse
         */
        public function refreshFromService(Request $request)
        {
            $languages = $this->getLanguages();

            if (empty($languages)) {
                return redirect()
                    ->route('languages.index')
                    ->with('error', 'Failed to fetch languages from service.');
            }

            $rows = [];

            foreach ($languages as $item) {
                $rows[] = [
                    'language_code' => $item['language_code'] ?? null,
                    'language_name' => $item['language_name'] ?? null,
                ];
            }

            // Filter out rows missing mandatory fields
            $rows = array_values(array_filter($rows, function (array $row) {
                return $row['language_code'] !== null && $row['language_name'] !== null;
            }));

            if (empty($rows)) {
                return redirect()
                    ->route('languages.index')
                    ->with('error', 'Service returned no valid language rows.');
            }

            $chunk_size = 1000;
            foreach (array_chunk($rows, $chunk_size) as $chunk) {
                Language::upsert(
                    $chunk,
                    ['language_code'],
                    ['language_name']
                );
            }

            return redirect()
                ->route('languages.index')
                ->with('success', 'Languages refreshed: ' . count($rows) . ' rows processed.');
        }

        /**
         * @return array
         */
        private function getLanguages(): array
        {
            $client = new Client([
                'timeout' => 30,
                'connect_timeout' => 10,
            ]);

            $apiKey = config('services.dataforseo.api_key');
            $apiLogin = config('services.dataforseo.api_login');

            try {
                $response = $client->get('https://api.dataforseo.com/v3/serp/google/languages', [
                    'auth' => [$apiLogin, $apiKey],
                ]);

                $payload = json_decode($response->getBody()->getContents(), true);

                return $payload['tasks'][0]['result'] ?? [];
            } catch (\Throwable $e) {
                Log::error('Getting languages error: ' . $e->getMessage()) . '. Try later';

                return [];
            }
        }

        /**
         * @param Request $request
         * @return \Illuminate\Http\JsonResponse
         */
        public function getForSelect(Request $request)
        {
            $q = trim((string) $request->query('q', ''));
            $perPage = 20;

            $query = Language::query()->orderBy('language_name');

            if ($q !== '') {
                $query->where(function ($sub) use ($q) {
                    $sub->where('language_name', 'like', '%' . $q . '%')
                        ->orWhere('language_code', 'like', '%' . $q . '%');
                });
            }

            $paginator = $query->paginate($perPage);

            $results = $paginator->getCollection()->map(function (Language $language) {
                return [
                    'id' => $language->id, // важливо: віддаємо id, а не language_code
                    'text' => $language->language_name . ' (' . $language->language_code . ')',
                ];
            })->values();

            return response()->json([
                'results' => $results,
                'pagination' => ['more' => $paginator->hasMorePages()],
            ]);
        }
    }