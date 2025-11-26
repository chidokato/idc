<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    function showToast(type, message) {
        Swal.fire({
            toast: true,
            position: 'bottom-start', // Góc trái
            icon: type,
            title: message,
            showConfirmButton: false,
            timer: 2000,
            timerProgressBar: true
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        @if (session('success'))
            showToast('success', '{{ session('success') }}');
        @endif

        @if (session('warning'))
            showToast('warning', '{{ session('warning') }}');
        @endif

        @if (session('error'))
            showToast('error', '{{ session('error') }}');
        @endif
    });

    function showCenterError(message) {
        Swal.fire({
            icon: 'error',
            title: 'Lỗi',
            text: message,
            confirmButtonText: 'OK',
            position: 'center',
            backdrop: true
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        @if (session('center_error'))
            showCenterError('{{ session('center_error') }}');
        @endif
    });
</script>
