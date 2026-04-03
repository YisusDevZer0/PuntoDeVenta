console.log("VentasDelDia.js cargado correctamente");

function CargaListadoDeProductos(){
    console.log("Iniciando CargaListadoDeProductos...");
    mostrarCargando();
    
    // Si existe la tabla, destruirla antes de crear una nueva
    if ($.fn.DataTable.isDataTable('#Clientes')) {
        console.log("Destruyendo tabla existente...");
        $('#Clientes').DataTable().destroy();
    }
    
    // Obtener valores de filtros
    var fechaInicio = $('#fecha_inicio').val() || getFirstDayOfMonth();
    var fechaFin = $('#fecha_fin').val() || getCurrentDate();
    var sucursal = $('#sucursal').val() || '';
    
    console.log("Filtros:", {fechaInicio, fechaFin, sucursal});
    
    // Construir parámetros
    var parametros = {
        fecha_inicio: fechaInicio,
        fecha_fin: fechaFin,
        sucursal: sucursal
    };
    
    console.log("Enviando petición a VentasDelDiaConFiltros.php...");
    
    $.get((window.__FDP_BASE_URL__||"")+"ControlYAdministracion/Controladores/VentasDelDiaConFiltros.php", parametros, function(data){
        console.log("Respuesta recibida:", data);
        $("#DataDeServicios").html(data);
        // Esperar a que se inicialice la tabla antes de calcular estadísticas
        setTimeout(function() {
            console.log("Calculando estadísticas...");
            calcularEstadisticasHoy();
        }, 1500);
        ocultarCargando();
    }).fail(function(xhr, status, error) {
        ocultarCargando();
        console.error('Error en CargaListadoDeProductos:', xhr.responseText);
        mostrarError("Error al cargar los datos de ventas del día: " + error);
    });
}

// Función para obtener el primer día del mes actual
function getFirstDayOfMonth() {
    var now = new Date();
    var year = now.getFullYear();
    var month = (now.getMonth() + 1).toString().padStart(2, '0');
    return year + '-' + month + '-01';
}

// Función para obtener la fecha actual
function getCurrentDate() {
    var now = new Date();
    var year = now.getFullYear();
    var month = (now.getMonth() + 1).toString().padStart(2, '0');
    var day = now.getDate().toString().padStart(2, '0');
    return year + '-' + month + '-' + day;
}

// Función para calcular estadísticas del día
function calcularEstadisticasHoy() {
    console.log("Calculando estadísticas...");
    
    try {
        // Verificar si la tabla existe
        if (!$.fn.DataTable.isDataTable('#Clientes')) {
            console.log("DataTable no está inicializada, usando método alternativo");
            calcularEstadisticasAlternativo();
            return;
        }
        
        // Obtener datos de la tabla (incluyendo datos filtrados)
        var tabla = $('#Clientes').DataTable();
        var datos = tabla.data().toArray();
        
        console.log("Datos obtenidos de DataTable:", datos.length, "registros");
        
        // Si no hay datos en la tabla, usar método alternativo
        if (datos.length === 0) {
            console.log("No hay datos en la tabla, usando método alternativo");
            calcularEstadisticasAlternativo();
            return;
        }
        
        var totalVentas = datos.length;
        var totalIngresos = 0;
        var sucursalesUnicas = new Set();
        
        datos.forEach(function(fila) {
            // Sumar ingresos usando la columna Importe
            if (fila.Importe && !isNaN(parseFloat(fila.Importe))) {
                totalIngresos += parseFloat(fila.Importe);
            }
            
            // Contar sucursales únicas
            if (fila.Sucursal && fila.Sucursal !== 'Sin Sucursal') {
                sucursalesUnicas.add(fila.Sucursal);
            }
        });
        
        var promedioVenta = totalVentas > 0 ? totalIngresos / totalVentas : 0;
        
        console.log("Estadísticas calculadas:", {
            totalVentas: totalVentas,
            totalIngresos: totalIngresos,
            sucursalesUnicas: sucursalesUnicas.size,
            promedioVenta: promedioVenta
        });
        
        // Actualizar las estadísticas en la interfaz
        $('#total-ventas-hoy').text(totalVentas.toLocaleString());
        $('#total-ingresos-hoy').text('$' + totalIngresos.toLocaleString('es-MX', {minimumFractionDigits: 2}));
        $('#sucursales-activas').text(sucursalesUnicas.size);
        $('#promedio-venta-hoy').text('$' + promedioVenta.toLocaleString('es-MX', {minimumFractionDigits: 2}));
        
        console.log("Estadísticas actualizadas en la interfaz");
    } catch (error) {
        console.error("Error al calcular estadísticas:", error);
        // Usar método alternativo
        calcularEstadisticasAlternativo();
    }
}

