@extends('layouts.app')
@section('title', 'Akun')

@section('content')
<h1 class="text-lg font-semibold mb-4">Ubah Profil</h1>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <form method="POST" action="{{ route('account.profile') }}" enctype="multipart/form-data">
        @csrf

        {{-- AREA FOTO PROFIL --}}
        <div class="p-6 border-b border-gray-100"> {{-- Padding dikurangi dari 8 ke 6 --}}
            <div class="grid grid-cols-1 md:grid-cols-3 items-center gap-4"> {{-- Gap dikurangi dari 6 ke 4 --}}
                <label class="text-sm font-semibold text-gray-700">Foto Profil</label>
                <div class="md:col-span-2 flex flex-col items-center md:items-start">
                    <div class="relative w-28 h-28">
                        <img
                            id="profilePreview"
                            src="{{ auth()->user()->photo ? auth()->user()->photo : asset('icons/account.png') }}"
                            data-original="{{ auth()->user()->photo ? auth()->user()->photo : asset('icons/account.png') }}"
                            class="w-28 h-28 rounded-full object-cover border cursor-pointer hover:brightness-95 transition shadow-sm"
                            onclick="viewFullImage()"
                            title="Klik untuk melihat foto"
                        >

                        <input type="file" id="photoInput" name="photo" accept="image/*" class="hidden">

                        {{-- Icon Pensil --}}
                        <button type="button" id="pencilBtn" class="absolute top-0 -right-1 bg-white border rounded-full p-1.5 shadow-sm text-gray-500 hover:text-gray-700 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                            </svg>
                        </button>

                        {{-- Tombol Hapus --}}
                        @if(auth()->user()->photo)
                            <button type="button" onclick="deleteProfilePhoto()" class="absolute bottom-0 -right-1 bg-white border rounded-full p-1.5 shadow-sm text-gray-400 hover:text-red-500 transition">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        @endif

                        <button type="button" id="removePhotoBtn" class="hidden absolute top-1/2 -left-2 -translate-y-1/2 bg-black text-white w-5 h-5 rounded-full text-[10px] flex items-center justify-center shadow-md">✕</button>
                    </div>
                    <p class="text-[10px] text-gray-400 mt-2 italic">Tipe file yang diizinkan: png, jpg, jpeg.</p>
                </div>
            </div>
        </div>

        {{-- FORM INPUT DATA --}}
        <div class="p-6 space-y-4"> {{-- Space-y dikurangi dari 8 ke 4 --}}
            {{-- Nama --}}
            <div class="grid grid-cols-1 md:grid-cols-3 items-center gap-4">
                <label class="text-sm font-semibold text-gray-700">Nama</label>
                <div class="md:col-span-2">
                    <input name="name" value="{{ $user->name }}" class="w-full max-w-lg border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none transition" placeholder="Masukkan nama">
                </div>
            </div>

            {{-- Nama Usaha --}}
            <div class="grid grid-cols-1 md:grid-cols-3 items-center gap-4">
                <label class="text-sm font-semibold text-gray-700">Nama Usaha</label>
                <div class="md:col-span-2">
                    <input name="business_name" value="{{ $user->business_name }}" class="w-full max-w-lg border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none transition" placeholder="Masukkan nama usaha">
                </div>
            </div>

            {{-- Email --}}
            <div class="grid grid-cols-1 md:grid-cols-3 items-center gap-4">
                <label class="text-sm font-semibold text-gray-700">Email</label>
                <div class="md:col-span-2">
                    <input type="email" value="{{ auth()->user()->email }}" disabled class="w-full max-w-lg bg-gray-50 border border-gray-200 rounded-md px-3 py-2 text-sm cursor-not-allowed text-gray-500">
                </div>
            </div>

            {{-- Button Simpan Profil --}}
            <div class="flex justify-end pt-2">
                <button type="submit" class="bg-blue-800 text-white px-8 py-2 rounded-lg text-sm font-medium shadow-sm hover:bg-blue-900 transition">
                    Simpan
                </button>
            </div>
        </div>
    </form>

    {{-- SECTION UBAH KATA SANDI --}}
    <div class="bg-gray-50/50 border-t border-gray-100 p-6">
        <h2 class="text-md font-bold mb-4">Ubah Kata Sandi</h2>
        <form method="POST" action="{{ route('account.password') }}" class="space-y-4">
            {{-- Password Lama --}}@csrf
            <div class="grid grid-cols-1 md:grid-cols-3 items-center gap-4">
                <label class="text-sm font-semibold text-gray-700">Kata Sandi Lama</label>
                <div class="md:col-span-2 relative max-w-lg">
                    <input id="old_password" name="old_password" type="password" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none transition" placeholder="Masukkan kata sandi lama">
                    @include('partials.eye', ['id' => 'old_password'])
                </div>
            </div>

            {{-- Password Baru --}}
            <div class="grid grid-cols-1 md:grid-cols-3 items-center gap-4">
                <label class="text-sm font-semibold text-gray-700">Kata Sandi Baru</label>
                <div class="md:col-span-2 relative max-w-lg">
                    <input id="password" name="password" type="password" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none transition" placeholder="Masukkan kata sandi baru">
                    @include('partials.eye', ['id' => 'password'])
                </div>
            </div>

            {{-- Konfirmasi --}}
            <div class="grid grid-cols-1 md:grid-cols-3 items-center gap-4">
                <label class="text-sm font-semibold text-gray-700">Konfirmasi Kata Sandi</label>
                <div class="md:col-span-2 relative max-w-lg">
                    <input id="password_confirmation" name="password_confirmation" type="password" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none transition" placeholder="Masukkan konfirmasi kata sandi">
                    @include('partials.eye', ['id' => 'password_confirmation'])
                </div>
            </div>

            {{-- Button Simpan Password --}}
            <div class="flex justify-end pt-2">
                <button type="submit" class="bg-blue-800 text-white px-8 py-2 rounded-lg text-sm font-medium shadow-sm hover:bg-blue-900 transition">
                    Simpan Kata Sandi
                </button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL FOTO UTUH --}}
<div id="imageModal" class="hidden fixed inset-0 z-[99] bg-black bg-opacity-80 flex items-center justify-center p-4" onclick="closeModal()">
    <div class="relative" onclick="event.stopPropagation()">
        <button type="button" onclick="closeModal()" class="absolute -top-12 -right-2 text-white text-4xl hover:text-gray-300 transition">&times;</button>
        <img id="fullImage" src="" class="max-w-full max-h-[85vh] rounded shadow-lg border-2 border-white">
    </div>
</div>

{{-- FORM DELETE HIDDEN --}}
<form id="deletePhotoForm" action="{{ route('account.profile.delete') }}" method="POST" class="hidden">@csrf @method('DELETE')</form>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@vite(['resources/js/account.js'])

<script>
    document.addEventListener('DOMContentLoaded', function() {
        @if(session('success')) Swal.fire({ icon: 'success', title: 'Berhasil!', text: "{{ session('success') }}", timer: 2000, showConfirmButton: false }); @endif
        @if(session('error')) Swal.fire({ icon: 'error', title: 'Gagal', text: "{{ session('error') }}" }); @endif
        @if($errors->any()) Swal.fire({ icon: 'warning', title: 'Periksa Kembali', html: `<ul style='text-align: left; font-size: 13px;'>@foreach ($errors->all() as $error)<li>• {{ $error }}</li>@endforeach</ul>` }); @endif
    });
</script>
@endsection