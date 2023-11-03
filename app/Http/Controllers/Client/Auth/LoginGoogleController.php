<?php

namespace App\Http\Controllers\Client\Auth;

use App\Http\Controllers\Controller;
use App\Models\Candidate;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;

class LoginGoogleController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }
  
    public function handleGoogleCallback()
    {

        $google_user = Socialite::driver('google')->user();
        $user = Candidate::where('google_id', $google_user->getId())->first();
        if ($user) {
            return response()->json([
                'message' => 'Tài khoản đã tồn tại',
            ], 200);
            // auth()->login($user);
        } else {
            $new_user = Candidate::create([
                'name' => $google_user->getName(),
                'email' => $google_user->getEmail(),
                'google_id' => $google_user->getId()
            ]);
            // auth()->login($new_user);
            return response()->json([
                'message' => 'Tạo tài khoản thành công',
            ], 200);
        }
    }
}
