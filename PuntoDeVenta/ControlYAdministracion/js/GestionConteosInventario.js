// Cargar sucursales en el filtro
function CargarSucursales() {
    $.get("https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/Controladores/DataSucursales.php", 
        function(data) {
            if (data && data.length > 0) {
                var options = '<option value="">Todas las sucursales</option>';
                $.each(data, function(i, sucursal) {
                    options += '<option value="' + sucursal.ID_Sucursal + '">' + sucursal.Nombre_Sucursal + '</option>';
                });
                $('#filtroSucursal').html(options);
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

// Exportar reporte a Excel
function ExportarConteosInventario() {
    // Obtener valores de los filtros actuales
    var sucursal = $('#filtroSucursal').val() || '';
    var usuario = $('#filtroUsuario').val() || '';
    var fecha_desde = $('#fechaDesde').val() || '';
    var fecha_hasta = $('#fechaHasta').val() || '';
    
    // Construir URL con parámetros
    var url = 'https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/Controladores/ExportarGestionConteos.php?';
    var params = [];
    
    if (sucursal && sucursal !== '' && sucursal !== '0') {
        params.push('sucursal=' + encodeURIComponent(sucursal));
    }
    
    if (usuario && usuario !== '') {
        params.push('usuario=' + encodeURIComponent(usuario));
    }
    
    if (fecha_desde) {
        params.push('fecha_desde=' + encodeURIComponent(fecha_desde));
    }
    
    if (fecha_hasta) {
        params.push('fecha_hasta=' + encodeURIComponent(fecha_hasta));
    }
    
    url += params.join('&');
    
    // Mostrar loading
    $('#loading-overlay').show();
    $('#loading-text').text('Generando archivo Excel...');
    
    // Crear un iframe temporal para la descarga
    var iframe = document.createElement('iframe');
    iframe.style.display = 'none';
    iframe.src = url;
    document.body.appendChild(iframe);
    
    // Ocultar loading después de un tiempo
    setTimeout(function() {
        $('#loading-overlay').hide();
        document.body.removeChild(iframe);
    }, 3000);
}

// Liberar productos contados por sucursal y rango de fechas
function liberarProductosSucursal(sucursal, fechaDesde, fechaHasta) {
    $.ajax({
        url: 'https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/api/gestion_turnos.php',
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
