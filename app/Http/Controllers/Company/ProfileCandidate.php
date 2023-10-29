<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProfileCandidate extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = DB::table('profile')
        ->select(
            'candidates.main_cv',
            'candidates.id',
            'curriculum_vitae.path_cv'
        )
            ->where('candidates.find_job', 1)
            ->where('candidates.main_profile', 1)
            ->join('candidates', 'candidates.id', '=', 'profile.candidate_id')
            ->get();
        return response()->json([
            "status" => 'success',
            "data" => $data,
        ], 404);
    }

    public function profile_open()
    {
        $company_id = 1;
        // Auth::user()->id;

        $data = DB::table('profile_open')
            ->select(
                'candidates.main_cv',
                'candidates.id',
                'curriculum_vitae.path_cv'
            )
            ->join('candidates', 'candidates.id', '=', 'profile_open.candidate_id')
            ->join('curriculum_vitae', 'candidates.main_cv', '=', 'curriculum_vitae.id')
            ->join('profile', 'candidates.id', '=', 'profile.candidate_id')
            ->where('profile_open.company_id', $company_id)
            ->get();
        return response()->json([
            "status" => 'success',
            "data" => $data,
        ], 404);
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
