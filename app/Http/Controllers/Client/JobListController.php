<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\JobPost;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class JobListController extends Controller
{
    public function job_detail(string $id)
    {
        $job_detail = DB::table('job_post')->where('job_post.id', $id)
            ->join('job_position', 'job_position.id', '=', 'job_post.job_position_id')
            ->join('experiences', 'experiences.id', '=', 'job_post.exp_id')
            ->join('level', 'level.id', '=', 'job_post.level_id')
            ->join('companies', 'companies.id', '=', 'job_post.company_id')
            ->join('working_form', 'working_form.id', '=', 'job_post.working_form_id')
            ->join('academic_level', 'academic_level.id', '=', 'job_post.academic_level_id')
            ->join('major', 'major.id', '=', 'job_post.major_id')
            ->select(
                'job_post.id',
                'job_post.title',
                'job_post.min_salary',
                'job_post.max_salary',
                'level.level',
                'job_position.job_position',
                'experiences.experience',
                'companies.name as company_name',
                'companies.desc',
                'companies.address',
                'working_form.working_form',
                'academic_level.academic_level',
                'major.major',
                'job_post.start_date',
                'job_post.end_date',
                'job_post.quantity',
                'job_post.require',
                'job_post.interest',
            )->first();
        if ($job_detail) {
            return response()->json([
                'status' => 200,
                'job_detail' => $job_detail
            ], 200);
        } else {
            return response()->json([
                'status' => 404,
                'mesage' => 'không có bản ghi nào',
                'job_detail' => []
            ], 404);
        }
    }
    public function job_list()
    {
        $job_list = DB::table('job_post')->where('start_date', '<=', now()->format('Y-m-d'))
            // ->join('area', 'area.id', '=', 'job_post.area_id')
            ->where('job_post.status',1)
            ->join('companies', 'companies.id', '=', 'job_post.company_id')

            ->select(
                'job_post.id',
                'job_post.title',
                // 'area.area',
                'job_post.min_salary',
                'job_post.max_salary',
                'companies.name as company_name',
                'companies.logo',
            )->get();
        if ($job_list) {
            return response()->json([
                'status' => 200,
                'job_list' => $job_list
            ], 200);
        } else {
            return response()->json([
                'status' => 404,
                'mesage' => 'không có bản ghi nào',
                'job_list' => []
            ], 404);
        }
    }
    public function job_apply() {
        $candidate_id = Auth::guard('candidate')->user()->id;
        $job_apply = DB::table('job_post_apply')
        ->join('job_post', 'job_post_apply.job_post_id', '=', 'job_post.id')
        ->join('profile', 'job_post_apply.profile_id', '=', 'profile.id')
        ->join('companies', 'companies.id', '=', 'job_post.company_id')
        ->where('profile.candidate_id',$candidate_id)
        ->select(
            'job_post.id',
            'job_post.title',
            // 'area.area',
            'job_post.min_salary',
            'job_post.max_salary',
            'companies.name as company_name',
            'companies.logo',
            'job_post_apply.created_at as time_apply',
        )->get(); 
    if ($job_apply) {
        return response()->json([
            'status' => 200,
            'job_list' => $job_apply
        ], 200);
    } else {
        return response()->json([
            'status' => 404,
            'mesage' => 'không có bản ghi nào',
            'job_list' => []
        ], 404);
    }    }
}
