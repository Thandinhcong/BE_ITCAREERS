<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\WorkingFormResources;
use App\Models\WorkingForm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WorkingFormController extends Controller
{
    //
    public function index()
    {
        $workingForm = WorkingFormResources::collection(WorkingForm::all());
        if ($workingForm->count() > 0) {
            return response()->json([
                'status' => 200,
                'workingForm' => $workingForm
            ], 200);
        } else {
            return response()->json([
                'status' => 404,
                'workingForm' => 'không có bản ghi nào'
            ], 404);
        }
    }
    public function store(Request $request)
    {
        $valdator = Validator::make($request->all(), [
            'working_form' => 'required|string|max:55|unique:working_form',
            'description' => 'string|max:191'
        ]);
        if ($valdator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $valdator->messages()
            ], 422);
        } else {
            $workingForm = WorkingForm::create(
                [
                    'working_form' => $request->working_form,
                    'description' => $request->description,
                ]
            );
        }
        if ($workingForm) {
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
        $workingForm =WorkingForm::find($id);
        if ($workingForm) {
            return response()->json([
                'status' => 200,
                'workingForm' => new WorkingFormResources($workingForm)
            ], 200);
        } else {
            return response()->json([
                'status' => 404,
                'major' => 'không có bản ghi nào'
            ], 404);
        }
    }
    public function update(Request $request, $id)
    {
        $valdator = Validator::make($request->all(), [
            'working_form' => 'required|string|max:55|unique:working_form,working_form,' . $id,
            'description' => 'string|max:191'
        ]);

        $workingForm = WorkingForm::find($id);
        if ($workingForm) {
            if ($valdator->fails()) {
                return response()->json([
                    'status' => 422,
                    'errors' => $valdator->messages(),
                ], 422);
            } else {
                $workingForm->update(
                    $request->all()
                );
                return response()->json([
                    'status' => 201,
                    'message' => 'Sửa thành công'
                ], 200);
            }
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'Không tìm thấy'
            ], 404);
        }
    }
    public function destroy($id)
    {
        $workingForm = WorkingForm::find($id);
        if (!$workingForm) {
            return response()->json(['message' => 'working form not found'], 404);
        }
        $workingForm->delete();
        return response()->json(null, 204);
    }
}
