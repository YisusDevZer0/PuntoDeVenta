<style>
  /* Personalizar el diseño de la paginación con CSS */
  .dataTables_wrapper .dataTables_paginate {
    text-align: center !important; /* Centrar los botones de paginación */
    margin-top: 10px !important;
  }

  .dataTables_paginate .paginate_button {
    padding: 5px 10px !important;
    border: 1px solid #ef7980 !important;
    margin: 2px !important;
    cursor: pointer !important;
    font-size: 16px !important;
    color: #ef7980 !important;
    background-color: #fff !important;
  }

  /* Cambiar el color del paginado seleccionado */
  .dataTables_paginate .paginate_button.current {
    background-color: #ef7980 !important;
    color: #fff !important;
    border-color: #ef7980 !important;
  }

  /* Cambiar el color del hover */
  .dataTables_paginate .paginate_button:hover {
    background-color: #C80096 !important;
    color: #fff !important;
    border-color: #C80096 !important;
  }
</style>

<style>
  /* Estilos personalizados para la tabla */
  #Clientes th {
    font-size: 12px; /* Tamaño de letra para los encabezados */
    padding: 4px; /* Ajustar el espaciado entre los encabezados */
    white-space: nowrap; /* Evitar que los encabezados se dividan en varias líneas */
  }
</style>

<style>
  /* Estilos para la tabla */
  #Clientes {
    font-size: 12px; /* Tamaño de letra para el contenido de la tabla */
    border-collapse: collapse; /* Colapsar los bordes de las celdas */
    width: 100%;
    text-align: center; /* Centrar el contenido de las celdas */
  }

  #Clientes th {
    font-size: 16px; /* Tamaño de letra para los encabezados de la tabla */
    background-color: #ef7980 !important; /* Nuevo color de fondo para los encabezados */
    color: white; /* Cambiar el color del texto a blanco para contrastar */
    padding: 10px; /* Ajustar el espaciado de los encabezados */
  }

  #Clientes td {
    font-size: 14px; /* Tamaño de letra para el contenido de la tabla */
    padding: 8px; /* Ajustar el espaciado de las celdas */
    border-bottom: 1px solid #ccc; /* Agregar una línea de separación entre las filas */
    color:#000000;
  }

  /* Estilos para el botón de Excel */
  .dt-buttons {
    display: flex;
    justify-content: center;
    margin-bottom: 10px;
  }

  .dt-buttons button {
    font-size: 14px;
    margin: 0 5px;
    color: white; /* Cambiar el color del texto a blanco */
    background-color: #fff; /* Cambiar el color de fondo a blanco */
  }

 
</style>

<style>
  /* Estilos para la capa de carga */
  #loading-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.7);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 9999; /* Asegurarse de que el overlay esté encima de todo */
    display: none; /* Ocultar inicialmente el overlay */
  }

  /* Estilo para el ícono de carga */
  .loader {
    border: 6px solid #f3f3f3; /* Color del círculo externo */
    border-top: 6px solid #26b814; /* Color del círculo interno */
    border-radius: 50%;
    width: 60px;
    height: 60px;
    animation: spin 1s linear infinite; /* Animación de rotación */
  }

  @keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
  }
</style>

<script>
  // Definir una lista de mensajes para el mensaje de carga
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
"🎩 Sacando conejos del sombrero de la programación...",
  ];

  // Función para mostrar el mensaje de carga con un texto aleatorio
  function mostrarCargando(event, settings) {
    var randomIndex = Math.floor(Math.random() * mensajesCarga.length);
    var mensaje = mensajesCarga[randomIndex];
    document.getElementById('loading-text').innerText = mensaje;
    document.getElementById('loading-overlay').style.display = 'flex';
  }

  // Función para ocultar el mensaje de carga
  function ocultarCargando() {
    document.getElementById('loading-overlay').style.display = 'none';
  }


// Variables globales para filtros (hacerlas globales)
// Inicializar como null para que no se envíen parámetros al inicio (usar valores por defecto)
window.filtrosActuales = {
    filtro_sucursal: null,
    filtro_mes: null,
    filtro_anio: null,
    filtro_fecha_inicio: null,
    filtro_fecha_fin: null
};
var filtrosActuales = window.filtrosActuales;

