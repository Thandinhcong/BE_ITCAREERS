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

    protected $fillable = ['curriculum_vitae_id', 'job_post_id', 'evaluate', 'name', 'email', 'phone', 'status', 'candidate_id', 'qualifying_round_id', 'introduce', 'type'];
    public static function getCadidate($request, $job_post_id)
    {
        if (!empty($request->time_filter)) {
            if ($request->time_filter == 28) {
                $from =  date_format(date_modify(now(), "-28 days"), "Y-m-d");
            } else {
                $from =  date_format(date_modify(now(), "-7 days"), "Y-m-d");
            }
        } else {
            $from =  date_format(date_modify(now(), "-7 days"), "Y-m-d");
        }
        $to =  date_format(now(), "Y-m-d");
        $totalApplied = DB::table('job_post_apply')
            ->where('job_post_id', $job_post_id)->whereBetween('created_at', [$from, $to])
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as applied'))
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
                // if($data['total']!=0){
                //     dd([$val->date, $data['total']]);
                // }
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
