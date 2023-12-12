<?php

namespace App\Http\Controllers\Candidate;

use App\Http\Controllers\Controller;
use App\Models\Experience;
use App\Models\JobPosition;
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
        if (!empty($profile)) {
            $coin = $candidate->coin;
            $path_cv = $profile->path_cv;
            $job_apply = JobPostApply::where('curriculum_vitae_id', $profile->id)->get();
            $today = strtotime(Carbon::now());
            $date = date('Y/m/d', time());
            $job_fast = JobPostApply::where('curriculum_vitae_id', $profile->id)
                ->whereDate('created_at', $date)
                ->where('type_apply', 1)
                ->first();
            $major = $candidate->major;
            $skill_profile = SkillProfile::where('profile_id', $profile->id)->get();
            $job = DB::table('job_post')
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
                )
                ->leftJoin('companies', 'job_post.company_id', '=', 'companies.id')
                ->join('district', 'district.id', '=', 'job_post.area_id')
                ->leftJoin('province', 'district.province_id', '=', 'province.id')
                ->leftJoin('working_form', 'job_post.working_form_id', '=', 'working_form.id')
                ->leftJoin('job_position', 'job_post.job_position_id', '=', 'job_position.id')
                ->leftJoin('experiences', 'job_post.exp_id', '=', 'experiences.id')
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
            $count = count($job);
            return $job;
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
            } else if (count($job) == 0) {
                return response()->json([
                    'status' => false,
                    'message' => 'Không tìm thấy công việc phù hợp cho bạn',
                ], 404);
            } else {
            }
        } else {
            return response()->json([
                'status' => true,
                'message' => 'Vui lòng tạo hoặc tải CV của bạn lên website để sử dụng chức năng này!!!',
            ], 400);
        }
    }
}
