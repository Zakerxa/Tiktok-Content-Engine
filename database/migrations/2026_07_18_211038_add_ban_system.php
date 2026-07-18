<?php

// database/migrations/xxxx_add_ban_system.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('banned_ips', function (Blueprint $table) {
            $table->id();
            $table->string('ip_address')->unique();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('reason')->nullable();
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_banned')->default(false)->after('email');
            $table->string('banned_reason')->nullable()->after('is_banned');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->string('ip_address')->nullable()->after('phone');
        });
    }

    public function down(): void
    {
        // reverse order — payments/users ကို drop မလုပ်ခင် dependency မရှိမှသေချာအောင်
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn('ip_address');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['is_banned', 'banned_reason']);
        });

        Schema::dropIfExists('banned_ips');
    }
};