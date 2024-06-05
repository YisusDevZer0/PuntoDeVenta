<!-- Botón para abrir el modal -->
<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#ConsultaProductosModal">
  Consultar Productos
</button>

<!-- Modal -->
<div class="modal fade" id="ConsultaProductosModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Consulta de Productos</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="table-responsive">
          <table id="StocksDESucursales" class="table table-hover">
            <thead>
              <th>Cod Barra</th>
              <th>Nombre</th>
              <th>Clave interna</th>
              <th>PV </th>
              <th>Servicio </th>
              <th>Tipo</th>
              <th>Proveedor</th>
              <th>Proveedor</th>
              <th>Ultima actualizacion</th>
              <th>Stock</th>
              <th>Maximo</th>
              <th>Minimo</th>
              <th>Estado</th>
              <th>Acciones Farmacia</th>
              <th>Acciones Enfermeria</th>
            </thead>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  // Definir una lista de mensajes para el mensaje de carga
  var mensajesCarga = [
    // Mensajes de carga...
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

  // Inicializar DataTables dentro del modal
  $('#ConsultaProductosModal').on('shown.bs.modal', function() {
    var tabla = $('#StocksDESucursales').DataTable({
      "bProcessing": true,
      // Otras opciones de DataTables...
      "ajax": {
        "url": "https://doctorpez.mx/PuntoDeVenta/PuntoDeVentaFarmacias/Controladores/ArrayStocks.php",
        "dataSrc": ""
      },
      "columns": [
        { "data": "Cod_Barra" },
        { "data": "Nombre_Prod" },
        { "data": "Clave_adicional" },
        { "data": "Precio_Venta" },
        { "data": "Nom_Serv" },
        { "data": "Tipo" },
        { "data": "Proveedor1" },
        { "data": "Proveedor2" },
        { "data": "UltimoMovimiento" },
        { "data": "Existencias_R" },
        { "data": "Min_Existencia" },
        { "data": "Max_Existencia" },
        { 
          "data": "Existencias_R",
          "render": function(data, type, row) {
            // Lógica para el botón de estado
            // Puedes personalizar según tus necesidades
          }
        },
        { "data": "Editar" },
        { "data": "Eliminar" }
      ],
      // Otras opciones de DataTables...
      "language": {
        // Configuración de idioma...
      },
      "initComplete": function() {
        ocultarCargando(); // Ocultar el mensaje de carga cuando se completa la inicialización
      }
    });
  });
</script>

<!-- Capa de carga -->
<div id="loading-overlay">
  <div class="loader"></div>
  <div id="loading-text" style="color: white; margin-top: 10px;"></div>
</div>