tabla = $('#Clientes').DataTable({

 "bProcessing": true,
 "ordering": true,
 "stateSave":true,
 "bAutoWidth": false,
 "order": [[ 0, "desc" ]],
 "ajax": {
    "url": "' . BASE_URL . 'ControlYAdministracion/Controladores/ArrayDesgloseTickets.php",
    "type": "POST",
    "data": function(d) {
        // Agregar los filtros actuales a la petición
        // Solo enviar los parámetros si tienen un valor (no null/undefined)
        var filtros = window.filtrosActuales || filtrosActuales;
        
        // Solo agregar filtro_sucursal si tiene un valor (no null/undefined)
        // Si es null, no se envía el parámetro y el backend usará el valor por defecto
        // Si es cadena vacía "", se envía para indicar "todas las sucursales"
        if (filtros.filtro_sucursal !== null && filtros.filtro_sucursal !== undefined) {
            d.filtro_sucursal = filtros.filtro_sucursal;
        }
        // Si es null, no agregar el parámetro (el backend usará la sucursal del usuario por defecto)
        
        if (filtros.filtro_mes !== null && filtros.filtro_mes !== undefined && filtros.filtro_mes !== '') {
            d.filtro_mes = filtros.filtro_mes;
        }
        
        if (filtros.filtro_anio !== null && filtros.filtro_anio !== undefined && filtros.filtro_anio !== '') {
            d.filtro_anio = filtros.filtro_anio;
        }
        
        if (filtros.filtro_fecha_inicio !== null && filtros.filtro_fecha_inicio !== undefined && filtros.filtro_fecha_inicio !== '') {
            d.filtro_fecha_inicio = filtros.filtro_fecha_inicio;
        }
        
        if (filtros.filtro_fecha_fin !== null && filtros.filtro_fecha_fin !== undefined && filtros.filtro_fecha_fin !== '') {
            d.filtro_fecha_fin = filtros.filtro_fecha_fin;
        }
    },
    "dataSrc": function(json) {
        // Actualizar estadísticas si están disponibles
        if (json.estadisticas) {
            if (typeof window.actualizarEstadisticas === 'function') {
                window.actualizarEstadisticas(json.estadisticas);
            } else if (typeof actualizarEstadisticas === 'function') {
                actualizarEstadisticas(json.estadisticas);
            }
        }
        
        // Manejar errores
        if (json.error) {
            console.error('Error en la respuesta:', json.error);
            return [];
        }
        
        return json.aaData || [];
    }
 },
 "aoColumns": [
    { mData: 'NumberTicket' },  
  { mData: 'Fecha' },
  { mData: 'Hora' },
       { mData: 'Vendedor' },
       { mData: 'Desglose' },
       { mData: 'Reimpresion' },
       { mData: 'Eliminar' }
      ],
     
      "lengthMenu": [[20,150,250,500, -1], [20,50,250,500, "Todos"]],  
  
  "language": {
  "lengthMenu": "Mostrar _MENU_ registros",
  "sPaginationType": "extStyle",
  "zeroRecords": "No se encontraron resultados",
  "info": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
  "infoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
  "infoFiltered": "(filtrado de un total de _MAX_ registros)",
  "sSearch": "Buscar:",
  "paginate": {
    "first": '<i class="fas fa-angle-double-left"></i>',
    "last": '<i class="fas fa-angle-double-right"></i>',
    "next": '<i class="fas fa-angle-right"></i>',
    "previous": '<i class="fas fa-angle-left"></i>'
  },
  "processing": function () {
    mostrarCargando();
  }
},
"initComplete": function() {
  // Al completar la inicialización de la tabla, ocultar el mensaje de carga
  ocultarCargando();
},
// Para personalizar el estilo del botón de Excel
"buttons": [
  {
    extend: 'excelHtml5',
    text: 'Exportar a Excel  <i Exportar a Excel class="fas fa-file-excel"></i> ',
    titleAttr: 'Exportar a Excel',
    title: 'Base de Clientes',
    className: 'btn btn-success',
    exportOptions: {
      columns: ':visible' // Exportar solo las columnas visibles
    }
  }
],
// Personalizar la posición de los elementos del encabezado
"dom": '<"d-flex justify-content-between"lBf>rtip', // Modificar la disposición aquí
"responsive": true
});

