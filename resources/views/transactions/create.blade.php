<!-- @extends('layouts.app')

@section('title', 'Tambah Transaksi')

@section('content')
<div class="max-w-xl mx-auto mt-10 bg-white p-6 rounded shadow">

    <h2 class="text-xl font-bold mb-4">Tambah Transaksi</h2>

    <form method="POST" action="{{ route('transactions.store') }}" enctype="multipart/form-data">
        @csrf

        {{-- TIPE --}}
        <div class="mb-4">
            <label class="block mb-1">Tipe Transaksi</label>
            <select id="type" name="type" class="w-full border p-2 rounded">
                <option value="">Pilih tipe</option>
                <option value="income">Pendapatan</option>
                <option value="expense">Pengeluaran</option>
            </select>
        </div>

        {{-- KATEGORI --}}
        <div class="mb-4">
            <label class="block mb-1">Kategori</label>
            <select id="category" name="category" class="w-full border p-2 rounded" disabled>
                <option value="">Pilih kategori</option>
            </select>
        </div>

        {{-- JUMLAH --}}
        <div class="mb-4">
            <label class="block mb-1">Jumlah</label>
            <input type="number" name="amount" class="w-full border p-2 rounded">
        </div>

        {{-- TANGGAL --}}
        <div class="mb-4">
            <label class="block mb-1">Tanggal</label>
            <input type="date" name="transaction_date" class="w-full border p-2 rounded">
        </div>

        {{-- CATATAN --}}
        <div class="mb-4">
            <label class="block mb-1">Catatan (opsional)</label>
            <textarea name="note" class="w-full border p-2 rounded"></textarea>
        </div>

        {{-- BUKTI TRANSAKSI --}}
        <div class="mb-4">
            <label class="block mb-1">Bukti Transaksi (Foto)</label>
            <input 
                type="file"
                name="image"
                accept="image/*"
                capture="environment"
                class="w-full border p-2 rounded"
            >
            <p class="text-sm text-gray-500 mt-1">
                Bisa ambil dari kamera atau galeri
            </p>
        </div>

        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">
            Simpan
        </button>

    </form>
</div>
@endsection

@vite('resources/js/transactions.js') -->
