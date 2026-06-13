@extends('layouts.app')
@section('title', 'Transaksi')

@section('content')
<div id="business-metadata" data-type="{{ auth()->user()->business_type }}" class="hidden"></div>

<div class="flex items-center justify-between mb-3">
    <h1 class="text-lg md:text-xl font-semibold">
        Transaksi
    </h1>
    <span class="text-[10px] bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full font-bold uppercase">
        {{ auth()->user()->business_type }}
    </span>
</div>

@include('layouts.summary')

{{-- CONTENT --}}
<div class="grid grid-cols-1 md:grid-cols-12 gap-4">

    {{-- TAMBAH TRANSAKSI --}}
    <div class="md:col-span-4 bg-white p-4 rounded-xl shadow-sm">
        <div class="flex items-center justify-between mb-3">
            <h2 class="font-semibold text-sm">
                Tambah Transaksi
            </h2>
        </div>
        
        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-3 py-2 rounded-md text-xs mb-3">
                <ul class="list-disc pl-4">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST"
              action="{{ route('transactions.store') }}"
              enctype="multipart/form-data"
              class="space-y-3 text-sm">

            @csrf

            {{-- TIPE --}}
            <div>
                <label class="block mb-1 font-medium">Tipe</label>
                <select id="type" name="type" class="w-full border rounded-md p-2">
                    <option value="">Pilih Tipe</option>
                    <option value="income">Pendapatan</option>
                    <option value="expense">Pengeluaran</option>
                </select>
            </div>

            {{-- KATEGORI --}}
            <div>
                <label class="block mb-1 font-medium">Kategori / Produk</label>
                <select id="category" name="category" class="w-full border rounded-md p-2" disabled>
                    <option value="">Pilih Kategori</option>
                </select>
            </div>

            {{-- WRAPPER QUANTITY & SATUAN --}}
            <div id="quantityWrapper" class="grid grid-cols-2 gap-2 hidden">
                <div>
                    <label class="block mb-1 font-medium">Kuantitas</label>
                    <input type="number" id="quantity" name="quantity" value="1" min="1" class="w-full border rounded-md p-2">
                </div>
                <div>
                    <label class="block mb-1 font-medium">Satuan</label>
                    <input type="text" id="unitDisplay" name="unit" class="w-full border rounded-md p-2 bg-gray-50 text-gray-500 font-semibold uppercase text-xs" readonly placeholder="-">
                </div>
            </div>

            {{-- JUMLAH --}}
            <div>
                <label id="amountLabel" class="block mb-1 font-medium">Jumlah</label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 text-sm">
                        Rp
                    </span>
                    <input
                        type="text"
                        id="amount"
                        name="amount"
                        inputmode="numeric"
                        autocomplete="off"
                        placeholder="0"
                        class="w-full border rounded-md pl-10 pr-3 py-2 text-sm"
                    >
                </div>
            </div>

            {{-- TANGGAL --}}
            <div>
                <label class="block mb-1 font-medium">Tanggal</label>
                <input type="date" name="transaction_date" class="w-full border rounded-md p-2">
            </div>

            {{-- CATATAN --}}
            <div>
                <label class="block mb-1 font-medium">
                    Catatan <span class="italic text-xs text-gray-400">(tidak wajib)</span>
                </label>
                <textarea name="note" rows="2" class="w-full border rounded-md p-2" placeholder="Tulis catatan jika ada"></textarea>
            </div>

            {{-- UNGGAH GAMBAR --}}
            <div id="imageInputSection">
                <label class="block mb-1 font-medium">
                    Unggah Gambar <span class="italic text-xs text-gray-400">(tidak wajib)</span>
                </label>
                <select id="imageSource" class="w-full border rounded-md p-2 text-sm mb-2">
                    <option value="">Pilih sumber gambar</option>
                    <option value="camera" id="cameraOption">Ambil Foto</option>
                    <option value="file">Pilih dari Perangkat</option>
                </select>
                <input type="file" id="imageInput" name="image" accept="image/*" class="hidden">
                <div id="imagePreviewWrapper" class="relative hidden mt-2">
                    <img id="imagePreview" class="w-full max-h-48 object-cover rounded-lg border">
                    <button type="button" id="removeImage" class="absolute top-1 right-1 bg-black/60 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs">✕</button>
                </div>
            </div>

            <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded-lg font-semibold">
                Simpan
            </button>
        </form>
    </div>

    {{-- DAFTAR TRANSAKSI --}}
    <div class="md:col-span-8 bg-white p-4 rounded-xl shadow-sm">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-4">
            <h2 class="font-semibold text-sm">Daftar Transaksi</h2>
            <form method="GET" class="flex flex-wrap items-center gap-2 text-sm">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari kategori" class="border rounded-md px-3 py-2 text-sm" oninput="this.form.submit()">
                <div class="flex items-center gap-2 border rounded-md px-3 py-2 bg-gray-50">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <input type="date" name="start_date" value="{{ request('start_date') }}" class="bg-transparent outline-none text-xs">
                    <span class="text-gray-400">–</span>
                    <input type="date" name="end_date" value="{{ request('end_date') }}" class="bg-transparent outline-none text-xs">
                </div>
                <button class="bg-blue-600 text-white px-3 py-2 rounded-md">Terapkan</button>
            </form>
        </div>

        {{-- HEADERS TABEL --}}
        <div class="grid grid-cols-11 text-xs font-semibold text-gray-700 border-b border-gray-200 pb-2 mb-3">
            <div class="col-span-4">Kategori / Menu</div>
            <div class="col-span-2">Tanggal</div>
            <div class="col-span-2">Jumlah</div>
            <div class="col-span-1">Catatan</div>
            <div class="col-span-2 text-center">Aksi</div>
        </div>

        {{-- ISI DAFTAR TRANSAKSI --}}
        <div class="space-y-3 text-sm">
            @forelse ($transactions as $trx)
                <div class="grid grid-cols-11 items-center border-b pb-2">
                    
                    {{-- KOLOM KATEGORI + BADGE QUANTITY SEJAJAR --}}
                    <div class="col-span-4 flex items-center flex-wrap gap-1.5 justify-start">
                        <div class="flex items-center gap-2 font-medium text-gray-800">
                            <svg class="w-4 h-4 {{ $trx->type === 'income' ? 'text-green-500' : 'text-red-500 rotate-90' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7 17L17 7M7 7h10v10" />
                            </svg>
                            <span>{{ $trx->category }}</span>
                        </div>
                        
                        @if($trx->type === 'income' && isset($trx->quantity))
                            <span class="text-[10px] bg-green-50 text-green-700 px-1.5 py-0.5 rounded border border-green-200 font-bold uppercase tracking-wider">
                                {{ $trx->quantity }} {{ $trx->unit ?? 'pcs' }}
                            </span>
                        @endif
                    </div>

                    <div class="col-span-2 text-gray-600 text-xs md:text-sm">
                        {{ \Carbon\Carbon::parse($trx->transaction_date)->translatedFormat('d M Y') }}
                    </div>

                    <div class="col-span-2 font-medium">
                        Rp {{ number_format($trx->amount, 0, ',', '.') }}
                    </div>

                    <div class="col-span-1 text-xs text-gray-700 truncate" title="{{ $trx->note }}">
                        {{ $trx->note ?? '-' }}
                    </div>

                    <div class="col-span-2 flex justify-end gap-2 whitespace-nowrap">
                        @if($trx->image || $trx->note)
                        <button type="button" onclick="openImage(this)" class="text-gray-400 hover:text-gray-600 transition" data-image="{{ $trx->image }}" data-note="{{ e($trx->note) }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5 c4.478 0 8.268 2.943 9.542 7 -1.274 4.057 -5.064 7 -9.542 7 -4.477 0 0 -8.268 -2.943 -9.542 -7z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </button>
                        @endif
                        
                        <button type="button" class="text-gray-400 hover:text-blue-600 transition" 
                                data-id="{{ $trx->id }}" 
                                data-type="{{ $trx->type }}" 
                                data-category="{{ $trx->category }}" 
                                data-amount="{{ $trx->amount }}" 
                                data-date="{{ $trx->transaction_date }}" 
                                data-quantity="{{ $trx->quantity ?? 1 }}"
                                data-unit="{{ $trx->unit ?? '' }}"
                                data-note="{{ e($trx->note) }}" 
                                data-image="{{ $trx->image ?? '' }}" 
                                onclick="openEdit(this)">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16.862 4.487a2.1 2.1 0 113.03 2.9L7.5 19.78 3 21l1.22-4.5 12.642-12.013z" />
                            </svg>
                        </button>
                        
                        <form method="POST" action="{{ route('transactions.destroy', $trx->id) }}" onsubmit="return confirm('Hapus transaksi ini?')">
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
                <p class="text-center text-gray-500 text-sm py-4">Tidak ada transaksi</p>
            @endforelse
        </div>

        <div class="mt-4">
            <p class="text-xs text-gray-500 mb-2">
                Menampilkan {{ $transactions->firstItem() }} – {{ $transactions->lastItem() }} dari {{ $transactions->total() }} transaksi
            </p>
            {{ $transactions->links() }}
        </div>
    </div>
