<?php
header('Content-Type: application/json');
include_once "../Controladores/db_connect.php";
include_once "../ControladorUsuario.php";

// Verificar sesión
if(!isset($_SESSION['ControlMaestro']) && !isset($_SESSION['AdministradorRH']) && !isset($_SESSION['Marketing'])){
    echo json_encode(['success' => false, 'message' => 'Sesión no válida']);
    exit();
}

try {
    $query = isset($_POST['query']) ? trim($_POST['query']) : '';
    $limit = isset($_POST['limit']) ? intval($_POST['limit']) : 20;
    
    if (empty($query)) {
        // Si no hay query, devolver productos populares o recientes
        $sql = "SELECT 
                    s.ID_Prod_POS,
                    s.Nombre_Prod,
                    s.Cod_Barra,
                    s.Existencias_R,
                    s.Min_Existencia,
                    s.Max_Existencia,
                    s.Estatus,
                    COALESCE(p.Precio_Venta, 0) as Precio_Venta
                FROM Stock_POS s
                LEFT JOIN Productos_POS p ON s.ID_Prod_POS = p.ID_Prod_POS
                WHERE s.Estatus = 'Activo'
                  AND s.Existencias_R > 0
                ORDER BY s.Existencias_R DESC
                LIMIT ?";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $limit);
    } else {
        // Búsqueda dinámica mejorada
        $sql = "SELECT 
                    s.ID_Prod_POS,
                    s.Nombre_Prod,
                    s.Cod_Barra,
                    s.Existencias_R,
                    s.Min_Existencia,
                    s.Max_Existencia,
                    s.Estatus,
                    COALESCE(p.Precio_Venta, 0) as Precio_Venta,
                    CASE 
                        WHEN s.Cod_Barra = ? THEN 3
                        WHEN s.Nombre_Prod LIKE ? THEN 2
                        WHEN s.Nombre_Prod LIKE ? THEN 1
                        ELSE 0
                    END as relevancia
                FROM Stock_POS s
                LEFT JOIN Productos_POS p ON s.ID_Prod_POS = p.ID_Prod_POS
                WHERE s.Estatus = 'Activo'
                  AND (s.Nombre_Prod LIKE ? OR s.Cod_Barra LIKE ?)
                ORDER BY relevancia DESC, s.Nombre_Prod ASC
                LIMIT ?";
        
        $stmt = $conn->prepare($sql);
        $exactQuery = $query;
        $startQuery = $query . '%';
        $containsQuery = '%' . $query . '%';
        $stmt->bind_param("sssssi", $exactQuery, $startQuery, $containsQuery, $containsQuery, $containsQuery, $limit);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    
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
            'Estatus' => $row['Estatus'],
            'stock_status' => getStockStatus($row['Existencias_R'], $row['Min_Existencia'])
        ];
    }
    
    echo json_encode([
        'success' => true,
        'productos' => $productos,
        'total' => count($productos),
        'query' => $query
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error en la búsqueda: ' . $e->getMessage()
    ]);
}

// Función para determinar el estado del stock
function getStockStatus($existencias, $minExistencia) {
    if ($existencias <= 0) {
        return 'agotado';
    } elseif ($existencias <= $minExistencia) {
        return 'bajo';
    } else {
        return 'normal';
    }
}
?> 