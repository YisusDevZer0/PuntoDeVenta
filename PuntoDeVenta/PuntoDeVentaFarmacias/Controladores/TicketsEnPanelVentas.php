
<script type="text/javascript">
$(document).ready( function () {
    var printCounter = 0;
    $('#VEntas').DataTable({
     
      "lengthMenu": [[25,50, 150, 200, -1], [25,50, 150, 200, "Todos"]],   
        language: {
            "lengthMenu": "Mostrar _MENU_ registros",
                "zeroRecords": "No se encontraron resultados",
                "info": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                "infoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
                "infoFiltered": "(filtrado de un total de _MAX_ registros)",
                "sSearch": "Buscar:",
                "oPaginate": {
                    "sFirst": "Primero",
                    "sLast":"Último",
                    "sNext":"Siguiente",
                    "sPrevious": "Anterior"
			     },
			     "sProcessing":"Procesando...",
            },
          
        //para usar los botones   
        responsive: "true",
        dom: "<'#colvis row'><'row'><'row'<'col-md-6'l><'col-md-6'f>r>t<'bottom'ip><'clear'>'",
        
   
	   
        	        
    });     
});
   
	  
	 
</script>
<?php 
include "db_connect.php";
include "ControladorUsuario.php";


$user_id=null;
$sql1= "SELECT Ventas_POS.Folio_Ticket,Ventas_POS.Fk_Caja,Ventas_POS.Venta_POS_ID,Ventas_POS.Identificador_tipo,Ventas_POS.Cod_Barra,Ventas_POS.Clave_adicional,
Ventas_POS.Nombre_Prod,Ventas_POS.Cantidad_Venta,Ventas_POS.Fk_sucursal,Ventas_POS.AgregadoPor,Ventas_POS.AgregadoEl,
Ventas_POS.Total_Venta,Ventas_POS.Lote,Ventas_POS.ID_H_O_D,Sucursales.ID_Sucursal,Sucursales.Nombre_Sucursal FROM 
Ventas_POS,Sucursales WHERE   
Ventas_POS.Fk_sucursal= Sucursales.ID_Sucursal  AND Ventas_POS.Fk_sucursal='".$row['Fk_Sucursal']."' 
AND Ventas_POS.ID_H_O_D ='".$row['ID_H_O_D']."' GROUP BY (Ventas_POS.Folio_Ticket) ORDER BY `Ventas_POS`.`AgregadoEl` DESC ";
$query = $conn->query($sql1);
?>

<?php if($query->num_rows>0):?>
  <div class="text-center">
	<div class="table-responsive">
	<table  id="VEntas" class="table table-hover">
<thead>


<th>N° Ticket</th>
<th>Fecha | Hora</th>
    <th>Vendedor</th>
    <th>Acciones</th>
    


</thead>
<?php while ($Usuarios=$query->fetch_array()):?>
<tr>



    <td><?php echo $Usuarios["Folio_Ticket"]; ?></td>
    

      <td><?php echo fechaCastellano($Usuarios["AgregadoEl"]); ?> <br>
      <?php echo date("g:i a",strtotime($Usuarios["AgregadoEl"])); ?>
    </td>
    <td><?php echo $Usuarios["AgregadoPor"]; ?></button></td>
    <td>
		 <!-- Basic dropdown -->
<button class="btn btn-primary dropdown-toggle " type="button" data-toggle="dropdown"
  aria-haspopup="true" aria-expanded="false"><i class="fas fa-list-ul"></i></button>

<div class="dropdown-menu">
<a data-id="<?php echo $Usuarios["Folio_Ticket"];?>" class="btn-desglose dropdown-item" >Desglosar ticket <i class="fas fa-receipt"></i></a>
<a data-id="<?php echo $Usuarios["Folio_Ticket"];?>" class="btn-Reimpresion dropdown-item" >Reimpresión ticket <i class="fas fa-print"></i></a> 
<a href="https://controlfarmacia.com/POS2/TicketVenta.php=<?php echo $Usuarios["Folio_Ticket"];?>" target="_Blank" class="btn-Reimpresion dropdown-item">Reimpresión ticket <i class="fas fa-print"></i></a>

