<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\JobPostApply;
use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CVController extends Controller
{
    public function index(Request $request)
    {
        if (Auth::user()) {
            $id_candidate = Auth::user()->id;
            // $data = DB::table('profile')
            //     ->where('candidate_id', '=', $id_candidate)
            //     ->get();
            $data =  Profile::where('candidate_id', $id_candidate)
            ->whereNotNull('type')
            ->get();
            
            return response()->json([
                'status' => true,
                'data' => $data
            ], 200);
        }
    }
    public function store(Request $request)
    {
        $candidate = Auth::user();
        $candidate_id = $candidate->id;
        $validator = Validator::make($request->all(), [
            'path_cv' => 'required',
            'title' => 'required',

        ]);
        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()
            ], 404);
        };
        $check_count = Profile::where('candidate_id', $candidate_id)
            ->where('type', 0)
            ->count();
        if ($check_count >= 3) {
            return response()->json([
                'status' => false,
                'message' => 'Bạn đã upload tối đa 3 CV !!!'
            ], 400);
        } else {

            $path_cv = $request->path_cv;
            $title = $request->title;
            $pathInfo = pathinfo($path_cv);
            $fileExtension = strtolower($pathInfo['extension']);
            if ($fileExtension === 'pdf') {
                $candidate = Auth::user();
                $candidate_id = $candidate->id;
                $cv = new Profile();
                $path_cv = $request->path_cv;
                if ($candidate) {
                    $cv->title = $title;
                    $cv->candidate_id = $candidate_id;
                    $cv->name = $candidate->name;
                    $cv->email = $candidate->email;
                    $cv->phone = $candidate->phone;
                    $cv->image = $request->image;
                    $cv->path_cv = $path_cv;
                    $cv->type = 0;
                    $cv->coin = 1000;
                    $cv->save();
                    $profile_id = $cv->id;
                    return response()->json([
                        'profile_id' => $profile_id,
                        'message' => 'Tạo thành công'
                    ], 201);
                }
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Vui lòng chọn file pdf'
                ], 400);
            }
        }
    }
    public function activeCV(Request $request)
    {
        try {
            $id = $request->id;
            $id_candidate = Auth::user()->id;
            $main_cv = Auth::user();
            $get_all_cv = Profile::where('candidate_id', $id_candidate)
                ->where('is_active', 1)->first();
            if (isset($get_all_cv->is_active)) {
                $get_all_cv->is_active = 0;
                $get_all_cv->save();
            }
            $cv_up = Profile::find($id);
            $cv_up->is_active = 1;
            $cv_up->save();
            $main_cv->update(
                ['main_cv' => $id]
            );
            $profile = Profile::find($main_cv->main_cv);
            if ($profile && $main_cv->major && $main_cv->desired_salary && $main_cv->experience_id && $main_cv->district_id) {
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
            return response()->json([
                'status' => true,
                'message' => 'Cập nhật thành công'
            ], 201);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }

    public function destroyCv(Request $request)
    {
        $id_cv = $request->id;
        if (isset($id_cv)) {
            $cv = Profile::find($id_cv);
            if ($cv->is_active == 1) {
                return response()->json([
                    'status' => false,
                    'message' => 'Vui lòng chuyển trạng thái CV'
                ], 400);
            }
            if (isset($cv)) {
                // $job_at = JobPostApply::where('status', $id)->get();
                // if (isset($job_at) && $job_at->count() > 0) {
                //     foreach ($job_at as $j) {
                //         $j->delete();
                //     }
                // }
                $cv->delete();
                return response()->json([
                    'status' => true,
                    'message' => 'Xóa thành công!',
                ]);
            }
            return response()->json([
                'status' => false,
                'message' => 'Xóa thất bại!'
            ]);
        }
    }
}