<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('locations', function (Blueprint $table) {
            $table->string('country_iso_code', 2)->change();
            $table->integer('location_code_parent')->nullable()->change();
            $table->string('location_type')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('locations', function (Blueprint $table) {
            $table->string('country_iso_code')->change();
            $table->integer('location_code_parent')->nullable(false)->change();
            $table->string('location_type')->nullable(false)->change();
        });
    }
};
