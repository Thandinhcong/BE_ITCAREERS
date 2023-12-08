<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Candidate;
use App\Models\Company;
use App\Models\Profile;
use App\Models\ProfileOpen;
use App\Models\SaveProfile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProfileCandidate extends Controller
{
    public function company_id()
    {
        // return 1;
        return Auth::user()->id;
    }
    public function hide_info($data,)
    {
        $check_open = DB::table('profile_open')
            ->join('profile', 'profile.id', '=', 'profile_open.profile_id')
            ->where('profile_open.company_id', $this->company_id())
            ->where('profile_open.profile_id', $data->id)
            ->select(
                'profile_open.profile_id',
                'profile_open.start',
                'profile_open.comment',
                'profile.path_cv',
                // 'profile.id'
            )
            ->first();
        $check_save = DB::table('save_profile')
            ->where('company_id', $this->company_id())
            ->where('profile_id', $data->id)
            ->select(
                'profile_id'
            )
            ->first();

        if ($check_open) {
            $data->path_cv = $check_open->path_cv;
            $data->start = $check_open->start;
            $data->comment = $check_open->comment;
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
    public function avgStart($data)
    {
       
            return  $data->start = ProfileOpen::where('profile_id',$data->id)
            ->avg('start');
    }
    public function index()
    {
        $data = DB::table('profile')
            ->join('candidates', 'candidates.main_cv', '=', 'profile.id')
            ->leftjoin('district', 'district.id', '=', 'candidates.district_id')
            ->leftjoin('province', 'district.province_id', '=', 'province.id')
            ->leftjoin('experiences', 'experiences.id', '=', 'candidates.experience_id')
            ->leftJoin('profile_open', 'profile.id', '=', 'profile_open.profile_id')
            ->where('candidates.find_job', 1)
            ->select(
                'profile.name',
                'profile.title',
                'profile.id',
                'profile.email',
                'profile.phone',
                'profile.address',
                'profile.image',
                'profile.birth',
                'profile.careers_goal',
                'profile.total_exp',
                'profile.created_at',
                'candidates.id as candidate_id',
                'candidates.image',
                'candidates.desired_salary',
                'experiences.experience',
                'district.name as district',
                'province.province as province',
                DB::raw('AVG(profile_open.start) as start'),
            )
            ->groupBy('candidates.id')

            ->get();
        foreach ($data as $customer) {
            $this->hide_info($customer, 1);
        }
        return response()->json([
            "status" => 'success',
            "data" => $data,
        ], 200);
    }
    public function show_profile_open()
    {
        $data = DB::table('profile')
            ->join('candidates', 'candidates.id', '=', 'profile.candidate_id')
            ->leftjoin('district', 'district.id', '=', 'candidates.district_id')
            ->leftjoin('province', 'district.province_id', '=', 'province.id')
            ->leftjoin('experiences', 'experiences.id', '=', 'candidates.experience_id')
            ->leftJoin('profile_open', 'profile.id', '=', 'profile_open.profile_id')
            ->groupBy('profile.id')

            ->where('profile_open.company_id', $this->company_id())
            ->select(
                'profile.name',
                'profile.title',
                'profile.id',
                'profile.email',
                'profile.phone',
                'profile.path_cv',
                'profile.address',
                'candidates.id as candidate_id',
                'profile.created_at',
                'candidates.image',
                'candidates.find_job',
                'candidates.desired_salary',
                'experiences.experience',
                'district.name as district',
                'province.province as province',
            )
            ->get();
        foreach ($data as $customer) {
            $this->avgStart($customer);
        }
        return response()->json([
            "status" => 'success',
            "data" => $data,
        ], 200);
    }
    public function show_save_profile()
    {

        $data = DB::table('profile')
            ->join('candidates', 'candidates.id', '=', 'profile.candidate_id')
            ->leftjoin('district', 'district.id', '=', 'candidates.district_id')
            ->leftjoin('province', 'district.province_id', '=', 'province.id')
            ->leftjoin('experiences', 'experiences.id', '=', 'candidates.experience_id')
            ->join('save_profile', 'profile.id', '=', 'save_profile.profile_id')
            ->where('candidates.find_job', 1)
            ->where('save_profile.company_id', $this->company_id())
            ->select(
                'profile.name',
                'profile.title',
                'profile.id',
                'profile.email',
                'profile.phone',
                'profile.address',
                'candidates.id as candidate_id',
                'profile.created_at',
                'candidates.image',
                'candidates.desired_salary',
                'experiences.experience',
                'district.name as district',
                'province.province as province',
            )
            ->get();
        foreach ($data as $customer) {
            $this->hide_info($customer);
        }
        foreach ($data as $customer) {
            $this->avgStart($customer);
        }
        return response()->json([
            "status" => 'success',
            "data" => $data,
        ], 200);
    }
    public function save_profile($id)
    {
        $check = DB::table('save_profile')->where('company_id', $this->company_id())->where('profile_id', $id)
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
                    'profile_id' => $id
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
            ->where('profile_id', $id)
            ->first();
        $check_coin = DB::table('companies')
            ->select('coin')
            ->where('id', $this->company_id())->first();
        $coin_profile = 11;
        // DB::table('profile')
        //     ->where('id', $id)
        //     ->select(
        //         'profile.coin',
        //     )
        //     ->first();
        if ($check_coin->coin > 20000) {
            if ($check) {
                return response()->json([
                    'status' => 'fail',
                    'error' => 'Bạn đã mua hồ sơ này'
                ], 400);
            } else {
                $saveProfile = ProfileOpen::create(
                    [
                        'company_id' => $this->company_id(),
                        'profile_id' => $id,
                        //thêm ->coin
                        'coin' =>  $coin_profile
                    ]
                );
                //thêm ->coin
                $coinCompanyAffter = ($check_coin->coin) - ($coin_profile);
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

    public function feeback_profile(Request $request, string $id)
    {
        $valdator = Validator::make($request->all(), [
            'start' => 'required|',
            'comment' => 'required|',
        ]);
        if ($valdator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $valdator->messages(),
            ], 400);
        }
        $profile_open = ProfileOpen::where('company_id', $this->company_id())
            ->where('profile_id', $id)
            ->first();
        if (!$profile_open) {
            return response()->json([
                'status' => 400,
                'mesage' => "Bạn chưa mua hồ sơ",
            ], 400);
        }
        if ($profile_open->start != null) {
            return response()->json([
                'status' => 422,
                'errors' => "Bạn đã đánh giá rồi",
            ], 422);
        }
        if ($profile_open) {
            $profile_open->start = $request->start;
            $profile_open->comment = $request->comment;
            $profile_open->update();
            $company  = Company::where('id', $this->company_id())->first();
            $company->coin += 100;
            $company->update();
            return response()->json([
                'status' => 'success',
                'errors' => "Đánh giá thành công",
            ], 200);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'Không tìm thấy'
            ], 404);
        }
    }
    public function cancel_save_profile($id)
    {
        $saveProfile = SaveProfile::where('candidate_id', $id)
            ->where('company_id', $this->company_id())
            ->first();
        if (!$saveProfile) {
            return response()->json(['message' => 'save_profile not found'], 404);
        }
        $saveProfile->delete();
        return response()->json(null, 204);
    }
}
