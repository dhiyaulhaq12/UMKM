document.addEventListener('DOMContentLoaded', () => {

    /* ===============================
       KATEGORI DINAMIS
    =============================== */
    const typeSelect = document.getElementById('type');
    const categorySelect = document.getElementById('category');

    if (typeSelect && categorySelect) {
        const categories = {
            income: [
                "Penjualan Produk",
                "Penjualan Jasa",
                "Investasi",
                "Sewa Properti",
                "Royalti",
                "Bunga Bank",
                "Hadiah/Bonus",
                "Lainnya"
            ],
            expense: [
                "Bahan Baku",
                "Operasional",
                "Gaji Karyawan",
                "Marketing",
                "Transportasi",
                "Sewa Tempat",
                "Utilitas (Listrik, Air, Internet)",
                "Asuransi",
                "Maintenance",
                "Lainnya"
            ]
        };

        typeSelect.addEventListener('change', function () {
            categorySelect.innerHTML = '<option value="">Pilih Kategori</option>';

            if (!this.value) {
                categorySelect.disabled = true;
                return;
            }

            categorySelect.disabled = false;

            categories[this.value].forEach(cat => {
                const opt = document.createElement('option');
                opt.value = cat;
                opt.textContent = cat;
                categorySelect.appendChild(opt);
            });
        });
    }

    /* ===============================
       FORMAT RUPIAH
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

    /* ===============================
       BERSIHKAN SAAT SUBMIT
    =============================== */
    const form = document.querySelector('form');
    if (form && amountInput) {
        form.addEventListener('submit', () => {
            amountInput.value = amountInput.value.replace(/\./g, '');
        });
    }

    /* ===============================
       UPLOAD GAMBAR + PREVIEW
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
       AUTO HIDE CAMERA (DESKTOP)
    =============================== */
    const isMobile = /Android|iPhone|iPad|iPod/i.test(navigator.userAgent);
    const cameraOption = document.getElementById('cameraOption');

    if (cameraOption && !isMobile) {
        cameraOption.remove();
    }

});
