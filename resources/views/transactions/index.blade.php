@extends('layouts.app')
@section('title', 'Daftar Transaksi')

@section('content')
<div id="business-metadata" data-type="{{ auth()->user()->business_type }}" class="hidden"></div>

<div class="flex items-center justify-between mb-3">
    <h1 class="text-lg md:text-xl font-semibold">Transaksi</h1>
    <span class="text-[10px] bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full font-bold uppercase">
        {{ auth()->user()->business_type }}
    </span>
</div>

@include('layouts.summary')

{{-- CONTENT --}}
<div class="bg-white p-4 rounded-xl shadow-sm w-full">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-4">
        <h2 class="font-semibold text-sm">Daftar Transaksi</h2>
        <form method="GET" class="flex flex-wrap items-center gap-2 text-sm">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Transaksi" class="border rounded-md px-3 py-2 text-sm" oninput="this.form.submit()">
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
        <div class="col-span-4">Item</div>
        <div class="col-span-2">Tanggal & Waktu</div>
        <div class="col-span-2">Jumlah</div>
        <div class="col-span-1">Catatan</div>
        <div class="col-span-2 text-center">Aksi</div>
    </div>

    {{-- ISI DAFTAR TRANSAKSI --}}
    <div class="space-y-3 text-sm">
        @if(is_array($transactions) || $transactions instanceof \Illuminate\Support\Collection || $transactions->count() > 0)
            @foreach ($transactions as $trx)
                <div class="grid grid-cols-11 items-center border-b pb-2">
                    <div class="col-span-4 flex items-center flex-wrap gap-1.5 justify-start">
                        <div class="flex items-center gap-2 font-medium text-gray-800">
                            <svg class="w-4 h-4 {{ $trx->type === 'income' ? 'text-green-500' : 'text-red-500 rotate-90' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7 17L17 7M7 7h10v10" />
                            </svg>
                            <span class="truncate max-w-[200px] md:max-w-[340px]" title="{{ $trx->items_summary }}">{{ $trx->items_summary }}</span>
                        </div>
                    </div>

                    <div class="col-span-2 text-gray-600 text-xs md:text-sm flex flex-col justify-center">
                        <span>{{ \Carbon\Carbon::parse($trx->transaction_date)->translatedFormat('d M Y') }}</span>
                        {{-- MENAMPILKAN JAM OTOMATIS DI BAWAH TANGGAL --}}
                        <span class="text-[10px] text-gray-400 font-medium mt-0.5">
                            {{ $trx->transaction_time ? \Carbon\Carbon::parse($trx->transaction_time)->format('H:i') : '00:00' }}
                        </span>
                    </div>

                    <div class="col-span-2 font-medium">
                        Rp {{ number_format($trx->total_amount, 0, ',', '.') }}
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
                        
                        {{-- Tombol Edit Menggunakan AJAX Group --}}
                        <button type="button" class="text-gray-400 hover:text-blue-600 transition" onclick="openEdit('{{ $trx->id }}')">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16.862 4.487a2.1 2.1 0 113.03 2.9L7.5 19.78 3 21l1.22-4.5 12.642-12.013z" />
                            </svg>
                        </button>
                        
                        <form method="POST" action="{{ route('transactions.destroy', $trx->id) }}" onsubmit="return confirm('Peringatan! Menghapus transaksi ini akan menghapus seluruh item dalam grup nota ini. Lanjutkan?')">
                            @csrf @method('DELETE')
                            <button class="text-gray-400 hover:text-red-600 transition">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 7h12M9 7V4h6v3m-7 3v7m4-7v7m4-7v7M6 7l1 13h10l1-13" />
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        @else
            <p class="text-center text-gray-500 text-sm py-4">Tidak ada transaksi</p>
        @endif
    </div>

    @if(!is_array($transactions) && $transactions instanceof \Illuminate\Pagination\LengthAwarePaginator)
    <div class="mt-4">
        <p class="text-xs text-gray-500 mb-2">
            Menampilkan {{ $transactions->firstItem() }} – {{ $transactions->lastItem() }} dari {{ $transactions->total() }} transaksi
        </p>
        {{ $transactions->links() }}
    </div>
    @endif
</div>

