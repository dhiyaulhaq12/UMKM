document.addEventListener('DOMContentLoaded', () => {

    /* =============================================================
       LOGIKA KATEGORI DINAMIS SUDAH DIPINDAH TOTAL KE FILE BLADE
       AGAR BISA MENAMPILKAN MENU CUSTOM DARI DATABASE.
       (Blok kode lama di bagian ini sudah dihapus agar tidak bentrok)
    ============================================================= */

    /* ===============================
       FORMAT RUPIAH (TIDAK BERUBAH)
    =============================== */
    const amountInput = document.getElementById('amount');
    if (amountInput) {
        amountInput.addEventListener('input', function () {
            let value = this.value.replace(/[^0-9]/g, '');
            this.value = value
                ? value.replace(/\B(?=(\d{3})+(?!\d))/g, '.')
                : '';
        });
    }

    document.getElementById('type').addEventListener('change', function() {
        const type = this.value;
        const categorySelect = document.getElementById('category');
        const qtyWrapper = document.getElementById('quantityWrapper');
        const amountInput = document.getElementById('amount');
        const amountLabel = document.getElementById('amountLabel');
        
        // Ambil elemen wrapper gambar (Kita beri ID/bisa pakai pembungkus div gambar jualan)
        const imageWrapper = document.getElementById('imageSource').parentElement; 
        const noteWrapper = document.querySelector('textarea[name="note"]').parentElement;
    
        categorySelect.innerHTML = '<option value="">Pilih Item</option>';
        
        if (type === 'income') {
            categorySelect.disabled = false;
            if (qtyWrapper) qtyWrapper.classList.remove('hidden');
            amountInput.readOnly = true;
            if (amountLabel) amountLabel.innerText = "Jumlah";
    
            // 🟢 Sembunyikan Gambar untuk Pendapatan agar ringkas dan cepat saat input kasir
            if (imageWrapper) imageWrapper.classList.add('hidden');
            // Catatan biarkan tetap ada atau dikecilkan fungsinya
            if (noteWrapper) noteWrapper.classList.remove('hidden'); 
    
            // Render customIncomeCategories ...
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
            if (amountLabel) amountLabel.innerText = "Jumlah (Rp)";
    
            // 🔴 Munculkan Kembali Gambar dan Catatan untuk Pengeluaran (Wajib Bukti Struk)
            if (imageWrapper) imageWrapper.classList.remove('hidden');
            if (noteWrapper) noteWrapper.classList.remove('hidden');
    
            // Render expenseCategories ...
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

    /* ===============================
       BERSIHKAN SAAT SUBMIT (TIDAK BERUBAH)
    =============================== */
    const form = document.querySelector('form');
    if (form && amountInput) {
        form.addEventListener('submit', () => {
            amountInput.value = amountInput.value.replace(/\./g, '');
        });
    }

    /* ===============================
       UPLOAD GAMBAR + PREVIEW (TIDAK BERUBAH)
    =============================== */
    const imageSource = document.getElementById('imageSource');
    const imageInput = document.getElementById('imageInput');
    const imagePreviewWrapper = document.getElementById('imagePreviewWrapper');
    const imagePreview = document.getElementById('imagePreview');
    const removeImageBtn = document.getElementById('removeImage');

    if (imageSource && imageInput) {
        imageSource.addEventListener('change', function () {
            if (!this.value) return;
            imageInput.value = '';
            if (this.value === 'camera') {
                imageInput.setAttribute('accept', 'image/*');
                imageInput.setAttribute('capture', 'environment');
            } else {
                imageInput.setAttribute('accept', 'image/*');
                imageInput.removeAttribute('capture');
            }
            imageInput.click();
        });

        imageInput.addEventListener('change', function () {
            const file = this.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = e => {
                imagePreview.src = e.target.result;
                imagePreviewWrapper.classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        });

        removeImageBtn?.addEventListener('click', () => {
            imageInput.value = '';
            imagePreview.src = '';
            imagePreviewWrapper.classList.add('hidden');
            imageSource.value = '';
        });
    }

    /* ===============================
       AUTO HIDE CAMERA (DESKTOP) (TIDAK BERUBAH)
    =============================== */
    const isMobile = /Android|iPhone|iPad|iPod/i.test(navigator.userAgent);
    const cameraOption = document.getElementById('cameraOption');
    if (cameraOption && !isMobile) {
        cameraOption.remove();
    }
});