<?php

namespace App\Http\Controllers\Candidate;

use App\Http\Controllers\Controller;
use App\Http\Resources\CandidateApplyResource;
use App\Models\CandidateApply;
use App\Models\CurriculumVitae;
use App\Models\JobPostApply;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CandidateApplyController extends Controller
{
    public function job_apply()
    {
        $candidate_id = Auth::guard('candidate')->user()->id;
        $job_apply = DB::table('job_post_apply')->where('job_post_apply.candidate_id', $candidate_id)
            ->join('job_post', 'job_post_apply.job_post_id', '=', 'job_post.id')
            ->join('companies', 'companies.id', '=', 'job_post.company_id')
            ->join('district', 'district.id', '=', 'job_post.area_id')
            ->join('province', 'district.province_id', '=', 'province.id',)
            ->select(
                'job_post.id',
                'job_post.title',
                'district.name as district',
                'province.province',
                'job_post.min_salary',
                'job_post.max_salary',
                'companies.name as company_name',
                'companies.logo',
                'job_post_apply.created_at as time_apply',
            )->get();
        if ($job_apply) {
            return response()->json([
                'status' => 200,
                'job_list' => $job_apply
            ], 200);
        } else {
            return response()->json([
                'status' => 404,
                'mesage' => 'không có bản ghi nào',
                'job_list' => []
            ], 404);
        }
    }
    public function candidate_apply(Request $request, string $id)
    {
        $candidate_id = Auth::guard('candidate')->user()->id;
        $request['job_post_id'] = $id;
        $request['candidate_id'] = $candidate_id;
        $data_check = DB::table('job_post_apply')
            ->where('job_post_id', $id)
            ->where('candidate_id', $candidate_id)
            ->get();
        if ($data_check->count() > 0) {
            return response()->json([
                'error' => 'Bạn đã ứng tuyển',
            ], 400);
        } else {
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'phone' => 'required',
                'email' => 'required',
                'path_cv' => 'required_without:curriculum_vitae_id',
                // 'path_cv' => 'mimes:pdf'
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'status' => 'fail',
                    'errors' => $validator->messages()
                ], 400);
            }
            if ($request['path_cv'] && $request['curriculum_vitae_id']) {
                return response()->json([
                    'status' => 'fail',
                    'errors' => 'Không được chọn cả 2 '
                ], 400);
            } else {
                if ($request['path_cv']) {
                    $request['curriculum_vitae_id'] = DB::table('curriculum_vitae')->insertGetId(
                        [
                            'path_cv' => $request['path_cv'],
                            'candidate_id' => 1
                        ]
                    );
                    $candidate_apply = JobPostApply::create($request->all());
                } else {
                    $candidate_apply = JobPostApply::create($request->all());
                }
            }
            if ($candidate_apply) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Bạn đã ứng tuyển thành công ',
                    '$candidate_apply' => $candidate_apply
                ], 200);
            } else {
                return response()->json([
                    'status' => 'fail',
                ], 500);
            }
        }
    }
}