{{-- MODAL IMAGE PREVIEW --}}
<div id="imageModal" class="fixed inset-0 bg-black/60 hidden items-center justify-center z-50">
    <div class="bg-white p-4 rounded-lg max-w-md w-full relative">
        <button onclick="closeImage()" class="absolute top-2 right-3 text-xl">✕</button>
        <img id="modalImage" class="w-full rounded mb-3">
        <div><p class="text-xs text-gray-500 mb-1">Catatan</p><p id="modalNote" class="text-sm text-gray-800 whitespace-pre-line"></p></div>
    </div>
</div>

{{-- MODAL EDIT TRANSAKSI LIVE MULTI-ITEM REPEATER --}}
<div id="editModal" class="fixed inset-0 bg-black/60 hidden items-center justify-center z-50 overflow-y-auto">
    <div class="bg-white rounded-xl max-w-2xl w-full p-5 relative my-8 mx-4">
        <button onclick="closeEdit()" class="absolute top-3 right-4 text-xl text-gray-500">✕</button>
        <h3 class="font-semibold text-base mb-4 border-b pb-2">Edit Transaksi (Satu Nota)</h3>
        
        <form id="editForm" method="POST" enctype="multipart/form-data" class="space-y-4 text-sm">
            @csrf @method('PUT')
            
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block mb-1 font-medium text-gray-700">Tipe Transaksi</label>
                    <select id="editType" name="type" class="w-full border rounded-md p-2 bg-gray-50 font-medium">
                        <option value="income">Pendapatan</option>
                        <option value="expense">Pengeluaran</option>
                    </select>
                </div>
                <div>
                    <label class="block mb-1 font-medium text-gray-700">Tanggal</label>
                    <input id="editDate" name="transaction_date" type="date" class="w-full border rounded-md p-2" required>
                </div>
            </div>

            {{-- LIVE REPEATER CONTAINER PERSIS HALAMAN TAMBAH TRANSAKSI --}}
            <div>
                <div class="flex items-center justify-between mb-2">
                    <label class="font-semibold text-gray-700">Daftar Item / Produk</label>
                    <button type="button" onclick="addEditItemRow()" class="text-xs bg-blue-50 text-blue-600 px-2.5 py-1 rounded font-medium hover:bg-blue-100 transition">+ Tambah Item</button>
                </div>
                
                <div id="editItemsContainer" class="space-y-2 max-h-60 overflow-y-auto p-1 border rounded-lg bg-gray-50">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3 border-t pt-3">
                <div>
                    <label class="block mb-1 font-medium text-gray-700">Catatan Global</label>
                    <textarea id="editNote" name="note" rows="2" class="w-full border rounded-md p-2 text-xs" placeholder="Tambahkan keterangan nota..."></textarea>
                </div>
                <div>
                    <label class="block mb-1 font-medium text-gray-700">Gambar Bukti / Nota</label>
                    <img id="editImagePreview" class="w-full max-h-24 object-cover rounded mb-1 hidden border">
                    <input type="file" name="image" accept="image/*" class="w-full border rounded-md p-1.5 text-xs">
                    <label class="flex items-center gap-1.5 text-xs mt-1 text-red-500 font-medium cursor-pointer">
                        <input type="checkbox" name="remove_image" value="1"> Hapus berkas gambar saat ini
                    </label>
                </div>
            </div>
            
            <div class="flex items-center justify-between border-t pt-3 bg-blue-50/50 p-3 rounded-lg">
                <span class="font-semibold text-gray-700 text-sm">Total Akumulasi Nota:</span>
                <span id="editGrandTotalDisplay" class="font-bold text-base text-blue-700">Rp 0</span>
            </div>
            
            <button class="w-full bg-blue-600 text-white py-2.5 rounded-lg font-semibold shadow-sm hover:bg-blue-700 transition">Simpan Perubahan Nota</button>
        </form>
    </div>
</div>

<script>
const customIncomeCategories = @json($customIncomeCategories) || [];
const expenseCategories = ["Bahan Baku", "Operasional", "Gaji Karyawan", "Marketing", "Transportasi", "Sewa Tempat", "Utilitas (Listrik, Air, Internet)", "Asuransi", "Maintenance", "Lainnya"];
let editItemIndex = 0;

