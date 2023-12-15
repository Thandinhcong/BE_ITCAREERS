<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
            'remember_me' => 'boolean'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'fails',
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors()->toArray(),
            ]);
        }
        $credentials = request(['email', 'password']);
        if (!Auth::attempt($credentials, $remember = true)) {
            return response()->json([
                'status' => false,
                'message' => 'Tài khoản hoặc mật khẩu không chính xác'
            ], 400);
        }
        if (Auth::user()->status == 0) {
            return response()->json([
                'status' => false,
                'message' => 'Tài khoản của bạn đã bị khóa!!!'
            ], 400);
        }
        $user = $request->user();
        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->token;
        ////        if ($request->input('remember_me')) {
        $token->expires_at = Carbon::now()->addMinute(1);

        ////        }

        $token->save();
        return response()->json([
            'status' => 'success',
            'access_token' => $tokenResult->accessToken,
            'token_type' => 'Bearer',
            'expires_at' => Carbon::parse(
                $tokenResult->token->expires_at
            )->toDateTimeString()
        ]);
    }



    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return response()->json([
            'status' => 'success',
        ]);
    }
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function user(Request $request)
    {

        return response()->json($request->user());
    }
}
