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
    // Consulta mejorada para productos con bajo stock usando INNER JOIN como ArrayStocks.php
    $sql = "SELECT 
                s.ID_Prod_POS,
                s.Nombre_Prod,
                s.Cod_Barra,
                s.Existencias_R,
                s.Min_Existencia,
                s.Max_Existencia,
                s.Estatus,
                p.Precio_Venta
            FROM Stock_POS s
            INNER JOIN Productos_POS p ON s.ID_Prod_POS = p.ID_Prod_POS
            WHERE s.Estatus = 'Activo'
              AND s.Existencias_R IS NOT NULL
              AND s.Min_Existencia IS NOT NULL
              AND s.Existencias_R <= s.Min_Existencia
              AND s.Existencias_R >= 0
            ORDER BY (s.Min_Existencia - s.Existencias_R) DESC, s.Nombre_Prod ASC";
    
    $result = $conn->query($sql);
    
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
            'Max_Existencia' => $row['Max_Existencia'],
            'Estatus' => $row['Estatus']
        ];
    }
    
    // Para debugging, también obtener estadísticas usando la misma lógica
    $sqlStats = "SELECT 
                    COUNT(*) as total_productos,
                    COUNT(CASE WHEN Existencias_R <= Min_Existencia THEN 1 END) as con_bajo_stock,
                    COUNT(CASE WHEN Existencias_R IS NULL THEN 1 END) as sin_existencias,
                    COUNT(CASE WHEN Min_Existencia IS NULL THEN 1 END) as sin_min_existencia
                  FROM Stock_POS s
                  INNER JOIN Productos_POS p ON s.ID_Prod_POS = p.ID_Prod_POS
                  WHERE s.Estatus = 'Activo'";
    
    $resultStats = $conn->query($sqlStats);
    $stats = $resultStats->fetch_assoc();
    
    echo json_encode([
        'success' => true,
        'productos' => $productos,
        'total' => count($productos),
        'debug' => [
            'total_productos' => $stats['total_productos'],
            'con_bajo_stock' => $stats['con_bajo_stock'],
            'sin_existencias' => $stats['sin_existencias'],
            'sin_min_existencia' => $stats['sin_min_existencia']
        ]
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error al obtener productos con bajo stock: ' . $e->getMessage()
    ]);
}
?> 