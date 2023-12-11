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
        $data = [];
        for ($i = 11; $i >= 0; $i--) {
            $month = Carbon::today()->startOfMonth()->subMonth($i);
            $year = Carbon::today()->startOfMonth()->subMonth($i)->format('Y');
            array_push($monthRange, array(
                'month' => $month->month,
                'year' => $year
            ));
        }
        $totalMoney = HistoryPayment::select(DB::raw('sum(coin) as totalMoney'), DB::raw('MONTH(created_at) as month'))
            ->groupBy('month')
            ->get()->toArray();
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
        $data = [
            'time' => $months,
            'money' => $staticMoneyFollowMonth
        ];
        return $data;
    }
}