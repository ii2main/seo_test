<?php

    namespace App\Models;

    use Illuminate\Database\Eloquent\Model;

    class SeoRank extends Model
    {
        protected $table = 'seo_ranks';
        protected $fillable = [
            'domain_id',
            'location_id',
            'language_id',
            'rank',
            'url',
            'title',
            'description',
            'keywords',
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

    }
