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

  /* Estilos personalizados para la tabla */
  #Productos th {
    font-size: 12px; /* Tamaño de letra para los encabezados */
    padding: 4px; /* Ajustar el espaciado entre los encabezados */
    white-space: nowrap; /* Evitar que los encabezados se dividan en varias líneas */
  }

  #Productos {
    font-size: 12px; /* Tamaño de letra para el contenido de la tabla */
    border-collapse: collapse; /* Colapsar los bordes de las celdas */
    width: 100%;
    text-align: center; /* Centrar el contenido de las celdas */
  }

  #Productos th {
    font-size: 16px; /* Tamaño de letra para los encabezados de la tabla */
    background-color: #ef7980 !important; /* Nuevo color de fondo para los encabezados */
    color: white; /* Cambiar el color del texto a blanco para contrastar */
    padding: 10px; /* Ajustar el espaciado de los encabezados */
  }

  #Productos td {
    font-size: 14px; /* Tamaño de letra para el contenido de la tabla */
    padding: 8px; /* Ajustar el espaciado de las celdas */
    border-bottom: 1px solid #ccc; /* Agregar una línea de separación entre las filas */
    color: #000000;
  }
</style>

<script>
  // Inicializar la tabla con DataTables
  $(document).ready(function () {
    $('#Productos').DataTable({
      "bProcessing": true,
      "ordering": true,
      "stateSave": true,
      "bAutoWidth": false,
      "order": [[0, "desc"]],
      "sAjaxSource": "https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/Controladores/ArrayProductosActualizadosminmax.php", // Cambiar la fuente de datos
      "aoColumns": [
        { mData: 'id' }, // ID único
        { mData: 'Folio_Prod_Stock' }, // Folio del producto
        { mData: 'ID_Prod_POS' }, // ID del producto
        { mData: 'Cod_Barra' }, // Código de barra
        { mData: 'Nombre_Prod' }, // Nombre del producto
        { mData: 'Fk_sucursal' }, // ID de la sucursal
        { mData: 'Nombre_Sucursal' }, // Nombre de la sucursal
        { mData: 'Max_Existencia' }, // Máximo de existencia
        { mData: 'Min_Existencia' }, // Mínimo de existencia
        { mData: 'AgregadoPor' }, // Usuario que agregó
        { mData: 'FechaAgregado' } // Fecha de agregado
      ],
      "lengthMenu": [[10, 20, 50, 100, -1], [10, 20, 50, 100, "Todos"]],
      "language": {
        "lengthMenu": "Mostrar _MENU_ registros",
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
        }
      },
      "dom": '<"d-flex justify-content-between"lf>rtip', // Modificar la disposición aquí
      "responsive": true
    });
  });
</script>

<div class="text-center">
  <div class="table-responsive">
    <table id="Productos" class="table table-hover">
      <thead>
        <tr>
          <th>ID</th>
          <th>Folio Producto</th>
          <th>ID Producto</th>
          <th>Código de Barra</th>
          <th>Nombre Producto</th>
          <th>ID Sucursal</th>
          <th>Nombre Sucursal</th>
          <th>Máximo</th>
          <th>Mínimo</th>
          <th>Agregado Por</th>
          <th>Fecha Agregado</th>
        </tr>
      </thead>
    </table>
  </div>
</div>