<?php

namespace App\Http\Controllers\Company\Auth;

use App\Http\Controllers\Controller;
use App\Jobs\SendEmailJob;
use App\Models\Company;
use App\Models\ManagementWeb;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

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
        if (!Auth::guard('company')->attempt($credentials, $remember = true)) {
            return response()->json([
                'status' => false,
                'message' => 'Tài khoản hoặc mật khẩu không chính xác'
            ], 400);
        }
        if (Auth::guard('company')->user()->status == 2) {
            return response()->json([
                'status' => false,
                'message' => 'Tài khoản của bạn đã bị khóa!!!'
            ], 400);
        } if (Auth::guard('company')->user()->email_verified_at == null) {
            return response()->json([
                'status' => false,
                'message' => 'Tài khoản của bạn chưa xác thực!!!'
            ], 400);
        }
        $user = Auth::guard('company')->user();
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
        ], 200);
    }

    public function user(Request $request)
    {
        return response()->json(Auth::guard('company')->user());
    }
    public function forget_password(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|exists:companies',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'fail',
                'errors' => $validator->messages()
            ], 400);
        }
        $company = Company::where('email', $request->email)
            ->first();
        $new_pass = strtoupper(Str::random(8));
        $company->update([
            'token' => null,
            'password' => bcrypt($new_pass)
        ]);
        $manage_web = ManagementWeb::find(1);
        $data = [];
        $data['email'] = $company->email;
        $data['name'] = $company->name;
        $data['new_pass'] = $new_pass;
        $data['name_web'] = $manage_web->name_web;
        $data['logo'] =  $manage_web->logo;
        dispatch(new SendEmailJob(
            $data,
            $manage_web->name_web . ' - Mật khẩu mới',
            'emails.forgetpass_company'
        ));
        return response()->json([
            'status' => 'success',
            'message' => 'Một mật khẩu mới đã được gửi đến email của bạn vui lòng kiểm tra nó'
        ], 200);
    }
}
