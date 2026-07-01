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
        Schema::create('servers', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();      // v1, v2, v3, recap
            $table->string('url');                  // https://...
            $table->json('role_access')->nullable(); // ['tester','normal'] or null = all
            $table->integer('priority')->default(99); // role per ဘယ်ဟာအရင်စမ်းမလဲ
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('servers');
    }
};
