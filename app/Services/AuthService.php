<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class AuthService
{
    // تم إزالة $otpService

    public function __construct()
    {
        // لا حاجة لشيء هنا
    }

    public function registerUser(array $data, $profileImage, $idImage): User
    {
        // تم إزالة التحقق من OTP ($this->otpService->verifyOtp)

        // رفع الصور
        $data['profile_image'] = $profileImage->store('profiles', 'public');
        $data['id_image'] = $idImage->store('ids', 'public');

        $data['status'] = 'pending'; // ينتظر موافقة الأدمن

        // إنشاء المستخدم مباشرة
        $user = User::create($data);

        return $user;
    }

    public function loginUser(string $phone, ?string $fcmToken = null): array
    {
        // تم إزالة التحقق من OTP

        $user = User::where('phone', $phone)->first();

        if (!$user) {
            // ملاحظة: بما أن فايربيز قال الرقم صحيح، لكنه غير موجود عندنا،
            // فهذا يعني أن المستخدم يجب أن يذهب للتسجيل (Register) بدلاً من الدخول.
            // في التطبيق، سنعالج هذا الخطأ ونوجه المستخدم لصفحة التسجيل.
            throw ValidationException::withMessages(['phone' => 'User not found, please register first.']);
        }

        if ($user->status !== 'approved') {
            throw ValidationException::withMessages(['status' => 'Account not approved yet']);
        }

        if ($fcmToken) {
            $user->update(['fcm_token' => $fcmToken]);
        }

        $token = JWTAuth::fromUser($user);

        return ['user' => $user, 'token' => $token];
    }

    public function logout(): void
    {
        $token = JWTAuth::getToken();
        if (!$token) {
            throw new \Exception('Token not provided');
        }
        try {
            JWTAuth::invalidate($token);
        } catch (TokenInvalidException $e) {
            throw new \Exception('Token is invalid');
        }
    }

    public function updateProfile(User $user, array $data, $newProfileImage = null): User
    {
        if ($newProfileImage) {
            // يمكن إضافة منطق لحذف الصورة القديمة هنا إذا أردت
            // Storage::disk('public')->delete($user->profile_image);
            $data['profile_image'] = $newProfileImage->store('profiles', 'public');
        }

        $user->update($data);

        return $user;
    }
}
