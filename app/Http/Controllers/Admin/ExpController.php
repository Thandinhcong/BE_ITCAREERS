<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\ExpResource;
use App\Models\Exp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ExpController extends Controller

{
    public function index()
    {
        $exp = Exp::all();
        return ExpResource::collection($exp);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'company_name' => 'required|string|unique:exp',
            'postion' => 'required|string',
            'start_date' => 'required',
            'end_date' => 'required',

        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'fail',
                'errors' => $validator->messages()
            ], 400);
        } else {
            $exp = Exp::create($request->all());
        }
        if ($exp) {
            return response()->json([
                'status' => 'success',
                'message' => 'Thêm thành công'
            ], 200);
        } else {
            return response()->json([
                'status' => 'fail',
                'message' => 'error'
            ], 500);
        }
    }
    public function show($id)
    {
        $exp = Exp::find($id);
        if ($exp) {
            return response()->json([
                'status' => 200,
                'exp' => $exp
            ], 200);
        } else {
            return response()->json([
                'status' => 'fail',
                'exp' => 'Exp Not Found'
            ], 404);
        }
    }
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'company_name' => 'required|string',
            'postion' => 'required|string',
            'start_date' => 'required',
            'end_date' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'fail',
                'errors' => $validator->messages()
            ], 400);
        }

        $exp = Exp::find($id);
        if ($exp) {
            $exp->update($request->all());
            return response()->json([
                'status' => 'success',
                'message' => 'Update Success'
            ], 200);
        } else {
            return response()->json([
                'status' => 'fail',
                'message' => 'Exp Not Found'
            ], 404);
        }
    }

    public function destroy(string $id)
    {
        $exp = Exp::find($id);
        if (!$exp) {
            return response()->json([
                "message" => 'Exp not found'
            ], 404);
        }
        $exp->delete();
        return response()->json(['status' => 204, 'message' => 'xoá thành công']);
    }
}
