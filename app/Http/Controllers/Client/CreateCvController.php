<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\AcademicLevel;
use App\Models\District;
use App\Models\Edu;
use App\Models\Exp;
use App\Models\JobPosition;
use App\Models\Major;
use App\Models\Profile;
use App\Models\Project;
use App\Models\Province;
use App\Models\Skill;
use App\Models\SkillProfile;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CreateCvController extends Controller
{
    private $data;
    public function __construct()
    {
        $this->data = [];
    }
    /**
     * Display a listing of the resource.
     */
    public function getData(Request $request)
    {
        $this->data['major'] = Major::all();
        $this->data['academic'] = AcademicLevel::all();
        $this->data['job_position'] = JobPosition::all();
        return response()->json([
            'status' => true,
            'data' => $this->data,
        ]);
    }
    public function createCV(Request $request)
    {
        // if (Auth::guard('candidate')->check()) {
        if (Auth::check()) {
            $candidate = Auth::user();
            $candidate_id = $candidate->id;
            $path_cv = $request->path_cv;
            $cv = new Profile();
            $check_count = Profile::where('candidate_id', $candidate_id)->count();
            if ($check_count >= 3) {
                return response()->json([
                    'status' => false,
                    'message' => 'Bạn đã tạo tối đa 3 CV !!!'
                ], 400);
            } else {
                $cv->candidate_id = $candidate_id;
                $cv->name = $candidate->name;
                $cv->email = $candidate->email;
                $cv->phone = $candidate->phone;
                $cv->image = $candidate->image;
                $cv->address = $candidate->address;
                $cv->birth = $candidate->birth;
                $cv->path_cv = $path_cv;
                $cv->save();
                $profile_id = $cv->id;
                return response()->json([
                    'profile_id' => $profile_id,
                    'message' => 'Tạo thành công'
                ], 201);
            }
        }
    }
    public function index(Request $request)
    {
        $candidate = Auth::user();
        $candidate_id = $candidate->id;
        $profile_id = $request->profile_id;
        $major = DB::table('profile')
            ->where('profile.candidate_id', '=', $candidate_id)
            ->where('profile.id', '=', $profile_id)
            ->join('major', 'major.id', '=', 'profile.major_id')
            ->select('major.major')
            ->first();
        $job_position = DB::table('profile')
            ->where('profile.candidate_id', '=', $candidate_id)
            ->where('profile.id', '=', $profile_id)
            ->join('job_position', 'job_position.id', '=', 'profile.job_position_id')
            ->select('job_position.job_position')
            ->first();
        $cv_get = Profile::where('id', $profile_id)->first();
        $cv = Profile::where('id', $profile_id)
            ->select(
                'id',
                'title',
                'name',
                'email',
                'phone',
                'birth',
                'major_id as major',
                'address',
                'job_position_id as job_position',
                'careers_goal',
                'candidate_id',
                'total_exp',
                'is_active',
                'image',
                'path_cv',
            )
            ->first();
        if (is_string($cv->birth)) {
            $cv_get->birth = Carbon::parse($cv->birth);
        }
        $cv->birth = $cv_get->birth->format('m-d-Y');
        $cv->major = $major ? $major->major : null;
        $cv->job_position = $job_position ? $job_position->job_position : null;

        $this->data['cv'] = $cv;
        if (!empty($cv)) {
            $this->data['skill_cv'] = DB::table('skill_profile')
                ->where('profile_id', '=', $profile_id)
                ->whereNull('skill_profile.deleted_at')
                ->select(
                    'id',
                    'name_skill',
                    'profile_id'
                )
                ->get();

            $this->data['exps'] = DB::table('exp')
                ->where('profile_id', '=', $profile_id)
                ->whereNull('deleted_at')
                ->select(
                    'id',
                    'company_name',
                    'position',
                    'start_date',
                    'end_date',
                    'profile_id',
                    'created_at',
                    'updated_at',
                    'deleted_at'
                )
                ->get();
            foreach ($this->data['exps'] as $exp) {
                $exp->start_date = \Carbon\Carbon::parse($exp->start_date)->format('m-d-Y');
                $exp->end_date = \Carbon\Carbon::parse($exp->end_date)->format('m-d-Y');
            }

            $this->data['projects'] = DB::table('project')->where('profile_id', '=', $profile_id)
                ->select(
                    'id',
                    'project_name',
                    'position',
                    'start_date',
                    'end_date',
                    'desc',
                    'link_project',
                    'profile_id',
                )
                ->whereNull('deleted_at')
                ->get();
            foreach ($this->data['projects'] as $exp) {
                $exp->start_date = \Carbon\Carbon::parse($exp->start_date)->format('m-d-Y');
                $exp->end_date = \Carbon\Carbon::parse($exp->end_date)->format('m-d-Y');
            }
            $this->data['educations'] = DB::table('edu')
                ->where('profile_id', '=', $profile_id)
                ->join('major', 'major.id', '=', 'edu.major_id')
                ->join('academic_level', 'academic_level.id', '=', 'edu.type_degree')
                ->whereNull('edu.deleted_at')
                ->select(
                    'edu.id',
                    'edu.name',
                    'gpa',
                    'academic_level.academic_level as type_degree',
                    'start_date',
                    'end_date',
                    'major.major',
                    'profile_id',
                )
                ->get();
            foreach ($this->data['educations'] as $exp) {
                $exp->start_date = \Carbon\Carbon::parse($exp->start_date)->format('m-d-Y');
                $exp->end_date = \Carbon\Carbon::parse($exp->end_date)->format('m-d-Y');
            }
        }
        return response()->json([
            'status' => true,
            'profile' => $this->data,
        ]);
    }

    public function updateInfo(Request $request)
    {
        $validator_info = Validator::make($request->all(), [
            'title' => 'required',
            'name' => 'required',
            'email' => 'required|email',
            'phone' => 'required',
            'major_id' => 'required',
            'birth' => 'required|date_format:m-d-Y',
            'profile_id' => 'required',
            'job_position_id' => 'required',
            'careers_goal' => 'required',
            'image' => 'required',
        ]);
        if ($validator_info->fails()) {
            return response()->json([
                'error' => $validator_info->errors()
            ], 404);
        }
        $profile_id = $request->profile_id;
        $cv = Profile::find($profile_id);
        $cv->title = $request->title;
        $cv->name = $request->name;
        $cv->email = $request->email;
        $cv->phone = $request->phone;
        $cv->major_id = $request->major_id;
        $cv->birth = $request->birth;
        $cv->address = $request->address;
        $cv->image = $request->image;
        $cv->job_position_id = $request->job_position_id;
        $cv->careers_goal = $request->careers_goal;

        $res = $cv->update();
        if ($res == null) {
            return response()->json([
                'status' => false,
                'message' => 'Cập nhật thất bại!'
            ]);
        }
        if ($res == 1) {
            return response()->json([
                'is_check' => true,
                'success' => 'Cập nhật thành công!',
                'data' => $cv,
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Lỗi Cập nhật!'
            ]);
        }
    }

    public function saveExp(Request $request)
    {
        $profile_id = $request->profile_id;
        $check_exp = Exp::where('profile_id', $profile_id)->count();

        if ($check_exp >= 5) {
            return response()->json([
                'status' => false,
                'message' => "Bạn chỉ có thể tối đa 5 kinh nghiệm"
            ], 400);
        }

        $validator_exp = Validator::make($request->all(), [
            'company_name' => 'required|string',
            'position' => 'required',
            'start_date' => 'required|date_format:m-d-Y',
            'profile_id' => 'required',
        ]);

        if ($validator_exp->fails()) {
            return response()->json([
                'error' => $validator_exp->errors()
            ], 422);
        }

        $exp = new Exp();
        $exp->fill([
            'company_name' => $request->company_name,
            'position' => $request->position,
            'start_date' => Carbon::parse($request->start_date),
            'end_date' => Carbon::parse(empty($request->end_date) ? Carbon::now() : $request->end_date),
            'profile_id' => $request->profile_id,
        ]);
        if (!$exp->save()) {
            return response()->json([
                'status' => false,
                'message' => 'Tạo mới thất bại!'
            ]);
        }
        $exp_id = $exp->profile_id;
        $expRecords = Exp::where('profile_id', $exp_id)
            ->orderBy('start_date', 'asc')
            ->orderBy('end_date', 'desc')
            ->whereNull('deleted_at')
            ->get();

        if ($expRecords->count() > 0) {

            $start_date = Carbon::parse($expRecords->first()->start_date);
            $end_date = Carbon::parse($expRecords->last()->end_date);

            $total_months = $start_date->diffInMonths($end_date);
            $total_years = floor($total_months / 12);
            Profile::where('id', $exp_id)->update([
                'total_exp' => $total_years,
            ]);
        }


        return response()->json([
            'status' => true,
            'success' => 'Tạo mới thành công!',
            'data' => $exp,
        ]);
    }
    public function updateExp(Request $request)
    {
        $validator_exp = Validator::make($request->all(), [
            'company_name' => 'required|string',
            'position' => 'required',
            'start_date' => 'required|date_format:m-d-Y',
            'profile_id' => 'required',
        ]);
        if ($validator_exp->fails()) {
            return response()->json([
                'error' => $validator_exp->errors()
            ], 422);
        }

        $exp_id = $request->id;
        $exp = Exp::find($exp_id);
        $exp->fill([
            'company_name' => $request->company_name,
            'position' => $request->position,
            'start_date' => Carbon::parse($request->start_date),
            'end_date' => Carbon::parse(empty($request->end_date) ? Carbon::now() : $request->end_date),
            'profile_id' => $request->profile_id,
        ]);

        if (!$exp->update()) {
            return response()->json([
                'status' => false,
                'message' => 'Cập nhật thất bại!'
            ], 400);
        }
        $exp_id = $exp->profile_id;
        $expRecords = Exp::where('profile_id', $exp_id)
            ->orderBy('start_date', 'asc')
            ->orderBy('end_date', 'desc')
            ->whereNull('deleted_at')
            ->get();

        if ($expRecords->count() > 0) {

            $start_date = Carbon::parse($expRecords->first()->start_date);
            $end_date = Carbon::parse($expRecords->last()->end_date);

            $total_months = $start_date->diffInMonths($end_date);
            $total_years = floor($total_months / 12);
            Profile::where('id', $exp_id)->update([
                'total_exp' => $total_years,
            ]);
        }

        return response()->json([
            'is_check' => true,
            'success' => 'Cập nhật thành công!',
            'data' => $exp,
        ], 201);
    }
    public function deleteExp(Request $request)
    {
        $exp_id = $request->id;
        $exp = Exp::find($exp_id);
        $profile_id_exp = $exp->profile_id;
        $expRecords = Exp::where('profile_id', $profile_id_exp)
            ->orderBy('start_date', 'asc')
            ->orderBy('end_date', 'desc')
            ->whereNull('deleted_at')
            ->get();

        if ($expRecords->count() > 0) {

            $start_date = Carbon::parse($expRecords->first()->start_date);
            $end_date = Carbon::parse($expRecords->last()->end_date);

            $total_months = $start_date->diffInMonths($end_date);
            $total_years = floor($total_months / 12);
            Profile::where('id', $exp_id)->update([
                'total_exp' => $total_years,
            ]);
        }
        if (isset($exp_id)) {
            $exp = Exp::find($exp_id);
            $exp->delete();
            return response()->json([
                'is_check' => true,
                'success' => 'Xóa thành công!',
            ], 201);
        }
        return response()->json([
            'status' => false,
            'message' => 'Xóa thất bại!'
        ], 400);
    }

    public function saveEdu(Request $request)
    {
        $validator_edu = Validator::make($request->all(), [
            'name' => 'required|string',
            'gpa' => 'required',
            'type_degree' => 'required',
            'start_date' => 'required|date_format:m-d-Y',
            'major_id' => '',
            'profile_id' => 'required',
        ]);

        if ($validator_edu->fails()) {
            return response()->json([
                'error' => $validator_edu->errors()
            ], 422);
        }

        $edu = new Edu();
        $edu->fill([
            'name' => $request->name,
            'gpa' => $request->gpa,
            'type_degree' =>  $request->type_degree,
            'start_date' => Carbon::parse($request->start_date),
            'end_date' => Carbon::parse(empty($request->end_date) ? Carbon::now()->toDateString() : $request->end_date),
            'major_id' =>  $request->major_id,
            'profile_id' => $request->profile_id,
        ]);

        if (!$edu->save()) {
            return response()->json([
                'status' => false,
                'message' => 'Tạo mới thất bại!'
            ]);
        }

        return response()->json([
            'is_check' => true,
            'success' => 'Tạo mới thành công!',
            'data' => $edu,
        ]);
    }
    public function updateEdu(Request $request)
    {
        $validator_edu = Validator::make($request->all(), [
            'name' => 'required|string',
            'gpa' => 'required',
            'type_degree' => 'required',
            'start_date' => 'required|date_format:m-d-Y',
            'major_id' => 'required',
        ]);

        if ($validator_edu->fails()) {
            return response()->json([
                'error' => $validator_edu->errors()
            ], 422);
        }
        $edu_id = $request->id;
        $edu = Edu::find($edu_id);
        $edu->fill([
            'name' => $request->name,
            'gpa' => $request->gpa,
            'type_degree' =>  $request->type_degree,
            'start_date' => Carbon::parse($request->start_date),
            'end_date' => Carbon::parse(empty($request->end_date) ? Carbon::now()->toDateString() : $request->end_date),
        ]);

        if (!$edu->update()) {
            return response()->json([
                'status' => false,
                'message' => 'Cập nhật thất bại!'
            ], 400);
        }

        return response()->json([
            'is_check' => true,
            'success' => 'Cập nhật thành công!',
            'data' => $edu,
        ], 201);
    }
    public function deleteEdu(Request $request)
    {
        $edu_id = $request->id;
        if (isset($edu_id)) {
            $edu = Edu::find($edu_id);
            $edu->delete();
            return response()->json([
                'is_check' => true,
                'success' => 'Xóa thành công!',
            ], 201);
        }
        return response()->json([
            'status' => false,
            'message' => 'Xóa thất bại!'
        ], 400);
    }
    public function saveProject(Request $request)
    {
        $validator_project = Validator::make($request->all(), [
            'project_name' => 'required|string',
            'position' => 'required',
            'start_date' => 'required|date_format:m-d-Y',
            'desc' => 'required',
            'link_project' => 'required',
            'profile_id' => 'required',
        ]);

        if ($validator_project->fails()) {
            return response()->json([
                'error' => $validator_project->errors()
            ], 422);
        }

        $project = new Project();
        $project->fill([
            'project_name' => $request->project_name,
            'position' => $request->position,
            'start_date' => Carbon::parse($request->start_date),
            'end_date' => Carbon::parse(empty($request->end_date) ? Carbon::now()->toDateString() : $request->end_date),
            'desc' => $request->desc,
            'link_project' => $request->link_project,
            'profile_id' => $request->profile_id,
        ]);

        if (!$project->save()) {
            return response()->json([
                'status' => false,
                'message' => 'Tạo mới thất bại!'
            ]);
        }

        return response()->json([
            'is_check' => true,
            'success' => 'Tạo mới thành công!',
            'data' => $project,
        ]);
    }
    public function updateProject(Request $request)
    {
        $validator_project = Validator::make($request->all(), [
            'project_name' => 'required|string',
            'position' => 'required',
            'start_date' => 'required|date_format:m-d-Y',
            'desc' => 'required',
            'link_project' => 'required',
        ]);

        if ($validator_project->fails()) {
            return response()->json([
                'error' => $validator_project->errors()
            ], 422);
        }
        $project_id = $request->id;
        $project = Project::find($project_id);
        $project->fill([
            'project_name' => $request->project_name,
            'position' => $request->position,
            'start_date' => Carbon::parse($request->start_date),
            'end_date' => Carbon::parse(empty($request->end_date) ? Carbon::now()->toDateString() : $request->end_date),
            'desc' => $request->desc,
            'link_project' => $request->link_project,
        ]);
        if (!$project->update()) {
            return response()->json([
                'status' => false,
                'message' => 'Cập nhật thất bại!'
            ], 400);
        }

        return response()->json([
            'is_check' => true,
            'success' => 'Cập nhật thành công!',
            'data' => $project,
        ], 201);
    }
    public function deleteProject(Request $request)
    {
        $project_id = $request->id;
        if (isset($project_id)) {
            $project = Project::find($project_id);
            $project->delete();
            return response()->json([
                'is_check' => true,
                'success' => 'Xóa thành công!',
            ], 201);
        }
        return response()->json([
            'status' => false,
            'message' => 'Xóa thất bại!'
        ], 400);
    }
    public function saveSkill(Request $request)
    {
        $validator_skill = Validator::make($request->all(), [
            'name_skill' => 'required',
            'profile_id' => 'required',
        ]);

        if ($validator_skill->fails()) {
            return response()->json([
                'error' => $validator_skill->errors()
            ], 422);
        }

        $skill_profile = new SkillProfile();
        $skill_profile->fill([
            'name_skill' => $request->name_skill,
            'profile_id' => $request->profile_id,
        ]);

        if (!$skill_profile->save()) {
            return response()->json([
                'status' => false,
                'message' => 'Tạo mới thất bại!'
            ]);
        }

        return response()->json([
            'is_check' => true,
            'success' => 'Tạo mới thành công!',
            'data' => $skill_profile,
        ]);
    }
    public function updateSkill(Request $request)
    {
        $validator_skill = Validator::make($request->all(), [
            'name_skill' => 'required',
        ]);

        if ($validator_skill->fails()) {
            return response()->json([
                'error' => $validator_skill->errors()
            ], 422);
        }

        $skill_profile_id = $request->id;
        $skill_profile = SkillProfile::find($skill_profile_id);
        $skill_profile->fill([
            'name_skill' => $request->name_skill,
        ]);

        if (!$skill_profile->update()) {
            return response()->json([
                'status' => false,
                'message' => 'Cập nhật thất bại!'
            ], 400);
        }

        return response()->json([
            'is_check' => true,
            'success' => 'Cập nhật thành công!',
            'data' => $skill_profile,
        ], 201);
    }
    public function deleteSkill(Request $request)
    {
        $skill_profile_id = $request->id;
        if (isset($skill_profile_id)) {
            $project = SkillProfile::find($skill_profile_id);
            $project->delete();
            return response()->json([
                'is_check' => true,
                'success' => 'Xóa thành công!',
            ], 201);
        }
        return response()->json([
            'status' => false,
            'message' => 'Xóa thất bại!'
        ], 400);
    }
    public function saveCV(Request $request)
    {
        $profile_id = $request->profile_id;
        $cv = Profile::where('id', $profile_id)->first();
        $path_cv = $request->path_cv;
        if (isset($cv) && isset($path_cv)) {
            $path_cv_old = $cv->path_cv;
            if ($path_cv === $path_cv_old) {
                return response()->json([
                    'status' => false,
                    'message' => 'CV chưa có thay đổi',
                ]);
            } else {
                $cv->update([
                    'path_cv' => $path_cv,
                ]);
                return response()->json([
                    'status' => true,
                    'message' => 'Lưu thành công CV',
                ]);
            }
        }
        return response()->json([
            'status' => false,
            'message' => 'Lưu CV thất bại',
        ]);
    }
}
