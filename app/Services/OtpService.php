<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\ValidationException;

class OtpService
{
    public function generateAndSendOtp(string $phone): int
    {
        $otp = rand(1000, 9999);

        Cache::put('otp_' . $phone, $otp, now()->addMinutes(5));


        return $otp;
    }

    public function verifyOtp(string $phone, string $otp): void
    {
        $cachedOtp = Cache::get('otp_' . $phone);

        if (!$cachedOtp || $cachedOtp != $otp) {
            throw ValidationException::withMessages(['otp' => 'Invalid or expired OTP']);
        }
    }

    public function clearOtp(string $phone): void
    {
        Cache::forget('otp_' . $phone);
    }
}
