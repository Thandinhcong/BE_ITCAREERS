<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\ManagementWebResource;
use App\Models\ManagementWeb;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ManagementWebController extends Controller
{

    public function index()
    {
        $man_web = ManagementWeb::all();
        return ManagementWebResource::collection($man_web);
    }


    public function show(string $id)
    {
        $man_web = ManagementWeb::find($id);
        if ($man_web) {
            return response()->json([
                'status' => 200,
                'man_web' => $man_web
            ], 200);
        } else {
            return response()->json([
                'status' => 'fail',
                'man_web' => 'Không có bản ghi nào'
            ], 404);
        }
    }


    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'logo' => '',
            'banner' => '',
            'name_web' => 'required|string',
            'company_name' => 'required|string',
            'address' => 'required|string',
            'email' => 'required|string',
            'phone' => 'required|string',
            'sdt_lienhe' => 'required|string'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'fail',
                'errors' => $validator->messages()
            ], 400);
        }
        $man_web = ManagementWeb::find($id);
        if ($man_web) {
            $man_web->update($request->all());
            return response()->json([
                'status' => 'success',
                'message' => 'Cập nhật thành công!'
            ], 200);
        } else {
            return response()->json([
                'status' => 'fail',
                'message' => 'error'
            ], 500);
        }
    }
}
