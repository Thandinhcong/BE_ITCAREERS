<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\ExpResource;
use App\Models\Exp;
use Illuminate\Http\Request;

class ExpController extends Controller
{
    // hiển thị danh sách trình độ 
    public function index()
    {
        $exp = Exp::all();
        return ExpResource::collection($exp);
    }


    // thêm trình độ 
    public function store(Request $request)
    {
        $exp = Exp::create($request->all());
        if ($exp) {
            return response()->json(['status' => 201, 'message' => "thêm trình độ thành công"]);
        }
        return new ExpResource($exp);
    }

    // chi tiêts trình độ 
    public function show(string $id)
    {
        $exp = Exp::find($id);
        if (!$exp) {
            return response()->json(['status' => 404, 'message' => "không tìm thấy trình độ"]);
        }
        return new ExpResource($exp);
    }

    // uodate trình độ 
    public function update(Request $request, string $id)
    {

        $exp = Exp::find($id);
        if (!$exp) {
            return response()->json(['status' => 404, 'message' => "không tìm thấy trình độ"]);
        }
        $exp->update($request->all());
        return response()->json(['status' => 200, 'message' => 'sửa trình độ thành công']);
    }

    // xóa trình độ 
    public function destroy(string $id)
    {
        $exp = Exp::find($id);
        if (!$exp) {
            return response()->json(['status' => 404, 'message' => "không tìm thấy trình độ"]);
        }
        $exp->delete();
        return response()->json(['status' => 200, 'message' => 'xóa trình độ thành công']);
    }
}
