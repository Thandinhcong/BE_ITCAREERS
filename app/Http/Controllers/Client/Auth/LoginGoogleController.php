<?php

namespace App\Http\Controllers\Client\Auth;

use App\Http\Controllers\Controller;
use App\Models\Candidate;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;

class LoginGoogleController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback(Request $request)
    {
        $google_user = Socialite::driver('google')->user();
        $google_user->expiresIn = 6 * 3600;
        $user = Candidate::where('google_id', $google_user->getId())->orWhere('email', $google_user->getEmail())->first();
        if ($user) {
            return response()->json([
                'message' => 'Tài khoản đã tồn tại',
                'access_token' => $google_user->token,
                'token_type' => 'Bearer',
                'expires_in' => $google_user->expiresIn,
            ], 200);
        } else {
            $new_user = Candidate::create([
                'name' => $google_user->getName(),
                'email' => $google_user->getEmail(),
                'google_id' => $google_user->getId(),
                'image' => $google_user->getAvatar(),
            ]);

            return response()->json([
                'message' => 'Tạo tài khoản thành công',
                'access_token' => $google_user->token,
                'token_type' => 'Bearer',
                'expires_in' => $google_user->expiresIn,
            ], 200);
        }
    }
}
