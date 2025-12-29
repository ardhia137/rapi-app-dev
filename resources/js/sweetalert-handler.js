// SweetAlert2 Handler for Laravel Flash Messages and Confirmations
// Place this in: resources/js/sweetalert-handler.js

import Swal from 'sweetalert2';

// Initialize SweetAlert2 when DOM is ready
document.addEventListener('DOMContentLoaded', function() {

    // Handle Flash Messages from Laravel Session
    if (window.FLASH_MESSAGES) {
        const messages = window.FLASH_MESSAGES;

        // Success message
        if (messages.success) {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: messages.success,
                timer: 3000,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
        }

        // Error message
        if (messages.error) {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: messages.error,
                confirmButtonColor: '#dc2626'
            });
        }

        // Warning message
        if (messages.warning) {
            Swal.fire({
                icon: 'warning',
                title: 'Peringatan!',
                text: messages.warning,
                confirmButtonColor: '#f59e0b'
            });
        }

        // Info message
        if (messages.info) {
            Swal.fire({
                icon: 'info',
                title: 'Informasi',
                text: messages.info,
                confirmButtonColor: '#3b82f6'
            });
        }

        // Status message (general success)
        if (messages.status && !messages.success) {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: messages.status,
                timer: 3000,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
        }
    }

    // Handle Delete Confirmations
    document.querySelectorAll('form[data-confirm-delete]').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Data yang dihapus tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });

    // Handle Logout Confirmations
    document.querySelectorAll('form[data-logout-confirm]').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            Swal.fire({
                title: 'Keluar dari aplikasi?',
                text: "Anda akan keluar dari sesi Anda",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3b82f6',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, keluar',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });

    // Generic confirmation handler
    document.querySelectorAll('[data-confirm]').forEach(element => {
        element.addEventListener('click', function(e) {
            e.preventDefault();

            const message = this.getAttribute('data-confirm') || 'Apakah Anda yakin?';

            Swal.fire({
                title: 'Konfirmasi',
                text: message,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3b82f6',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    if (this.tagName === 'A') {
                        window.location.href = this.href;
                    } else if (this.form) {
                        this.form.submit();
                    }
                }
            });
        });
    });
});

// Export Swal for use in other files
window.Swal = Swal;
