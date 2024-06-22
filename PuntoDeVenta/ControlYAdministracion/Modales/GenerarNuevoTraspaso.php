
<?php
  $sql = "SELECT * FROM Traspasos_generados ORDER BY ID_Traspaso_Generado DESC LIMIT 1";
  $resultset = mysqli_query($conn, $sql) or die("database error:" . mysqli_error($conn));
  $Ticketss = mysqli_fetch_assoc($resultset);
  
  $monto1 = $Ticketss['Num_Orden'];
  $monto2 = 1;
  $totalmonto = $monto1 + $monto2;
  
  // Obtener la longitud original de $Ticketss['Num_Orden']
  $longitud_original = strlen($Ticketss['Num_Orden']);
  
  // Mostrar $totalmonto con los caracteres '0000000000' (ajustando la longitud)
  $totalmonto_con_ceros = str_pad($totalmonto, $longitud_original, '0', STR_PAD_LEFT);
  

   
  
  ?>
      <div class="modal fade bd-example-modal-xl" id="GeneradorDeOrdenesDeTraspaso" tabindex="-1" role="dialog" aria-labelledby="myExtraLargeModalLabel" aria-hidden="true">
      <div class="modal-dialog  modal-notify modal-success">
    <div class="modal-content">
      <div class="modal-header" style=" background-color: #ef7980 !important;">
        <h5 class="modal-title" style="color:white;" id="exampleModalLabel">Agregar Nuevo Producto</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="text-center">
             <div class="modal-body">
     
 <form  method="POST" action="https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/GeneradorTraspasosCEDIS">
  <div class="form-group">
  <label for="exampleInputEmail1">Elija proveedor</label>

    <input type="text" class="form-control" name="NombreProveedor" value="CEDIS">
  </div>
</div>

<div class="form-group">
  <label for="exampleInputEmail1">Elija sucursal</label>
 
    <select id="sucursalconorden" name="SucursalConOrdenDestino" class="form-control" required>
      <option value="">Seleccione una Sucursal:</option>
      <?php
        $query = $conn -> query ("SELECT ID_Sucursal,Nombre_Sucursal FROM Sucursales");
        while ($valores = mysqli_fetch_array($query)) {
          echo '<option value="'.$valores["ID_Sucursal"].'">'.$valores["Nombre_Sucursal"].'</option>';
        }
      ?>
    </select>
  </div>


<div class="form-group">
  <label for="exampleInputEmail1"># de orden de traspaso</label>
  <div class="input-group-prepend">
    <span class="input-group-text" id="Tarjeta2"><i class="fas fa-list-ol"></i></span>
    <input type="number" value="<?php echo  $totalmonto_con_ceros?>" class="form-control" id="NumOrden" name="NumOrden" readonly>
  </div>
</div>

<div class="form-group">
  <label for="exampleInputEmail1">Sucursal</label>
  <div class="input-group-prepend">
    <span class="input-group-text" id="Tarjeta2"><i class="fas fa-barcode"></i></span>
    <input type="text" name="sucursalLetras" id="sucursalLetras" class="form-control">
  </div>
</div>

<button type="submit" id="registrotraspaso" value="Guardar" class="btn btn-success">
  Generar orden de traspaso <i class="fas fa-exchange-alt"></i>
</button>

                                        </form>
                                        </div>
                                        </div> </div>
     <script>
$(document).on('change', '#sucursalconorden', function(event) {
     $('#sucursalLetras').val($("#sucursalconorden option:selected").text());
});

     </script>

<script>
    // Función para manejar cambios en el proveedor seleccionado
    $('#nombreproveedor').on('change', function() {
        var selectedProveedor = $(this).val();

        if (selectedProveedor === 'CEDIS') {
            $('#numFacturaContainer').hide(); // Ocultar el input NumFactura

            // Combinar las primeras 4 letras del input sucursalLetras con totalmonto y establecerlo como valor del input NumOrden
            var inputSucursal = $('#sucursalLetras').val().slice(0, 4);
            var totalmonto = '<?php echo $totalmonto; ?>'; // Convertimos $totalmonto a cadena con comillas
            $('#NumOrden').val(inputSucursal + totalmonto);
        } else {
            $('#numFacturaContainer').show(); // Mostrar el input NumFactura
        }
    });

    // Llamar al evento change del select al cargar la página para establecer el valor inicial de NumOrden solo si se seleccionó CEDIS
    $(document).ready(function() {
        if ($('#nombreproveedor').val() === 'CEDIS') {
            $('#nombreproveedor').trigger('change');
        }
    });
</script>


<script>

function comprobarFactura() {
	$("#loaderIcon").show();
	jQuery.ajax({
	url: "https://saludapos.com/AdminPOS/Consultas/ComprobarFactura.php",
	data:'NumFactura='+$("#NumFactura").val(),
	type: "POST",
	success:function(data){
		$("#estadousuario").html(data);
		$("#loaderIcon").hide();
	},
	error:function (){}
	});
}

</script>


<script>
  
  function desactivar()
{
  $('#registrotraspaso').attr('disabled', true);
  
}

function reactivar(){
  $('#registrotraspaso').attr('disabled', false);
}
</script>

<!-- Agrega un script para la validación -->
<script>
  // Utiliza jQuery para escuchar el evento de apertura del modal
  $('#FiltroLabs').on('show.bs.modal', function () {
    console.log("Script ejecutándose..."); // Verifica si este mensaje aparece en la consola

    // Obtiene el valor actual del campo NumOrden
    var numOrdenValue = $('#NumOrden').val();

    // Realiza una solicitud AJAX para verificar si el valor ya existe en la base de datos
    $.ajax({
      url: 'Consultas/ValidacionNumOrden.php',
      method: 'POST',
      data: { NumOrden: numOrdenValue },
      success: function (response) {
     // Verifica la respuesta del servidor
     if (response.trim() === 'existe') {
          // Si la respuesta indica que el valor ya existe, proporciona retroalimentación al usuario
          alert('El número de orden ya existe. Por favor, elija otro.');
          // Puedes deshabilitar el botón de guardar o realizar otras acciones según tus necesidades
        } else {
          // Puedes realizar acciones adicionales si el NumOrden no existe
          console.log('El número de orden no existe.');
        }
      },
      error: function (error) {
        console.error('Error en la solicitud AJAX:', error);
      }
    });
  });
</script>


    </div>
  </div>
  </div>
  </div>
  