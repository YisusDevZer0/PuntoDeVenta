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
    
    var url = 'https://doctorpez.mx/PuntoDeVenta/PuntoDeVentaFarmacias/Controladores/DataInventarioTurnos.php';
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
            { "data": "Lotes_Caducidad_Texto", "title": "Lotes y caducidad", "defaultContent": "-" },
            { "data": "Lote", "title": "Lote", "defaultContent": "-" },
            { "data": "Fecha_Caducidad", "title": "Fecha Cad.", "defaultContent": "-" },
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
        title: '¿Iniciar nuevo turno de conteo diario?',
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
                url: 'https://doctorpez.mx/PuntoDeVenta/PuntoDeVentaFarmacias/api/gestion_turnos.php',
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
                url: 'https://doctorpez.mx/PuntoDeVenta/PuntoDeVentaFarmacias/api/gestion_turnos.php',
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
        url: 'https://doctorpez.mx/PuntoDeVenta/PuntoDeVentaFarmacias/api/gestion_turnos.php',
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
    
    // Verificar primero con el servidor para obtener datos actualizados
    $.ajax({
        url: 'https://doctorpez.mx/PuntoDeVenta/PuntoDeVentaFarmacias/api/gestion_turnos.php',
        type: 'POST',
        data: {
            accion: 'obtener_estado',
            id_turno: idTurno
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                var total_productos = response.total_productos || 0;
                var productos_completados = response.productos_completados || 0;
                var limite_requerido = turnoActivo.Limite_Productos || 50;
                
                // El turno solo se cierra cuando tenga 50 productos contados
                if (productos_completados < limite_requerido) {
                    Swal.fire({
                        title: 'No se puede finalizar el turno',
                        html: '<p>Debes tener <strong>' + limite_requerido + '</strong> productos contados.</p>' +
                              '<p>Tienes <strong>' + productos_completados + '</strong> de ' + limite_requerido + '.</p>' +
                              '<p>Cuenta los productos pendientes antes de finalizar.</p>',
                        icon: 'warning',
                        confirmButtonText: 'Entendido',
                        confirmButtonColor: '#3085d6'
                    });
                    return;
                }
                
                confirmarFinalizarTurno(idTurno, total_productos, productos_completados);
            } else {
                var productos_completados = turnoActivo.Productos_Completados || 0;
                var limite_requerido = turnoActivo.Limite_Productos || 50;
                if (productos_completados < limite_requerido) {
                    Swal.fire({
                        title: 'No se puede finalizar el turno',
                        html: '<p>Debes tener ' + limite_requerido + ' productos contados. Tienes ' + productos_completados + '.</p>',
                        icon: 'warning',
                        confirmButtonText: 'Entendido',
                        confirmButtonColor: '#3085d6'
                    });
                    return;
                }
                confirmarFinalizarTurno(idTurno, turnoActivo.Total_Productos || 0, productos_completados);
            }
        },
        error: function() {
            var productos_completados = turnoActivo.Productos_Completados || 0;
            var limite_requerido = turnoActivo.Limite_Productos || 50;
            if (productos_completados < limite_requerido) {
                Swal.fire({
                    title: 'No se puede finalizar el turno',
                    html: '<p>Debes tener ' + limite_requerido + ' productos contados. Tienes ' + productos_completados + '.</p>',
                    icon: 'warning',
                    confirmButtonText: 'Entendido',
                    confirmButtonColor: '#3085d6'
                });
                return;
            }
            confirmarFinalizarTurno(idTurno, turnoActivo.Total_Productos || 0, productos_completados);
        }
    });
}

