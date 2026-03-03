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
        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            $table->integer('location_code')->unique(); // location code
            $table->string('location_name'); // full name of the location
            $table->integer('location_code_parent'); // the code of the superordinate location
            $table->string('country_iso_code'); // ISO country code of the location
            $table->string('location_type'); // location type indicates the geographic classification of the location example: "location_type": "Country", or "location_type": "State"
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('locations');
    }
};
