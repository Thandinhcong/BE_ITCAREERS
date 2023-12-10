<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\SalaryTypeResource;
use App\Models\SalaryType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SalaryTypeController extends Controller
{
    public function index()
    {
        $salaryType = SalaryType::all();
        if ($salaryType->count() > 0) {
            return response()->json([
                'status' => 200,
                'salaryType' => SalaryTypeResource::collection($salaryType)
            ], 200);
        } else {
            return response()->json([
                'status' => 404,
                'salaryType' => 'không có bản ghi nào'
            ], 404);
        }
    }
    public function store(Request $request)
    {
        $valdator = Validator::make($request->all(), [
            'salary_type' => 'required|string|max:55|unique:salary_type|min:4',
        ]);
        if ($valdator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $valdator->messages(),
                'salaty_type' => $request->all()
            ], 422);
        } else {
            $salaryType = SalaryType::create($request->all());
        }
        if ($salaryType) {
            return response()->json([
                'status' => 201,
                'message' => 'Tạo thành công'
            ], 200);
        } else {
            return response()->json([
                'status' => 500,
                'message' => 'Lỗi'
            ], 500);
        }
    }
    public function show($id)
    {
        $salaryType = SalaryType::find($id);
        if ($salaryType) {
            return response()->json([
                'status' => 200,
                'salaryType' => new SalaryTypeResource($salaryType),
            ], 200);
        } else {
            return response()->json([
                'status' => 404,
                'salaryType' => 'không có bản ghi nào'
            ], 404);
        }
    }
    public function update(Request $request, $id)
    {
        $valdator = Validator::make($request->all(), [
            'salary_type' => 'required|string|max:55|unique:salary_type|min:4',
        ]);
        if ($valdator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $valdator->messages(),
                'salaty_type' => $request->all()
            ], 422);
        } else {
            $salaryType = SalaryType::find($id);
        }
        if ($salaryType) {
            $salaryType->update($request->all());
            return response()->json([
                'status' => 201,
                'message' => 'Sửa thành công'
            ], 200);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'not found'
            ], 404);
        }
    }
    public function destroy($id)
    {
        $salaryType = SalaryType::find($id);
        if (!$salaryType) {
            return response()->json(['message' => 'salary type not found'], 404);
        }
        $salaryType->delete();
        return response()->json(null, 204);
    }
}
