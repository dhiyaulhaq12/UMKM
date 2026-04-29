@extends('layouts.admin')
@section('title', 'Akun Admin')

@section('content')
<h1 class="text-xl font-bold mb-6 text-gray-800">Ubah Profil</h1>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
<form method="POST" action="{{ route('admin.account.profile.update') }}" enctype="multipart/form-data">        @csrf

        {{-- AREA FOTO PROFIL --}}
        <div class="p-8 border-b border-gray-100">
            <div class="grid grid-cols-1 md:grid-cols-3 items-start gap-6">
                <label class="text-sm font-semibold text-gray-700">Foto Profil</label>
                <div class="md:col-span-2 flex flex-col items-center md:items-start">
                    <div class="relative w-32 h-32">
                        <img id="profilePreview" 
                             src="{{ auth()->user()->photo ? auth()->user()->photo : asset('icons/account.png') }}"
                             class="w-32 h-32 rounded-full object-cover border-4 border-gray-50 shadow-md">
                        
                        <input type="file" id="photoInput" name="photo" accept="image/*" class="hidden" onchange="previewImage(event)">
                        
                        {{-- Icon Pensil untuk ganti foto --}}
                        <button type="button" onclick="document.getElementById('photoInput').click()" 
                                class="absolute top-0 right-0 bg-white border rounded-full p-2 shadow-sm text-gray-500 hover:text-blue-600 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                            </svg>
                        </button>
                    </div>
                    <p class="text-[10px] text-gray-400 mt-3 italic text-center md:text-left">Tipe file yang diizinkan: png, jpg, jpeg.</p>
                </div>
            </div>
        </div>

        {{-- FORM INPUT DATA --}}
        <div class="p-8 space-y-6">
            {{-- Nama --}}
            <div class="grid grid-cols-1 md:grid-cols-3 items-center gap-6">
                <label class="text-sm font-semibold text-gray-700">Nama</label>
                <div class="md:col-span-2">
                    <input name="name" value="{{ $user->name }}" class="w-full max-w-xl border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500 outline-none transition bg-gray-50/50">
                </div>
            </div>

            {{-- Email --}}
            <div class="grid grid-cols-1 md:grid-cols-3 items-center gap-6">
                <label class="text-sm font-semibold text-gray-700">Email</label>
                <div class="md:col-span-2 text-sm text-gray-900 font-bold">
                    {{ auth()->user()->email }}
                </div>
            </div>

            <div class="flex justify-end pt-4">
                <button type="submit" class="bg-[#24294b] text-white px-10 py-2.5 rounded-xl text-sm font-bold shadow-lg shadow-blue-100 hover:bg-blue-800 transition">
                    Simpan
                </button>
            </div>
        </div>
    </form>

    {{-- SECTION UBAH KATA SANDI --}}
    <div class="bg-gray-50/50 border-t border-gray-100 p-8">
        <h2 class="text-lg font-bold mb-6 text-gray-800">Ubah Kata Sandi</h2>
        <form method="POST" action="{{ route('admin.account.password.update') }}">            @csrf
            <div class="grid grid-cols-1 md:grid-cols-3 items-center gap-6">
                <label class="text-sm font-semibold text-gray-700">Kata Sandi Lama</label>
                <div class="md:col-span-2">
                    <input name="old_password" type="password" class="w-full max-w-xl border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500 outline-none transition bg-white" placeholder="Masukkan kata sandi lama">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 items-center gap-6">
                <label class="text-sm font-semibold text-gray-700">Kata Sandi Baru</label>
                <div class="md:col-span-2">
                    <input name="password" type="password" class="w-full max-w-xl border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500 outline-none transition bg-white" placeholder="Masukkan kata sandi baru">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 items-center gap-6">
                <label class="text-sm font-semibold text-gray-700">Konfirmasi Kata Sandi</label>
                <div class="md:col-span-2">
                    <input name="password_confirmation" type="password" class="w-full max-w-xl border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500 outline-none transition bg-white" placeholder="Masukkan konfirmasi kata sandi">
                </div>
            </div>

            <div class="flex justify-end pt-4">
                <button type="submit" class="bg-[#24294b] text-white px-10 py-2.5 rounded-xl text-sm font-bold shadow-lg shadow-blue-100 hover:bg-blue-800 transition">
                    Simpan Kata Sandi
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function previewImage(event) {
        const reader = new FileReader();
        reader.onload = function(){
            const output = document.getElementById('profilePreview');
            output.src = reader.result;
        };
        reader.readAsDataURL(event.target.files[0]);
    }
</script>
@endsection