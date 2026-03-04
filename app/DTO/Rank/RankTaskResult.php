<?php

    namespace App\DTO\Rank;

    class RankTaskResult
    {
        public function __construct(
            public bool $ready,
            public ?array $items = null,
            public ?string $error_message = null
        ) {}
    }