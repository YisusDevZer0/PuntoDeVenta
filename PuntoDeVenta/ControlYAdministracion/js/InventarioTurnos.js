// Variable para el timeout del buscador (debounce)
var buscarTimeout = null;

// Variables para procesamiento de escáner
var scanBuffer = "";
var scanInterval = 5;
var isScannerInput = false;
var ultimoEscaneo = null;
var tiempoUltimoEscaneo = 0;

var BASE_URL = 'https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion';

// Función para cargar productos del turno (0 = sin turno, mostrar disponibles)
function CargarProductosTurno(idTurno) {
    idTurno = parseInt(idTurno) || 0;
    if (idTurno === undefined || idTurno === null || isNaN(idTurno)) {
        idTurno = 0;
    }
    
    var buscar = $('#buscar-producto').val();
    var estado = $('#filtro-estado-producto').val();
    
    var url = BASE_URL + '/Controladores/DataInventarioTurnos.php';
    var params = ['id_turno=' + (idTurno || 0)];
    if (buscar && buscar.trim() !== '') {
        params.push('buscar=' + encodeURIComponent(buscar.trim()));
    }
    if (estado && estado !== '') {
        params.push('estado=' + encodeURIComponent(estado));
    }
    url += '?' + params.join('&');
    
    if ($.fn.DataTable.isDataTable('#tablaInventarioTurnos')) {
        $('#tablaInventarioTurnos').DataTable().destroy();
        $('#tablaInventarioTurnos').empty();
    }
    
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
            { "data": "Lotes_Caducidad_Texto", "title": "Lotes / Caducidad", "defaultContent": "-", "orderable": false },
            { "data": "Lote", "title": "Lote conteo", "defaultContent": "-" },
            { "data": "Fecha_Caducidad", "title": "Fecha Cad. conteo", "defaultContent": "-" },
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
        }
    });
}

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
                url: BASE_URL + '/api/gestion_turnos.php',
                type: 'POST',
                data: { accion: 'iniciar' },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        Swal.fire('¡Éxito!', response.message, 'success').then(() => { location.reload(); });
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
                url: BASE_URL + '/api/gestion_turnos.php',
                type: 'POST',
                data: { accion: 'pausar', id_turno: idTurno },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        Swal.fire('¡Éxito!', response.message, 'success').then(() => { location.reload(); });
                    } else {
                        Swal.fire('Error', response.message, 'error');
                    }
                }
            });
        }
    });
}

function reanudarTurno(idTurno) {
    $.ajax({
        url: BASE_URL + '/api/gestion_turnos.php',
        type: 'POST',
        data: { accion: 'reanudar', id_turno: idTurno },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                Swal.fire('¡Éxito!', response.message, 'success').then(() => { location.reload(); });
            } else {
                Swal.fire('Error', response.message, 'error');
            }
        }
    });
}

