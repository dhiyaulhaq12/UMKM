@extends('layouts.app')
@section('title', 'Laporan Pengeluaran')

@section('content')

{{-- HEADER HALAMAN + TOMBOL EKSPOR PDF PENGELUARAN --}}
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
    <h1 class="text-lg md:text-xl font-semibold">
        Laporan Pengeluaran
    </h1>
    
    {{-- GRUP TOMBOL UNTUK UNDUH LAPORAN --}}
    <div class="flex flex-col sm:flex-row items-center gap-2 w-full sm:w-auto">
        {{-- Tombol PDF Pengeluaran --}}
        <a href="{{ route('reports.export.expense-pdf', ['month' => request('month', request('summary_month', now()->format('Y-m')))]) }}" 
           id="downloadExpensePdfBtn"
           onclick="window.location.href='{{ route('reports.export.expense-pdf') }}?month={{ request('month', request('summary_month', now()->format('Y-m'))) }}&sort=' + document.getElementById('rekapSorter').value; return false;"
           class="inline-flex items-center justify-center gap-2 bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-xl text-xs font-semibold shadow-sm transition-all h-fit w-full sm:w-auto">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            Unduh Laporan PDF
        </a>

        {{-- 🟢 TOMBOL UNDUH MULTI-SHEET EXCEL BARU (SINKRON DENGAN HALAMAN EXPENSE) --}}
        <a href="{{ route('reports.export.expense-excel', ['month' => request('month', request('summary_month', now()->format('Y-m')))]) }}" 
        class="inline-flex items-center justify-center gap-2 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-xl text-xs font-semibold shadow-sm transition-all h-fit w-full sm:w-auto">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            Ekspor ke Excel
        </a>
    </div>
</div>

@include('layouts.summary')

<div class="space-y-4">
    {{-- RINGKASAN KATEGORI --}}
    <div class="bg-white rounded-xl shadow-sm p-5">
        {{-- Header Rekapitulasi & Dropdown Sortir Kontainer Flex --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
            <div>
                <h2 class="font-semibold text-sm text-gray-800">Alokasi Biaya Pengeluaran</h2>
                <p class="text-xs text-gray-500">Periode {{ $monthCarbon->translatedFormat('F Y') }}</p>
            </div>
            
            {{-- 🟢 DROPDOWN SORTIR INTERAKTIF BARU (4 PILIHAN) --}}
            <div class="flex items-center gap-2 text-xs">
                <label for="rekapSorter" class="text-gray-500 font-medium whitespace-nowrap">Urutkan:</label>
                <select id="rekapSorter" onchange="sortExpense(this.value)" 
                        class="border rounded-lg bg-gray-50 px-3 py-2 font-medium text-gray-700 shadow-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all cursor-pointer">
                    <option value="amount_desc">Nominal Terbesar</option>
                    <option value="amount_asc">Nominal Terkecil</option>
                    <option value="count_desc">Transaksi Terbanyak</option>
                    <option value="count_asc">Transaksi Terkecil</option>
                </select>
            </div>
        </div>

        {{-- 🟢 CONTAINER LIST DATA DENGAN DATA-ATTRIBUTES --}}
        <div id="rekapContainer" class="space-y-2 text-sm">
            @forelse($expensesByCategory->sortByDesc('total') as $exp)
            <div class="rekap-item flex justify-between border-b pb-2 items-center hover:bg-gray-50/50 transition px-1 rounded-md"
                 data-amount="{{ $exp->total }}"
                 data-count="{{ $exp->count }}">
                
                {{-- Menampilkan counter berapa kali alokasi transaksi dilakukan --}}
                <div class="flex flex-col">
                    <span class="text-gray-700 font-medium mb-0.5">{{ $exp->category }}</span>
                    <span class="text-[11px] text-gray-400">
                        <span class="font-semibold text-gray-600">{{ $exp->count }}</span> Kali Transaksi
                    </span>
                </div>
                <span class="font-semibold text-red-500">
                    Rp {{ number_format($exp->total, 0, ',', '.') }}
                </span>
            </div>
            @empty
            <p class="text-center text-gray-400 py-3 text-xs" id="emptyMessage">Belum ada catatan pengeluaran biaya.</p>
            @endforelse

            {{-- Footer Total Tetap Terkunci di Bawah Kontainer --}}
            <div id="rekapFooter" class="flex justify-between pt-3 font-bold text-base border-t border-gray-200">
                <span>Total Pengeluaran Kas</span>
                <span class="text-red-500">Rp {{ number_format($totalExpense, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>

    {{-- TABEL PENJABARAN DETAIL TRANSAKSI --}}
    <div class="bg-white rounded-xl shadow-sm p-5">
        <h3 class="font-semibold text-sm mb-3 text-gray-800">Rincian Pengeluaran</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="text-xs uppercase bg-gray-50 text-gray-600 border-b">
                    <tr>
                        <th class="px-3 py-2">Tanggal & Waktu</th>
                        <th class="px-3 py-2">Pos Anggaran / Beban</th>
                        <th class="px-3 py-2">Catatan Keterangan</th>
                        <th class="px-3 py-2 text-right">Jumlah Biaya</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($expenseDetails as $detail)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-3 py-2.5 text-xs text-gray-600 flex flex-col justify-center">
                            <span>{{ \Carbon\Carbon::parse($detail->transaction_date)->translatedFormat('d M Y') }}</span>
                            <span class="text-[10px] text-gray-400 font-medium mt-0.5">
                                {{ $detail->transaction_time ? \Carbon\Carbon::parse($detail->transaction_time)->format('H:i') : '00:00' }}
                            </span>
                        </td>
                        <td class="px-3 py-2.5 font-medium text-gray-800">{{ $detail->category }}</td>
                        <td class="px-3 py-2.5 text-xs text-gray-500 truncate max-w-[180px]" title="{{ $detail->note }}">
                            {{ $detail->note ?? '-' }}
                        </td>
                        <td class="px-3 py-2.5 text-right font-semibold text-red-500">
                            Rp {{ number_format($detail->amount, 0, ',', '.') }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-6 text-gray-400 text-xs">Tidak ada rincian biaya keluar pada periode ini.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $expenseDetails->links() }}
        </div>
    </div>
</div>

{{-- 🟢 SCRIPT JAVASCRIPT DROPDOWN SORTING LIVE UNTUK PENGELUARAN --}}
<script>
function sortExpense(option) {
    const container = document.getElementById('rekapContainer');
    const items = Array.from(container.getElementsByClassName('rekap-item'));
    const footer = document.getElementById('rekapFooter');

    if (items.length === 0) return;

    // Jalankan Logika Komparasi Array DOM
    items.sort((a, b) => {
        const amountA = parseFloat(a.getAttribute('data-amount')) || 0;
        const amountB = parseFloat(b.getAttribute('data-amount')) || 0;
        const countA = parseInt(a.getAttribute('data-count')) || 0;
        const countB = parseInt(b.getAttribute('data-count')) || 0;

        switch (option) {
            case 'amount_desc': // Nominal Terbesar Ke Terkecil
                return amountB - amountA;
            case 'amount_asc':  // Nominal Terkecil Ke Terbesar
                return amountA - amountB;
            case 'count_desc':  // Transaksi Terbanyak Ke Tersedikit
                return countB - countA;
            case 'count_asc':   // Transaksi Tersedikit Ke Terbanyak
                return countA - countB;
            default:
                return 0;
        }
    });

    // Susun Ulang Urutan Element HTML Tepat Sebelum Kotak Baris Footer Total
    items.forEach(item => container.insertBefore(item, footer));
}
</script>

@endsection