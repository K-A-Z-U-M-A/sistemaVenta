/**
 * Script para la gestión de backups
 */

document.addEventListener('DOMContentLoaded', function () {
    // SweetAlert2 para confirmación de eliminación
    const deleteButtons = document.querySelectorAll('.delete-backup-btn');

    deleteButtons.forEach(function (button) {
        button.addEventListener('click', function (e) {
            e.preventDefault();
            const fileName = this.dataset.filename;
            const deleteUrl = this.dataset.url;

            Swal.fire({
                title: '¿Estás seguro?',
                html: '¿Deseas eliminar el backup <strong>' + fileName + '</strong>?<br>Esta acción no se puede deshacer.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then(function (result) {
                if (result.isConfirmed) {
                    // Crear y enviar formulario
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = deleteUrl;

                    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    const csrfInput = document.createElement('input');
                    csrfInput.type = 'hidden';
                    csrfInput.name = '_token';
                    csrfInput.value = csrfToken;

                    const methodInput = document.createElement('input');
                    methodInput.type = 'hidden';
                    methodInput.name = '_method';
                    methodInput.value = 'DELETE';

                    form.appendChild(csrfInput);
                    form.appendChild(methodInput);
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        });
    });
});