function finalizarTurno(idTurno) {
    if (!turnoActivo || !turnoActivo.ID_Turno) {
        Swal.fire('Error', 'No hay un turno activo', 'error');
        return;
    }
    $.ajax({
        url: BASE_URL + '/api/gestion_turnos.php',
        type: 'POST',
        data: { accion: 'obtener_estado', id_turno: idTurno },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                var total_productos = response.total_productos || 0;
                var productos_completados = response.productos_completados || 0;
                var limite_requerido = turnoActivo.Limite_Productos || 50;
                if (productos_completados < limite_requerido) {
                    Swal.fire({
                        title: 'No se puede finalizar el turno',
                        html: '<p>Debes tener <strong>' + limite_requerido + '</strong> productos contados.</p><p>Tienes <strong>' + productos_completados + '</strong> de ' + limite_requerido + '.</p>',
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

function confirmarFinalizarTurno(idTurno, total_productos, productos_completados) {
    Swal.fire({
        title: '¿Finalizar el turno?',
        html: '<p>Has completado <strong>' + productos_completados + '</strong> productos contados.</p><p>Una vez finalizado, no podrás agregar más productos.</p>',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, finalizar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: BASE_URL + '/api/gestion_turnos.php',
                type: 'POST',
                data: { accion: 'finalizar', id_turno: idTurno },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        Swal.fire('¡Éxito!', response.message, 'success').then(() => { location.reload(); });
                    } else {
                        Swal.fire('Error', response.message, 'error');
                    }
                },
                error: function(xhr) {
                    var errorMsg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Error al comunicarse con el servidor';
                    Swal.fire('Error', errorMsg, 'error');
                }
            });
        }
    });
}

$(document).on('click', '.btn-seleccionar-producto', function() {
    var idProducto = $(this).data('id');
    var codigo = $(this).data('codigo');
    if (!turnoActivo) {
        Swal.fire('Error', 'No hay un turno activo', 'error');
        return;
    }
    $.ajax({
        url: BASE_URL + '/api/gestion_turnos.php',
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
                if (turnoActivo && response.total_productos !== undefined) {
                    turnoActivo.Total_Productos = response.total_productos;
                    turnoActivo.Productos_Completados = response.productos_completados || 0;
                    actualizarBarraProgreso();
                }
                Swal.fire({
                    icon: 'success',
                    title: 'Producto agregado',
                    timer: 1500,
                    showConfirmButton: false,
                    toast: true,
                    position: 'top-end'
                });
                CargarProductosTurno(turnoActivo.ID_Turno);
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

$(document).on('click', '.btn-contar-producto', function() {
    var idRegistro = $(this).data('id');
    var lotesData = $(this).data('lotes');
    var lotes = (typeof lotesData === 'string') ? (lotesData ? JSON.parse(lotesData) : []) : (lotesData || []);
    var hayLotes = lotes && lotes.length > 0;
    
    var htmlLotes = '';
    if (hayLotes) {
        htmlLotes = '<div class="mb-3"><label class="form-label fw-semibold mb-2"><i class="fa-solid fa-boxes-stacked me-1"></i> Lotes y caducidades del producto</label>' +
            '<div class="border rounded p-2 bg-light" style="max-height: 140px; overflow-y: auto;">';
        lotes.forEach(function(l, i) {
            var fechaF = l.Fecha_Caducidad ? (function(d) { var p = d.split('-'); return p[2] + '/' + p[1] + '/' + p[0]; })(l.Fecha_Caducidad) : '-';
            htmlLotes += '<div class="form-check mb-1">' +
                '<input class="form-check-input opcion-lote" type="radio" name="opcion_lote" id="lote-ex-' + i + '" value="' + i + '">' +
                '<label class="form-check-label small" for="lote-ex-' + i + '"><strong>' + (l.Lote || '-') + '</strong> · Cad: ' + fechaF + ' · ' + (l.Existencias || 0) + ' und</label></div>';
        });
        htmlLotes += '</div></div>';
    }
    
    var mostrarNuevo = !hayLotes;
    var html = '<div class="text-start conteo-modal-fields">' +
        '<div class="mb-3">' +
        '<label class="form-label fw-semibold mb-1">Existencias físicas <span class="text-danger">*</span></label>' +
        '<input type="number" id="existencias-fisicas" class="form-control form-control-lg" placeholder="Cantidad contada" min="0" required autofocus>' +
        '</div>' + htmlLotes +
        '<div class="mb-2"><div class="form-check">' +
        '<input class="form-check-input" type="radio" name="opcion_lote" id="opcion-nuevo-lote" value="nuevo"' + (mostrarNuevo ? ' checked' : '') + '>' +
        '<label class="form-check-label fw-semibold" for="opcion-nuevo-lote">Agregar lote nuevo</label></div></div>' +
        '<div id="conteo-nuevo-lote-fields" class="mb-3 ms-3 border-start border-2 border-secondary ps-3" style="' + (mostrarNuevo ? '' : 'display:none;') + '">' +
        '<label class="form-label mb-1">Lote nuevo</label>' +
        '<input type="text" id="conteo-lote" class="form-control mb-2" placeholder="Ej. LOTE-2024-001"' + (mostrarNuevo ? '' : ' disabled') + '>' +
        '<label class="form-label mb-1">Fecha de caducidad</label>' +
        '<input type="date" id="conteo-fecha-caducidad" class="form-control"' + (mostrarNuevo ? '' : ' disabled') + '></div></div>';
    
    Swal.fire({
        title: '<i class="fa-solid fa-calculator text-primary me-2"></i>Registrar conteo físico',
        html: html,
        showCancelButton: true,
        confirmButtonText: '<i class="fa-solid fa-check me-1"></i> Guardar',
        cancelButtonText: 'Cancelar',
        width: '520px',
        customClass: { confirmButton: 'btn btn-primary', cancelButton: 'btn btn-outline-secondary' },
        didOpen: function() {
            $('#opcion-nuevo-lote').on('change', function() {
                var c = this.checked;
                $('#conteo-nuevo-lote-fields').toggle(c);
                $('#conteo-lote, #conteo-fecha-caducidad').prop('disabled', !c);
            });
            $('.opcion-lote').on('change', function() {
                $('#conteo-nuevo-lote-fields').hide();
                $('#conteo-lote, #conteo-fecha-caducidad').val('').prop('disabled', true);
            });
        },
        preConfirm: () => {
            var ex = document.getElementById('existencias-fisicas').value;
            if (!ex || ex.trim() === '' || parseInt(ex, 10) < 0) {
                Swal.showValidationMessage('Ingrese existencias físicas válidas');
                return false;
            }
            var opcionNuevo = document.getElementById('opcion-nuevo-lote').checked;
            var lote = '', fecha = '';
            if (opcionNuevo) {
                lote = document.getElementById('conteo-lote').value.trim();
                fecha = document.getElementById('conteo-fecha-caducidad').value || '';
            } else {
                var sel = document.querySelector('input[name="opcion_lote"]:checked');
                if (sel && sel.value !== 'nuevo' && lotes[parseInt(sel.value, 10)]) {
                    var e = lotes[parseInt(sel.value, 10)];
                    lote = e.Lote || '';
                    fecha = e.Fecha_Caducidad || '';
                }
            }
            return { existencias: ex, lote: lote, fecha: fecha };
        }
    }).then((result) => {
        if (result.isConfirmed && result.value) {
            var d = result.value;
            var payload = { accion: 'contar_producto', id_registro: idRegistro, existencias_fisicas: d.existencias };
            if (d.lote) payload.lote = d.lote;
            if (d.fecha) payload.fecha_caducidad = d.fecha;
            $.ajax({
                url: BASE_URL + '/api/gestion_turnos.php',
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
                            CargarProductosTurno(turnoActivo.ID_Turno);
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

function verHistorialTurnos() {
    Swal.fire('Información', 'Funcionalidad de historial en desarrollo', 'info');
}

function buscarYSeleccionarProducto(codigo) {
    if (!codigo || codigo.trim() === '') return;
    if (!turnoActivo || !turnoActivo.ID_Turno) {
        Swal.fire('Error', 'No hay un turno activo', 'error');
        limpiarCampoBusqueda();
        return;
    }
    var formData = new FormData();
    formData.append('codigo', codigo.trim());
    formData.append('id_turno', turnoActivo.ID_Turno);
    
    $.ajax({
        url: BASE_URL + '/Controladores/BusquedaEscanerInventarioTurnos.php',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(response) {
            if (response.success && response.producto) {
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
                if (response.ya_contado_otro_turno) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Producto ya contado',
                        text: 'Este producto ya fue contado en el turno ' + (response.folio_turno_anterior || '') + ' hoy.',
                        timer: 3500,
                        showConfirmButton: false
                    });
                    limpiarCampoBusqueda();
                    return;
                }
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

function seleccionarProductoEnTurno(idProducto, codigo, nombre) {
    $.ajax({
        url: BASE_URL + '/api/gestion_turnos.php',
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
                if (turnoActivo && response.total_productos !== undefined) {
                    turnoActivo.Total_Productos = response.total_productos;
                    turnoActivo.Productos_Completados = response.productos_completados || 0;
                    actualizarBarraProgreso();
                }
                Swal.fire({
                    icon: 'success',
                    title: 'Producto agregado',
                    text: nombre,
                    timer: 1500,
                    showConfirmButton: false,
                    toast: true,
                    position: 'top-end'
                });
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

function limpiarCampoBusqueda() {
    $('#buscar-producto').val('');
    $('#buscar-producto').focus();
}

function actualizarBarraProgreso() {
    if (!turnoActivo || !turnoActivo.ID_Turno) return;
    var total_productos = parseInt(turnoActivo.Total_Productos) || 0;
    var productos_completados = parseInt(turnoActivo.Productos_Completados) || 0;
    var limite = parseInt(turnoActivo.Limite_Productos) || 0;
    var porcentaje = limite > 0 ? Math.round((productos_completados / limite) * 100) : 0;
    porcentaje = Math.min(100, porcentaje);
    
    var progressBar = $('.turno-activo .progress-bar');
    if (progressBar.length > 0) {
        progressBar.css('width', porcentaje + '%').attr('aria-valuenow', porcentaje).attr('aria-valuemax', 100).text(porcentaje + '%');
        var progresoText = productos_completados + ' / ' + limite + ' completados';
        if (total_productos > 0) progresoText += ' (Máx: ' + limite + ' productos)';
        var textoProgreso = $('.turno-activo .texto-progreso');
        if (textoProgreso.length > 0) {
            textoProgreso.text(progresoText);
        } else {
            $('.turno-activo .progress').next('small').text(progresoText);
        }
    }
}
