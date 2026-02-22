<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\PredictionController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OtpController;

/*
|--------------------------------------------------------------------------
| Guest Routes (Hanya bisa diakses jika BELUM login)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'loginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/register', [AuthController::class, 'registerForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/verify-otp', function () {
        // Mengambil email dari session (biasanya dikirim setelah register)
        $email = session('otp_email'); 
        if (!$email) return redirect()->route('register');
        
        return view('auth.verify-otp', compact('email'));
    })->name('otp.view');
    
    // Proses Verifikasi Kode
    Route::post('/verify-otp', [OtpController::class, 'verify'])->name('otp.verify');
    
    // Proses Kirim Ulang (Resend) dengan pembatasan akses (3 kali per menit)
    Route::post('/resend-otp', [OtpController::class, 'sendOtp'])
        ->middleware('throttle:3,1') 
        ->name('otp.resend');
});
Route::get('/', function () {
    return view('welcome');
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes (HARUS LOGIN)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    // DASHBOARD
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    // TRANSACTIONS
    Route::prefix('transactions')->name('transactions.')->group(function () {
        Route::get('/', [TransactionController::class, 'index'])->name('index');
        Route::post('/', [TransactionController::class, 'store'])->name('store');
        Route::put('/{transaction}', [TransactionController::class, 'update'])->name('update');
        Route::delete('/{transaction}', [TransactionController::class, 'destroy'])->name('destroy');
    });

    // LAPORAN (REPORTS)
    Route::prefix('laporan')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/export/pdf', [ReportController::class, 'exportPdf'])->name('export.pdf');
        Route::get('/export/excel', [ReportController::class, 'exportExcel'])->name('export.excel');
    });

    // PREDIKSI
    Route::prefix('prediksi')->name('predictions.')->group(function () {
        Route::get('/', [PredictionController::class, 'index'])->name('index');
        Route::post('/generate', [PredictionController::class, 'generate'])->name('generate');
    });

    // AKUN (ACCOUNT)
    Route::prefix('akun')->name('account.')->group(function () {
        Route::get('/', [AccountController::class, 'index'])->name('index');
        Route::post('/profile', [AccountController::class, 'updateProfile'])->name('profile');
        Route::post('/password', [AccountController::class, 'updatePassword'])->name('password');
        Route::delete('/profile/photo', [AccountController::class, 'deletePhoto'])->name('profile.delete');
    });

    // LOGOUT
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});