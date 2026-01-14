// Función para cargar productos del turno
function CargarProductosTurno(idTurno) {
    var buscar = $('#buscar-producto').val();
    var estado = $('#filtro-estado-producto').val();
    
    var url = 'https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/Controladores/DataInventarioTurnos.php';
    var params = ['id_turno=' + idTurno];
    
    if (buscar) params.push('buscar=' + encodeURIComponent(buscar));
    if (estado) params.push('estado=' + encodeURIComponent(estado));
    
    url += '?' + params.join('&');
    
    if ($.fn.DataTable.isDataTable('#tablaInventarioTurnos')) {
        $('#tablaInventarioTurnos').DataTable().destroy();
    }
    
    $('#tablaInventarioTurnos').DataTable({
        "processing": true,
        "serverSide": false,
        "ajax": {
            "url": url,
            "type": "GET",
            "dataSrc": "aaData"
        },
        "columns": [
            { "data": "Cod_Barra", "title": "Código" },
            { "data": "Nombre_Prod", "title": "Producto" },
            { "data": "Existencias_R", "title": "Existencias Sistema" },
            { "data": "Existencias_Fisicas", "title": "Existencias Físicas" },
            { "data": "Diferencia", "title": "Diferencia" },
            { "data": "Estado", "title": "Estado" },
            { "data": "Acciones", "title": "Acciones", "orderable": false }
        ],
        "language": {
            "lengthMenu": "Mostrar _MENU_ registros",
            "zeroRecords": "No se encontraron resultados",
            "info": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
            "infoEmpty": "No hay registros disponibles",
            "infoFiltered": "(filtrado de un total de _MAX_ registros)",
            "search": "Buscar:",
            "paginate": {
                "first": '<i class="fas fa-angle-double-left"></i>',
                "last": '<i class="fas fa-angle-double-right"></i>',
                "next": '<i class="fas fa-angle-right"></i>',
                "previous": '<i class="fas fa-angle-left"></i>'
            }
        },
        "pageLength": 25,
        "responsive": true,
        "rowCallback": function(row, data) {
            if (data.clase_fila) {
                $(row).addClass(data.clase_fila);
            }
        }
    });
}

// Iniciar turno
function iniciarTurno() {
    Swal.fire({
        title: '¿Iniciar nuevo turno de inventario?',
        text: 'Se creará un nuevo turno para el día de hoy',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#dc3545',
        confirmButtonText: 'Sí, iniciar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: 'https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/api/gestion_turnos.php',
                type: 'POST',
                data: {
                    accion: 'iniciar'
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        Swal.fire('¡Éxito!', response.message, 'success').then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire('Error', response.message, 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error', 'Error al comunicarse con el servidor', 'error');
                }
            });
        }
    });
}

// Pausar turno
function pausarTurno(idTurno) {
    Swal.fire({
        title: '¿Pausar el turno?',
        text: 'El turno se guardará y podrás reanudarlo más tarde',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ffc107',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, pausar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: 'https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/api/gestion_turnos.php',
                type: 'POST',
                data: {
                    accion: 'pausar',
                    id_turno: idTurno
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        Swal.fire('¡Éxito!', response.message, 'success').then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire('Error', response.message, 'error');
                    }
                }
            });
        }
    });
}

// Reanudar turno
function reanudarTurno(idTurno) {
    $.ajax({
        url: 'https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/api/gestion_turnos.php',
        type: 'POST',
        data: {
            accion: 'reanudar',
            id_turno: idTurno
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                Swal.fire('¡Éxito!', response.message, 'success').then(() => {
                    location.reload();
                });
            } else {
                Swal.fire('Error', response.message, 'error');
            }
        }
    });
}

// Finalizar turno
function finalizarTurno(idTurno) {
    Swal.fire({
        title: '¿Finalizar el turno?',
        text: 'Una vez finalizado, no podrás agregar más productos',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, finalizar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: 'https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/api/gestion_turnos.php',
                type: 'POST',
                data: {
                    accion: 'finalizar',
                    id_turno: idTurno
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        Swal.fire('¡Éxito!', response.message, 'success').then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire('Error', response.message, 'error');
                    }
                }
            });
        }
    });
}

// Seleccionar producto
$(document).on('click', '.btn-seleccionar-producto', function() {
    var idProducto = $(this).data('id');
    var codigo = $(this).data('codigo');
    
    if (!turnoActivo) {
        Swal.fire('Error', 'No hay un turno activo', 'error');
        return;
    }
    
    $.ajax({
        url: 'https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/api/gestion_turnos.php',
        type: 'POST',
        data: {
            accion: 'seleccionar_producto',
            id_turno: turnoActivo.ID_Turno,
            id_producto: idProducto,
            cod_barra: codigo
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                Swal.fire('¡Éxito!', response.message, 'success').then(() => {
                    CargarProductosTurno(turnoActivo.ID_Turno);
                });
            } else {
                Swal.fire('Error', response.message, 'error');
            }
        }
    });
});

// Contar producto
$(document).on('click', '.btn-contar-producto', function() {
    var idRegistro = $(this).data('id');
    
    Swal.fire({
        title: 'Registrar conteo físico',
        html: '<input type="number" id="existencias-fisicas" class="swal2-input" placeholder="Existencias físicas" min="0">',
        showCancelButton: true,
        confirmButtonText: 'Guardar',
        cancelButtonText: 'Cancelar',
        preConfirm: () => {
            const existencias = document.getElementById('existencias-fisicas').value;
            if (!existencias || existencias < 0) {
                Swal.showValidationMessage('Ingrese un valor válido');
            }
            return existencias;
        }
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: 'https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/api/gestion_turnos.php',
                type: 'POST',
                data: {
                    accion: 'contar_producto',
                    id_registro: idRegistro,
                    existencias_fisicas: result.value
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        Swal.fire('¡Éxito!', response.message, 'success').then(() => {
                            CargarProductosTurno(turnoActivo.ID_Turno);
                        });
                    } else {
                        Swal.fire('Error', response.message, 'error');
                    }
                }
            });
        }
    });
});

// Ver historial
function verHistorialTurnos() {
    // Implementar vista de historial
    Swal.fire('Información', 'Funcionalidad de historial en desarrollo', 'info');
}

// Inicializar tabla si hay turno activo
$(document).ready(function() {
    if (turnoActivo) {
        if ($('#tablaInventarioTurnos').length === 0) {
            $('#DataInventarioTurnos').html(`
                <table id="tablaInventarioTurnos" class="table table-striped table-hover">
                    <thead></thead>
                    <tbody></tbody>
                </table>
            `);
        }
        // Esperar un momento para asegurar que el DOM esté listo
        setTimeout(function() {
            CargarProductosTurno(turnoActivo.ID_Turno);
        }, 100);
    } else {
        $('#DataInventarioTurnos').html('<div class="alert alert-info text-center">No hay un turno activo. Inicia un nuevo turno para comenzar.</div>');
    }
});
