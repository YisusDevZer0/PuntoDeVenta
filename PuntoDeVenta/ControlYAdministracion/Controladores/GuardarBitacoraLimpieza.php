<?php
include "db_connect.php";
include "ControladorUsuario.php";


// Validar que todos los campos requeridos estén presentes
if (
    !isset($_POST['area'], $_POST['semana'], $_POST['fecha_inicio'], $_POST['fecha_fin'], 
    $_POST['responsable'], $_POST['supervisor'], $_POST['aux_res'], $_POST['elemento'])
) {
    echo json_encode(["success" => false, "message" => "Faltan datos requeridos."]);
    exit;
}

// Obtener y sanitizar datos del formulario
$area = $conn->real_escape_string($_POST['area']);
$semana = $conn->real_escape_string($_POST['semana']);
$fecha_inicio = $conn->real_escape_string($_POST['fecha_inicio']);
$fecha_fin = $conn->real_escape_string($_POST['fecha_fin']);
$responsable = $conn->real_escape_string($_POST['responsable']);
$supervisor = $conn->real_escape_string($_POST['supervisor']);
$aux_res = $conn->real_escape_string($_POST['aux_res']);

// Insertar datos en la tabla Bitacora_Limpieza usando prepared statements
$sql = "INSERT INTO Bitacora_Limpieza (area, semana, fecha_inicio, fecha_fin, responsable, supervisor, aux_res)
        VALUES (?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode(["success" => false, "message" => "Error al preparar la consulta: " . $conn->error]]);
    exit;
}

$stmt->bind_param("sssssss", $area, $semana, $fecha_inicio, $fecha_fin, $responsable, $supervisor, $aux_res);
if (!$stmt->execute()) {
    echo json_encode(["success" => false, "message" => "Error al insertar en Bitacora_Limpieza: " . $stmt->error]]);
    $stmt->close();
    exit;
}

$id_bitacora = $stmt->insert_id; // Obtener el ID de la bitácora recién insertada
$stmt->close();

// Insertar detalles de limpieza
foreach ($_POST['elemento'] as $index => $elemento) {
    // Sanitizar el nombre del elemento
    $elemento = $conn->real_escape_string($elemento);

    // Obtener los valores de los checkboxes (1 si está marcado, 0 si no)
    $lunes_mat = isset($_POST['lunes_mat'][$index]) ? 1 : 0;
    $lunes_vesp = isset($_POST['lunes_vesp'][$index]) ? 1 : 0;
    $martes_mat = isset($_POST['martes_mat'][$index]) ? 1 : 0;
    $martes_vesp = isset($_POST['martes_vesp'][$index]) ? 1 : 0;
    $miercoles_mat = isset($_POST['miercoles_mat'][$index]) ? 1 : 0;
    $miercoles_vesp = isset($_POST['miercoles_vesp'][$index]) ? 1 : 0;
    $jueves_mat = isset($_POST['jueves_mat'][$index]) ? 1 : 0;
    $jueves_vesp = isset($_POST['jueves_vesp'][$index]) ? 1 : 0;
    $viernes_mat = isset($_POST['viernes_mat'][$index]) ? 1 : 0;
    $viernes_vesp = isset($_POST['viernes_vesp'][$index]) ? 1 : 0;
    $sabado_mat = isset($_POST['sabado_mat'][$index]) ? 1 : 0;
    $sabado_vesp = isset($_POST['sabado_vesp'][$index]) ? 1 : 0;
    $domingo_mat = isset($_POST['domingo_mat'][$index]) ? 1 : 0;
    $domingo_vesp = isset($_POST['domingo_vesp'][$index]) ? 1 : 0;

    // Insertar en Detalle_Limpieza usando prepared statements
    $sql_detalle = "INSERT INTO Detalle_Limpieza (
                        id_bitacora, elemento, lunes_mat, lunes_vesp, martes_mat, martes_vesp, 
                        miercoles_mat, miercoles_vesp, jueves_mat, jueves_vesp, viernes_mat, 
                        viernes_vesp, sabado_mat, sabado_vesp, domingo_mat, domingo_vesp
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt_detalle = $conn->prepare($sql_detalle);
    if (!$stmt_detalle) {
        echo json_encode(["success" => false, "message" => "Error al preparar la consulta de detalle: " . $conn->error]]);
        exit;
    }

    $stmt_detalle->bind_param(
        "isiiiiiiiiiiiiii", 
        $id_bitacora, $elemento, $lunes_mat, $lunes_vesp, $martes_mat, $martes_vesp, 
        $miercoles_mat, $miercoles_vesp, $jueves_mat, $jueves_vesp, $viernes_mat, 
        $viernes_vesp, $sabado_mat, $sabado_vesp, $domingo_mat, $domingo_vesp
    );

    if (!$stmt_detalle->execute()) {
        echo json_encode(["success" => false, "message" => "Error al insertar en Detalle_Limpieza: " . $stmt_detalle->error]]);
        $stmt_detalle->close();
        exit;
    }

    $stmt_detalle->close();
}

echo json_encode(["success" => true, "message" => "Bitácora guardada correctamente."]);
$conn->close();
?>