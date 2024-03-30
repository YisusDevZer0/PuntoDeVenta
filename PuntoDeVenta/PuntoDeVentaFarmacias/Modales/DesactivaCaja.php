<?php
include "../Controladores/db_connect.php.php";
include "../Controladores/ControladorUsuario.php";

$user_id = null;
$sql1 = "SELECT * FROM Cajas WHERE ID_Caja= " . $_POST["id"];
$query = $conn->query($sql1);
$Especialistas = $query->fetch_object();
?>

<?php if ($Especialistas) : ?>
<form action="javascript:void(0)" method="post" id="EliminaServiciosForm">
    <?php $icon = ($Especialistas->Estatus == 'Abierto') ? 'lock-open' : 'lock'; ?>
    <i class="fas fa-<?php echo $icon; ?> fa-5x text-<?php echo ($Especialistas->Estatus == 'Abierto') ? 'success' : 'danger'; ?>"></i>
    <p>¿Está seguro de que desea <?php echo ($Especialistas->Estatus == 'Abierto') ? 'desactivar' : 'activar'; ?> la caja con el turno <?php echo $Especialistas->Turno; ?>, aperturada el día <?php echo $Especialistas->Fecha_Apertura; ?>?</p>
    <input type="hidden" name="Id_Serv" id="id" value="<?php echo $Especialistas->ID_Caja; ?>">
    <button type="submit" id="submit" class="btn btn-<?php echo ($Especialistas->Estatus == 'Abierto') ? 'danger' : 'success'; ?>">
        <?php echo ($Especialistas->Estatus == 'Abierto') ? 'Desactivar' : 'Activar'; ?><i class="fas fa-check"></i>
    </button>
</form>
<script src="js/DesactivaLaCaja.js"></script>

<?php else : ?>
    <p class="alert alert-danger">404 No se encuentra</p>
<?php endif; ?>
