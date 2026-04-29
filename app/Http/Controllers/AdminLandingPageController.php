<?php

namespace App\Http\Controllers;

use App\Models\LandingSetting;
use App\Models\LandingFeature;
use Illuminate\Http\Request;
use Cloudinary\Api\Upload\UploadApi;

class AdminLandingPageController extends Controller
{
    public function edit()
    {
        $settings = LandingSetting::pluck('value', 'key');
        $features = LandingFeature::all();
        return view('admin.landing.edit', compact('settings', 'features'));
    }

    /* =========================================
       UPDATE HERO (TEXT ONLY)
    ========================================= */
    public function updateHero(Request $request)
    {
        $data = $request->only(['hero_title', 'hero_desc']);
        foreach ($data as $key => $value) {
            LandingSetting::updateOrCreate(['key' => $key], ['value' => $value]);
        }
        return back()->with('success', 'Hero section diperbarui!');
    }

    /* =========================================
       SIMPAN FITUR DENGAN CLOUDINARY
    ========================================= */
    public function storeFeature(Request $request)
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'required',
            'icon'        => 'required|image|mimes:png,jpg,jpeg,svg|max:2048',
        ]);

        // Proses Upload ke Cloudinary
        if ($request->hasFile('icon')) {
            $uploaded = (new UploadApi())->upload(
                $request->file('icon')->getRealPath(),
                [
                    'folder' => 'landing_features', // Disimpan di folder khusus fitur
                    'resource_type' => 'auto'       // Mendukung SVG jika diperlukan
                ]
            );

            // Simpan secure_url dari Cloudinary ke database
            LandingFeature::create([
                'title'       => $request->title,
                'description' => $request->description,
                'icon'        => $uploaded['secure_url'], 
            ]);

            return back()->with('success', 'Fitur berhasil ditambah dan diupload ke Cloudinary!');
        }

        return back()->with('error', 'Gagal mengupload ikon');
    }

    /* =========================================
   UPDATE FITUR (DENGAN CLOUDINARY)
========================================= */
    public function updateFeature(Request $request, LandingFeature $feature)
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'required',
            'icon'        => 'nullable|image|mimes:png,jpg,jpeg,svg|max:2048',
        ]);

        $data = [
            'title'       => $request->title,
            'description' => $request->description,
        ];

        // Jika admin mengupload ikon baru
        if ($request->hasFile('icon')) {
            $uploaded = (new UploadApi())->upload(
                $request->file('icon')->getRealPath(),
                [
                    'folder' => 'landing_features',
                    'resource_type' => 'auto'
                ]
            );
            $data['icon'] = $uploaded['secure_url'];
        }

        $feature->update($data);

        return back()->with('success', 'Fitur berhasil diperbarui!');
    }

    /* =========================================
       HAPUS FITUR
    ========================================= */
    public function destroyFeature(LandingFeature $feature)
    {
        // Menghapus data dari database
        // Catatan: Jika ingin menghapus dari Cloudinary juga, perlu public_id-nya.
        // Untuk sekarang, kita fokus menghapus record di database.
        $feature->delete();

        return back()->with('success', 'Fitur berhasil dihapus!');
    }
}