<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

include_once "../dbconect.php";

try {
    $sql = "SELECT ID_Sucursal as id, Nombre_Sucursal as nombre FROM Sucursales ORDER BY Nombre_Sucursal ASC";
    $stmt = $con->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $sucursales = [];
    while ($row = $result->fetch_assoc()) {
        $sucursales[] = $row;
    }
    
    echo json_encode([
        'success' => true,
        'sucursales' => $sucursales
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
} finally {
    if (isset($con)) {
        $con->close();
    }
}
?>
