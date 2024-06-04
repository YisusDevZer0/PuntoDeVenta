<?php
include "../Controladores/db_connect.php.php";
include "../Controladores/ControladorUsuario.php";

$user_id = null;
$sql1 = "SELECT * FROM Recordatorios_Pendientes WHERE ID_Notificacion= " . $_POST["id"];
$query = $conn->query($sql1);
$Especialistas = $query->fetch_object();
?>

<?php if ($Especialistas) : ?>
    <form action="javascript:void(0)" method="post" id="EliminaServiciosForm">
        <i id="lockIcon" class="fas fa-unlock fa-5x text-success"></i>
        <p>¿Estas seguro que deseas marcar como leido el siguiente mensaje? <?php echo $Especialistas->Mensaje_Recordatorio; ?></p>
        <input type="hidden" name="ID_Notificacion" id="ID_Notificacion" value="<?php echo $Especialistas->ID_Notificacion; ?>">
        <input type="hidden" name="Estatus" id="Estatus" value="2">
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