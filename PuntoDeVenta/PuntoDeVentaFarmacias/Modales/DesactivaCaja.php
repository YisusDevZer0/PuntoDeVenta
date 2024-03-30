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
    <?php
    $iconClass = ($Especialistas->Estatus == 'Activo') ? 'unlock' : 'lock';
    $text = ($Especialistas->Estatus == 'Activo') ? 'Inactivar' : 'Activar';
    ?>
    <i id="lockIcon" class="fas fa-<?php echo $iconClass; ?> fa-5x text-<?php echo ($Especialistas->Estatus == 'Activo') ? 'success' : 'danger'; ?>"></i>
    <p>¿Está seguro de que desea <?php echo $text; ?> la caja del cajero <?php echo $Especialistas->Empleado; ?> con el turno <?php echo $Especialistas->Turno; ?>, aperturada el día <?php echo $Especialistas->Fecha_Apertura; ?>?</p>
    <input type="hidden" name="Id_Serv" id="id" value="<?php echo $Especialistas->ID_Caja; ?>">
    <button type="submit" id="submit" class="btn btn-<?php echo ($Especialistas->Estatus == 'Activo') ? 'danger' : 'success'; ?>">
        <?php echo $text; ?><i class="fas fa-<?php echo $iconClass; ?>"></i>
    </button>
</form>
<script src="js/DesactivaLaCaja.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        var lockIcon = document.getElementById("lockIcon");
        var button = document.getElementById("submit");
        lockIcon.classList.remove("fa-<?php echo $iconClass; ?>");
        lockIcon.classList.add("fa-lock");
        lockIcon.classList.remove("text-<?php echo ($Especialistas->Estatus == 'Activo') ? 'success' : 'danger'; ?>");
        lockIcon.classList.add("text-danger");
        button.addEventListener("click", function() {
            lockIcon.classList.remove("fa-unlock");
            lockIcon.classList.add("fa-lock");
            lockIcon.classList.remove("text-success");
            lockIcon.classList.add("text-danger");
        });
    });
</script>
<?php else : ?>
    <p class="alert alert-danger">404 No se encuentra</p>
<?php endif; ?>
