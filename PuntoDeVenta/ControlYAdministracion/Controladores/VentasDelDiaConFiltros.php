<?php
include_once "../Controladores/db_connect.php";

// Obtener parámetros de filtro
$fecha_inicio = isset($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : date('Y-m-01');
$fecha_fin = isset($_GET['fecha_fin']) ? $_GET['fecha_fin'] : date('Y-m-d');
$sucursal = isset($_GET['sucursal']) ? $_GET['sucursal'] : '';

// Construir la consulta SQL con filtros
$sql = "SELECT 
    v.Cod_Barra,
    v.Nombre_Prod,
    v.PrecioCompra,
    v.PrecioVenta,
    v.FolioTicket,
    s.Nombre_Sucursal as Sucursal,
    v.Turno,
    v.Cantidad_Venta,
    v.Total_Venta,
    v.Importe,
    v.Descuento,
    v.FormaPago,
    v.Cliente,
    v.FolioSignoVital,
    v.NomServ,
    v.AgregadoEl,
    v.AgregadoEnMomento,
    v.AgregadoPor
FROM Ventas v
LEFT JOIN Sucursales s ON v.ID_Sucursal = s.ID_Sucursal
WHERE v.AgregadoEl BETWEEN ? AND ?
";

$params = [$fecha_inicio . ' 00:00:00', $fecha_fin . ' 23:59:59'];
$types = "ss";

// Agregar filtro de sucursal si se especifica
if (!empty($sucursal)) {
    $sql .= " AND v.ID_Sucursal = ?";
    $params[] = $sucursal;
    $types .= "i";
}

$sql .= " ORDER BY v.AgregadoEl DESC";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Error en la preparación de la consulta: " . $conn->error);
}

$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

// Construir la URL para el AJAX de DataTables
$ajax_url = "https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/Controladores/ArrayVentasSimple.php";
?>

<style>
  /* Personalizar el diseño de la paginación con CSS */
  .dataTables_wrapper .dataTables_paginate {
    text-align: center !important;
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

  .dataTables_paginate .paginate_button.current {
    background-color: #ef7980 !important;
    color: #fff !important;
    border-color: #ef7980 !important;
  }

  .dataTables_paginate .paginate_button:hover {
    background-color: #C80096 !important;
    color: #fff !important;
    border-color: #C80096 !important;
  }
</style>

<style>
  /* Estilos personalizados para la tabla */
  #Clientes th {
    font-size: 12px;
    padding: 4px;
    white-space: nowrap;
  }
</style>

<style>
  /* Estilos para la tabla */
  #Clientes {
    font-size: 12px;
    border-collapse: collapse;
    width: 100%;
    text-align: center;
  }

  #Clientes th {
    font-size: 16px;
    background-color: #ef7980 !important;
    color: white;
    padding: 10px;
  }

  #Clientes td {
    font-size: 14px;
    padding: 8px;
    border-bottom: 1px solid #ccc;
    color:#000000;
  }

  .dt-buttons {
    display: flex;
    justify-content: center;
    margin-bottom: 10px;
  }

  .dt-buttons button {
    font-size: 14px;
    margin: 0 5px;
    color: white;
    background-color: #fff;
  }
</style>

<script>
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

  tabla = $('#Clientes').DataTable({
    "bProcessing": true,
    "ordering": true,
    "stateSave": true,
    "bAutoWidth": false,
    "order": [[ 0, "desc" ]],
    "sAjaxSource": "<?php echo $ajax_url; ?>",
    "aoColumns": [
      { mData: 'Cod_Barra' },
      { mData: 'Nombre_Prod' },
      { mData: 'PrecioCompra' },
      { mData: 'PrecioVenta' },
      { mData: 'FolioTicket' },
      { mData: 'Sucursal' },
      { mData: 'Turno' },
      { mData: 'Cantidad_Venta' },
      { mData: 'Total_Venta' },
      { mData: 'Importe' },
      { mData: 'Descuento' },
      { mData: 'FormaPago' },
      { mData: 'Cliente' },
      { mData: 'FolioSignoVital' },
      { mData: 'NomServ' },
      { mData: 'AgregadoEl' },
      { mData: 'AgregadoEnMomento' },
      { mData: 'AgregadoPor' }
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
      ocultarCargando();
    },
    "buttons": [
      {
        extend: 'excelHtml5',
        text: 'Exportar a Excel <i class="fas fa-file-excel"></i>',
        titleAttr: 'Exportar a Excel',
        title: 'Ventas del Día',
        className: 'btn btn-success',
        exportOptions: {
          columns: ':visible'
        }
      }
    ],
    "dom": '<"d-flex justify-content-between"lBf>rtip',
    "responsive": true
  });
</script>

<div class="text-center">
  <div class="table-responsive">
    <table id="Clientes" class="order-column">
      <thead>
        <th>Cod</th>
        <th>Nombre</th>
        <th>Precio compra</th>
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
  </div>
</div>