async function openEdit(id) {
    const container = document.getElementById('editItemsContainer');
    container.innerHTML = '<p class="text-center text-gray-400 py-4 text-xs">Memuat daftar item...</p>';
    
    const modal = document.getElementById('editModal');
    modal.classList.remove('hidden'); modal.classList.add('flex');

    try {
        const response = await fetch(`{{ route('transactions.index') }}/${id}/group-items`);
        const data = await response.json();
        
        document.getElementById('editType').value = data.main.type;
        document.getElementById('editDate').value = data.main.transaction_date;
        document.getElementById('editNote').value = data.main.note || '';
        
        const form = document.getElementById('editForm');
        form.action = `{{ route('transactions.index') }}/${data.main.id}`;

        const img = document.getElementById('editImagePreview');
        if (data.main.image) { img.src = data.main.image; img.classList.remove('hidden'); } else { img.classList.add('hidden'); }

        container.innerHTML = '';
        editItemIndex = 0;

        data.items.forEach(item => {
            addEditItemRow(item);
        });

        toggleEditTypeFields();

    } catch (err) {
        container.innerHTML = '<p class="text-center text-red-500 py-4 text-xs">Gagal memuat detail data grup.</p>';
    }
}

function addEditItemRow(itemData = null) {
    const container = document.getElementById('editItemsContainer');
    const type = document.getElementById('editType').value;
    const index = editItemIndex++;

    const row = document.createElement('div');
    row.className = "flex items-center gap-2 bg-white p-2 rounded-md shadow-sm border border-gray-200 edit-item-row";
    row.setAttribute('data-index', index);

    let categoryOptions = `<option value="">Pilih Kategori</option>`;
    if (type === 'income') {
        customIncomeCategories.forEach(c => {
            const isSelected = itemData && itemData.category === c.name ? 'selected' : '';
            categoryOptions += `<option value="${c.name}" data-price="${c.default_price}" data-unit="${c.unit}" ${isSelected}>${c.name}</option>`;
        });
    } else {
        expenseCategories.forEach(c => {
            const isSelected = itemData && itemData.category === c ? 'selected' : '';
            categoryOptions += `<option value="${c}" ${isSelected}>${c}</option>`;
        });
    }

    const qtyVal = itemData ? (itemData.quantity ?? 1) : 1;
    const unitVal = itemData ? (itemData.unit ?? '-') : '-';
    let rawAmount = itemData ? Math.round(itemData.amount) : 0;
    let formattedAmount = rawAmount > 0 ? rawAmount.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.') : '';

    row.innerHTML = `
        <div class="flex-1 grid grid-cols-12 gap-2">
            <div class="col-span-4">
                <select name="items[${index}][category]" class="w-full border rounded p-1.5 text-xs edit-row-category" required onchange="handleEditRowCategoryChange(this)">
                    ${categoryOptions}
                </select>
            </div>
            <div class="col-span-2 qty-field ${type === 'expense' ? 'hidden' : ''}">
                <input type="number" name="items[${index}][quantity]" value="${qtyVal}" min="1" class="w-full border rounded p-1.5 text-xs edit-row-qty" oninput="calculateEditRowTotal(this)">
            </div>
            <div class="col-span-2 unit-field ${type === 'expense' ? 'hidden' : ''}">
                <input type="text" name="items[${index}][unit]" value="${unitVal}" class="w-full border rounded p-1.5 text-xs bg-gray-50 text-gray-500 uppercase text-center font-semibold edit-row-unit" readonly>
            </div>
            <div class="col-span-4 amount-container">
                <div class="relative">
                    <span class="absolute left-1.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs">Rp</span>
                    <input type="text" name="items[${index}][amount]" value="${formattedAmount}" inputmode="numeric" class="w-full border rounded pl-6 pr-1.5 py-1.5 text-xs edit-row-amount" required ${type === 'income' ? 'readonly' : ''} oninput="formatRupiahInput(this)">
                </div>
            </div>
        </div>
        <button type="button" onclick="removeEditItemRow(this)" class="text-red-500 hover:text-red-700 p-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-7v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
        </button>
    `;

    container.appendChild(row);
    calculateGrandTotalEdit();
}

