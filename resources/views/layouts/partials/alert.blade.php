@if (session('success'))
<script>
    document.addEventListener("DOMContentLoaded", function() {
        var message = @json(session('success'));
        var Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 1500,
            timerProgressBar: true,
            didOpen: function(toast) {
                toast.addEventListener('mouseenter', Swal.stopTimer);
                toast.addEventListener('mouseleave', Swal.resumeTimer);
            }
        });

        Toast.fire({
            icon: 'success',
            title: message
        });
    });
</script>
@endif

@if (session('error'))
<script>
    document.addEventListener("DOMContentLoaded", function() {
        var message = @json(session('error'));
        Swal.fire({
            icon: 'error',
            title: '¡Atención!',
            text: message,
            confirmButtonText: 'Entendido',
            confirmButtonColor: '#d33',
            allowOutsideClick: false
        });
    });
</script>
@endif