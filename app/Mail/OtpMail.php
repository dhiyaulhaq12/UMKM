<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public $otp;

    public function __construct($otp)
    {
        $this->otp = $otp;
    }

    public function build()
    {
        return $this->subject('Kode OTP Verifikasi Akun')
                    ->html("<h1>Kode OTP Anda adalah: <b>{$this->otp}</b></h1><p>Kode ini berlaku selama 5 menit.</p>");
    }
}