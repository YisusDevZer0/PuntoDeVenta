function mostrarSugerencia(boton) {
    // Obtener datos del botón
    const id = boton.dataset.id;
    const nombre = boton.dataset.nombre;
    const codigo = boton.dataset.codigo;
    const existencia = boton.dataset.existencia;
    const sugerida = boton.dataset.sugerida;

    // Llenar el modal con los datos
    document.getElementById('idProducto').value = id;
    document.getElementById('nombreProducto').value = nombre;
    document.getElementById('codigoBarras').value = codigo;
    document.getElementById('existenciaActual').value = existencia;
    document.getElementById('cantidadSugerida').value = sugerida;

    // Mostrar el modal
    $('#SugerenciaPedidoModal').modal('show');
}

// Manejar el envío del formulario
document.getElementById('guardarSugerencia').addEventListener('click', function() {
    // Obtener los datos del formulario
    const formData = new FormData();
    formData.append('idProducto', document.getElementById('idProducto').value);
    formData.append('nombreProducto', document.getElementById('nombreProducto').value);
    formData.append('codigoBarras', document.getElementById('codigoBarras').value);
    formData.append('existenciaActual', document.getElementById('existenciaActual').value);
    formData.append('cantidadSugerida', document.getElementById('cantidadSugerida').value);

    // Mostrar loading
    document.getElementById('loading-overlay').style.display = 'flex';

    // Enviar la solicitud
    fetch('Controladores/GuardarSugerencia.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        // Ocultar loading
        document.getElementById('loading-overlay').style.display = 'none';

        if (data.status === 'success') {
            Swal.fire({
                icon: 'success',
                title: '¡Éxito!',
                text: data.message,
                showConfirmButton: true
            }).then(() => {
                // Cerrar el modal
                $('#SugerenciaPedidoModal').modal('hide');
                // Recargar la tabla si es necesario
                $('#Clientes').DataTable().ajax.reload();
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: data.message
            });
        }
    })
    .catch(error => {
        // Ocultar loading
        document.getElementById('loading-overlay').style.display = 'none';
        
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Hubo un error al procesar la solicitud'
        });
        console.error('Error:', error);
    });
}); 