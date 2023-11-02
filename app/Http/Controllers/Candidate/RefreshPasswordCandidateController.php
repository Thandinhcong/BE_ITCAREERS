<?php

namespace App\Http\Controllers\Candidate;

use App\Http\Controllers\Controller;
use App\Models\Candidate;
use App\Models\Major;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RefreshPasswordCandidateController extends Controller
{


    public function store(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'password' => 'required',   
                'password_old' => 'required',
                're_password' => 'required|same:password',
            ],
            [
                'password.required' => 'Vui lòng nhập mật khẩu mới',
                'password_old.required' => 'Vui lòng nhập mật khẩu cũ',
                're_password.required' => 'Vui lòng nhập lại mật khẩu mới',
                're_password.same' => 'Mật khẩu nhập lại không khớp',
            ]
        );
        if ($validator->fails()) {
            return response()->json([
                'status' => 'fail',
                'errors' => $validator->messages('')
            ], 400);
        }
        $id = Auth::guard('candidate')->user()->id;
        $param = [];
        $param = $request->post();
        unset($param['_token']);
        $model =  Candidate::find($id);
        if (Hash::check($param['password_old'], auth('candidate')->user()->password)) {
            unset($param['password_old']);
            unset($param['re_password']);
            $candidate = $model->update($param);
            if ($candidate == 1) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Đổi mật khẩu thành công'
                ], 200);
            }
        } else {
            return response()->json([
                'status' => 'fail',
                'message' => 'Mật khẩu cũ không đúng'
            ], 500);
        }
    }
}
