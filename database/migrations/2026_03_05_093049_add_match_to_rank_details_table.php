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
        Schema::table('rank_details', function (Blueprint $table) {
            $table->boolean('is_match')->default(false)->after('breadcrumb');
            $table->string('match_reason')->nullable()->after('is_match'); // optional: "domain"
            $table->index(['rank_id', 'is_match']);
        });
    }

    public function down(): void
    {
        Schema::table('rank_details', function (Blueprint $table) {
            $table->dropIndex(['rank_id', 'is_match']);
            $table->dropColumn(['is_match', 'match_reason']);
        });
    }
};
