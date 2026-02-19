var BASE_URL_CONFIG = 'https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion';

// Cargar sucursales en el filtro y en selects de configuración
function CargarSucursales() {
    $.get(BASE_URL_CONFIG + "/Controladores/DataSucursales.php", 
        function(data) {
            if (data && data.length > 0) {
                var options = '<option value="">Todas las sucursales</option>';
                var optionsConfig = '<option value="0">Global (por defecto)</option>';
                var optionsModal = '<option value="0">Global</option>';
                $.each(data, function(i, sucursal) {
                    options += '<option value="' + sucursal.ID_Sucursal + '">' + sucursal.Nombre_Sucursal + '</option>';
                    optionsConfig += '<option value="' + sucursal.ID_Sucursal + '">' + sucursal.Nombre_Sucursal + '</option>';
                    optionsModal += '<option value="' + sucursal.ID_Sucursal + '">' + sucursal.Nombre_Sucursal + '</option>';
                });
                $('#filtroSucursal').html(options);
                if ($('#configSucursalSelect').length) $('#configSucursalSelect').html(optionsConfig);
                if ($('#configEmpleadoSucursal').length) $('#configEmpleadoSucursal').html('<option value="0">Todas</option>' + optionsModal.replace('<option value="0">Global</option>', ''));
                if ($('#modalPeriodoSucursal').length) $('#modalPeriodoSucursal').html(optionsModal);
            }
        }, 'json')
        .fail(function() {
            console.error('Error al cargar sucursales');
        });
}

// Cargar usuarios que han realizado conteos
function CargarUsuarios() {
    $.get("https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/Controladores/DataUsuariosConteos.php", 
        function(data) {
            if (data && data.length > 0) {
                var options = '<option value="">Todos los usuarios</option>';
                $.each(data, function(i, usuario) {
                    // Escapar el valor para HTML y mantener exactamente como viene de la BD
                    var usuarioValor = $('<div>').text(usuario.Usuario_Selecciono || '').html();
                    var usuarioTexto = usuario.Usuario_Selecciono || '';
                    options += '<option value="' + usuarioValor + '">' + usuarioTexto + '</option>';
                });
                $('#filtroUsuario').html(options);
            } else {
                console.warn('No se encontraron usuarios en la respuesta');
            }
        }, 'json')
        .fail(function(xhr, status, error) {
            console.error('Error al cargar usuarios:', error, xhr.responseText);
        });
}

