<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\OtpCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // ===== FORM =====
    public function loginForm() { return view('auth.login'); }
    public function registerForm() { return view('auth.register'); }
    
    // Form Login Khusus Admin
    public function adminLoginForm() { return view('auth.admin-login'); }

// ===== REGISTER =====
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'business_name' => 'required|string|max:150',
            'business_type' => 'required|in:Mikro,Kecil,Menengah', // Validasi Dropdown
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed|min:6',
        ]);

        $user = User::create([
            'name' => $request->name,
            'business_name' => $request->business_name,
            'business_type' => $request->business_type, // Simpan tipe usaha
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'user', 
        ]);

        session(['otp_email' => $user->email]);
        app(OtpController::class)->sendOtp($request);

        return redirect()->route('otp.view')->with('success', 'Registrasi berhasil, silakan cek email untuk kode OTP.');
    }

    // ===== LOGIN USER BIASA =====
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
    
        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            // 1. CEK: Jika Admin mencoba login di sini, tendang balik
            if ($user->role === 'admin') {
                Auth::logout();
                return back()->withErrors(['email' => 'Admin harus login melalui portal khusus admin.']);
            }

            // 2. CEK: Jika akun dinonaktifkan
            if (!$user->is_active) {
                Auth::logout();
                return back()->withErrors(['email' => 'Akun Anda telah dinonaktifkan oleh admin.']);
            }

            // 3. CEK: Verifikasi OTP
            if ($user->email_verified_at == null) {
                $email = $user->email;
                Auth::logout();
                session(['otp_email' => $email]);
                return redirect()->route('otp.view')->with('info', 'Silakan verifikasi akun Anda.');
            }

            $request->session()->regenerate();
            return redirect()->route('dashboard');
        }
    
        return back()->withErrors(['email' => 'Email atau password salah']);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }

    // ===== LOGIN KHUSUS ADMIN =====
    public function adminLogin(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            // CEK: Harus Role Admin
            if ($user->role !== 'admin') {
                Auth::logout();
                return back()->withErrors(['email' => 'Anda tidak memiliki hak akses admin.']);
            }

            $request->session()->regenerate();
            return redirect()->route('admin.dashboard'); // Arahkan ke dashboard admin
        }

        return back()->withErrors(['email' => 'Email atau Sandi Anda salah']);
    }

    // ===== LOGOUT =====


    // app/Http/Controllers/AuthController.php

    public function adminLogout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Di sini kuncinya, diarahkan ke admin/login
        return redirect('/admin/login');
    }
}