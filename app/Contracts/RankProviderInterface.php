<?php

    namespace App\Contracts;

    use App\DTO\Rank\RankQuery;
    use App\DTO\Rank\RankTaskCreateResult;
    use App\DTO\Rank\RankTaskResult;

    interface RankProviderInterface
    {
        public function createTask(RankQuery $query): RankTaskCreateResult;

        public function fetchTaskResult(string $task_id): RankTaskResult;
    }