<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\SkillResource;
use App\Models\Skill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SkillController extends Controller
{

    // danh sach hien thi kỹ năng
    public function index()
    {
        $skill = Skill::all();
        return SkillResource::collection($skill);
    }

    // thêm kỹ năng
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'skill' => 'required|string|unique:skill',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'fail',
                'errors' => $validator->messages()
            ], 400);
        } else {
            $skill = Skill::create($request->all());
        }
        if ($skill) {
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

    // chi tiết 
    public function show(string $id)
    {
        $skill = Skill::find($id);
        if ($skill) {
            return response()->json(['status' => 200, 'skill' => $skill], 200);
        } else {
            return response()->json(['status' => 'fail', 'skill' => 'không tìm thấy kỹ năng'], 404);
        }
    }

    // sửa kỹ năng
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'skill' => 'required|string',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'fail',
                'errors' => $validator->messages()
            ], 400);
        }

        $skill = Skill::find($id);
        if ($skill) {
            $skill->update($request->all());
            return response()->json(['status' => 'success', 'message' => "sửa kỹ năng thành công"], 200);
        } else {
            return response()->json([
                'status' => 'fail', 'message' => 'Không tìm thấy kỹ năng'
            ], 404);
        }
    }

    // xóa kỹ năng
    public function destroy(string $id)
    {
        $skill = Skill::find($id);
        if (!$skill) {
            return response()->json(['message' => "không tìm thấy kỹ năng"], 404);
        }
        $skill->delete();
        return response()->json(['status' => 204, 'message' => 'xóa kỹ năng thành công']);
    }
}
