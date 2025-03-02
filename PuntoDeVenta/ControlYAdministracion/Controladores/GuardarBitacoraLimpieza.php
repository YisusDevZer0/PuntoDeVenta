<?php
include "db_connect.php";
include "ControladorUsuario.php";

// Obtener datos del formulario
$area = $_POST['area'];
$semana = $_POST['semana'];
$fecha_inicio = $_POST['fecha_inicio'];
$fecha_fin = $_POST['fecha_fin'];
$responsable = $_POST['responsable'];
$supervisor = $_POST['supervisor'];
$aux_res = $_POST['aux_res'];

// Insertar datos en la tabla Bitacora_Limpieza
$sql = "INSERT INTO Bitacora_Limpieza (area, semana, fecha_inicio, fecha_fin, responsable, supervisor, aux_res)
        VALUES ('$area', '$semana', '$fecha_inicio', '$fecha_fin', '$responsable', '$supervisor', '$aux_res')";
if ($conn->query($sql) {
    $id_bitacora = $conn->insert_id; // Obtener el ID de la bitácora recién insertada

    // Insertar detalles de limpieza
    foreach ($_POST['elemento'] as $index => $elemento) {
        $lunes_mat = isset($_POST['lunes_mat'][$index]) ? 1 : 0;
        $lunes_vesp = isset($_POST['lunes_vesp'][$index]) ? 1 : 0;
        // Repetir para los demás días...

        $sql_detalle = "INSERT INTO Detalle_Limpieza (id_bitacora, elemento, lunes_mat, lunes_vesp, ...)
                        VALUES ('$id_bitacora', '$elemento', '$lunes_mat', '$lunes_vesp', ...)";
        $conn->query($sql_detalle);
    }

    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false]);
}

$conn->close();
?>