<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Vnpay_payment extends Model
{
    use HasFactory;
    protected $table = 'vnpay_payment';
    protected $fillable = [
        'id',
        'vnp_Amount',
        'vnp_BankCode',
        'vnp_BankTranNo',
        'vnp_CardType',
        'vnp_OrderInfo',
        'vnp_ResponseCode',
        'vnp_PayDate',
        'vnp_TmnCode',
        'vnp_TransactionNo',
        'vnp_TransactionStatus',
        'vnp_TxnRef',
        'vnp_SecureHash',
        'created_at',
        'updated_at'
    ];

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
        $totalMoney = Vnpay_payment::where('vnp_ResponseCode', '00')
            ->select(DB::raw('sum(vnp_Amount) as totalMoney'), DB::raw('MONTH(created_at) as month'))
            ->groupBy('month')
            ->get()->toArray();
        $staticMoneyFollowMonth = [];
        foreach ($monthRange as $month) {
            $total = 0;
            foreach ($totalMoney as $key => $value) {
                if ($value['month'] == $month['month']) {
                    $total = $value['totalMoney']/100;
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