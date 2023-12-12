<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\TypeJobPostResource;
use App\Models\TypeJobPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TypeJobPostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $typejob = TypeJobPost::all();
        return TypeJobPostResource::collection($typejob);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|',
            'salary' => 'required',
            'desc' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'fail',
                'errors' => $validator->messages()
            ], 400);
        } else {
            $typejob = TypeJobPost::create($request->all());
        }
        if ($typejob) {
            return response()->json([
                'status' => 'success',
                'message' => 'Thêm thành công'
            ], 200);
        } else {
            return response()->json([
                'status' => 'fail',
                'message' => 'error'
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $typejob = TypeJobPost::find($id);
        if ($typejob) {
            return response()->json([
                'status' => 200,
                'typejob' => $typejob
            ], 200);
        } else {
            return response()->json([
                'status' => 'fail',
                'typejob' => 'typejob Not Found'
            ], 404);
        }
    }

    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'salary' => 'required',
            'desc' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'fail',
                'errors' => $validator->messages()
            ], 400);
        }

        $typejob = TypeJobPost::find($id);
        if ($typejob) {
            $typejob->update($request->all());
            return response()->json([
                'status' => 'success',
                'message' => 'Update Success'
            ], 200);
        } else {
            return response()->json([
                'status' => 'fail',
                'message' => 'typejob Not Found'
            ], 404);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $typejob = TypeJobPost::find($id);
        if (!$typejob) {
            return response()->json([
                "message" => 'typejob not found'
            ], 404);
        }
        $typejob->delete();
        return response()->json(['status' => 204, 'message' => 'xoá thành công']);
    }
}
