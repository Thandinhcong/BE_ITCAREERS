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
        $skill = Skill::create($request->all());
        if ($skill) {
            return response()->json(['status' => 201, 'message' => "thêm kỹ năng thành công"]);
        }
        return new SkillResource($skill);
    }

    // chi tiết 
    public function show(string $id)
    {
        $skill = Skill::find($id);
        if (!$skill) {
            return response()->json(['status' => 404, 'message' => "không tìm thấy kỹ năng"]);
        }
        return new SkillResource($skill);
    }

    // sửa kỹ năng
    public function update(Request $request, string $id)
    {
        $skill = Skill::find($id);
        if (!$skill) {
            return response()->json(['status' => 404, 'message' => "không tìm thấy kỹ năng"]);
        }
        $skill->update($request->all());
        return response()->json(['status' => 200, 'message' => 'sửa kỹ năng thành công']);
    }

    // xóa kỹ năng
    public function destroy(string $id)
    {
        $skill = Skill::find($id);
        if (!$skill) {
            return response()->json(['status' => 404, 'message' => "không tìm thấy kỹ năng"]);
        }
        $skill->delete();
        return response()->json(['status' => 204, 'message' => 'xóa kỹ năng thành công']);
    }
}
