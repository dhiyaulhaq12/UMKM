@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    {{-- FORM HERO --}}
    <div class="bg-white p-6 rounded-2xl border shadow-sm">
        <h3 class="font-bold text-lg mb-4 text-gray-800">Hero Section</h3>
        <form action="{{ route('admin.landing.hero.update') }}" method="POST">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Judul Utama</label>
                    <input type="text" name="hero_title" value="{{ $settings['hero_title'] ?? '' }}" class="w-full border rounded-xl p-3 focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Deskripsi Utama</label>
                    <textarea name="hero_desc" rows="3" class="w-full border rounded-xl p-3 focus:ring-2 focus:ring-blue-500 outline-none">{{ $settings['hero_desc'] ?? '' }}</textarea>
                </div>
                <button type="submit" class="bg-[#24294b] text-white px-6 py-2 rounded-xl font-bold hover:bg-blue-900 transition">Update</button>
            </div>
        </form>
    </div>

    {{-- FORM FITUR --}}
    <div class="bg-white p-6 rounded-2xl border shadow-sm">
        <h3 class="font-bold text-lg mb-4 text-gray-800">Tambah Fitur Unggulan</h3>
        <form action="{{ route('admin.landing.feature.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                <input type="file" name="icon" class="border p-2 rounded-xl text-sm" required>
                <input type="text" name="title" placeholder="Nama Fitur" class="border p-2 rounded-xl text-sm" required>
                <input type="text" name="description" placeholder="Deskripsi Singkat" class="border p-2 rounded-xl text-sm" required>
            </div>
            <button type="submit" class="bg-[#24294b] text-white px-6 py-2 rounded-xl font-bold hover:bg-blue-900 transition">Tambah Fitur</button>
        </form>

        <hr class="my-6">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($features as $feature)
            <div x-data="{ open: false }" class="p-4 border rounded-2xl bg-gray-50">
                <div class="flex items-center gap-4">
                    <img src="{{ $feature->icon }}" class="w-12 h-12 object-contain rounded-lg">
                    <div class="flex-1">
                        <h4 class="font-bold text-sm">{{ $feature->title }}</h4>
                        <p class="text-xs text-gray-500 line-clamp-1">{{ $feature->description }}</p>
                    </div>
                    <div class="flex gap-2">
                        {{-- Tombol Edit (Trigger Alpine.js) --}}
                        <button @click="open = !open" class="text-blue-600 text-xs font-bold hover:underline">Edit</button>
                        
                        <form action="{{ route('admin.landing.feature.destroy', $feature->id) }}" method="POST" onsubmit="return confirm('Hapus fitur ini?')">
                            @csrf @method('DELETE')
                            <button class="text-red-500 text-xs font-bold hover:underline">Hapus</button>
                        </form>
                    </div>
                </div>

                {{-- Form Edit (Muncul saat tombol Edit diklik) --}}
                <div x-show="open" x-transition class="mt-4 pt-4 border-t border-gray-200">
                    <form action="{{ route('admin.landing.feature.update', $feature->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf @method('PUT')
                        <div class="space-y-3">
                            <input type="text" name="title" value="{{ $feature->title }}" class="w-full border p-2 rounded-lg text-sm">
                            <textarea name="description" rows="2" class="w-full border p-2 rounded-lg text-sm">{{ $feature->description }}</textarea>
                            <div>
                                <label class="text-[10px] font-bold text-gray-400 uppercase">Ganti Ikon (Opsional)</label>
                                <input type="file" name="icon" class="w-full border p-2 rounded-lg text-sm">
                            </div>
                            <div class="flex gap-2">
                                <button type="submit" class="bg-[#24294b] text-white px-4 py-1.5 rounded-lg text-xs font-bold">Simpan</button>
                                <button type="button" @click="open = false" class="bg-gray-200 text-gray-600 px-4 py-1.5 rounded-lg text-xs font-bold">Batal</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection