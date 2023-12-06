<?php

namespace App\Http\Controllers\Candidate;

use App\Http\Controllers\Controller;
use App\Http\Resources\CandidateApplyResource;
use App\Models\CandidateApply;
use App\Models\Company;
use App\Models\CurriculumVitae;
use App\Models\JobPost;
use App\Models\JobPostApply;
use App\Models\ManagementWeb;
use App\Models\SaveJobPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class CandidateApplyController extends Controller
{
    public function candidate_id()
    {
        return Auth::user()->id;
    }
    public function job_apply()
    {
        $candidate_id = Auth::user()->id;
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
                'companies.company_name as company_name',
                'companies.logo',
                'job_post_apply.created_at as time_apply',
                'job_post_apply.updated_at',
                'job_post_apply.status',
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
    public function candidate_id()
    {
        return Auth::user()->id;
    }
    public function candidate_apply(Request $request, string $id)
    {
        $candidate_id = Auth::user()->id;
        $request['job_post_id'] = $id;
        $request['candidate_id'] = $candidate_id;

        $data_check = DB::table('job_post_apply')
            ->where('job_post_id', $id)
            ->where('candidate_id', $candidate_id)
            ->get();
        $job_apply = JobPost::find($id);
        $now = now();
        if ($job_apply->status == 2 || $job_apply->end_date < $now) {
            return response()->json([
                'status' => 'fail',
                'error' => 'Bài đăng đã bị khóa do vi phạm các nguyên tắc của nền tảng
                 hoặc đã hết thời gian tuyển dụng'
            ], 400);
        }  if ( $job_apply->start_date > $now) {
            return response()->json([
                'status' => 'fail',
                'error' => 'Bài đăng chưa đến thời gian tuyển dụng'
            ], 400);
        }
        $company_apply = Company::find($job_apply->company_id);
        if ($data_check->count() > 0) {
            return response()->json([
                'error' => 'Bạn đã ứng tuyển',
            ], 400);
        } else {
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'phone' => 'required',
                'email' => 'required|email',
                'path_cv' => 'required_without:curriculum_vitae_id',
                // 'path_cv' => 'mimetypes:application/pdf|max:400000'
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
                    'errors' => 'Bạn chỉ được chọn 1 hồ sơ chính để ứng tuyển'
                ], 400);
            } else {
                if ($request['path_cv']) {
                    $request['curriculum_vitae_id'] = DB::table('profile')->insertGetId(
                        [
                            'name' => $request['name'],
                            'phone' => $request['phone'],
                            'email' => $request['email'],
                            'path_cv' => $request['path_cv'],
                            'candidate_id' => $this->candidate_id()
                        ]
                    );
                    $candidate_apply = JobPostApply::create($request->all());
                } else {
                    $candidate_apply = JobPostApply::create($request->all());
                }
            }
            if ($candidate_apply) {
                $manage_web = ManagementWeb::find(1);
                Mail::send('emails.candidate_apply', compact('candidate_apply', 'manage_web', 'job_apply', 'company_apply'), function ($email) use ($candidate_apply, $manage_web) {
                    $email->subject($manage_web->name_web . ' - Bạn đã ứng tuyển thành công');
                    $email->to($candidate_apply->email);
                });
                Mail::send('emails.notification_company_candidate_apply', compact('candidate_apply', 'manage_web', 'job_apply', 'company_apply'), function ($email) use ( $manage_web,$company_apply) {
                    $email->subject($manage_web->name_web . ' - Ứng viên ứng tuyển');
                    $email->to($company_apply->email);
                });
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
    public function show_save_job_post()
    {
        $candidate_id = Auth::user()->id;
        $data = DB::table('save_job_post')
            ->select(
                'job_post.id',
                'job_post.title',
                'district.name as district',
                'province.province',
                'job_post.min_salary',
                'job_post.max_salary',
                'companies.company_name as company_name',
                'companies.logo',
                'save_job_post.created_at'
            )
            ->join('job_post', 'save_job_post.job_post_id', '=', 'job_post.id')
            ->join('companies', 'companies.id', '=', 'job_post.company_id')
            ->join('district', 'district.id', '=', 'job_post.area_id')
            ->join('province', 'district.province_id', '=', 'province.id',)
            ->where('save_job_post.candidate_id', $candidate_id)
            ->where('start_date', '<=', now()->format('Y-m-d'))
            ->where('end_date', '>=', now()->format('Y-m-d'))
            // ->whereNull('save_job_post.deleted_at')
            ->get();
        return response()->json([
            "status" => 'success',
            "data" => $data,
        ], 200);
    }
    public function save_job_post($id)
    {
        $candidate_id = Auth::user()->id;
        $check = DB::table('save_job_post')
            ->where('job_post_id', $id)
            ->where('candidate_id', $candidate_id)
            ->first();
        if ($check) {
            return response()->json([
                'status' => 'fail',
                'error' => 'Đã lưu'
            ], 400);
        } else {
            $saveJobPost = SaveJobPost::create(
                [
                    'candidate_id' => $candidate_id,
                    'job_post_id' => $id
                ]
            );
        }
        if ($saveJobPost) {
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
    public function cancel_save_job_post($id)
    {
        $candidate_id = Auth::user()->id;
        $cancel_save_profile = SaveJobPost::where('candidate_id', $candidate_id)->where('job_post_id', $id)->first();
        // dd($cancel_save_profile);
        if (!$cancel_save_profile) {
            return response()->json(['error' => 'SaveJobPost not found'], 404);
        }
        $cancel_save_profile->delete();
        return response()->json([
            'message' => 'Xóa thành công'
        ], 200);
    }
}