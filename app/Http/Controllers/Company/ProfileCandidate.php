<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\ProfileOpen;
use App\Models\SaveProfile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProfileCandidate extends Controller
{
    public function company_id()
    {
        return Auth::guard('company')->user()->id;
    }
    public function hide_info($data)
    {
        foreach ($data as $customer) {
            $extractedPhoneNumber = substr($customer->phone, 0, 6);
            $customer->phone = str_pad($extractedPhoneNumber, 10, '*****', STR_PAD_RIGHT);
        }
        foreach ($data as $customer) {
            $index = strpos($customer->email, '@');
            $customer->email = substr($customer->email, 0, $index - 3) . str_repeat('*', 3) . substr($customer->email, $index);
        }
        return $data;
    }
    public function detail_candidate() {
        $data['profie'] = DB::table('profile')
        ->join('candidates', 'candidates.main_cv', '=', 'profile.id')
        ->where('candidates.find_job', 1)
        ->select(
            'candidates.id as candidate_id',
            'candidates.image',
            'profile.id as profile_id',
            'profile.name',
            'profile.email',
            'profile.phone',
            'profile.address',
            'profile.phone',
            'profile.path_cv',
            'profile.title',
            'profile.coin',
        )
        ->get();
        // $data['profile'] = $this->hide_info($data['profile']);
        // $data['profie_exp'] =
        // $data['project_name'] =
        // $data['profie_exp'] =

    }
    public function index()
    {
        $data = DB::table('profile')
            ->join('candidates', 'candidates.main_cv', '=', 'profile.id')
            ->where('candidates.find_job', 1)
            ->select(
                'candidates.id as candidate_id',
                'candidates.image',
                'profile.id as profile_id',
                'profile.name',
                'profile.email',
                'profile.phone',
                'profile.address',
                'profile.phone',
            )
            ->get();
        $data = $this->hide_info($data);
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
                'candidates.id as candidate_id',
                'candidates.image',
                'profile.id as profile_id',
                'profile.name',
                'profile.email',
                'profile.phone',
                'profile.address',
                'profile.phone',
            )
            ->get();
        $data = $this->hide_info($data);
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
            ->where('candidates.find_job', 1)
            ->where('save_profile.company_id', $this->company_id())
            ->select(
                'candidates.id as candidate_id',
                'candidates.image',
                'profile.id as profile_id',
                'profile.name',
                'profile.email',
                'profile.phone',
                'profile.address',
                'profile.phone',
            )
            ->get();
        $data = $this->hide_info($data);
        return response()->json([
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
                        'candidate_id' => $id
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
