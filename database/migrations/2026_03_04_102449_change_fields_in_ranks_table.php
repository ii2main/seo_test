<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('ranks', function (Blueprint $table) {
            $table->dropColumn('url');

            $table->dropColumn('rank');
            $table->decimal('rank_min',8,2)->nullable();
            $table->decimal('rank_max',8,2)->nullable();
            $table->decimal('rank_avg',8,2)->nullable();

            $table->renameColumn('keywords', 'keyword');
            $table->string('keyword')->nullable()->change();
        });
    }

        /**
         * Reverse the migrations.
         *
         * @return void
         */
        public function down()
    {
        Schema::table('ranks', function (Blueprint $table) {
            $table->string('url')->nullable();

            $table->dropColumn('rank_min');
            $table->dropColumn('rank_max');
            $table->dropColumn('rank_avg');
            $table->decimal('rank',8,2)->nullable();

            $table->renameColumn('keyword', 'keywords');
            $table->json('keywords')->nullable()->change();
        });
    }
};
