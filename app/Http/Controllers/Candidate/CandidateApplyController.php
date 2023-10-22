<?php

namespace App\Http\Controllers\Candidate;

use App\Http\Controllers\Controller;
use App\Http\Resources\CandidateApplyResource;
use App\Models\CandidateApply;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CandidateApplyController extends Controller
{
    //
    public function index()
    {
      $candidate_apply=CandidateApply::all();
      return CandidateApplyResource::collection($candidate_apply);
      
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required',
            'phone' => 'required',
            'desc' => 'required',
            'image' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'fail',
                'errors' => $validator->messages()
            ], 400);
        } else {
            $candidate_apply = CandidateApply::create($request->all());
        }
        if ($candidate_apply) {
            return response()->json([
                'status' => 'success',
                'message' => 'Bạn đã ứng tuyển thành công '
            ], 200);
        } else {
            return response()->json([
                'status' => 'fail',
            ], 500);
        }
    }
}
