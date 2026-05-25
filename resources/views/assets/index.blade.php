@extends('layouts.app')
@section('title', 'Manajemen Aset')

@section('content')

<div class="flex items-center justify-between mb-3">
    <h1 class="text-lg md:text-xl font-semibold">
        Manajemen Aset
    </h1>
    <span class="text-[10px] bg-green-100 text-green-700 px-2 py-0.5 rounded-full font-bold uppercase">
        {{ auth()->user()->business_type }}
    </span>
</div>

{{-- RINGKASAN ASET --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <div class="bg-white p-4 rounded-xl shadow-sm border-l-4 border-blue-600">
        <p class="text-xs text-gray-500 uppercase font-bold">Total Nilai Aset</p>
        <p class="text-lg font-bold text-gray-800">Rp {{ number_format($assets->sum('value'), 0, ',', '.') }}</p>
    </div>
    <div class="bg-white p-4 rounded-xl shadow-sm border-l-4 border-purple-600">
        <p class="text-xs text-gray-500 uppercase font-bold">Jumlah Item</p>
        <p class="text-lg font-bold text-gray-800">{{ $assets->count() }} Unit</p>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-12 gap-4">

    {{-- FORM TAMBAH ASET --}}
    <div class="md:col-span-4 bg-white p-4 rounded-xl shadow-sm h-fit">
        <h2 class="font-semibold text-sm mb-3">Tambah Aset Baru</h2>
        
        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-3 py-2 rounded-md text-xs mb-3">
                <ul class="list-disc pl-4">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('assets.store') }}" enctype="multipart/form-data" class="space-y-3 text-sm">
            @csrf
            
            <div>
                <label class="block mb-1 font-medium">Nama Aset</label>
                <input name="name" type="text" class="w-full border rounded-md p-2" placeholder="Contoh: Mesin Kopi" required>
            </div>

            <div>
                <label class="block mb-1 font-medium">Nilai Aset (Harga Beli)</label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">Rp</span>
                    <input type="text" id="amount" name="value" inputmode="numeric" class="w-full border rounded-md pl-10 pr-3 py-2" placeholder="0" required>
                </div>
            </div>

            <div>
                <label class="block mb-1 font-medium">Tanggal Perolehan</label>
                <input name="purchase_date" type="date" class="w-full border rounded-md p-2" required>
            </div>

            <div>
                <label class="block mb-1 font-medium text-gray-700">Bukti/Foto Aset <span class="text-gray-400 italic">(opsional)</span></label>
                <select id="imageSource" class="w-full border rounded-md p-2 text-sm mb-2">
                    <option value="">Pilih Sumber</option>
                    <option value="camera" id="cameraOption">Ambil Foto</option>
                    <option value="file">Galeri Perangkat</option>
                </select>
                <input type="file" id="imageInput" name="image" accept="image/*" class="hidden">
                
                <div id="imagePreviewWrapper" class="relative hidden mt-2">
                    <img id="imagePreview" class="w-full max-h-40 object-cover rounded-lg border">
                    <button type="button" id="removeImage" class="absolute top-1 right-1 bg-black/60 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs">✕</button>
                </div>
            </div>

            <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded-lg font-semibold hover:bg-blue-700 transition">
                Simpan Aset
            </button>
        </form>
    </div>

    {{-- DAFTAR ASET --}}
<div class="md:col-span-8 bg-white p-4 rounded-xl shadow-sm">
    <h2 class="font-semibold text-sm mb-4">Daftar Aset Dimiliki</h2>

    <div class="grid grid-cols-11 text-xs font-semibold text-gray-700 border-b border-gray-200 pb-2 mb-3">
        <div class="col-span-4">Nama Aset</div>
        <div class="col-span-3">Tanggal Beli</div>
        <div class="col-span-2">Nilai</div>
        <div class="col-span-2 text-center">Aksi</div>
    </div>

    <div class="space-y-3 text-sm">
        @forelse ($assets as $asset)
            <div class="grid grid-cols-11 items-center border-b pb-2">
                {{-- NAMA ASET --}}
                <div class="col-span-4 font-medium text-gray-700">
                    {{ $asset->name }}
                </div>

                {{-- TANGGAL --}}
                <div class="col-span-3 text-gray-600">
                    {{ \Carbon\Carbon::parse($asset->purchase_date)->translatedFormat('d M Y') }}
                </div>

                {{-- NILAI --}}
                <div class="col-span-2 font-semibold text-blue-600">
                    Rp {{ number_format($asset->value, 0, ',', '.') }}
                </div>

                {{-- AKSI --}}
                <div class="col-span-2 flex justify-end gap-2 whitespace-nowrap">
                    {{-- LIHAT GAMBAR --}}
                    @if($asset->image)
                    <button type="button" onclick="openImage(this)" class="text-gray-400 hover:text-gray-600 transition" 
                            data-image="{{ $asset->image }}" data-note="Aset: {{ $asset->name }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5 c4.478 0 8.268 2.943 9.542 7 -1.274 4.057 -5.064 7 -9.542 7 -4.477 0 0 -8.268 -2.943 -9.542 -7z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </button>
                    @endif

                    {{-- EDIT --}}
                    <button type="button" class="text-gray-400 hover:text-blue-600 transition" 
                            data-id="{{ $asset->id }}" 
                            data-name="{{ $asset->name }}" 
                            data-value="{{ $asset->value }}" 
                            data-date="{{ $asset->purchase_date }}" 
                            data-image="{{ $asset->image ?? '' }}" 
                            onclick="openEditAsset(this)">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16.862 4.487a2.1 2.1 0 113.03 2.9L7.5 19.78 3 21l1.22-4.5 12.642-12.013z" />
                        </svg>
                    </button>

                    {{-- HAPUS --}}
                    <form method="POST" action="{{ route('assets.destroy', $asset->id) }}" onsubmit="return confirm('Hapus aset ini?')">
                        @csrf @method('DELETE')
                        <button class="text-gray-400 hover:text-red-600 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 7h12M9 7V4h6v3m-7 3v7m4-7v7m4-7v7M6 7l1 13h10l1-13" />
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <p class="text-center text-gray-500 text-sm py-4">Belum ada data aset</p>
        @endforelse
    </div>
