<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JobPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class JobPostController extends Controller
{
    public function index()
    {
        $jobPost = JobPost::all();
        if ($jobPost->count() > 0) {
            return response()->json([
                'status' => 200,
                'major' => $jobPost
            ], 200);
        } else {
            return response()->json([
                'status' => 404,
                'error' => 'không có bản ghi nào'
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        $valdator = Validator::make($request->all(), [
            'status' => 'required|in:0,1,2'
        ]);
        if ($valdator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $valdator->messages()
            ], 422);
        } else {
            $jobPost = jobPost::find($id);
        }
        if ($jobPost) {
            $jobPost->update(['status'=>$request->status]);
            return response()->json([
                'status' => 201,
                'message' => 'Sửa thành công',
            ], 200);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'Không tìm thấy'
            ], 404);
        }
    }
    public function messages()
    {
        return [
            'status.min' => 'Error',
            'status.max' => 'Error',
        ];
    }
}
