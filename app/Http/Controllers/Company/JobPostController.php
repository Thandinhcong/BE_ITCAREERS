<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\AcademicLevel;
use App\Models\Experience;
use App\Models\JobPosition;
use App\Models\JobPost;
use App\Models\JobPostApply;
use App\Models\Level;
use App\Models\Major;
use App\Models\SkillPost;
use App\Models\WorkingForm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class JobPostController extends Controller
{
    public function index()
    {
        $company_id = Auth::guard('company')->user()->id;
        $job_post = JobPost::all()->where('company_id', $company_id);
        if ($job_post->count() == 0) {
            return response()->json([
                "status" => 'fail',
                "message" => "Job Post empty",
            ], 404);
        }
        return response()->json([
            'status' => 'success',
            'Job_position' => $job_post,
        ], 200);
    }
    public function job_post_select()
    {
        $d = [];
        $d['job_position'] = JobPosition::all();
        $d['exp'] = Experience::all();
        $d['level'] = Level::all();
        $d['working_form'] = WorkingForm::all();
        $d['academic_level'] = AcademicLevel::all();
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
            'min_salary' => 'required',
            'max_salary' => 'required',
            'min_salary' => 'lte:max_salary',
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
        } else {
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
    public function update(Request $request, string $id)
    {
        $job_post = JobPost::find($id);
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'job_position_id' => 'required|',
            'quantity' => 'required|integer',
            'academic_level_id' => 'required|',
            'exp_id' => 'required|',
            'working_form_id' => 'required|',
            'min_salary' => 'required',
            'max_salary' => 'required',
            'min_salary' => 'lte:max_salary',
            'require' => 'required|',
            'interest' => 'required|',
            'level_id' => 'required|',
            'area_id' => 'required|',
            'major_id' => 'required|',
            'start_date' => 'required|',
            'end_date' => 'required|',
            'start_time' => 'lte:end_time',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'fail',
                'errors' => $validator->messages(),
                'data' => $job_post
            ], 400);
        }

        if ($job_post) {
            $job_post->update($request->all());
            return response()->json([
                'status' => 'success',
                'message' => 'Update Success',
                'data' => $job_post
            ], 200);
        } else {
            return response()->json([
                'status' => 'fail',
            ], 500);
        }
    }
    public function show(string $id)
    {
        $job_post = JobPost::find($id);
        if ($job_post) {
            return response()->json([
                'status' => 200,
                'level' => $job_post
            ], 200);
        } else {
            return response()->json([
                'status' => 'fail',
                'level' => 'job post Not Found',
                'job_post' => $job_post
            ], 404);
        }
        //
    }
    function list_candidate_apply_job(string $id)
    {
        $company_id = 1;
        // Auth::guard('company')->user()->id;
        $list_candidate_apply_job = DB::table('job_post_apply')
            ->join('job_post', 'job_position.id', '=', 'job_post.job_position_id')
            ->where('company_id', $company_id)->where('job_post_id', $id);
        if ($list_candidate_apply_job) {
            return response()->json([
                'status' => 200,
                'level' => $list_candidate_apply_job
            ], 200);
        } else {
            return response()->json([
                'status' => 'fail',
                'level' => 'job post Not Found',
                'job_post' => $list_candidate_apply_job
            ], 404);
        }
    }
}
