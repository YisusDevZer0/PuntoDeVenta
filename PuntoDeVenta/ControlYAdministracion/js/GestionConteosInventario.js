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
                    options += '<option value="' + usuario.Usuario_Selecciono + '">' + usuario.Usuario_Selecciono + '</option>';
                });
                $('#filtroUsuario').html(options);
            }
        }, 'json')
        .fail(function() {
            console.error('Error al cargar usuarios');
        });
}

// Cargar productos contados
function CargarProductosContados() {
    var filtros = {
        sucursal: $('#filtroSucursal').val() || '',
        usuario: $('#filtroUsuario').val() || '',
        fecha_desde: $('#fechaDesde').val() || '',
        fecha_hasta: $('#fechaHasta').val() || ''
    };
    
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
                
                if (response.aaData.length > 0) {
                    $.each(response.aaData, function(i, item) {
                        tabla += '<tr class="' + (item.clase_fila || '') + '">';
                        tabla += '<td>' + item.Folio_Turno + '</td>';
                        tabla += '<td>' + item.Cod_Barra + '</td>';
                        tabla += '<td>' + item.Nombre_Producto + '</td>';
                        tabla += '<td>' + item.Sucursal + '</td>';
                        tabla += '<td>' + item.Usuario + '</td>';
                        tabla += '<td>' + item.Existencias_Sistema + '</td>';
                        tabla += '<td>' + item.Existencias_Fisicas + '</td>';
                        tabla += '<td>' + item.Diferencia + '</td>';
                        tabla += '<td>' + item.Fecha_Turno + '</td>';
                        tabla += '<td>' + item.Fecha_Conteo + '</td>';
                        tabla += '<td>' + item.Estado_Turno + '</td>';
                        tabla += '</tr>';
                    });
                } else {
                    tabla += '<tr><td colspan="11" class="text-center">No se encontraron productos contados con los filtros seleccionados.</td></tr>';
                }
                
                tabla += '</tbody></table>';
                $('#tablaProductosContados').html(tabla);
                
                // Inicializar DataTable si está disponible
                if ($.fn.DataTable) {
                    if ($.fn.DataTable.isDataTable('#tablaConteos')) {
                        $('#tablaConteos').DataTable().destroy();
                    }
                    $('#tablaConteos').DataTable({
                        "language": {
                            "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json"
                        },
                        "pageLength": 25,
                        "order": [[8, "desc"]] // Ordenar por Fecha Turno descendente
                    });
                }
            } else {
                $('#tablaProductosContados').html('<div class="alert alert-info">No se encontraron productos contados.</div>');
            }
        },
        error: function() {
            $('#tablaProductosContados').html('<div class="alert alert-danger">Error al cargar los datos. Por favor, intenta nuevamente.</div>');
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