// Función auxiliar para confirmar finalización (solo se llama cuando ya hay 50 contados)
function confirmarFinalizarTurno(idTurno, total_productos, productos_completados) {
    var html = '<p>Has completado <strong>' + productos_completados + '</strong> productos contados.</p>' +
               '<p>Una vez finalizado, no podrás agregar más productos.</p>';
    
    Swal.fire({
        title: '¿Finalizar el turno?',
        html: html,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, finalizar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: 'https://doctorpez.mx/PuntoDeVenta/PuntoDeVentaFarmacias/api/gestion_turnos.php',
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
        url: 'https://doctorpez.mx/PuntoDeVenta/PuntoDeVentaFarmacias/api/gestion_turnos.php',
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
                // Actualizar el estado del turno activo inmediatamente
                if (turnoActivo && response.total_productos !== undefined) {
                    turnoActivo.Total_Productos = response.total_productos;
                    turnoActivo.Productos_Completados = response.productos_completados || 0;
                    // Actualizar la barra de progreso en tiempo real
                    actualizarBarraProgreso();
                }
                
                // Mostrar notificación rápida sin bloquear
                Swal.fire({
                    icon: 'success',
                    title: 'Producto agregado',
                    timer: 1500,
                    showConfirmButton: false,
                    toast: true,
                    position: 'top-end'
                });
                
                // Actualizar tabla de productos
                if (turnoActivo && turnoActivo.ID_Turno) {
                    CargarProductosTurno(turnoActivo.ID_Turno);
                } else {
                    location.reload();
                }
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

// Contar producto: muestra lotes existentes, permite agregar varios lotes con cantidad cada uno
$(document).on('click', '.btn-contar-producto', function() {
    var idRegistro = $(this).data('id');
    var idProducto = $(this).data('producto');
    var fkSucursal = $(this).data('fk-sucursal');
    var lotesData = $(this).data('lotes');
    var lotes = [];
    try {
        if (typeof lotesData === 'string') lotes = JSON.parse(lotesData || '[]');
        else if (Array.isArray(lotesData)) lotes = lotesData;
    } catch (e) { lotes = []; }
    
    var listaLotesHtml = '';
    if (lotes.length > 0) {
        listaLotesHtml = '<div class="mb-3">' +
            '<label class="form-label fw-semibold mb-2"><i class="fa-solid fa-list me-1"></i>Lotes y fechas de caducidad del producto (existentes)</label>' +
            '<div class="list-group list-group-flush border rounded" style="max-height: 160px; overflow-y: auto;">';
        lotes.forEach(function(l, i) {
            var fechaStr = l.Fecha_Caducidad ? (l.Fecha_Caducidad.split(' ')[0] || l.Fecha_Caducidad) : '';
            var fechaDisplay = fechaStr ? new Date(fechaStr + 'T12:00:00').toLocaleDateString('es-MX', { day: '2-digit', month: '2-digit', year: 'numeric' }) : '-';
            listaLotesHtml += '<div class="list-group-item d-flex justify-content-between align-items-center">' +
                '<span><strong>' + (l.Lote || '-') + '</strong> · Cad: ' + fechaDisplay + ' · <span class="text-muted">' + (l.Existencias || 0) + ' und</span></span>' +
                '</div>';
        });
        listaLotesHtml += '</div><small class="text-muted">Si agregas el mismo lote abajo, el sistema sumará la cantidad a este.</small></div>';
    }
    
    var filaLoteHtml = function(idx) {
        return '<div class="row g-2 align-items-end fila-lote-conteo mb-2" data-idx="' + idx + '">' +
            '<div class="col-4"><input type="text" class="form-control form-control-sm inp-lote" placeholder="Lote"></div>' +
            '<div class="col-3"><input type="date" class="form-control form-control-sm inp-fecha" placeholder="Cad."></div>' +
            '<div class="col-3"><input type="number" class="form-control form-control-sm inp-cantidad" placeholder="Cant." min="0" value="0"></div>' +
            '<div class="col-2"><button type="button" class="btn btn-outline-danger btn-sm btn-quitar-lote" title="Quitar"><i class="fa-solid fa-times"></i></button></div>' +
            '</div>';
    };
    
    var html = '<div class="text-start conteo-modal-fields">' +
        listaLotesHtml +
        '<div class="mb-2">' +
        '<label class="form-label fw-semibold mb-1"><i class="fa-solid fa-plus me-1"></i>Lotes a registrar (lote, fecha cad., cantidad)</label>' +
        '<div id="contenedor-filas-lote">' + filaLoteHtml(0) + '</div>' +
        '<button type="button" class="btn btn-sm btn-outline-primary mt-2" id="btn-agregar-lote-conteo"><i class="fa-solid fa-plus me-1"></i>Agregar otro lote</button>' +
        '</div>' +
        '<div class="mb-3">' +
        '<label class="form-label fw-semibold mb-1">Total existencias físicas <span class="text-danger">*</span></label>' +
        '<input type="number" id="existencias-fisicas-total" class="form-control form-control-lg" placeholder="Total contado" min="0">' +
        '<small class="text-muted">Se rellena con la suma de cantidades por lote. Puedes editarlo si el total es distinto.</small>' +
        '</div>' +
        '</div>';
    
    Swal.fire({
        title: '<i class="fa-solid fa-calculator text-primary me-2"></i>Registrar conteo físico',
        html: html,
        showCancelButton: true,
        confirmButtonText: '<i class="fa-solid fa-check me-1"></i> Guardar',
        cancelButtonText: 'Cancelar',
        width: '560px',
        customClass: { confirmButton: 'btn btn-primary', cancelButton: 'btn btn-outline-secondary' },
        didOpen: function() {
            var $cont = $('#contenedor-filas-lote');
            var $total = $('#existencias-fisicas-total');
            function actualizarTotal() {
                var sum = 0;
                $cont.find('.inp-cantidad').each(function() {
                    sum += parseInt($(this).val(), 10) || 0;
                });
                $total.val(sum);
            }
            $cont.on('input', '.inp-cantidad', actualizarTotal);
            $('#btn-agregar-lote-conteo').on('click', function() {
                var idx = $cont.find('.fila-lote-conteo').length;
                $cont.append(filaLoteHtml(idx));
            });
            $cont.on('click', '.btn-quitar-lote', function() {
                var $fila = $(this).closest('.fila-lote-conteo');
                if ($cont.find('.fila-lote-conteo').length > 1) {
                    $fila.remove();
                    actualizarTotal();
                }
            });
            actualizarTotal();
        },
        preConfirm: () => {
            var totalEx = parseInt($('#existencias-fisicas-total').val(), 10) || 0;
            if (totalEx < 0) {
                Swal.showValidationMessage('El total de existencias debe ser mayor o igual a 0');
                return false;
            }
            var lotesEnviar = [];
            $('#contenedor-filas-lote .fila-lote-conteo').each(function() {
                var lote = $(this).find('.inp-lote').val().trim();
                var fecha = $(this).find('.inp-fecha').val() || '';
                var cant = parseInt($(this).find('.inp-cantidad').val(), 10) || 0;
                if (lote || fecha || cant > 0) {
                    lotesEnviar.push({ lote: lote, fecha_caducidad: fecha, cantidad: cant });
                }
            });
            return {
                existencias_fisicas_total: totalEx,
                lotes: lotesEnviar
            };
        }
    }).then((result) => {
        if (result.isConfirmed && result.value) {
            var d = result.value;
            var payload = {
                accion: 'contar_producto',
                id_registro: idRegistro,
                existencias_fisicas: d.existencias_fisicas_total,
                lotes_json: JSON.stringify(d.lotes || [])
            };
            $.ajax({
                url: 'https://doctorpez.mx/PuntoDeVenta/PuntoDeVentaFarmacias/api/gestion_turnos.php',
                type: 'POST',
                data: payload,
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        if (turnoActivo && response.total_productos !== undefined) {
                            turnoActivo.Total_Productos = response.total_productos;
                            turnoActivo.Productos_Completados = response.productos_completados;
                            actualizarBarraProgreso();
                        }
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
                    Swal.fire('Error', (xhr.responseJSON && xhr.responseJSON.message) || 'Error al comunicarse con el servidor', 'error');
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
        url: 'https://doctorpez.mx/PuntoDeVenta/PuntoDeVentaFarmacias/Controladores/BusquedaEscanerInventarioTurnos.php',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(response) {
            if (response.success && response.producto) {
                // Verificar si está bloqueado por otro usuario en el mismo turno
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
                
                // Verificar si ya fue contado en otro turno hoy (bloqueado para otro turno)
                if (response.ya_contado_otro_turno) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Producto ya contado',
                        text: 'Este producto ya fue contado en el turno ' + (response.folio_turno_anterior || '') + ' hoy. No se puede contar el mismo producto en otro turno el mismo día.',
                        timer: 3500,
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
        url: 'https://doctorpez.mx/PuntoDeVenta/PuntoDeVentaFarmacias/api/gestion_turnos.php',
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
                // Actualizar el estado del turno activo inmediatamente
                if (turnoActivo && response.total_productos !== undefined) {
                    turnoActivo.Total_Productos = response.total_productos;
                    turnoActivo.Productos_Completados = response.productos_completados || 0;
                    // Actualizar la barra de progreso en tiempo real
                    actualizarBarraProgreso();
                }
                
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

// Función para actualizar la barra de progreso en tiempo real
function actualizarBarraProgreso() {
    if (!turnoActivo || !turnoActivo.ID_Turno) {
        return;
    }
    
    var total_productos = parseInt(turnoActivo.Total_Productos) || 0;
    var productos_completados = parseInt(turnoActivo.Productos_Completados) || 0;
    var porcentaje = total_productos > 0 
        ? Math.round((productos_completados / total_productos) * 100) 
        : 0;
    
    // Actualizar la barra de progreso visual
    var progressBar = $('.turno-activo .progress-bar');
    if (progressBar.length > 0) {
        progressBar.css('width', porcentaje + '%')
                   .attr('aria-valuenow', porcentaje)
                   .attr('aria-valuemax', 100)
                   .text(porcentaje + '%');
        
        // Actualizar el texto de progreso
        var progresoText = productos_completados + ' / ' + total_productos + ' completados';
        var limite = parseInt(turnoActivo.Limite_Productos) || 0;
        if (limite > 0) {
            progresoText += ' (Máx: ' + limite + ' productos)';
        }
        
        var textoProgreso = $('.turno-activo .progress').parent().find('small.texto-progreso');
        if (textoProgreso.length > 0) {
            textoProgreso.text(progresoText);
        } else {
            // Si no existe el elemento con la clase, buscar el small después del progress
            $('.turno-activo .progress').next('small').text(progresoText);
        }
        
        console.log('Progreso actualizado:', productos_completados, '/', total_productos, '(', porcentaje + '%)');
    } else {
        console.warn('No se encontró la barra de progreso para actualizar');
    }
}

// El autocomplete se inicializa en InventarioTurnos.php directamente
// Aquí solo mantenemos las funciones auxiliares
