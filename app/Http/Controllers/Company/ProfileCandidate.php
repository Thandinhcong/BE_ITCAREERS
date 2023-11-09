<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\ProfileOpen;
use App\Models\SaveProfile;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProfileCandidate extends Controller
{
    public function __construct()
    {
    }
    public function index()
    {
        // $company_id =Auth::guard('company')->user()->id;
        // $data['profile_open'] = DB::table('profile_open')->where('company_id', $company_id)->count->get();
        // $data['save_profile'] = DB::table('save_profile')->select('id')->where('company_id', $company_id)->get();
        $data['candidates'] = DB::table('candidates')
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
            ->addSelect(DB::raw("(SELECT COUNT(*) FROM profile_open WHERE candidates.id = profile_open.candidate_id) AS have_profile_open"))
            ->addSelect(DB::raw("(SELECT COUNT(*) FROM save_profile WHERE candidates.id = save_profile.candidate_id) AS have_save_profile"))
            ->join('profile', 'candidates.id', '=', 'profile.candidate_id')
            ->join('curriculum_vitae', 'candidates.main_cv', '=', 'curriculum_vitae.id')
            ->where('candidates.find_job', 1)
            ->get();

        return response()->json([
            "status" => 'success',
            "data" => $data,
        ], 200);
    }
    // public function show_profile_open()
    // {
    //     $company_id = Auth::guard('company')->user()->id;
    //     $data = DB::table('profile')
    //         ->select(
    //             'candidates.id as candidate_id',
    //             'candidates.image',
    //             'candidates.phone',
    //             'curriculum_vitae.id as cv_id',
    //             'curriculum_vitae.path_cv',
    //             'profile.name',
    //             'profile.email',
    //             'profile.phone',
    //             'profile.birth',
    //         )
    //         ->join('candidates', 'candidates.id', '=', 'profile.candidate_id')
    //         ->join('curriculum_vitae', 'candidates.main_cv', '=', 'curriculum_vitae.id')
    //         ->where('candidates.find_job', 1)
    //         ->where('profile_open.company_id', $company_id)

    //         ->whereExists(function (QueryBuilder $query) {
    //             $query->select(DB::raw(1))
    //                 ->from('profile_open')
    //                 ->whereColumn('profile_open.candidate_id', 'candidates.id');
    //         })
    //         ->get();

    //     return response()->json([
    //         "status" => 'success',
    //         "data" => $data,
    //     ], 200);
    // }
    // public function show_save_profile()
    // {
    //     $company_id = Auth::guard('company')->user()->id;
    //     $data = DB::table('profile')
    //         ->select(
    //             'candidates.id as candidate_id',
    //             'candidates.image',
    //             'candidates.phone',
    //             'curriculum_vitae.id as cv_id',
    //             'curriculum_vitae.path_cv',
    //             'profile.name',
    //             'profile.email',
    //             'profile.phone',
    //             'profile.birth',

    //         )
    //         ->join('candidates', 'candidates.id', '=', 'save_profile.candidate_id')
    //         ->join('curriculum_vitae', 'candidates.main_cv', '=', 'curriculum_vitae.id')
    //         ->join('profile', 'candidates.id', '=', 'profile.candidate_id')
    //         ->where('save_profile.company_id', $company_id)
    //         ->whereExists(function (QueryBuilder $query) {
    //             $query->select(DB::raw(1))
    //                 ->from('profile_open')
    //                 ->whereColumn('save_profile.candidate_id', 'candidates.id');
    //         })
    //         ->get();
    //     return response()->json([
    //         "status" => 'success',
    //         "data" => $data,
    //     ], 200);
    // }
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
    }
    public function open_profile($id)
    {
        $company_id = Auth::guard('company')->user()->id;
        $check = DB::table('profile_open')->where('company_id', $company_id)->where('candidate_id', $id)
            ->first();
        $check_coin = DB::table('companies')
            ->select('coin')
            ->where('id', $company_id)->first();
        if ($check_coin->coin == 0) {
            return response()->json([
                'status' => 'fail',
                'error' => 'Bạn không đủ tiền'
            ], 400);
        }
        $check_profile_apply = DB::table('job_post_apply')
            ->join('job_post', 'job_post.id', '=', 'job_post_apply.job_post_id')
            ->join('companies', 'companies.id', '=', 'job_post.company_id')
            ->join('candidates', 'candidates.id', '=', 'job_post_apply.candidate_id')
            ->select(
                'candidates.id',
            )
            ->where('companies.id', 1)->where('candidates.id', $id)->first();
        if ($check_profile_apply) {
            return response()->json([
                'status' => 'fail',
                'error' => 'ứng viên này đã gửi thông tin của mình đến bạn',

            ], 400);
        }
        if ($check) {
            return response()->json([
                'status' => 'fail',
                'error' => 'Đã lưu'
            ], 400);
        } else {
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
