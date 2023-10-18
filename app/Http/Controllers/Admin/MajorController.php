<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\MajorResource;
use App\Http\Resources\SalaryTypeResource;
use App\Models\Major;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MajorController extends Controller
{
    public function index()
    {
        $major = Major::all();
        if ($major->count() > 0) {
            return response()->json([
                'status' => 200,
                'major' => MajorResource::collection($major)
            ], 200);
        } else {
            return response()->json([
                'status' => 400,
                'message'=> "Không có dữ liệu",
                'major' => []
            ], 40);
        }
    }
    public function store(Request $request)
    {
        $valdator = Validator::make($request->all(), [
            'major' => 'required|string|max:55|unique:major|min:4',
            'description' => 'string|max:191'

        ]);
        if ($valdator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $valdator->messages()
            ], 422);
        } else {
            $major = Major::create(
                [
                    'major' => $request->major,
                ]
            );
        }
        if ($major) {
            return response()->json([
                'status' => 201,
                'message' => 'Tạo thành công'
            ], 200);
        } else {
            return response()->json([
                'status' => 500,
                'message' => 'Lỗi'
            ], 500);
        }
    }
    public function show($id)
    {
        $major = Major::find($id);
        if ($major) {
            return response()->json([
                'status' => 200,
                'major' => new MajorResource($major)
            ], 200);
        } else {
            return response()->json([
                'status' => 404,
                'major' => 'không có bản ghi nào'
            ], 404);
        }
    }
    public function update(Request $request, $id)
    {
        $valdator = Validator::make($request->all(), [
            'major' => 'required|string|max:55,unique:major,' . $id
        ]);
        if ($valdator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $valdator->messages()
            ], 422);
        } else {
            $major = Major::find($id);
        }
        if ($major) {
            $major->update(
                [
                    'major' => $request->major,
                ]
            );
            return response()->json([
                'status' => 201,
                'message' => 'Sửa thành công'
            ], 200);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'Không tìm thấy'
            ], 404);
        }
    }
    public function destroy($id)
    {
        $major = Major::find($id);
        if (!$major) {
            return response()->json(['message' => 'not found'], 404);
        }
        $major->delete();
        return response()->json(null, 204);
    }
}
