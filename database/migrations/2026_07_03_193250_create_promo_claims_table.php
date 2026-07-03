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
        Schema::create('promo_claims', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('ip_address', 45);          // IPv4/IPv6 both fit
            $table->char('ua_hash', 64);                // sha256(user_agent)
            $table->timestamp('claimed_at');

            $table->index(['ip_address', 'claimed_at']);
            $table->index(['ua_hash', 'claimed_at']);
             $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promo_claims');
    }
};
