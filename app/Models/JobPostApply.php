<?php

namespace App\Models;

use App\Models\JobPostApply as ModelsJobPostApply;
use DateInterval;
use DatePeriod;
use DateTime;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class JobPostApply extends Model
{
    use HasFactory;
    protected $table = 'job_post_apply';
    protected $fillable = ['curriculum_vitae_id', 'company_id', 'job_post_id', 'evaluate', 'name', 'email', 'phone', 'status', 'candidate_id', 'qualifying_round_id', 'introduce', 'type_apply'];
    public static function getCandidate($request, $company_id)
    {
        if (!empty($request->time_filter)) {
            if ($request->time_filter == 28) {
                $from =  date_format(date_modify(now(), "-28 days"), "Y-m-d H:i:s");
            } else {
                $from =  date_format(date_modify(now(), "-7 days"), "Y-m-d H:i:s");
            }
        } else {
            $from =  date_format(date_modify(now(), "-7 days"), "Y-m-d H:i:s");
        }
        $to =  date_format(now(), "Y-m-d H:i:s");
        $totalApplied = DB::table('job_post_apply')
            ->join('job_post', 'job_post.id', '=', 'job_post_apply.job_post_id')
            ->where('job_post.company_id', $company_id)
            ->whereBetween('job_post_apply.created_at', [$from, $to])
            ->select(DB::raw('DATE(job_post_apply.created_at) as date'), DB::raw('count(*) as applied'))
            ->groupBy('date')
            ->get()
            ->toArray();
        $model = new ModelsJobPostApply();
        $dateArray = $model->getDatesFromRange($from, $to);
        $arrayShow = [];
        foreach ($dateArray as $key => $value) {
            $data = [];
            foreach ($totalApplied as $val) {
                $data['date'] = date('d-m-Y', strtotime($value));
                if ($value == $val->date) {
                    $data['total'] = $val->applied;
                    break;
                } else {
                    $data['total'] = 0;
                }
            }
            array_push($arrayShow, $data);
        }
        return $arrayShow;
    }
    public function getDatesFromRange($start, $end, $format = 'Y-m-d')
    {
        $array = array();
        $interval = new DateInterval('P1D');
        $realEnd = new DateTime($end);
        $realEnd->add($interval);
        $period = new DatePeriod(new DateTime($start), $interval, $realEnd);
        foreach ($period as $date) {
            $array[] = $date->format($format);
        }
        return $array;
    }
}
