<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Mail\SendEmail;
use App\Models\ManagementWeb;
use Mail;

class SendEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $data;
    protected $subject;
    protected $view;
    /**
     * Create a new job instance.
     */
    public function __construct($data,$subject,$view)
    {
        $this->data = $data;
        $this->subject = $subject;
        $this->view = $view;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $data = $this->data;
        Mail::send($this->view, compact('data'), function ($email) use ($data) {
            $email->subject($this->subject);
            $email->to($data['email']);
        });
    }
}
