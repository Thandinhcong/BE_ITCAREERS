<?php

namespace App\Http\Controllers\Candidate\Auth;

use App\Http\Controllers\Controller;
use App\Jobs\SendEmailJob;
use App\Models\Candidate;
use App\Models\ManagementWeb;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Mail;

class RegisterCandidateController extends Controller
{
    public function register(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|string|email|unique:candidates',
            'password' => 'required|string|confirmed',
            'phone' => 'required|string|unique:candidates',

        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'fails',
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors()->toArray(),
            ]);
        }
        $candidate = new Candidate([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => bcrypt($request->input('password')),
            'phone' => $request->input('phone'),
            'remember_token'=>strtoupper(Str::random(10))
        ]);
        $candidate->save();
        $manage_web = ManagementWeb::find(1);
        $data = [];
        $data['email'] = $candidate->email;
        $data['name'] = $candidate->name;
        $data['remeber_token'] = $candidate->remember_token;
        $data['id'] = $candidate->id;
        $data['name_web'] = $manage_web->name_web;
        $data['logo'] =  $manage_web->logo;
         dispatch(new SendEmailJob(
                    $data,
                    $manage_web->name_web . ' - Xác nhận tài khoản',
                    'emails.active-acc'
                ));
        // Mail::send('emails.active-acc', compact('data'), function ($email) use ($data) {
        //     $email->subject('UbWork - Xác nhận tài khoản');
        //     $email->to($data['email'], $data['name']);
        // });
        return response()->json([
            'status' => 'success',
        ], 200);
    }
    public function activeCandidate(Candidate $candidate,$token)
    {
        if ($candidate->remember_token === $token) {
            $candidate->update([
                
                'email_verified_at' => Carbon::now(),
                'remember_token' => null
            ]);
            return redirect("http://localhost:5173/login");
        } elseif ($candidate->remember_token == null) {
            // return view('email.404');
        } 
    }

}