console.log("VentasDelDiaSimple.js cargado correctamente");

// Función simple para probar
function CargaListadoDeProductos(){
    console.log("Iniciando CargaListadoDeProductos SIMPLE...");
    
    // Verificar que jQuery esté disponible
    if (typeof $ === 'undefined') {
        console.error("jQuery no está disponible");
        return;
    }
    
    // Verificar que DataTables esté disponible
    if (typeof $.fn.DataTable === 'undefined') {
        console.error("DataTables no está disponible");
        return;
    }
    
    console.log("jQuery y DataTables están disponibles");
    
    // Crear tabla simple
    var tablaHTML = `
        <table id="Clientes" class="order-column">
            <thead>
                <th>Cod</th>
                <th>Nombre</th>
                <th>Precio Compra</th>
                <th>Precio Venta</th>
                <th>N° Ticket</th>
                <th>Sucursal</th>
                <th>Turno</th>
                <th>Cantidad</th>
                <th>P.U</th>
                <th>Importe</th>
                <th>Descuento</th>
                <th>Forma de pago</th>
                <th>Cliente</th>
                <th>Folio Signo Vital</th>
                <th>Servicio</th>
                <th>Fecha</th>
                <th>Hora</th>   
                <th>Vendedor</th>
            </thead>
        </table>
    `;
    
    $("#DataDeServicios").html(tablaHTML);
    console.log("Tabla HTML creada");
    
    // Crear DataTable simple
    try {
        var tabla = $('#Clientes').DataTable({
            "ajax": {
                "url": "https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/Controladores/ArrayVentasDelDia.php",
                "type": "GET"
            },
            "columns": [
                { data: 'Cod_Barra' },
                { data: 'Nombre_Prod' },
                { data: 'PrecioCompra' },
                { data: 'PrecioVenta' },
                { data: 'FolioTicket' },
                { data: 'Sucursal' },
                { data: 'Turno' },
                { data: 'Cantidad_Venta' },
                { data: 'Total_Venta' },
                { data: 'Importe' },
                { data: 'Descuento' },
                { data: 'FormaPago' },
                { data: 'Cliente' },
                { data: 'FolioSignoVital' },
                { data: 'NomServ' },
                { data: 'AgregadoEl' },
                { data: 'AgregadoEnMomento' },
                { data: 'AgregadoPor' }
            ],
            "initComplete": function() {
                console.log("DataTable inicializado correctamente");
                // Calcular estadísticas simples
                var datos = tabla.data().toArray();
                console.log("Datos obtenidos:", datos.length);
                
                var totalVentas = datos.length;
                var totalIngresos = 0;
                
                datos.forEach(function(fila) {
                    if (fila.Importe && !isNaN(parseFloat(fila.Importe))) {
                        totalIngresos += parseFloat(fila.Importe);
                    }
                });
                
                $('#total-ventas-hoy').text(totalVentas);
                $('#total-ingresos-hoy').text('$' + totalIngresos.toFixed(2));
                $('#sucursales-activas').text('1');
                $('#promedio-venta-hoy').text('$' + (totalVentas > 0 ? (totalIngresos / totalVentas).toFixed(2) : '0.00'));
                
                console.log("Estadísticas actualizadas:", {
                    totalVentas: totalVentas,
                    totalIngresos: totalIngresos
                });
            }
        });
        console.log("DataTable creado exitosamente");
    } catch (error) {
        console.error("Error al crear DataTable:", error);
    }
}

// Función para filtrar datos
function filtrarDatos() {
    console.log("Filtrar datos - función simple");
    CargaListadoDeProductos();
}

// Función para exportar a Excel
function exportarExcel() {
    console.log("Exportar Excel - función simple");
    alert("Función de exportar en desarrollo");
}

// Inicializar cuando el documento esté listo
$(document).ready(function() {
    console.log("Documento listo - versión simple");
    CargaListadoDeProductos();
});

// También ejecutar inmediatamente
if (document.readyState === 'loading') {
    console.log("Documento aún cargando - versión simple");
} else {
    console.log("Documento ya listo - ejecutando inmediatamente - versión simple");
    CargaListadoDeProductos();
}
