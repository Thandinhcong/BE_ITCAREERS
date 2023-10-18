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
        $candidate = Auth::guard('candidate')->user();
        return response()->json(['candidate' => CandidatesResource::make($candidate)]);
    }

    public function store(Request $request)
    {
        $candidate = Auth::guard('candidate')->user();
        $id = Auth::guard('candidate')->user()->id;
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|email',
            'phone' => 'required|unique:candidates,phone,' . $id,
            'address' => 'required|string',
            'gender' => 'required|string',
            'type' => 'required|string',
            'status' => 'required|string',
            'coin' => 'required|string',
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
}
