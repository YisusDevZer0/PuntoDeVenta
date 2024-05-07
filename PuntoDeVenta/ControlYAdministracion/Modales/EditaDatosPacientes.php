<?php
include "../Controladores/db_connect.php.php";
include "../Controladores/ControladorUsuario.php";

$user_id=null;
$sql1= "SELECT dp.ID_Data_Paciente, 
dp.Nombre_Paciente, 
dp.Edad, 
dp.Sexo, 
dp.Telefono, 
dp.Fecha_Nacimiento, 
dp.Fk_Sucursal, 
dp.SucursalVisita,
s.ID_Sucursal, 
s.Nombre_Sucursal
FROM Data_Pacientes dp
INNER JOIN Sucursales s ON dp.Fk_Sucursal = s.ID_Sucursal AND
dp.ID_Data_Paciente= ".$_POST["id"];
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
    <label for="exampleFormControlInput1">Folio</label>
    <div class="input-group mb-3">
  
  <input type="text" class="form-control " disabled readonly value="<?php echo $Especialistas->ID_Data_Paciente; ?>">
    </div>
    </div>
    
   
    <div class="form-group">
    <label for="exampleFormControlInput1">Nombre del servicio<span class="text-danger">*</span></label>
     <div class="input-group mb-3">
  
  <input type="text" class="form-control "  id="actnomserv" name="ActNomServ" value="<?php echo $Especialistas->Nombre_Paciente; ?>" aria-describedby="basic-addon1" maxlength="60">            
</div></div>
<div class="form-group">
    <label for="exampleFormControlInput1">Fecha de nacimiento <span class="text-danger">*</span></label>
     <div class="input-group mb-3">
  
  <input type="date" class="form-control "  id="actfechanac" name="ActFechaNac" value="<?php echo $Especialistas->Fecha_Nacimiento; ?>" aria-describedby="basic-addon1" maxlength="60">            
</div></div>

<div class="form-group">
    <label for="exampleFormControlInput1">Edad <span class="text-danger">*</span></label>
     <div class="input-group mb-3">
  
  <input type="text" class="form-control "  id="actedad" name="ActEdad" value="<?php echo $Especialistas->Edad; ?>" aria-describedby="basic-addon1" maxlength="60">            
</div></div>
</div>

    
</div>
    </div>
   
    <input type="text" class="form-control " hidden  readonly id="actusuariocserv" name="ActUsuarioCServ" readonly value="<?php echo $row['Nombre_Apellidos']?>">
<input type="text" class="form-control "  hidden  readonly id="actsistemacserv" name="ActSistemaCServ" readonly value="Administrador">
<input type="hidden" name="Id_Serv" id="id" value="<?php echo $Especialistas->ID_Data_Paciente; ?>">
<button type="submit"  id="submit"  class="btn btn-info">Aplicar cambios <i class="fas fa-check"></i></button>
                          
</form>
<script src="js/ActualizacionDeMarcas.js"></script>

<?php else:?>
  <p class="alert alert-danger">404 No se encuentra</p>
<?php endif;?>
<script>
    // Función para calcular la edad
    function calcularEdad() {
        // Obtener la fecha de nacimiento del input
        var fechaNacimiento = new Date(document.getElementById('actfechanac').value);
        // Obtener la fecha actual
        var fechaActual = new Date();
        
        // Calcular la diferencia en milisegundos
        var diferencia = fechaActual - fechaNacimiento;
        
        // Calcular la edad en años
        var edad = Math.floor(diferencia / (1000 * 60 * 60 * 24 * 365.25));
        
        // Actualizar el valor del input de edad
        document.getElementById('actedad').value = edad;
    }

    // Llamar a la función calcularEdad cada vez que se abra el modal
    $('#ModalEdDele').on('show.bs.modal', function (e) {
        calcularEdad();
    });
</script>
