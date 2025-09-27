<?php
header('Content-Type: application/json');
include_once "../dbconect.php";

try {
    $sql = "SELECT id, nombre FROM Sucursales WHERE estado = 'activo' ORDER BY nombre";
    $result = $con->query($sql);
    
    if (!$result) {
        throw new Exception('Error en la consulta: ' . $con->error);
    }
    
    $sucursales = [];
    while ($row = $result->fetch_assoc()) {
        $sucursales[] = [
            'id' => $row['id'],
            'nombre' => $row['nombre']
        ];
    }
    
    echo json_encode([
        'success' => true,
        'sucursales' => $sucursales
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>
