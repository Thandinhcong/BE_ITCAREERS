<?php

namespace App\Http\Controllers\Candidate\Auth;

use App\Http\Controllers\Controller;
use App\Models\Candidates;
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
        if (!Auth::guard('candidate')->attempt($credentials)) {
            return response()->json([
                'status' => 'fails',
                'message' => 'Unauthorized'
            ], 401);
        }
        $user = Auth::guard('candidate')->user();
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

    public function user()
    {
        response()->json([
            'user' =>  'success',
        ]);
    }
}
