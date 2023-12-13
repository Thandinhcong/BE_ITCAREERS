<?php

namespace App\Http\Controllers\Candidate;

use App\Http\Controllers\Controller;
use App\Http\Resources\CandidateApplyResource;
use App\Jobs\SendEmailJob;
use App\Models\Candidate;
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
        }
        if ($job_apply->start_date > $now) {
            return response()->json([
                'status' => 'fall',
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
                    'status' => 'error validate',
                    'errors' => $validator->messages()
                ], 400);
            }
            if ($request['path_cv'] && $request['curriculum_vitae_id']) {
                return response()->json([
                    'status' => 'error validate',
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
                $data = [];
                $data['email'] = $candidate_apply->email;;
                $data['subject'] = $manage_web->name_web . ' - Bạn đã ứng tuyển thành công';
                $data['view'] = 'emails.candidate_apply';
                $data['title'] = $job_apply->title;
                $data['name'] = $candidate_apply->name;
                $data['logo'] = $manage_web->logo;
                $data['name_web'] = $manage_web->name_web;
                $data['company_name'] = $company_apply->company_name;

                dispatch(new SendEmailJob(
                    $data,
                    $manage_web->name_web . ' - Bạn đã ứng tuyển thành công',
                    'emails.candidate_apply'
                ));
                dispatch(new SendEmailJob(
                    $data,
                    $manage_web->name_web . ' - Ứng viên ứng tuyển',
                    'emails.notification_company_candidate_apply'
                ));

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
        if (!$cancel_save_profile) {
            return response()->json(['error' => 'SaveJobPost not found'], 404);
        }
        $cancel_save_profile->delete();
        return response()->json([
            'message' => 'Xóa thành công'
        ], 200);
    }
    public function profile_to_top()
    {
        $candidate = Candidate::find($this->candidate_id());
        if ($candidate->status_to_top === 1) {
            return response()->json([
                'status' => 422,
                'errors' => 'Chức năng của bạn vẫn chưa hết hạn'
            ], 422);
        }
        if ($candidate->coin < 10000) {
            return response()->json([
                'status' => 422,
                'errors' => 'Bạn không đủ tiền'
            ], 422);
        }
        $coin = $candidate->coin - 10000;
        $dateToTop = date_format(date_modify(now(), "+10 days"), "Y-m-d");
        if ($candidate) {
            $candidate->update([
                'coin' => $coin,
                'date_to_top' => $dateToTop,
                'status_to_top' => 1
            ]);
            $manage_web = ManagementWeb::find(1);
            $data = [];
            $data['email'] = $candidate->email;;
            $data['subject'] = $manage_web->name_web . ' - Bạn đã đăng kí thành công chức năng đẩy hồ sơ';
            $data['view'] = 'emails.candidate_to_top';
            $data['name'] = $candidate->name;
            $data['logo'] = $manage_web->logo;
            $data['name_web'] = $manage_web->name_web;
            $data['date_to_top'] = date("d-m-Y", strtotime($candidate->date_to_top));
            updateProcess($candidate->id, "Chức năng đẩy hồ sơ", 10000, 1, 1);
            dispatch(new SendEmailJob(
                $data,
                $manage_web->name_web . ' - Bạn đã đăng kí thành công chức năng đẩy hồ sơ',
                'emails.candidate_to_top'
            ));
            return response()->json([
                'status' => 201,
                'message' => 'Tạo thành công',
                'job_post_id' => $candidate
            ], 200);
        } else {
            return response()->json([
                'status' => 500,
                'message' => 'Lỗi'
            ], 500);
        }
    }
}
