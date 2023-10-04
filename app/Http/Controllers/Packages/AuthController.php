<?php

namespace App\Http\Controllers\Packages;

use App\Http\Controllers\Controller;
use App\Models\Packages;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    // index chính
    public function index()
    {
        $package = Packages::all();
        return view('package.index', compact('package'));
    }

    // thêm gói nạp
    public function store(Request $request)
    {
        if ($request->isMethod('POST')) {
            $params = $request->except('_token');
            $package = Packages::create($params);
            if ($package->id) {
                return redirect()->route('package.add')->with('success', 'them thanh cong');
            }
        }
        return view('package.add');
    }

    // sửa gói nạp
    public function edit(Request $request, string $id)
    {
        $package = Packages::find($id);
        if ($request->isMethod('POST')) {
            $params = $request->except('_token');
            $result = $package->update($params);
            if ($result) {
                return redirect()->route('package.edit', ['id' => $id])->with('success', 'sua thanh cong');
            }
        }
        return view('package.edit', compact('package'));
    }

    // xoá gói nạp
    public function destroy(string $id)
    {
        $package = Packages::find($id);
        $package->delete();
        return redirect()->back()->with('success', 'xoa thanh cong');
    }
}
