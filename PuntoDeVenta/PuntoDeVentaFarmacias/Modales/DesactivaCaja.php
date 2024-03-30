<?php
include "../Controladores/db_connect.php.php";
include "../Controladores/ControladorUsuario.php";

$user_id = null;
$sql1 = "SELECT * FROM Cajas WHERE Licencia='" . $row['Licencia'] . "' AND ID_Caja= " . $_POST["id"];
$query = $conn->query($sql1);
$Especialistas = null;
if ($query->num_rows > 0) {
    while ($r = $query->fetch_object()) {
        $Especialistas = $r;
        break;
    }
}
?>

<?php if ($Especialistas != null) : ?>

<form action="javascript:void(0)" method="post" id="EliminaServiciosForm">
    <?php if ($Especialistas->Estado == 'Abierto') : ?>
        <i class="fas fa-lock-open fa-5x text-success"></i>
    <?php else : ?>
        <i class="fas fa-lock fa-5x text-danger"></i>
    <?php endif; ?>
    <p>
        <?php if ($Especialistas->Estado == 'Abierto') : ?>
            ¿Está seguro de que desea desactivar la caja con el turno <?php echo $Especialistas->Turno; ?>, aperturada el día
            <?php echo $Especialistas->Fecha_Apertura; ?> ?
        <?php else : ?>
            ¿Está seguro de que desea activar la caja con el turno <?php echo $Especialistas->Turno; ?>, aperturada el día
            <?php echo $Especialistas->Fecha_Apertura; ?> ?
        <?php endif; ?>
    </p>
    <input type="hidden" name="Id_Serv" id="id" value="<?php echo $Especialistas->ID_Caja; ?>">
    <button type="submit" id="submit" class="btn btn-<?php echo $Especialistas->Estado == 'Abierto' ? 'danger' : 'success'; ?>">
        <?php echo $Especialistas->Estado == 'Abierto' ? 'Desactivar' : 'Activar'; ?><i class="fas fa-check"></i>
    </button>
</form>
<script src="js/DesactivaLaCaja.js"></script>

<?php else : ?>
    <p class="alert alert-danger">404 No se encuentra</p>
<?php endif; ?>
