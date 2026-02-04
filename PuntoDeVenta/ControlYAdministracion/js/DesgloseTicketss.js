// Variable global para la tabla DataTable
var tablaTickets = null;
var filtrosActuales = {
    filtro_sucursal: '',
    filtro_mes: '',
    filtro_anio: '',
    filtro_fecha_inicio: '',
    filtro_fecha_fin: ''
};

// Función para cargar los servicios (tabla de tickets)
function CargaServicios() {
    // Si la tabla ya existe, destruirla primero
    if (tablaTickets !== null && $.fn.DataTable.isDataTable('#Clientes')) {
        tablaTickets.destroy();
        tablaTickets = null;
    }
    
    // Cargar el HTML de la tabla
    $.get("https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/Controladores/DesgloseDeTickets.php", "", function(data) {
        $("#DataDeServicios").html(data);
        
        // Esperar un momento para que el DOM se actualice
        setTimeout(function() {
            inicializarTabla();
        }, 100);
    }).fail(function(xhr, status, error) {
        console.error('Error al cargar la tabla:', error);
        mostrarError('Error al cargar los tickets');
    });
}

// Función para inicializar la tabla DataTable
function inicializarTabla() {
    if ($.fn.DataTable.isDataTable('#Clientes')) {
        return;
    }
    
    mostrarCargando();
    
    tablaTickets = $('#Clientes').DataTable({
        "bProcessing": true,
        "ordering": true,
        "stateSave": true,
        "bAutoWidth": false,
        "order": [[0, "desc"]],
        "ajax": {
            "url": "https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/Controladores/ArrayDesgloseTickets.php",
            "type": "POST",
            "data": function(d) {
                // Agregar los filtros actuales a la petición
                d.filtro_sucursal = filtrosActuales.filtro_sucursal;
                d.filtro_mes = filtrosActuales.filtro_mes;
                d.filtro_anio = filtrosActuales.filtro_anio;
                d.filtro_fecha_inicio = filtrosActuales.filtro_fecha_inicio;
                d.filtro_fecha_fin = filtrosActuales.filtro_fecha_fin;
            },
            "dataSrc": function(json) {
                // Actualizar estadísticas si están disponibles
                if (json.estadisticas) {
                    actualizarEstadisticas(json.estadisticas);
                }
                
                // Manejar errores
                if (json.error) {
                    console.error('Error en la respuesta:', json.error);
                    mostrarError(json.error);
                    return [];
                }
                
                ocultarCargando();
                return json.aaData || [];
            },
            "error": function(xhr, error, thrown) {
                console.error('Error en AJAX:', error, thrown);
                ocultarCargando();
                mostrarError('Error al cargar los datos de tickets');
            }
        },
        "aoColumns": [
            { mData: 'NumberTicket' },
            { mData: 'Fecha' },
            { mData: 'Hora' },
            { mData: 'Vendedor' },
            { mData: 'Desglose' },
            { mData: 'Reimpresion' },
            { mData: 'Eliminar' }
        ],
        "lengthMenu": [[20, 150, 250, 500, -1], [20, 50, 250, 500, "Todos"]],
        "language": {
            "lengthMenu": "Mostrar _MENU_ registros",
            "sPaginationType": "extStyle",
            "zeroRecords": "No se encontraron resultados",
            "info": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
            "infoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
            "infoFiltered": "(filtrado de un total de _MAX_ registros)",
            "sSearch": "Buscar:",
            "paginate": {
                "first": '<i class="fas fa-angle-double-left"></i>',
                "last": '<i class="fas fa-angle-double-right"></i>',
                "next": '<i class="fas fa-angle-right"></i>',
                "previous": '<i class="fas fa-angle-left"></i>'
            },
            "processing": function() {
                mostrarCargando();
            }
        },
        "initComplete": function() {
            ocultarCargando();
        },
        "buttons": [
            {
                extend: 'excelHtml5',
                text: 'Exportar a Excel <i class="fas fa-file-excel"></i>',
                titleAttr: 'Exportar a Excel',
                title: 'Tickets de Venta',
                className: 'btn btn-success',
                exportOptions: {
                    columns: ':visible'
                }
            }
        ],
        "dom": '<"d-flex justify-content-between"lBf>rtip',
        "responsive": true
    });
}

