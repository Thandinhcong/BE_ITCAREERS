<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\PackageResource;
use App\Models\Packages;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PackageController extends Controller
{

    // trang hiển thị
    public function index()
    {
        $package = Packages::all();
        return PackageResource::collection($package);
    }

    //trang thêm
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:55'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'fail',
                'errors' => $validator->messages()
            ], 400);
        } else {
            $package = Packages::create($request->all());
        }
        if ($package) {
            return response()->json([
                'status' => 'success',
                'message' => 'Thêm thành công',
            ], 200);
        } else {
            return response()->json([
                'status' => 'fail',
                'message' => 'Thêm thất bại'
            ], 500);
        }
    }

    // trang hiển thị chi tiết
    public function show(string $id)
    {
        $package = Packages::find($id);
        if ($package) {
            return response()->json([
                'status' => 200,
                'package' => $package
            ], 200);
        } else {
            return response()->json([
                'status' => 'fail',
                'major' => 'Không tìm thấy gói nạp'
            ], 404);
        }
    }

    // trang sửa
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'fail',
                'errors' => $validator->messages()
            ], 400);
        }

        $package = Packages::find($id);
        if ($package) {
            $package->update($request->all());
            return response()->json([
                'status' => 'success',
                'message' => 'Cập nhật thành công gói nạp'
            ], 200);
        } else {
            return response()->json([
                'status' => 'fail',
                'message' => 'Job Position Not Found'
            ], 404);
        }
    }


    //  trang xóa
    public function destroy(string $id)
    {
        $package = Packages::find($id);
        if (!$package) {
            return response()->json(['status' => 404, 'message' => "Không tìm thấy gói nạp"], 404);
        }
        $package->delete();
        return response()->json(['status' => 204, 'message' => "Xóa gói nạp thành công"]);
    }
}
