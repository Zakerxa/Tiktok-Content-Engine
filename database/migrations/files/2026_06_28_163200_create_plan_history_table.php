<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plan_history', function (Blueprint $table) {
            $table->charset   = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';

            $table->id();

            // nullOnDelete (not cascade): if the user is deleted, this row
            // survives as a permanent audit record — only the link is cleared.
            // Matches AdminController::deleteUser()'s intent to keep history.
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();

            $table->string('role_name', 50);
            $table->integer('recap_limit')->default(0);
            $table->dateTime('plan_starts_at');
            $table->dateTime('plan_expires_at');
            $table->string('renewed_by', 100)->default('admin');
            $table->text('note')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plan_history');
    }
};
