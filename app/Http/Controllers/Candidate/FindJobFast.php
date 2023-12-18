<?php

namespace App\Http\Controllers\Candidate;

use App\Http\Controllers\Controller;
use App\Jobs\SendEmailJob;
use App\Models\Company;
use App\Models\Experience;
use App\Models\JobPosition;
use App\Models\JobPost;
use App\Models\JobPostApply;
use App\Models\ManagementWeb;
use App\Models\Profile;
use App\Models\Province;
use App\Models\SkillProfile;
use App\Models\WorkingForm;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class FindJobFast extends Controller
{
    public $v;
    public function __construct()
    {
        $this->v = [];
    }
    public function getDataJobFast()
    {
        $this->v['experiences'] = Experience::all();
        $this->v['job_position'] = JobPosition::all();
        $this->v['working_form'] = WorkingForm::all();
        $this->v['province'] = Province::all();

        return response()->json([
            'status' => true,
            'data' => $this->v
        ], 200);
    }
    public function jobFast(Request $request)
    {
        $candidate = Auth::user();
        $profile = Profile::where('id', $candidate->main_cv)->first();
        if (!empty($profile) && !empty($profile->path_cv)) {
            $coin = $candidate->coin;
            $path_cv = $profile->path_cv;
            $job_apply = JobPostApply::where('curriculum_vitae_id', $profile->id)->get();
            $today = strtotime(Carbon::now());
            // $date = date('Y/m/d H:i:s', time());
            $date = date('Y/m/d', time());
            $job_fast = JobPostApply::where('candidate_id', $candidate->id)
                ->where('type_apply', 1)
                ->whereDate('created_at', $date)
                ->first();
            $major = $candidate->major;
            $skill_profile = SkillProfile::where('profile_id', $profile->id)->get();
            $jobs = DB::table('job_post')
                ->select(
                    'job_post.id',
                    'job_post.title',
                    'district.name as district',
                    'province.province',
                    'job_post.min_salary',
                    'job_post.max_salary',
                    'job_post.created_at',
                    'job_post.start_date',
                    'job_post.desc',
                    'companies.company_name as company_name',
                    'companies.logo',
                    'job_post.company_id'
                )
                ->leftJoin('companies', 'job_post.company_id', '=', 'companies.id')
                ->join('district', 'district.id', '=', 'job_post.area_id')
                ->leftJoin('province', 'district.province_id', '=', 'province.id')
                ->leftJoin('working_form', 'job_post.working_form_id', '=', 'working_form.id')
                ->leftJoin('job_position', 'job_post.job_position_id', '=', 'job_position.id')
                ->leftJoin('experiences', 'job_post.exp_id', '=', 'experiences.id')
                ->leftJoin('job_post_apply', function ($join) use ($candidate) {
                    $join->on('job_post.id', '=', 'job_post_apply.job_post_id')
                        ->where('job_post_apply.candidate_id', '=', $candidate->id);
                })
                ->whereNull('job_post_apply.job_post_id')
                ->where(function ($q) use ($request, $candidate, $skill_profile) {
                    if (!empty($request->search)) {
                        $q->orWhereFullText(['title', 'desc'], $request->search);
                    }
                    if (!empty($request->province)) {
                        $q->where('province.id', '=', $request->province);
                    }
                    if (!empty($request->experiences)) {
                        $q->where('exp_id', '=', $request->experiences);
                    }
                    if (!empty($request->job_position)) {
                        $q->where('job_position_id', '=', $request->job_position);
                    }
                    if (!empty($request->working_form)) {
                        $q->where('working_form_id', '=', $request->working_form);
                    }
                    if (!empty($candidate->major)) {
                        $q->orWhereFullText(['title', 'desc'], $candidate->major);
                    }
                    if (!empty($skill_profile)) {
                        $q->orWhere(function ($q) use ($skill_profile) {
                            foreach ($skill_profile as $item) {
                                if (!empty($item->name_skill)) {
                                    $q->orWhere(function ($q) use ($item) {
                                        $q->whereFullText(['title', 'desc'], $item->name_skill);
                                    });
                                }
                            }
                        });
                    }
                })
                ->where('start_date', '<=', now()->format('Y-m-d'))
                ->where('end_date', '>=', now()->format('Y-m-d'))
                ->where('job_post.status', 1)
                ->get();
            $count = count($jobs);
            if ($count == 0) {
                $price = 0;
            } elseif ($count <= 10) {
                $price = 30000;
            } elseif ($count > 10 && $count <= 30) {
                $price = 50000;
            } elseif ($count > 30) {
                $price = 100000;
            }
            if ($coin - $price < 0) {
                return response()->json([
                    'status' => false,
                    'message' => 'Tài khoản của bạn không đủ số dư, vui lòng nạp thêm tiền !!!',
                ], 400);
            } else if (!empty($job_fast)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Bạn đã sử dụng Tính Năng này rồi vui lòng quay lại vào Ngày mai !!!',
                ], 400);
            } else if (count($jobs) == 0) {
                return response()->json([
                    'status' => false,
                    'message' => 'Không tìm thấy công việc phù hợp cho bạn',
                ], 404);
            } else {
                foreach ($jobs as $item) {
                    $candidate_apply = JobPostApply::create([
                        'job_post_id' => $item->id,
                        'curriculum_vitae_id' => $candidate->main_cv,
                        'name' => $candidate->name,
                        'phone' => $candidate->phone,
                        'email' => $candidate->email,
                        'candidate_id' => $candidate->id,
                        'type_apply' => 1, // ứng tuyển nhanh
                    ]);
                    $company_apply = Company::find($item->company_id);
                    $manage_web = ManagementWeb::find(1);
                    $data = [];
                    $data['email'] = $candidate_apply->email;;
                    $data['subject'] = $manage_web->name_web . ' - Bạn đã ứng tuyển thành công';
                    $data['view'] = 'emails.candidate_apply';
                    $data['title'] = $item->title;
                    $data['name'] = $candidate_apply->name;
                    $data['logo'] = $manage_web->logo;
                    $data['name_web'] = $manage_web->name_web;
                    $data['company_name'] = $company_apply->company_name;
                    dispatch(new SendEmailJob(
                        $data,
                        $manage_web->name_web . ' - Ứng viên ứng tuyển',
                        'emails.notification_company_candidate_apply'
                    ));
                }
                $candidate->update([
                    'coin' => $coin - $price,
                ]);
                updateProcess($candidate->id, 'Thực hiện - ' . $price . ' coin sử dụng vào chức năng tìm việc nhanh', $price, 1, 1);
                return response()->json([
                    'status' => true,
                    'message' => 'Bạn đã ứng tuyển thành công',
                ], 200);
            }
        } else {
            return response()->json([
                'status' => true,
                'message' => 'Vui lòng tạo hoặc tải CV của bạn lên website để sử dụng chức năng này!!!',
            ], 400);
        }
    }
}