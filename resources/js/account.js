/* ============================================================
   FUNGSI GLOBAL (Agar bisa dipanggil oleh onclick di Blade)
   ============================================================ */
   window.viewFullImage = function() {
    const preview = document.getElementById('profilePreview');
    const modal = document.getElementById('imageModal');
    const fullImg = document.getElementById('fullImage');
    if (preview && modal && fullImg) {
        fullImg.src = preview.src;
        modal.classList.remove('hidden');
    }
};

window.closeModal = function() {
    const modal = document.getElementById('imageModal');
    if (modal) modal.classList.add('hidden');
};

window.deleteProfilePhoto = function() {
    // Pastikan SweetAlert (Swal) sudah tersedia
    if (typeof Swal === 'undefined') {
        if(confirm("Hapus foto profil?")) {
            document.getElementById('deletePhotoForm').submit();
        }
        return;
    }

    Swal.fire({
        title: 'Hapus foto profil?',
        text: "Foto akan dikembalikan ke default.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.getElementById('deletePhotoForm');
            if (form) form.submit();
        }
    });
};

window.togglePassword = function(id) {
    const input = document.getElementById(id);
    if (input) {
        input.type = input.type === 'password' ? 'text' : 'password';
    }
};

/* ============================================================
   LOGIKA DOM (Tetap seperti semula)
   ============================================================ */
document.addEventListener('DOMContentLoaded', function() {
    const photoInput = document.getElementById('photoInput');
    const pencilBtn = document.getElementById('pencilBtn');
    const preview = document.getElementById('profilePreview');
    const removeBtn = document.getElementById('removePhotoBtn');

    if (photoInput && pencilBtn) {
        const originalSrc = preview.getAttribute('data-original');

        pencilBtn.addEventListener('click', () => photoInput.click());

        photoInput.addEventListener('change', function () {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = e => {
                    preview.src = e.target.result;
                    removeBtn.classList.remove('hidden');
                };
                reader.readAsDataURL(file);
            }
        });

        removeBtn.addEventListener('click', function() {
            photoInput.value = '';
            preview.src = originalSrc;
            removeBtn.classList.add('hidden');
        });
    }
});