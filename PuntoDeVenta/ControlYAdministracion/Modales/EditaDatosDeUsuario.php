<?php
include "../Controladores/db_connect.php.php";
include "../Controladores/ControladorUsuario.php";

$user_id=null;
$sql1= "SELECT Usuarios_PV.Id_PvUser, Usuarios_PV.Nombre_Apellidos, Usuarios_PV.file_name, 
Usuarios_PV.Fk_Usuario, Usuarios_PV.Fecha_Nacimiento, Usuarios_PV.Correo_Electronico, 
Usuarios_PV.Telefono, Usuarios_PV.AgregadoPor, Usuarios_PV.AgregadoEl, Usuarios_PV.Estatus,
Usuarios_PV.Licencia, Tipos_Usuarios.ID_User, Tipos_Usuarios.TipoUsuario,
Sucursales.ID_Sucursal, Sucursales.Nombre_Sucursal 
FROM Usuarios_PV 
INNER JOIN Tipos_Usuarios ON Usuarios_PV.Fk_Usuario = Tipos_Usuarios.ID_User 
INNER JOIN Sucursales ON Usuarios_PV.Fk_Sucursal = Sucursales.ID_Sucursal AND Usuarios_PV.Id_PvUser = ".$_POST["id"];
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

<form action="javascript:void(0)" method="post" id="ActualizaDatosDelUsuario" >
<div class="container">
    <div class="row">
        <!-- Primera columna -->
        <div class="col-md-4">
            <div class="form-group">
                <label for="exampleFormControlInput1"># de empleado</label>
                <div class="input-group mb-3">
                    <input type="text" class="form-control" disabled readonly value="<?php echo $Especialistas->Id_PvUser; ?>">
                </div>
            </div>
        </div>

        <!-- Segunda columna -->
        <div class="col-md-4">
            <div class="form-group">
                <label for="exampleFormControlInput1">Nombre del servicio <span class="text-danger">*</span></label>
                <div class="input-group mb-3">
                    <input type="text" class="form-control" id="actnomserv" name="NombreEmpleado" value="<?php echo $Especialistas->Nombre_Apellidos; ?>" aria-describedby="basic-addon1" maxlength="60">            
                </div>
            </div>
        </div>

        <!-- Tercera columna -->
        <div class="col-md-4">
            <div class="form-group">
                <label for="exampleFormControlInput1">Sucursal</label>
                <div class="input-group mb-3">
                <select id = "sucursal" class = "form-control" name = "Sucursal">
                                               <option value="<?php echo $Especialistas->Nombre_Sucursal; ?>"><?php echo $Especialistas->Nombre_Sucursal; ?></option>
        <?php
          $query = $conn -> query ("SELECT * FROM Sucursales");
          while ($valores = mysqli_fetch_array($query)) {
            echo '<option value="'.$valores["ID_Sucursal"].'">'.$valores["Nombre_Sucursal"].'</option>';
          }
        ?>  </select>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
        <!-- Primera columna -->
        <div class="col-md-4">
            <div class="form-group">
                <label for="exampleFormControlInput1">Correo</label>
                <div class="input-group mb-3">
                    <input type="text" class="form-control" name="Correo" value="<?php echo $Especialistas->Correo_Electronico; ?>">
                </div>
            </div>
        </div>

        <!-- Segunda columna -->
        <div class="col-md-4">
            <div class="form-group">
                <label for="exampleFormControlInput1">Contraseña <span class="text-danger">*</span></label>
                <div class="input-group mb-3">
                    <input type="text" class="form-control" id="contra" name="Contra" value="<?php echo $Especialistas->Nombre_Apellidos; ?>" aria-describedby="basic-addon1" maxlength="60">            
                </div>
            </div>
        </div>

        <!-- Tercera columna -->
        <div class="col-md-4">
            <div class="form-group">
                <label for="exampleFormControlInput1">Tipo de usuario</label>
                <div class="input-group mb-3">
                <select id = "proveedor2" class = "form-control" name = "TiposUsuarios">
                                               <option value="<?php echo $Especialistas->ID_User; ?>"><?php echo $Especialistas->TipoUsuario; ?></option>
        <?php
          $query = $conn -> query ("SELECT * FROM Tipos_Usuarios");
          while ($valores = mysqli_fetch_array($query)) {
            echo '<option value="'.$valores["ID_User"].'">'.$valores["Tipos_Usuarios"].'</option>';
          }
        ?>  </select>
                </div>
            </div>
        </div>
    </div>
</div>
   
<div class="row">
        <!-- Primera columna -->
        <div class="col-md-4">
            <div class="form-group">
                <label for="exampleFormControlInput1">Telefono</label>
                <div class="input-group mb-3">
                    <input type="text" class="form-control" name="Telefono" value="<?php echo $Especialistas->Telefono; ?>">
                </div>
            </div>
        </div>

        <!-- Segunda columna -->
        <div class="col-md-4">
            <div class="form-group">
                <label for="exampleFormControlInput1">Fecha nacimiento <span class="text-danger">*</span></label>
                <div class="input-group mb-3">
                    <input type="text" class="form-control" id="contra" name="Fechana" value="<?php echo $Especialistas->Fecha_Nacimiento; ?>" aria-describedby="basic-addon1" maxlength="60">            
                </div>
            </div>
        </div>

        <!-- Tercera columna -->
        <div class="col-md-4">
            <div class="form-group">
                <label for="exampleFormControlInput1">Estado</label>
                <div class="input-group mb-3">
                <select id = "estado" class = "form-control" name = "Estado">
                                               <option value="<?php echo $Especialistas->Estatus; ?>"><?php echo $Especialistas->Estatus; ?></option>
        <option value="Baja">Baja</option>
        <option value="Baja Temporal">Baja Temporal</option>
        <option value="Activo">Activo</option>
          </select>
                </div>
            </div>
        </div>
    </div>
</div>
    <input type="text" class="form-control " hidden  readonly id="actusuariocserv" name="Actualiza" readonly value="<?php echo $row['Nombre_Apellidos']?>">

<input type="hidden" name="Id_Serv" id="id" value="<?php echo $Especialistas->Id_PvUser; ?>">
<button type="submit"  id="submit"  class="btn btn-info">Aplicar cambios <i class="fas fa-check"></i></button>
                          
</form>
<script src="js/ActualizaDatosUsuario.js"></script>

<?php else:?>
  <p class="alert alert-danger">404 No se encuentra</p>
<?php endif;?>
