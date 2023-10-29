<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Mail\MailNotify;
use App\Models\AcademicLevel;
use App\Models\Edu;
use App\Models\Exp;
use App\Models\Experience;
use App\Models\JobPosition;
use App\Models\JobPost;
use App\Models\JobPostApply;
use App\Models\Level;
use App\Models\Major;
use App\Models\Project;
use App\Models\Area;
use App\Models\District;
use App\Models\Province;
use App\Models\SkillPost;
use App\Models\SkillProfile;
use App\Models\WorkingForm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class JobPostController extends Controller
{
    public function index()
    {
        $company_id = Auth::user()->id;
        // $job_post = DB::table('job_post')->where('company_id', $company_id)
        // $company_id = Auth::guard('company')->user()->id;
        // $company_id = Auth::guard('company')->user()->id;
        $job_post = DB::table('job_post')->where('company_id',  $company_id)
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
                'job_post.require',
                'job_post.interest',
                'job_post.status',
            )->get();
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
        $d['district_id'] = District::all();
        $d['province_id'] = Province::all();

        // $d['area_id'] = Area::all();
        // $d['major_id'] = Major::all();
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
            'gender' => 'required',
            'gender' => 'in:0,1,2',
            //Bắt buộc 1 trong 3 số trên 
            'area_id' => 'required|',
            'major_id' => 'required|',
            'start_date' => 'required|date|after:now',
            'end_date' => 'required|date|after:start_date',
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
            'gender' => 'required',
            'gender' => 'in:0,1,2',
            //Bắt buộc 1 trong 3 số trên 
            'area_id' => 'required|',
            'major_id' => 'required|',
            'start_date' => 'required|',
            'start_date' => 'required|date|',
            'end_date' => 'required|date|after:start_date|after:now',
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
        $job_post = DB::table('job_post')->where('job_post.id', $id)
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
                'province.province',
                'district.name as district',
                'job_post.start_date',
                'job_post.end_date',
                'job_post.quantity',
                'job_post.require',
                'job_post.interest',
                'job_post.status',
                'job_post.job_position_id',
                'job_post.exp_id',
                'job_post.working_form_id',
                'job_post.academic_level_id',
                'job_post.major_id',
                'district.province_id',
                'district.id as district_id',
            )->first();
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
    function list_candidate_apply_job(string $id)
    {
        // Auth::guard('company')->user()->id;
        $list_candidate_apply_job = DB::table('job_post_apply')
            ->join('job_post', 'job_post.id', '=', 'job_post_apply.job_post_id')
            ->join('candidates', 'candidates.id', '=', 'job_post_apply.candidate_id')
            ->join('candidates', 'candidates.id', '=', 'curriculum_vitae.candidate_id')
            ->select(
                'job_post.title as job_post_name',
                'job_post_apply.created_at as time_apply',
                'job_post_apply.qualifying_round_id',
                'job_post_apply.id as candidate_code',
                'job_post_apply.status',
                'job_post_apply.email',
                'job_post_apply.phone',
                'job_post_apply.name',
                'curriculum_vitae.path_cv'
            )
            ->where('job_post_id', $id)->get();
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
            'status' => 'required|in:1,2',
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
            $candidate = Db::table('job_post_apply')->where('job_post_apply.id', $id)
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
                )
                ->first();
            Mail::send('emails.demo', compact('candidate'), function ($email) use ($candidate) {
                $email->subject('UbWork - Lấy Lại Mật Khẩu');
                $email->to('huyetcongtu4869@gmail.com');
            });
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'not found'
            ], 404);
        }
    }
    public function candidate_detail(string $id)
    {
        $data = [];
        $data['profile'] = Db::table('curriculum_vitae')->where('curriculum_vitae.id', $id)
            ->join('job_post_apply', 'curriculum_vitae.id', '=', 'curriculum_vitae.id')
            ->select(
                'job_post_apply.name',
                'job_post_apply.email',
                'job_post_apply.phone',
                'curriculum_vitae.path_cv',
            )
            ->first();
        if ($data) {
            JobPostApply::where('id', $id)->update(['status' => 1]);
            Mail::send('emails.demo', compact('candidate'), function ($email) use ($data) {
                $email->subject('Nhà tuyển dụng đã xem hồ sơ của bạn');
                $email->to('huyetcongtu4869@gmail.com');
            });
            return response()->json([
                'status' => 200,
                'data' => $data
            ], 200);
        } else {
            return response()->json([
                'status' => 'fail',
                'data' => 'job post Not Found',
                'data' => $data
            ], 404);
        }
    }
    public function job_post_expires()
    {
        $company_id = Auth::user()->id;
        $job_post = DB::table('job_post')->where('company_id',  $company_id)->where('end_date', '<=', now()->format('Y-m-d'))
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
                'job_post.require',
                'job_post.interest',
                'job_post.status',
            )->get();
        return response()->json([
            'data' => $job_post,
        ]);
    }
    function extend_job_post(Request $request, string $id)
    {
        $job_post_date = JobPost::find($id);
        $validator = Validator::make($request->all(), [
            'start_date' => 'required|',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date|after:now',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'fail',
                'errors' => $validator->messages(),
                'data' => $job_post_date
            ], 400);
        }
        if ($job_post_date) {
            $job_post_date->update($request->all());
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
    function stop_job_post(string $id) {
        $job_post_date = JobPost::find($id);
        if ($job_post_date) {
            $job_post_date->status=3;
            $job_post_date->update();
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
}
