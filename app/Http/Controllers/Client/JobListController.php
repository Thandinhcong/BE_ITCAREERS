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
            ->join('companies', 'companies.id', '=', 'job_post.company_id')
            ->join('working_form', 'working_form.id', '=', 'job_post.working_form_id')
            ->join('academic_level', 'academic_level.id', '=', 'job_post.academic_level_id')
            ->join('major', 'major.id', '=', 'job_post.major_id')
            ->join('district', 'district.id', '=', 'job_post.area_id')
            ->join('province', 'district.province_id', '=', 'province.id',)
            ->select(
                'job_post.id',
                'job_post.title',
                'job_post.min_salary',
                'job_post.max_salary',
                'job_position.job_position',
                'experiences.experience',
                'companies.company_name as company_name',
                'companies.description',
                'companies.address',
                'working_form.working_form',
                'academic_level.academic_level',
                'major.major',
                'district.name',
                'province.province',
                'job_post.start_date',
                'job_post.end_date',
                'job_post.quantity',
                'job_post.requirement as require',
                'job_post.interest',
                'job_post.desc',
                'district.name as district',
                'province.province',
            )->first();
        if ($job_detail != []) {
            DB::table('job_post')->where('job_post.id', $id)->increment('view');
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
    public function check_save($job_list)
    {
        $candidate_id = Auth::user()->id;
        $check_save = DB::table('save_job_post')
            ->join('candidates', 'save_job_post.candidate_id', '=', 'candidates.id')
            ->join('job_post', 'save_job_post.job_post_id', '=', 'job_post.id')
            ->where('save_job_post.candidate_id', $candidate_id)
            ->where('save_job_post.job_post_id', $job_list->id)
            ->select(
                'job_post.id',
            )
            ->first();
        if ($check_save) {
            $job_list->id = $check_save->id;
            $job_list->save_job_post = 'đã lưu';
        } else {
            $job_list->save_job_post = 'chưa lưu';
        }
        return $job_list;
    }
    public function job_list()
    {
        $job_list = DB::table('job_post')
            // ->where('start_date', '<=', now()->format('Y-m-d'))
            // ->where('end_date', '>=', now()->format('Y-m-d'))
            // ->where('job_post.status', 1)
            ->join('companies', 'companies.id', '=', 'job_post.company_id')
            ->join('district', 'district.id', '=', 'job_post.area_id')
            ->join('province', 'district.province_id', '=', 'province.id',)
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

            )->get();
            foreach ($job_list as $data) {
                $this->check_save($data);
        }
        if ($job_list != []) {
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
    } public function location_work()
    {
//
        $job_list = DB::table('job_post')
            // ->where('start_date', '<=', now()->format('Y-m-d'))
            // ->where('end_date', '>=', now()->format('Y-m-d'))
            // ->where('job_post.status', 1)
            ->join('district', 'job_post.area_id', '=', 'district.id')
            ->join('province', 'district.province_id', '=', 'province.id')
            ->groupBy('province.id')
            ->orderByDesc('job_count')
            ->select(
                'province.id',
                'province.province',
                DB::raw('count(*) as  job_count'),
            )->limit(3)->get();
           
        if ($job_list != []) {
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
}
