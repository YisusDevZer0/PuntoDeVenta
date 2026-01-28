<?php
header('Content-Type: application/json');
include_once "../dbconect.php";

try {
    $sql = "SELECT ID_Sucursal, Nombre_Sucursal FROM Sucursales ORDER BY Nombre_Sucursal";
    $result = $con->query($sql);
    
    if (!$result) {
        throw new Exception('Error en la consulta: ' . $con->error);
    }
    
    $sucursales = [];
    while ($row = $result->fetch_assoc()) {
        $sucursales[] = [
            'id' => $row['ID_Sucursal'],
            'nombre' => $row['Nombre_Sucursal']
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
