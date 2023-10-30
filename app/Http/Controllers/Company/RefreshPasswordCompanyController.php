<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class RefreshPasswordCompanyController extends Controller
{
    public function store(Request $request)
    {
        $company = Auth::guard('company')->user();
        $id = Auth::guard('company')->user()->id;
        $validator = Validator::make($request->all(), [
            'password' => 'required|',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'fail',
                'errors' => $validator->messages()
            ], 400);
        }
        if ($company) {
            $company->update($request->all());
            return response()->json([
                'status' => 'success',
                'message' => 'Đổi mật khẩu thành công'
            ], 200);
        } else {
            return response()->json([
                'status' => 'fail',
            ], 500);
        }
    }
}
