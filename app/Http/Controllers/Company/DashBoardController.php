<?php

namespace App\Http\Controllers\Company;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\JobPost;
use App\Models\JobPostApply;
use App\Models\ProfileOpen;
use App\Models\HistoryPayment;
use App\Models\Vnpay_payment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashBoardController extends Controller
{
    private $v;
    public function __construct()
    {
        $this->v = [];
    }
    public function index(Request $request)
    {
        $company_id = Auth::user()->id;
        $today = Carbon::now();
        $dayRange = [];
        for ($i = 6; $i >=0; $i--) {
            $dayRange[] = $today->copy()->subDays($i)->format('Y-m-d');
        }
        $monthRange = array();
        for ($i = 11; $i >= 0; $i--) {
            $month = Carbon::today()->startOfMonth()->subMonth($i);
            $year = Carbon::today()->startOfMonth()->subMonth($i)->format('Y');
            array_push($monthRange, array(
                'month' => $month->month,
                'year' => $year
            ));
        }
        foreach ($monthRange as $month) {
            $months[] = $month['month'] . '/' . $month['year'];
        }
        $this->v['count_apply_post'] = DB::table('job_post')
            ->where('job_post.company_id', '=', $company_id)
            ->leftJoin('job_post_apply', 'job_post.id', '=', 'job_post_apply.job_post_id')
            ->join('type_job_post', 'job_post.type_job_post_id', '=', 'type_job_post.id')
            ->select(
                'job_post.id',
                'job_post.title',
                'type_job_post.name',
                DB::raw('IFNULL(COUNT(job_post_apply.job_post_id), 0) as count_apply'),
                'view',
                DB::raw('IFNULL(SUM(CASE WHEN job_post_apply.qualifying_round_id = 1 THEN 1 ELSE 0 END), 0) as matching_qualifying_round'),
                DB::raw('IFNULL(SUM(CASE WHEN job_post_apply.qualifying_round_id = 0 THEN 1 ELSE 0 END), 0) as inappropriate_qualifiers'),
                'job_post.created_at'
            )
            ->groupBy('job_post.id', 'job_post.title', 'type_job_post.name', 'job_post.created_at')
            ->orderBy('job_post.created_at', 'desc')
            ->get();

        foreach ($this->v['count_apply_post'] as &$item) {
            $coin_post = DB::table('history_payments')
                ->where('user_id', '=', $company_id)
                ->where('history_payments.created_at', '=', $item->created_at)
                ->where('type_account', '=', 0)
                ->where('type_coin', '=', 1)
                ->where('note', 'LIKE', '%' . $item->title . '%')
                ->select(DB::raw('COALESCE(coin, 0) as coin'))
                ->orderBy('history_payments.created_at', 'desc')
                ->first();

            $item->coin_post = $coin_post ? $coin_post->coin : 0;
        }
        $this->v['day'] = $dayRange;
        $this->v['month'] = $months;
        // coin post vip
        $coin_post_vip_by_day = [];
        $coin_post_vip_by_month = [];

        $coin_post_vip_day = HistoryPayment::where('user_id', $company_id)
            ->where('type_account', 0)
            ->where('type_coin', 1)
            ->where('note', 'LIKE', '%vip%')
            ->select(
                DB::raw('DATE(created_at) as day'),
                DB::raw('SUM(coin) as total_coin'),
            )
            ->groupBy('day')
            ->get()
            ->toArray();

        foreach ($dayRange as $day) {
            $total = 0;
            foreach ($coin_post_vip_day as $item) {
                if (strtotime($item['day']) == strtotime($day)) {
                    $total = $item['total_coin'];
                }
            }
            $coin_post_vip_by_day[] = $total;
        }
        $coin_post_vip_month = HistoryPayment::where('user_id', $company_id)
            ->where('type_account', 0)
            ->where('type_coin', 1)
            ->where('note', 'LIKE', '%vip%')
            ->select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(coin) as total_coin'),
            )
            ->groupBy('month')
            ->get()
            ->toArray();

        foreach ($monthRange as $month) {
            $total = 0;
            foreach ($coin_post_vip_month as $item) {
                if ($item['month'] == $month['month']) {
                    $total = $item['total_coin'];
                }
            }
            $coin_post_vip_by_month[] = $total;
        }

        $this->v['coin_post_vip_by_day'] = $coin_post_vip_by_day;
        $this->v['coin_post_vip_by_month'] = $coin_post_vip_by_month;
        // coin post normal
        $coin_post_normal_by_day = [];
        $coin_post_normal_by_month = [];

        $coin_post_normal_day = HistoryPayment::where('user_id', $company_id)
            ->where('type_account', 0)
            ->where('type_coin', 1)
            ->where('note', 'LIKE', '%thường%')
            ->select(
                DB::raw('DATE(created_at) as day'),
                DB::raw('SUM(coin) as total_coin'),
            )
            ->groupBy('day')
            ->get()
            ->toArray();
        foreach ($dayRange as $day) {
            $total = 0;
            foreach ($coin_post_normal_day as $item) {
                if (strtotime($item['day']) == strtotime($day)) {
                    $total = $item['total_coin'];
                }
            }
            $coin_post_normal_by_day[] = $total;
        }
        $coin_post_normal_month = HistoryPayment::where('user_id', $company_id)
            ->where('type_account', 0)
            ->where('type_coin', 1)
            ->where('note', 'LIKE', '%thường%')
            ->select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(coin) as total_coin'),
            )
            ->groupBy('month')
            ->get()
            ->toArray();

        foreach ($monthRange as $month) {
            $total = 0;
            foreach ($coin_post_normal_month as $item) {
                if ($item['month'] == $month['month']) {
                    $total = $item['total_coin'];
                }
            }
            $coin_post_normal_by_month[] = $total;
        }

        $this->v['coin_post_normal_by_day'] = $coin_post_normal_by_day;
        $this->v['coin_post_normal_by_month'] = $coin_post_normal_by_month;
        // coin open profile
        $coin_open_profile_by_day = [];
        $coin_open_profile_by_month = [];

        $coin_cv_day = ProfileOpen::where('company_id', $company_id)
            ->select(
                DB::raw('DATE(created_at) as day'),
                DB::raw('SUM(coin) as total_coin'),
            )
            ->groupBy('day')
            ->get()
            ->toArray();
        foreach ($dayRange as $day) {
            $total = 0;
            foreach ($coin_cv_day as $item) {
                if (strtotime($item['day']) == strtotime($day)) {
                    $total = $item['total_coin'];
                }
            }
            $coin_open_profile_by_day[] = $total;
        }
        $coin_cv_month = ProfileOpen::where('company_id', $company_id)
            ->select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(coin) as total_coin'),
            )
            ->groupBy('month')
            ->get()
            ->toArray();

        foreach ($monthRange as $month) {
            $total = 0;
            foreach ($coin_cv_month as $item) {
                if ($item['month'] == $month['month']) {
                    $total = $item['total_coin'];
                }
            }
            $coin_open_profile_by_month[] = $total;
        }
        $this->v['coin_open_profile_by_day'] = $coin_open_profile_by_day;
        $this->v['coin_open_profile_by_month'] = $coin_open_profile_by_month;
        ////////////////////////////////////////////////////////////////
        // SỐ TIỀN CHI TIÊU
        // day
        $spend_coin_payment_day = HistoryPayment::where('user_id', $company_id)
            ->where('type_account', 0)
            ->where('type_coin', 1)
            ->select(
                DB::raw('DATE(created_at) as day'),
                DB::raw('SUM(coin) as total_coin'),
            )
            ->groupBy('day')
            ->get()
            ->toArray();
        $spend_coin_day = [];
        foreach ($dayRange as $day) {
            $totalCoinCV = 0;
            $totalSpendCoin = 0;

            foreach ($coin_cv_day as $item) {
                if (strtotime($item['day']) == strtotime($day)) {
                    $totalCoinCV = $item['total_coin'];
                }
            }

            foreach ($spend_coin_payment_day as $item) {
                if (strtotime($item['day']) == strtotime($day)) {
                    $totalSpendCoin = $item['total_coin'];
                }
            }

            $spend_coin_day[] = $totalCoinCV + $totalSpendCoin;
        }

        $this->v['spend_coin_day'] = $spend_coin_day;
        // month
        $spend_coin_payment_month = HistoryPayment::where('user_id', $company_id)
            ->where('type_account', 0)
            ->where('type_coin', 1)
            ->select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(coin) as total_coin'),
            )
            ->groupBy('month')
            ->get()
            ->toArray();
        $spend_coin_month = [];
        foreach ($monthRange as $month) {
            $totalCoinCV = 0;
            $totalSpendCoin = 0;

            foreach ($coin_cv_month as $item) {
                if ($item['month'] == $month['month']) {
                    $totalCoinCV = $item['total_coin'];
                }
            }

            foreach ($spend_coin_payment_month as $item) {
                if ($item['month'] == $month['month']) {
                    $totalSpendCoin = $item['total_coin'];
                }
            }

            $spend_coin_month[] = $totalCoinCV + $totalSpendCoin;
        }
        $this->v['spend_coin_month'] = $spend_coin_month;
        ////////////////////////////////
        $this->v['coin_payment'] = DB::table('history_payments')
            ->where('user_id', '=', $company_id)
            ->where('note', 'like', '%' . 'coin vào tài khoản' . '%')
            ->where('type_account', '=', 0)
            ->where('type_coin', '=', 0)
            ->sum('coin');

        $spend_coin_payment = DB::table('history_payments')
            ->where('user_id', '=', $company_id)
            ->where('type_account', '=', 0)
            ->where('type_coin', '=', 1)
            ->sum('coin');
        $spend_coin_profile = DB::table('profile_open')
            ->where('company_id', '=', $company_id)
            ->sum('coin');
        $this->v['spend_coin'] = $spend_coin_profile + $spend_coin_payment;
        // statistics apply 
        $this->v['count_all_apply'] = DB::table('job_post')
            ->join('job_post_apply', 'job_post.id', '=', 'job_post_apply.job_post_id')
            ->where('company_id', '=', $company_id)
            ->count('job_post_apply.id');
        $count_apply_by_day = [];
        $count_apply_by_month = [];
        $count_apply_day = JobPost::where('company_id', '=', $company_id)
            ->join('job_post_apply', 'job_post.id', '=', 'job_post_apply.job_post_id')
            ->where('company_id', '=', $company_id)
            ->select(
                DB::raw('DATE(job_post_apply.created_at) as day'),
                DB::raw('COUNT(job_post_apply.id) as count_apply'),
            )
            ->groupBy('day')
            ->get()
            ->toArray();
        foreach ($dayRange as $day) {
            $total = 0;
            foreach ($count_apply_day as $item) {
                if (strtotime($item['day']) == strtotime($day)) {
                    $total = $item['count_apply'];
                }
            }
            $count_apply_by_day[] = $total;
        }

        $count_apply_month = JobPost::where('company_id', '=', $company_id)
            ->join('job_post_apply', 'job_post.id', '=', 'job_post_apply.job_post_id')
            ->where('company_id', '=', $company_id)
            ->select(
                DB::raw('MONTH(job_post_apply.created_at) as month'),
                DB::raw('COUNT(job_post_apply.id) as count_apply'),
            )
            ->groupBy('month')
            ->get()
            ->toArray();
        foreach ($monthRange as $month) {
            $total = 0;
            foreach ($count_apply_month as $item) {
                if ($item['month'] == $month['month']) {
                    $total = $item['count_apply'];
                }
            }
            $count_apply_by_month[] = $total;
        }

        $this->v['count_apply_by_day'] = $count_apply_by_day;
        $this->v['count_apply_by_month'] = $count_apply_by_month;
        $this->v['count_view'] = JobPost::where('company_id', '=', $company_id)
        ->sum('view');
    $this->v['count_profile_new'] = JobPost::where('company_id', '=', $company_id)
        ->join('job_post_apply', 'job_post.id', '=', 'job_post_apply.job_post_id')
        ->where('job_post_apply.status', '=', 0)
        ->count('job_post_apply.id');
    $this->v['count_matching_qualifiers'] = JobPost::where('company_id', '=', $company_id)
        ->join('job_post_apply', 'job_post.id', '=', 'job_post_apply.job_post_id')
        ->where('job_post_apply.status', '=', 1)
        ->where('job_post_apply.qualifying_round_id', '=', 1)
        ->count('job_post_apply.id');
    $this->v['count_inappropriate_qualifiers'] = JobPost::where('company_id', '=', $company_id)
        ->join('job_post_apply', 'job_post.id', '=', 'job_post_apply.job_post_id')
        ->where('job_post_apply.status', '=', 1)
        ->where('job_post_apply.qualifying_round_id', '=', 0)
        ->count('job_post_apply.id');
        return  $this->v;
    }
}