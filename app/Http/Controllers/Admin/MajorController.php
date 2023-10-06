<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\MajorResource;
use App\Models\Major;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MajorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
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
                'status' => 404,
                'major' => 'không có bản ghi nào'
            ], 404);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $valdator = Validator::make($request->all(), [
            'major' => 'required|string|max:55'
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

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $major = Major::find($id);
        if ($major) {
            return response()->json([
                'status' => 200,
                'major' => $major
            ], 200);
        } else {
            return response()->json([
                'status' => 404,
                'major' => 'không có bản ghi nào'
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $valdator = Validator::make($request->all(), [
            'major' => 'required|string|max:55'
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
            $major ->update(
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

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $major = Major::find($id);
        if (!$major) {
            return response()->json(['message' => 'Major not found'], 404);
        }
        $major->delete();
        return response()->json(null, 204);
    }
}
