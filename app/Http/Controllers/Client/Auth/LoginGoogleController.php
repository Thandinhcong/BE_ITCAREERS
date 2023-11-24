<?php

namespace App\Http\Controllers\Client\Auth;

use App\Http\Controllers\Controller;
use App\Models\Candidate;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Cookie;

class LoginGoogleController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback(Request $request)
    {
        // try {
        //     $google_user = Socialite::driver('google')->user();
        //     $google_user->expiresIn = 6 * 3600;

        //     $user = Candidate::where('google_id', $google_user->getId())
        //         ->orWhere('email', $google_user->getEmail())
        //         ->first();
        //     if ($user) {
        //         $token = $user->createToken('API Token')->accessToken;
        //         $cookie = Cookie::make('access_token', $token, 120); // 120 phút
        //         return redirect()->away('http://localhost:5173')->withCookie($cookie);
        //     } else {
        //         $new_user = Candidate::create([
        //             'name' => $google_user->getName(),
        //             'email' => $google_user->getEmail(),
        //             'google_id' => $google_user->getId(),
        //             'image' => $google_user->getAvatar(),
        //         ]);
        //         $token = $new_user->createToken('API Token')->accessToken;
        //         $cookie = Cookie::make('access_token', $token, 120); // 120 phút
        //         return redirect()->away('http://localhost:5173')->withCookie($cookie);
        //     }
        // } catch (\Exception $e) {
        //     return response()->json(['error' => $e->getMessage()], 500);
        // }
        try {
            $google_user = Socialite::driver('google')->user();
            $user = Candidate::where('google_id', $google_user->getId())
                ->orWhere('email', $google_user->getEmail())
                ->first();
            if ($user) {
                Auth::login($user);
                return redirect()->away('http://localhost:5173');
            } else {
                $new_user = Candidate::create([
                    'name' => $google_user->getName(),
                    'email' => $google_user->getEmail(),
                    'google_id' => $google_user->getId(),
                    'image' => $google_user->getAvatar(),
                ]);
                Auth::login($new_user);
                return redirect()->away('http://localhost:5173');
            }
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