</div>

{{-- MODAL IMAGE --}}
<div id="imageModal" class="fixed inset-0 bg-black/60 hidden items-center justify-center z-50">
    <div class="bg-white p-4 rounded-lg max-w-md w-full relative">
        <button onclick="closeImage()" class="absolute top-2 right-3 text-xl">✕</button>
        <img id="modalImage" class="w-full rounded mb-3">
        <div><p class="text-xs text-gray-500 mb-1">Catatan</p><p id="modalNote" class="text-sm text-gray-800 whitespace-pre-line"></p></div>
    </div>
</div>

{{-- MODAL EDIT TRANSAKSI (SUDAN TEXT RUPIAH DAN MENYIMPAN QTY) --}}
<div id="editModal" class="fixed inset-0 bg-black/60 hidden items-center justify-center z-50">
    <div class="bg-white rounded-xl max-w-md w-full p-4 relative">
        <button onclick="closeEdit()" class="absolute top-3 right-4 text-xl text-gray-500">✕</button>
        <h3 class="font-semibold text-sm mb-4">Edit Transaksi</h3>
        <form id="editForm" method="POST" enctype="multipart/form-data" class="space-y-3 text-sm">
            @csrf @method('PUT')
            
            <div>
                <label class="block mb-1 font-medium">Tipe</label>
                <select id="editType" name="type" class="w-full border rounded-md p-2">
                    <option value="income">Pendapatan</option>
                    <option value="expense">Pengeluaran</option>
                </select>
            </div>
            
            <div>
                <label class="block mb-1 font-medium">Kategori / Produk</label>
                <select id="editCategory" name="category" class="w-full border rounded-md p-2" required>
                    {{-- Opsi dinamis --}}
                </select>
            </div>

            {{-- FIELD KUANTITAS & SATUAN KHUSUS MODAL EDIT --}}
            <div id="editQuantityWrapper" class="grid grid-cols-2 gap-2 hidden">
                <div>
                    <label class="block mb-1 font-medium">Kuantitas</label>
                    <input type="number" id="editQuantity" name="quantity" min="1" class="w-full border rounded-md p-2">
                </div>
                <div>
                    <label class="block mb-1 font-medium">Satuan</label>
                    <input type="text" id="editUnitDisplay" name="unit" class="w-full border rounded-md p-2 bg-gray-50 text-gray-500 font-semibold uppercase text-xs" readonly placeholder="-">
                </div>
            </div>

            {{-- GANTI JADI TYPE="TEXT" AGAR FORMAT TITIK RUPIAH BISA BERJALAN --}}
            <div>
                <label id="editAmountLabel" class="block mb-1 font-medium">Jumlah</label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">Rp</span>
                    <input id="editAmount" name="amount" type="text" inputmode="numeric" class="w-full border rounded-md pl-10 pr-3 py-2" required>
                </div>
            </div>
            
            <div>
                <label class="block mb-1 font-medium">Tanggal</label>
                <input id="editDate" name="transaction_date" type="date" class="w-full border rounded-md p-2" required>
            </div>
            
            <div>
                <label class="block mb-1 font-medium">Catatan</label>
                <textarea id="editNote" name="note" rows="3" class="w-full border rounded-md p-2"></textarea>
            </div>
            
            <div>
                <label class="block mb-1 font-medium">Gambar (opsional)</label>
                <img id="editImagePreview" class="w-full max-h-40 object-cover rounded mb-2 hidden">
                <input type="file" name="image" accept="image/*" class="w-full border rounded-md p-2">
                <label class="flex items-center gap-2 text-xs mt-2"><input type="checkbox" name="remove_image" value="1"> Hapus gambar</label>
            </div>
            
            <button class="w-full bg-blue-600 text-white py-2 rounded-lg font-semibold">Simpan Perubahan</button>
        </form>
    </div>
