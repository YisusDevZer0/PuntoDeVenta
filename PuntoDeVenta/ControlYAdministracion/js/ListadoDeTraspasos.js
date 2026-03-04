var baseUrl = "https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion";

function getFiltrosTraspasos() {
    return {
        fechaDesde: $('#filtroFechaDesde').val() || '',
        fechaHasta: $('#filtroFechaHasta').val() || '',
        sucursalOrigen: $('#filtroSucursalOrigen').val() || '',
        sucursalDestino: $('#filtroSucursalDestino').val() || '',
        estatus: $('#filtroEstatus').val() || ''
    };
}

function CargaServicios() {
    var filtros = getFiltrosTraspasos();
    $.get(baseUrl + "/Controladores/DataDeTraspasos.php", filtros, function(data) {
        $("#DataDeServicios").html(data);
    }).fail(function() {
        $("#DataDeServicios").html('<div class="alert alert-danger">Error al cargar el listado de traspasos.</div>');
    });
}

function CargarSucursalesTraspasos() {
    $.get(baseUrl + "/Controladores/DataSucursales.php", {}, function(data) {
        if (data && data.length > 0) {
            var options = '<option value="">Todas</option>';
            $.each(data, function(i, sucursal) {
                options += '<option value="' + sucursal.ID_Sucursal + '">' + sucursal.Nombre_Sucursal + '</option>';
            });
            $('#filtroSucursalOrigen').html(options);
            $('#filtroSucursalDestino').html(options);
        }
    }, 'json').fail(function() {
        console.error('Error al cargar sucursales');
    });
}

function limpiarFiltrosTraspasos() {
    $('#filtroSucursalOrigen').val('');
    $('#filtroSucursalDestino').val('');
    $('#filtroFechaDesde').val('');
    $('#filtroFechaHasta').val('');
    $('#filtroEstatus').val('');
    var hoy = new Date();
    var primerDia = new Date(hoy.getFullYear(), hoy.getMonth(), 1);
    $('#filtroFechaDesde').val(primerDia.toISOString().split('T')[0]);
    $('#filtroFechaHasta').val(hoy.toISOString().split('T')[0]);
    CargaServicios();
}

$(document).ready(function() {
    CargarSucursalesTraspasos();

    // Fechas por defecto: primer día del mes y hoy
    var hoy = new Date();
    var primerDia = new Date(hoy.getFullYear(), hoy.getMonth(), 1);
    $('#filtroFechaDesde').val(primerDia.toISOString().split('T')[0]);
    $('#filtroFechaHasta').val(hoy.toISOString().split('T')[0]);

    $('#btnLimpiarFiltrosTraspasos').on('click', limpiarFiltrosTraspasos);

    CargaServicios();
});
