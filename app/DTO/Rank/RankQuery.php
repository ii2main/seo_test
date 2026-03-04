<?php

    namespace App\DTO\Rank;

    class RankQuery
    {
        public function __construct(
            public string $keyword,
            public int $location_code,
            public string $language_code,
            public int $depth
        ) {}
    }