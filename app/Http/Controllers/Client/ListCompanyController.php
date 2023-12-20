<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Resources\CompanyResource;
use App\Models\Company;
use App\Models\JobPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use function Laravel\Prompts\select;

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
                ->join('job_post', 'job_post.company_id', '=', 'companies.id')
                ->groupBy('job_post.company_id')
                ->select(
                    'companies.id',
                    'companies.company_name',
                    'companies.address',
                    'companies.founded_in',
                    'companies.name',
                    'companies.office',
                    'companies.email',
                    'companies.password',
                    'companies.phone',
                    'companies.map',
                    'companies.logo',
                    'companies.link_web',
                    'companies.image_paper',
                    'companies.description',
                    'companies.coin',
                    'companies.email_verified_at',
                    'companies.remember_token',
                    'companies.tax_code',
                    'companies.status',
                    'companies.company_size_max',
                    'companies.company_size_min',
                    'companies.created_at',
                    'companies.updated_at',
                    'companies.deleted_at',
                    DB::raw('count(*) as job_post_company'),
                )
                ->where('company_name', 'LIKE', '%' . $request->company_name . '%')
                ->whereNull('deleted_at')
                ->get();
            $count_company = $list_company->count();
        } else {
            $list_company = DB::table('companies')
                ->leftJoin('job_post', 'job_post.company_id', '=', 'companies.id')
                ->groupBy('companies.id')
                ->select(
                    'companies.id',
                    'companies.company_name',
                    'companies.address',
                    'companies.founded_in',
                    'companies.name',
                    'companies.office',
                    'companies.email',
                    'companies.password',
                    'companies.phone',
                    'companies.map',
                    'companies.logo',
                    'companies.link_web',
                    'companies.image_paper',
                    'companies.description',
                    'companies.coin',
                    'companies.email_verified_at',
                    'companies.remember_token',
                    'companies.tax_code',
                    'companies.status',
                    'companies.company_size_max',
                    'companies.company_size_min',
                    'companies.created_at',
                    'companies.updated_at',
                    'companies.deleted_at',
                    DB::raw('count(job_post.id) as job_post_company'),

                )->get();
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
        $number_jobs = JobPost::where('company_id', $id)
            ->count();
        if ($company) {
            return response()->json([
                'status' => 'success',
                'company' =>  $company,
                'number_jobs' => $number_jobs
            ], 200);
        } else {
            return response()->json([
                'status' => 'fail',
                'company' =>  $company
            ], 404);
        }
    }
}