</div>

<script>
const customIncomeCategories = @json($customIncomeCategories) || [];

const expenseCategories = [
    "Bahan Baku", "Operasional", "Gaji Karyawan", "Marketing", "Transportasi",
    "Sewa Tempat", "Utilitas (Listrik, Air, Internet)", "Asuransi", "Maintenance", "Lainnya"
];

// 1. HYBRID LOGIC FORM TAMBAH TRANSAKSI
document.getElementById('type').addEventListener('change', function() {
    const type = this.value;
    const categorySelect = document.getElementById('category');
    const qtyWrapper = document.getElementById('quantityWrapper');
    const amountInput = document.getElementById('amount');
    const amountLabel = document.getElementById('amountLabel');
    const imageInputSection = document.getElementById('imageInputSection');

    categorySelect.innerHTML = '<option value="">Pilih Kategori</option>';
    document.getElementById('unitDisplay').value = '-';
    document.getElementById('quantity').value = 1;

    if (type === 'income') {
        categorySelect.disabled = false;
        if (qtyWrapper) qtyWrapper.classList.remove('hidden');
        amountInput.readOnly = true;
        if (amountLabel) amountLabel.innerText = "Jumlah";
        if (imageInputSection) imageInputSection.classList.add('hidden');

        if (customIncomeCategories.length === 0) {
            const option = document.createElement('option');
            option.value = "";
            option.textContent = "-- Belum ada menu custom, silakan kelola menu dahulu --";
            categorySelect.appendChild(option);
        } else {
            customIncomeCategories.forEach(cat => {
                const option = document.createElement('option');
                option.value = cat.name;
                option.textContent = cat.name;
                option.setAttribute('data-price', cat.default_price);
                option.setAttribute('data-unit', cat.unit);
                categorySelect.appendChild(option);
            });
        }
    } else if (type === 'expense') {
        categorySelect.disabled = false;
        if (qtyWrapper) qtyWrapper.classList.add('hidden');
        amountInput.readOnly = false;
        amountInput.value = '';
        if (amountLabel) amountLabel.innerText = "Jumlah";
        if (imageInputSection) imageInputSection.classList.remove('hidden');

        expenseCategories.forEach(cat => {
            const option = document.createElement('option');
            option.value = cat;
            option.textContent = cat;
            categorySelect.appendChild(option);
        });
    } else {
        categorySelect.disabled = true;
        if (qtyWrapper) qtyWrapper.classList.add('hidden');
    }
});

