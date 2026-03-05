<?php

    namespace App\Models;

    use Illuminate\Database\Eloquent\Model;

    class Rank extends Model
    {
        protected $table = 'ranks';
        protected $fillable = [
            'domain_id',
            'location_id',
            'language_id',
            'items_count',
            'rank_min',
            'rank_max',
            'rank_avg',
            'keyword',
            'task_id',
            'status',
            'error_message',
            'results_fetched_at',
        ];

        /**
         * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
         */
        public function domain(): \Illuminate\Database\Eloquent\Relations\BelongsTo
        {
            return $this->belongsTo(Domain::class);
        }

        /**
         * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
         */
        public function location(): \Illuminate\Database\Eloquent\Relations\BelongsTo
        {
            return $this->belongsTo(Location::class);
        }

        /**
         * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
         */
        public function language(): \Illuminate\Database\Eloquent\Relations\BelongsTo
        {
            return $this->belongsTo(Language::class);
        }

        /**
         * @return \Illuminate\Database\Eloquent\Relations\HasMany
         */
        public function rankDetails(): \Illuminate\Database\Eloquent\Relations\HasMany
        {
            return $this->hasMany(RankDetail::class);
        }

    }
