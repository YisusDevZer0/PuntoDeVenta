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
    
    // Usar la misma lógica que PedidosController.php
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
    $stmt->bind_param("ssss", $sucursal, $like, $like, $like);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $productos = [];
    while ($row = $result->fetch_assoc()) {
        $productos[] = [
            'ID_Prod_POS' => $row['ID_Prod_POS'],
            'Nombre_Prod' => $row['Nombre_Prod'],
            'Cod_Barra' => $row['Cod_Barra'],
            'Clave_adicional' => $row['Clave_adicional'],
            'Precio_Venta' => $row['Precio_Venta'],
            'Existencias_R' => $row['Existencias_R'],
            'Min_Existencia' => $row['Min_Existencia'],
            'Max_Existencia' => $row['Max_Existencia'],
            'Estatus' => 'Activo',
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