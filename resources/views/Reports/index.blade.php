@extends('layouts.app')
@section('title', 'Laporan')

@section('content')

<h1 class="text-lg md:text-xl font-semibold mb-4">
    Laporan
</h1>

@include('layouts.summary')

{{-- CARD LAPORAN --}}
<div class="bg-white rounded-xl shadow-sm p-5">

    {{-- HEADER --}}
        <div class="flex justify-between items-center mb-4">
        <div>
            <h2 class="font-semibold text-sm">Laporan Keuangan</h2>
            <p class="text-xs text-gray-500">
                Ringkasan laporan keuangan periode
                {{ $monthCarbon->translatedFormat('F Y') }}
            </p>
        </div>
    </div>


    {{-- LABA RUGI --}}
    <h3 class="font-semibold text-sm mb-2">
        Laporan Laba Rugi
    </h3>

    <div class="space-y-2 text-sm">

        <div class="flex justify-between border-b pb-2">
            <span>Total Pendapatan</span>
            <span class="font-semibold text-green-600">
                Rp {{ number_format($totalIncome, 0, ',', '.') }}
            </span>
        </div>

        @foreach($expensesByCategory as $exp)
        <div class="flex justify-between border-b pb-2">
            <span>{{ $exp->category }}</span>
            <span class="text-red-500">
                Rp {{ number_format($exp->total, 0, ',', '.') }}
            </span>
        </div>
        @endforeach

        <div class="flex justify-between pt-3 font-semibold">
            <span>Keuntungan Bersih</span>
            <span class="text-green-600">
                Rp {{ number_format($profit, 0, ',', '.') }}
            </span>
        </div>

    </div>

    {{-- METRIK --}}
    <div class="mt-6 border rounded-xl p-4">
        <h4 class="font-semibold text-sm mb-3">
            Metrik Keuangan
        </h4>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-center text-sm">

            <div class="border rounded-lg p-3">
                <p class="font-bold text-blue-600">
                    {{ $profitMargin }}%
                </p>
                <p class="text-xs text-gray-500">
                    Margin Keuntungan
                </p>
            </div>

            <div class="border rounded-lg p-3">
                <p class="font-bold text-red-500">
                    {{ $ratio }}%
                </p>
                <p class="text-xs text-gray-500">
                    Rasio Pendapatan/Biaya
                </p>
            </div>

            <div class="border rounded-lg p-3">
                <p class="font-bold text-green-600">
                    {{ $health }}
                </p>
                <p class="text-xs text-gray-500">
                    Status Kesehatan
                </p>
            </div>

        </div>
    </div>

    {{-- EXPORT --}}
    <div class="flex gap-3 mt-5">
    <a href="{{ route('reports.export.pdf', ['month' => $month]) }}"
       class="border px-4 py-2 rounded-lg text-sm hover:bg-gray-100">
        ⬇ Export PDF
    </a>
    <a href="{{ route('reports.export.excel', ['month' => $month]) }}"
        class="border px-4 py-2 rounded-lg text-sm hover:bg-gray-100">
        ⬇ Export Excel
    </a>
    </div>

</div>

@endsection
