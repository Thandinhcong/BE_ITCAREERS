<?php

namespace App\Http\Controllers\Company\Auth;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;

class RegisterCompanyController extends Controller
{
    public function register(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'company_name' => 'required|string',
            'email' => 'required|string|email|unique:companies',
            'password' => 'required|string|confirmed',
            'phone' => 'required|string|unique:companies',
            'link_web' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'fails',
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors()->toArray(),
            ]);
        }
        $company = new Company([
            'company_name' => $request->input('company_name'),
            'email' => $request->input('email'),
            'password' => bcrypt($request->input('password')),
            'phone' => $request->input('phone'),
            'link_web' => $request->input('link_web'),
            'name' => $request->input('name'),
            'address' => $request->input('address'),
        ]);
        $company->save();
        return response()->json([
            'status' => 'success',
        ], 200);
    }
    // public function refreshGetCompany()
    // {
    //     return view('emails.refresh-pass-company');
    // }

    public function PassCompanies(Request $request)
    {
        // dd(211);
        // $request->validate([
        //     'email' => 'required|exists:candidates',
        // ], [
        //     'email.required' => 'Email không được để trống',
        //     'email.exists' => 'Email không tồn tại trên hệ thống'
        // ]);
        // dd($request->email);
        $candidate = Company::where('email', $request->email)->first();
        $token = strtoupper(Str::random(10));
        $candidate->update([
            'remember_token' => $token,
        ]);
        Mail::send('emails.refresh-pass-company', compact('candidate'), function ($email) use ($candidate) {
            $email->subject(' Lấy Lại Mật Khẩu');
            $email->to($candidate->email, $candidate->name);
        });
        // return redirect()->back()->with('success', 'Vui Lòng Kiểm Tra Mail Để Thực Hiện Thay Đổi Mật Khẩu');
        return response()->json('success');
    }

    // public function getPassCompany()
    // {
    //     return view('emails.get-pass-company');
    // }

    // public function PostPassCompanies(Company $candidate, Request $request)
    // {
    //     if ($candidate->token === $request->token) {
    //         if ($request->password === $request->password2) {
    //             $candidate->update([
    //                 'remember_token' => null,
    //                 'password' => bcrypt($request->password)
    //             ]);
    //             return  response()->json('success', 'Đổi mật khẩu thành công');
    //         } else {
    //             return back()->with('error', 'Mật khẩu không trùng khớp');
    //         }
    //     }
    // }
}
