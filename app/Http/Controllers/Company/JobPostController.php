<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Exp;
use App\Models\JobPosition;
use App\Models\Major;
use App\Models\Skill;
use App\Models\WorkingForm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class JobPostController extends Controller
{
    protected $data=[];
    
    public function index()
    {
        $this->data['job_position'] = JobPosition::all();
        $this->data['exp'] = Exp::all();
        $this->data['skill'] = Skill::all();
        $this->data['major'] = Major::all();
        $this->data['working_form'] = WorkingForm::all();
        $this->data['job_position'] = JobPosition::all();
        // $this->data['area'] = Area::all();
        if ($this->data['exp']->count() > 0) {
            return response()->json([
                'status' => 200,
                'major' => $this->data
            ], 200);
        } else {
            return response()->json([
                'status' => 404,
                'error' => 'không có bản ghi nào '
            ], 404);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|',
            'job_position_id'=>'required',
            'exp_id'=>'required',
            'skill_id'=>'required',
            'require'=>'required',
            'interest'=>'required',
            'level_id'=>'required',
            'working_form_id'=>'required',
            'academic_level_id'=>'required',
            'major_id'=>'required',
            'start_date'=>'required',
            'end_date'=>'required',
            'ranks_id'=>'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'fail',
                'errors' => $validator->messages()
            ], 400);
        } else {
            $job_position = JobPosition::create($request->all());
        }
        if ($job_position) {
            return response()->json([
                'status' => 'success',
                'message' => 'Add new success',
            ], 200);
        } else {
            return response()->json([
                'status' => 'fail',
                'message' => 'error'
            ], 500);
        }
    }
    

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
