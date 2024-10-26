<script>
  $(document).ready(function() {
    // Delegación de eventos para el botón "btn-Reimpresion" dentro de .dropdown-menu
    $(document).on("click", ".btn-Reimpresion", function() {
        var id = $(this).data("id");  // Asignar el valor correcto aquí
        console.log("Botón de cancelar clickeado para el ID:", id); // Mover console.log después de la asignación de id
        $('#CajasDi').removeClass('modal-dialog  modal-xl modal-notify modal-success').addClass('modal-dialog  modal-notify modal-success');  // Asegúrate de que solo tenga el tamaño grande
        $.post("https://doctorpez.mx/PuntoDeVenta/PuntoDeVentaFarmacias/Modales/ReimprimeTicketsVenta.php", { id: id }, function(data) {
            $("#FormCajas").html(data);
            $("#TitulosCajas").html("Generando archivo para reimpresion");
        });
        
        $('#ModalEdDele').modal('show');
    });


    // Delegación de eventos para el botón "btn-Reimpresion" dentro de .dropdown-menu
    $(document).on("click", ".btn-desglose", function() {
        var id = $(this).data("id");  // Asignar el valor correcto aquí
        console.log("Botón de cancelar clickeado para el ID:", id); // Mover console.log después de la asignación de id
        
    $('#CajasDi').removeClass('modal-dialog  modal-notify modal-success').addClass('modal-dialog  modal-xl modal-notify modal-success');  // Asegúrate de que solo tenga el tamaño grande
   
        $.post("https://doctorpez.mx/PuntoDeVenta/PuntoDeVentaFarmacias/Modales/DesgloseTicketsVenta.php", { id: id }, function(data) {
          $("#TitulosCajas").html("Desglose de ticket");  
          $("#FormCajas").html(data);
            $("#TitulosCajas").html("Desglose de ticket");
        });
        
        $('#ModalEdDele').modal('show');
    });


   

});



</script>





  <div class="modal fade" id="ModalEdDele" tabindex="-1" role="dialog" style="overflow-y: scroll;" aria-labelledby="ModalEdDeleLabel" aria-hidden="true">
  <div id="CajasDi"class="modal-dialog  modal-notify modal-success" >
    <div class="text-center">
      <div class="modal-content">
      <div class="modal-header" style=" background-color: #ef7980 !important;" >
         <p class="heading lead" id="TitulosCajas"  style="color:white;" ></p>

         
       </div>
        
	        <div class="modal-body">
          <div class="text-center">
        <div id="FormCajas"></div>
        
        </div>

      </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
  </div><!-- /.modal --></div>
 