</div>

{{-- MODAL IMAGE (Sama dengan Transaksi) --}}
<div id="imageModal" class="fixed inset-0 bg-black/60 hidden items-center justify-center z-50">
    <div class="bg-white p-4 rounded-lg max-w-md w-full relative">
        <button onclick="closeImage()" class="absolute top-2 right-3 text-xl">✕</button>
        <img id="modalImage" class="w-full rounded mb-3">
        <p id="modalNote" class="text-sm text-gray-800 font-semibold"></p>
    </div>
</div>

{{-- MODAL EDIT ASET --}}
<div id="editAssetModal" class="fixed inset-0 bg-black/60 hidden items-center justify-center z-50">
    <div class="bg-white rounded-xl max-w-md w-full p-4 relative">
        <button onclick="closeEditAsset()" class="absolute top-3 right-4 text-xl text-gray-500">✕</button>
        <h3 class="font-semibold text-sm mb-4">Edit Data Aset</h3>

        <form id="editAssetForm" method="POST" enctype="multipart/form-data" class="space-y-3 text-sm">
            @csrf @method('PUT')
            <div>
                <label class="block mb-1 font-medium">Nama Aset</label>
                <input id="editAssetName" name="name" type="text" class="w-full border rounded-md p-2">
            </div>
            <div>
                <label class="block mb-1 font-medium">Nilai Aset (Rp)</label>
                <input id="editAssetValue" name="value" type="number" class="w-full border rounded-md p-2">
            </div>
            <div>
                <label class="block mb-1 font-medium">Tanggal Perolehan</label>
                <input id="editAssetDate" name="purchase_date" type="date" class="w-full border rounded-md p-2">
            </div>
            <div>
                <label class="block mb-1 font-medium text-gray-700">Gambar (opsional)</label>
                <img id="editAssetPreview" class="w-full max-h-40 object-cover rounded mb-2 hidden border">
                <input type="file" name="image" accept="image/*" class="w-full border rounded-md p-2 text-xs">
            </div>
            <button class="w-full bg-blue-600 text-white py-2 rounded-lg font-semibold">Simpan Perubahan</button>
        </form>
    </div>
</div>

{{-- Metadata untuk JS --}}
<div id="business-metadata" data-type="{{ auth()->user()->business_type }}" class="hidden"></div>

@endsection

@push('scripts')
    {{-- Kita panggil JS yang sama karena fungsi Format Rupiah & Camera ada di sana --}}
    @vite('resources/js/transactions.js')
@endpush

<script>
// Fungsi Modal Gambar (Sama dengan Transaksi)
function openImage(el) {
    const image = el.dataset.image;
    const note = el.dataset.note || '-';
    const img = document.getElementById('modalImage');
    img.src = image;
    document.getElementById('modalNote').innerText = note;
    const modal = document.getElementById('imageModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeImage() {
    document.getElementById('imageModal').classList.add('hidden');
}

// Fungsi Modal Edit Aset
function openEditAsset(el) {
    document.getElementById('editAssetName').value = el.dataset.name;
    document.getElementById('editAssetValue').value = el.dataset.value;
    document.getElementById('editAssetDate').value = el.dataset.date;

    const form = document.getElementById('editAssetForm');
    form.action = `/assets/${el.dataset.id}`;

    const imgPreview = document.getElementById('editAssetPreview');
    if (el.dataset.image) {
        imgPreview.src = el.dataset.image;
        imgPreview.classList.remove('hidden');
    } else {
        imgPreview.classList.add('hidden');
    }

    const modal = document.getElementById('editAssetModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeEditAsset() {
    document.getElementById('editAssetModal').classList.add('hidden');
}
</script>