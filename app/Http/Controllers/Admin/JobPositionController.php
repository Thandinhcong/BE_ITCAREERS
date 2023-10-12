<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\JobPositionResource;
use App\Models\JobPosition;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class JobPositionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $job_position = JobPosition::all();
        if ($job_position->count() == 0) {
            return response()->json([
                "status" => 'fail',
                "message" => "Job Position empty",
            ], 404);
        }
        return response()->json([
            'status' => 'success',
            'Job_position' => JobPositionResource::collection($job_position),
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'job_position' => 'required|string|unique:job_position',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'fail',
                'errors' => $validator->messages()
            ], 400);
        } else {
            $job_position = JobPosition::create($request->all());
        }
        if ($job_position) {
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

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $job_position = JobPosition::find($id);
        if ($job_position) {
            return response()->json([
                'status' => 'success',
                'Job_position' => new JobPositionResource($job_position)
            ], 200);
        } else {
            return response()->json([
                'status' => 'fail',
                'message' => 'Job Position Not Found'
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'job_position' => 'required|string|unique:job_position,job_position,' . $id,
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'fail',
                'errors' => $validator->messages()
            ], 400);
        }

        $job_position = JobPosition::find($id);
        if ($job_position) {
            $job_position->update($request->all());
            return response()->json([
                'status' => 'success',
                'message' => 'Update Success'
            ], 200);
        } else {
            return response()->json([
                'status' => 'fail',
                'message' => 'Job Position Not Found'
            ], 404);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $job_position = JobPosition::find($id);
        if (!$job_position) {
            return response()->json([
                "message" => 'Job Position not found'
            ], 404);
        }
        $job_position->delete();
        return response()->json(null, 204);
    }
}
