<?php

    namespace App\Services;

    use App\Contracts\RankProviderInterface;
    use App\DTO\Rank\RankQuery;
    use App\DTO\Rank\RankTaskCreateResult;
    use App\DTO\Rank\RankTaskResult;
    use GuzzleHttp\Client;
    use Illuminate\Validation\ValidationException;

    class DataForSeoService implements RankProviderInterface
    {
        public function __construct(private Client $client)
        {
        }

        public function createTask(RankQuery $query): RankTaskCreateResult
        {
            $response = $this->client->request('POST', 'https://api.dataforseo.com/v3/serp/google/organic/task_post', [
                'auth' => [config('services.dataforseo.api_login'), config('services.dataforseo.api_key')],
                'json' => [
                    [
                        'keyword' => $query->keyword,
                        'location_code' => $query->location_code,
                        'language_code' => $query->language_code,
                        'depth' => $query->depth,
                    ],
                ],
            ]);

            $data = json_decode($response->getBody()->getContents(), true);

            if (!is_array($data) || (int)($data['status_code'] ?? 0) !== 20000) {
                throw ValidationException::withMessages([
                    'api' => [(string)($data['status_message'] ?? 'DataForSEO: failed to create task.')],
                ]);
            }

            $task_id = $data['tasks'][0]['id'] ?? null;
            if (!$task_id) {
                throw ValidationException::withMessages([
                    'api' => ['DataForSEO: task id not returned.'],
                ]);
            }

            return new RankTaskCreateResult((string) $task_id);
        }

        public function fetchTaskResult(string $task_id): RankTaskResult
        {
            $response = $this->client->request('GET', "https://api.dataforseo.com/v3/serp/google/organic/task_get/regular/{$task_id}", [
                'auth' => [config('services.dataforseo.api_login'), config('services.dataforseo.api_key')],
            ]);

            $data = json_decode($response->getBody()->getContents(), true);

            if (!is_array($data) || (int)($data['status_code'] ?? 0) !== 20000) {
                return new RankTaskResult(false, null, (string)($data['status_message'] ?? 'DataForSEO: failed to get task result.'));
            }

            $task = $data['tasks'][0] ?? [];
            $items = $task['result'][0]['items'] ?? null;

            if (!is_array($items)) {
                // результат ще не готовий
                return new RankTaskResult(false, null, null);
            }

            return new RankTaskResult(true, $items, null);
        }
    }