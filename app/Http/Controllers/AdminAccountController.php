<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Cloudinary\Api\Upload\UploadApi;

class AdminAccountController extends Controller
{
    public function index()
    {
        return view('admin.account.index', [
            'user' => Auth::user()
        ]);
    }

    /* ===============================
       UPDATE PROFIL ADMIN (CLOUDINARY)
    =============================== */
    public function updateProfile(Request $request)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $user = Auth::user();

        if ($request->hasFile('photo')) {
            // Upload ke Cloudinary folder admin
            $uploaded = (new UploadApi())->upload(
                $request->file('photo')->getRealPath(),
                ['folder' => 'admin_profiles']
            );

            $user->photo = $uploaded['secure_url'];
        }

        $user->name = $request->name;
        $user->save();

        return back()->with('success', 'Profil admin berhasil diperbarui');
    }

    /* ===============================
       UPDATE PASSWORD ADMIN
    =============================== */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'old_password' => 'required',
            'password'     => 'required|min:6|confirmed',
        ], [
            'password.min'       => 'Kata sandi baru minimal harus 6 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->old_password, $user->password)) {
            return back()->with('error', 'Kata sandi lama salah.');
        }

        $user->password = Hash::make($request->password);
        $user->save();

        return back()->with('success', 'Kata sandi berhasil diperbarui.');
    }
}