</div>
<!-- Basic dropdown -->
	 </td>
     
      
   
</tr>
<?php endwhile;?>
</table>
</div>
</div>
<?php else:?>
	<p class="alert alert-warning">No hay resultados</p>
<?php endif;?>
<script>
    $(".btn-desglose").click(function(){
    id = $(this).data("id");
    $.post("https://controlfarmacia.com/POS2/Modales/DesgloseTicket.php","id="+id,function(data){
        $("#FormCancelacion").html(data);
        $("#TituloCancelacion").html("Desglose del ticket");
        $("#Di3").removeClass("modal-dialog modal-lg modal-notify modal-info");
        $("#Di3").removeClass("modal-dialog modal-xl modal-notify modal-success");
        $("#Di3").addClass("modal-dialog modal-xl modal-notify modal-primary");
        var modal_lv = 0;
          $('.modal').on('shown.bs.modal', function (e) {
            $('.modal-backdrop:last').css('zIndex', 1051 + modal_lv);
            $(e.currentTarget).css('zIndex', 1052 + modal_lv);
            modal_lv++
          });

          $('.modal').on('hidden.bs.modal', function (e) {
            modal_lv--
          });
    });
    $('#Cancelacionmodal').modal('show');
});

$(".btn-Reimpresion").click(function(){
    id = $(this).data("id");
    $.post("https://controlfarmacia.com/POS2/Modales/ReimpresionTicketVenta.php","id="+id,function(data){
        $("#FormCancelacion").html(data);
        $("#TituloCancelacion").html("Editar datos de categoría");
        $("#Di3").removeClass("modal-dialog modal-lg modal-notify modal-info");
        $("#Di3").removeClass("modal-dialog modal-xl modal-notify modal-primary");
        $("#Di3").addClass("modal-dialog modal-xl modal-notify modal-success");
        var modal_lv = 0;
          $('.modal').on('shown.bs.modal', function (e) {
            $('.modal-backdrop:last').css('zIndex', 1051 + modal_lv);
            $(e.currentTarget).css('zIndex', 1052 + modal_lv);
            modal_lv++
          });

          $('.modal').on('hidden.bs.modal', function (e) {
            modal_lv--
          });
    });
    $('#Cancelacionmodal').modal('show');
});
</script>
<?php

function fechaCastellano ($fecha) {
  $fecha = substr($fecha, 0, 10);
  $numeroDia = date('d', strtotime($fecha));
  $dia = date('l', strtotime($fecha));
  $mes = date('F', strtotime($fecha));
  $anio = date('Y', strtotime($fecha));
  $dias_ES = array("Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado", "Domingo");
  $dias_EN = array("Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday");
  $nombredia = str_replace($dias_EN, $dias_ES, $dia);
$meses_ES = array("Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");
  $meses_EN = array("January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
  $nombreMes = str_replace($meses_EN, $meses_ES, $mes);
  return $nombredia." ".$numeroDia." de ".$nombreMes." de ".$anio;
}
?>
 <div class="modal fade" id="Cancelacionmodal" tabindex="-2" role="dialog" style="overflow-y: scroll;" aria-labelledby="CancelacionmodalLabel" aria-hidden="true">
  <div id="Di3" class="modal-dialog modal-lg modal-notify modal-info">
      <div class="modal-content">
      <div class="modal-header">
         <p class="heading lead" id="TituloCancelacion">Confirmacion de ticket</p>
         <button type="button" class="close" data-dismiss="modal" aria-label="Close">
           <span aria-hidden="true" class="white-text">&times;</span>
         </button>
       </div>
       
	        <div class="modal-body">
          <div class="text-center">
        <div id="FormCancelacion"></div>
        
        </div>

      </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
  </div><!-- /.modal -->