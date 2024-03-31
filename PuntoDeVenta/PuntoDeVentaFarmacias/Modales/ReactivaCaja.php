<?php
include "../Controladores/db_connect.php.php";
include "../Controladores/ControladorUsuario.php";

$user_id = null;
$sql1 = "SELECT * FROM Cajas WHERE ID_Caja= " . $_POST["id"];
$query = $conn->query($sql1);
$Especialistas = $query->fetch_object();
?>

<?php if ($Especialistas) : ?>
<form action="javascript:void(0)" method="post" id="ReactivaCajaForm">
    <i id="lockIcon" class="fas fa-lock fa-5x text-danger"></i>
    <p>La caja con el turno <?php echo $Especialistas->Turno; ?>, aperturada el día <?php echo $Especialistas->Fecha_Apertura; ?> será desbloqueada. ¿Estás seguro que deseas desactivarla?</p>
    <input type="hidden" name="ID_Caja" id="ID_Caja" value="<?php echo $Especialistas->ID_Caja; ?>">
    <input type="hidden" name="Estatus" id="Estatus" value="1">
    <button type="submit" id="submit" class="btn btn-success">
        Desbloquear<i class="fas fa-unlock"></i>
    </button>
</form>
<script src="js/DesbloqueaLaCaja.js"></script>
<script>
document.getElementById("submit").addEventListener("click", function() {
    var lockIcon = document.getElementById("lockIcon");
    lockIcon.classList.remove("fa-lock");
    lockIcon.classList.add("fa-unlock");
    lockIcon.classList.remove("text-danger");
    lockIcon.classList.add("text-success");
});
</script>
<?php else : ?>
    <p class="alert alert-danger">404 No se encuentra</p>
<?php endif; ?>
