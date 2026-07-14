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
        Schema::table('roles', function (Blueprint $table) {
            $table->unsignedInteger('price')->default(1000)->after('name'); // MMK/day
            $table->string('tagline')->nullable()->after('price');
            $table->unsignedInteger('subtitle_limit')->default(8)->after('can_subtitle');
            $table->unsignedInteger('voiceover_limit')->default(16)->after('can_voiceover');
            $table->unsignedTinyInteger('copyright_protection')->default(40); // percent
            $table->string('video_quality')->default('standard');   // low | standard | high
            $table->string('processing_speed')->default('standard'); // low | standard | priority
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('show_in_pricing')->default(false)->after('sort_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->dropColumn([
                'price', 'tagline', 'subtitle_limit', 'voiceover_limit',
                'copyright_protection', 'video_quality', 'processing_speed', 'sort_order','show_in_pricing'
            ]);
        });
    }
};