function removeEditItemRow(button) {
    const rows = document.querySelectorAll('.edit-item-row');
    if (rows.length <= 1) {
        alert('Minimal harus menyisakan 1 item di dalam nota!');
        return;
    }
    button.closest('.edit-item-row').remove();
    calculateGrandTotalEdit();
}

function toggleEditTypeFields() {
    const type = document.getElementById('editType').value;
    document.querySelectorAll('.edit-item-row').forEach(row => {
        const qtyF = row.querySelector('.qty-field');
        const unitF = row.querySelector('.unit-field');
        const amountI = row.querySelector('.edit-row-amount');
        const amtContainer = row.querySelector('.amount-container');
        
        if (type === 'income') {
            if(qtyF) qtyF.classList.remove('hidden');
            if(unitF) unitF.classList.remove('hidden');
            if(amountI) amountI.readOnly = true;
            if(amtContainer) { amtContainer.classList.remove('col-span-8'); amtContainer.classList.add('col-span-4'); }
        } else {
            if(qtyF) qtyF.classList.add('hidden');
            if(unitF) unitF.classList.add('hidden');
            if(amountI) amountI.readOnly = false;
            if(amtContainer) { amtContainer.classList.remove('col-span-4'); amtContainer.classList.add('col-span-8'); }
        }
    });
    calculateGrandTotalEdit();
}

document.getElementById('editType').addEventListener('change', function() {
    const container = document.getElementById('editItemsContainer');
    container.innerHTML = '';
    editItemIndex = 0;
    addEditItemRow();
    toggleEditTypeFields();
});

function handleEditRowCategoryChange(selectEl) {
    const type = document.getElementById('editType').value;
    const row = selectEl.closest('.edit-item-row');
    const opt = selectEl.options[selectEl.selectedIndex];
    
    if (type === 'income' && opt && opt.value !== '') {
        const price = parseFloat(opt.getAttribute('data-price')) || 0;
        const unit = opt.getAttribute('data-unit') || 'pcs';
        row.querySelector('.edit-row-unit').value = unit;
        row.setAttribute('data-base-price', price);
    } else {
        row.querySelector('.edit-row-unit').value = '-';
        row.setAttribute('data-base-price', 0);
    }
    
    calculateEditRowTotal(row.querySelector('.edit-row-qty'));
}

function calculateEditRowTotal(inputEl) {
    const row = inputEl.closest('.edit-item-row');
    const type = document.getElementById('editType').value;
    if (type !== 'income') { calculateGrandTotalEdit(); return; }

    const price = parseFloat(row.getAttribute('data-base-price')) || 0;
    const qty = parseFloat(row.querySelector('.edit-row-qty').value) || 1;
    const amountInput = row.querySelector('.edit-row-amount');
    
    let total = price * qty;
    amountInput.value = total.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    calculateGrandTotalEdit();
}

function formatRupiahInput(inputEl) {
    let value = inputEl.value.replace(/[^0-9]/g, '');
    inputEl.value = value ? value.replace(/\B(?=(\d{3})+(?!\d))/g, '.') : '';
    calculateGrandTotalEdit();
}

function calculateGrandTotalEdit() {
    let grandTotal = 0;
    document.querySelectorAll('.edit-row-amount').forEach(input => {
        let val = parseFloat(input.value.replace(/\./g, '')) || 0;
        grandTotal += val;
    });
    document.getElementById('editGrandTotalDisplay').innerText = 'Rp ' + grandTotal.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
}

document.getElementById('editForm').addEventListener('submit', function() {
    document.querySelectorAll('.edit-row-amount').forEach(input => {
        input.value = input.value.replace(/\./g, '');
    });
});

function openImage(el) {
    document.getElementById('modalImage').src = el.dataset.image;
    document.getElementById('modalNote').innerText = el.dataset.note || '-';
    const modal = document.getElementById('imageModal');
    modal.classList.remove('hidden'); modal.classList.add('flex');
}

function closeImage() { document.getElementById('imageModal').classList.add('hidden'); }
function closeEdit() { document.getElementById('editModal').classList.add('hidden'); }
</script>
@endsection

@push('scripts')
    @vite('resources/js/transactions.js')
@endpush