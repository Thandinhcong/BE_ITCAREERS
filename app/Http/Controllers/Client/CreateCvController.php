<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Exp;
use App\Models\Major;
use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CreateCvController extends Controller
{
    private $data;
    public function __construct()
    {
        $this->data = [];
    }
    /**
     * Display a listing of the resource.
     */
    public function getData()
    {
        $this->data['major'] = Major::all();
        $this->data['exp'] = Exp::all();
        if ($this->data['major']) {
            return response()->json([
                $this->data['major'],
            ], 200);
        } else {
            return response()->json([
                $this->data['major'],
            ], 404);
        }
    }
    public function index(Request $request)
    {
        $validator_edu = Validator::make($request->all(), [
            'name' => 'required|string|max:55',
            'gpa' => 'required|double',
            'type_degree' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
        ]);

        $validator_exp = Validator::make($request->all(), [
            'company_name' => 'required|string',
            'postion' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
        ]);
        $validator_project = Validator::make($request->all(), [
            'company_name' => 'required|string',
            'postion' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'desc' => 'required|string',
            'phone_instructor' => 'required',
            'email_instructor' => 'required|email',
        ]);
        return response()->json([
            'profile' => Profile::all(),
        ]);
    }

    public function saveInfo(Request $request)
    {
        $validator_info = Validator::make($request->all(), [
            // 'name' => 'required',
            // 'email' => 'required',
            // 'phone' => 'required',
            'image' => 'required',
            // 'candidate_id' => 'required',
            'career_goal' => 'required|string',
            'address' => 'required|string',
        ]);
        // if (Auth::guard('candidate')->check()) {
        // if (Auth::check()) {
        if ($validator_info->fails()) {
            return response()->json([
                'error' => $validator_info->errors()
            ], 404);
        }
        $candidate = Auth::guard('candidate')->user();
        $candidate_id = $candidate->id;
        $cv = new Profile();
        $cv->candidate_id = $candidate_id;
        $cv->name = $candidate->name;
        $cv->email = $candidate->email;
        $cv->phone = $candidate->phone;
        $cv->career_goal = $request->career_goal;
        $cv->address = $request->address;
        $cv->image = $request->image;
        $cv->save();
        // }
        // return response()->json([
        //     'status' => false,
        //     'message' => 'Chưa đăng nhập'
        // ], 401);
    }
}
