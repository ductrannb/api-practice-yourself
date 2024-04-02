<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OtpMail extends Mailable
{
    use Queueable, SerializesModels;

    private $otp;
    private $name;
    private $message;
    public function __construct($name, $otp, $message = '')
    {
        $this->name = $name;
        $this->otp = $otp;
        $this->message = $message;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->otp . ' là mã OTP của bạn.',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mails.otp',
            with: [
                'name' => $this->name,
                'otp' => $this->otp,
                'text' => $this->message
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
