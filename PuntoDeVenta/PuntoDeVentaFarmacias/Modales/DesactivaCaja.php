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
    <i id="lockIcon" class="fas fa-unlock fa-5x text-success"></i>
    <p>¿Está seguro de que desea bloquear la caja con el turno <?php echo $Especialistas->Turno; ?>, aperturada el día <?php echo $Especialistas->Fecha_Apertura; ?>?</p>
    <input type="hidden" name="ID_Caja" id="ID_Caja" value="<?php echo $Especialistas->ID_Caja; ?>">
    <input type="hidden" name="Estatus" id="Estatus" value="Inactiva">
    <button type="submit" id="submit" class="btn btn-danger">
        Bloquear<i class="fas fa-lock"></i>
    </button>
</form>
<script src="js/DesactivaLaCaja.js"></script>
<script>
document.getElementById("submit").addEventListener("click", function() {
    var lockIcon = document.getElementById("lockIcon");
    lockIcon.classList.remove("fa-unlock");
    lockIcon.classList.add("fa-lock");
    lockIcon.classList.remove("text-success");
    lockIcon.classList.add("text-danger");
});
</script>
<?php else : ?>
    <p class="alert alert-danger">404 No se encuentra</p>
<?php endif; ?>
