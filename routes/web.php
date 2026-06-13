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
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\AdminLandingPageController;
use App\Http\Controllers\AdminAccountController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\CustomCategoryController;

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
    $settings = \App\Models\LandingSetting::pluck('value', 'key');
    $features = \App\Models\LandingFeature::all();
    return view('welcome', compact('settings', 'features'));
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
        Route::get('/create', [TransactionController::class, 'create'])->name('create');
        Route::get('/{id}/group-items', [TransactionController::class, 'getGroupItems'])->name('transactions.group-items');
        Route::post('/', [TransactionController::class, 'store'])->name('store');
        Route::put('/{transaction}', [TransactionController::class, 'update'])->name('update');
        Route::delete('/{transaction}', [TransactionController::class, 'destroy'])->name('destroy');
    });
    Route::resource('custom-categories', CustomCategoryController::class);

    Route::prefix('laporan')->name('reports.')->group(function () {
        Route::get('/income', [ReportController::class, 'incomeReport'])->name('income');
        Route::get('/expense', [ReportController::class, 'expenseReport'])->name('expense');
        Route::get('/export/income-pdf', [ReportController::class, 'exportIncomePdf'])->name('export.pdf'); 
        Route::get('/export/expense-pdf', [ReportController::class, 'exportExpensePdf'])->name('export.expense-pdf');
        Route::get('/export/income-excel', [ReportController::class, 'exportIncomeExcel'])->name('export.income-excel'); // Hasilnya: reports.export.income-excel
        Route::get('/export/expense-excel', [ReportController::class, 'exportExpenseExcel'])->name('export.expense-excel'); // Hasilnya: reports.export.expense-excel
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
    Route::resource('assets', AssetController::class)->middleware('auth');

    // LOGOUT
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

// 1. Rute Login Admin (Hanya bisa diakses jika belum login)
Route::middleware('guest')->group(function () {
    Route::get('/admin/login', [AuthController::class, 'adminLoginForm'])->name('admin.login');
    Route::post('/admin/login', [AuthController::class, 'adminLogin']);
});

// 2. Rute Dashboard & Kelola User (Hanya untuk Admin yang sudah login)
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    
    // Dashboard Admin
    Route::get('/dashboard', [DashboardController::class, 'adminIndex'])->name('dashboard');

    // Kelola User
    Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
    Route::post('/users/{user}/toggle', [AdminUserController::class, 'toggleStatus'])->name('users.toggle');
    Route::get('/landing-page', [AdminLandingPageController::class, 'edit'])->name('landing.edit');
    Route::post('/landing-page/hero', [AdminLandingPageController::class, 'updateHero'])->name('landing.hero.update');
    Route::post('/landing-page/feature', [AdminLandingPageController::class, 'storeFeature'])->name('landing.feature.store');
    Route::put('/landing-page/feature/{feature}', [AdminLandingPageController::class, 'updateFeature'])->name('landing.feature.update');
    Route::delete('/landing-page/feature/{feature}', [AdminLandingPageController::class, 'destroyFeature'])->name('landing.feature.destroy');

    Route::get('/akun', [AdminAccountController::class, 'index'])->name('account.index');
    Route::post('/akun/profile', [AdminAccountController::class, 'updateProfile'])->name('account.profile.update');
    Route::post('/akun/password', [AdminAccountController::class, 'updatePassword'])->name('account.password.update');
    Route::post('/logout-admin', [AuthController::class, 'adminLogout'])->name('logout');
});