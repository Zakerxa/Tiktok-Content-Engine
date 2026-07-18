<?php

namespace App\Http\Controllers;

use App\Services\PaymentScreenshotMover;
use App\Services\GeminiReceiptParser;
use App\Services\PlanService;
use App\Models\BannedIp;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\Rule;
use Throwable;

class PaymentController extends Controller
{
    // MMK price per day, per plan. Must match what Plan.vue / PricingSection.vue
    // display, otherwise the amount charged won't match what the user saw.
    // TODO: replace with a real lookup against your pricing_plans table once
    // you tell me its model name — for now this is a hardcoded mirror of it.
    private const PLAN_PRICES = [
        'normal' => 1000,
        'pro'    => 1750,
        'vip'    => 5000,
    ];

    // Duration (days) => discount % — mirrors Plan.vue's `purchasePackages`.
    // TODO: confirm the 3-day discount (10% is a placeholder estimate).
    private const DURATION_DISCOUNTS = [
        3  => 10.0,
        7  => 20.0,
        21 => 33.3,
        30 => 40.0,
    ];

    /**
     * Step 1 of checkout: generate a 6-character ref code + pending payment row.
     */
    public function initiate(Request $request)
    {
        $data = $request->validate([
            'plan_key'      => ['required', Rule::in(array_keys(self::PLAN_PRICES))],
            'duration_days' => ['required', Rule::in(array_keys(self::DURATION_DISCOUNTS))],
            'phone'         => ['required', 'string', 'min:7', 'max:20'],
            'bank_type'     => ['required', Rule::in(['kpay', 'wave'])],
        ]);

        // If the user already has an unexpired pending/processing checkout,
        // hand that same one back instead of minting a second ref_code — this
        // is what lets the "resume after coming back from KPay" flow work
        // even if they somehow re-trigger initiate() (double click, reopening
        // the modal, etc.) instead of just calling GET /api/payments/current.
        $existing = Payment::where('user_id', $request->user()?->id)
            ->whereIn('status', [Payment::STATUS_PENDING, Payment::STATUS_PROCESSING])
            ->latest()
            ->first();

        if ($existing && $existing->isActive()) {
            return response()->json([
                'ref_code'           => $existing->ref_code,
                'amount'             => $existing->amount,
                'plan_key'           => $existing->plan_key,
                'duration_days'      => $existing->duration_days,
                'bank_type'          => $existing->bank_type,
                'status'             => $existing->status,
                'pay_to_phone'       => config('payment.account_phone'),
                'pay_to_name'        => config('payment.account_name'),
                'expires_in_seconds' => max(
                    Payment::UPLOAD_WINDOW_SECONDS - $existing->created_at->diffInSeconds(now()),
                    0
                ),
            ]);
        }

        // Server computes the amount itself — never trust a client-sent price.
        $pricePerDay = self::PLAN_PRICES[$data['plan_key']];
        $discount    = self::DURATION_DISCOUNTS[$data['duration_days']];
        $amount      = (int) round($pricePerDay * $data['duration_days'] * (1 - $discount / 100));

        $payment = DB::transaction(function () use ($data, $amount, $request) {
            return Payment::create([
                'user_id'       => $request->user()?->id,
                'phone'         => $data['phone'],
                'plan_key'      => $data['plan_key'],
                'duration_days' => $data['duration_days'],
                'amount'        => $amount,
                'ref_code'      => $this->generateRefCode(),
                'bank_type'     => $data['bank_type'],
                'status'        => Payment::STATUS_PENDING,
            ]);
        });

        return response()->json([
            'ref_code'           => $payment->ref_code,
            'amount'             => $payment->amount,
            'plan_key'           => $payment->plan_key,
            'duration_days'      => $payment->duration_days,
            'bank_type'          => $payment->bank_type,
            'status'             => $payment->status,
            'pay_to_phone'       => config('payment.account_phone'),
            'pay_to_name'        => config('payment.account_name'),
            'expires_in_seconds' => Payment::UPLOAD_WINDOW_SECONDS,
        ]);
    }

