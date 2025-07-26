$(document).ready(function() {
    console.log("Inicializando DataTable para reporte de sucursales...");
    
    // Inicializar DataTable
    var table = $('#tablaReporte').DataTable({
        "processing": true,
        "serverSide": false,
        "ajax": {
            "url": "Controladores/ArrayDeReporteSucursales.php",
            "type": "GET",
            "data": function(d) {
                console.log("Enviando parámetros:", d);
                d.fecha_inicio = $('#fecha_inicio').val();
                d.fecha_fin = $('#fecha_fin').val();
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
                console.error("URL llamada:", "Controladores/ArrayDeReporteSucursales.php");
                $('#loading-overlay').hide();
                Swal.fire({
                    icon: 'error',
                    title: 'Error de conexión',
                    text: 'No se pudo cargar los datos. Error: ' + error
                });
            }
        },
        "columns": [
            {"data": "Sucursal"},
            {"data": "Total_Ventas"},
            {"data": "Total_Importe"},
            {"data": "Total_Descuento"},
            {"data": "Numero_Transacciones"},
            {"data": "Promedio_Venta"},
            {"data": "Productos_Vendidos"},
            {"data": "Clientes_Atendidos"},
            {"data": "Ultima_Venta"}
        ],
        "order": [[2, "desc"]], // Ordenar por Total Importe descendente
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
            var totalSucursales = data.length;
            var totalVentas = 0;
            var totalImporte = 0;
            var sucursalMejor = '';
            var maxImporte = 0;
            
            data.forEach(function(item) {
                totalImporte += parseFloat(item.Total_Importe.replace(/[$,]/g, ''));
                
                var importe = parseFloat(item.Total_Importe.replace(/[$,]/g, ''));
                if (importe > maxImporte) {
                    maxImporte = importe;
                    sucursalMejor = item.Sucursal;
                }
            });
            
            var promedioSucursal = totalSucursales > 0 ? totalImporte / totalSucursales : 0;
            
            $('#totalSucursales').text(totalSucursales);
            $('#totalVentas').text('$' + totalImporte.toLocaleString('es-MX', {minimumFractionDigits: 2}));
            $('#sucursalMejor').text(sucursalMejor.length > 15 ? sucursalMejor.substring(0, 15) + '...' : sucursalMejor);
            $('#promedioSucursal').text('$' + promedioSucursal.toLocaleString('es-MX', {minimumFractionDigits: 2}));
            
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
        
        // Mostrar loading
        $('#loading-overlay').show();
        $('#loading-text').text('Generando archivo Excel...');
        
        var url = 'Controladores/exportar_reporte_sucursales.php?fecha_inicio=' + fecha_inicio +
                  '&fecha_fin=' + fecha_fin;
        
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