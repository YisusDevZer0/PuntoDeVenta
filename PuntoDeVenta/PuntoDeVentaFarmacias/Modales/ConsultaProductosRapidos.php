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
        <!-- Aquí se mostrarán los datos -->
        <div id="productosTable"></div>
      </div>
    </div>
  </div>
</div>

<script>
  $(document).ready(function() {
    // Función para cargar los datos en el modal al abrirlo
    $('#ConsultaProductosModal').on('show.bs.modal', function() {
      $.ajax({
        url: 'https://doctorpez.mx/PuntoDeVenta/PuntoDeVentaFarmacias/Controladores/ArrayStocks.php', // Cambia esto por la ruta correcta a tu archivo PHP
        type: 'GET',
        dataType: 'json',
        success: function(response) {
          // Construir la tabla con los datos recibidos
          var table = '<table class="table">';
          table += '<thead><tr><th>Cod_Barra</th><th>Nombre</th><th>Precio</th></tr></thead><tbody>';
          $.each(response.aaData, function(index, value) {
            table += '<tr>';
            table += '<td>' + value.Cod_Barra + '</td>';
            table += '<td>' + value.Nombre_Prod + '</td>';
            table += '<td>' + value.Precio_Venta + '</td>';
            table += '</tr>';
          });
          table += '</tbody></table>';

          // Mostrar la tabla en el modal
          $('#productosTable').html(table);
        }
      });
    });
  });
</script>