// Función para actualizar estadísticas
function actualizarEstadisticas(stats) {
    if (stats) {
        $('#total-tickets').text(stats.total_tickets || 0);
        $('#total-ventas').text('$' + parseFloat(stats.total_ventas || 0).toLocaleString('es-MX', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
        $('#tickets-hoy').text(stats.tickets_hoy || 0);
        $('#promedio-ticket').text('$' + parseFloat(stats.promedio_ticket || 0).toLocaleString('es-MX', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
    }
}

// Función para aplicar filtros
function aplicarFiltros() {
    if (tablaTickets !== null && $.fn.DataTable.isDataTable('#Clientes')) {
        mostrarCargando();
        tablaTickets.ajax.reload(function() {
            ocultarCargando();
        });
    } else {
        CargaServicios();
    }
}

// Función para filtrar por sucursal
function filtrarPorSucursal(sucursalId) {
    filtrosActuales.filtro_sucursal = sucursalId;
    filtrosActuales.filtro_mes = '';
    filtrosActuales.filtro_anio = '';
    filtrosActuales.filtro_fecha_inicio = '';
    filtrosActuales.filtro_fecha_fin = '';
    aplicarFiltros();
    // Cerrar modal (compatible con Bootstrap 4 y 5)
    var modal = bootstrap.Modal.getInstance(document.getElementById('FiltroPorSucursal')) || $('#FiltroPorSucursal').data('bs.modal');
    if (modal) {
        modal.hide();
    } else {
        $('#FiltroPorSucursal').modal('hide');
    }
}

// Función para filtrar por mes
function filtrarPorMes(mes, anio) {
    filtrosActuales.filtro_mes = mes;
    filtrosActuales.filtro_anio = anio;
    filtrosActuales.filtro_sucursal = '';
    filtrosActuales.filtro_fecha_inicio = '';
    filtrosActuales.filtro_fecha_fin = '';
    aplicarFiltros();
    // Cerrar modal (compatible con Bootstrap 4 y 5)
    var modal = bootstrap.Modal.getInstance(document.getElementById('FiltroEspecificoMes')) || $('#FiltroEspecificoMes').data('bs.modal');
    if (modal) {
        modal.hide();
    } else {
        $('#FiltroEspecificoMes').modal('hide');
    }
}

// Función para filtrar por rango de fechas
function filtrarPorRangoFechas(fechaInicio, fechaFin, sucursalId) {
    filtrosActuales.filtro_fecha_inicio = fechaInicio;
    filtrosActuales.filtro_fecha_fin = fechaFin;
    filtrosActuales.filtro_sucursal = sucursalId || '';
    filtrosActuales.filtro_mes = '';
    filtrosActuales.filtro_anio = '';
    aplicarFiltros();
    // Cerrar modal (compatible con Bootstrap 4 y 5)
    var modal = bootstrap.Modal.getInstance(document.getElementById('FiltroRangoFechas')) || $('#FiltroRangoFechas').data('bs.modal');
    if (modal) {
        modal.hide();
    } else {
        $('#FiltroRangoFechas').modal('hide');
    }
}

// Función para limpiar filtros y recargar
function cargarTickets() {
    filtrosActuales = {
        filtro_sucursal: '',
        filtro_mes: '',
        filtro_anio: '',
        filtro_fecha_inicio: '',
        filtro_fecha_fin: ''
    };
    aplicarFiltros();
}

// Función para mostrar carga
function mostrarCargando() {
    $('#loading-overlay').css('display', 'flex');
}

// Función para ocultar carga
function ocultarCargando() {
    $('#loading-overlay').css('display', 'none');
}

// Función para mostrar error
function mostrarError(mensaje) {
    // Puedes usar un toast o alert según tu preferencia
    if (typeof toastr !== 'undefined') {
        toastr.error(mensaje);
    } else {
        alert('Error: ' + mensaje);
    }
}

// Función para mostrar éxito
function mostrarExito(mensaje) {
    if (typeof toastr !== 'undefined') {
        toastr.success(mensaje);
    }
}

// No cargar automáticamente - se llamará desde Tickets.php cuando el documento esté listo