    /**
     * Step 2 of checkout: user uploads the payment screenshot.
     *
     * The DB-locked part (find row, check status/expiry, store file, mark
     * "processing") happens inside a transaction and commits immediately —
     * we deliberately do NOT call Gemini while holding that lock, since an
     * external API call can be slow and would block any other request
     * touching this row for no reason. Once the lock is released, we OCR
     * the screenshot and run the match logic in a separate step.
     */
    public function verify(Request $request): JsonResponse
    {
        $data = $request->validate([
            'ref_code'   => ['required', 'string', 'size:6'],
            'screenshot' => ['required', 'image', 'max:5120'], // 5MB
        ]);

        $payment = DB::transaction(function () use ($data, $request) {
            // lockForUpdate() = the actual DB-level lock, so two concurrent
            // verify() calls for the same ref_code can't both proceed at once.
            $payment = Payment::where('ref_code', $data['ref_code'])
                ->lockForUpdate()
                ->first();

            if (!$payment) {
                $this->fail('ဒီကုဒ်ကို ရှာမတွေ့ပါ', 404);
            }

            // Someone else's request is already mid-flight and the lock is
            // still fresh — reject instead of double-processing.
            if ($payment->status === Payment::STATUS_PROCESSING && !$payment->isLockStale()) {
                $this->fail('ဒီငွေလွှဲမှုကို စစ်ဆေးနေဆဲ ဖြစ်ပါသည်', 409);
            }

            if (in_array($payment->status, [
                Payment::STATUS_SUCCESS,
                Payment::STATUS_MANUAL_REVIEW,
                Payment::STATUS_FAILED,
                Payment::STATUS_CANCELLED,
            ], true)) {
                $this->fail('ဒီကုဒ်ကို အသုံးပြုပြီးသား (သို့) ပယ်ဖျက်ပြီးသား ဖြစ်ပါသည်', 409);
            }

            if ($payment->isUploadWindowExpired()) {
                $payment->update([
                    'status'     => Payment::STATUS_FAILED,
                    'last_error' => 'Upload window expired before a screenshot was received.',
                ]);
                $this->fail('ကုဒ်သက်တမ်း ကုန်သွားပါပြီ။ အသစ်ပြန်တောင်းပါ', 410);
            }

            $path = $request->file('screenshot')->store('payment-screenshots', 'local');

            $payment->update([
                'status'          => Payment::STATUS_PROCESSING,
                'screenshot_path' => $path,
                'ip_address'      => $request->ip(),
                'locked_at'       => now(),
                'attempts'        => $payment->attempts + 1,
            ]);

            return $payment;
        });

        return $this->runGeminiVerification($payment, $request->ip());
    }

    /**
     * OCRs the screenshot and applies the auto-approve / manual_review
     * decision. Any failure here (API down, bad JSON, timeout) falls back
     * to manual_review rather than leaving the row stuck on "processing" —
     * an admin can always approve it by hand from the screenshot.
     */
    private function runGeminiVerification(Payment $payment, string $ip): JsonResponse
    {
        try {
            $aiData = (new GeminiReceiptParser())->parse($payment->screenshot_path);
            $this->applyVerification($payment, $aiData, $ip);
        } catch (Throwable $e) {
            $payment->update([
                'status'     => Payment::STATUS_MANUAL_REVIEW,
                'last_error' => 'Gemini OCR failed: ' . $e->getMessage(),
            ]);
        }

        $payment->refresh();

        return response()->json([
            'status'  => $payment->status,
            'message' => $payment->status === Payment::STATUS_SUCCESS
                ? 'ငွေလွှဲမှု အတည်ပြုပြီးပါပြီ 🎉'
                : 'ပြေစာလက်ခံရရှိပါပြီ၊ Admin မှ ကိုယ်တိုင်စစ်ဆေးပေးပါမည်',
        ]);
    }

