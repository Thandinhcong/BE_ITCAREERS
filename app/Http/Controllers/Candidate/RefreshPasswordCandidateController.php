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
    public function index()
    {
        $major = Major::all();
        $id = auth('candidate')->user()->id;
        $detail = Candidate::where('id', $id)->first();
        return view('change-pass', compact('major', 'detail'));
    }
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
        $param['cols'] = $request->post();
        unset($param['cols']['_token']);
        if (Hash::check($param['cols']['password_old'], auth('candidate')->user()->password)) {
            $model = new Candidate();
            unset($param['cols']['password_old']);
            unset($param['cols']['re_password']);
            $param['cols']['id'] = $id;
            $candidate = $model->update($param);
            if ($candidate == null) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Đổi mật khẩu thành công'
                ], 200);
            }
            if ($candidate == 1) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Đổi mật khẩu thành công'
                ], 200);
            } else {
                return response()->json([
                    'status' => 'fail',
                    'message' => 'Mật khẩu cũ không đúng'
                ], 500);
            }
        }
    }
}
