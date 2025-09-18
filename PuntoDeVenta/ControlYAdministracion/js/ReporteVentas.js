// Función para cargar el reporte de ventas
function cargarReporteDeVentas() {
    mostrarCargando();
    
    $.get("https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/Controladores/VentasDelDia.php", "", function(data) {
        $("#DataDeServicios").html(data);
        calcularEstadisticas();
        ocultarCargando();
    }).fail(function() {
        ocultarCargando();
        mostrarError("Error al cargar los datos del reporte");
    });
}

// Función para cargar el reporte completo
function cargarReporteCompleto() {
    cargarReporteDeVentas();
}

// Función para calcular estadísticas
function calcularEstadisticas() {
    // Obtener datos de la tabla
    var tabla = $('#Clientes').DataTable();
    var datos = tabla.data().toArray();
    
    var totalVentas = datos.length;
    var totalIngresos = 0;
    var sucursalesUnicas = new Set();
    var sumaVentas = 0;
    
    datos.forEach(function(fila) {
        // Sumar ingresos (asumiendo que la columna de total está en el índice 9)
        if (fila[9] && !isNaN(parseFloat(fila[9]))) {
            totalIngresos += parseFloat(fila[9]);
        }
        
        // Contar sucursales únicas (asumiendo que la columna de sucursal está en el índice 5)
        if (fila[5]) {
            sucursalesUnicas.add(fila[5]);
        }
    });
    
    var promedioVenta = totalVentas > 0 ? totalIngresos / totalVentas : 0;
    
    // Actualizar las estadísticas en la interfaz
    $('#total-ventas').text(totalVentas.toLocaleString());
    $('#total-ingresos').text('$' + totalIngresos.toLocaleString('es-MX', {minimumFractionDigits: 2}));
    $('#total-sucursales').text(sucursalesUnicas.size);
    $('#promedio-venta').text('$' + promedioVenta.toLocaleString('es-MX', {minimumFractionDigits: 2}));
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
    "Consultando ventas...",
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
    cargarReporteDeVentas();
    
    // Actualizar mensaje de carga cada 3 segundos
    setInterval(function() {
        if ($('#loading-overlay').is(':visible')) {
            mostrarMensajeCarga();
        }
    }, 3000);
});
