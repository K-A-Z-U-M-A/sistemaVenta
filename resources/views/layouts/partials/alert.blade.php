@if (session('success'))
<script>
    let message = {!! json_encode(session('success')) !!};
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 1500,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer)
            toast.addEventListener('mouseleave', Swal.resumeTimer)
        }
    })

    Toast.fire({
        icon: 'success',
        title: message
    })
</script>
@endif

@if (session('error'))
<script>
    Swal.fire({
        icon: 'error',
        title: '¡Atención!',
        text: {!! json_encode(session('error')) !!},
        confirmButtonText: 'Entendido',
        confirmButtonColor: '#d33',
        allowOutsideClick: false
    })
</script>
@endif