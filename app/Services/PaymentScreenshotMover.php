<?php

namespace App\Services;

use App\Models\Payment;
use Illuminate\Support\Facades\Storage;

class PaymentScreenshotMover
{
    /**
     * Moves the payment's screenshot from payment-screenshots/ into
     * payment-screenshots/{approve|reject}/, keeping the same filename.
     * Returns the new relative path, or null if there was nothing to move
     * (missing file — shouldn't normally happen, but the caller shouldn't
     * be blocked over a storage hiccup).
     */
    public function move(Payment $payment, string $folder): ?string
    {
        if (!$payment->screenshot_path || !Storage::disk('local')->exists($payment->screenshot_path)) {
            return null;
        }

        // Already moved (e.g. double-click on approve, or re-run) — don't re-move.
        if (str_starts_with($payment->screenshot_path, "payment-screenshots/{$folder}/")) {
            return $payment->screenshot_path;
        }

        $filename = basename($payment->screenshot_path);
        $newPath  = "payment-screenshots/{$folder}/{$filename}";

        Storage::disk('local')->move($payment->screenshot_path, $newPath);

        return $newPath;
    }
}