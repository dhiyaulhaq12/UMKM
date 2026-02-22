<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Cloudinary\Api\Upload\UploadApi;

class AccountController extends Controller
{
    public function index()
    {
        return view('account.index', [
            'user' => Auth::user()
        ]);
    }

    /* ===============================
       UPDATE PROFIL
    =============================== */
    public function updateProfile(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'business_name' => 'required|string|max:255',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $user = Auth::user();

        if ($request->hasFile('photo')) {
            $uploaded = (new UploadApi())->upload(
                $request->file('photo')->getRealPath(),
                ['folder' => 'profiles']
            );

            $user->photo = $uploaded['secure_url'];
        }

        $user->name = $request->name;
        $user->business_name = $request->business_name;
        $user->save();

        return back()->with('success', 'Profil berhasil diperbarui');
    }

    /* ===============================
       UPDATE PASSWORD
    =============================== */
    public function updatePassword(Request $request)
{
    $request->validate([
        'old_password' => 'required',
        'password'     => 'required|min:6|confirmed',
    ], [
        // Pesan kustom dalam Bahasa Indonesia
        'password.required'  => 'Kata sandi baru wajib diisi.',
        'password.min'       => 'Kata sandi baru minimal harus 6 karakter.',
        'password.confirmed' => 'Konfirmasi kata sandi tidak cocok dengan kata sandi baru.',
    ]);

    $user = Auth::user();

    // Cek apakah password lama benar
    if (!Hash::check($request->old_password, $user->password)) {
        return back()->with('error', 'Kata sandi lama yang Anda masukkan salah.');
    }

    $user->password = Hash::make($request->password);
    $user->save();

    return back()->with('success', 'Kata sandi Anda berhasil diperbarui.');
}

/* ===============================
    HAPUS FOTO PROFIL
=============================== */
public function deletePhoto()
{
    // Mengambil user yang sedang login
    $user = \App\Models\User::find(\Illuminate\Support\Facades\Auth::id());

    if ($user) {
        // Update kolom foto menjadi NULL
        $user->photo = null;
        $user->save();

        return back()->with('success', 'Foto profil berhasil dihapus secara permanen');
    }

    return back()->with('error', 'User tidak ditemukan');
}
}
