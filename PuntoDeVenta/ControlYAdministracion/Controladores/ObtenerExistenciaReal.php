<?php
include_once "db_connect.php";

$codigo = isset($_GET['codigo']) ? $_GET['codigo'] : '';
$sucursal = isset($_GET['sucursal']) ? $_GET['sucursal'] : '';

if (empty($codigo) || empty($sucursal)) {
    echo json_encode(['success' => false, 'message' => 'Faltan parámetros requeridos']);
    exit;
}

// Obtener la existencia real más reciente del producto en la sucursal
$sql = "SELECT Existencias_R FROM ConteosDiarios 
        WHERE Cod_Barra = ? AND Fk_sucursal = ? 
        ORDER BY AgregadoEl DESC LIMIT 1";

$stmt = $conn->prepare($sql);
if ($stmt) {
    $stmt->bind_param("si", $codigo, $sucursal);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        echo json_encode([
            'success' => true, 
            'existenciaReal' => $row['Existencias_R']
        ]);
    } else {
        // Si no hay registros previos, buscar en inventario general
        $sqlInventario = "SELECT Stock FROM InventarioGeneral WHERE Cod_Barra = ? AND Fk_sucursal = ? LIMIT 1";
        $stmtInventario = $conn->prepare($sqlInventario);
        if ($stmtInventario) {
            $stmtInventario->bind_param("si", $codigo, $sucursal);
            $stmtInventario->execute();
            $resultInventario = $stmtInventario->get_result();
            
            if ($rowInventario = $resultInventario->fetch_assoc()) {
                echo json_encode([
                    'success' => true, 
                    'existenciaReal' => $rowInventario['Stock']
                ]);
            } else {
                echo json_encode([
                    'success' => true, 
                    'existenciaReal' => 0
                ]);
            }
            $stmtInventario->close();
        } else {
            echo json_encode([
                'success' => true, 
                'existenciaReal' => 0
            ]);
        }
    }
    
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Error al consultar la existencia real']);
}

$conn->close();
?>
