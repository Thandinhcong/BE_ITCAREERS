<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\ManagementWebResource;
use App\Models\ManagementWeb;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ManagementWebController extends Controller
{

    public function index()
    {
        $man_web = ManagementWeb::all();
        return ManagementWebResource::collection($man_web);
    }


    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'logo' => 'required|string',
            'banner' => 'required|string',
            'name_web' => 'required|string',
            'company_name' => 'required|string',
            'address' => 'required|string',
            'email' => 'required|string',
            'phone' => 'required|string',
            'sdt_lienhe' => 'required|string'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'fail',
                'errors' => $validator->messages()
            ], 400);
        }
        $man_web = ManagementWeb::find($id);
        if ($man_web) {
            $man_web->update($request->all());
            return response()->json([
                'status' => 'success',
                'message' => 'update success'
            ], 200);
        } else {
            return response()->json([
                'status' => 'fail',
                'message' => 'error'
            ], 500);
        }
    }
}
