<?php

namespace App\Http\Controllers\Mail;

use App\Http\Controllers\Controller;
use App\Models\Profile;
use Illuminate\Bus\Queueable;
use Illuminate\Http\Request;
use Illuminate\Queue\SerializesModels;

class SendMail extends Controller
{
    use Queueable, SerializesModels;
    public $subject;
    public $company_name;
    public $post_name;
    public function __construct($subject, $company_name, $post_name)
    {
        $this->subject = $subject;
        $this->company_name = $company_name;
        $this->post_name = $post_name;
    }
    public function build(Request $request)
    {
        $user = auth('candidate')->user()->id;
        $name = auth('candidate')->user()->name;
        $profile = Profile::where('candidate_id', $user)->where('is_active', 1)->first();
        $company_name = $this->company_name;
        $post_name = $this->post_name;
        return $this->subject('BEWork')
            ->view('email.email', compact('name', 'company_name', 'profile', 'post_name'));
    }
}
