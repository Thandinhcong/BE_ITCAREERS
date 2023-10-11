<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\CompanyResource;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CompanyController extends Controller
{
    // trang hiển thị
    public function index()
    {
        $company = Company::all();
        return CompanyResource::collection($company);
    }

    // thêm
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'company_name' => 'required|string|unique:companies',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'fail',
                'errors' => $validator->messages()
            ], 400);
        } else {
            $company = Company::create($request->all());
        }
        if ($company) {
            return response()->json([
                'status' => 'success',
                'message' => 'Add new success',
            ], 200);
        } else {
            return response()->json([
                'status' => 'fail',
                'message' => 'error'
            ], 500);
        }
    }

    // trang hiển thị chi tiết
    public function show(string $id)
    {
        $company = Company::find($id);
        if ($company) {
            return response()->json([
                'status' => 200,
                'company' => $company
            ], 200);
        } else {
            return response()->json([
                'status' => 'fail',
                'major' => 'Company Not Found'
            ], 404);
        }
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'company_name' => 'required|string'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'fail',
                'errors' => $validator->messages()
            ], 400);
        }

        $company = Company::find($id);
        if ($company) {
            $company->update($request->all());
            return response()->json([
                'status' => 'success',
                'message' => 'Update Success'
            ], 200);
        } else {
            return response()->json([
                'status' => 'fail',
                'message' => 'Company Not Found'
            ], 404);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $company = Company::find($id);
        if (!$company) {
            return response()->json([
                "message" => 'Company not found'
            ], 404);
        }
        $company->delete();
        return response()->json(null, 204);
    }
}
