@extends('layouts.app')
@section('title', 'Prediksi')

@section('content')
<h1 class="text-lg md:text-xl font-semibold mb-4">
    Prediksi
</h1>

{{-- SUMMARY --}}
@include('layouts.summary')

{{-- PREDIKSI --}}
<div class="bg-white rounded-xl p-4 shadow-sm mb-4">
    <h2 class="font-semibold text-sm mb-1">Prediksi Keuntungan</h2>
    <p class="text-xs text-gray-500 mb-3">
        Analisis untuk memprediksi keuntungan
    </p>

    <form method="POST" action="{{ route('predictions.generate') }}">
        @csrf
        <button class="bg-black text-white px-4 py-2 rounded-lg text-sm">
            Generate Prediksi
        </button>
    </form>

    <div class="bg-blue-50 mt-4 p-3 rounded-lg text-sm">
        <strong>Analisis:</strong><br>
        <span class="text-blue-600">
            Analisis berdasarkan riwayat data
        </span>
    </div>
</div>

{{-- HASIL PREDIKSI --}}
@if(session('prediction'))
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
    @foreach ($predictions as $pred)
        <div class="border rounded-xl p-4 bg-white">
            <div class="flex justify-between items-center text-xs text-gray-500 mb-2">
                <span>{{ $pred['month'] }}</span>
                <span>{{ $pred['accuracy'] }}% Akurasi</span>
            </div>

            <p class="text-lg font-semibold text-green-600">
                Rp {{ number_format($pred['amount'], 0, ',', '.') }}
            </p>
        </div>
    @endforeach
</div>

<div class="bg-blue-50 rounded-xl p-4 mt-4 text-sm">
    <p class="font-semibold mb-1">Analisis:</p>
    <p>
        Prediksi dibuat berdasarkan tren keuntungan bulan berjalan
        dengan asumsi pertumbuhan stabil setiap bulan.
    </p>
</div>

{{-- REKOMENDASI --}}
<div class="bg-blue-50 p-4 rounded-xl">
    <h3 class="font-semibold text-sm mb-2">Rekomendasi:</h3>
    <ul class="space-y-2 text-sm">
        <li>● Tingkatkan efisiensi operasional</li>
        <li>● Kembangkan strategi pemasaran</li>
        <li>● Monitor biaya produksi</li>
    </ul>
</div>
@endif

@endsection
