<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\AcademicLevel;
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
            ->leftjoin('province', 'district.province_id', '=', 'province.id')
            ->leftjoin('type_job_post', 'type_job_post.id', '=', 'job_post.type_job_post_id')
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
                'district.name as district',
                'province.province',
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
                DB::raw('count(job_post_id) as  quantity_apply'),
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
            ->leftjoin('district', 'district.id', '=', 'job_post.area_id')
            ->leftjoin('province', 'district.province_id', '=', 'province.id',)
            ->leftjoin('type_job_post', 'type_job_post.id', '=', 'job_post.type_job_post_id',)
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
                'province.province',
                'district.name',
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
                'district.province_id',
                'district.id as district_id',
                'type_job_post.name as type_job_post_name',
                'type_job_post.id as type_job_post_id',
            )
            ->first();
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
            'major_id' => 'required|',
            'start_date' => 'required|after:yesterday',
            'end_date' => 'required|after:start_date',
        ]);
        if ($valdator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $valdator->messages(),
            ], 422);
        }
        $interval = ((strtotime($request['end_date']) - strtotime($request['start_date'])) / 86400) + 1;
        if ($interval < 10) {
            return response()->json([
                'status' => 422,
                'errors' => "Tối thiểu 10 ngày",
            ], 422);
        }
        switch ($request->type_job_post_id) {
            case '0':
                $job_post = JobPost::create($request->all());
                break;
            default:
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
                $job_post = JobPost::create(
                    [
                        "working_form_id"=>$request->working_form_id,
                        "title"=>$request->title,
                        "desc"=>$request->desc,
                        "job_position_id"=>$request->job_position_id  ,                   
                        "area_id"=>$request->area_id,
                        "academic_level_id"=>$request->academic_level_id,
                        "exp_id"=>$request->exp_id,
                        "gender"=>$request->gender,
                        "major_id"=>$request->major_id,
                        "type_job_post_id"=>$request->type_job_post_id,
                        "quantity"=>$request->quantity,
                        "min_salary"=>$request->min_salary,
                        "max_salary"=>$request->max_salary,
                        "interest"=>$request->interest,
                        "requirement"=>$request->requirement,
                        "start_date"=>$request->start_date,
                        "end_date"=>$request->end_date,
                       "company_id"=>$this->company_id(),
                        
                    ]
                );
                Company::find($this->company_id())->update(['coin' => $coinCompanyAffter]);
                updateProcess($this->company_id(), "Thanh toán bài đăng loại {$jobPostType->name} với tiêu đề là {$request->title} trong {$interval} ngày", $coinForJob_post, 1, 0);
                break;
        }
        if ($job_post) {

            return response()->json([
                'status' => 201,
                'message' => 'Tạo thành công',
                'job_post_id' => $job_post
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
        // $check_day_update = ((strtotime($request->end_date) - strtotime($job_post->end_date)) / 86400);
        // $dayPostBeforeEdit = ((strtotime($job_post->end_date) - strtotime($job_post->start_date)) / 86400) + 1;
        // $jobPostTypeBefore = JobPostType::find($job_post->type_job_post_id);
        // $jobPostTypeAfter = JobPostType::find($request['type_job_post_id']);
        // $jobPostDifferencePrice = $jobPostTypeAfter->salary - $jobPostTypeBefore->salary;
        // return ($check_day_update);
        // if ($job_post) {
        //     if ($job_post->type_job_post_id != 0) {
        //         //Trường hợp thêm ngày đăng
        //         if ($job_post->end_date < $request->end_date) {
        //             //trường hợp vẫn giữ gói đăng cũ
        //             if ($request->type_job_post_id == $job_post->type_job_post_id) {
        //                 $coinCompanyAffter = $company_coin->coin - $jobPostTypeBefore->salary * $check_day_update;
        //                 if ($coinCompanyAffter < 0) {
        //                     return response()->json([
        //                         'status' => 422,
        //                         'errors' => 'Bạn không đủ tiền'
        //                     ], 422);
        //                 }
        //             }
        //             //Trường hợp chọn gói đăng mới với mệnh giá cao hơn
        //             else {
        //                 $coinCompanyAffter = $company_coin->coin - $jobPostDifferencePrice * $dayPostBeforeEdit - $check_day_update * $jobPostTypeAfter->salary;
        //                 if ($coinCompanyAffter < 0) {
        //                     return response()->json([
        //                         'status' => 422,
        //                         'errors' => 'Bạn không đủ tiền'
        //                     ], 422);
        //                 }
        //             }
        //             Company::find($this->company_id())->update(['coin' => $coinCompanyAffter]);
        //         } else {
        //             if ($request->type_job_post_id == $job_post->type_job_post_id) {
        //                 $coinCompanyAffter = $company_coin->coin - $jobPostTypeBefore->salary * $check_day_update;
        //                 if ($coinCompanyAffter < 0) {
        //                     return response()->json([
        //                         'status' => 422,
        //                         'errors' => 'Bạn không đủ tiền'
        //                     ], 422);
        //                 }
        //             }
        //         }
        //     }
        if ($job_post->status == 1) {
            return response()->json([
                'status' => 'fail',
                'error' => 'Bài đăng đã được hiển thị không thể sửa '
            ], 200);
        }
        if ($job_post->status != 1) {
            $job_post->update($request->all());
            $job_post->update(['status' => 0]);
            // $company_info = Auth::user();
            // $manage_web = ManagementWeb::find(1);
            // Mail::send('emails.job_post_update', compact('job_post', 'manage_web', 'company_info',), function ($email) use ($manage_web, $company_info) {
            //     $email->subject($manage_web->name_web . ' - Bài đăng tuyển của bạn đã được cập nhật thành công');
            //     $email->to($company_info->email);
            // });
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
                với tiêu đề là {$request->title} trong {$interval} ngày", ($jobPostType->salary) * $interval, 1, 0);
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