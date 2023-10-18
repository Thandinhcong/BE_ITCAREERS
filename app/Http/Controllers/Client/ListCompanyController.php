<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Resources\CompanyResource;
use App\Models\Company;
use Illuminate\Http\Request;

class ListCompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $list_company = Company::all();
        if ($list_company->count() === 0) {
            return response()->json([
                'status' => 'fail',
                'list_company' => CompanyResource::collection($list_company),
            ], 404);
        }
        // return response()->json([
        //     'status' => 'success',
        //     'list_company' => CompanyResource::collection($list_company),
        // ], 200);
        return response()->json([
            'status' => 'success',
            'list_company' => $list_company,
        ], 200);
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
        $company = Company::find($id);
        if ($company) {
            return response()->json([
                'status' => 'success',
                'company' =>  $company
            ], 200);
        } else {
            return response()->json([
                'status' => 'fail',
                'company' =>  $company
            ], 404);
        }
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
