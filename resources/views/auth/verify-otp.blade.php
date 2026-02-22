@extends('layouts.auth')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-700 to-indigo-900 flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl shadow-2xl p-8 max-w-md w-full text-center">
        
        {{-- ICON --}}
        <div class="mb-6 flex justify-center">
            <img src="{{ asset('icons/umkm.png') }}" class="w-20 h-20 object-contain">
        </div>

        <h1 class="text-2xl font-bold text-gray-800 mb-1">Verifikasi Kode OTP</h1>
        <p class="text-gray-400 text-sm mb-8">Masukan kode OTP yang telah dikirim melalui email Anda</p>

        <form action="{{ route('otp.verify') }}" method="POST" id="otp-form">
            @csrf
            <input type="hidden" name="email" value="{{ $email }}">

            {{-- TIPS: AUTO FOCUS OTP INPUTS --}}
            <div class="flex justify-between gap-2 mb-10">
                @for ($i = 0; $i < 6; $i++)
                    <input type="text" name="otp[]" maxlength="1" 
                        class="otp-input w-12 h-14 border-2 border-gray-100 rounded-xl text-center text-xl font-bold focus:border-blue-600 focus:ring-4 focus:ring-blue-100 outline-none bg-blue-50 transition-all"
                        inputmode="numeric" required>
                @endfor
            </div>

            {{-- TIMER & RESEND (Sesuai Permintaan) --}}
            <div class="text-right mb-8 h-6">
                <p id="timer-text" class="text-xs text-gray-500 font-medium">
                    Tunggu <span id="seconds" class="font-bold text-blue-600">60</span> detik untuk kirim ulang
                </p>
                <button type="button" id="resend-btn" onclick="resendOtp()" 
                    class="hidden text-xs text-gray-500 hover:text-blue-700 transition font-medium">
                    Tidak menerima kode? <span class="text-blue-700 font-bold">Kirim ulang kode</span>
                </button>
            </div>

            <button type="submit" class="w-full bg-[#2b25db] text-white py-4 rounded-2xl font-bold hover:bg-blue-800 transition-all shadow-lg active:scale-95">
                Verifikasi
            </button>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const inputs = document.querySelectorAll('.otp-input');
    const timerText = document.getElementById('timer-text');
    const resendBtn = document.getElementById('resend-btn');
    const secondsSpan = document.getElementById('seconds');
    let timeLeft = 60;

    // --- FITUR 1: AUTO FOCUS ---
    inputs.forEach((input, index) => {
        input.addEventListener('keyup', (e) => {
            if (e.target.value.length === 1 && index < inputs.length - 1) {
                inputs[index + 1].focus(); // Pindah ke kanan
            }
        });

        input.addEventListener('keydown', (e) => {
            if (e.key === 'Backspace' && e.target.value.length === 0 && index > 0) {
                inputs[index - 1].focus(); // Pindah ke kiri saat hapus
            }
        });
    });

    // --- FITUR 2: TIMER RESEND ---
    function startTimer() {
        timeLeft = 60;
        timerText.classList.remove('hidden');
        resendBtn.classList.add('hidden');
        
        let timer = setInterval(() => {
            if(timeLeft <= 0) {
                clearInterval(timer);
                timerText.classList.add('hidden');
                resendBtn.classList.remove('hidden'); // Muncul setelah 60 detik
            } else {
                secondsSpan.innerText = timeLeft;
            }
            timeLeft -= 1;
        }, 1000);
    }

    // Fungsi Resend via AJAX
    window.resendOtp = function() {
        fetch("{{ route('otp.resend') }}", {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' },
            body: JSON.stringify({ email: '{{ $email }}' })
        }).then(() => startTimer());
    };

    startTimer();
});
</script>
@endsection