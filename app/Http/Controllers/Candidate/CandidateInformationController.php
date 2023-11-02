<?php

namespace App\Http\Controllers\Candidate;

use App\Http\Controllers\Controller;
use App\Http\Resources\CandidatesResource;
use App\Models\Candidate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CandidateInformationController extends Controller
{

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
}