document.getElementById('category').addEventListener('change', calculateTotal);
document.getElementById('quantity').addEventListener('input', calculateTotal);

function calculateTotal() {
    const type = document.getElementById('type').value;
    if (type !== 'income') return;

    const categorySelect = document.getElementById('category');
    const selectedOption = categorySelect.options[categorySelect.selectedIndex];
    const qty = parseFloat(document.getElementById('quantity').value) || 1;
    const amountInput = document.getElementById('amount');
    const unitDisplay = document.getElementById('unitDisplay');

    if (selectedOption && selectedOption.value !== '') {
        const price = parseFloat(selectedOption.getAttribute('data-price')) || 0;
        const unit = selectedOption.getAttribute('data-unit') || 'pcs';
        
        if (unitDisplay) unitDisplay.value = unit;
        let total = price * qty;
        amountInput.value = total.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    } else {
        amountInput.value = '0';
        if (unitDisplay) unitDisplay.value = '-';
    }
}

// 2. LOGIKA INTERAKTIF KHUSUS MODAL EDIT TRANSAKSI
function openEdit(el) {
    const type = el.dataset.type;
    const currentCategory = el.dataset.category;
    const qty = el.dataset.quantity || 1;

    document.getElementById('editType').value = type;
    document.getElementById('editDate').value = el.dataset.date;
    document.getElementById('editNote').value = el.dataset.note || '';
    document.getElementById('editQuantity').value = qty;
    
    const form = document.getElementById('editForm');
    form.action = `/transactions/${el.dataset.id}`;

    renderEditCategories(type, currentCategory);
    toggleEditFields(type);

    if (type === 'expense') {
        let rawAmount = el.dataset.amount ? Math.round(el.dataset.amount).toString() : '';
        document.getElementById('editAmount').value = rawAmount.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    } else {
        setTimeout(calculateEditTotal, 50); 
    }

    const img = document.getElementById('editImagePreview');
    if (el.dataset.image) { img.src = el.dataset.image; img.classList.remove('hidden'); } else { img.classList.add('hidden'); }

    const modal = document.getElementById('editModal');
    modal.classList.remove('hidden'); modal.classList.add('flex');
}

