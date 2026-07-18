<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    // Status constants — use these everywhere instead of raw strings
    public const STATUS_PENDING        = 'pending';
    public const STATUS_PROCESSING     = 'processing';
    public const STATUS_SUCCESS        = 'success';
    public const STATUS_MANUAL_REVIEW  = 'manual_review';
    public const STATUS_FAILED         = 'failed';
    // Distinct from STATUS_FAILED (which means the window expired) so
    // reporting/history can tell "user changed their mind" apart from
    // "user never came back to upload".
    public const STATUS_CANCELLED      = 'cancelled';

    // How long a "processing" lock is considered valid before we treat it as
    // stuck (server crashed mid-request, Gemini call hung, etc.) and allow a retry.
    public const LOCK_TIMEOUT_SECONDS = 60;

    // How long the user has to upload a screenshot after a ref_code is
    // generated. Matches the 10-minute countdown shown in BuyNowModal.vue.
    public const UPLOAD_WINDOW_SECONDS = 600;

    /**
     * True once the 10-minute upload window has passed without a screenshot.
     */
    public function isUploadWindowExpired(): bool
    {
        return $this->created_at->addSeconds(self::UPLOAD_WINDOW_SECONDS)->isPast();
    }

    protected $fillable = [
        'user_id',
        'phone',
        'plan_key',
        'duration_days',
        'amount',
        'ref_code',
        'bank_type',
        'transaction_id',
        'sender_phone',
        'note',
        'paid_at',
        'screenshot_path',
        'raw_ai_response',
        'status',
        'attempts',
        'locked_at',
        'last_error',
    ];

    protected $casts = [
        'raw_ai_response' => 'array',
        'paid_at'         => 'datetime',
        'locked_at'       => 'datetime',
        'amount'          => 'integer',
        'duration_days'   => 'integer',
        'attempts'        => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * A row is safely retryable if it's not currently locked, or the lock
     * is older than LOCK_TIMEOUT_SECONDS (meaning a previous attempt died
     * without releasing it). This is the "fallback if error/lock happens"
     * behaviour the user asked for.
     */
    public function isLockStale(): bool
    {
        return $this->status === self::STATUS_PROCESSING
            && $this->locked_at
            && $this->locked_at->addSeconds(self::LOCK_TIMEOUT_SECONDS)->isPast();
    }

    /**
     * Still an open checkout the user could resume or upload against —
     * i.e. not yet succeeded, failed, cancelled, or under manual review.
     */
    public function isActive(): bool
    {
        return in_array($this->status, [self::STATUS_PENDING, self::STATUS_PROCESSING], true)
            && !$this->isUploadWindowExpired();
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeAwaitingReview($query)
    {
        return $query->where('status', self::STATUS_MANUAL_REVIEW);
    }
}