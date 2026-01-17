<?php
header('Content-Type: application/json');
include("db_connect.php");
include_once "ControladorUsuario.php";

// Consulta para obtener todas las sucursales
$sql = "SELECT ID_Sucursal, Nombre_Sucursal 
        FROM Sucursales 
        ORDER BY Nombre_Sucursal ASC";

$stmt = $conn->prepare($sql);
$data = [];

if ($stmt) {
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($fila = $result->fetch_assoc()) {
        $data[] = [
            'ID_Sucursal' => $fila['ID_Sucursal'],
            'Nombre_Sucursal' => $fila['Nombre_Sucursal']
        ];
    }
    
    $stmt->close();
}

echo json_encode($data);
$conn->close();
?>
