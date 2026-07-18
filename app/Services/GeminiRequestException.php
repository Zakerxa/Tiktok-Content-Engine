<?php

namespace App\Services;

use RuntimeException;
use Throwable;

/**
 * Thrown by GeminiReceiptParser's per-model call. `retryable` tells the
 * caller whether it's worth trying the SAME model again (network blip,
 * rate limit, transient 5xx) or whether it should give up on this model
 * and fall through to the next tier immediately (bad key, bad request,
 * model deprecated/not found).
 */
class GeminiRequestException extends RuntimeException
{
    public function __construct(string $message, public readonly bool $retryable = false, ?Throwable $previous = null)
    {
        parent::__construct($message, 0, $previous);
    }
}