function renderEditCategories(type, selectedValue = '') {
    const editCategorySelect = document.getElementById('editCategory');
    editCategorySelect.innerHTML = '<option value="">Pilih Kategori</option>';

    if (type === 'income') {
        customIncomeCategories.forEach(cat => {
            const option = document.createElement('option');
            option.value = cat.name;
            option.textContent = cat.name;
            option.setAttribute('data-price', cat.default_price);
            option.setAttribute('data-unit', cat.unit);
            if (cat.name === selectedValue) option.selected = true;
            editCategorySelect.appendChild(option);
        });
    } else if (type === 'expense') {
        expenseCategories.forEach(cat => {
            const option = document.createElement('option');
            option.value = cat;
            option.textContent = cat;
            if (cat === selectedValue) option.selected = true;
            editCategorySelect.appendChild(option);
        });
    }
}

function toggleEditFields(type) {
    const qtyWrapper = document.getElementById('editQuantityWrapper');
    const amountInput = document.getElementById('editAmount');
    const amountLabel = document.getElementById('editAmountLabel');

    if (type === 'income') {
        if (qtyWrapper) qtyWrapper.classList.remove('hidden');
        amountInput.readOnly = true;
        if (amountLabel) amountLabel.innerText = "Jumlah";
    } else {
        if (qtyWrapper) qtyWrapper.classList.add('hidden');
        amountInput.readOnly = false;
        if (amountLabel) amountLabel.innerText = "Jumlah";
    }
}

document.getElementById('editType').addEventListener('change', function() {
    renderEditCategories(this.value);
    toggleEditFields(this.value);
    calculateEditTotal();
});
document.getElementById('editCategory').addEventListener('change', calculateEditTotal);
document.getElementById('editQuantity').addEventListener('input', calculateEditTotal);

// Input mask rupiah manual jika user mengetik di modal pengeluaran
document.getElementById('editAmount').addEventListener('input', function() {
    let value = this.value.replace(/[^0-9]/g, '');
    this.value = value ? value.replace(/\B(?=(\d{3})+(?!\d))/g, '.') : '';
});

function calculateEditTotal() {
    const type = document.getElementById('editType').value;
    if (type !== 'income') return;

    const categorySelect = document.getElementById('editCategory');
    const selectedOption = categorySelect.options[categorySelect.selectedIndex];
    const qty = parseFloat(document.getElementById('editQuantity').value) || 1;
    const amountInput = document.getElementById('editAmount');
    const unitDisplay = document.getElementById('editUnitDisplay');

    if (selectedOption && selectedOption.value !== '') {
        const price = parseFloat(selectedOption.getAttribute('data-price')) || 0;
        const unit = selectedOption.getAttribute('data-unit') || 'pcs';
        
        if (unitDisplay) unitDisplay.value = unit;
        let total = price * qty;
        // FORMAT FORMAT RUPIAH DENGAN TITIK (.)
        amountInput.value = total.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    } else {
        amountInput.value = '0';
        if (unitDisplay) unitDisplay.value = '-';
    }
}

// Tambahkan intercept form edit agar titik rupiah dibersihkan sebelum dikirim ke server Laravel
document.getElementById('editForm').addEventListener('submit', function() {
    const amountInput = document.getElementById('editAmount');
    if (amountInput) {
        amountInput.value = amountInput.value.replace(/\./g, '');
    }
});

function closeImage() { document.getElementById('imageModal').classList.add('hidden'); }
function closeEdit() { document.getElementById('editModal').classList.add('hidden'); }
</script>
@endsection

@push('scripts')
    @vite('resources/js/transactions.js')
@endpush