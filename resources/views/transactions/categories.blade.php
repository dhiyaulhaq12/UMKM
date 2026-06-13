@extends('layouts.app')
@section('title', 'Kelola Sumber Pendapatan')

@section('content')

<div class="flex items-center justify-between mb-3">
    <h1 class="text-lg md:text-xl font-semibold">
        Kelola Sumber Pendapatan 
    </h1>
    <a href="{{ route('transactions.index') }}" class="text-xs bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-1.5 rounded-lg font-medium transition">
        ← Kembali ke Transaksi
    </a>
</div>

<div class="grid grid-cols-1 md:grid-cols-12 gap-4">

    {{-- FORM TAMBAH MASTER SUMBER PENDAPATAN --}}
    <div class="md:col-span-4 bg-white p-4 rounded-xl shadow-sm h-fit">
        <h2 class="font-semibold text-sm mb-3">Tambah Sumber Pendapatan</h2>
        
        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-3 py-2 rounded-md text-xs mb-3">
                <ul class="list-disc pl-4">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('custom-categories.store') }}" class="space-y-3 text-sm">
            @csrf
            
            {{-- INPUT HIDDEN: Otomatis memaksa sistem menyimpan sebagai income/pendapatan --}}
            <input type="hidden" name="type" value="income">

            {{-- NAMA SUMBER PENDAPATAN --}}
            <div>
                <label class="block mb-1 font-medium">Nama Sumber Pendapatan / Item Usaha</label>
                <input name="name" type="text" class="w-full border rounded-md p-2" placeholder="" required>
            </div>

            {{-- HARGA DEFAULT --}}
            <div>
                <label class="block mb-1 font-medium">Harga</label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">Rp</span>
                    <input type="text" id="amount" name="default_price" inputmode="numeric" class="w-full border rounded-md pl-10 pr-3 py-2" placeholder="0" required>
                </div>
            </div>

            {{-- SATUAN --}}
            <div>
                <label class="block mb-1 font-medium">Satuan</label>
                <select id="unitSelect" name="unit" class="w-full border rounded-md p-2" required onchange="toggleCustomUnit()">
                    <option value="pcs">pcs</option>
                    <option value="porsi">porsi</option>
                    <option value="unit">unit</option>
                    <option value="kg">kg</option>
                    <option value="liter">liter</option>
                    <option value="box">box</option>
                    <option value="bulan">bulan</option>
                    <option value="custom">Lainnya (Kustom)</option>
                </select>
            </div>

            {{-- INPUT KUSTOM SATUAN --}}
            <div id="customUnitWrapper" class="hidden mt-2 animate-fade-in">
                <label class="block mb-1 text-xs font-medium text-gray-500">Masukkan Nama Satuan Baru</label>
                <input type="text" id="customUnitInput" placeholder="Contoh: ikat, jam, paket, pasang" class="w-full border rounded-md p-2 bg-gray-50 uppercase text-xs font-semibold placeholder:normal-case tracking-wider">
            </div>

            <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded-lg font-semibold hover:bg-blue-700 transition">
                Simpan Sumber Pendapatan
            </button>
        </form>
    </div>

    {{-- DAFTAR MASTER SUMBER PENDAPATAN --}}
    <div class="md:col-span-8 bg-white p-4 rounded-xl shadow-sm">
        <h2 class="font-semibold text-sm mb-4">Daftar Sumber Pendapatan Terdaftar</h2>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="text-xs uppercase bg-gray-50 text-gray-600">
                    <tr>
                        <th class="px-3 py-2">Nama Sumber Pendapatan / Item</th>
                        <th class="px-3 py-2">Harga</th>
                        <th class="px-3 py-2">Satuan</th>
                        <th class="px-3 py-2 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($categories as $cat)
                    <tr>
                        <td class="px-3 py-3 font-medium text-gray-800">
                            {{ $cat->name }}
                        </td>
                        <td class="px-3 py-3 font-semibold text-gray-700">
                            Rp {{ number_format($cat->default_price, 0, ',', '.') }}
                        </td>
                        <td class="px-3 py-3 text-gray-500">
                            / {{ $cat->unit }}
                        </td>
                        <td class="px-3 py-3 text-center">
                            <div class="flex items-center justify-center gap-3">
                                {{-- BUTTON EDIT --}}
                                <button type="button" class="text-gray-400 hover:text-blue-600 transition"
                                        data-id="{{ $cat->id }}"
                                        data-type="{{ $cat->type }}"
                                        data-name="{{ $cat->name }}"
                                        data-price="{{ $cat->default_price }}"
                                        data-unit="{{ $cat->unit }}"
                                        onclick="openEditCategory(this)">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16.862 4.487a2.1 2.1 0 113.03 2.9L7.5 19.78 3 21l1.22-4.5 12.642-12.013z" />
                                    </svg>
                                </button>

                                {{-- TOMBOL HAPUS --}}
                                <form action="{{ route('custom-categories.destroy', $cat->id) }}" method="POST" onsubmit="return confirm('Hapus sumber pendapatan ini?')" class="inline">
                                    @csrf @method('DELETE')
                                    <button class="text-gray-400 hover:text-red-600 transition flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-3 py-8 text-center text-gray-400">Belum ada data sumber pendapatan yang dibuat.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- ⬇️ BARIS TAMBAHAN BARU: NAVIGASI LINKS HALAMAN PAGINATION ⬇️ --}}
        @if ($categories->hasPages())
            <div class="mt-4 border-t pt-3">
                <p class="text-xs text-gray-500 mb-2">
                    Menampilkan {{ $categories->firstItem() }} – {{ $categories->lastItem() }} dari {{ $categories->total() }} item
                </p>
                {{ $categories->links() }}
            </div>
        @endif

    </div>
