@extends('layouts.auth')
@section('title', 'Masuk')

@section('content')
<div class="bg-white rounded-2xl shadow-lg p-6">

    {{-- HEADER --}}
    <div class="text-center mb-5">
        <img src="{{ asset('icons/umkm.png') }}" class="w-28 mx-auto mb-4">
        <h2 class="text-lg font-semibold text-gray-800">Selamat Datang</h2>
        <p class="text-sm text-gray-500">Masuk untuk mengelola UMKM Anda</p>
    </div>

    {{-- ERROR --}}
    @if ($errors->any())
        <div class="mb-4 p-3 bg-red-100 text-red-700 rounded text-sm">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>- {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- FORM --}}
    <form method="POST" action="{{ route('login') }}" class="space-y-4 text-sm">
        @csrf

        {{-- EMAIL --}}
        <div>
            <label class="block mb-1 font-medium text-gray-700">Email</label>
            <div class="relative">
                <span class="absolute inset-y-0 left-3 flex items-center text-gray-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l9 6 9-6M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                </span>
                <input name="email" type="email" class="w-full border border-gray-300 rounded-md p-2 pl-10 focus:ring-2 focus:ring-blue-500 outline-none" placeholder="email@example.com" required>
            </div>
        </div>

        {{-- PASSWORD --}}
        <div>
            <label class="block mb-1 font-medium text-gray-700">Kata Sandi</label>
            <div class="relative">
                <span class="absolute inset-y-0 left-3 flex items-center text-gray-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V7.875a4.5 4.5 0 10-9 0V10.5M6.75 10.5h10.5v9.75H6.75z" />
                    </svg>
                </span>

                <input id="password" name="password" type="password" class="w-full border border-gray-300 rounded-md p-2 pl-10 pr-10 focus:ring-2 focus:ring-blue-500 outline-none" placeholder="Masukkan kata sandi" required>

                <button type="button" onclick="togglePassword('password')" class="absolute inset-y-0 right-3 flex items-center text-gray-400 hover:text-gray-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5 c4.478 0 8.268 2.943 9.542 7 -1.274 4.057 -5.064 7 -9.542 7 -4.477 0 -8.268 -2.943 -9.542 -7z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </button>
            </div>
            
            {{-- DAFTAR & LUPA SANDI (Warna Biru) --}}
            <div class="flex justify-between mt-2 text-[11px] md:text-xs">
                <p class="text-gray-500">
                    Belum punya Akun? 
                    <a href="{{ route('register') }}" class="text-blue-700 font-bold hover:underline">Daftar</a>
                </p>
                <a href="#" class="text-blue-700 font-medium hover:underline">Lupa sandi?</a>
            </div>
        </div>

        {{-- SUBMIT --}}
        <div class="pt-2">
            <button class="w-full bg-[#2b25db] text-white py-2.5 rounded-lg font-semibold hover:bg-blue-800 transition shadow-md active:scale-[0.98]">
                Masuk
            </button>
        </div>

        {{-- KEMBALI KE BERANDA --}}
        <p class="text-center text-sm mt-4">
        <a href="/" class="text-gray-500 font-semibold">← kembali ke beranda</a>
    </p>
    </form>

</div>

<script>
function togglePassword(id) {
    const input = document.getElementById(id);
    input.type = input.type === 'password' ? 'text' : 'password';
}
</script>
@endsection