    /**
     * Duplicate / bank / amount / note matching against the Gemini result.
     * All checks must pass for auto-approval; any single mismatch routes
     * the payment to manual_review with the reason recorded in last_error
     * so an admin can see exactly what didn't match.
     */
    private function applyVerification(Payment $payment, array $aiData, string $ip): void
    {
        $mover = app(PaymentScreenshotMover::class);

        // ── receiver_name check ──
        $receiverName = $this->normalizeName($aiData['receiver_name'] ?? null);
        $expectedName = $this->normalizeName(config('payment.account_name'));

        if ($receiverName === '' || $receiverName !== $expectedName) {
            $newPath = $mover->move($payment, 'reject');

            $payment->update([
                'transaction_id'  => isset($aiData['transaction_id']) ? (string) $aiData['transaction_id'] : null,
                'sender_phone'    => $aiData['sender_phone'] ?? null,
                'note'            => $aiData['note'] ?? null,
                'raw_ai_response' => $aiData,
                'screenshot_path' => $newPath ?? $payment->screenshot_path,
                'status'          => Payment::STATUS_FAILED,
                'last_error'      => 'Rejected by System (Gemini AI): receiver name mismatch (expected "'
                    . config('payment.account_name') . '", receipt shows "'
                    . ($aiData['receiver_name'] ?? 'null') . '")',
            ]);

            return;
        }

        $reasons = [];

        $bankMap = ['kbzpay' => 'kpay', 'wavepay' => 'wave'];
        $aiBank  = $bankMap[strtolower((string) ($aiData['bank_type'] ?? ''))] ?? null;

        $transactionId = isset($aiData['transaction_id']) ? (string) $aiData['transaction_id'] : null;
        $amount        = isset($aiData['amount']) ? (int) $aiData['amount'] : null;
        $note          = $aiData['note'] ?? null;
        $paidAt        = $this->parseDateTime($aiData['date_time'] ?? null);

        if (!$transactionId) {
            $reasons[] = 'transaction_id missing from receipt';
        } elseif (Payment::where('transaction_id', $transactionId)
            ->where('id', '!=', $payment->id)
            ->where('status', Payment::STATUS_SUCCESS)
            ->exists()
        ) {
            $reasons[] = 'transaction_id already used on another payment';
        }

        if ($aiBank === null) {
            $reasons[] = 'could not read bank type from screenshot';
        } elseif ($aiBank !== $payment->bank_type) {
            $reasons[] = "bank type mismatch (selected {$payment->bank_type}, receipt shows {$aiBank})";
        }

        if ($amount === null || $amount !== $payment->amount) {
            $reasons[] = 'amount mismatch (expected ' . $payment->amount . ', got ' . ($amount ?? 'null') . ')';
        }

        $noteMatchesRefCode = $note && str_contains(strtolower($note), strtolower($payment->ref_code));
        if (!$noteMatchesRefCode) {
            $reasons[] = 'ref_code not found in note';
        }

        $isStaleReceipt = false;
        if (!$paidAt) {
            $reasons[] = 'could not read transaction date/time from receipt';
        } elseif ($paidAt->lt($payment->created_at->subMinutes(1))) {
            $reasons[] = 'receipt date/time predates this checkout — possible reused screenshot';
            $isStaleReceipt = true;
        } elseif ($paidAt->gt(now()->addMinutes(1))) {
            $reasons[] = 'receipt date/time is in the future';
        }

        if (empty($reasons) && !$payment->user) {
            $reasons[] = 'no linked user to grant the plan to';
        }

        // ── SUCCESS ဖြစ်မှသာ approve/ ထဲ ရွှေ့ — MANUAL_REVIEW ကတော့ admin ဆုံးဖြတ်ချက်
        // စောင့်ဆဲမို့ root ထဲမှာပဲ ချန်ထား (admin panel ကနေ approve/reject ချချိန်
        // moveScreenshot() ကတင် ရွှေ့ပေးမှာမို့)
        $newPath = empty($reasons) ? $mover->move($payment, 'approve') : null;

        $payment->update([
            'transaction_id'  => $transactionId,
            'sender_phone'    => $aiData['sender_phone'] ?? null,
            'note'            => $note,
            'paid_at'         => $paidAt,
            'raw_ai_response' => $aiData,
            'screenshot_path' => $newPath ?? $payment->screenshot_path,
            'status'          => empty($reasons) ? Payment::STATUS_SUCCESS : Payment::STATUS_MANUAL_REVIEW,
            'last_error'      => empty($reasons) ? null : implode('; ', $reasons),
        ]);

        if (empty($reasons)) {
            PlanService::grant($payment->user, $payment->plan_key, $payment->duration_days, 'System (Gemini AI)');
            $this->notifyPaymentSuccess($payment);
        }

        if ($noteMatchesRefCode && $isStaleReceipt) {
            $this->banUserAndIp($payment, $ip, $mover);
        }
    }

