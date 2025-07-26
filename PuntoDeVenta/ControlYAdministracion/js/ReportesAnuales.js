$(document).ready(function() {
    console.log("Inicializando DataTable para reporte anual...");
    
    // Inicializar DataTable
    var table = $('#tablaReporte').DataTable({
        "processing": true,
        "serverSide": false,
        "ajax": {
            "url": "Controladores/ArrayDeReportesAnuales.php",
            "type": "GET",
            "data": function(d) {
                console.log("Enviando parámetros:", d);
                d.anio = $('#anio').val();
                d.sucursal = $('#sucursal').val();
                d.tipo_periodo = $('#tipo_periodo').val();
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
                console.error("URL llamada:", "Controladores/ArrayDeReportesAnuales.php");
                $('#loading-overlay').hide();
                Swal.fire({
                    icon: 'error',
                    title: 'Error de conexión',
                    text: 'No se pudo cargar los datos. Error: ' + error
                });
            }
        },
        "columns": [
            {"data": "Periodo"},
            {"data": "Total_Ventas"},
            {"data": "Total_Importe"},
            {"data": "Total_Descuento"},
            {"data": "Numero_Transacciones"},
            {"data": "Promedio_Venta"},
            {"data": "Productos_Vendidos"},
            {"data": "Clientes_Atendidos"}
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
            var totalPeriodos = data.length;
            var totalVentas = 0;
            var totalImporte = 0;
            var mejorPeriodo = 0;
            
            data.forEach(function(item) {
                totalImporte += parseFloat(item.Total_Importe.replace(/[$,]/g, ''));
                var ventasPeriodo = parseFloat(item.Total_Ventas.replace(/[$,]/g, ''));
                if (ventasPeriodo > mejorPeriodo) {
                    mejorPeriodo = ventasPeriodo;
                }
            });
            
            var promedioPeriodo = totalPeriodos > 0 ? totalImporte / totalPeriodos : 0;
            
            $('#totalPeriodos').text(totalPeriodos);
            $('#totalVentas').text('$' + totalImporte.toLocaleString('es-MX', {minimumFractionDigits: 2}));
            $('#promedioPeriodo').text('$' + promedioPeriodo.toLocaleString('es-MX', {minimumFractionDigits: 2}));
            $('#mejorPeriodo').text('$' + mejorPeriodo.toLocaleString('es-MX', {minimumFractionDigits: 2}));
            
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
        var anio = $('#anio').val();
        var sucursal = $('#sucursal').val();
        var tipo_periodo = $('#tipo_periodo').val();
        
        // Mostrar loading
        $('#loading-overlay').show();
        $('#loading-text').text('Generando archivo Excel...');
        
        var url = 'Controladores/exportar_reporte_anual.php?anio=' + anio +
                  '&sucursal=' + sucursal +
                  '&tipo_periodo=' + tipo_periodo;
        
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