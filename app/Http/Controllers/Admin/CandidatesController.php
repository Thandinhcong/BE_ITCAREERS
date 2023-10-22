<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\CandidatesResource;
use App\Models\Candidate;
use App\Models\Candidates;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CandidatesController extends Controller
{

    // danh sach ung vien
    public function index()
    {
        $candidate = Candidate::all();
        return CandidatesResource::collection($candidate);
    }

    // public function store(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'name' => 'required|string|max:55',
    //     ]);
    //     if ($validator->fails()) {
    //         return response()->json([
    //             'status' => 'fail',
    //             'errors' => $validator->messages()
    //         ], 400);
    //     } else {
    //         $candidate = Candidates::create($request->all());
    //     }
    //     if ($candidate) {
    //         return response()->json(['status' => 'success', 'message' => 'Thêm thành công'], 200);
    //     } else {
    //         return response()->json(['status' => 'fail', 'message' => 'error'], 500);
    //     }
    // }

    // chi tiet ung vien
    public function show(string $id)
    {
        $candidate = Candidate::find($id);
        if ($candidate) {
            return response()->json([
                'status' => 200,
                'candidate' => $candidate
            ], 200);
        } else {
            return response()->json([
                'status' => 'fail',
                'candidate' => 'Candidate Not Found'
            ], 404);
        }
    }


    // cap nhat thong tin ung vien
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'fail',
                'errors' => $validator->messages()
            ], 400);
        }

        $candidate = Candidate::find($id);
        if ($candidate) {
            $candidate->update($request->all());
            return response()->json([
                'status' => 'success',
                'message' => 'Update Success'
            ], 200);
        } else {
            return response()->json([
                'status' => 'fail',
                'message' => 'Candidate Not Found'
            ], 404);
        }
    }
}