    /**
     * Auto-approved payment တိုင်း admin ရဲ့ Telegram ကို ပို့မယ့် notification.
     */
    private function notifyPaymentSuccess(Payment $payment): void
    {
        $message = "💰 New payment approved\n"
            . "User: {$payment->user->username} ({$payment->user->email})\n"
            . "Plan: {$payment->plan_key} — {$payment->duration_days} day(s)\n"
            . "Amount: " . number_format($payment->amount) . " Ks\n"
            . "Bank: {$payment->bank_type}\n"
            . "ref_code: {$payment->ref_code}\n"
            . "Transaction ID: {$payment->transaction_id}";

        Http::post('https://api.telegram.org/bot' . config('services.telegram.token') . '/sendMessage', [
            'chat_id' => config('services.telegram.admin_chat_id'),
            'text'    => $message,
        ]);
    }

    private function banUserAndIp(Payment $payment, string $ip, PaymentScreenshotMover $mover): void
    {
        $reason = "Auto-banned: reused/forged screenshot on payment {$payment->ref_code}";

        $newPath = $mover->move($payment, 'reject');

        DB::transaction(function () use ($payment, $ip, $reason, $newPath) {
            if ($payment->user && !$payment->user->is_banned) {
                $payment->user->update([
                    'is_banned'     => true,
                    'banned_reason' => $reason,
                ]);
            }

            // ── forgery confirmed ဖြစ်နေပြီမို့ MANUAL_REVIEW ကနေ FAILED ကို
            // တန်းပြောင်း — admin queue ထဲမှာ ကျန်နေစရာမလို
            $payment->update([
                'status'          => Payment::STATUS_FAILED,
                'screenshot_path' => $newPath ?? $payment->screenshot_path,
                'last_error'      => $reason,
            ]);

            BannedIp::firstOrCreate(
                ['ip_address' => $ip],
                ['user_id' => $payment->user_id, 'reason' => $reason]
            );
        });

        cache()->forget("banned_ip:{$ip}");

        if ($payment->user_id && config('session.driver') === 'database') {
            DB::table(config('session.table', 'sessions'))
                ->where('user_id', $payment->user_id)
                ->delete();
        }

        Http::post('https://api.telegram.org/bot' . config('services.telegram.token') . '/sendMessage', [
            'chat_id' => config('services.telegram.admin_chat_id'),
            'text'    => "🚫 Auto-ban\nUser: {$payment->user_id}\nIP: {$ip}\nref_code: {$payment->ref_code}\n{$reason}",
        ]);
    }

    /**
     * KBZ receipts append a masked phone like "Zin Min Htet {******7858}" to
     * the receiver name — strip that plus punctuation/casing so a clean
     * "zin min htet" comparison works for both banks.
     */
    private function normalizeName(?string $name): string
    {
        if (!$name) {
            return '';
        }
        $name = preg_replace('/[\{\(].*?[\}\)]/', '', $name);
        $name = preg_replace('/[^a-zA-Z\s]/', '', $name);
        return trim(preg_replace('/\s+/', ' ', strtolower($name)));
    }

    private function parseDateTime(?string $value): ?Carbon
    {
        if (!$value) {
            return null;
        }

        try {
            return Carbon::parse($value);
        } catch (Throwable) {
            return null;
        }
    }

