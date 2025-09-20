<?php
header('Content-Type: application/json');
include_once "../Controladores/db_connect.php";
include_once "../Controladores/ControladorUsuario.php";

// Verificar sesión
session_start();
if(!isset($_SESSION['VentasPos'])){
    echo json_encode(['success' => false, 'message' => 'Sesión no válida']);
    exit();
}

try {
    $sucursal_id = $row['Fk_Sucursal'];
    
    // Buscar productos con stock bajo
    $sql = "SELECT 
                s.ID_Prod_POS, 
                s.Nombre_Prod, 
                s.Cod_Barra,
                s.Clave_adicional,
                s.Existencias_R,
                s.Min_Existencia,
                s.Max_Existencia,
                p.Precio_Venta
            FROM Stock_POS s
            LEFT JOIN Productos_POS p ON s.ID_Prod_POS = p.ID_Prod_POS
            WHERE s.Fk_sucursal = ? 
            AND s.Existencias_R <= s.Min_Existencia
            ORDER BY s.Existencias_R ASC, s.Nombre_Prod ASC";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $sucursal_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $productos = [];
    while ($row = $result->fetch_assoc()) {
        $productos[] = [
            'id' => $row['ID_Prod_POS'],
            'nombre' => $row['Nombre_Prod'],
            'codigo_barras' => $row['Cod_Barra'],
            'clave_adicional' => $row['Clave_adicional'],
            'precio' => floatval($row['Precio_Venta']),
            'existencias' => intval($row['Existencias_R']),
            'min_existencia' => intval($row['Min_Existencia']),
            'max_existencia' => intval($row['Max_Existencia'])
        ];
    }
    
    echo json_encode([
        'success' => true,
        'productos' => $productos,
        'total' => count($productos)
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error al obtener productos con stock bajo: ' . $e->getMessage()
    ]);
}
?>
