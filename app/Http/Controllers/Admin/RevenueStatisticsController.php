<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Vnpay_payment;
use Illuminate\Http\Request;

class RevenueStatisticsController extends Controller
{
    private $v;
    public function __construct()
    {
        $this->v = [];
    }
    public function index()
    {
        $totalMoney = Vnpay_payment::getMoneyMonthly();
        $this->v['months'] = $totalMoney['time'];
        $this->v['totalMoneyMonth'] =  $totalMoney['money'];
        return $this->v;
    }
}