    /**
     * Throws a response immediately (rolls back the enclosing DB transaction)
     * instead of returning, since this is called from inside DB::transaction().
     */
    private function fail(string $message, int $status): never
    {
        throw new HttpResponseException(response()->json(['message' => $message], $status));
    }

    /**
     * Called when Plan.vue mounts (page load / return from KPay app / tab
     * refocus). If the user has an open, unexpired checkout, return it so
     * the frontend can reopen BuyNowModal straight to the payment step
     * with the correct remaining time — instead of losing it if the mobile
     * browser killed the tab while they were in the KPay app.
     */
    public function current(Request $request)
    {
        $payment = Payment::where('user_id', $request->user()->id)
            ->whereIn('status', [Payment::STATUS_PENDING, Payment::STATUS_PROCESSING])
            ->latest()
            ->first();

        if (!$payment) {
            return response()->json(['payment' => null]);
        }

        // Lazily expire it here instead of waiting for a verify() call that
        // may never come — this is the "reduce to 5 min and auto-fail if no
        // screenshot" behaviour. Only applies while still PENDING: once a
        // screenshot has been uploaded (PROCESSING), the upload window is no
        // longer relevant and this must not auto-fail an in-review payment.
        if ($payment->status === Payment::STATUS_PENDING && $payment->isUploadWindowExpired()) {
            $payment->update([
                'status'     => Payment::STATUS_FAILED,
                'last_error' => 'Upload window expired before a screenshot was received.',
            ]);

            return response()->json(['payment' => null]);
        }

        return response()->json([
            'payment' => [
                'ref_code'           => $payment->ref_code,
                'amount'             => $payment->amount,
                'plan_key'           => $payment->plan_key,
                'duration_days'      => $payment->duration_days,
                'bank_type'          => $payment->bank_type,
                'status'             => $payment->status,
                'pay_to_phone'       => config('payment.account_phone'),
                'pay_to_name'        => config('payment.account_name'),
                'expires_in_seconds' => max(
                    Payment::UPLOAD_WINDOW_SECONDS - $payment->created_at->diffInSeconds(now()),
                    0
                ),
            ],
        ]);
    }

    /**
     * User explicitly backs out of a pending/processing checkout — the
     * "cancel by user" case, kept distinct from window-expiry ("failed").
     */
    public function cancel(Request $request)
    {
        $data = $request->validate([
            'ref_code' => ['required', 'string', 'size:6'],
        ]);

        $payment = Payment::where('ref_code', $data['ref_code'])
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$payment) {
            return response()->json(['message' => 'ဒီကုဒ်ကို ရှာမတွေ့ပါ'], 404);
        }

        if (!in_array($payment->status, [Payment::STATUS_PENDING, Payment::STATUS_PROCESSING], true)) {
            return response()->json(['message' => 'ဒီငွေလွှဲမှုကို ပယ်ဖျက်၍ မရတော့ပါ'], 409);
        }

        $payment->update([
            'status'     => Payment::STATUS_CANCELLED,
            'last_error' => 'Cancelled by user.',
        ]);

        return response()->json(['message' => 'ပယ်ဖျက်ပြီးပါပြီ']);
    }

    /**
     * Payment history tab in Plan.vue.
     */
    public function history(Request $request)
    {
        return response()->json(
            Payment::where('user_id', $request->user()->id)
                ->latest()
                ->paginate(10)
        );
    }

    /**
     * 6-char code using a readable charset — excludes 0/O/1/I/l so it's
     * unambiguous when a user has to hand-type it into a bank app's note field.
     */
    private function generateRefCode(): string
    {
        $chars = 'ABCDEFGHJKMNPQRSTUVWXYZ23456789';

        do {
            $code = collect(range(1, 6))->map(fn() => $chars[random_int(0, strlen($chars) - 1)])->implode('');
        } while (Payment::where('ref_code', $code)->exists());

        return $code;
    }
}
