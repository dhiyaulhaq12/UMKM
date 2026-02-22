{{-- SUMMARY FILTER --}}
<form method="GET" class="flex justify-end mb-3">
    <input
        type="month"
        name="summary_month"
        value="{{ $month }}"
        class="border rounded-md px-3 py-2 text-sm"
        onchange="this.form.submit()">
</form>

{{-- SUMMARY --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-4">

    {{-- PENDAPATAN --}}
    <div class="bg-white p-3 rounded-xl shadow-sm">
        <div class="flex items-center gap-1 text-xs text-gray-500 mb-1">
            <svg class="w-4 h-4 text-green-500"
                 fill="none" stroke="currentColor" stroke-width="2"
                 viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M7 17L17 7M7 7h10v10" />
            </svg>
            Pendapatan
        </div>

        <p class="font-bold text-green-600">
            Rp {{ number_format($totalIncome, 0, ',', '.') }}
        </p>
    </div>

    {{-- PENGELUARAN --}}
    <div class="bg-white p-3 rounded-xl shadow-sm">
        <div class="flex items-center gap-1 text-xs text-gray-500 mb-1">
            <svg class="w-4 h-4 text-red-500 rotate-90"
                 fill="none" stroke="currentColor" stroke-width="2"
                 viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M7 17L17 7M7 7h10v10" />
            </svg>
            Pengeluaran
        </div>

        <p class="font-bold text-red-500">
            Rp {{ number_format($totalExpense, 0, ',', '.') }}
        </p>
    </div>

    {{-- KEUNTUNGAN --}}
    <div class="bg-white p-3 rounded-xl shadow-sm">
        <p class="text-xs text-gray-500 mb-1">Keuntungan</p>

        <p class="font-bold
            {{ $profit >= 0 ? 'text-blue-600' : 'text-red-600' }}">
            Rp {{ number_format($profit, 0, ',', '.') }}
        </p>
    </div>

    {{-- JUMLAH TRANSAKSI --}}
    <div class="bg-white p-3 rounded-xl shadow-sm">
        <p class="text-xs text-gray-500 mb-1">Jumlah Transaksi</p>

        <p class="font-bold text-purple-600">
            {{ $totalTransactions }}
        </p>
    </div>

</div>