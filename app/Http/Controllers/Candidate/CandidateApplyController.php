<?php

namespace App\Http\Controllers\Candidate;

use App\Http\Controllers\Controller;
use App\Http\Resources\CandidateApplyResource;
use App\Models\CandidateApply;
use App\Models\JobPostApply;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CandidateApplyController extends Controller
{
    //
    // public function index()
    // {
    //     $candidate_apply = CandidateApply::all();
    //     return CandidateApplyResource::collection($candidate_apply);
    // }

    public function candidate_apply(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'phone' => 'required',
            'profile_id' => 'required',
            'email' => 'required'
        ]);
        $request['job_post_id'] = $id;
        // $request['candidate_id']=1
        // // Auth::guard('candidate')->user()->id;
        if ($validator->fails()) {
            return response()->json([
                'status' => 'fail',
                'errors' => $validator->messages()
            ], 400);
        } else {
            $candidate_apply = DB::table('job_post_apply')->where('job_post_id', $id)
                ->where('candidate_id', $request['candidate_id'])->first();
            if ($candidate_apply) {
                return response()->json([
                    'message' => 'Bạn đã ứng tuyên',
                ], 400);
            }
            $candidate_apply = JobPostApply::create($request->all());
        }
        if ($candidate_apply) {
            return response()->json([
                'status' => 'success',
                'message' => 'Bạn đã ứng tuyển thành công ',
                ' $candidate_apply' => $candidate_apply
            ], 200);
        } else {
            return response()->json([
                'status' => 'fail',
            ], 500);
        }
    }
}
