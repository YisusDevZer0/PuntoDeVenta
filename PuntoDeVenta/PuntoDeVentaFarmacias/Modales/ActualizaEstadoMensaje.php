<?php
include "../Controladores/db_connect.php";
include "../Controladores/ControladorUsuario.php";

// Obtener el ID de la notificación desde el POST
$id_notificacion = isset($_POST["id"]) ? $_POST["id"] : null;

// Verificar si el ID de la notificación está definido
if ($id_notificacion) {
    // Consulta para obtener los detalles del mensaje de la notificación
    $sql = "SELECT * FROM Recordatorios_Pendientes WHERE ID_Notificacion= ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_notificacion);
    $stmt->execute();
    $result = $stmt->get_result();
    $especialistas = $result->fetch_object();
    $stmt->close();
}

?>

<?php if (isset($especialistas) && $especialistas) : ?>
    <form action="javascript:void(0)" method="post" id="MarcarLeidoForm">
        <i id="lockIcon" class="fas fa-unlock fa-5x text-success"></i>
        
        <p>¿Estás seguro que deseas marcar como leído el siguiente mensaje?</p>
        <p><strong><?php echo $especialistas->Mensaje_Recordatorio; ?></strong></p>
        <input type="hidden" name="ID_Notificacion" id="ID_Notificacion" value="<?php echo $especialistas->ID_Notificacion; ?>">
        <input type="hidden" name="Estado" id="Estado" value="2">
        <button type="submit" id="submit" class="btn btn-danger">
            Marcar como leído <i class="fas fa-lock"></i>
        </button>
    </form>
    <script src="js/ActualizaElEstadoDelMensaje.js"></script>
  
<?php else : ?>
    <p class="alert alert-danger">404 No se encuentra el mensaje</p>
<?php endif; ?>
