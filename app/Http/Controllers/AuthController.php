<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\OtpCode; // Tambahkan ini
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // ===== FORM =====
    public function loginForm() { return view('auth.login'); }
    public function registerForm() { return view('auth.register'); }

    // ===== REGISTER (Update Redirect & Session) =====
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'business_name' => 'required|string|max:150',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed|min:6',
        ]);

        $user = User::create([
            'name' => $request->name,
            'business_name' => $request->business_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Simpan email ke session untuk verifikasi OTP
        session(['otp_email' => $user->email]);

        // Kirim OTP Pertama kali
        app(OtpController::class)->sendOtp($request);

        // Redirect ke halaman verifikasi OTP
        return redirect()->route('otp.view')->with('success', 'Registrasi berhasil, silakan cek email untuk kode OTP.');
    }

    // ===== LOGIN (Update Pengecekan Verifikasi) =====
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
    
        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            // Cek jika user belum memverifikasi email (Skema keamanan)
            if ($user->email_verified_at == null) {
                $email = $user->email;
                Auth::logout(); // Logoutkan dulu
                session(['otp_email' => $email]);
                
                return redirect()->route('otp.view')->with('info', 'Silakan verifikasi akun Anda terlebih dahulu.');
            }

            $request->session()->regenerate();
            return redirect()->route('dashboard');
        }
    
        return back()->withErrors([
            'email' => 'Email atau password salah',
        ]);
    }

    // ===== LOGOUT =====
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}