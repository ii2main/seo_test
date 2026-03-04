<?php

    namespace App\Http\Controllers;

    use App\Http\Requests\Location\LocationCreateRequest;
    use App\Http\Requests\Location\LocationUpdateRequest;
    use App\Http\Resources\LocationResource;
    use App\Models\Location;
    use Illuminate\Http\Request;
    use GuzzleHttp\Client;
    use Illuminate\Support\Facades\Log;

    class LocationController extends Controller
    {
        /**
         * @param Request $request
         * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Illuminate\View\View
         */
        public function index(Request $request)
        {
            $locations = Location::query()
                ->latest()
                ->paginate(10)
                ->withQueryString();

            if ($request->expectsJson()) {
                return LocationResource::collection($locations);
            }

            return view('pages.locations.index', compact('locations'));
        }

        /**
         * @param Request $request
         * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\View\View
         */
        public function create(Request $request)
        {
            return view('pages.locations.form');
        }

        /**
         * @param LocationCreateRequest $request
         * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
         */
        public function store(LocationCreateRequest $request)
        {
            $validated = $request->validated();

            $location = Location::create($validated);

            if ($request->expectsJson()) {
                return (new LocationResource($location))->response()->setStatusCode(201);
            }

            return redirect()
                ->route('locations.index')
                ->with('success', 'Location created successfully.');
        }

        /**
         * @param Request $request
         * @param Location $location
         * @return LocationResource|\Illuminate\Http\RedirectResponse
         */
        public function show(Request $request, Location $location)
        {
            if ($request->expectsJson()) {
                return new LocationResource($location);
            }

            return redirect()->route('locations.index');
        }

        /**
         * @param Request $request
         * @param Location $location
         * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\View\View
         */
        public function edit(Request $request, Location $location)
        {

            return view('pages.locations.form', compact('location'));
        }

        /**
         * @param LocationUpdateRequest $request
         * @param Location $location
         * @return LocationResource|\Illuminate\Http\RedirectResponse
         */
        public function update(LocationUpdateRequest $request, Location $location)
        {
            $validated = $request->validated();

            $location->update($validated);

            if ($request->expectsJson()) {
                return new LocationResource($location->fresh());
            }

            return redirect()
                ->route('locations.index')
                ->with('success', 'Location updated successfully.');
        }

        /**
         * @param Request $request
         * @param Location $location
         * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
         */
        public function destroy(Request $request, Location $location)
        {
            $location->delete();

            if ($request->expectsJson()) {
                return response()->noContent();
            }

            return redirect()
                ->route('locations.index')
                ->with('success', 'Location deleted successfully.');
        }

        /**
         * @param Request $request
         * @return \Illuminate\Http\RedirectResponse
         */
        public function refreshFromService(Request $request)
        {
            $locations = $this->getLocations();

            if (empty($locations)) {
                return redirect()
                    ->route('locations.index')
                    ->with('error', 'Failed to fetch locations from service.');
            }

            $now = now();
            $rows = [];

            foreach ($locations as $item) {
                $rows[] = [
                    'location_code' => $item['location_code'] ?? null,
                    'location_name' => $item['location_name'] ?? null,
                    'location_code_parent' => $item['location_code_parent'] ?? null,
                    'country_iso_code' => $item['country_iso_code'] ?? null,
                    'location_type' => $item['location_type'] ?? null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            // If there are no good data for all feilds we will remove it
            $rows = array_values(array_filter($rows, function (array $row) {
                return $row['location_code'] !== null
                    && $row['location_name'] !== null
                    && $row['country_iso_code'] !== null;
            }));

            if (empty($rows)) {
                return redirect()
                    ->route('locations.index')
                    ->with('error', 'Service returned no valid location rows.');
            }

            $chunk_size = 1000;
            foreach (array_chunk($rows, $chunk_size) as $chunk) {
                Location::upsert(
                    $chunk,
                    ['location_code'],
                    ['location_name', 'location_code_parent', 'country_iso_code', 'location_type', 'updated_at']
                );
            }

            return redirect()
                ->route('locations.index')
                ->with('success', 'Locations refreshed: ' . count($rows) . ' rows processed.');
        }

        /**
         * @return array
         */
        private function getLocations(): array
        {
            $client = new Client([
                'timeout' => 30,
                'connect_timeout' => 10,
            ]);

            $apiKey = config('services.dataforseo.api_key');
            $apiLogin = config('services.dataforseo.api_login');

            try {
                $response = $client->get('https://api.dataforseo.com/v3/serp/google/locations', [
                    'auth' => [$apiLogin, $apiKey],
                ]);

                $payload = json_decode($response->getBody()->getContents(), true);

                return $payload['tasks'][0]['result'] ?? [];
            } catch (\Throwable $e) {
                Log::error('Locales getting error: ' . $e->getMessage() . '. Try later');

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

            $query = Location::query()->orderBy('location_name');

            if ($q !== '') {
                $query->where(function ($sub) use ($q) {
                    $sub->where('location_name', 'like', '%' . $q . '%')
                        ->orWhere('country_iso_code', 'like', '%' . $q . '%')
                        ->orWhere('location_code', 'like', '%' . $q . '%');
                });
            }

            $paginator = $query->paginate($perPage);

            $results = $paginator->getCollection()->map(function (Location $location) {
                return [
                    'id' => $location->id, // віддаємо id, а не location_code
                    'text' => $location->location_name . ' (' . $location->country_iso_code . ', ' . $location->location_code . ')',
                ];
            })->values();

            return response()->json([
                'results' => $results,
                'pagination' => ['more' => $paginator->hasMorePages()],
            ]);
        }

    }