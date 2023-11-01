<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Resources\CompanyResource;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ListCompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $list_company = null;
        $count_company = 0;

        if ($request->company_name) {
            $list_company = DB::table('companies')
                ->where('company_name', 'LIKE', '%' . $request->company_name . '%')
                ->whereNull('deleted_at')
                ->get();
            $count_company = $list_company->count();
        } else {
            $list_company = Company::all();
            $count_company = $list_company->count();
        }

        $response = [
            'status' => true,
            'list_company' => $list_company,
        ];

        if ($count_company === 0) {
            $response['status'] = false;
            $response['message'] = 'Không tìm thấy công ty';
            return response()->json($response, 404);
        } else {
            $response['count_company'] =  $count_company;
            return response()->json($response, 200);
        }
    }

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
}
