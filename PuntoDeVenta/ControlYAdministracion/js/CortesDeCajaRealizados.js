function CargaFCajas(){
    var fecha_inicio = $('#fecha_inicio').val();
    var fecha_fin = $('#fecha_fin').val();
    var sucursal = $('#filtro_sucursal').val();
    var cajero = $('#filtro_cajero').val();
    
    var params = {};
    if (fecha_inicio) params.fecha_inicio = fecha_inicio;
    if (fecha_fin) params.fecha_fin = fecha_fin;
    if (sucursal) params.sucursal = sucursal;
    if (cajero) params.cajero = cajero;
    
    var url = "https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/Controladores/CortesDeCajasRealizados.php";
    if (Object.keys(params).length > 0) {
        url += "?" + $.param(params);
    }
    
    $.get(url, function(data){
        $("#FCajas").html(data);
        // Calcular estadísticas después de cargar los datos
        calcularEstadisticas();
    });
}

// Función para calcular estadísticas desde la tabla
function calcularEstadisticas() {
    // Esperar a que la tabla esté completamente cargada
    setTimeout(function() {
        if ($('#Clientes').length > 0 && $.fn.DataTable.isDataTable('#Clientes')) {
            var tabla = $('#Clientes').DataTable();
            var datos = tabla.rows({search: 'applied'}).data();
            
            var totalCortes = 0;
            var totalMonto = 0;
            var cajasCerradas = 0;
            
            datos.each(function(row) {
                totalCortes++;
                
                // Contar cajas cerradas
                if (row.Estatus && row.Estatus.includes("Cerrada")) {
                    cajasCerradas++;
                }
                
                // Extraer el monto total de la caja (formato puede variar)
                if (row.ValorTotalCaja) {
                    var montoStr = row.ValorTotalCaja.toString();
                    // Remover $, comas y espacios
                    var monto = parseFloat(montoStr.replace(/[$,]/g, '').trim());
                    if (!isNaN(monto)) {
                        totalMonto += monto;
                    }
                }
            });
            
            var promedio = totalCortes > 0 ? totalMonto / totalCortes : 0;
            
            // Actualizar las estadísticas
            $('#total-cortes').text(totalCortes.toLocaleString('es-MX'));
            $('#total-monto').text('$' + totalMonto.toLocaleString('es-MX', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
            $('#promedio-corte').text('$' + promedio.toLocaleString('es-MX', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
            $('#cajas-cerradas').text(cajasCerradas.toLocaleString('es-MX'));
            
            // Mostrar las estadísticas
            $('#statsRow').show();
        }
    }, 500);
}

// Variable para debounce del input de texto
var timeoutFiltro = null;

// Función para filtrar datos automáticamente con debounce para el input de texto
function debounceFiltrar() {
    clearTimeout(timeoutFiltro);
    timeoutFiltro = setTimeout(function() {
        filtrarDatos();
    }, 500); // Esperar 500ms después de que el usuario deje de escribir
}

// Función para filtrar datos
function filtrarDatos() {
    // Si la tabla ya existe, recargarla con los nuevos filtros
    if ($('#Clientes').length > 0 && $.fn.DataTable.isDataTable('#Clientes')) {
        var tabla = $('#Clientes').DataTable();
        tabla.ajax.reload(function(json) {
            calcularEstadisticas();
        }, false); // false = mantener la página actual
    } else {
        // Si no existe, recargar todo el contenido
        CargaFCajas();
    }
}

// Función para limpiar filtros
function limpiarFiltros() {
    var hoy = new Date();
    var primerDia = new Date(hoy.getFullYear(), hoy.getMonth(), 1);
    $('#fecha_inicio').val(primerDia.toISOString().split('T')[0]);
    $('#fecha_fin').val(hoy.toISOString().split('T')[0]);
    $('#filtro_sucursal').val('');
    $('#filtro_cajero').val('');
    CargaFCajas();
}

// Función para exportar a Excel
function exportarExcel() {
    var fecha_inicio = $('#fecha_inicio').val();
    var fecha_fin = $('#fecha_fin').val();
    var sucursal = $('#filtro_sucursal').val();
    var cajero = $('#filtro_cajero').val();
    
    if ($('#Clientes').length > 0 && $.fn.DataTable.isDataTable('#Clientes')) {
        var tabla = $('#Clientes').DataTable();
        tabla.button('.buttons-excel').trigger();
    } else {
        // Si no hay tabla DataTable, crear un enlace de descarga simple
        var url = 'https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/Controladores/exportar_cortes_caja.php';
        var params = [];
        if (fecha_inicio) params.push('fecha_inicio=' + fecha_inicio);
        if (fecha_fin) params.push('fecha_fin=' + fecha_fin);
        if (sucursal) params.push('sucursal=' + sucursal);
        if (cajero) params.push('cajero=' + encodeURIComponent(cajero));
        
        if (params.length > 0) {
            url += '?' + params.join('&');
        }
        
        window.open(url, '_blank');
    }
}

// Cargar datos al inicio
$(document).ready(function() {
    CargaFCajas();
});
  