// Función alternativa para calcular estadísticas usando AJAX directo
function calcularEstadisticasAlternativo() {
    console.log("Calculando estadísticas de forma alternativa...");
    
    var fechaInicio = $('#fecha_inicio').val() || getFirstDayOfMonth();
    var fechaFin = $('#fecha_fin').val() || getCurrentDate();
    var sucursal = $('#sucursal').val() || '';
    
    console.log("Parámetros para estadísticas alternativas:", {
        fechaInicio: fechaInicio,
        fechaFin: fechaFin,
        sucursal: sucursal
    });
    
    var parametros = {
        fecha_inicio: fechaInicio,
        fecha_fin: fechaFin,
        sucursal: sucursal
    };
    
    $.get((window.__FDP_BASE_URL__||"")+"ControlYAdministracion/Controladores/ArrayVentasDelDia.php", parametros, function(data) {
        console.log("Respuesta de ArrayVentasDelDia.php:", data);
        
        if (data && data.data) {
            var datos = data.data;
            var totalVentas = datos.length;
            var totalIngresos = 0;
            var sucursalesUnicas = new Set();
            
            console.log("Procesando", datos.length, "registros para estadísticas");
            
            datos.forEach(function(fila) {
                if (fila.Importe && !isNaN(parseFloat(fila.Importe))) {
                    totalIngresos += parseFloat(fila.Importe);
                }
                if (fila.Sucursal && fila.Sucursal !== 'Sin Sucursal') {
                    sucursalesUnicas.add(fila.Sucursal);
                }
            });
            
            var promedioVenta = totalVentas > 0 ? totalIngresos / totalVentas : 0;
            
            // Actualizar las estadísticas
            $('#total-ventas-hoy').text(totalVentas.toLocaleString());
            $('#total-ingresos-hoy').text('$' + totalIngresos.toLocaleString('es-MX', {minimumFractionDigits: 2}));
            $('#sucursales-activas').text(sucursalesUnicas.size);
            $('#promedio-venta-hoy').text('$' + promedioVenta.toLocaleString('es-MX', {minimumFractionDigits: 2}));
            
            console.log("Estadísticas calculadas alternativamente:", {
                totalVentas: totalVentas,
                totalIngresos: totalIngresos,
                sucursalesUnicas: sucursalesUnicas.size,
                promedioVenta: promedioVenta
            });
        } else {
            console.log("No hay datos en la respuesta");
            $('#total-ventas-hoy').text('0');
            $('#total-ingresos-hoy').text('$0.00');
            $('#sucursales-activas').text('0');
            $('#promedio-venta-hoy').text('$0.00');
        }
    }).fail(function(xhr, status, error) {
        console.error("Error al calcular estadísticas alternativas:", error);
        console.error("Respuesta del servidor:", xhr.responseText);
        $('#total-ventas-hoy').text('0');
        $('#total-ingresos-hoy').text('$0.00');
        $('#sucursales-activas').text('0');
        $('#promedio-venta-hoy').text('$0.00');
    });
}

// Función para filtrar datos
function filtrarDatos() {
    console.log("Iniciando filtrarDatos...");
    mostrarCargando();
    
    var fechaInicio = $('#fecha_inicio').val();
    var fechaFin = $('#fecha_fin').val();
    var sucursal = $('#sucursal').val();
    
    // Validar fechas
    if (!fechaInicio || !fechaFin) {
        ocultarCargando();
        mostrarError("Por favor selecciona las fechas de inicio y fin");
        return;
    }
    
    if (fechaInicio > fechaFin) {
        ocultarCargando();
        mostrarError("La fecha de inicio no puede ser mayor a la fecha fin");
        return;
    }
    
    // Si existe la tabla, destruirla antes de crear una nueva
    if ($.fn.DataTable.isDataTable('#Clientes')) {
        $('#Clientes').DataTable().destroy();
    }
    
    // Construir parámetros para la consulta
    var parametros = {
        fecha_inicio: fechaInicio,
        fecha_fin: fechaFin,
        sucursal: sucursal
    };
    
    $.get((window.__FDP_BASE_URL__||"")+"ControlYAdministracion/Controladores/VentasDelDiaConFiltros.php", parametros, function(data) {
      $("#DataDeServicios").html(data);
        // Esperar a que se inicialice la tabla antes de calcular estadísticas
        setTimeout(function() {
            console.log("Recalculando estadísticas después del filtro...");
            calcularEstadisticasHoy();
        }, 2000); // Aumentar el tiempo para asegurar que la tabla esté lista
        ocultarCargando();
        mostrarExito("Filtros aplicados correctamente");
    }).fail(function(xhr, status, error) {
        ocultarCargando();
        console.error('Error en filtrarDatos:', xhr.responseText);
        mostrarError("Error al filtrar los datos: " + error);
    });
}

