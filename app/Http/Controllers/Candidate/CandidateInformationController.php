<?php

namespace App\Http\Controllers\Candidate;

use App\Http\Controllers\Controller;
use App\Http\Resources\CandidatesResource;
use App\Models\Candidate;
use App\Models\District;
use App\Models\Experience;
use App\Models\Major;
use App\Models\Profile;
use App\Models\Province;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

use function Laravel\Prompts\table;

class CandidateInformationController extends Controller
{
    private $v;
    public function __construct()
    {
        $this->v = [];
    }

    public function index()
    {
        $candidate = Auth::user();
        return response()->json(['candidate' => CandidatesResource::make($candidate)]);
    }

    public function store(Request $request)
    {
        $candidate = Auth::user();
        $id = Auth::user()->id;
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|email',
            'phone' => 'required|unique:candidates,phone,' . $id,
            'address' => 'required|string',
            'gender' => '',
            'type' => '',
            'status' => '',
            'coin' => '',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'fail',
                'errors' => $validator->messages()
            ], 400);
        }

        if ($candidate) {
            $candidate->update($request->all());
            return response()->json([
                'status' => 'success',
                'message' => 'update success'
            ], 200);
        } else {
            return response()->json([
                'status' => 'fail',
            ], 500);
        }
    }
    public function findJob(Request $request)
    {
        $candidate = Auth::user();
        if ($candidate->find_job === 1 || $candidate->find_job === 0) {
            $newStatus = $candidate->find_job === 1 ? 0 : 1;
            $candidate->update(['find_job' => $newStatus]);
            return response()->json([
                'status' => true,
                'message' => 'Chuyển trạng thái thành công',
            ], 201);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Chuyển trạng thái thất bại',
            ], 400);
        }
    }
    public function getDataInformationFindJob()
    {
        $this->v['experience'] = Experience::all();
        $this->v['province'] = Province::all();
        $this->v['district'] = District::all();
        return response()->json([
            'status' => true,
            'data' => $this->v,
        ], 200);
    }
    public function getInfoFindJob()
    {
        $candidate = Auth::user();
        $id = Auth::user()->id;
        $experience = DB::table('candidates')
            ->where('candidates.id', '=', $id)
            ->leftJoin('experiences', 'experiences.id', '=', 'candidates.experience_id')
            ->select('experiences.experience')
            ->first();

        $work_location = DB::table('candidates')
            ->where('candidates.id', '=', $id)
            ->leftJoin('district', 'district.id', '=', 'candidates.district_id')
            ->leftJoin('province', 'province.id', '=', 'district.province_id')
            ->select('district.name', 'province.province')
            ->first();

        $info_find_job = DB::table('candidates')
            ->where('candidates.id', '=', $id)
            ->select(
                'experience_id as experience',
                'major',
                'desired_salary',
                'district_id as work_location'
            )
            ->first();

        $info_find_job->work_location = $work_location ? $work_location->name . ', ' . $work_location->province : null;
        $info_find_job->experience = $experience ? $experience->experience : null;

        $this->v['info_find_job'] = $info_find_job;
        $this->v['count_open_profile'] = DB::table('profile')
            ->join('profile_open', 'profile.id', '=', 'profile_open.profile_id')
            ->where('profile.candidate_id',$id)
            // ->where('profile.deleted_at',null)
            ->select('profile.candidate_id')
            ->count();
        return response()->json([
            'status' => true,
            'info_find_job' => $this->v,
        ], 200);
    }
    public function saveInformationFindJob(Request $request)
    {
        $candidate = Auth::user();
        $validator = Validator::make($request->all(), [
            'experience_id' => 'required',
            'district_id' => 'required',
            'desired_salary' => 'required',
            'major' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'fail',
                'errors' => $validator->messages()
            ], 400);
        }
        //update coin
        $profile = Profile::find($candidate->main_cv);
        if ($profile) {
            if ($profile->coin_status) {
                $coinStatus = json_decode($profile->coin_status, true);
            } else {
                $coinStatus = ['2000' => false, '3000' => false];
            }
            if (!$coinStatus['2000']) {
                $profile->update(['coin' => $profile->coin + 2000]);
                $coinStatus['2000'] = true;
                $profile->update(['coin_status' => json_encode($coinStatus)]);
            }
        }
        if ($candidate) {
            $candidate->update($request->all());
            return response()->json([
                'status' => 'success',
                'message' => 'update success'
            ], 200);
        } else {
            return response()->json([
                'status' => 'fail',
            ], 500);
        }
    }
}
