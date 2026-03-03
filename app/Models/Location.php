<?php

    namespace App\Models;

    use Illuminate\Database\Eloquent\Model;

    class Location extends Model
    {
        protected $table = 'locations';
        protected $fillable = [
            'location_code',
            'location_name',
            'location_code_parent',
            'country_iso_code',
            'location_type',
        ];

        /**
         * @return \Illuminate\Database\Eloquent\Relations\HasMany
         */
        public function seoRanks(): \Illuminate\Database\Eloquent\Relations\HasMany
        {
            return $this->hasMany(SeoRank::class);
        }
    }
