<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OtpVerificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $otp;
    public string $userName;
    public string $purpose;

    public function __construct(string $otp, string $userName, string $purpose = 'register')
    {
        $this->otp      = $otp;
        $this->userName = $userName;
        $this->purpose  = $purpose;
    }

    public function envelope(): Envelope
    {
        $subject = $this->purpose === 'change_email'
            ? '[E-Arsip DLH] Kode Verifikasi Perubahan Email Akun'
            : '[E-Arsip DLH] Kode Verifikasi Email Anda';

        return new Envelope(
            subject: $subject,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.otp-verification',
            with: [
                'otp'      => $this->otp,
                'userName' => $this->userName,
                'purpose'  => $this->purpose,
            ],
        );
    }
}

