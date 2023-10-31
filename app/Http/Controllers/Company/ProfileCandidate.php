<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\ProfileOpen;
use App\Models\SaveProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProfileCandidate extends Controller
{
    public function index()
    {
        $company_id = Auth::guard('company')->user()->id;
        $data['profile'] = DB::table('profile')
            ->select(
                'candidates.id as candidate_id',
                'candidates.image',
                'candidates.phone',
                'curriculum_vitae.id as cv_id',
                'curriculum_vitae.path_cv',
                'profile.name',
                'profile.email',
                'profile.phone',
                'profile.birth',
            )
            ->join('candidates', 'candidates.id', '=', 'profile.candidate_id')
            ->join('curriculum_vitae', 'candidates.main_cv', '=', 'curriculum_vitae.id')
            ->where('candidates.find_job', 1)
            ->get();
        $data['profile_open'] = DB::table('profile_open')->where('company_id', $company_id)->get();
        $data['save_profile'] = DB::table('save_profile')->where('company_id', $company_id)->get();
        return response()->json([
            "status" => 'success',
            "data" => $data,
        ], 200);
    }
    public function show_profile_open()
    {
        $company_id = Auth::guard('company')->user()->id;
        $data = DB::table('profile_open')
            ->select(
                'candidates.id as candidate_id',
                'curriculum_vitae.id as cv_id',
                'curriculum_vitae.path_cv',
                'profile.name',
                'profile.email',
                'profile.phone',
                'profile.birth',
                'profile_open.id as profile_open_id'
            )
            ->join('candidates', 'candidates.id', '=', 'profile_open.candidate_id')
            ->join('curriculum_vitae', 'candidates.main_cv', '=', 'curriculum_vitae.id')
            ->join('profile', 'candidates.id', '=', 'profile.candidate_id')
            ->where('profile_open.company_id', $company_id)
            ->get();
        return response()->json([
            "status" => 'success',
            "data" => $data,
        ], 200);
    }
    public function show_save_profile()
    {
        $company_id = Auth::guard('company')->user()->id;
        $data = DB::table('save_profile')
            ->select(
                'candidates.id as candidate_id',
                'curriculum_vitae.id as cv_id',
                'curriculum_vitae.path_cv',
                'profile.name',
                'profile.email',
                'profile.phone',
                'profile.birth',
                'save_profile.id as save_profile_id'
            )
            ->join('candidates', 'candidates.id', '=', 'save_profile.candidate_id')
            ->join('curriculum_vitae', 'candidates.main_cv', '=', 'curriculum_vitae.id')
            ->join('profile', 'candidates.id', '=', 'profile.candidate_id')
            ->where('save_profile.company_id', $company_id)
            ->get();
        return response()->json([
            "status" => 'success',
            "data" => $data,
        ], 200);
    }
    public function save_profile($id)
    {
        $company_id = Auth::guard('company')->user()->id;
        $check = DB::table('save_profile')->where('company_id', $company_id)->where('candidate_id', $id)
            ->first();
        if ($check) {
            return response()->json([
                'status' => 'fail',
                'error' => 'Đã lưu'
            ], 400);
        } else {
            $saveProfile = SaveProfile::create(
                [
                    'company_id' => $company_id,
                    'candidate_id' => $id
                ]
            );
        }
        if ($saveProfile) {
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
    } public function open_profile($id)
    {
        $company_id = Auth::guard('company')->user()->id;
        $check = DB::table('profile_open')->where('company_id', $company_id)->where('candidate_id', $id)
            ->first();
        if ($check) {
            return response()->json([
                'status' => 'fail',
                'error' => 'Đã lưu'
            ], 400);
        }
       else {
            $saveProfile = ProfileOpen::create(
                [
                    'company_id' => $company_id,
                    'candidate_id' => $id
                ]
            );
        }
        if ($saveProfile) {
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
    public function cancel_save_profile($id)
    {
        $saveProfile = SaveProfile::find($id);
        if (!$saveProfile) {
            return response()->json(['message' => 'save_profile not found'], 404);
        }
        $saveProfile->delete();
        return response()->json(null, 204);
    }
}
