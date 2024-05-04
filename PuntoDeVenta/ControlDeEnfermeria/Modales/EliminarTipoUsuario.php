<?php
include "../Controladores/db_connect.php.php";
include "../Controladores/ControladorUsuario.php";

$user_id=null;
$sql1= "SELECT * FROM Tipos_Usuarios WHERE Licencia='".$row['Licencia']."' AND ID_User = ".$_POST["id"];
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

<form action="javascript:void(0)" method="post" id="EliminaServiciosForm" >
<i class="fas fa-question-circle fa-5x text-warning"></i>
        <p>¿Está seguro de que desea eliminar el servicio <?php echo $Especialistas->TipoUsuario; ?> ?</p>
<input type="hidden" name="Id_Serv" id="id" value="<?php echo $Especialistas->ID_User; ?>">
<button type="submit"  id="submit"  class="btn btn-danger">Confirmar<i class="fas fa-check"></i></button>
                          
</form>
<script src="js/EliminarTiposDeUsuarios.js"></script>

<?php else:?>
  <p class="alert alert-danger">404 No se encuentra</p>
<?php endif;?>
