<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Cloudinary\Api\Upload\UploadApi;

class AssetController extends Controller
{
    // 1. Fungsi untuk menampilkan daftar aset
    public function index()
    {
        // Ambil aset hanya milik user yang sedang login
        $assets = Asset::where('user_id', Auth::id())
                        ->orderBy('created_at', 'desc')
                        ->get();

        return view('assets.index', compact('assets'));
    }

    // 2. Fungsi simpan (yang tadi kita bahas)
    public function store(Request $request)
    {
        // Bersihkan input value dari titik (format rupiah) sebelum validasi
        if ($request->has('value')) {
            $cleanValue = preg_replace('/[^0-9]/', '', $request->value);
            $request->merge(['value' => $cleanValue]);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'value' => 'required|numeric|min:0',
            'purchase_date' => 'required|date',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $imageUrl = null;

        if ($request->hasFile('image')) {
            try {
                $file = $request->file('image');
                $uploaded = (new UploadApi())->upload($file->getRealPath(), [
                    'folder' => 'assets',
                ]);
                $imageUrl = $uploaded['secure_url'];
            } catch (\Exception $e) {
                return back()->withErrors(['image' => 'Gagal upload ke Cloudinary.']);
            }
        }

        Asset::create([
            'user_id' => Auth::id(),
            'name' => $request->name,
            'value' => $request->value,
            'purchase_date' => $request->purchase_date,
            'image' => $imageUrl,
        ]);

        return back()->with('success', 'Aset berhasil dicatat!');
    }


    public function update(Request $request, Asset $asset)
{
    // Pastikan milik user
    if ($asset->user_id !== Auth::id()) abort(403);

    $request->validate([
        'name' => 'required|string|max:255',
        'value' => 'required|numeric|min:0',
        'purchase_date' => 'required|date',
        'image' => 'nullable|image|max:2048',
    ]);

    $imageUrl = $asset->image;

    if ($request->hasFile('image')) {
        $file = $request->file('image');
        $uploaded = (new UploadApi())->upload($file->getRealPath(), [
            'folder' => 'assets',
        ]);
        $imageUrl = $uploaded['secure_url'];
    }

    $asset->update([
        'name' => $request->name,
        'value' => $request->value,
        'purchase_date' => $request->purchase_date,
        'image' => $imageUrl,
    ]);

    return back()->with('success', 'Aset berhasil diperbarui');
}

    // 3. Fungsi hapus aset
    public function destroy(Asset $asset)
    {
        // Pastikan hanya pemilik yang bisa hapus
        if ($asset->user_id !== Auth::id()) {
            abort(403);
        }

        $asset->delete();
        return back()->with('success', 'Aset berhasil dihapus.');
    }
}