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
            $table->string('charged_role', 20)->nullable()->default(null)->after('status');
            $table->boolean('storage_cleaned')->default(false)->after('storage');
            $table->timestamp('storage_cleaned_at')->nullable()->after('storage_cleaned');
            $table->text('storage_cleanup_error')->nullable()->after('storage_cleaned_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('recap_jobs', function (Blueprint $table) {
            $table->dropColumn(['charged_role','storage_cleaned', 'storage_cleaned_at', 'storage_cleanup_error']);
        });
    }
};
