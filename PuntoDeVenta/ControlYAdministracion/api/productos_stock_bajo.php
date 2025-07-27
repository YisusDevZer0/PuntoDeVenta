<?php
header('Content-Type: application/json');
include_once "../Controladores/db_connect.php";
include_once "../Controladores/ControladorUsuario.php";

// Verificar sesión
session_start();
if(!isset($_SESSION['ControlMaestro']) && !isset($_SESSION['AdministradorRH']) && !isset($_SESSION['Marketing'])){
    echo json_encode(['success' => false, 'message' => 'Sesión no válida']);
    exit();
}

try {
    $sucursal = $row['Fk_Sucursal']; // Sucursal del usuario actual
    
    // Usar la misma lógica que PedidosController.php
    $sql = "SELECT 
                s.ID_Prod_POS, 
                s.Nombre_Prod, 
                s.Cod_Barra,
                s.Existencias_R,
                s.Min_Existencia,
                p.Precio_Venta,
                p.Precio_C,
                (s.Min_Existencia - s.Existencias_R) as cantidad_necesaria
            FROM Stock_POS s
            LEFT JOIN Productos_POS p ON s.ID_Prod_POS = p.ID_Prod_POS
            WHERE s.Fk_sucursal = ? 
            AND s.Existencias_R < s.Min_Existencia
            ORDER BY (s.Min_Existencia - s.Existencias_R) DESC";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $sucursal);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if (!$result) {
        throw new Exception("Error en la consulta: " . $conn->error);
    }
    
    $productos = [];
    while ($row = $result->fetch_assoc()) {
        $productos[] = [
            'ID_Prod_POS' => $row['ID_Prod_POS'],
            'Nombre_Prod' => $row['Nombre_Prod'],
            'Cod_Barra' => $row['Cod_Barra'],
            'Precio_Venta' => $row['Precio_Venta'],
            'Existencias_R' => $row['Existencias_R'],
            'Min_Existencia' => $row['Min_Existencia'],
            'Max_Existencia' => 0, // No se usa en la consulta original
            'Estatus' => 'Activo',
            'cantidad_necesaria' => $row['cantidad_necesaria']
        ];
    }
    
    // Debug: obtener estadísticas
    $sqlStats = "SELECT 
                    COUNT(*) as total_productos,
                    COUNT(CASE WHEN Existencias_R < Min_Existencia THEN 1 END) as con_bajo_stock
                  FROM Stock_POS 
                  WHERE Fk_sucursal = ?";
    
    $stmtStats = $conn->prepare($sqlStats);
    $stmtStats->bind_param("s", $sucursal);
    $stmtStats->execute();
    $resultStats = $stmtStats->get_result();
    $stats = $resultStats->fetch_assoc();
    
    echo json_encode([
        'success' => true,
        'productos' => $productos,
        'total' => count($productos),
        'sucursal' => $sucursal,
        'debug' => [
            'total_productos' => $stats['total_productos'],
            'con_bajo_stock' => $stats['con_bajo_stock']
        ]
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error al obtener productos con bajo stock: ' . $e->getMessage()
    ]);
}
?> 