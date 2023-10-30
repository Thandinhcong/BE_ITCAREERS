<?php

namespace App\Http\Controllers\Candidate;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class RefreshPasswordCandidateController extends Controller
{
    public function store(Request $request)
    {
        $candidate = Auth::guard('candidate')->user();
        $id = Auth::guard('candidate')->user()->id;
        $validator = Validator::make($request->all(), [
            'password' => 'required|',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'fail',
                'errors' => $validator->messages()
            ], 400);
        }
        if ($candidate) {
            $candidate->update($request->all());
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
