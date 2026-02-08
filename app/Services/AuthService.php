<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class AuthService
{

    public function __construct()
    {
    }

    public function registerUser(array $data, $profileImage, $idImage): User
    {

        $data['profile_image'] = $profileImage->store('profiles', 'public');
        $data['id_image'] = $idImage->store('ids', 'public');

        $data['status'] = 'pending';

        $user = User::create($data);

        return $user;
    }

    public function loginUser(string $phone, ?string $fcmToken = null): array
    {

        $user = User::where('phone', $phone)->first();

        if (!$user) {
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
            $data['profile_image'] = $newProfileImage->store('profiles', 'public');
        }

        $user->update($data);

        return $user;
    }
}
