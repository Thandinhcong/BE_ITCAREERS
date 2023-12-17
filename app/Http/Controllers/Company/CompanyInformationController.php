<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Http\Resources\CompanyInformationResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CompanyInformationController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function index()
    {
        $data = Auth::user();
        return response()->json([
            'company' => CompanyInformationResource::make($data),
        ]);
    }

    /**
     * Store a newly created resource in storage.1
     */
    public function store(Request $request)
    {
        $data = Auth::user();
        $id = Auth::user()->id;
        $validator = Validator::make($request->all(), [
            'company_name' => 'required|string',
            'tax_code' => 'required|string|unique:companies,tax_code,' . $id,
            'address' => 'required|string',
            'founded_in' => 'required|date',
            'name' => 'required|string',
            'office' => 'required|string',
            'email' => 'required|email',
            'phone' => 'required|unique:companies,phone,' . $id,
            'map' => 'required|string',
            'logo' => 'required|string',
            'link_web' => 'required|string',
            'image_paper' => 'required|string',
            'description' => 'required|string',
            'company_size_max' => 'required',
            'company_size_min' => 'required|lte:company_size_max'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'fail',
                'errors' => $validator->messages()
            ], 400);
        }

        if ($data) {
            $data->update($request->all());
            return response()->json([
                'status' => 'success',
                'message' => 'Update Success',
                'company' => CompanyInformationResource::make($data),
            ], 200);
        } else {
            return response()->json([
                'status' => 'fail',
            ], 500);
        }
    }

    /**
     * Display the specified resource.ss
     */
    public function show(string $id)
    {
        //sssss
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}