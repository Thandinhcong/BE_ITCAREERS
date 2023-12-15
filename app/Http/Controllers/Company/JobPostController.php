<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\AcademicLevel;
use App\Models\AreaJob;
use App\Models\Company;
use App\Models\Experience;
use App\Models\JobPosition;
use App\Models\JobPost;
use App\Models\JobPostApply;
use App\Models\Level;
use App\Models\Major;
use App\Models\District;
use App\Models\JobPostType;
use App\Models\ManagementWeb;
use App\Models\Province;
use App\Models\WorkingForm;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class JobPostController extends Controller
{

    public function company_id()
    {
        return Auth::user()->id;
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
        $d['district_id'] = District::all();
        $d['province_id'] = Province::all();
        $d['type_job_post'] = JobPostType::all();
        return response()->json([
            'status' => 'success',
            'data' => $d,
        ], 200);
    }
    public function index()
    {
        $job_post = DB::table('job_post')->where('job_post.company_id',  $this->company_id())
            ->leftjoin('job_post_apply', 'job_post.id', '=', 'job_post_apply.job_post_id')
            ->leftjoin('job_position', 'job_position.id', '=', 'job_post.job_position_id')
            ->leftjoin('experiences', 'experiences.id', '=', 'job_post.exp_id')
            ->leftjoin('companies', 'companies.id', '=', 'job_post.company_id')
            ->leftjoin('working_form', 'working_form.id', '=', 'job_post.working_form_id')
            ->leftjoin('academic_level', 'academic_level.id', '=', 'job_post.academic_level_id')
            ->leftjoin('major', 'major.id', '=', 'job_post.major_id')
            ->leftjoin('district', 'district.id', '=', 'job_post.area_id')
            ->leftjoin('type_job_post', 'type_job_post.id', '=', 'job_post.type_job_post_id')
            ->leftjoin('area_job', 'area_job.job_post_id', '=', 'job_post.id',)
            ->leftjoin('province', 'area_job.province_id', '=', 'province.id')

            ->groupBy('job_post.id')
            ->select(
                'job_post.id',
                'job_post.title',
                'job_post.min_salary',
                'job_post.max_salary',
                'job_post.view',
                'job_position.job_position',
                'experiences.experience',
                'companies.name as company_name',
                'companies.description',
                'companies.address',
                'companies.logo',
                'working_form.working_form',
                'academic_level.academic_level',
                'major.major',
                // 'district.name as district',
                // 'province.province',
                'type_job_post.name',
                'job_post.start_date',
                'job_post.end_date',
                'job_post.quantity',
                'job_post.requirement as require',
                'job_post.interest',
                'job_post.desc',
                'job_post.status',
                'type_job_post.name as type_job_post_name',
                'type_job_post.id as type_job_post_id',
                DB::raw('count(job_post_apply.job_post_id) as  quantity_apply'),
                DB::raw('GROUP_CONCAT(province.province SEPARATOR " -") as province'),
            )->get();
        if ($job_post->count() === 0) {
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
    public function show(string $id)
    {
        $job_post = DB::table('job_post')->where('job_post.id', $id)
            ->leftjoin('job_position', 'job_position.id', '=', 'job_post.job_position_id')
            ->leftjoin('experiences', 'experiences.id', '=', 'job_post.exp_id')
            ->leftjoin('companies', 'companies.id', '=', 'job_post.company_id')
            ->leftjoin('working_form', 'working_form.id', '=', 'job_post.working_form_id')
            ->leftjoin('academic_level', 'academic_level.id', '=', 'job_post.academic_level_id')
            ->leftjoin('major', 'major.id', '=', 'job_post.major_id')
            // ->leftjoin('district', 'district.id', '=', 'job_post.area_id')
            // ->leftjoin('province', 'district.province_id', '=', 'province.id',)
            ->leftjoin('type_job_post', 'type_job_post.id', '=', 'job_post.type_job_post_id',)
            // ->leftjoin('area_job', 'area_job.job_post_id', '=', 'job_post.id',)
            ->select(
                'job_post.id',
                'job_post.title',
                'job_post.min_salary',
                'job_post.max_salary',
                'job_position.job_position',
                'experiences.experience',
                'companies.name as company_name',
                'companies.description',
                'companies.address',
                'companies.logo',
                'working_form.working_form',
                'academic_level.academic_level',
                'major.major',
                // 'province.province',
                // 'district.name',
                'job_post.start_date',
                'job_post.end_date',
                'job_post.quantity',
                'job_post.requirement',
                'job_post.interest',
                'job_post.desc',
                'job_post.status',
                'job_post.job_position_id',
                'job_post.exp_id',
                'job_post.working_form_id',
                'job_post.academic_level_id',
                'job_post.major_id',
                'job_post.gender',
                // 'district.province_id',
                // 'district.id as district_id',
                'type_job_post.name as type_job_post_name',
                'type_job_post.id as type_job_post_id',
            )
            ->first();
        $area_job = AreaJob::where('job_post_id', $id)->get();
        if ($job_post) {
            return response()->json([
                'status' => 200,
                'level' => $job_post,
                "area_job" => $area_job
            ], 200);
        } else {
            return response()->json([
                'status' => 'fail',
                'level' => 'job post Not Found',
                'job_post' => $job_post,
                "area_job" => $area_job

            ], 404);
        }
    }
    public function job_post_expires()
    {
        $job_post = DB::table('job_post')->where('company_id',  $this->company_id())->where('end_date', '<=', now()->format('Y-m-d'))
            ->join('job_position', 'job_position.id', '=', 'job_post.job_position_id')
            ->join('experiences', 'experiences.id', '=', 'job_post.exp_id')
            ->join('companies', 'companies.id', '=', 'job_post.company_id')
            ->join('working_form', 'working_form.id', '=', 'job_post.working_form_id')
            ->join('academic_level', 'academic_level.id', '=', 'job_post.academic_level_id')
            ->join('major', 'major.id', '=', 'job_post.major_id')
            ->join('district', 'district.id', '=', 'job_post.area_id')
            ->join('province', 'district.province_id', '=', 'province.id',)
            ->select(
                'job_post.id',
                'job_post.title',
                'job_post.min_salary',
                'job_post.max_salary',
                'job_position.job_position',
                'experiences.experience',
                'companies.name as company_name',
                'companies.description',
                'companies.address',
                'companies.logo',
                'working_form.working_form',
                'academic_level.academic_level',
                'major.major',
                'district.name as district',
                'province.province',
                'job_post.start_date',
                'job_post.end_date',
                'job_post.quantity',
                'job_post.requirement as require',
                'job_post.interest',
                'job_post.desc',
                'job_post.status',
            )->get();
        return response()->json([
            'data' => $job_post,
        ]);
    }
    public function store(Request $request)
    {
        $company_coin = DB::table('companies')
            ->select('coin')
            ->where('id', $this->company_id())
            ->first();
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
            'requirement' => 'required|',
            'interest' => 'required|',
            'gender' => 'required',
            'gender' => 'in:0,1,2',
            'area_id' => 'required|',
            'desc' => 'required|',
            'desc' => 'required|',
            'major_id' => 'required|',
            'start_date' => 'required|after:yesterday',
            'end_date' => 'required|after:start_date',
            'type_job_post_id' => 'required',
            'area_job' => 'required',
        ]);
        $newJobArea=[];
        foreach ($request->area_job as $key => $value) {
            $newJobArea[$key]=$value['province_id'];
        };
        if (count(array_unique($newJobArea)) != count($request->area_job)) {
            return response()->json([
                'status' => 422,
                'errors' =>"Bạn đang nhập trùng thanh phố",
            ], 422);
        }
        if ($valdator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $valdator->messages(),
            ], 422);
        }
        if (count(array_unique($newJobArea)) != count($request->area_job)) {
            return response()->json([
                'status' => 422,
                'errors' =>"Bạn đang nhập trùng thành phố",
            ], 422);
        }
        // if (!$request->area_job) {
        //    return response()->json([
        //     'status'=>422,
        //     'errors'=>'Vui lòng chọn địa điểm',
        //     'data'=>$request->area_job
        //    ],422);
        // }
        $interval = ((strtotime($request['end_date']) - strtotime($request['start_date'])) / 86400) + 1;
        if ($interval < 10) {
            return response()->json([
                'status' => 422,
                'errors' => "Tối thiểu 10 ngày",
            ], 422);
        }
        $jobPostType = JobPostType::find($request['type_job_post_id']);
        $coinForJob_post = ($jobPostType->salary) * $interval;
        $coinCompanyAffter = $company_coin->coin -  $coinForJob_post;
        if ($coinCompanyAffter < 0) {
            return response()->json([
                'status' => 422,
                'errors' => 'Bạn không đủ tiền'
            ], 422);
        }
        //Thanh toán  $coinForJob_post xu cho bài đăng loại $jobPostType->name  $request->title trong $interval ngày
        Company::find($this->company_id())->update(['coin' => $coinCompanyAffter]);
        updateProcess($this->company_id(), "Thanh toán bài đăng loại {$jobPostType->name} với tiêu đề la {$request->title} trong {$interval} ngày", $coinForJob_post, 1, 0);
        $job_post = JobPost::create($request->all());
        foreach ($request->area_job as $key => $value) {
            $area_job = AreaJob::create(
                [
                    'job_post_id' => $job_post->id,
                    'province_id' => $value['province_id'],
                    'detail'=>$value['detail']
                ]
            );
        }

        if ($job_post) {
            return response()->json([
                'status' => 201,
                'message' => 'Tạo thành công',
                'job_post_id' => $job_post,
                'area_job' => $area_job
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
        $validator = Validator::make($request->all(), [
            // 'title' => 'required|',
            // 'job_position_id' => 'required|',
            // 'quantity' => 'required|integer',
            // 'academic_level_id' => 'required|',
            // 'exp_id' => 'required|',
            // 'working_form_id' => 'required|',
            // 'min_salary' => 'required',
            // 'max_salary' => 'required',
            // 'min_salary' => 'lte:max_salary',
            // 'requirement' => 'required|',
            // 'interest' => 'required|',
            // 'gender' => 'required',
            // 'gender' => 'in:0,1,2',
            // //Bắt buộc 1 trong 3 số trên
            // 'area_id' => 'required|',
            // 'desc' => 'required|',
            // 'major_id' => 'required|',
            // // 'start_date' => 'required|',
            // // 'start_date' => 'required|date|',
            // // 'end_date' => 'required|date|after:start_date|after:now',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'fail',
                'errors' => $validator->messages(),
            ], 400);
        }

        $job_post = JobPost::find($id);

        if ($job_post->status == 1) {
            return response()->json([
                'status' => 'fail',
                'error' => 'Bài đăng đã được hiển thị không thể sửa '
            ], 200);
        }
        if ($job_post->status != 1) {
            $job_post->update($request->all());
            $job_post->update(['status' => 0]);
            return response()->json([
                'status' => 'success',
                'message' => 'Update Success'
            ], 200);
        } else {
            return response()->json([
                'status' => 'fail',
            ], 500);
        }
    }
    public function extend_job_post(Request $request, string $id)
    {
        $company_coin = DB::table('companies')
            ->select('coin')
            ->where('id', $this->company_id())->first();
        $job_post_date = JobPost::find($id);
        $validator = Validator::make($request->all(), [
            'start_date' => 'date',
            'type_job_post_id' => 'required',
            'end_date' => 'required|date|after:start_date|after:now',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'fail',
                'errors' => $validator->messages(),
                'data' => $job_post_date
            ], 400);
        }
        if ($request['type_job_post_id']) {
            $interval = (strtotime($request['end_date']) -  strtotime($request['start_date'])) / 86400 + 1;
            $jobPostType = JobPostType::find($request['type_job_post_id']);
            $coinCompanyAffter = $company_coin->coin - ($jobPostType->salary) * $interval;
            if ($coinCompanyAffter < 0) {
                return response()->json([
                    'status' => 422,
                    'errors' => 'Bạn không đủ tiền'
                ], 422);
            } else {
                Company::find($this->company_id())->update(['coin' => $coinCompanyAffter]);
                updateProcess($this->company_id(), "Thanh toán bài đăng loại {$jobPostType->name}
                với tiêu đề la {$request->title} trong {$interval} ngày", ($jobPostType->salary) * $interval, 1, 0);
            }
        }
        if ($job_post_date) {
            $job_post_date->update($request->all());
            $job_post_date->update(['status' => 0]);
            return response()->json([
                'status' => 'success',
                'message' => 'Update Success',
                'data' => $job_post_date
            ], 200);
        } else {
            return response()->json([
                'status' => 'fail',
            ], 500);
        }
    }

    // function stop_job_post(string $id)
    // {
    //     $job_post_date = JobPost::find($id);
    //     if ($job_post_date) {
    //         $job_post_date->update(['status' => 3]);
    //         return response()->json([
    //             'status' => 'success',
    //             'message' => 'Update Success',
    //             'data' => $job_post_date
    //         ], 200);
    //     } else {
    //         return response()->json([
    //             'status' => 'fail',
    //         ], 500);
    //     }
    // }
}
