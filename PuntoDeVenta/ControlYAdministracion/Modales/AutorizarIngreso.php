<?php
include "../Controladores/db_connect.php";
include "../Controladores/ControladorUsuario.php";

// Validar y sanitizar el ID
$id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);

if ($id) {
    // Preparar la consulta para evitar inyecciones SQL
    $stmt = $conn->prepare("SELECT * FROM  Solicitudes_Ingresos WHERE IdProdCedis = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $Especialistas = $stmt->get_result()->fetch_object();
    $stmt->close();
}

?>

<?php if ($Especialistas) : ?>
    <form action="javascript:void(0)" method="post" id="EliminaServiciosForm" class="mb-3">

      

        <!-- Mantén el input oculto con el ID_Caja -->
        <input type="hidden" name="Fk_Caja" id="ID_Caja" value="<?php echo htmlspecialchars($Especialistas->ID_Caja, ENT_QUOTES, 'UTF-8'); ?>">
        <input type="hidden" name="Empleado" id="empleado" value="<?php echo htmlspecialchars($row['Nombre_Apellidos'], ENT_QUOTES, 'UTF-8'); ?>">
        <input type="hidden" name="AgregadoPor" id="AgregadoPor" value="<?php echo htmlspecialchars($row['Nombre_Apellidos'], ENT_QUOTES, 'UTF-8'); ?>">
        <input type="hidden" name="Fk_sucursal" id="sucursal" value="<?php echo htmlspecialchars($row['Fk_Sucursal'], ENT_QUOTES, 'UTF-8'); ?>">
        <input type="hidden" name="Sistema" id="licencia" value="Administrador">
        <input type="hidden" name="Licencia" id="licencia" value="<?php echo htmlspecialchars($row['Licencia'], ENT_QUOTES, 'UTF-8'); ?>">

        <button type="submit" class="btn btn-primary">Enviar</button>
    </form>

    <script src="js/RegistraElNuevoGasto.js"></script>

<?php else : ?>
    <p class="alert alert-danger">404 No se encuentra</p>
<?php endif; ?>
