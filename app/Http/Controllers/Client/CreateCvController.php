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
    private $message_val;
    public function __construct()
    {
        $this->data = [];
        $this->message_val = [
            'title.required' => 'Vui lòng nhập tiêu đề!',
            'name.required' => 'Vui lòng nhập tên!',
            'email.required' => 'Vui lòng nhập email!',
            'email.email' => 'Vui lòng nhập đúng định dạng email!',
            'phone.required' => 'Vui lòng nhập số điện thoại!',
            'address.required' => 'Vui lòng nhập địa chỉ!',
            'image.image' => 'Chọn file ảnh!',
            'image.mimes' => 'Chọn file ảnh có định dạng jpg,png,jpeg!',
            'image.max' => 'Chọn ảnh có kích thước nhỏ hơn 5mb!',
            'major.required' => 'Vui lòng nhập chuyên ngành!',
            'birth.required' => 'Vui lòng nhập ngày sinh!',
            'careers_goal.required' => 'Vui lòng nhập mục tiêu nghề nghiệp!',
            'image' => 'Vui lòng thêm ảnh',
            'company_name.required' => 'Vui lòng nhập tên công ty!',
            'position.required' => 'Vui lòng nhập vị trí làm việc!',
            'start_date.required' => 'Vui lòng nhập ngày bắt đầu!',
            'start_date.date_format' => 'Vui lòng nhập đúng định dạng!',
            'end_date.required' => 'Vui lòng nhập ngày ngày kết thúc!',
            'end_date.date_format' => 'Vui lòng nhập đúng định dạng!',
            'end_date.after' => 'Ngày kết thúc không nhỏ hơn ngày bắt đầu!',
            'end_date.before' => 'Ngày kết thúc không lớn hơn ngày hiện tại!',
            'gpa.required' => 'Vui lòng nhập điểm GPA!',
            'gpa.numeric' => 'Điểm GPA phải là một số!',
            'gpa.between' => 'Điểm GPA phải nằm trong khoảng từ 0 đến 10!',
            'type_degree.required' => 'Vui lòng chọn Trình độ học vấn!',
            'project_name.required' => 'Vui lòng nhập tên dự án!',
            'desc.required' => 'Vui lòng nhập mô tả!',
            'link_project.required' => 'Vui lòng nhập link dự án!',
            'name_skill.required'   => 'Vui lòng nhập tên kĩ năng!',
        ];
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
            $cv = new Profile();
            $check_count = Profile::where('candidate_id', $candidate_id)
                ->where('type', 1)
                ->count();
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
                $cv->type = 1;
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
        $cv_get = Profile::where('id', $profile_id)->first();
        $cv = Profile::where('id', $profile_id)
            ->select(
                'id',
                'title',
                'name',
                'email',
                'phone',
                'birth',
                'address',
                'candidate_id',
                'total_exp',
                'is_active',
                'image',
                'path_cv',
                'careers_goal',
            )
            ->first();
        if (is_string($cv->birth)) {
            $cv_get->birth = Carbon::parse($cv->birth);
        }
        $cv->birth = $cv_get->birth ? $cv_get->birth->format('Y-m-d') : null;
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
                    'desc',
                    'created_at',
                    'updated_at',
                    'deleted_at'
                )
                ->get();
            foreach ($this->data['exps'] as $exp) {
                $exp->start_date = \Carbon\Carbon::parse($exp->start_date)->format('Y-m-d');
                $exp->end_date = \Carbon\Carbon::parse($exp->end_date)->format('Y-m-d');
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
                $exp->start_date = \Carbon\Carbon::parse($exp->start_date)->format('Y-m-d');
                $exp->end_date = \Carbon\Carbon::parse($exp->end_date)->format('Y-m-d');
            }
            $this->data['educations'] = DB::table('edu')
                ->where('profile_id', '=', $profile_id)
                ->join('academic_level', 'academic_level.id', '=', 'edu.type_degree')
                ->whereNull('edu.deleted_at')
                ->select(
                    'edu.id',
                    'edu.name',
                    'gpa',
                    'academic_level.academic_level as type_degree',
                    'start_date',
                    'end_date',
                    'major',
                    'profile_id',
                )
                ->get();
            foreach ($this->data['educations'] as $exp) {
                $exp->start_date = \Carbon\Carbon::parse($exp->start_date)->format('Y-m-d');
                $exp->end_date = \Carbon\Carbon::parse($exp->end_date)->format('Y-m-d');
            }
        }
        return response()->json([
            'status' => true,
            'profile' => $this->data,
        ]);
    }

    public function updateInfo(Request $request)
    {
        $messages =  $this->message_val;
        $validator_info = Validator::make($request->all(), [
            'title' => 'required',
            'name' => 'required',
            'email' => 'required|email',
            'phone' => 'required',
            'birth' => 'required',
            'address' => 'required',
            'careers_goal' => 'required',
            'image' => 'required',
        ], $messages);
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
        $cv->birth = Carbon::parse($request->birth);
        $cv->address = $request->address;
        $cv->image = $request->image;
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
        $messages =  $this->message_val;
        $validator_exp = Validator::make($request->all(), [
            'company_name' => 'required|string',
            'position' => 'required',
            'start_date' => 'required|date_format:Y-m-d',
            'end_date' => 'required|date_format:Y-m-d|after:start_date|before:now',
            'profile_id' => 'required',
            'desc' => ''
        ], $messages);

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
            'desc' => $request->desc,
        ]);
        if (!$exp->save()) {
            return response()->json([
                'status' => false,
                'message' => 'Tạo mới thất bại!'
            ]);
        }
        //update tổng năm kinh nghiệm và coin
        $exp_id = $exp->profile_id;
        $expRecords = Exp::where('profile_id', $exp_id)
            ->whereNull('deleted_at')
            ->select('start_date', 'end_date')
            ->get();
        if ($expRecords->count() > 0) {
            $coinIncrementPerYear = 500;
            $maxCoinWithoutIncrement = 10 * $coinIncrementPerYear;
            $totalExperienceYears = 0;
            $totalCoin = 0;
            foreach ($expRecords as $expRecord) {
                $startDate = Carbon::parse($expRecord->start_date);
                $endDate = Carbon::parse($expRecord->end_date);
                $experienceYears = $endDate->diffInYears($startDate);
                $totalExperienceYears += $experienceYears;
            }
            if ($totalExperienceYears < 10) {
                $totalCoin += $totalExperienceYears * $coinIncrementPerYear;
            } else {
                $totalCoin += $maxCoinWithoutIncrement;
            }
            $profile = Profile::where('id', $profile_id)->first();
            $profile->update([
                'total_exp' => $totalExperienceYears,
                'coin_exp' => $totalCoin,
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
        $messages =  $this->message_val;
        $validator_exp = Validator::make($request->all(), [
            'company_name' => 'required|string',
            'position' => 'required',
            'start_date' => 'required|date_format:Y-m-d',
            'end_date' => 'required|date_format:Y-m-d|after:start_date|before:now',
            'desc' => ''
        ], $messages);
        if ($validator_exp->fails()) {
            return response()->json([
                'error' => $validator_exp->errors()
            ], 422);
        }

        $exp_id = $request->id;
        $exp = Exp::find($exp_id);
        $profile_id_exp = $exp->profile_id;
        $exp->fill([
            'company_name' => $request->company_name,
            'position' => $request->position,
            'start_date' => Carbon::parse($request->start_date),
            'end_date' => Carbon::parse(empty($request->end_date) ? Carbon::now() : $request->end_date),
            'desc' => $request->desc,
        ]);

        if (!$exp->update()) {
            return response()->json([
                'status' => false,
                'message' => 'Cập nhật thất bại!'
            ], 400);
        }
        $exp_id = $exp->profile_id;
        $expRecords = Exp::where('profile_id', $exp_id)
            ->whereNull('deleted_at')
            ->select('start_date', 'end_date')
            ->get();
        if ($expRecords->count() > 0) {
            $coinIncrementPerYear = 500;
            $maxCoinWithoutIncrement = 10 * $coinIncrementPerYear;
            $totalExperienceYears = 0;
            $totalCoin = 0;
            foreach ($expRecords as $expRecord) {
                $startDate = Carbon::parse($expRecord->start_date);
                $endDate = Carbon::parse($expRecord->end_date);
                $experienceYears = $endDate->diffInYears($startDate);
                $totalExperienceYears += $experienceYears;
            }
            if ($totalExperienceYears < 10) {
                $totalCoin += $totalExperienceYears * $coinIncrementPerYear;
            } else {
                $totalCoin += $maxCoinWithoutIncrement;
            }
            $profile = Profile::where('id', $profile_id_exp)->first();
            $profile->update([
                'total_exp' => $totalExperienceYears,
                'coin_exp' => $totalCoin,
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
        if (!$exp) {
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy bản ghi để xóa!'
            ], 404);
        }

        $profile_id_exp = $exp->profile_id;
        $exp->delete();
        $exp_id = $exp->profile_id;
        $expRecords = Exp::where('profile_id', $exp_id)
            ->whereNull('deleted_at')
            ->select('start_date', 'end_date')
            ->get();
        if ($expRecords->count() > 0) {
            $coinIncrementPerYear = 500;
            $maxCoinWithoutIncrement = 10 * $coinIncrementPerYear;
            $totalExperienceYears = 0;
            $totalCoin = 0;
            foreach ($expRecords as $expRecord) {
                $startDate = Carbon::parse($expRecord->start_date);
                $endDate = Carbon::parse($expRecord->end_date);
                $experienceYears = $endDate->diffInYears($startDate);
                $totalExperienceYears += $experienceYears;
            }
            if ($totalExperienceYears < 10) {
                $totalCoin += $totalExperienceYears * $coinIncrementPerYear;
            } else {
                $totalCoin += $maxCoinWithoutIncrement;
            }
            $profile = Profile::where('id', $profile_id_exp)->first();
            $profile->update([
                'total_exp' => $totalExperienceYears,
                'coin_exp' => $totalCoin,
            ]);
        } else {
            $profile = Profile::where('id', $profile_id_exp)->first();
            $profile->update([
                'total_exp' => 0,
                'coin_exp' => 0,
            ]);
        }


        return response()->json([
            'is_check' => true,
            'success' => 'Xóa thành công!',
        ], 201);
    }


    public function saveEdu(Request $request)
    {
        $messages =  $this->message_val;
        $validator_edu = Validator::make($request->all(), [
            'name' => 'required|string',
            'gpa' => 'required|numeric|between:0,10',
            'type_degree' => 'required',
            'start_date' => 'required|date_format:Y-m-d',
            'end_date' => 'required|date_format:Y-m-d|after:start_date|before:now',
            'major' => 'required',
            'profile_id' => 'required',
        ], $messages);

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
            'major' =>  $request->major,
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
        $messages =  $this->message_val;
        $validator_edu = Validator::make($request->all(), [
            'name' => 'required|string',
            'gpa' => 'required|numeric|between:0,10',
            'type_degree' => 'required',
            'start_date' => 'required|date_format:Y-m-d',
            'end_date' => 'required|date_format:Y-m-d|after:start_date|before:now',
            'major' => 'required',
        ], $messages);

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
            'major' =>  $request->major,
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
        $messages =  $this->message_val;
        $validator_project = Validator::make($request->all(), [
            'project_name' => 'required|string',
            'position' => 'required',
            'start_date' => 'required|date_format:Y-m-d',
            'end_date' => 'required|date_format:Y-m-d|after:start_date|before:now',
            'desc' => 'required',
            'link_project' => 'required',
            'profile_id' => 'required',
        ], $messages);

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
        $messages =  $this->message_val;
        $validator_project = Validator::make($request->all(), [
            'project_name' => 'required|string',
            'position' => 'required',
            'start_date' => 'required|date_format:Y-m-d',
            'end_date' => 'required|date_format:Y-m-d|after:start_date|before:now',
            'desc' => 'required',
            'link_project' => 'required',
        ], $messages);

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
        $messages =  $this->message_val;
        $validator_skill = Validator::make($request->all(), [
            'name_skill' => 'required',
            'profile_id' => 'required',
        ], $messages);

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
        $messages =  $this->message_val;
        $validator_skill = Validator::make($request->all(), [
            'name_skill' => 'required',
        ], $messages);

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
    public function percentCV(Request $request)
    {
        $profile_id = $request->profile_id;
        $profile = Profile::find($profile_id);
        $totalPossiblePoints = 100;
        $totalPointsEarned = 0;
        $completionPercentage = 0;
        $cvData = [
            'personal_info' => Profile::where('id', $profile_id)->count(),
            'work_experience' => Exp::where('profile_id', $profile_id)->count(),
            'education' => Edu::where('profile_id', $profile_id)->count(),
            'project' => Project::where('profile_id', $profile_id)->count(),
            'skills' => SkillProfile::where('profile_id', $profile_id)->count(),
        ];
        $fieldWeights = [
            'personal_info' => 20,
            'work_experience' => 30,
            'education' => 20,
            'project' => 15,
            'skills' => 15,
        ];

        foreach ($fieldWeights as $field => $weight) {
            if (isset($cvData[$field]) && is_numeric($cvData[$field]) && $cvData[$field] > 0) {
                $totalPointsEarned += $weight;
            }
        }

        $completionPercentage = ($totalPointsEarned / $totalPossiblePoints) * 100;
        $profile->update(['percent_cv' => ($totalPointsEarned / $totalPossiblePoints) * 100]);
        if ($completionPercentage >= 88) {
            if ($profile->coin_status) {
                $coinStatus = json_decode($profile->coin_status, true);
            } else {
                $coinStatus = ['2000' => false, '3000' => false];
            }
            if (!$coinStatus['3000']) {
                $profile->update(['coin' => $profile->coin + 3000]);
                $coinStatus['3000'] = true;
                $profile->update(['coin_status' => json_encode($coinStatus)]);
            }
        } else if ($completionPercentage < 88) {
            if ($profile->coin_status) {
                $coinStatus = json_decode($profile->coin_status, true);
            } else {
                $coinStatus = ['2000' => false, '3000' => false];
            }

            if ($coinStatus['3000']) {
                $profile->update(['coin' => $profile->coin - 3000]);
                $coinStatus['3000'] = false;
                $profile->update(['coin_status' => json_encode($coinStatus)]);
            }
        }
        return response()->json([
            'completion_percentage' => $profile->percent_cv,
        ], 200);
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
                $profile = Profile::find($profile_id);
                $totalPossiblePoints = 100;
                $totalPointsEarned = 0;
                $completionPercentage = 0;
                $cvData = [
                    'personal_info' => Profile::where('id', $profile_id)->count(),
                    'work_experience' => Exp::where('profile_id', $profile_id)->count(),
                    'education' => Edu::where('profile_id', $profile_id)->count(),
                    'project' => Project::where('profile_id', $profile_id)->count(),
                    'skills' => SkillProfile::where('profile_id', $profile_id)->count(),
                ];
                $fieldWeights = [
                    'personal_info' => 20,
                    'work_experience' => 30,
                    'education' => 20,
                    'project' => 15,
                    'skills' => 15,
                ];

                foreach ($fieldWeights as $field => $weight) {
                    if (isset($cvData[$field]) && is_numeric($cvData[$field]) && $cvData[$field] > 0) {
                        $totalPointsEarned += $weight;
                    }
                }

                $completionPercentage = ($totalPointsEarned / $totalPossiblePoints) * 100;
                $profile->update(['percent_cv' => ($totalPointsEarned / $totalPossiblePoints) * 100]);
                if ($completionPercentage >= 88) {
                    if ($profile->coin_status) {
                        $coinStatus = json_decode($profile->coin_status, true);
                    } else {
                        $coinStatus = ['2000' => false, '3000' => false];
                    }
                    if (!$coinStatus['3000']) {
                        $profile->update(['coin' => $profile->coin + 3000]);
                        $coinStatus['3000'] = true;
                        $profile->update(['coin_status' => json_encode($coinStatus)]);
                    }
                } else if ($completionPercentage < 88) {
                    if ($profile->coin_status) {
                        $coinStatus = json_decode($profile->coin_status, true);
                    } else {
                        $coinStatus = ['2000' => false, '3000' => false];
                    }

                    if ($coinStatus['3000']) {
                        $profile->update(['coin' => $profile->coin - 3000]);
                        $coinStatus['3000'] = false;
                        $profile->update(['coin_status' => json_encode($coinStatus)]);
                    }
                }
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