</div>

{{-- MODAL EDIT MASTER SUMBER PENDAPATAN --}}
<div id="editCategoryModal" class="fixed inset-0 bg-black/60 hidden items-center justify-center z-50">
    <div class="bg-white rounded-xl max-w-md w-full p-4 relative">
        <button onclick="closeEditCategory()" class="absolute top-3 right-4 text-xl text-gray-500">✕</button>
        <h3 class="font-semibold text-sm mb-4">Edit Sumber Pendapatan</h3>

        <form id="editCategoryForm" method="POST" class="space-y-3 text-sm">
            @csrf @method('PUT')
            
            {{-- INPUT HIDDEN EDIT: Memaksa tipe edit tetap terkunci sebagai income --}}
            <input type="hidden" id="editCatType" name="type" value="income">

            <div>
                <label class="block mb-1 font-medium">Nama Sumber Pendapatan / Item Usaha</label>
                <input id="editCatName" name="name" type="text" class="w-full border rounded-md p-2" required>
            </div>

            <div>
                <label class="block mb-1 font-medium">Harga</label>
                <input id="editCatPrice" name="default_price" type="number" class="w-full border rounded-md p-2" required>
            </div>

            <div>
                <label class="block mb-1 font-medium">Satuan</label>
                <select id="editCatUnitSelect" name="unit" class="w-full border rounded-md p-2" required onchange="toggleEditCustomUnit()">
                    <option value="pcs">pcs</option>
                    <option value="porsi">porsi</option>
                    <option value="unit">unit</option>
                    <option value="kg">kg</option>
                    <option value="liter">liter</option>
                    <option value="box">box</option>
                    <option value="bulan">bulan</option>
                    <option value="custom">Lainnya (Kustom)</option>
                </select>
            </div>

            <div id="editCustomUnitWrapper" class="hidden mt-2">
                <label class="block mb-1 text-xs font-medium text-gray-500">Masukkan Nama Satuan Baru</label>
                <input type="text" id="editCustomUnitInput" class="w-full border rounded-md p-2 bg-gray-50 uppercase text-xs font-semibold">
            </div>

            <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded-lg font-semibold hover:bg-blue-700 transition">
                Simpan Perubahan
            </button>
        </form>
    </div>
</div>

