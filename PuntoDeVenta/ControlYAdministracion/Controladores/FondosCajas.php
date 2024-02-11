<script type="text/javascript">
$(document).ready( function () {
    $('#Empleados').DataTable({
      "order": [[ 0, "desc" ]],
      bFilter: false,
      "info": false,
      "lengthMenu": [[10,50,200, -1], [10,50,200, "Todos"]],   
      "language": {
        "url": "Componentes/Spanish.json"
		},
    initComplete: function() {
        this.api().columns([0]).every(function() {
            var column = this;
            var select = $('<select class="form-control form-control-sm" ><option value="">Filtrar</option></select>')
                .appendTo($(column.header()))
                .on('change', function() {
                    var val = $.fn.dataTable.util.escapeRegex(
                        $(this).val()
                    );
 
                        column
                        .search(val ? '^' + val + '$' : '', true, false)
                        .draw();
 
 
                });
                //Este codigo sirve para que no se active el ordenamiento junto con el filtro
            $(select).click(function(e) {
                e.stopPropagation();
            });
            //===================
 
            column.data().unique().sort().each(function(d, j) {
                // select.append('<option value="' + d + '">' + d + '</option>')
 
                    select.append('<option value="' + d + '">' + d + '</option>')
 
            });
 
 
 
        });
    },
    "aoColumnDefs": [
     { "bSearchable": false, "aTargets": [ 1 ] }
   ]
		
	  } 
	  
	  );
} );
</script>
<?php

include("db_connection.php");
include "ControladorUsuario.php";


$user_id=null;
$sql1="SELECT Fondos_Cajas.ID_Fon_Caja,Fondos_Cajas.Fk_Sucursal,Fondos_Cajas.Fondo_Caja,Fondos_Cajas.ID_H_O_D, Fondos_Cajas.CodigoEstatus,Fondos_Cajas.Estatus, 
SucursalesCorre.ID_SucursalC,SucursalesCorre.Nombre_Sucursal FROM 
Fondos_Cajas,SucursalesCorre where Fondos_Cajas.Fk_Sucursal = SucursalesCorre.ID_SucursalC AND 
Fondos_Cajas.ID_H_O_D ='".$row['ID_H_O_D']."'";
$query = $conn->query($sql1);
?>

<?php if($query->num_rows>0):?>
  <div class="text-center">
	<div class="table-responsive">
	<table  id="Empleados" class="table table-hover">
<thead>
  <th>Sucursal</th>
<th>Fondo</th>
<th>Estatus</th>
	<th >Accciones</th>
	


</thead>
<?php while ($Usuarios=$query->fetch_array()):?>
<tr>
<td > <?php echo $Usuarios["Nombre_Sucursal"]; ?></td>
    <td>$ <?php echo $Usuarios["Fondo_Caja"]; ?></td>
    <td> <button style="<?echo $Usuarios['CodigoEstatus'];?>" class="btn btn-default btn-sm" > <?php echo $Usuarios["Estatus"]; ?></button></td>
<td>		 <!-- Basic dropdown -->
<button class="btn btn-primary btn-sm dropdown-toggle " type="button" data-toggle="dropdown"
  aria-haspopup="true" aria-expanded="false"><i class="fas fa-th-list fa-1x"></i></button>

<div class="dropdown-menu">

<a data-id="<?php echo $Usuarios["ID_Fon_Caja"];?>" class="btn-edit2 dropdown-item" >Editar datos <i class="fas fa-pencil-alt"></i></a>
  <a data-id="<?php echo $Usuarios["ID_Fon_Caja"];?>" class="btn-detalles dropdown-item" >Detalles <i class="fas fa-info-circle"></i></a>
  <a data-id="<?php echo $Usuarios["ID_Fon_Caja"];?>" class="btn-HistorialFondos dropdown-item" >Movimientos <i class="fas fa-history" aria-hidden="true"></i></a>

 
 
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
  <!-- Modal -->
  <script>
  	
    $(".btn-edit2").click(function(){
  		id = $(this).data("id");
  		$.post("https://controlfarmacia.com/AdminPOS/Modales/EditaFondoCaja.php","id="+id,function(data){
              $("#form-edit").html(data);
              $("#Titulo").html("Editar fondo de caja");
              $("#Di").removeClass("modal-dialog  modal-notify modal-info");
              $("#Di").removeClass("modal-dialog modal-notify modal-danger");
              $("#Di").removeClass("modal-dialog modal-xl modal-notify modal-primary");
              $("#Di").addClass("modal-dialog modal-notify modal-info");
  		});
  		$('#editModal').modal('show');
    });
    $(".btn-detalles").click(function(){
  		id = $(this).data("id");
  		$.post("https://controlfarmacia.com/AdminPOS/Modales/DetallesFondoCaja.php","id="+id,function(data){
              $("#form-edit").html(data);
              $("#Titulo").html("Detalles fondo de caja");
              $("#Di").removeClass("modal-dialog  modal-notify modal-info");
              $("#Di").removeClass("modal-dialog modal-notify modal-danger");
              $("#Di").removeClass("modal-dialog modal-xl modal-notify modal-primary");
              $("#Di").addClass("modal-dialog modal-notify modal-info");
  		});
  		$('#editModal').modal('show');
    });
    $(".btn-HistorialFondos").click(function(){
  		id = $(this).data("id");
  		$.post("https://controlfarmacia.com/AdminPOS/Modales/HistorialFondoCaja.php","id="+id,function(data){
              $("#form-edit").html(data);
              $("#Titulo").html("Movimientos de fondo de caja");
              $("#Di").removeClass("modal-dialog  modal-notify modal-info");
              $("#Di").removeClass("modal-dialog modal-notify modal-danger");
              $("#Di").addClass("modal-dialog modal-xl modal-notify modal-primary");
  		});
  		$('#editModal').modal('show');
    });
  </script>
  <div class="modal fade" id="editModal" tabindex="-1" role="dialog" style="overflow-y: scroll;" aria-labelledby="editModalLabel" aria-hidden="true">
  <div id="Di"class="modal-dialog  modal-notify modal-info">
      <div class="modal-content">
      <div class="modal-header">
         <p class="heading lead" id="Titulo"></p>

         <button type="button" class="close" data-dismiss="modal" aria-label="Close">
           <span aria-hidden="true" class="white-text">&times;</span>
         </button>
       </div>
        <div id="Mensaje "class="alert alert-info alert-styled-left text-blue-800 content-group">
						                <span id="Aviso" class="text-semibold">Estimado usuario, 
                            Verifique los campos antes de realizar alguna accion</span>
						                <button type="button" class="close" data-dismiss="alert">×</button>
                            </div>
	        <div class="modal-body">
          <div class="text-center">
        <div id="form-edit"></div>
        
        </div>

      </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
  </div><!-- /.modal -->