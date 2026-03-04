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
        Schema::table('ranks', function (Blueprint $table) {
            $table->string('task_id')->nullable()->after('language_id');
            $table->string('status')->default('new')->after('task_id'); // new|queued|running|done|failed

            $table->dropColumn('title');
            $table->dropColumn('description');

            $table->text('error_message')->nullable()->after('status');
            $table->timestamp('results_fetched_at')->nullable()->after('error_message');

            $table->index('task_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ranks', function (Blueprint $table) {
            $table->dropIndex(['task_id']);
            $table->dropIndex(['status']);

            $table->string('title')->nullable();
            $table->text('description')->nullable();

            $table->dropColumn([
                'task_id',
                'status',
                'error_message',
                'results_fetched_at',
            ]);
        });
    }
};
