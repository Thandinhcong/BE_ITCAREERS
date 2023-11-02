<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\LevelResource;
use App\Models\Level;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LevelController extends Controller
{
    // trang hien thi
    public function index()
    {
        $level = Level::all();
        return LevelResource::collection($level);
    }


    // trang them
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'level' => 'required|string|max:55'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'fail',
                'errors' => $validator->messages()
            ], 400);
        } else {
            $level = Level::create($request->all());
        }
        if ($level) {
            return response()->json(
                [
                    'status' => 'success',
                    'message' => 'Thêm thành công', 
                    'data' => $request->all()
                ],
                200
            );
        } else {
            return response()->json(['status' => 'fail', 'message' => 'error'], 500);
        }
    }

    // trang hien thi chi tiet
    public function show(string $id)
    {
        $level = Level::find($id);
        if ($level) {
            return response()->json([
                'status' => 200,
                'level' => $level
            ], 200);
        } else {
            return response()->json([
                'status' => 'fail',
                'level' => 'Level Not Found'
            ], 404);
        }
        //
    }



    // trang sua
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'level' => 'required|string'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'fail',
                'errors' => $validator->messages()
            ], 400);
        }
        $level = Level::find($id);
        if ($level) {
            $level->update($request->all());
            return response()->json([
                'status' => 'success',
                'message' => 'Update Success'
            ], 200);
        } else {
            return response()->json([
                'status' => 'fail',
                'message' => 'Level Not Found'
            ], 404);
        }
    }

    //    trang xoa
    public function destroy(string $id)
    {
        $level = Level::find($id);
        if (!$level) {
            return response()->json([
                "message" => 'Level not found'
            ], 404);
        }
        $level->delete();
        return response()->json(['status' => 204, 'message' => "xoá thành công"]);
    }
}
