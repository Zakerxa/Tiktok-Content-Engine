<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tiktok_posts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique()->nullable();
            $table->string('cover_title_burmese');
            $table->text('content');
            $table->text('image_prompt')->nullable();
            $table->text('b_roll_animation_suggestion')->nullable();
            $table->string('hashtags')->nullable();
            $table->string('topic')->nullable();
            $table->string('model_used')->nullable();
            $table->string('image_path')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tiktok_posts');
    }
};
