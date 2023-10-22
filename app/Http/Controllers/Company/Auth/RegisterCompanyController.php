<?php

namespace App\Http\Controllers\Company\Auth;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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
        ]);
        $company->save();
        return response()->json([
            'status' => 'success',
        ], 200);
    }
}