// Función para exportar a Excel
function exportarExcel() {
    if ($.fn.DataTable.isDataTable('#Clientes')) {
        var tabla = $('#Clientes').DataTable();
        var datos = tabla.data().toArray();
        
        if (datos.length === 0) {
            mostrarError("No hay datos para exportar");
            return;
        }
        
        // Usar la función manual de exportación
        exportarExcelManual();
    } else {
        mostrarError("No hay datos para exportar");
    }
}

// Función alternativa para exportar a Excel
function exportarExcelManual() {
    try {
        var tabla = $('#Clientes').DataTable();
        var datos = tabla.data().toArray();
        
        if (datos.length === 0) {
            mostrarError("No hay datos para exportar");
            return;
        }
        
        // Crear CSV simple
        var csv = "Código,Nombre,Precio Compra,Precio Venta,Ticket,Sucursal,Turno,Cantidad,Total,Importe,Descuento,Forma Pago,Cliente,Folio SV,Servicio,Fecha,Hora,Vendedor\n";
        
        datos.forEach(function(fila) {
            var linea = [
                fila.Cod_Barra || '',
                fila.Nombre_Prod || '',
                fila.PrecioCompra || '',
                fila.PrecioVenta || '',
                fila.FolioTicket || '',
                fila.Sucursal || '',
                fila.Turno || '',
                fila.Cantidad_Venta || '',
                fila.Total_Venta || '',
                fila.Importe || '',
                fila.Descuento || '',
                fila.FormaPago || '',
                fila.Cliente || '',
                fila.FolioSignoVital || '',
                fila.NomServ || '',
                fila.AgregadoEl || '',
                fila.AgregadoEnMomento || '',
                fila.AgregadoPor || ''
            ].map(function(campo) {
                return '"' + String(campo).replace(/"/g, '""') + '"';
            }).join(',');
            csv += linea + '\n';
        });
        
        // Descargar archivo
        var blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
        var link = document.createElement("a");
        var url = URL.createObjectURL(blob);
        link.setAttribute("href", url);
        link.setAttribute("download", "ventas_del_dia_" + new Date().toISOString().slice(0,10) + ".csv");
        link.style.visibility = 'hidden';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        
        mostrarExito("Archivo exportado correctamente");
    } catch (error) {
        console.error('Error al exportar:', error);
        mostrarError("Error al exportar los datos: " + error.message);
    }
}

// Función para mostrar el loading
function mostrarCargando() {
    $('#loading-overlay').show();
}

// Función para ocultar el loading
function ocultarCargando() {
    $('#loading-overlay').hide();
}

// Función para mostrar errores
function mostrarError(mensaje) {
    Swal.fire({
        icon: 'error',
        title: 'Error',
        text: mensaje,
        confirmButtonColor: '#667eea'
    });
}

// Función para mostrar éxito
function mostrarExito(mensaje) {
    Swal.fire({
        icon: 'success',
        title: 'Éxito',
        text: mensaje,
        confirmButtonColor: '#667eea'
    });
}

// Mensajes de carga personalizados
var mensajesCarga = [
    "Consultando ventas del día...",
    "Estamos realizando la búsqueda...",
    "Cargando datos...",
    "Procesando la información...",
    "Espere un momento...",
    "Cargando... ten paciencia, incluso los planetas tardaron millones de años en formarse.",
    "¡Espera un momento! Estamos contando hasta el infinito... otra vez.",
    "¿Sabías que los pingüinos también tienen que esperar mientras cargan su comida?",
    "¡Zapateando cucarachas de carga! ¿Quién necesita un exterminador?",
    "Cargando... ¿quieres un chiste para hacer más amena la espera? ¿Por qué los pájaros no usan Facebook? Porque ya tienen Twitter.",
    "¡Alerta! Un koala está jugando con los cables de carga. Espera un momento mientras lo persuadimos.",
    "¿Sabías que las tortugas cargan a una velocidad épica? Bueno, estamos intentando superarlas.",
    "¡Espera un instante! Estamos pidiendo ayuda a los unicornios para acelerar el proceso.",
    "Cargando... mientras nuestros programadores disfrutan de una buena taza de café.",
    "Cargando... No estamos seguros de cómo llegamos aquí, pero estamos trabajando en ello.",
    "Estamos contando en binario... 10%, 20%, 110%... espero que esto no sea un error de desbordamiento.",
    "Cargando... mientras cazamos pokémons para acelerar el proceso.",
    "Error 404: Mensaje gracioso no encontrado. Estamos trabajando en ello.",
    "Cargando... ¿Sabías que los programadores también tienen emociones? Bueno, nosotros tampoco.",
    "Estamos buscando la respuesta a la vida, el universo y todo mientras cargamos... Pista: es un número entre 41 y 43.",
    "Cargando... mientras los gatos toman el control. ¡Meowtrix está en marcha!",
    "Estamos ajustando tu espera a la velocidad de la luz. Aún no es suficientemente rápida, pero pronto llegaremos.",
    "Cargando... Ten paciencia, incluso los programadores necesitan tiempo para pensar en nombres de variables.",
    "Estamos destilando líneas de código para obtener la solución perfecta. ¡Casi listo!",
    "Buscando el café perdido... ☕️",
    "Cargando unicornios pixelados...",
    "Generando excusas para la lentitud...",
    "Contando hasta el infinito... dos veces.",
    "Alineando los bits desobedientes...",
    "Convocando hamsters de velocidad...",
    "Reorganizando cajones virtuales...",
    "¿Estás ahí, mundo digital?",
    "Haciendo magia binaria...",
    "Consultando el manual del universo...",
    "Midiendo la velocidad de la luz en píxeles...",
    "Desenredando cables imaginarios...",
    "Haciendo una pausa para tomar un byte.",
    "Cargando una oveja contadora de sueños...",
    "¡Alerta! Bits desordenados, se necesita aspiradora digital.",
    "Comprando boletos para el hiperespacio...",
    "Dibujando una puerta en la pared de ladrillos...",
    "Esperando a que los electrones hagan ejercicio.",
    "Silencio, estamos calibrando los chistes.",
    "Revolviendo el caos en cámara lenta...",
    "🚀 Preparándose para despegar hacia el ciberespacio...",
    "🐢 Cargando a la velocidad de una tortuga con resaca...",
    "🌀 Girando los engranajes de la paciencia...",
    "⚡ Generando rayos de alta velocidad...",
    "🎮 Insertando monedas virtuales para acelerar...",
    "🌌 Navegando por el agujero de gusano del sistema...",
    "🤖 Despertando a los gnomos del procesador...",
    "🍕 Ordenando pizza digital mientras esperas...",
    "🕒 Viajando en el tiempo para cargar más rápido...",
    "🎩 Sacando conejos del sombrero de la programación..."
];

// Función para mostrar mensaje de carga aleatorio
function mostrarMensajeCarga() {
    var randomIndex = Math.floor(Math.random() * mensajesCarga.length);
    var mensaje = mensajesCarga[randomIndex];
    $('#loading-text').text(mensaje);
}

// Inicializar el reporte al cargar la página
$(document).ready(function() {
    console.log("Documento listo, iniciando VentasDelDia...");
    CargaListadoDeProductos();
    
    // Configurar eventos de los filtros
    $('#fecha_inicio, #fecha_fin, #sucursal').on('change', function() {
        console.log("Filtro cambiado:", $(this).attr('id'), $(this).val());
        // Recalcular estadísticas cuando cambien los filtros
        setTimeout(function() {
            calcularEstadisticasHoy();
        }, 500);
    });
    
    // Configurar botón de filtrar
    $('#btn-filtrar').on('click', function() {
        console.log("Botón filtrar clickeado");
        filtrarDatos();
    });
    
    // Configurar botón de exportar
    $('#btn-exportar').on('click', function() {
        console.log("Botón exportar clickeado");
        exportarExcel();
    });
    
    // Actualizar mensaje de carga cada 3 segundos
    setInterval(function() {
        if ($('#loading-overlay').is(':visible')) {
            mostrarMensajeCarga();
        }
    }, 3000);
});

// También ejecutar inmediatamente si el documento ya está listo
if (document.readyState === 'loading') {
    console.log("Documento aún cargando, esperando...");
} else {
    console.log("Documento ya listo, ejecutando inmediatamente...");
    CargaListadoDeProductos();
}

  
  