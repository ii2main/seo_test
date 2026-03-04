<?php

    namespace App\Models;

    use Illuminate\Database\Eloquent\Model;

    class Language extends Model
    {
        protected $table = 'languages';
        protected $fillable = [
            'language_name',
            'language_code',
        ];

        /**
         * @return \Illuminate\Database\Eloquent\Relations\HasMany
         */
        public function ranks(): \Illuminate\Database\Eloquent\Relations\HasMany
        {
            return $this->hasMany(Rank::class);
        }
    }
