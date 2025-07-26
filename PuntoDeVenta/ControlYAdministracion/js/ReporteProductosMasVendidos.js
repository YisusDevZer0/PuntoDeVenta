$(document).ready(function() {
    console.log("Inicializando DataTable para productos más vendidos...");
    
    // Inicializar DataTable
    var table = $('#tablaReporte').DataTable({
        "processing": true,
        "serverSide": false,
        "ajax": {
            "url": "Controladores/ArrayDeReporteProductosMasVendidos.php",
            "type": "GET",
            "data": function(d) {
                console.log("Enviando parámetros:", d);
                d.fecha_inicio = $('#fecha_inicio').val();
                d.fecha_fin = $('#fecha_fin').val();
                d.sucursal = $('#sucursal').val();
                d.limite = $('#limite').val();
                console.log("Parámetros finales:", d);
                return d;
            },
            "dataSrc": function(json) {
                console.log("Respuesta del servidor:", json);
                
                // Ocultar loading
                $('#loading-overlay').hide();
                
                // Verificar si hay error en la respuesta
                if (json.error) {
                    console.error("Error del servidor:", json.error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Error al cargar los datos: ' + json.error
                    });
                    return [];
                }
                
                // Actualizar estadísticas
                actualizarEstadisticas(json.data);
                
                return json.data || [];
            },
            "error": function(xhr, status, error) {
                console.error("Error AJAX:", xhr, status, error);
                console.error("URL llamada:", "Controladores/ArrayDeReporteProductosMasVendidos.php");
                $('#loading-overlay').hide();
                Swal.fire({
                    icon: 'error',
                    title: 'Error de conexión',
                    text: 'No se pudo cargar los datos. Error: ' + error
                });
            }
        },
        "columns": [
            {"data": "Ranking"},
            {"data": "ID_Prod_POS"},
            {"data": "Nombre_Prod"},
            {"data": "Cod_Barra"},
            {"data": "Categoria"},
            {"data": "Total_Vendido"},
            {"data": "Total_Importe"},
            {"data": "Promedio_Venta"},
            {"data": "Numero_Ventas"},
            {"data": "Ultima_Venta"}
        ],
        "order": [[5, "desc"]], // Ordenar por Total Vendido descendente
        "pageLength": 25,
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json"
        },
        "error": function(xhr, error, thrown) {
            console.error("Error en DataTables:", error);
            $('#loading-overlay').hide();
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Error al cargar los datos. Por favor, verifica la conexión.'
            });
        },
        "initComplete": function() {
            console.log("DataTable inicializado completamente");
            $('#loading-overlay').hide();
        }
    });

    // Función para actualizar estadísticas
    function actualizarEstadisticas(data) {
        if (data && data.length > 0) {
            var totalProductos = data.length;
            var totalVentas = 0;
            var totalImporte = 0;
            var productoEstrella = '';
            var maxVentas = 0;
            var totalImporteTop10 = 0;
            
            data.forEach(function(item, index) {
                totalImporte += parseFloat(item.Total_Importe.replace(/[$,]/g, ''));
                
                var ventas = parseInt(item.Total_Vendido.replace(/,/g, ''));
                if (ventas > maxVentas) {
                    maxVentas = ventas;
                    productoEstrella = item.Nombre_Prod;
                }
                
                // Calcular porcentaje del top 10
                if (index < 10) {
                    totalImporteTop10 += parseFloat(item.Total_Importe.replace(/[$,]/g, ''));
                }
            });
            
            var porcentajeTop10 = totalImporte > 0 ? (totalImporteTop10 / totalImporte * 100) : 0;
            
            $('#totalProductos').text(totalProductos);
            $('#totalVentas').text('$' + totalImporte.toLocaleString('es-MX', {minimumFractionDigits: 2}));
            $('#productoEstrella').text(productoEstrella.length > 20 ? productoEstrella.substring(0, 20) + '...' : productoEstrella);
            $('#porcentajeTop10').text(porcentajeTop10.toFixed(1) + '%');
            
            $('#statsRow').show();
        }
    }

    // Función para filtrar datos con loading
    window.filtrarDatos = function() {
        console.log("Ejecutando filtro...");
        
        // Mostrar loading
        $('#loading-overlay').show();
        $('#loading-text').text('Filtrando datos...');
        
        // Recargar datos
        table.ajax.reload(function() {
            $('#loading-overlay').hide();
            console.log("Filtro completado");
        });
    };

    // Función para exportar a Excel
    window.exportarExcel = function() {
        var fecha_inicio = $('#fecha_inicio').val();
        var fecha_fin = $('#fecha_fin').val();
        var sucursal = $('#sucursal').val();
        var limite = $('#limite').val();
        
        // Mostrar loading
        $('#loading-overlay').show();
        $('#loading-text').text('Generando archivo Excel...');
        
        var url = 'Controladores/exportar_reporte_productos_mas_vendidos.php?fecha_inicio=' + fecha_inicio +
                  '&fecha_fin=' + fecha_fin +
                  '&sucursal=' + sucursal +
                  '&limite=' + limite;
        
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
    };
}); 