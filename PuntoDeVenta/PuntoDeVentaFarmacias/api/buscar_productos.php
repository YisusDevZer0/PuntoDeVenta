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
    $query = isset($_POST['query']) ? trim($_POST['query']) : '';
    $sucursal = $row['Fk_Sucursal']; // Sucursal del usuario actual
    
    if (empty($query)) {
        echo json_encode(['success' => false, 'message' => 'Término de búsqueda requerido']);
        exit();
    }
    
    // Buscar productos en la sucursal del usuario
    $sql = "SELECT 
                s.ID_Prod_POS, 
                s.Nombre_Prod, 
                s.Cod_Barra,
                s.Clave_adicional,
                s.Existencias_R,
                s.Min_Existencia,
                s.Max_Existencia,
                p.Precio_Venta,
                p.Precio_C
            FROM Stock_POS s
            LEFT JOIN Productos_POS p ON s.ID_Prod_POS = p.ID_Prod_POS
            WHERE s.Fk_sucursal = ? 
            AND (s.Nombre_Prod LIKE ? OR s.Cod_Barra LIKE ? OR s.Clave_adicional LIKE ?)
            ORDER BY s.Nombre_Prod ASC 
            LIMIT 20";
    
    $stmt = $conn->prepare($sql);
    $like = "%$query%";
    $stmt->bind_param("isss", $sucursal, $like, $like, $like);
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
            'max_existencia' => intval($row['Max_Existencia']),
            'stock_status' => getStockStatus($row['Existencias_R'], $row['Min_Existencia'])
        ];
    }
    
    echo json_encode([
        'success' => true,
        'productos' => $productos,
        'total' => count($productos),
        'query' => $query,
        'sucursal' => $sucursal
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
    } elseif ($existencias < $minExistencia) {
        return 'bajo';
    } else {
        return 'normal';
    }
}
?>
