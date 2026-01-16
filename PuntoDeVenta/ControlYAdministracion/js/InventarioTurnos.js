// Variable para el timeout del buscador (debounce)
var buscarTimeout = null;

// Variables para procesamiento de escáner
var scanBuffer = "";
var scanInterval = 5; // Milisegundos
var isScannerInput = false;
var ultimoEscaneo = null;
var tiempoUltimoEscaneo = 0;

// Función para cargar productos del turno (0 = sin turno, mostrar disponibles)
function CargarProductosTurno(idTurno) {
    // Permitir idTurno = 0 para mostrar productos sin turno activo
    // Convertir a número y validar
    idTurno = parseInt(idTurno) || 0;
    
    // Solo validar si es undefined o null, NO si es 0
    if (idTurno === undefined || idTurno === null || isNaN(idTurno)) {
        console.warn('ID de turno inválido, usando 0:', idTurno);
        idTurno = 0;
    }
    
    var buscar = $('#buscar-producto').val();
    var estado = $('#filtro-estado-producto').val();
    
    var url = 'https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/Controladores/DataInventarioTurnos.php';
    var params = ['id_turno=' + (idTurno || 0)];
    
    if (buscar && buscar.trim() !== '') {
        params.push('buscar=' + encodeURIComponent(buscar.trim()));
    }
    if (estado && estado !== '') {
        params.push('estado=' + encodeURIComponent(estado));
    }
    
    url += '?' + params.join('&');
    
    // Destruir tabla existente si hay
    if ($.fn.DataTable.isDataTable('#tablaInventarioTurnos')) {
        $('#tablaInventarioTurnos').DataTable().destroy();
        $('#tablaInventarioTurnos').empty();
    }
    
    // Crear tabla si no existe
    if ($('#tablaInventarioTurnos').length === 0) {
        $('#DataInventarioTurnos').html(`
            <table id="tablaInventarioTurnos" class="table table-striped table-hover">
                <thead></thead>
                <tbody></tbody>
            </table>
        `);
    }
    
    $('#tablaInventarioTurnos').DataTable({
        "processing": true,
        "serverSide": false,
        "destroy": true,
        "ajax": {
            "url": url,
            "type": "GET",
            "dataSrc": function(json) {
                console.log('Datos recibidos:', json);
                if (json.aaData && json.aaData.length > 0) {
                    return json.aaData;
                }
                return [];
            },
            "error": function(xhr, error, thrown) {
                console.error('Error al cargar productos:', error, thrown);
                $('#DataInventarioTurnos').html('<div class="alert alert-danger">Error al cargar los productos. Por favor, recarga la página.</div>');
            }
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
            "zeroRecords": "No se encontraron productos. Intenta con otros filtros.",
            "info": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
            "infoEmpty": "No hay registros disponibles",
            "infoFiltered": "(filtrado de un total de _MAX_ registros)",
            "search": "Buscar:",
            "processing": "Cargando productos...",
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
        },
        "initComplete": function() {
            console.log('Tabla de productos cargada correctamente');
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
    // Primero verificar cuántos productos están completados
    if (!turnoActivo || !turnoActivo.ID_Turno) {
        Swal.fire('Error', 'No hay un turno activo', 'error');
        return;
    }
    
    var limite_productos = turnoActivo.Limite_Productos || 50;
    var productos_completados = turnoActivo.Productos_Completados || 0;
    
    // Verificar si se han completado todos los productos requeridos
    if (productos_completados < limite_productos) {
        Swal.fire({
            title: 'No se puede finalizar el turno',
            html: '<p>Has completado <strong>' + productos_completados + '</strong> de <strong>' + limite_productos + '</strong> productos requeridos.</p>' +
                  '<p>Debes completar todos los productos antes de poder finalizar el turno.</p>',
            icon: 'warning',
            confirmButtonText: 'Entendido',
            confirmButtonColor: '#3085d6'
        });
        return;
    }
    
    // Si se han completado todos, mostrar confirmación
    Swal.fire({
        title: '¿Finalizar el turno?',
        html: '<p>Has completado <strong>' + productos_completados + '</strong> de <strong>' + limite_productos + '</strong> productos.</p>' +
              '<p>Una vez finalizado, no podrás agregar más productos.</p>',
        icon: 'question',
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
                },
                error: function(xhr) {
                    var errorMsg = 'Error al comunicarse con el servidor';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }
                    Swal.fire('Error', errorMsg, 'error');
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
                    if (turnoActivo && turnoActivo.ID_Turno) {
                        CargarProductosTurno(turnoActivo.ID_Turno);
                    } else {
                        location.reload();
                    }
                });
            } else {
                Swal.fire('Error', response.message, 'error');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error al seleccionar producto:', error);
            Swal.fire('Error', 'Error al comunicarse con el servidor', 'error');
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
                            if (turnoActivo && turnoActivo.ID_Turno) {
                                CargarProductosTurno(turnoActivo.ID_Turno);
                            } else {
                                location.reload();
                            }
                        });
                    } else {
                        Swal.fire('Error', response.message, 'error');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error al contar producto:', error);
                    Swal.fire('Error', 'Error al comunicarse con el servidor', 'error');
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

// Función global para recargar datos después de acciones
function recargarDatosTurno() {
    if (typeof turnoActivo !== 'undefined' && turnoActivo && turnoActivo.ID_Turno) {
        // Recargar página para obtener el turno actualizado
        location.reload();
    }
}

// ===== SISTEMA DE BÚSQUEDA ACTIVA CON ESCÁNER =====

// Función para buscar y seleccionar producto (desde escáner o autocomplete)
function buscarYSeleccionarProducto(codigo) {
    if (!codigo || codigo.trim() === '') {
        return;
    }
    
    if (!turnoActivo || !turnoActivo.ID_Turno) {
        Swal.fire('Error', 'No hay un turno activo', 'error');
        limpiarCampoBusqueda();
        return;
    }
    
    // Buscar producto primero
    var formData = new FormData();
    formData.append('codigo', codigo.trim());
    formData.append('id_turno', turnoActivo.ID_Turno);
    
    $.ajax({
        url: 'https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/Controladores/BusquedaEscanerInventarioTurnos.php',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(response) {
            if (response.success && response.producto) {
                // Verificar si está bloqueado por otro usuario
                if (response.bloqueado_por_otro) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Producto bloqueado',
                        text: 'Este producto está siendo contado por: ' + response.usuario_bloqueador,
                        timer: 2000,
                        showConfirmButton: false
                    });
                    limpiarCampoBusqueda();
                    return;
                }
                
                // Seleccionar el producto en el turno
                seleccionarProductoEnTurno(response.producto.id, response.producto.codigo, response.producto.nombre);
            } else {
                Swal.fire({
                    icon: 'info',
                    title: 'Producto no encontrado',
                    text: response.message || 'No se encontró el producto con código: ' + codigo,
                    timer: 2000,
                    showConfirmButton: false
                });
                limpiarCampoBusqueda();
            }
        },
        error: function() {
            Swal.fire('Error', 'Error al buscar el producto', 'error');
            limpiarCampoBusqueda();
        }
    });
}

