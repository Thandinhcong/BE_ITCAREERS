<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\DegreeResource;
use App\Models\Degrees;
use Illuminate\Http\Request;

class DegreeController extends Controller
{
    // hiển thị danh sách trình độ 
    public function index()
    {
        $degree = Degrees::all();
        return DegreeResource::collection($degree);
    }


    // thêm trình độ 
    public function store(Request $request)
    {
        $degree = Degrees::create($request->all());
        if ($degree) {
            return response()->json(['status' => 201, 'message' => "thêm trình độ thành công"]);
        }
        return new DegreeResource($degree);
    }

    // chi tiêts trình độ 
    public function show(string $id)
    {
        $degree = Degrees::find($id);
        if (!$degree) {
            return response()->json(['status' => 404, 'message' => "không tìm thấy trình độ"]);
        }
        return new DegreeResource($degree);
    }

    // uodate trình độ 
    public function update(Request $request, string $id)
    {

        $degree = Degrees::find($id);
        if (!$degree) {
            return response()->json(['status' => 404, 'message' => "không tìm thấy trình độ"]);
        }
        $degree->update($request->all());
        return response()->json(['status' => 200, 'message' => 'sửa trình độ thành công']);
    }

    // xóa trình độ 
    public function destroy(string $id)
    {
        $degree = Degrees::find($id);
        if (!$degree) {
            return response()->json(['status' => 404, 'message' => "không tìm thấy trình độ"]);
        }
        $degree->delete();
        return response()->json(['status' => 200, 'message' => 'xóa trình độ thành công']);
    }
}
