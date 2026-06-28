<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('usage_log', function (Blueprint $table) {
            $table->charset   = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';

            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->date('used_date');
            $table->unsignedInteger('run_count')->default(0);

            // One row per user per day — next day with no row = 0 usage (no cron needed).
            $table->unique(['user_id', 'used_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('usage_log');
    }
};
