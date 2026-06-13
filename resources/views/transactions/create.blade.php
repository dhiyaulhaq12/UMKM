@extends('layouts.app')
@section('title', 'Tambah Transaksi')

@section('content')
<div id="business-metadata" data-type="{{ auth()->user()->business_type }}" class="hidden"></div>

<div class="flex items-center justify-between mb-3">
    <h1 class="text-lg md:text-xl font-semibold">
        Tambah Transaksi
    </h1>
</div>

<div class="grid grid-cols-1 md:grid-cols-12 gap-4">

    {{-- KANVAS UTAMA: CONTAINER INPUT FORM ITEM --}}
    <div class="md:col-span-5 bg-white p-4 rounded-xl shadow-sm">
        <div class="flex items-center justify-between mb-3">
            <span class="text-[10px] bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full font-bold uppercase">
                {{ auth()->user()->business_type }}
            </span>
        </div>

        {{-- AREA INDEPENDEN SIMPAN ITEM --}}
        <div class="space-y-3 text-sm">
            {{-- TIPE --}}
            <div>
                <label class="block mb-1 font-medium">Tipe</label>
                <select id="type" class="w-full border rounded-md p-2">
                    <option value="">Pilih Tipe</option>
                    <option value="income">Pendapatan</option>
                    <option value="expense">Pengeluaran</option>
                </select>
            </div>

            {{-- KATEGORI --}}
            <div>
                <label class="block mb-1 font-medium">Item</label>
                <select id="category" class="w-full border rounded-md p-2" disabled>
                    <option value="">Pilih Item</option>
                </select>
            </div>

            {{-- WRAPPER QUANTITY & SATUAN --}}
            <div id="quantityWrapper" class="grid grid-cols-2 gap-2 hidden">
                <div>
                    <label class="block mb-1 font-medium">Kuantitas</label>
                    <input type="number" id="quantity" value="1" min="1" class="w-full border rounded-md p-2">
                </div>
                <div>
                    <label class="block mb-1 font-medium">Satuan</label>
                    <input type="text" id="unitDisplay" class="w-full border rounded-md p-2 bg-gray-50 text-gray-500 font-semibold uppercase text-xs" readonly placeholder="-">
                </div>
            </div>

            {{-- JUMLAH --}}
            <div>
                <label id="amountLabel" class="block mb-1 font-medium">Jumlah</label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">Rp</span>
                    <input type="text" id="amount" inputmode="numeric" autocomplete="off" placeholder="0" class="w-full border rounded-md pl-10 pr-3 py-2 text-sm">
                </div>
            </div>

            <button type="button" onclick="addItemToBasket()" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg font-semibold transition">
                Tambah
            </button>
        </div>
    </div>

    {{-- KANVAS STRUK BELANJA & ATRIBUT NOTA GLOBAL --}}
    <div class="md:col-span-7 bg-white p-4 rounded-xl shadow-sm flex flex-col justify-between">
        <form method="POST" action="{{ route('transactions.store') }}" enctype="multipart/form-data" id="finalTransactionForm" class="space-y-4 text-sm h-full flex flex-col justify-between">
            @csrf
            {{-- Mengirimkan type transaksi secara hidden saat disubmit --}}
            <input type="hidden" name="type" id="hidden_type">

            <div>
                <h2 class="font-semibold text-sm mb-3 text-gray-800 border-b pb-1.5">Daftar Item Sesi Belanja</h2>
                
                {{-- TABEL CHEKOUT DAFTAR ITEM SEMENTARA --}}
                <div class="overflow-x-auto border rounded-xl mb-4 bg-gray-50/30">
                    <table class="w-full text-left text-xs" id="basketTable">
                        <thead class="bg-gray-50 text-gray-600 border-b uppercase font-semibold">
                            <tr>
                                <th class="px-3 py-2">Item</th>
                                <th class="px-3 py-2 text-center">Kuantitas</th>
                                <th class="px-3 py-2 text-right">Subtotal</th>
                                <th class="px-3 py-2 text-center w-12">Hapus</th>
                            </tr>
                        </thead>
                        <tbody id="basketTableBody" class="divide-y text-gray-700">
                            <tr id="emptyRow">
                                <td colspan="4" class="text-center py-8 text-gray-400 italic">Belum ada item yang disematkan ke dalam list.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                {{-- FORM DATA DOKUMEN GLOBAL ASLI MILIKMU (BERSIH TANPA INPUT JAM) --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 bg-gray-50 p-3 rounded-xl border border-gray-100">
                    <div>
                        <label class="block mb-1 font-medium text-xs text-gray-600">Tanggal Transaksi</label>
                        <input type="date" name="transaction_date" value="{{ date('Y-m-d') }}" required class="w-full border bg-white rounded-md p-2 text-xs">
                    </div>
                    <div>
                        <label class="block mb-1 font-medium text-xs text-gray-600">Catatan <span class="italic text-gray-400">(opsional)</span></label>
                        <textarea name="note" rows="1" class="w-full border bg-white rounded-md p-2 text-xs" placeholder="Catatan global"></textarea>
                    </div>
                    <div class="sm:col-span-2" id="imageInputSection">
                        <label class="block mb-1 font-medium text-xs text-gray-600">Unggah Gambar <span class="italic text-gray-400">(opsional)</span></label>
                        <select id="imageSource" class="w-full border bg-white rounded-md p-1.5 text-xs mb-1">
                            <option value="">Pilih sumber gambar</option>
                            <option value="camera" id="cameraOption">Ambil Foto</option>
                            <option value="file">Pilih dari Perangkat</option>
                        </select>
                        <input type="file" id="imageInput" name="image" accept="image/*" class="hidden">
                        <div id="imagePreviewWrapper" class="relative hidden mt-1">
                            <img id="imagePreview" class="w-full max-h-24 object-cover rounded-lg border">
                            <button type="button" id="removeImage" class="absolute top-1 right-1 bg-black/60 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs">✕</button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- TOMBOL SUBMIT GABUNGAN --}}
            <div class="pt-3 border-t flex justify-end gap-2">
                <a href="{{ route('transactions.index') }}" class="px-4 py-2 text-gray-500 hover:underline text-xs">Batal</a>
                <button type="submit" id="submitAllBtn" disabled class="bg-green-600 disabled:bg-gray-300 disabled:cursor-not-allowed hover:bg-green-700 text-white py-2 px-5 rounded-lg font-semibold transition text-xs shadow-sm">
                    Simpan 
                </button>
            </div>
        </form>
    </div>
