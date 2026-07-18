<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();

            // Who's buying — link to user if you have auth, otherwise phone alone is enough
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('phone', 20)->index(); // buyer's phone number, typed in Step 1 form

            // What they're buying (matches Plan.vue's plan.key + duration_days)
            $table->string('plan_key', 20);        // tester / normal / pro / vip / admin
            $table->unsignedSmallInteger('duration_days'); // 1 / 7 / 21 / 30
            $table->unsignedInteger('amount');      // expected total MMK (server-calculated, never trust client)

            // The 6-digit code the user must put in the KPay/WavePay note
            $table->string('ref_code', 6)->unique();

            // Filled in AFTER the user uploads a screenshot + Gemini OCRs it
            $table->string('bank_type', 20)->nullable();       // KBZPay / WavePay / Unknown
            $table->string('transaction_id', 64)->nullable()->index(); // string! WavePay ids are short, KBZPay ids are long
            $table->string('sender_phone', 20)->nullable();
            $table->string('note')->nullable();
            $table->timestamp('paid_at')->nullable();          // parsed from the receipt, not "now()"
            $table->string('screenshot_path')->nullable();
            $table->json('raw_ai_response')->nullable();       // full Gemini reply — keep for debugging/manual review

            // When this payment code expires — used to drive the countdown timer server-side
            $table->timestamp('expires_at')->nullable()->index();

            // Status machine + retry/lock bookkeeping (the fallback mechanism)
            $table->enum('status', [
                'pending',        // code generated, waiting for screenshot upload
                'processing',     // verify() picked it up, Gemini call in flight — this is the "lock"
                'success',        // auto-approved, plan granted
                'manual_review',  // AI/db couldn't confirm confidently, admin must check
                'failed',         // rejected — duplicate txn, amount mismatch, etc.
                'cancelled',      // user cancelled manually before paying/uploading
            ])->default('pending')->index();

            $table->unsignedTinyInteger('attempts')->default(0); // how many times verify() has been tried
            $table->timestamp('locked_at')->nullable();          // when processing started — used to detect a stuck/crashed lock
            $table->text('last_error')->nullable();              // last exception/reason, shown to admin

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};