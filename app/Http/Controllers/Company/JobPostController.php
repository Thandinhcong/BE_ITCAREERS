<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Experience;
use App\Models\JobPosition;
use App\Models\JobPost;
use App\Models\Level;
use App\Models\Major;
use App\Models\SkillPost;
use App\Models\WorkingForm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class JobPostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function job_post_select()
    {
        $d = [];
        $d['job_position'] = JobPosition::all();
        $d['exp'] = Experience::all();
        $d['level'] = Level::all();
        $d['working_form'] = WorkingForm::all();
        $d['academic_level'] = Level::all();
        $d['rank_id'] = Level::all();
        $d['major_id'] = Major::all();
        // if ($d['exp']->count() == 0) {
        //     return response()->json([
        //         "status" => 'fail',
        //         "message" => "Job Position empty",
        //     ], 404);
        // }
        return response()->json([
            'status' => 'success',
            'data' => $d,
        ], 200);
    }
    public function store(Request $request)
    {
        $valdator = Validator::make($request->all(), [
            'title' => 'required|',
            'job_position_id' => 'required|',
            'quantity' => 'required|integer',
            'academic_level_id' => 'required|',
            'exp_id' => 'required|',
            'working_form_id' => 'required|',
            'min_salary'=>'requiredl|less:max_salary',
            'max_salary'=>'required',
            'min_salary' => 'lte:max_salary',
            'salary_type'=>'required',
            'require' => 'required|',
            'interest' => 'required|',
            'level_id' => 'required|',
            'area_id' => 'required|',
            'major_id' => 'required|',
            'start_date' => 'required|',
            'end_date' => 'required|',
            'start_time' => 'lte:end_time',
        ]);
        $d = $request->all();
        if ($valdator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $valdator->messages(),
                'data' => $d
            ], 422);
        }
        else {
            $job_post = new JobPost($d);
            $job_post->save();
        }
        if ($d) {
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

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