// Función para actualizar estadísticas (hacerla global)
window.actualizarEstadisticas = function(stats) {
    if (stats) {
        $('#total-tickets').text(stats.total_tickets || 0);
        $('#total-ventas').text('$' + parseFloat(stats.total_ventas || 0).toLocaleString('es-MX', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
        $('#tickets-hoy').text(stats.tickets_hoy || 0);
        $('#promedio-ticket').text('$' + parseFloat(stats.promedio_ticket || 0).toLocaleString('es-MX', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
    }
};

// Función para aplicar filtros (hacerla global)
window.aplicarFiltros = function() {
    if (typeof tabla !== 'undefined' && tabla) {
        mostrarCargando();
        tabla.ajax.reload(function() {
            ocultarCargando();
        });
    }
};

// Función para filtrar por sucursal (hacerla global)
window.filtrarPorSucursal = function(sucursalId) {
    // Si sucursalId es cadena vacía, establecer como null para mostrar todas
    // Si tiene valor, usarlo normalmente
    window.filtrosActuales.filtro_sucursal = (sucursalId === '' || sucursalId === null) ? null : sucursalId;
    window.filtrosActuales.filtro_mes = null;
    window.filtrosActuales.filtro_anio = null;
    window.filtrosActuales.filtro_fecha_inicio = null;
    window.filtrosActuales.filtro_fecha_fin = null;
    window.aplicarFiltros();
    var modal = bootstrap.Modal.getInstance(document.getElementById('FiltroPorSucursal')) || $('#FiltroPorSucursal').data('bs.modal');
    if (modal) {
        modal.hide();
    } else {
        $('#FiltroPorSucursal').modal('hide');
    }
};

// Función para filtrar por mes (hacerla global)
window.filtrarPorMes = function(mes, anio) {
    window.filtrosActuales.filtro_mes = mes;
    window.filtrosActuales.filtro_anio = anio;
    window.filtrosActuales.filtro_sucursal = null;
    window.filtrosActuales.filtro_fecha_inicio = null;
    window.filtrosActuales.filtro_fecha_fin = null;
    window.aplicarFiltros();
    var modal = bootstrap.Modal.getInstance(document.getElementById('FiltroEspecificoMes')) || $('#FiltroEspecificoMes').data('bs.modal');
    if (modal) {
        modal.hide();
    } else {
        $('#FiltroEspecificoMes').modal('hide');
    }
};

// Función para filtrar por rango de fechas (hacerla global)
window.filtrarPorRangoFechas = function(fechaInicio, fechaFin, sucursalId) {
    window.filtrosActuales.filtro_fecha_inicio = fechaInicio;
    window.filtrosActuales.filtro_fecha_fin = fechaFin;
    // Si sucursalId está vacío o es null, establecer como null (no filtrar por sucursal)
    window.filtrosActuales.filtro_sucursal = (sucursalId === '' || sucursalId === null || sucursalId === undefined) ? null : sucursalId;
    window.filtrosActuales.filtro_mes = null;
    window.filtrosActuales.filtro_anio = null;
    window.aplicarFiltros();
    var modal = bootstrap.Modal.getInstance(document.getElementById('FiltroRangoFechas')) || $('#FiltroRangoFechas').data('bs.modal');
    if (modal) {
        modal.hide();
    } else {
        $('#FiltroRangoFechas').modal('hide');
    }
};

// Función para recargar y limpiar filtros (hacerla global)
window.cargarTicketsDesdeTabla = function() {
    window.filtrosActuales = {
        filtro_sucursal: null,
        filtro_mes: null,
        filtro_anio: null,
        filtro_fecha_inicio: null,
        filtro_fecha_fin: null
    };
    window.aplicarFiltros();
};

// Función para limpiar todos los filtros y volver al estado inicial (hacerla global)
window.limpiarFiltros = function() {
    // Resetear todos los filtros a null/undefined para que no se envíen al backend
    // Esto hará que el backend use los valores por defecto (sucursal del usuario, mes actual)
    window.filtrosActuales = {
        filtro_sucursal: null,  // null en lugar de '' para que no se envíe el parámetro
        filtro_mes: null,
        filtro_anio: null,
        filtro_fecha_inicio: null,
        filtro_fecha_fin: null
    };
    
    // Limpiar los valores en los modales también
    if ($('#sucursalFiltro').length) $('#sucursalFiltro').val('');
    if ($('#mesesSelect').length) $('#mesesSelect').val('');
    if ($('#añosSelect').length) $('#añosSelect').val('');
    if ($('#fechaInicio').length) $('#fechaInicio').val('');
    if ($('#fechaFin').length) $('#fechaFin').val('');
    if ($('#sucursalRango').length) $('#sucursalRango').val('');
    
    // Cerrar todos los modales abiertos
    $('.modal').modal('hide');
    
    // Recargar la tabla sin filtros
    if (typeof window.aplicarFiltros === 'function') {
        window.aplicarFiltros();
    } else if (typeof tabla !== 'undefined' && tabla) {
        mostrarCargando();
        tabla.ajax.reload(function() {
            ocultarCargando();
        });
    }
    
    // Mostrar mensaje de confirmación
    if (typeof toastr !== 'undefined') {
        toastr.success('Filtros limpiados correctamente. Mostrando datos por defecto.');
    } else {
        console.log('Filtros limpiados correctamente');
        alert('Filtros limpiados correctamente');
    }
};
</script>
<div class="text-center">
  <div class="table-responsive">
  <table  id="Clientes"  class="order-column">
<thead>
<th>N° Ticket</th>

<th>Fecha</th>
<th>Hora</th>
    <th>Vendedor</th>
    <th>Desglose</th>
    <th>Reimpresion</th>
    <th>Eliminar</th>
</thead>

</div>
</div>





