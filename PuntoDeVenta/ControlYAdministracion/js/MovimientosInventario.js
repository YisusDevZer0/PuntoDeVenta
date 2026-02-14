var paginaActualMovimientos = 1;
var registrosPorPaginaMovimientos = 25;

function CargarSucursalesMovimientos() {
    $.get("https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/Controladores/DataSucursales.php",
        {},
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

function CargarMovimientosInventario(pagina) {
    if (typeof pagina !== 'undefined') {
        paginaActualMovimientos = pagina;
    }
    var filtros = {
        sucursal: $('#filtroSucursal').val(),
        tipoRef: $('#filtroTipoRef').val(),
        fechaDesde: $('#fechaDesde').val(),
        fechaHasta: $('#fechaHasta').val(),
        pagina: paginaActualMovimientos,
        registros: registrosPorPaginaMovimientos
    };

    $.get("https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/Controladores/DataMovimientosInventario.php",
        filtros,
        function(data) {
            $("#DataMovimientosInventario").html(data);
        })
        .fail(function() {
            $("#DataMovimientosInventario").html('<div class="alert alert-danger">Error al cargar los movimientos de inventario.</div>');
        });
}

function CambiarPaginaMovimientos(pagina) {
    CargarMovimientosInventario(pagina);
}

function CambiarRegistrosMovimientos(registros) {
    registrosPorPaginaMovimientos = parseInt(registros, 10);
    paginaActualMovimientos = 1;
    CargarMovimientosInventario(1);
}

$(document).ready(function() {
    CargarSucursalesMovimientos();
    var hoy = new Date().toISOString().split('T')[0];
    $('#fechaHasta').val(hoy);
    CargarMovimientosInventario();
});
