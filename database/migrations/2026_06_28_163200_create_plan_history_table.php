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

            // Username snapshot (not a user_id FK) — matches database.py's
            // INSERT/SELECT exactly, and survives AdminController::deleteUser()
            // since this is a permanent audit log, not a live relation.
            $table->string('username', 100)->index();

            $table->string('old_role', 50);
            $table->string('new_role', 50);
            $table->integer('recap_limit')->default(0);

            // Nullable: _auto_rollback_pro()'s INSERT never supplies this column,
            // so it must have a safe default (NULL) rather than fail the insert.
            $table->dateTime('plan_expires_at')->nullable();

            $table->string('renewed_by', 100)->default('admin'); // admin username, or 'system_auto_rollback'

            // Not written explicitly by either INSERT in database.py — relies on
            // DEFAULT CURRENT_TIMESTAMP, and is read back via ORDER BY renewed_at.
            $table->timestamp('renewed_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plan_history');
    }
};