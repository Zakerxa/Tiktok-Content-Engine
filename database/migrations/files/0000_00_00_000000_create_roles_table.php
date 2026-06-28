<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            // Force collation explicitly so this table can NEVER drift from
            // other tables regardless of server/database defaults.
            $table->charset   = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';

            $table->id();
            $table->string('name', 50)->unique();           // tester / normal / pro / vip / admin
            $table->unsignedInteger('daily_limit')->default(1);
            $table->unsignedInteger('max_video_seconds')->default(0); // 0 = unlimited
            $table->boolean('can_watermark')->default(false);
            $table->boolean('can_subtitle')->default(false);
            $table->boolean('can_voiceover')->default(false);
            $table->timestamp('created_at')->useCurrent();
        });

        // Seed the 5 fixed roles so the app has something to join against immediately.
        // Values mirror recap.sql exactly.
        DB::table('roles')->insert([
            ['name' => 'tester', 'daily_limit' => 1,   'max_video_seconds' => 40,  'can_watermark' => false, 'can_subtitle' => false, 'can_voiceover' => true,  'created_at' => now()],
            ['name' => 'normal', 'daily_limit' => 3,   'max_video_seconds' => 120, 'can_watermark' => true,  'can_subtitle' => true,  'can_voiceover' => true,  'created_at' => now()],
            ['name' => 'pro',    'daily_limit' => 3,   'max_video_seconds' => 180, 'can_watermark' => true,  'can_subtitle' => true,  'can_voiceover' => true,  'created_at' => now()],
            ['name' => 'vip',    'daily_limit' => 5,   'max_video_seconds' => 300, 'can_watermark' => true,  'can_subtitle' => true,  'can_voiceover' => true,  'created_at' => now()],
            ['name' => 'admin',  'daily_limit' => 999, 'max_video_seconds' => 0,   'can_watermark' => true,  'can_subtitle' => true,  'can_voiceover' => true,  'created_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
