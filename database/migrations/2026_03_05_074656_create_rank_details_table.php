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
        Schema::create('rank_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rank_id')->constrained()->cascadeOnDelete();
            $table->string('type'); // Type of the item (organic, paid, featured_snippet, etc.)
            $table->integer('rank_group')->nullable(); // Group rank in SERP
            $table->integer('rank_absolute')->nullable(); // Absolute rank in SERP
            $table->integer('page')->nullable(); // Search results page number
            $table->string('domain')->nullable(); // Domain in SERP
            $table->string('title')->nullable(); // Title of the results element in SERP
            $table->text('description')->nullable(); // Description of the results element in SERP
            $table->string('url')->nullable(); // Relevant URL in SERP
            $table->string('breadcrumb')->nullable(); // Breadcrumb in SERP
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rank_details');
    }
};
