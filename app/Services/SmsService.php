<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsService
{
    public function enabled(): bool
    {
        return filled(config('services.twilio.sid'))
            && filled(config('services.twilio.token'))
            && filled(config('services.twilio.from'));
    }

    public function send(string $to, string $message): bool
    {
        if (!$this->enabled()) {
            return false;
        }

        try {
            $response = Http::withBasicAuth(config('services.twilio.sid'), config('services.twilio.token'))
                ->asForm()
                ->post(
                    'https://api.twilio.com/2010-04-01/Accounts/' . config('services.twilio.sid') . '/Messages.json',
                    [
                        'From' => config('services.twilio.from'),
                        'To' => $this->normalizeNumber($to),
                        'Body' => $message,
                    ]
                );

            return $response->successful();
        } catch (\Throwable $e) {
            Log::warning('SMS dispatch failed: ' . $e->getMessage());

            return false;
        }
    }

    protected function normalizeNumber(string $to): string
    {
        $digits = preg_replace('/[^0-9+]/', '', $to) ?? '';

        if ($digits === '') {
            return $to;
        }

        return str_starts_with($digits, '+') ? $digits : '+' . ltrim($digits, '+');
    }
}
