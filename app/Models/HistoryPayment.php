<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class HistoryPayment extends Model
{
    use HasFactory;
    protected $table = 'history_payments';
    protected $fillable = ['id', 'user_id', 'note', 'coin', 'type_coin', 'type_account'];
    public static function getMoneyMonthly()
    {

        $monthRange = array();
        $dayRange = [];
        $data = [];

        for ($i = 7; $i >= 1; $i--) {

            $dayRange[] = Carbon::now()->subDays($i)->format('Y-m-d');
        }
        $totalMoneyDay = HistoryPayment::where('type_coin', 0)
            ->where('type_account', 0)
            ->whereBetween('created_at', [$dayRange[0], $dayRange[6]])
            ->select(
                DB::raw('sum(coin) as totalMoneyDay'),
                DB::raw('DATE(created_at) as day')
            )
            ->groupBy('day')
            ->get()
            ->toArray();
        $jobPost = JobPost::where('start_date', '<=', now()->format('Y-m-d'))
            ->where('end_date', '>=', now()->format('Y-m-d'))
            ->where('job_post.status', 1)
            ->whereDate('created_at','>=',$dayRange[0])
            ->whereDate('created_at', '<=', $dayRange[6])
            ->select(
                DB::raw('count(status) as count'),
                DB::raw('DATE(created_at) as day')
            )
            ->groupBy('day')
            ->get()
            ->toArray();
        $jobPostVip = JobPost::where('job_post.status', 1)
            ->where('job_post.type_job_post_id', 1)
            ->whereDate('created_at','>=',$dayRange[0])
            ->whereDate('created_at', '<=', $dayRange[6])
                        ->select(
                DB::raw('count(status) as count'),
                DB::raw('DATE(created_at) as day')
            )
            ->groupBy('day')
            ->get()
            ->toArray();
        $jobPostNormal = JobPost::where('job_post.status', 1)
            ->where('job_post.type_job_post_id', 0)
            ->whereDate('created_at','>=',$dayRange[0])
            ->whereDate('created_at', '<=', $dayRange[6])            ->select(
                DB::raw('count(status) as count'),
                DB::raw('DATE(created_at) as day')
            )
            ->groupBy('day')
            ->get()
            ->toArray();
        $followDay = [];
        // $followDay['staticMoneyFollowDay'] = [];
        // $followDay['staticJobFollowDay '] = [];
        // $followDay['staticJobVipFollowDay '] = [];
        // $followDay['staticJobNormalFollowDay'] = [];
        foreach ($dayRange as $day) {
            $total = 0;
            foreach ($totalMoneyDay as $key => $value) {
                if (strtotime($value['day']) == strtotime($day)) {
                    $total = $value['totalMoneyDay'];
                    break;
                }
            }
            $followDay['staticMoneyFollowDay'][] = $total;
        }
        foreach ($dayRange as $day) {
            $total = 0;
            foreach ($jobPost as $key => $value) {
                if (strtotime($value['day']) == strtotime($day)) {
                    $total = $value['count'];
                    break;
                }
            }
            $followDay['staticJobFollowDay '][] = $total;
        }
        foreach ($dayRange as $day) {
            $total = 0;
            foreach ($jobPostVip as $key => $value) {
                if (strtotime($value['day']) == strtotime($day)) {
                    $total = $value['count'];
                    break;
                }
            }
            $followDay['staticJobVipFollowDay '][] = $total;
        }
        foreach ($dayRange as $day) {
            $total = 0;
            foreach ($jobPostNormal as $key => $value) {
                if (strtotime($value['day']) == strtotime($day)) {
                    $total = $value['count'];
                    break;
                }
            }
            $followDay['staticJobNormalFollowDay'][] = $total;
        }

        for ($i = 11; $i >= 0; $i--) {
            $month = Carbon::today()->startOfMonth()->subMonth($i);
            $year = Carbon::today()->startOfMonth()->subMonth($i)->format('Y');
            array_push($monthRange, array(
                'month' => $month->month,
                'year' => $year
            ));
        }
        $totalMoney = HistoryPayment::select(
            DB::raw('sum(coin) as totalMoney'),
            DB::raw('MONTH(created_at) as month')
        )
            ->groupBy('month')
            ->get()->toArray();
        $jobPost = JobPost::where('start_date', '<=', now()->format('Y-m-d'))
            ->where('end_date', '>=', now()->format('Y-m-d'))
            ->where('job_post.status', 1)
            ->select(
                DB::raw('count(status) '),
                DB::raw('MONTH(created_at) as month')
            )
            ->groupBy('month')
            ->get()
            ->toArray();
        $jobPostVip = JobPost::where('job_post.status', 1)
            ->where('job_post.type_job_post_id', 1)
            ->select(
                DB::raw('count(status) '),
                DB::raw('MONTH(created_at) as month')
            )
            ->groupBy('month')
            ->get()
            ->toArray();
        $jobPostNormal = JobPost::where('job_post.status', 1)
            ->where('job_post.type_job_post_id', 0)
            ->select(
                DB::raw('count(status) '),
                DB::raw('MONTH(created_at) as month')
            )
            ->groupBy('month')
            ->get()
            ->toArray();
        $staticMoneyFollowMonth = [];
        foreach ($monthRange as $month) {
            $total = 0;
            foreach ($totalMoney as $key => $value) {
                if ($value['month'] == $month['month']) {
                    $total = $value['totalMoney'];
                    break;
                }
            }
            $staticMoneyFollowMonth[] = $total;
            $months[] = $month['month'] . '/' . $month['year'];
        }
        $followMonth = [];

        foreach ($monthRange as $month) {
            $total = 0;
            foreach ($jobPost as $key => $value) {
                if (strtotime($value['month']) == strtotime($day)) {
                    $total = $value['count'];
                    break;
                }
            }
            $followMonth['staticJobFollowMonth'][] = $total;
        }
        foreach ($monthRange as $month) {
            $total = 0;
            foreach ($jobPostVip as $key => $value) {
                if (strtotime($value['month']) == strtotime($day)) {
                    $total = $value['count'];
                    break;
                }
            }
            $followMonth['staticJobVipFollowMonth'][] = $total;
        }
        foreach ($monthRange as $month) {
            $total = 0;
            foreach ($jobPostNormal as $key => $value) {
                if (strtotime($value['month']) == strtotime($day)) {
                    $total = $value['count'];
                    break;
                }
            }
            $followMonth['staticJobNormalFollowMonth'][] = $total;
        }
        $data = [
            'time' => $months,
            'money' => $staticMoneyFollowMonth,
            'day' => $dayRange,
            'folowDay' => $followDay,
            'folowMonth' => $followMonth,
        ];
        return $data;
    }
}
