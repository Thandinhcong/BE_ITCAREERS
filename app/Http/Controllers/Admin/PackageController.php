<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\PackageResource;
use App\Models\Packages;
use Illuminate\Http\Request;

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
        $package = Packages::create($request->all());
        if ($package) {
            return response()->json(['status' => 201, 'message' => "Thêm gói nạp thành công"]);
        }
        return new PackageResource($package);
    }

    // trang hiển thị chi tiết
    public function show(string $id)
    {
        $package = Packages::find($id);
        if (!$package) {
            return response()->json(['status' => 404, 'message' => "Không tìm thấy gói nạp đó"]);
        }
        return new PackageResource($package);
    }

    // trang sửa
    public function update(Request $request, string $id)
    {
        $package = Packages::find($id);
        if (!$package) {
            return response()->json(['status' => 404, 'message' => "Không tìm thấy gói nạp đó"]);
        }
        $package->update($request->all());
        return response()->json(['status' => 200, 'message' => "Sửa gói nạp thành công"]);;
    }


    //  trang xóa
    public function destroy(string $id)
    {
        $package = Packages::find($id);
        if (!$package) {
            return response()->json(['status' => 404, 'message' => "Không tìm thấy gói nạp"]);
        }
        $package->delete();
        return response()->json(['status' => 204, 'message' => "Xóa gói nạp thành công"]);
    }
}