<script>
function toggleCustomUnit() {
    const unitSelect = document.getElementById('unitSelect');
    const customWrapper = document.getElementById('customUnitWrapper');
    const customInput = document.getElementById('customUnitInput');

    if (unitSelect.value === 'custom') {
        customWrapper.classList.remove('hidden');
        customInput.required = true;
        customInput.focus();
        
        customInput.addEventListener('input', function() {
            unitSelect.options[unitSelect.selectedIndex].value = this.value.toLowerCase();
        });
    } else {
        customWrapper.classList.add('hidden');
        customInput.required = false;
        customInput.value = '';
        
        const customOption = unitSelect.querySelector('option[value="' + unitSelect.value + '"]') 
            || unitSelect.querySelector('option[id="custom"]') ;
        if (customOption && customOption.text.includes('Lainnya')) {
            customOption.value = 'custom';
        }
    }
}

document.querySelector('form[action="{{ route("custom-categories.store") }}"]').addEventListener('submit', function(e) {
    const unitSelect = document.getElementById('unitSelect');
    const customInput = document.getElementById('customUnitInput');
    
    if (unitSelect.value === 'custom' || unitSelect.value.trim() === '') {
        if(customInput.value.trim() !== '') {
            unitSelect.options[unitSelect.selectedIndex].value = customInput.value.trim().toLowerCase();
        } else {
            e.preventDefault();
            alert('Silakan isi satuan kustom Anda terlebih dahulu!');
        }
    }
});

function openEditCategory(el) {
    const id = el.dataset.id;
    const type = el.dataset.type;
    const name = el.dataset.name;
    const price = el.dataset.price;
    const unit = el.dataset.unit;

    document.getElementById('editCatType').value = type;
    document.getElementById('editCatName').value = name;
    document.getElementById('editCatPrice').value = Math.round(price);

    const form = document.getElementById('editCategoryForm');
    form.action = `/custom-categories/${id}`;

    const unitSelect = document.getElementById('editCatUnitSelect');
    const customWrapper = document.getElementById('editCustomUnitWrapper');
    const customInput = document.getElementById('editCustomUnitInput');

    const standardUnits = ['pcs', 'porsi', 'unit', 'kg', 'liter', 'box', 'bulan'];
    
    if (standardUnits.includes(unit)) {
        unitSelect.value = unit;
        customWrapper.classList.add('hidden');
        customInput.required = false;
        customInput.value = '';
    } else {
        unitSelect.value = 'custom';
        customWrapper.classList.remove('hidden');
        customInput.required = true;
        customInput.value = unit;
        
        customInput.addEventListener('input', function() {
            unitSelect.options[unitSelect.selectedIndex].value = this.value.toLowerCase();
        });
    }

    const modal = document.getElementById('editCategoryModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeEditCategory() {
    document.getElementById('editCategoryModal').classList.add('hidden');
    document.getElementById('editCategoryModal').classList.remove('flex');
}

function toggleEditCustomUnit() {
    const unitSelect = document.getElementById('editCatUnitSelect');
    const customWrapper = document.getElementById('editCustomUnitWrapper');
    const customInput = document.getElementById('editCustomUnitInput');

    if (unitSelect.value === 'custom') {
        customWrapper.classList.remove('hidden');
        customInput.required = true;
        customInput.focus();
        customInput.addEventListener('input', function() {
            unitSelect.options[unitSelect.selectedIndex].value = this.value.toLowerCase();
        });
    } else {
        customWrapper.classList.add('hidden');
        customInput.required = false;
    }
}

document.getElementById('editCategoryForm').addEventListener('submit', function(e) {
    const unitSelect = document.getElementById('editCatUnitSelect');
    const customInput = document.getElementById('editCustomUnitInput');
    
    if (unitSelect.value === 'custom' && customInput.value.trim() !== '') {
        unitSelect.options[unitSelect.selectedIndex].value = customInput.value.trim().toLowerCase();
    }
});
</script>
@endsection

@push('scripts')
    @vite('resources/js/transactions.js')
@endpush