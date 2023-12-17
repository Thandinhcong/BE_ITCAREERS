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

        // Hiển thị dữ liệu hoàn chỉnh
        // dd($this->v['count_apply_post']);



        $this->v['coin_post_vip'] = DB::table('history_payments')
            ->where('user_id', '=', $company_id)
            ->where('type_account', '=', 0)
            ->where('type_coin', '=', 1)
            ->where('note', 'LIKE', '%vip%')
            ->sum('coin');
        $this->v['coin_post_normal'] = DB::table('history_payments')
            ->where('user_id', '=', $company_id)
            ->where('type_account', '=', 0)
            ->where('type_coin', '=', 1)
            ->where('note', 'LIKE', '%thường%')
            ->sum('coin');
        $this->v['coin_profile_open'] = DB::table('profile_open')
            ->where('company_id', '=', $company_id)
            ->sum('coin');

        $today = Carbon::now();
        $oneMonthAgo = $today->copy()->subMonth();
        $oneYearAgo = $today->copy()->subYear();
        $sevenDaysAgo = $today->copy()->subDays(7);

        $profile_history_last_7_days = DB::table('profile_open')
            ->where('company_id', $company_id)
            ->whereBetween('created_at', [$sevenDaysAgo, $today])
            ->groupBy('date')
            ->selectRaw('DATE(created_at) as date, SUM(coin) as total_coin')
            ->get()
            ->toArray();
        $spend_coin_last_7_days = DB::table('history_payments')
            ->where('user_id', '=', $company_id)
            ->where('type_account', '=', 0)
            ->where('type_coin', '=', 1)
            ->whereBetween('created_at', [$sevenDaysAgo, $today])
            ->groupBy('date')
            ->selectRaw('DATE(created_at) as date, SUM(coin) as total_coin')
            ->get()
            ->toArray();
        $mergedData = [];

        foreach ($profile_history_last_7_days as $item) {
            $mergedData[$item->date]['profile_total_coin'] = $item->total_coin;
        }

        foreach ($spend_coin_last_7_days as $item) {
            if (!isset($mergedData[$item->date])) {
                $mergedData[$item->date] = [];
            }
            $mergedData[$item->date]['spend_total_coin'] = $item->total_coin;
        }
        $totalCoinByDate = [];
        for ($i = 0; $i < 7; $i++) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $profileTotalCoin = isset($mergedData[$date]['profile_total_coin']) ? $mergedData[$date]['profile_total_coin'] : 0;
            $spendTotalCoin = isset($mergedData[$date]['spend_total_coin']) ? $mergedData[$date]['spend_total_coin'] : 0;
            $totalCoinByDate[$date] = $profileTotalCoin + $spendTotalCoin;
        }

        $this->v['spend_coin_last_7_days'] = $totalCoinByDate;
        // month
        // Câu truy vấn lịch sử mở profile theo tháng
        $profile_history_by_month = DB::table('profile_open')
            ->where('company_id', $company_id)
            ->groupBy(DB::raw('MONTH(created_at)'))
            ->selectRaw('MONTH(created_at) as month, SUM(coin) as total_coin')
            ->get()
            ->toArray();

        // Câu truy vấn lịch sử thanh toán theo tháng
        $spend_coin_by_month = DB::table('history_payments')
            ->where('user_id', '=', $company_id)
            ->where('type_account', '=', 0)
            ->where('type_coin', '=', 1)
            ->groupBy(DB::raw('MONTH(created_at)'))
            ->selectRaw('MONTH(created_at) as month, SUM(coin) as total_coin')
            ->get()
            ->toArray();

        $mergedData = [];

        foreach ($profile_history_by_month as $item) {
            $mergedData[$item->month]['profile_total_coin'] = $item->total_coin;
        }

        foreach ($spend_coin_by_month as $item) {
            if (!isset($mergedData[$item->month])) {
                $mergedData[$item->month] = [];
            }
            $mergedData[$item->month]['spend_total_coin'] = $item->total_coin;
        }

        $totalCoinByMonth = [];
        for ($i = 1; $i <= 12; $i++) {
            $profileTotalCoin = isset($mergedData[$i]['profile_total_coin']) ? $mergedData[$i]['profile_total_coin'] : 0;
            $spendTotalCoin = isset($mergedData[$i]['spend_total_coin']) ? $mergedData[$i]['spend_total_coin'] : 0;
            $totalCoinByMonth[$i] = $profileTotalCoin + $spendTotalCoin;
        }
        $this->v['spend_coin_by_month'] = $totalCoinByMonth;

        // Câu truy vấn lịch sử mở profile theo năm
        $profile_history_by_year = DB::table('profile_open')
            ->where('company_id', $company_id)
            ->whereBetween('created_at', [$oneYearAgo, $today])
            ->groupBy(DB::raw('YEAR(created_at)'))
            ->selectRaw('YEAR(created_at) as year, SUM(coin) as total_coin')
            ->get()
            ->toArray();

        // Câu truy vấn lịch sử thanh toán theo năm
        $spend_coin_by_year = DB::table('history_payments')
            ->where('user_id', '=', $company_id)
            ->where('type_account', '=', 0)
            ->where('type_coin', '=', 1)
            ->whereBetween('created_at', [$oneYearAgo, $today])
            ->groupBy(DB::raw('YEAR(created_at)'))
            ->selectRaw('YEAR(created_at) as year, SUM(coin) as total_coin')
            ->get()
            ->toArray();

        $mergedData = [];

        foreach ($profile_history_by_year as $item) {
            $mergedData[$item->year]['profile_total_coin'] = $item->total_coin;
        }

        foreach ($spend_coin_by_year as $item) {
            if (!isset($mergedData[$item->year])) {
                $mergedData[$item->year] = [];
            }
            $mergedData[$item->year]['spend_total_coin'] = $item->total_coin;
        }
        $totalCoinByYear = [];

        for ($i = date('Y'); $i >= date('Y') - 1; $i--) {
            $profileTotalCoin = isset($mergedData[$i]['profile_total_coin']) ? $mergedData[$i]['profile_total_coin'] : 0;
            $spendTotalCoin = isset($mergedData[$i]['spend_total_coin']) ? $mergedData[$i]['spend_total_coin'] : 0;
            $totalCoinByYear[$i] = $profileTotalCoin + $spendTotalCoin;
        }

        $this->v['spend_coin_by_year'] = $totalCoinByYear;
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
        return response()->json([
            'status' => true,
            $this->v
        ], 200);
    }
}
