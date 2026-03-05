<?php

    namespace App\Models;

    use Illuminate\Database\Eloquent\Model;

    class RankDetail extends Model
    {
        protected $table = 'rank_details';
        protected $fillable = [
            'rank_id',
            'type',
            'rank_group',
            'rank_absolute',
            'page',
            'domain',
            'title',
            'description',
            'url',
            'breadcrumb',
            'is_match',
            'match_reason',
        ];

        /**
         * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
         */
        public function rank(): \Illuminate\Database\Eloquent\Relations\BelongsTo
        {
            return $this->belongsTo(Rank::class);
        }
    }
