<?php
header('Content-Type: application/json');
include("db_connect.php");
include_once "ControladorUsuario.php";

// Consulta para obtener usuarios únicos que han realizado conteos
// Incluimos 'completado' y 'liberado' (productos que fueron contados y luego liberados)
$sql = "SELECT DISTINCT Usuario_Selecciono 
        FROM Inventario_Turnos_Productos 
        WHERE (Estado = 'completado' OR Estado = 'liberado')
          AND Usuario_Selecciono IS NOT NULL
          AND Usuario_Selecciono != ''
          AND Existencias_Fisicas IS NOT NULL
        ORDER BY Usuario_Selecciono ASC";

$stmt = $conn->prepare($sql);
$data = [];

if ($stmt) {
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($fila = $result->fetch_assoc()) {
        if (!empty($fila['Usuario_Selecciono'])) {
            $data[] = [
                'Usuario_Selecciono' => $fila['Usuario_Selecciono']
            ];
        }
    }
    
    $stmt->close();
}

echo json_encode($data);
$conn->close();
?>
