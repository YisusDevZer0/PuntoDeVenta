function CargarConteosPausados() {
    const filtros = {
        sucursal: $('#filtroSucursal').val(),
        usuario: $('#filtroUsuario').val(),
        fechaDesde: $('#fechaDesde').val(),
        fechaHasta: $('#fechaHasta').val()
    };

    $.get("https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/Controladores/DataConteosPausados.php", 
        filtros, 
        function(data) {
            $("#DataConteosPausados").html(data);
        })
        .fail(function() {
            $("#DataConteosPausados").html('<div class="alert alert-danger">Error al cargar los datos de conteos pausados</div>');
        });
}

function CargarSucursales() {
    $.get("https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/Controladores/DataSucursales.php", 
        {}, 
        function(data) {
            $("#filtroSucursal").html(data);
        })
        .fail(function() {
            console.error('Error al cargar sucursales');
        });
}

function CargarUsuarios() {
    $.get("https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/Controladores/DataUsuarios.php", 
        {}, 
        function(data) {
            $("#filtroUsuario").html(data);
        })
        .fail(function() {
            console.error('Error al cargar usuarios');
        });
}

function CargarEstadisticas() {
    const filtros = {
        sucursal: $('#filtroSucursal').val(),
        usuario: $('#filtroUsuario').val(),
        fechaDesde: $('#fechaDesde').val(),
        fechaHasta: $('#fechaHasta').val()
    };

    $.get("https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/Controladores/EstadisticasConteosPausados.php", 
        filtros, 
        function(data) {
            if (data.success) {
                $('#totalPausados').text(data.totalPausados || 0);
                $('#sucursalesAfectadas').text(data.sucursalesAfectadas || 0);
                $('#conteosAntiguos').text(data.conteosAntiguos || 0);
                $('#reanudadosHoy').text(data.reanudadosHoy || 0);
            }
        }, 'json')
        .fail(function() {
            console.error('Error al cargar estadísticas');
        });
}

// Cargar datos al inicializar
$(document).ready(function() {
    CargarSucursales();
    CargarUsuarios();
    CargarConteosPausados();
    CargarEstadisticas();
});

// Función para exportar datos a Excel
function ExportarConteosPausados() {
    const filtros = {
        sucursal: $('#filtroSucursal').val(),
        usuario: $('#filtroUsuario').val(),
        fechaDesde: $('#fechaDesde').val(),
        fechaHasta: $('#fechaHasta').val(),
        exportar: true
    };

    // Crear URL con parámetros
    const params = new URLSearchParams(filtros);
    const url = `https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/Controladores/ExportarConteosPausados.php?${params.toString()}`;
    
    // Abrir en nueva ventana para descargar
    window.open(url, '_blank');
}

// Función para imprimir reporte
function ImprimirConteosPausados() {
    const filtros = {
        sucursal: $('#filtroSucursal').val(),
        usuario: $('#filtroUsuario').val(),
        fechaDesde: $('#fechaDesde').val(),
        fechaHasta: $('#fechaHasta').val(),
        imprimir: true
    };

    // Crear URL con parámetros
    const params = new URLSearchParams(filtros);
    const url = `https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/Controladores/ImprimirConteosPausados.php?${params.toString()}`;
    
    // Abrir en nueva ventana para imprimir
    window.open(url, '_blank');
}

// Función para limpiar filtros
function LimpiarFiltros() {
    $('#filtroSucursal').val('');
    $('#filtroUsuario').val('');
    $('#fechaDesde').val('');
    $('#fechaHasta').val(new Date().toISOString().split('T')[0]);
    CargarConteosPausados();
    CargarEstadisticas();
}

// Función para mostrar detalles de un conteo
function VerDetallesConteo(folio, codigo) {
    $('#CajasDi').removeClass('modal-dialog modal-xl modal-notify modal-success').addClass('modal-dialog modal-xl modal-notify modal-success');
    
    $.post("https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/Modales/DetallesConteoPausado.php", 
        { folio: folio, codigo: codigo }, 
        function(data) {
            $("#FormCajas").html(data);
            $("#TitulosCajas").html("Detalles del Conteo Pausado");
        });
    
    $('#ModalEdDele').modal('show');
}

// Función para enviar notificación de recordatorio
function EnviarRecordatorio(folio, codigo) {
    Swal.fire({
        title: '¿Enviar recordatorio?',
        text: '¿Quieres enviar un recordatorio para reanudar este conteo?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#17a2b8',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, enviar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.post("https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/Controladores/EnviarRecordatorioConteo.php", 
                { folio: folio, codigo: codigo }, 
                function(data) {
                    if (data.success) {
                        Swal.fire('¡Éxito!', 'Recordatorio enviado correctamente', 'success');
                    } else {
                        Swal.fire('Error', data.message, 'error');
                    }
                }, 'json');
        }
    });
}

// Función para marcar como urgente
function MarcarUrgente(folio, codigo) {
    $.post("https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/Controladores/MarcarConteoUrgente.php", 
        { folio: folio, codigo: codigo }, 
        function(data) {
            if (data.success) {
                Swal.fire('¡Éxito!', 'Conteo marcado como urgente', 'success');
                CargarConteosPausados();
            } else {
                Swal.fire('Error', data.message, 'error');
            }
        }, 'json');
}

// Función para generar reporte de conteos pausados por tiempo
function GenerarReporteTiempo() {
    const filtros = {
        sucursal: $('#filtroSucursal').val(),
        usuario: $('#filtroUsuario').val(),
        fechaDesde: $('#fechaDesde').val(),
        fechaHasta: $('#fechaHasta').val()
    };

    $.get("https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/Controladores/ReporteTiempoConteosPausados.php", 
        filtros, 
        function(data) {
            if (data.success) {
                // Mostrar reporte en modal
                $('#CajasDi').removeClass('modal-dialog modal-xl modal-notify modal-success').addClass('modal-dialog modal-xl modal-notify modal-success');
                $("#FormCajas").html(data.html);
                $("#TitulosCajas").html("Reporte de Tiempo - Conteos Pausados");
                $('#ModalEdDele').modal('show');
            }
        }, 'json')
        .fail(function() {
            Swal.fire('Error', 'No se pudo generar el reporte', 'error');
        });
}
