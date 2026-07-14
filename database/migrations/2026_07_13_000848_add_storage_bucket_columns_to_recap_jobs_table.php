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
        Schema::table('recap_jobs', function (Blueprint $table) {
            $table->string('output_path')->nullable()->after('server');
            $table->timestamp('completed_at')->nullable()->after('output_path');
            $table->timestamp('expires_at')->nullable()->after('completed_at');
            $table->string('storage', 15)->default('bucket')->after('server');
            $table->index('expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('recap_jobs', function (Blueprint $table) {
            $table->dropColumn(['output_path', 'completed_at', 'expires_at','storage']);
        });
    }
};