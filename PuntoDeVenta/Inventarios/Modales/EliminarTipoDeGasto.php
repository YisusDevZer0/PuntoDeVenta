<?php
include "../Controladores/db_connect.php.php";
include "../Controladores/ControladorUsuario.php";

$user_id=null;
$sql1= "SELECT * FROM TiposDeGastos WHERE Licencia='".$row['Licencia']."' AND Gasto_ID = ".$_POST["id"];
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
        <p>¿Está seguro de que desea eliminar el tipo de gasto <?php echo $Especialistas->Nom_Gasto; ?> ?</p>
<input type="hidden" name="Id_Serv" id="id" value="<?php echo $Especialistas->Gasto_ID; ?>">
<button type="submit"  id="submit"  class="btn btn-danger">Confirmar<i class="fas fa-check"></i></button>
                          
</form>
<script src="js/EliminarTiposDeGastos.js"></script>

<?php else:?>
  <p class="alert alert-danger">404 No se encuentra</p>
<?php endif;?>