// Función para seleccionar producto en el turno
function seleccionarProductoEnTurno(idProducto, codigo, nombre) {
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
                // Mostrar notificación rápida y actualizar tabla
                Swal.fire({
                    icon: 'success',
                    title: 'Producto agregado',
                    text: nombre,
                    timer: 1500,
                    showConfirmButton: false,
                    toast: true,
                    position: 'top-end'
                });
                
                // Actualizar tabla y limpiar campo
                CargarProductosTurno(turnoActivo.ID_Turno);
                limpiarCampoBusqueda();
            } else {
                Swal.fire('Error', response.message, 'error');
                limpiarCampoBusqueda();
            }
        },
        error: function() {
            Swal.fire('Error', 'Error al comunicarse con el servidor', 'error');
            limpiarCampoBusqueda();
        }
    });
}

// Función para procesar buffer de escaneo
function procesarBufferEscaneo() {
    var delimiter = "\n";
    var delimiterIndex = scanBuffer.indexOf(delimiter);
    
    while (delimiterIndex !== -1) {
        var codigoEscaneado = scanBuffer.slice(0, delimiterIndex).trim();
        scanBuffer = scanBuffer.slice(delimiterIndex + 1);
        
        if (esCodigoBarrasValido(codigoEscaneado)) {
            tiempoUltimoEscaneo = Date.now();
            buscarYSeleccionarProducto(codigoEscaneado);
        }
        
        delimiterIndex = scanBuffer.indexOf(delimiter);
    }
}

// Función para agregar escaneo al buffer
function agregarEscaneo(escaneo) {
    scanBuffer += escaneo;
}

// Función para validar código de barras
function esCodigoBarrasValido(codigo) {
    var longitud = codigo.length;
    return longitud >= 2 && longitud <= 50; // Rango ampliado para nombres también
}

// Función para limpiar campo de búsqueda
function limpiarCampoBusqueda() {
    $('#buscar-producto').val('');
    $('#buscar-producto').focus();
}

// El autocomplete se inicializa en InventarioTurnos.php directamente
// Aquí solo mantenemos las funciones auxiliares
