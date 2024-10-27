<?php
include "../Controladores/db_connect.php";
include "../Controladores/ControladorUsuario.php";


$fcha = date("Y-m-d");
$user_id=null;
$sql1= "SELECT 
    Ventas_POS.Folio_Ticket,
    Ventas_POS.Fk_Caja,
    Ventas_POS.Venta_POS_ID,
    Ventas_POS.Identificador_tipo,
    Ventas_POS.Cod_Barra,
    Ventas_POS.FormaDePago,
    Ventas_POS.Fecha_venta,
    Ventas_POS.Clave_adicional,
    Ventas_POS.Total_Venta,
    Ventas_POS.Importe,
    Ventas_POS.Total_VentaG,
    Ventas_POS.CantidadPago,
    Ventas_POS.Cambio,
    Servicios_POS.Servicio_ID,
    Servicios_POS.Nom_Serv,
    Ventas_POS.Nombre_Prod,
    Ventas_POS.Cantidad_Venta,
    Ventas_POS.Fk_sucursal,
    Ventas_POS.AgregadoPor,
    Ventas_POS.AgregadoEl,
    Ventas_POS.Lote,
    Ventas_POS.ID_H_O_D,
    Sucursales.ID_Sucursal,
    Sucursales.Nombre_Sucursal
FROM 
    Ventas_POS
JOIN 
    Sucursales ON Ventas_POS.Fk_sucursal = Sucursales.ID_Sucursal
LEFT JOIN 
    Servicios_POS ON Ventas_POS.Identificador_tipo = Servicios_POS.Servicio_ID
WHERE 
    Ventas_POS.Folio_Ticket = '".$_POST["id"]."'";

$query = $conn->query($sql1);
$Especialistas = null;
if($query->num_rows>0){
while ($r=$query->fetch_object()){
  $Especialistas=$r;
  break;
}

  }
  $user_id=null;
  $sql2= "SELECT 
    Ventas_POS.Folio_Ticket,
    Ventas_POS.Fk_Caja,
    Ventas_POS.Venta_POS_ID,
    Ventas_POS.Identificador_tipo,
    Ventas_POS.Cod_Barra,
    Ventas_POS.FormaDePago,
    Ventas_POS.Fecha_venta,
    Ventas_POS.Clave_adicional,
    Ventas_POS.Total_Venta,
    Ventas_POS.Importe,
    Ventas_POS.Total_VentaG,
    Ventas_POS.CantidadPago,
    Ventas_POS.Cambio,
    Servicios_POS.Servicio_ID,
    Servicios_POS.Nom_Serv,
    Ventas_POS.Nombre_Prod,
    Ventas_POS.Cantidad_Venta,
    Ventas_POS.Fk_sucursal,
    Ventas_POS.AgregadoPor,
    Ventas_POS.AgregadoEl,
    Ventas_POS.Lote,
    Ventas_POS.ID_H_O_D,
    Sucursales.ID_Sucursal,
    Sucursales.Nombre_Sucursal
FROM 
    Ventas_POS
JOIN 
    Sucursales ON Ventas_POS.Fk_sucursal = Sucursales.ID_Sucursal
LEFT JOIN 
    Servicios_POS ON Ventas_POS.Identificador_tipo = Servicios_POS.Servicio_ID
WHERE 
    Ventas_POS.Folio_Ticket = '".$_POST["id"]."'";
   $query = $conn->query($sql2);
?>



<?php if($Especialistas!=null):?>
    
<div id="advertencia" class="alert alert-warning">
    <strong>Advertencia:</strong> Antes de eliminar, por favor valide los datos del ticket <?php echo $Especialistas->Folio_Ticket; ?>
    <button id="validarBtn" class="btn btn-primary btn-sm mt-2">Ticket Validado</button>



<!-- Aquí va el resto de tu formulario y tabla -->

<script>
    document.getElementById("validarBtn").addEventListener("click", function() {
        // Oculta el mensaje de advertencia y muestra el de confirmación
        document.getElementById("advertencia").style.display = "none";
        document.getElementById("confirmacionEliminar").style.display = "block";
    });

    document.getElementById("eliminarBtn").addEventListener("click", function() {
        // Lógica de eliminación (puedes llamar una función AJAX aquí si es necesario)
        alert("Ticket " + "<?php echo $Especialistas->Folio_Ticket; ?>" + " eliminado.");
    });
