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
  #PagosServicios th {
    font-size: 12px; /* Tamaño de letra para los encabezados */
    padding: 4px; /* Ajustar el espaciado entre los encabezados */
    white-space: nowrap; /* Evitar que los encabezados se dividan en varias líneas */
  }
</style>

<style>
  /* Estilos para la tabla */
  #PagosServicios {
    font-size: 12px; /* Tamaño de letra para el contenido de la tabla */
    border-collapse: collapse; /* Colapsar los bordes de las celdas */
    width: 100%;
    text-align: center; /* Centrar el contenido de las celdas */
  }

  #PagosServicios th {
    font-size: 16px; /* Tamaño de letra para los encabezados de la tabla */
    background-color: #ef7980 !important; /* Nuevo color de fondo para los encabezados */
    color: white; /* Cambiar el color del texto a blanco para contrastar */
    padding: 10px; /* Ajustar el espaciado de los encabezados */
  }

  #PagosServicios td {
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

  /* Estilos para los botones de acciones */
  .btn-group-vertical {
    display: flex;
    flex-direction: column;
    gap: 2px;
  }

  .btn-group-vertical .btn {
    margin: 1px 0;
    font-size: 11px;
    padding: 4px 8px;
    border-radius: 4px;
    white-space: nowrap;
    min-width: 60px;
  }

  .btn-group-vertical .btn i {
    margin-right: 3px;
  }

  /* Estilos específicos para cada tipo de botón */
  .btn-VerPago {
    background-color: #17a2b8 !important;
    border-color: #17a2b8 !important;
    color: white !important;
  }

  .btn-VerPago:hover {
    background-color: #138496 !important;
    border-color: #117a8b !important;
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
    "Consultando pagos de servicios...",
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


tabla = $('#PagosServicios').DataTable({

 "bProcessing": true,
 "ordering": true,
 "stateSave":true,
 "bAutoWidth": false,
 "order": [[ 0, "desc" ]],
 "sAjaxSource": "https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/Controladores/ArrayDataPagosServicios.php",
 "aoColumns": [
  { mData: 'NumTicket' },
  { mData: 'NombrePaciente' },
  { mData: 'Servicio' },
  { mData: 'Costo' },
  { mData: 'FormaDePago' },
  { mData: 'Estado' },
  { mData: 'Sucursal' },
  { mData: 'Empleado' },
  { mData: 'Acciones' }
      
 
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
  
  // Agregar eventos para los botones de acciones
  agregarEventosAcciones();
},
// Para personalizar el estilo del botón de Excel

// Personalizar la posición de los elementos del encabezado
"dom": '<"d-flex justify-content-between"lf>rtip', // Sin botones de impresión ni descarga
"responsive": true
});

// Función para agregar eventos a los botones de acciones
function agregarEventosAcciones() {
  // Evento para ver detalles del pago
  $(document).on('click', '.btn-VerPago', function() {
    var pagoId = $(this).data('id');
    console.log('Ver pago ID:', pagoId);
    
    // Mostrar indicador de carga
    Swal.fire({
      title: 'Cargando detalles...',
      text: 'Por favor espere',
      allowOutsideClick: false,
      allowEscapeKey: false,
      showConfirmButton: false,
      didOpen: () => {
        Swal.showLoading();
      }
    });
    
    // Mostrar modal con detalles del pago
    $.ajax({
      url: 'Modales/VerPagoServicio.php',
      type: 'POST',
      data: { id: pagoId },
      success: function(response) {
        Swal.close();
        $('#modalContent').html(response);
        $('#editModal').modal('show');
      },
      error: function(xhr, status, error) {
        console.error('Error al cargar detalles del pago:', error);
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: 'No se pudieron cargar los detalles del pago',
          confirmButtonText: 'Aceptar'
        });
      }
    });
  });
}

// Agregar eventos cuando se redibuja la tabla
$('#PagosServicios').on('draw.dt', function() {
  agregarEventosAcciones();
});
</script>
<div class="text-center">
  <div class="table-responsive">
  <table  id="PagosServicios"  class="order-column">
<thead>
<th>Número de Ticket</th>
<th>Cliente</th>
<th>Servicio</th>
<th>Costo</th>
<th>Forma de Pago</th>

<th>Sucursal</th>
<th>Empleado</th>
<th>Acciones</th>
</thead>

</div>
</div>

<!-- Modal para acciones -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editModalLabel">Detalles del Pago de Servicio</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="modalContent">
        <!-- El contenido se cargará aquí -->
        <p>Cargando...</p>
      </div>
    </div>
  </div>
</div>
