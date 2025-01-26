<?php
include "../Controladores/db_connect.php.php";
include "../Controladores/ControladorUsuario.php";

$user_id=null;
$sql1= "SELECT Stock_POS.Folio_Prod_Stock,Stock_POS.Clave_adicional,Stock_POS.ID_Prod_POS,Stock_POS.AgregadoEl,Stock_POS.Clave_adicional,Stock_POS.Clave_Levic,
Stock_POS.Cod_Barra,Stock_POS.Nombre_Prod,Stock_POS.Tipo_Servicio,Stock_POS.Tipo,Stock_POS.Fk_sucursal,
Stock_POS.Max_Existencia,Stock_POS.Min_Existencia, Stock_POS.Existencias_R,Stock_POS.Proveedor1,
Stock_POS.Proveedor2,Stock_POS.Estatus,Stock_POS.ID_H_O_D, Sucursales.ID_Sucursal,
Sucursales.Nombre_Sucursal,Servicios_POS.Servicio_ID,Servicios_POS.Nom_Serv, Productos_POS.ID_Prod_POS,
Productos_POS.Precio_Venta,Productos_POS.Precio_C 
FROM Stock_POS
INNER JOIN Sucursales ON Stock_POS.Fk_sucursal = Sucursales.ID_Sucursal
INNER JOIN Servicios_POS ON Stock_POS.Tipo_Servicio= Servicios_POS.Servicio_ID
INNER JOIN Productos_POS ON Productos_POS.ID_Prod_POS =Stock_POS.ID_Prod_POS WHERE Stock_POS.Folio_Prod_Stock=".$_POST["id"];
$query = $conn->query($sql1);
$Especialistas = null;
if($query->num_rows>0){
while ($r=$query->fetch_object()){
  $Especialistas=$r;
  break;
}

  }
?>

<?php if($Especialistas!=null):?>

<form action="javascript:void(0)" method="post" id="ActualizaServicios" >
<div class="form-group">
    <label for="exampleFormControlInput1">Codigo de barras</label>
    <div class="input-group mb-3">
  
  <input type="text" class="form-control " disabled readonly value="<?php echo $Especialistas->Cod_Barra; ?>">
    </div>
    </div>
    
    <div class="form-group">
    <label for="exampleFormControlInput1">Maximo<span class="text-danger">*</span></label>
     <div class="input-group mb-3">
  
  <input type="text" class="form-control "  id="actnomserv" name="ActNomServ" value="<?php echo $Especialistas->Max_Existencia; ?>" aria-describedby="basic-addon1" maxlength="60">            
</div></div></div>

<div class="form-group">
    <label for="exampleFormControlInput1">Minimo<span class="text-danger">*</span></label>
     <div class="input-group mb-3">
  
  <input type="text" class="form-control "  id="actnomserv" name="ActNomServ" value="<?php echo $Especialistas->Min_Existencia; ?>" aria-describedby="basic-addon1" maxlength="60">            
</div></div></div>
</div>
    </div>
   
    <input type="text" class="form-control " hidden  readonly id="actusuariocserv" name="ActUsuarioCServ" readonly value="<?php echo $row['Nombre_Apellidos']?>">
<input type="text" class="form-control "  hidden  readonly id="actsistemacserv" name="ActSistemaCServ" readonly value="Administrador">
<input type="hidden" name="Id_Serv" id="id" value="<?php echo $Especialistas->Marca_ID; ?>">
<button type="submit"  id="submit"  class="btn btn-info">Aplicar cambios <i class="fas fa-check"></i></button>
                          
</form>
<script src="js/ActualizacionDeMarcas.js"></script>

<?php else:?>
  <p class="alert alert-danger">404 No se encuentra</p>
<?php endif;?>
