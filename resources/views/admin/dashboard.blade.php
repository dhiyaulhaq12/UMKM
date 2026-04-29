@extends('layouts.admin')

@section('content')
<div class="p-6">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">Dashboard</h2>

    {{-- STATS CARDS --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-[#24294b] p-6 rounded-xl text-white shadow-lg flex flex-col items-center justify-center">
            <span class="text-sm font-medium opacity-80 mb-2">Total User</span>
            <span class="text-5xl font-bold">{{ $totalUser }}</span>
        </div>

        <div class="bg-[#ff4444] p-6 rounded-xl text-white shadow-lg flex flex-col items-center justify-center">
            <span class="text-sm font-medium opacity-80 mb-2">User Aktif</span>
            <span class="text-5xl font-bold">{{ $userAktif }}</span>
        </div>

        <div class="bg-[#0084ff] p-6 rounded-xl text-white shadow-lg flex flex-col items-center justify-center">
            <span class="text-sm font-medium opacity-80 mb-2">Konten Aktif</span>
            <span class="text-5xl font-bold">{{ $kontenAktif }}</span>
        </div>

        <div class="bg-[#ffcc00] p-6 rounded-xl text-white shadow-lg flex flex-col items-center justify-center">
            <span class="text-sm font-medium opacity-80 mb-2">Aktivitas Hari Ini</span>
            <span class="text-5xl font-bold">{{ $aktivitasHariIni }}</span>
        </div>
    </div>

    {{-- AKTIVITAS TERAKHIR --}}
    <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
        <h3 class="text-lg font-bold text-gray-800 mb-4">Aktivitas Terakhir</h3>
        <div class="space-y-4">
            <div class="flex items-center gap-3">
                <span class="text-green-500 font-bold">✓</span>
                <p class="text-gray-700 font-medium">Mengedit Hero Section</p>
            </div>
            <div class="flex items-center gap-3">
                <span class="text-green-500 font-bold">✓</span>
                <p class="text-gray-700 font-medium">User baru mendaftar</p>
            </div>
            <div class="flex items-center gap-3">
                <span class="text-green-500 font-bold">✓</span>
                <p class="text-gray-700 font-medium">Menonaktifkan Akun Budi</p>
            </div>
        </div>
    </div>
</div>
@endsection