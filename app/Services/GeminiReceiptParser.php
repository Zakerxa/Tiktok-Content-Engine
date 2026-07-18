<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class GeminiReceiptParser
{
    // Tried in order, top to bottom, on any failure. gemini-1.5-flash (the
    // original choice) has since been shut down — keep this list current
    // against https://ai.google.dev/gemini-api/docs/models, prices/availability
    // shift often.
    private const MODEL_TIERS = [
        'gemini-3.5-flash',
        'gemini-3.1-flash-lite',
        'gemini-2.5-flash',
    ];

    // Attempts on the SAME model before moving to the next tier.
    private const MAX_ATTEMPTS_PER_MODEL = 2;
    private const RETRY_BASE_DELAY_MS = 400; // linear backoff: 400ms, 800ms, ...

    private const SYSTEM_PROMPT = <<<'PROMPT'
    You are an expert financial receipt parser for Myanmar Mobile Banking (KBZPay and WavePay). Analyze the provided receipt screenshot and extract the transaction details.
    
    You must convert all Myanmar numbers (e.g. ၁, ၂, ၃, ၁၇,၅၀၀) into standard English Arabic numerals (e.g. 1, 2, 3, 17500).
    Convert the date/time into standard "YYYY-MM-DD HH:MM:SS" format, regardless of what format or language it's written in on the receipt.
    
    Return ONLY the following JSON object — no markdown fences, no commentary:
    
    {
      "bank_type": "KBZPay" or "WavePay" or "Unknown",
      "transaction_id": "the transaction number/အိုင်ဒီ, as a string",
      "amount": integer_value_here,
      "date_time": "YYYY-MM-DD HH:MM:SS",
      "receiver_name": "the name of the person who RECEIVED the money — the 'Transfer To' / 'သို့' field, NOT the sender. Include exactly as written, even if a masked phone number is appended.",
      "sender_phone": "sender phone if visible, otherwise null",
      "note": "the note/message/အကြောင်းအရာ field, otherwise null"
    }
    PROMPT;

    /**
     * OCRs a screenshot already stored on the `local` disk. Walks
     * MODEL_TIERS in order — each model gets up to MAX_ATTEMPTS_PER_MODEL
     * tries for transient errors (timeout, 429, 5xx) before falling
     * through to the next tier. Non-transient errors (bad key, bad
     * request, model gone) skip straight to the next tier without
     * wasting a retry.
     *
     * @throws RuntimeException only if every model in every tier fails.
     */
    public function parse(string $storagePath): array
    {
        $bytes = Storage::disk('local')->get($storagePath);
        if ($bytes === null) {
            throw new RuntimeException("Screenshot not found on disk: {$storagePath}");
        }

        // mime_content_type() on the resolved absolute path instead of
        // Storage::mimeType() — avoids Flysystem-version quirks.
        $absolutePath = Storage::disk('local')->path($storagePath);
        $mime = mime_content_type($absolutePath) ?: 'image/jpeg';

        $apiKey = config('services.gemini.key');
        if (!$apiKey) {
            throw new RuntimeException('GEMINI_API_KEY is not configured (see config/services.php).');
        }

        $errors = [];

        foreach (self::MODEL_TIERS as $model) {
            for ($attempt = 1; $attempt <= self::MAX_ATTEMPTS_PER_MODEL; $attempt++) {
                try {
                    return $this->callModel($model, $mime, $bytes, $apiKey);
                } catch (GeminiRequestException $e) {
                    $errors[] = "{$model} attempt {$attempt}: {$e->getMessage()}";

                    // Not worth retrying this model (bad key/request/404),
                    // or we've used up its attempts — move to the next tier.
                    if (!$e->retryable || $attempt === self::MAX_ATTEMPTS_PER_MODEL) {
                        break;
                    }

                    usleep(self::RETRY_BASE_DELAY_MS * 1000 * $attempt);
                }
            }
        }

        throw new RuntimeException('All Gemini model tiers failed — ' . implode(' | ', $errors));
    }

    /**
     * @throws GeminiRequestException
     */
    private function callModel(string $model, string $mime, string $bytes, string $apiKey): array
    {
        $endpoint = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent";

        try {
            $response = Http::timeout(30)->post($endpoint . '?key=' . $apiKey, [
                'contents' => [[
                    'parts' => [
                        ['text' => self::SYSTEM_PROMPT],
                        ['inline_data' => [
                            'mime_type' => $mime,
                            'data'      => base64_encode($bytes),
                        ]],
                    ],
                ]],
                'generationConfig' => [
                    'temperature'        => 0,
                    'response_mime_type' => 'application/json',
                ],
            ]);
        } catch (ConnectionException $e) {
            // Network blip / request timed out — worth retrying.
            throw new GeminiRequestException("connection error: {$e->getMessage()}", retryable: true, previous: $e);
        }

        if ($response->failed()) {
            $status = $response->status();

            // 429 (rate limited) and 5xx (server-side) are transient —
            // retry. 4xx like bad key / bad request / model deprecated
            // won't fix itself on retry, so fall through to the next tier.
            $retryable = $status === 429 || $status >= 500;

            throw new GeminiRequestException("HTTP {$status}: {$response->body()}", retryable: $retryable);
        }

        $text = data_get($response->json(), 'candidates.0.content.parts.0.text');
        if (!$text) {
            throw new GeminiRequestException('empty response from Gemini', retryable: true);
        }

        // Defensive — response_mime_type=json should prevent this, but strip
        // markdown fences if the model adds them anyway.
        $clean = trim(preg_replace('/^```json\s*|\s*```$/m', '', $text));

        $data = json_decode($clean, true);
        if (json_last_error() !== JSON_ERROR_NONE || !is_array($data)) {
            // Malformed JSON from the model — occasionally happens even at
            // temperature 0, worth one retry before giving up on this model.
            throw new GeminiRequestException("invalid JSON: {$clean}", retryable: true);
        }

        return $data;
    }
}
