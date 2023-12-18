<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Jobs\SendEmailJob;
use App\Models\JobPostApply;
use App\Models\ManagementWeb;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class ManageCandidateApply extends Controller
{
    public function list_candidate_apply_job(string $id)
    {
        $list_candidate_apply_job = DB::table('job_post_apply')
            ->join('job_post', 'job_post.id', '=', 'job_post_apply.job_post_id')
            ->join('candidates', 'candidates.id', '=', 'job_post_apply.candidate_id')
            ->join('profile', 'profile.id', '=', 'job_post_apply.curriculum_vitae_id')
            ->orderByDesc('job_post_apply.type_apply')
            ->select(
                'job_post.title as job_post_name',
                'job_post.id as job_post_id',
                'job_post_apply.created_at as time_apply',
                'job_post_apply.qualifying_round_id',
                'job_post_apply.id as candidate_code',
                'job_post_apply.status',
                'job_post_apply.email',
                'job_post_apply.phone',
                'job_post_apply.name',
                'profile.path_cv',
                'candidates.image',
                'profile.id as curriculum_vitae_id',
            )
            ->where('job_post.id', $id)->get();
        if ($list_candidate_apply_job) {
            return response()->json([
                'status' => 200,
                'list_candidate_apply_job' => $list_candidate_apply_job
            ], 200);
        } else {
            return response()->json([
                'status' => 'fail',
                'level' => 'job post Not Found',
                'list_candidate_apply_job' => $list_candidate_apply_job
            ], 404);
        }
    }
    public function assses_candidate(Request $request, string $id)
    {
        $valdator = Validator::make($request->all(), [
            'evaluate' => 'required|string',
            'qualifying_round_id' => 'required|in:1,0',
        ]);
        if ($valdator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $valdator->messages(),
                'assses_candidate' => $request->all()
            ], 422);
        } else {
            $assses_candidate = JobPostApply::find($id);
        }
        if ($assses_candidate) {
            $assses_candidate->update($request->all());
            $candidate = DB::table('job_post_apply')->where('job_post_apply.id', $id)
                ->join('job_post', 'job_post_apply.job_post_id', '=', 'job_post.id')
                ->join('companies', 'companies.id', '=', 'job_post.company_id')
                ->join('candidates', 'candidates.id', '=', 'job_post_apply.candidate_id')
                ->select(
                    'job_post.title as job_post_title',
                    'companies.name as company_name',
                    'candidates.email as  candidate_email',
                    'job_post_apply.status',
                    'job_post_apply.evaluate',
                    'job_post_apply.email',
                    'job_post_apply.name',
                )
                ->first();
                $manage_web = ManagementWeb::find(1);
                $data = [];
                $data['email'] = $candidate->email;
                $data['name'] = $candidate->name;
                $data['job_post_title'] = $candidate->job_post_title;
                $data['logo'] =  $manage_web->logo;

                dispatch(new SendEmailJob(
                    $data,
                    $manage_web->name_web . ' - Nhà tuyển dụng đã đánh giá xong hồ sơ của bạn',
                    'emails.assses_candidate'
                ));
          
            return response()->json([
                'status' => 200,
                'mess' => "Đánh giá thành công"
            ], 200);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'not found'
            ], 404);
        }
    }
    public function candidate_detail(string $id)
    {
        $profile = Db::table('job_post_apply')
        ->join('profile', 'job_post_apply.curriculum_vitae_id', '=', 'profile.id')
        ->join('job_post', 'job_post.id', '=', 'job_post_apply.job_post_id')
        ->join('candidates', 'candidates.id', '=', 'job_post_apply.candidate_id')
        ->join('companies', 'job_post.company_id', '=', 'companies.id')
            ->select(
                'profile.name',
                'profile.title',
                'profile.id',
                'profile.email',
                'profile.phone',
                'profile.address',
                'candidates.id as candidate_id',
                'candidates.image',
                'job_post_apply.introduce',
                'job_post_apply.email as job_post_apply_email',
                'job_post_apply.name as job_post_apply_name',
                'job_post_apply.qualifying_round_id',
                'job_post_apply.id as candidate_code',
                'profile.path_cv',
                'job_post_apply.created_at',
                'job_post.title as job_post_title',
                'companies.company_name',
            )
            ->where('job_post_apply.id', $id)
            ->first();
        if ($profile) {
            $jobPostApply = JobPostApply::find( $profile->candidate_code);
            if ( $jobPostApply->status==0) {
                $jobPostApply->update(['status' => 1]);
                $manage_web = ManagementWeb::find(1);
                $data = [];
                $data['email'] = $profile->job_post_apply_email;
                $data['name'] = $profile->job_post_apply_name;
                $data['job_post_title'] = $profile->job_post_title;
                $data['logo'] =  $manage_web->logo;

                dispatch(new SendEmailJob(
                    $data,
                    $manage_web->name_web . ' - Nhà tuyển dụng đã xem hồ sơ của bạn',
                    'emails.company_see_profile'
                ));
              
            }
            return response()->json([
                'status' => 200,
                'data' => $profile
            ], 200);
        } else {
            return response()->json([
                'status' => 'fail',
                'data' => 'job post Not Found',
                'data' => $profile
            ], 404);
        }
    }
    public function list_candidate_applied()
    {
        $company_id = Auth::user()->id;
        $list_candidate_apply_job = DB::table('job_post_apply')
            ->join('job_post', 'job_post.id', '=', 'job_post_apply.job_post_id')
            ->join('candidates', 'candidates.id', '=', 'job_post_apply.candidate_id')
            ->join('profile', 'profile.id', '=', 'job_post_apply.curriculum_vitae_id')
            ->select(
                'job_post.title as job_post_name',
                'job_post.id as job_post_id',
                'job_post_apply.created_at as time_apply',
                'job_post_apply.qualifying_round_id',
                'job_post_apply.id as candidate_code',
                'job_post_apply.status',
                'job_post_apply.email',
                'job_post_apply.phone',
                'job_post_apply.name',
                'candidates.image',
                'profile.path_cv',
                'profile.id as curriculum_vitae_id',
            )
            ->where('job_post.company_id',  $company_id)
            ->get();
        if ($list_candidate_apply_job) {
            return response()->json([
                'status' => 200,
                'list_candidate_apply_job' => $list_candidate_apply_job
            ], 200);
        } else {
            return response()->json([
                'status' => 'fail',
                'level' => 'job post Not Found',
                'list_candidate_apply_job' => $list_candidate_apply_job
            ], 404);
        }
    }
}