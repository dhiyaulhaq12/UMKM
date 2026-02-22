<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OtpCode;
use App\Models\User;
use Illuminate\Support\Facades\Auth; // Tambahkan ini
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;

class OtpController extends Controller
{
    public function sendOtp(Request $request)
    {
        $otp = rand(100000, 999999);
        
        OtpCode::updateOrCreate(
            ['email' => $request->email],
            [
                'otp' => $otp,
                'expires_at' => now()->addMinutes(5),
                'is_used' => false
            ]
        );

        Mail::to($request->email)->send(new \App\Mail\OtpMail($otp));

        return response()->json(['status' => 'success', 'message' => 'OTP berhasil dikirim']);
    }

    // ===== VERIFY (Skema A: Verifikasi -> Login -> Dashboard/Transaksi) =====
    public function verify(Request $request)
    {
        $otpCode = implode('', $request->otp);

        $check = OtpCode::where('email', $request->email)
            ->where('otp', $otpCode)
            ->where('is_used', false)
            ->where('expires_at', '>', now())
            ->first();

        if (!$check) {
            return back()->withErrors(['otp' => 'Kode OTP salah atau sudah kedaluwarsa.']);
        }

        // 1. Tandai OTP sudah digunakan
        $check->update(['is_used' => true]);

        // 2. Tandai User sudah terverifikasi
        $user = User::where('email', $request->email)->first();
        $user->update(['email_verified_at' => now()]);

        // 3. Otomatis Login (Skema A)
        Auth::login($user);
        $request->session()->regenerate();

        // 4. Hapus session email OTP
        session()->forget('otp_email');

        return redirect()->route('dashboard')->with('success', 'Verifikasi berhasil! Selamat datang.');
    }
}