</script>
<div id="advertencia">
    <div class="row">
    <div class="col">
    <label for="exampleFormControlInput1">N° Ticket</label>
    <div class="input-group mb-3">
  
  <input type="text" class="form-control" readonly name="TicketVal"value="<?php echo $Especialistas->Folio_Ticket; ?>">
    </div>
    </div>
    <div class="col">
    <label for="exampleFormControlInput1">Sucursal </label>
    <div class="input-group mb-3">
  
  <input type="text" class="form-control" readonly value="<?php echo $Especialistas->Nombre_Sucursal; ?>">
    </div>
    </div>
    <div class="col">
    <label for="exampleFormControlInput1">Vendedor</label>
    <div class="input-group mb-3">
  
  <input type="text" class="form-control" readonly value="<?php echo $Especialistas->	AgregadoPor; ?>">
    </div>
    </div>
   </div>

<?php if($query->num_rows>0):?>
  <div class="text-center">
	<div class="table-responsive">
	<table  id="HistorialCajas" class="table table-hover">
<thead>

<th>Servicio</th>
<th>Cod barra</th>
<th>Prod</th>
<th>Cantidad</th>
<th>P.U</th>
<th>Descuento</th>
<th>Importe</th>

    



</thead>
<?php while ($Tickets=$query->fetch_array()):?>
<tr>

<td><input type="text" class="form-control" readonly value=<?php echo $Tickets["Nom_Serv"]; ?>></td>
    
<td><input type="text" class="form-control" readonly value=<?php echo $Tickets["Cod_Barra"]; ?>></td>
<td><textarea  type="text" class="form-control" name="NombreProd[]" readonly ><?php echo $Tickets["Nombre_Prod"]; ?></textarea></td>
<td><input type="text" class="form-control" name="CantidadTotal[]"readonly value=<?php echo $Tickets["Cantidad_Venta"]; ?>></td>
<td><input type="text" class="form-control" readonly name="pro_cantidad[]"value=<?php echo $Tickets["Total_Venta"]; ?>></td>
<td><input type="text" class="form-control"  readonly name="Descuento[]"value="<?php echo $Tickets["DescuentoAplicado"]; ?> %"></td>
<td><input type="text" class="form-control" name="ImporteT[]" readonly value=<?php echo $Tickets["Importe"]; ?>>
<input type="text" class="form-control" hidden readonly name="Vendedor[]"value=<?php echo $Tickets["AgregadoPor"]; ?>>
  <input type="text" class="form-control"  hidden readonly name="Horadeimpresion"value=<?php  echo date('h:i:s A'); ?>>
  <input type="text" class="form-control" hidden readonly value=<?php echo fechaCastellano($Tickets["AgregadoEl"]); ?>></td>
   
 
 
     
      
   
</tr>
<?php endwhile;?>
</table>
</div>
</div>

<div class="row">
    <div class="col">
    <label for="exampleFormControlInput1">Total de venta</label>
    <div class="input-group mb-3">
  
  <input type="text" class="form-control" readonly name="TotalVentas[]" value="<?php echo $Especialistas->Total_VentaG; ?>">
  
    </div>
    </div>
    <div class="col">
    <label for="exampleFormControlInput1">El cliente pago </label>
    <div class="input-group mb-3">
  
  <input type="text" class="form-control" readonly name="PagoReal[]" value="<?php echo $Especialistas->CantidadPago; ?>">
    </div>
    </div>
    <div class="col">
    <label for="exampleFormControlInput1">Cambio</label>
    <div class="input-group mb-3">
  
  <input type="text" class="form-control" readonly name="Cambio[]"value="<?php echo $Especialistas->Cambio; ?>">
    </div>
    </div>
    <div class="col">
    <label for="exampleFormControlInput1">Forma de pago</label>
    <div class="input-group mb-3">
  
  <input type="text" class="form-control" name="Formadepago" readonly value="<?php echo $Especialistas->FormaDePago; ?>">
    </div>
    </div>
   </div>
</div>

</div>

<div id="confirmacionEliminar" class="alert alert-danger mt-3" style="display:none;">
    ¿Está seguro que desea eliminar el ticket <strong><?php echo $Especialistas->Folio_Ticket; ?></strong>?
    <button id="eliminarBtn" class="btn btn-danger btn-sm">Eliminar Ticket</button>
</div>



<?php else:?>
	<p class="alert alert-warning">No hay resultados</p>
<?php endif;?>

  
<?php else:?>
  <p class="alert alert-danger">404 No se encuentra  <br>El ticket puede corresponder a un crédito, te sugerimos revisar el área de créditos  </p>
<?php endif;?>
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