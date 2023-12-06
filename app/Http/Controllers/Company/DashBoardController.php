<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\JobPost;
use App\Models\JobPostApply;
use App\Models\ProfileOpen;
use App\Models\Vnpay_payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        $company_id = auth('company')->user()->id;
        $this->v['JobPost'] = JobPost::with('activities')->where('company_id', $company_id)->get();
        $this->v['Applied'] = 0;
        foreach ($this->v['JobPost'] as $post) {
            $this->v['Applied'] += $post->activities()->count();
        }
        $this->v['countNotSuitable'] = JobPostApply::where('qualifying_round_id', 0)->get()->toArray();
        $this->v['countSuitable'] = JobPostApply::where('qualifying_round_id', 1)->get()->toArray();
        $this->v['countView'] = JobPost::where('id', "=", $company_id)->select('view')->first();
        $this->v['countAddCoin'] = Company::where('id', "=", $company_id)->select('coin')->first();
        $this->v['countSpendCoin'] = ProfileOpen::where('id', "=", $company_id)->select('coin')->first();
        $getModel = JobPostApply::getCadidate($request, $company_id);
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