// Cargar productos contados
function CargarProductosContados() {
    // Obtener valores de los filtros
    var sucursal = $('#filtroSucursal').val();
    var usuario = $('#filtroUsuario').val();
    var fecha_desde = $('#fechaDesde').val();
    var fecha_hasta = $('#fechaHasta').val();
    
    // Construir objeto de filtros (solo incluir valores no vacíos)
    var filtros = {};
    
    if (sucursal && sucursal !== '' && sucursal !== '0') {
        filtros.sucursal = sucursal;
    }
    
    if (usuario && usuario !== '' && usuario !== null) {
        filtros.usuario = usuario;
    }
    
    if (fecha_desde && fecha_desde !== '') {
        filtros.fecha_desde = fecha_desde;
    }
    
    if (fecha_hasta && fecha_hasta !== '') {
        filtros.fecha_hasta = fecha_hasta;
    }
    
    $.ajax({
        url: 'https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/Controladores/DataGestionConteos.php',
        type: 'GET',
        data: filtros,
        dataType: 'json',
        beforeSend: function() {
            $('#tablaProductosContados').html('<div class="text-center"><div class="spinner-border" role="status"><span class="visually-hidden">Cargando...</span></div></div>');
        },
        success: function(response) {
            if (response.success && response.aaData) {
                // Actualizar estadísticas
                if (response.estadisticas) {
                    $('#totalProductos').text(response.estadisticas.total_productos || 0);
                    $('#sinDiferencias').text(response.estadisticas.sin_diferencias || 0);
                    $('#conDiferencias').text(response.estadisticas.con_diferencias || 0);
                    $('#usuariosActivos').text(response.estadisticas.usuarios_activos || 0);
                }
                
                // Construir tabla HTML
                var tabla = '<table class="table table-striped table-hover" id="tablaConteos">';
                tabla += '<thead><tr>';
                tabla += '<th>Folio Turno</th>';
                tabla += '<th>Código</th>';
                tabla += '<th>Producto</th>';
                tabla += '<th>Sucursal</th>';
                tabla += '<th>Usuario</th>';
                tabla += '<th>Exist. Sistema</th>';
                tabla += '<th>Exist. Físicas</th>';
                tabla += '<th>Diferencia</th>';
                tabla += '<th>Fecha Turno</th>';
                tabla += '<th>Fecha Conteo</th>';
                tabla += '<th>Estado Turno</th>';
                tabla += '</tr></thead><tbody>';
                
                var tieneDatos = response.aaData && response.aaData.length > 0;
                
                if (tieneDatos) {
                    $.each(response.aaData, function(i, item) {
                        tabla += '<tr class="' + (item.clase_fila || '') + '">';
                        tabla += '<td>' + (item.Folio_Turno || '') + '</td>';
                        tabla += '<td>' + (item.Cod_Barra || '') + '</td>';
                        tabla += '<td>' + (item.Nombre_Producto || '') + '</td>';
                        tabla += '<td>' + (item.Sucursal || '') + '</td>';
                        tabla += '<td>' + (item.Usuario || '') + '</td>';
                        tabla += '<td>' + (item.Existencias_Sistema || '0') + '</td>';
                        tabla += '<td>' + (item.Existencias_Fisicas || '0') + '</td>';
                        tabla += '<td>' + (item.Diferencia || '') + '</td>';
                        tabla += '<td>' + (item.Fecha_Turno || '') + '</td>';
                        tabla += '<td>' + (item.Fecha_Conteo || '-') + '</td>';
                        tabla += '<td>' + (item.Estado_Turno || '') + '</td>';
                        tabla += '</tr>';
                    });
                } else {
                    tabla += '<tr><td colspan="11" class="text-center">No se encontraron productos contados con los filtros seleccionados.</td></tr>';
                }
                
                tabla += '</tbody></table>';
                $('#tablaProductosContados').html(tabla);
                
                // Inicializar DataTable solo si hay datos
                if (tieneDatos) {
                    // Esperar a que el DOM esté listo antes de inicializar DataTable
                    setTimeout(function() {
                        // Verificar que la tabla existe en el DOM
                        var $tabla = $('#tablaConteos');
                        if ($tabla.length > 0 && $.fn.DataTable) {
                            // Destruir instancia anterior si existe
                            try {
                                if ($.fn.DataTable.isDataTable('#tablaConteos')) {
                                    $tabla.DataTable().destroy();
                                }
                            } catch (e) {
                                console.warn('Error al destruir DataTable anterior:', e);
                            }
                            
                            // Verificar que la tabla tiene filas válidas (sin colspan)
                            var filasNormales = $tabla.find('tbody tr:not(:has(td[colspan]))').length;
                            
                            if (filasNormales > 0) {
                                try {
                                    $tabla.DataTable({
                                        "language": {
                                            "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json"
                                        },
                                        "pageLength": 25,
                                        "order": [[8, "desc"]], // Ordenar por Fecha Turno descendente
                                        "responsive": true,
                                        "autoWidth": false
                                    });
                                } catch (e) {
                                    console.error('Error al inicializar DataTable:', e);
                                }
                            }
                        }
                    }, 100); // Pequeño delay para asegurar que el DOM está listo
                }
            } else {
                $('#tablaProductosContados').html('<div class="alert alert-info">No se encontraron productos contados.</div>');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error al cargar datos:', error);
            console.error('Status:', status);
            console.error('Response:', xhr.responseText);
            $('#tablaProductosContados').html('<div class="alert alert-danger">Error al cargar los datos. Por favor, intenta nuevamente.<br><small>' + error + '</small></div>');
        }
    });
}

// Mostrar modal para liberar productos
function mostrarModalLiberarProductos() {
    // Obtener sucursales para el modal
    var sucursalSeleccionada = $('#filtroSucursal').val();
    var fechaDesde = $('#fechaDesde').val() || '';
    var fechaHasta = $('#fechaHasta').val() || '';
    
    Swal.fire({
        title: 'Liberar Productos Contados',
        html: '<div class="text-start">' +
              '<p class="mb-3">Esta acción cambiará el estado de los productos <strong>completados</strong> a <strong>liberados</strong>, permitiendo que puedan ser contados nuevamente en otros turnos.</p>' +
              '<div class="mb-3">' +
              '<label class="form-label">Sucursal:</label>' +
              '<select class="form-select" id="swal-sucursal">' +
              '<option value="">Todas las sucursales</option>' +
              '</select>' +
              '</div>' +
              '<div class="mb-3">' +
              '<label class="form-label">Fecha desde:</label>' +
              '<input type="date" class="form-control" id="swal-fecha-desde" value="' + fechaDesde + '">' +
              '</div>' +
              '<div class="mb-3">' +
              '<label class="form-label">Fecha hasta:</label>' +
              '<input type="date" class="form-control" id="swal-fecha-hasta" value="' + fechaHasta + '">' +
              '</div>' +
              '<div class="alert alert-warning">' +
              '<strong>Advertencia:</strong> Esta acción es irreversible y afectará todos los productos contados que coincidan con los filtros seleccionados.' +
              '</div>' +
              '</div>',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, liberar productos',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#ffc107',
        cancelButtonColor: '#6c757d',
        didOpen: function() {
            // Cargar sucursales en el select del modal
            $.get("https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/Controladores/DataSucursales.php", 
                function(data) {
                    if (data && data.length > 0) {
                        var options = '<option value="">Todas las sucursales</option>';
                        $.each(data, function(i, sucursal) {
                            var selected = (sucursal.ID_Sucursal == sucursalSeleccionada) ? 'selected' : '';
                            options += '<option value="' + sucursal.ID_Sucursal + '" ' + selected + '>' + sucursal.Nombre_Sucursal + '</option>';
                        });
                        $('#swal-sucursal').html(options);
                    }
                }, 'json');
        },
        preConfirm: function() {
            var sucursal = $('#swal-sucursal').val();
            var fechaDesde = $('#swal-fecha-desde').val();
            var fechaHasta = $('#swal-fecha-hasta').val();
            
            if (!fechaDesde || !fechaHasta) {
                Swal.showValidationMessage('Por favor, selecciona ambas fechas (desde y hasta)');
                return false;
            }
            
            return {
                sucursal: sucursal || '',
                fecha_desde: fechaDesde,
                fecha_hasta: fechaHasta
            };
        }
    }).then((result) => {
        if (result.isConfirmed && result.value) {
            liberarProductosSucursal(result.value.sucursal, result.value.fecha_desde, result.value.fecha_hasta);
        }
    });
}

// Exportar reporte a Excel - Asegurar que esté en scope global
window.ExportarConteosInventario = function() {
    // Obtener valores de los filtros actuales
    var sucursal = $('#filtroSucursal').val() || '';
    var usuario = $('#filtroUsuario').val() || '';
    var fecha_desde = $('#fechaDesde').val() || '';
    var fecha_hasta = $('#fechaHasta').val() || '';
    
    // Construir objeto con filtros
    var filtros = {};
    
    if (sucursal && sucursal !== '' && sucursal !== '0') {
        filtros.sucursal = sucursal;
    }
    
    if (usuario && usuario !== '' && usuario !== null) {
        filtros.usuario = usuario;
    }
    
    if (fecha_desde && fecha_desde !== '') {
        filtros.fecha_desde = fecha_desde;
    }
    
    if (fecha_hasta && fecha_hasta !== '') {
        filtros.fecha_hasta = fecha_hasta;
    }
    
    // Crear URL con parámetros usando URLSearchParams
    const params = new URLSearchParams(filtros);
    const url = 'https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/Controladores/ExportarGestionConteos.php?' + params.toString();
    
    console.log('Exportando reporte a Excel. URL:', url);
    console.log('Filtros aplicados:', filtros);
    
    // Abrir en nueva ventana para descargar (método más confiable)
    var ventana = window.open(url, '_blank');
    
    // Si el navegador bloquea la ventana emergente, mostrar mensaje
    if (!ventana || ventana.closed || typeof ventana.closed == 'undefined') {
        alert('Por favor, permite las ventanas emergentes en tu navegador para descargar el archivo Excel.');
    }
}

// --- Configuración inventario por turnos (periodos, sucursal, empleado) ---
var configSucursalList = [];

function CargarConfigInventario() {
    $.get(BASE_URL_CONFIG + '/api/config_inventario_turnos.php', function(data) {
        if (data.periodos) renderTablaPeriodos(data.periodos);
        if (data.config_sucursal) {
            configSucursalList = data.config_sucursal;
            actualizarFormConfigSucursal(data.config_sucursal);
        }
        if (data.config_empleados) renderListaConfigEmpleados(data.config_empleados);
    }, 'json').fail(function() { console.error('Error al cargar configuración'); });
}

function renderTablaPeriodos(periodos) {
    var mapSuc = {};
    $('#configSucursalSelect option').each(function() { var v = $(this).val(); if (v !== '0') mapSuc[v] = $(this).text(); });
    mapSuc['0'] = 'Global';
    var tbody = '';
    (periodos || []).forEach(function(p) {
        var suc = mapSuc[p.Fk_sucursal] || ('Suc. ' + p.Fk_sucursal);
        tbody += '<tr><td>' + suc + '</td><td>' + (p.Fecha_Inicio || '') + '</td><td>' + (p.Fecha_Fin || '') + '</td><td>' + (p.Nombre_Periodo || '-') + '</td><td>' + (p.Codigo_Externo || '-') + '</td><td>' + (p.Activo ? 'Sí' : 'No') + '</td><td><button type="button" class="btn btn-sm btn-outline-secondary" onclick="editarPeriodo(' + p.ID_Periodo + ',\'' + (p.Fecha_Inicio||'') + '\',\'' + (p.Fecha_Fin||'') + '\',' + p.Fk_sucursal + ',\'' + (p.Nombre_Periodo||'').replace(/'/g, "\\'") + '\',\'' + (p.Codigo_Externo||'').replace(/'/g, "\\'") + '\',' + (p.Activo?1:0) + ')">Editar</button></td></tr>';
    });
    $('#tablaPeriodos tbody').html(tbody || '<tr><td colspan="7" class="text-muted">No hay periodos. Agrega uno para restringir por fechas.</td></tr>');
}

function abrirModalPeriodo() {
    $('#modalPeriodoId').val('');
    $('#modalPeriodoInicio').val('');
    $('#modalPeriodoFin').val('');
    $('#modalPeriodoNombre').val('');
    $('#modalPeriodoCodigo').val('');
    $('#modalPeriodoActivo').prop('checked', true);
    $('#modalPeriodo').modal('show');
}

function editarPeriodo(id, inicio, fin, fkSuc, nombre, codigo, activo) {
    $('#modalPeriodoId').val(id);
    $('#modalPeriodoInicio').val(inicio);
    $('#modalPeriodoFin').val(fin);
    $('#modalPeriodoNombre').val(nombre || '');
    $('#modalPeriodoCodigo').val(codigo || '');
    $('#modalPeriodoActivo').prop('checked', !!activo);
    $('#modalPeriodoSucursal').val(fkSuc || '0');
    $('#modalPeriodo').modal('show');
}

function guardarPeriodo() {
    var id = $('#modalPeriodoId').val();
    var fk_sucursal = $('#modalPeriodoSucursal').val() || 0;
    var Fecha_Inicio = $('#modalPeriodoInicio').val();
    var Fecha_Fin = $('#modalPeriodoFin').val();
    var Nombre_Periodo = $('#modalPeriodoNombre').val();
    var Codigo_Externo = $('#modalPeriodoCodigo').val();
    var Activo = $('#modalPeriodoActivo').is(':checked') ? 1 : 0;
    if (!Fecha_Inicio || !Fecha_Fin) { alert('Fecha inicio y fecha fin son obligatorias.'); return; }
    $.post(BASE_URL_CONFIG + '/api/config_inventario_turnos.php', {
        accion: 'guardar_periodo',
        ID_Periodo: id,
        Fk_sucursal: fk_sucursal,
        Fecha_Inicio: Fecha_Inicio,
        Fecha_Fin: Fecha_Fin,
        Nombre_Periodo: Nombre_Periodo,
        Codigo_Externo: Codigo_Externo,
        Activo: Activo
    }, function(res) {
        if (res.success) { $('#modalPeriodo').modal('hide'); CargarConfigInventario(); if (typeof Swal !== 'undefined') Swal.fire({ icon: 'success', title: 'Guardado', text: res.message }); else alert(res.message); }
        else { if (typeof Swal !== 'undefined') Swal.fire({ icon: 'error', title: 'Error', text: res.message }); else alert(res.message); }
    }, 'json').fail(function() { alert('Error de conexión'); });
}

function actualizarFormConfigSucursal(configs) {
    if (!configs || !configs.length) return;
    var sel = $('#configSucursalSelect');
    if (!sel.length) return;
    var suc = sel.val() || '0';
    var c = configs.find(function(x) { return String(x.Fk_sucursal) === String(suc); }) || configs.find(function(x) { return x.Fk_sucursal === 0; });
    if (c) {
        $('#configSucursalMaxTurnos').val(c.Max_Turnos_Por_Dia);
        $('#configSucursalMaxProductos').val(c.Max_Productos_Por_Turno);
    }
}

function guardarConfigSucursal() {
    var Fk_sucursal = $('#configSucursalSelect').val() || 0;
    var Max_Turnos_Por_Dia = $('#configSucursalMaxTurnos').val() || 0;
    var Max_Productos_Por_Turno = $('#configSucursalMaxProductos').val() || 50;
    $.post(BASE_URL_CONFIG + '/api/config_inventario_turnos.php', {
        accion: 'guardar_config_sucursal',
        Fk_sucursal: Fk_sucursal,
        Max_Turnos_Por_Dia: Max_Turnos_Por_Dia,
        Max_Productos_Por_Turno: Max_Productos_Por_Turno
    }, function(res) {
        if (res.success) { CargarConfigInventario(); if (typeof Swal !== 'undefined') Swal.fire({ icon: 'success', title: 'Guardado', text: res.message }); else alert(res.message); }
        else { if (typeof Swal !== 'undefined') Swal.fire({ icon: 'error', title: 'Error', text: res.message }); else alert(res.message); }
    }, 'json').fail(function() { alert('Error de conexión'); });
}

function guardarConfigEmpleado() {
    var Fk_usuario = $('#configEmpleadoUsuario').val();
    var Fk_sucursal = $('#configEmpleadoSucursal').val() || 0;
    var Max_Turnos_Por_Dia = $('#configEmpleadoMaxTurnos').val() || 0;
    var Max_Productos_Por_Turno = $('#configEmpleadoMaxProductos').val() || 0;
    if (!Fk_usuario) { alert('Selecciona un empleado.'); return; }
    $.post(BASE_URL_CONFIG + '/api/config_inventario_turnos.php', {
        accion: 'guardar_config_empleado',
        Fk_usuario: Fk_usuario,
        Fk_sucursal: Fk_sucursal,
        Max_Turnos_Por_Dia: Max_Turnos_Por_Dia,
        Max_Productos_Por_Turno: Max_Productos_Por_Turno
    }, function(res) {
        if (res.success) { CargarConfigInventario(); if (typeof Swal !== 'undefined') Swal.fire({ icon: 'success', title: 'Guardado', text: res.message }); else alert(res.message); }
        else { if (typeof Swal !== 'undefined') Swal.fire({ icon: 'error', title: 'Error', text: res.message }); else alert(res.message); }
    }, 'json').fail(function() { alert('Error de conexión'); });
}

function renderListaConfigEmpleados(lista) {
    var html = '';
    (lista || []).forEach(function(e) {
        html += '<div class="border-bottom py-1 small">' + (e.Nombre_Apellidos || 'Id ' + e.Fk_usuario) + ' — Suc. ' + e.Fk_sucursal + ': máx ' + e.Max_Turnos_Por_Dia + ' turnos/día, ' + e.Max_Productos_Por_Turno + ' prod/turno</div>';
    });
    $('#listaConfigEmpleados').html(html || '<span class="text-muted">Sin config por empleado. Usa el formulario para agregar.</span>');
}

function CargarUsuariosParaConfig() {
    $.get(BASE_URL_CONFIG + '/api/config_inventario_turnos.php', { action: 'usuarios' }, function(data) {
        var opts = '<option value="">-- Seleccionar --</option>';
        (data.usuarios || []).forEach(function(u) {
            opts += '<option value="' + u.Id_PvUser + '">' + (u.Nombre_Apellidos || '') + '</option>';
        });
        $('#configEmpleadoUsuario').html(opts);
    }, 'json');
}

$(document).on('change', '#configSucursalSelect', function() {
    var suc = $(this).val();
    var c = configSucursalList.find(function(x) { return String(x.Fk_sucursal) === String(suc); }) || configSucursalList.find(function(x) { return x.Fk_sucursal === 0; });
    if (c) {
        $('#configSucursalMaxTurnos').val(c.Max_Turnos_Por_Dia);
        $('#configSucursalMaxProductos').val(c.Max_Productos_Por_Turno);
    } else {
        $('#configSucursalMaxTurnos').val(0);
        $('#configSucursalMaxProductos').val(50);
    }
});

// Liberar productos contados por sucursal y rango de fechas
function liberarProductosSucursal(sucursal, fechaDesde, fechaHasta) {
    $.ajax({
        url: BASE_URL_CONFIG + '/api/gestion_turnos.php',
        type: 'POST',
        data: {
            accion: 'liberar_productos_sucursal',
            sucursal: sucursal || '',
            fecha_desde: fechaDesde,
            fecha_hasta: fechaHasta
        },
        dataType: 'json',
        beforeSend: function() {
            Swal.fire({
                title: 'Procesando...',
                text: 'Liberando productos contados',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
        },
        success: function(response) {
            if (response.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Productos liberados',
                    html: '<p>Se liberaron <strong>' + (response.productos_liberados || 0) + '</strong> productos contados.</p>' +
                          '<p>Ahora estos productos pueden ser contados nuevamente en otros turnos.</p>',
                    confirmButtonText: 'Aceptar'
                }).then(() => {
                    // Recargar la tabla
                    CargarProductosContados();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: response.message || 'No se pudieron liberar los productos',
                    confirmButtonText: 'Aceptar'
                });
            }
        },
        error: function() {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Error al comunicarse con el servidor',
                confirmButtonText: 'Aceptar'
            });
        }
    });
}
