<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Candidate;
use App\Models\Company;
use App\Models\ProfileOpen;
use App\Models\SaveProfile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProfileCandidate extends Controller
{
    public function company_id()
    {
        return Auth::user()->id;
        // return 1;
    }
    public function hide_info($data)
    {
        $check_open = DB::table('profile_open')
        ->join('candidates', 'profile_open.candidate_id', '=', 'candidates.id')
        ->join('profile', 'profile.id', '=', 'candidates.main_cv')
            ->where('profile_open.company_id',$this->company_id())
            ->where('profile_open.candidate_id', $data->candidate_id)
            ->select(
                'profile_open.candidate_id',
                'profile.path_cv',
                // 'profile.id'
            )
            ->first();
        $check_save = DB::table('save_profile')
            ->where('company_id',$this->company_id())
            ->where('candidate_id', $data->candidate_id)
            ->select(
                'candidate_id'
            )
            ->first();
        if ($check_open) {
            $data->path_cv = $check_open->path_cv;
            $data->open_profile = 'đã mua';
        } else {
            $data->path_cv = null;
            $data->open_profile = 'chưa mua';
            $extractedPhoneNumber = substr($data->phone, 0, 6);
            $data->phone = str_pad($extractedPhoneNumber, 10, '*****', STR_PAD_RIGHT);
            $index = strpos($data->email, '@');
            $data->email = substr($data->email, 0, $index - 3) . str_repeat('*', 3) . substr($data->email, $index);
        }
        if ($check_save) {
            $data->save_profile = 'đã lưu';
        } else {
            $data->save_profile = 'chưa lưu';
        }
        return $data;
    }
    public function index()
    {
        $data = DB::table('candidates')
            ->join('profile', 'profile.id', '=', 'candidates.main_cv')
            // ->leftJoin('project', 'profile.id', '=', 'project.profile_id')
            // ->leftJoin('edu', 'profile.id', '=', 'edu.profile_id')
            // ->leftJoin('skill_profile', 'profile.id', '=', 'skill_profile.profile_id')
            ->groupBy('candidates.id')
            ->where('candidates.find_job', 1)
            ->select(
                'profile.name',
                'profile.title',
                'profile.id',
                'profile.email',
                'profile.phone',
                'profile.address',
                'candidates.id as candidate_id',
                'candidates.image',
                // DB::raw('GROUP_CONCAT(project.project_name SEPARATOR ",") as project_name'),
                // DB::raw('GROUP_CONCAT(edu.name SEPARATOR ",") as edu_name'),
            )
            ->get();
        foreach ($data as $customer) {
            $this->hide_info($customer);
        }
        return response()->json([
            "status" => 'success',
            "data" => $data,
        ], 200);
    }
    public function show_profile_open()
    {
        $data = DB::table('profile')
            ->join('candidates', 'candidates.main_cv', '=', 'profile.id')
            ->join('profile_open', 'candidates.id', '=', 'profile_open.candidate_id')
            ->where('candidates.find_job', 1)
            ->where('profile_open.company_id', $this->company_id())
            ->select(
                'profile.name',
                'profile.title',
                'profile.id',
                'profile.email',
                'profile.phone','profile.path_cv',
                'profile.address',
                'candidates.id as candidate_id',
                'candidates.image',
            )
            ->get();
        return response()->json([
            "status" => 'success',
            "data" => $data,
        ], 200);
    }
    public function show_save_profile()
    {
        $data = DB::table('profile')
            ->join('candidates', 'candidates.main_cv', '=', 'profile.id')
            ->join('save_profile', 'candidates.id', '=', 'save_profile.candidate_id')
            // ->where('candidates.find_job', 1)
            ->where('save_profile.company_id', $this->company_id())
            ->select(
                'profile.name',
                'profile.title',
                'profile.id',
                'profile.email',
                'profile.phone',
                'profile.address',
                'candidates.id as candidate_id',
                'candidates.image',
            )
            ->get();
            foreach ($data as $customer) {
                $this->hide_info($customer);
            }        return response()->json([
            "status" => 'success',
            "data" => $data,
        ], 200);
    }
    public function save_profile($id)
    {
        $check = DB::table('save_profile')->where('company_id', $this->company_id())->where('candidate_id', $id)
            ->first();
        if ($check) {
            return response()->json([
                'status' => 'fail',
                'error' => 'Đã lưu'
            ], 400);
        } else {
            $saveProfile = SaveProfile::create(
                [
                    'company_id' => $this->company_id(),
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
        $check = DB::table('profile_open')
            ->where('company_id', $this->company_id())
            ->where('candidate_id', $id)
            ->first();
        $check_coin = DB::table('companies')
            ->select('coin')
            ->where('id', $this->company_id())->first();
        $coin_profile = DB::table('profile')
            ->where('candidate_id', $id)
            ->select(
                'profile.coin',
            )
            ->first();
        if ($check_coin->coin > $coin_profile->coin) {
            if ($check) {
                return response()->json([
                    'status' => 'fail',
                    'error' => 'Bạn đã mua hồ sơ này'
                ], 400);
            } else {
                $saveProfile = ProfileOpen::create(
                    [
                        'company_id' => $this->company_id(),
                        'candidate_id' => $id,
                        'coin' => $coin_profile->coin
                    ]
                );
                $coinCompanyAffter = ($check_coin->coin) - ($coin_profile->coin);
                Company::find($this->company_id())->update(['coin' => $coinCompanyAffter]);
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
        } else {
            return response()->json([
                'status' => 'fail',
                'error' => 'Bạn không đủ tiền'
            ], 400);
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
