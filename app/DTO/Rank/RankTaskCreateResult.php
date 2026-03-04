<?php

    namespace App\DTO\Rank;

    class RankTaskCreateResult
    {
        public function __construct(
            public string $task_id
        ) {}
    }