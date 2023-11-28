<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\SelectSalaryResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SearchController extends Controller
{
    public function select_salary_result()
    {
        return response()->json([
            'status' => 200,
            'data' => SelectSalaryResult::all(),
        ], 200);
    }
    public function search(Request $request)
    {
        $data = DB::table('job_post')
            ->select(
                'job_post.id',
                'job_post.title',
                'district.name as district',
                'province.province',
                'job_post.min_salary',
                'job_post.max_salary',
                'job_post.created_at',
                'companies.company_name as company_name',
                'companies.logo',
            )
            ->leftJoin('companies', 'job_post.company_id', '=', 'companies.id')
            ->leftJoin('district', 'job_post.area_id', '=', 'district.id')
            ->leftJoin('province', 'district.province_id', '=', 'province.id')
            ->where(function ($q) use ($request) {
                if (!empty($request->search)) {
                    $q->whereFullText(['title', 'desc'], $request->search);
                }
                if (!empty($request->province)) {
                    $q->where('province.id', '=', $request->province);
                }
                if (!empty($request->min_salary) && !empty($request->max_salary)) {
                    $q->whereNot('job_post.max_salary', '<=', $request->min_salary);
                }
            })
            // ->where('start_date', '<=', now()->format('Y-m-d'))
            // ->where('end_date', '>=', now()->format('Y-m-d'))
            // ->where('job_post.status', 1)
            ->get();
        return response()->json([
            'status' => 200,
            'data' => $data
        ], 200);
    }
}
