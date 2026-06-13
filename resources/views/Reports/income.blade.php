@extends('layouts.app')
@section('title', 'Laporan Pendapatan')

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
    <h1 class="text-lg md:text-xl font-semibold">
        Laporan Pendapatan
    </h1>
    
    {{-- GRUP TOMBOL UNTUK UNDUH LAPORAN --}}
    <div class="flex flex-col sm:flex-row items-center gap-2 w-full sm:w-auto">
        {{-- Tombol PDF --}}
        <a href="{{ route('reports.export.pdf', ['month' => request('month', request('summary_month', now()->format('Y-m')))]) }}" 
           id="downloadIncomePdfBtn"
           onclick="window.location.href='{{ route('reports.export.pdf') }}?month={{ request('month', request('summary_month', now()->format('Y-m'))) }}&sort=' + document.getElementById('rekapSorter').value; return false;"
           class="inline-flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-xl text-xs font-semibold shadow-sm transition-all h-fit w-full sm:w-auto">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            Unduh Laporan PDF
        </a>

        {{-- 🟢 TOMBOL UNDUH MULTI-SHEET EXCEL BARU --}}
        <a href="{{ route('reports.export.income-excel', ['month' => request('month', request('summary_month', now()->format('Y-m')))]) }}" 
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
        {{-- Header Rekapitulasi & Dropdown Sortir --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
            <div>
                <h2 class="font-semibold text-sm text-gray-800">Rekapitulasi Sumber Pendapatan</h2>
                <p class="text-xs text-gray-500">Periode {{ $monthCarbon->translatedFormat('F Y') }}</p>
            </div>
            
            {{-- DROPDOWN SORTIR INTERAKTIF (4 PILIHAN) --}}
            <div class="flex items-center gap-2 text-xs">
                <label for="rekapSorter" class="text-gray-500 font-medium whitespace-nowrap">Urutkan:</label>
                <select id="rekapSorter" onchange="sortRekap(this.value)" 
                        class="border rounded-lg bg-gray-50 px-3 py-2 font-medium text-gray-700 shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all cursor-pointer">
                    <option value="amount_desc">Nominal Terbesar</option>
                    <option value="amount_asc">Nominal Terkecil</option>
                    <option value="count_desc">Transaksi Terbanyak</option>
                    <option value="count_asc">Transaksi Terkecil</option>
                </select>
            </div>
        </div>

        {{-- CONTAINER LIST DATA --}}
        <div id="rekapContainer" class="space-y-2 text-sm">
            @forelse($incomesByCategory->sortByDesc('total') as $inc) {{-- Default awal: nominal terbesar --}}
            <div class="rekap-item flex justify-between border-b pb-2 items-center hover:bg-gray-50/50 transition px-1 rounded-md"
                 data-amount="{{ $inc->total }}"
                 data-count="{{ $inc->count }}">
                
                <div class="flex flex-col">
                    <span class="font-medium text-gray-700 mb-0.5">{{ $inc->category }}</span>
                    <span class="text-[11px] text-gray-400">
                        <span class="font-semibold text-gray-600">{{ $inc->count }}</span> Kali Transaksi
                    </span>
                </div>
                <span class="font-semibold text-green-600">
                    Rp {{ number_format($inc->total, 0, ',', '.') }}
                </span>
            </div>
            @empty
            <p class="text-center text-gray-400 py-3 text-xs">Belum ada data pendapatan masuk.</p>
            @endforelse

{{-- Footer Total Tetap Terkunci di Bawah --}}
<div id="rekapFooter" class="flex flex-col pt-3 border-t border-gray-200 space-y-2">
                {{-- Baris Total Penerimaan --}}
                <div class="flex justify-between font-bold text-base">
                    <span>Total Pendapatan</span>
                    <span class="text-green-600">Rp {{ number_format($totalIncome, 0, ',', '.') }}</span>
                </div>
                
                {{-- 🟢 PERBAIKAN: Menggunakan $totalIncomeTransactions & Ukuran Font Disamakan (text-base font-normal) --}}
                <div class="flex justify-between text-base font-bold text-gray-700 pt-0.5">
                    <span>Rata-rata Pendapatan/Transaksi</span>
                    <span class="text-green-600">
                        @if($totalIncomeTransactions > 0)
                            Rp {{ number_format($totalIncome / $totalIncomeTransactions, 0, ',', '.') }}
                        @else
                            Rp 0
                        @endif
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- TABEL PENJABARAN DETAIL TRANSAKSI --}}
    <div class="bg-white rounded-xl shadow-sm p-5">
        <h3 class="font-semibold text-sm mb-3 text-gray-800">Rincian Pendapatan</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="text-xs uppercase bg-gray-50 text-gray-600 border-b">
                    <tr>
                        <th class="px-3 py-2">Tanggal & Waktu</th>
                        <th class="px-3 py-2">Item</th>
                        <th class="px-3 py-2">Kuantitas</th>
                        <th class="px-3 py-2">Catatan</th>
                        <th class="px-3 py-2 text-right">Total Nilai</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($incomeDetails as $detail)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-3 py-2.5 text-xs text-gray-600 flex flex-col justify-center">
                            <span>{{ \Carbon\Carbon::parse($detail->transaction_date)->translatedFormat('d M Y') }}</span>
                            {{-- FORMAT JAM TRANSAKSI OTOMATIS DI BAWAH TANGGAL --}}
                            <span class="text-[10px] text-gray-400 font-medium mt-0.5">
                                {{ $detail->transaction_time ? \Carbon\Carbon::parse($detail->transaction_time)->format('H:i') : '00:00' }}
                            </span>
                        </td>
                        <td class="px-3 py-2.5 font-medium text-gray-800">{{ $detail->category }}</td>
                        <td class="px-3 py-2.5 text-xs text-gray-500">
                            {{ $detail->quantity ?? 1 }} {{ $detail->unit ?? 'pcs' }}
                        </td>
                        
                        {{-- KOLOM CATATAN DENGAN TRUNCATE & TOMBOL MODAL --}}
                        <td class="px-3 py-2.5 text-xs text-gray-600 max-w-[200px]">
                            @if($detail->note)
                                <div class="flex items-center gap-2">
                                    <span class="truncate" title="{{ $detail->note }}">{{ $detail->note }}</span>
                                    <button type="button" 
                                            onclick="showNoteModal('{{ \Carbon\Carbon::parse($detail->transaction_date)->translatedFormat('d M Y') }}', '{{ $detail->category }}', '{{ addslashes($detail->note) }}')"
                                            class="text-blue-600 hover:text-blue-800 focus:outline-none flex-shrink-0"
                                            title="Lihat catatan utuh">
                                        👁️
                                    </button>
                                </div>
                            @else
                                <span class="text-gray-400 italic">-</span>
                            @endif
                        </td>

                        <td class="px-3 py-2.5 text-right font-semibold text-green-600">
                            Rp {{ number_format($detail->amount, 0, ',', '.') }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-6 text-gray-400 text-xs">Tidak ada rincian transaksi masuk pada periode ini.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $incomeDetails->links() }}
        </div>
    </div>
</div>

{{-- MODAL POP-UP UNTUK MELIHAT CATATAN UTUH --}}
<div id="noteModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    {{-- Background Gelap --}}
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeNoteModal()"></div>

        {{-- Trik Center Modal --}}
        <span class="hidden sm:inline-block sm:align-middle sm:min-h-screen" aria-hidden="true">&#8203;</span>

        {{-- Kotak Modal --}}
        <div class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                        <h3 class="text-base leading-6 font-semibold text-gray-900" id="modalTitle">
                            Detail Catatan Pendapatan
                        </h3>
                        <div class="mt-1 flex gap-2 text-[11px] text-gray-500 border-b pb-3">
                            <span id="modalDate"></span> | <span id="modalCategory" class="font-semibold text-blue-600"></span>
                        </div>
                        <div class="mt-4">
                            {{-- Ruang teks penuh catatan --}}
                            <p id="modalNoteText" class="text-sm text-gray-700 bg-gray-50 p-3.5 rounded-lg border border-gray-100 whitespace-pre-wrap break-words leading-relaxed"></p>
                        </div>
                    </div>
                </div>
            </div>
            {{-- Tombol Penutup di Bawah --}}
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="button" onclick="closeNoteModal()" class="w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>

{{-- JAVASCRIPT LOGIC UNTUK MODAL ACCORDION/POP-UP --}}
<script>
    function showNoteModal(date, category, note) {
        document.getElementById('modalDate').innerText = date;
        document.getElementById('modalCategory').innerText = category;
        document.getElementById('modalNoteText').innerText = note;
        
        // Munculkan Modal
        const modal = document.getElementById('noteModal');
        modal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden'); // Kunci scroll layar belakang
    }

    function closeNoteModal() {
        const modal = document.getElementById('noteModal');
        modal.classList.add('hidden');
        document.body.classList.remove('overflow-hidden'); // Lepas kunci scroll
    }

    // Pendukung: Tutup modal pakai tombol 'Esc' di keyboard
    document.addEventListener('keydown', function(event) {
        if (event.key === "Escape") {
            closeNoteModal();
        }
    });
</script>

{{-- SCRIPT JAVASCRIPT DROPDOWN SORTING --}}
<script>
function sortRekap(option) {
    const container = document.getElementById('rekapContainer');
    const items = Array.from(container.getElementsByClassName('rekap-item'));
    const footer = document.getElementById('rekapFooter');

    // Jalankan Logika Pengurutan Sesuai Pilihan Dropdown
    items.sort((a, b) => {
        const amountA = parseFloat(a.getAttribute('data-amount'));
        const amountB = parseFloat(b.getAttribute('data-amount'));
        const countA = parseInt(a.getAttribute('data-count'));
        const countB = parseInt(b.getAttribute('data-count'));

        switch (option) {
            case 'amount_desc': // Nominal Terbesar Ke Terkecil
                return amountB - amountA;
            case 'amount_asc':  // Nominal Terkecil Ke Terbesar
                return amountA - amountB;
            case 'count_desc':  // Transaksi Terbanyak Ke Terkecil
                return countB - countA;
            case 'count_asc':   // Transaksi Terkecil Ke Terbanyak
                return countA - countB;
            default:
                return 0;
        }
    });

    // Susun Ulang Elemen yang Sudah Disortir Tepat Sebelum Elemen Footer
    items.forEach(item => container.insertBefore(item, footer));
}
</script>
@endsection