</div>

<script>
const customIncomeCategories = @json($customIncomeCategories) || [];
const expenseCategories = ["Bahan Baku", "Operasional", "Gaji Karyawan", "Marketing", "Transportasi", "Sewa Tempat", "Utilitas (Listrik, Air, Internet)", "Asuransi", "Maintenance", "Lainnya"];
let itemBasketIndex = 0;

document.getElementById('type').addEventListener('change', function() {
    const type = this.value;
    const categorySelect = document.getElementById('category');
    const qtyWrapper = document.getElementById('quantityWrapper');
    const amountInput = document.getElementById('amount');
    const amountLabel = document.getElementById('amountLabel');
    const imageInputSection = document.getElementById('imageInputSection');

    categorySelect.innerHTML = '<option value="">Pilih Item</option>';
    document.getElementById('unitDisplay').value = '-';
    document.getElementById('quantity').value = 1;
    document.getElementById('basketTableBody').innerHTML = '<tr id="emptyRow"><td colspan="4" class="text-center py-8 text-gray-400 italic">Belum ada item yang disematkan ke dalam list.</td></tr>';
    checkBasketStatus();

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

document.getElementById('amount').addEventListener('input', function() {
    if(document.getElementById('type').value !== 'expense') return;
    let value = this.value.replace(/[^0-9]/g, '');
    this.value = value ? value.replace(/\B(?=(\d{3})+(?!\d))/g, '.') : '';
});

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

function addItemToBasket() {
    const type = document.getElementById('type').value;
    const catSelect = document.getElementById('category');
    const category = catSelect.value;
    const amtInput = document.getElementById('amount');

    if (!type || !category) {
        alert('Mohon pilih tipe dan Item produk terlebih dahulu, Bro!');
        return;
    }

    let amount = parseFloat(amtInput.value.replace(/\./g, '')) || 0;
    let quantity = 1;
    let unit = 'pcs';

    if (type === 'income') {
        quantity = parseFloat(document.getElementById('quantity').value) || 1;
        const opt = catSelect.options[catSelect.selectedIndex];
        unit = opt.getAttribute('data-unit') || 'pcs';
    }

    if (amount <= 0) {
        alert('Nominal jumlah transaksi tidak boleh kosong!');
        return;
    }

    const emptyRow = document.getElementById('emptyRow');
    if (emptyRow) emptyRow.remove();

    const tbody = document.getElementById('basketTableBody');
    const rowHtml = `
        <tr class="hover:bg-gray-50/50" id="basket_row_${itemBasketIndex}">
            <td class="px-3 py-2.5 font-medium text-gray-800">
                ${category}
                <input type="hidden" name="items[${itemBasketIndex}][category]" value="${category}">
            </td>
            <td class="px-3 py-2.5 text-center text-gray-500 font-semibold">
                ${type === 'income' ? `${quantity} ${unit}` : '-'}
                <input type="hidden" name="items[${itemBasketIndex}][quantity]" value="${quantity}">
                <input type="hidden" name="items[${itemBasketIndex}][unit]" value="${unit}">
            </td>
            <td class="px-3 py-2.5 text-right font-bold text-gray-800">
                Rp ${new Intl.NumberFormat('id-ID').format(amount)}
                <input type="hidden" name="items[${itemBasketIndex}][amount]" value="${amount}">
            </td>
            <td class="px-3 py-2.5 text-center">
                <button type="button" onclick="document.getElementById('basket_row_${itemBasketIndex}').remove(); checkBasketStatus();" class="text-red-500 font-bold hover:text-red-700">✕</button>
            </td>
        </tr>
    `;

    tbody.insertAdjacentHTML('beforeend', rowHtml);
    itemBasketIndex++;

    catSelect.value = '';
    document.getElementById('quantity').value = 1;
    document.getElementById('unitDisplay').value = '-';
    amtInput.value = '';

    checkBasketStatus();
}

function checkBasketStatus() {
    const tbody = document.getElementById('basketTableBody');
    const submitBtn = document.getElementById('submitAllBtn');
    const typeSelect = document.getElementById('type');
    const hiddenType = document.getElementById('hidden_type');

    if (tbody.children.length === 0 || tbody.querySelector('#emptyRow')) {
        submitBtn.disabled = true;
        typeSelect.disabled = false;
        hiddenType.value = '';
    } else {
        submitBtn.disabled = false;
        typeSelect.disabled = true; 
        hiddenType.value = typeSelect.value;
    }
}
</script>
@endsection