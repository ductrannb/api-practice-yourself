<?php

namespace App\Jobs;

use App\Mail\OtpMail;
use App\Models\Otp;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendMailForgetPassword implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $user;
    private $otp;
    public function __construct($user, $otp)
    {
        $this->user = $user;
        $this->otp = $otp;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Mail::to($this->user)->send(new OtpMail(
            $this->user->name,
            $this->otp,
            'Bạn đang yêu cầu đặt lại mật khẩu từ hệ thống ' . env('APP_NAME') . '.'
        ));

        Otp::create([
            'email' => $this->user->email,
            'code' => $this->otp,
            'expired_at' => Carbon::now()->addMinutes(5)
        ]);
    }
}
