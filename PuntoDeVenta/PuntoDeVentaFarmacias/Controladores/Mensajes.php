<?php
include_once "db_connect.php";
include_once "ControladorUsuario.php";

// Obtener el valor de la licencia y la sucursal de algún lugar, como la sesión o una petición
$licencia = isset($_GET['licencia']) ? $_GET['licencia'] : '';
$Fk_Sucursal = isset($_GET['Fk_Sucursal']) ? $_GET['Fk_Sucursal'] : '';

// Verificar que los valores no estén vacíos
if (empty($licencia) || empty($Fk_Sucursal)) {
    die(json_encode(['error' => 'Licencia o Sucursal no están definidos']));
}

// Consulta segura utilizando una sentencia preparada
$sql = "SELECT 
    RP.ID_Notificacion, 
    RP.Encabezado, 
    RP.TipoMensaje, 
    RP.Mensaje_Recordatorio, 
    RP.Registrado, 
    RP.Sistema, 
    RP.Sucursal, 
    CASE 
        WHEN RP.Estado = 0 THEN 'Pendiente'
        ELSE RP.Estado 
    END AS Estado, 
    RP.Licencia,
    S.Nombre_Sucursal
FROM 
    Recordatorios_Pendientes RP
JOIN 
    Sucursales S ON RP.Sucursal = S.ID_Sucursal 
AND RP.Licencia = ? AND RP.Sucursal = ?
ORDER BY RP.Registrado DESC
LIMIT 3";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    die(json_encode(['error' => 'Error al preparar la consulta: ' . $conn->error]));
}

$stmt->bind_param("ss", $licencia, $Fk_Sucursal);
$stmt->execute();
$result = $stmt->get_result();

$messages = array();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $messages[] = $row;
    }
} else {
    // Agregar mensaje de depuración si no hay resultados
    error_log("No se encontraron resultados para licencia: $licencia y sucursal: $Fk_Sucursal");
}

$stmt->close();
$conn->close();

header('Content-Type: application/json');
echo json_encode($messages);
?>
