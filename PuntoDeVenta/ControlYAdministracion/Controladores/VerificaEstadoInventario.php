<?php
// Incluir archivo de conexión
include_once "db_connect.php";

$fkSucursal = $_POST['fkSucursal'];

// Verificar si el inventario inicial ya ha sido establecido hoy
$query = "SELECT inventario_inicial_establecido, fecha_establecido FROM inventario_inicial_estado WHERE fkSucursal = ? AND DATE(fecha_establecido) = CURRENT_DATE";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $fkSucursal);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if ($row && $row['inventario_inicial_establecido']) {
    echo json_encode(['success' => true, 'fecha_establecido' => $row['fecha_establecido']]);
} else {
    echo json_encode(['success' => false]);
}
?>
