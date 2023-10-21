<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\CompanyManagementResource;
use App\Models\CompanyManagement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CompanyManagementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $company = CompanyManagement::all();
        return CompanyManagementResource::collection($company);
    }


    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'fail',
                'errors' => $validator->messages()
            ], 400);
        }
        $company = CompanyManagement::find($id);
        if ($company) {
            $company->update($request->all());
            return response()->json([
                'status' => 'success',
                'message' => 'Sửa trạng thái tài khoản thành công',
            ], 200);
        } else {
            return response()->json([
                'status' => 'fail',
                'message' => 'error'
            ], 500);
        }
    }

    public function destroy(string $id)
    {
        $company = CompanyManagement::find($id);
        $company->delete();
        return response()->json(['status' => 204, 'message' => 'xóa công ty thành công']);
    }
}
