<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JobPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class JobPostController extends Controller
{
    public function index()
    {
        $jobPost = DB::table('job_post')
            ->join('job_position', 'job_position.id', '=', 'job_post.job_position_id')
            ->join('experiences', 'experiences.id', '=', 'job_post.exp_id')
            ->join('companies', 'companies.id', '=', 'job_post.company_id')
            ->join('working_form', 'working_form.id', '=', 'job_post.working_form_id')
            ->join('academic_level', 'academic_level.id', '=', 'job_post.academic_level_id')
            ->join('major', 'major.id', '=', 'job_post.major_id')
            ->join('type_job_post', 'type_job_post.id', '=', 'job_post.type_job_post_id')
            ->join('district', 'district.id', '=', 'job_post.area_id')
            ->join('province', 'district.province_id', '=', 'province.id',)
            ->select(
                'job_post.id',
                'job_post.title',
                'job_post.min_salary',
                'job_post.max_salary',
                'job_position.job_position',
                'experiences.experience',
                'companies.company_name',
                'companies.name',
                'companies.description',
                'companies.address',
                'companies.logo',
                'working_form.working_form',
                'academic_level.academic_level',
                'major.major',
                'district.name',
                'province.province',
                'job_post.start_date',
                'job_post.end_date',
                'job_post.quantity',
                'job_post.requirement as require',
                'job_post.interest',
                'job_post.interest',
                'job_post.desc',
                'job_post.gender',
                'job_post.status',
                'job_post.type_job_post_id',
                'type_job_post.name as type_job_post',
            )->get();

        if ($jobPost->count() > 0) {
            return response()->json([
                'status' => 200,
                'jobPost' => $jobPost
            ], 200);
        } else {
            return response()->json([
                'status' => 404,
                'error' => 'không có bản ghi nào'
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        $valdator = Validator::make($request->all(), [
            'status' => 'required|in:1,2'
        ]);
        if ($valdator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $valdator->messages()
            ], 422);
        } else {
            $jobPost = jobPost::find($id);
        }
        if ($jobPost) {
            $jobPost->update(['status' => $request->status]);
            // Mail::send('emails.notification_job_post_status', compact('jobPost', 'manage_web', 'job_apply', 'company_apply'), function ($email) use ($candidate_apply, $manage_web) {
            //     $email->subject($manage_web->name_web . ' - Bạn đã ứng tuyển thành công');
            //     $email->to($candidate_apply->email);
            // });
            return response()->json([
                'status' => 201,
                'message' => 'Sửa thành công',
            ], 200);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'Không tìm thấy'
            ], 404);
        }
    }

}
