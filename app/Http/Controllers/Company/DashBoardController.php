<?php

namespace App\Http\Controllers\Company;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\JobPost;
use App\Models\JobPostApply;
use App\Models\ProfileOpen;
use App\Models\HistoryPayment;
use App\Models\Vnpay_payment;
use Illuminate\Http\Request;

class DashBoardController extends Controller
{
    private $v;
    public function __construct()
    {
        $this->v = [];
        $this->v['activeRoute'] = 'dashboard';
    }
    public function index(Request $request)
    {
        $this->v['title'] = "Tổng quan";
        $company_id = Auth::user()->id;
        // dd($company_id);
        $this->v['JobPost'] = JobPost::with('activities')->where('company_id', $company_id)->get();
        $this->v['Applied'] = 0;
        foreach ($this->v['JobPost'] as $post) {
            $this->v['Applied'] += $post->activities()->count();
        }
        $this->v['countNotSuitable'] = JobPostApply::where('qualifying_round_id', 0)->get()->toArray();
        $this->v['countSuitable'] = JobPostApply::where('qualifying_round_id', 1)->get()->toArray();
        $this->v['countView'] = JobPost::where('id', "=", $company_id)->select('view')->get();
        $this->v['countAddCoin'] = HistoryPayment::
        where('user_id', "=", $company_id)
       -> where('type_coin', "=", 0)
        ->select('coin')
        ->get();
        $this->v['finalAddCoin']=0;
        foreach ($this->v['countAddCoin'] as $key => $value) {
            $this->v['finalAddCoin']+=$value->coin;
        }
        $this->v['countSpendCoin'] = ProfileOpen::where('id', "=", $company_id)
        ->select('coin')
        ->get();
        $this->v['finalSpendCoin']=0;

        foreach ($this->v['countSpendCoin'] as $key => $value) {
            $this->v['finalSpendCoin']+=$value->coin;
        }
        $getModel = JobPostApply::getCandidate($request, $company_id);
        $this->v['totalApplied'] = array_column($getModel, 'total');
        $this->v['arrayDate'] = array_column($getModel, 'date');
        if ($request->ajax()) {
            $data = [
                'totalApplied' => $this->v['totalApplied'],
                'arrayDate' => $this->v['arrayDate'],
            ];
            return response()->json($data);
        }
        return $this->v;
    }
}