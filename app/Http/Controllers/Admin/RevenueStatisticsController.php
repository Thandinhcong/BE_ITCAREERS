<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Candidate;
use App\Models\Company;
use App\Models\JobPost;
use App\Models\Major;
use App\Models\Profile;
use App\Models\Skill;
use App\Models\HistoryPayment;
use App\Models\User;
use App\Models\Vnpay_payment;
use Illuminate\Http\Request;
use Illuminate\Queue\Jobs\Job;

class RevenueStatisticsController extends Controller
{
    private $v;
    public function __construct()
    {
        $this->v = [];
    }
    public function index()
    {
        $this->v['countCandidate'] = Candidate::all();
        $this->v['countCompany'] = Company::all();
        $this->v['countCV'] = Profile::all();
        $this->v['countSkill'] = Skill::all();
        $this->v['countUser'] = User::all();
        $this->v['countMajor'] = Major::all();
        $this->v['countJobPost'] = JobPost::all();
        $this->v['countPendingImagePaper'] = Company::where('status', 0)->get()->toArray();
        $this->v['countActiveImagePaper'] = Company::where('status', 1)->get()->toArray();
        $this->v['countBlockImagePaper'] = Company::where('status', 2)->get()->toArray();
        $totalMoney = HistoryPayment::getMoneyMonthly();
        $this->v['months'] = $totalMoney['time'];
        $this->v['totalMoneyMonth'] =  $totalMoney['money'];
        $this->v['data'] = $totalMoney;

        return $this->v;
    }
}
