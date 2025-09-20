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
    // Obtener query
    $query = $_POST['query'] ?? $_GET['query'] ?? '';
    $query = trim($query);
    
    if (empty($query)) {
        echo json_encode(['success' => false, 'message' => 'Término de búsqueda requerido']);
        exit();
    }
    
    // Obtener sucursal
    $sucursal = $_SESSION['VentasPos']['Fk_Sucursal'] ?? 1; // Default a 1 si no se encuentra
    
    // Buscar productos
    $sql = "SELECT 
                s.ID_Prod_POS, 
                s.Nombre_Prod, 
                s.Cod_Barra,
                s.Clave_adicional,
                s.Existencias_R,
                s.Min_Existencia,
                s.Max_Existencia,
                COALESCE(p.Precio_Venta, 0) as Precio_Venta
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
            'max_existencia' => intval($row['Max_Existencia'])
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
?>
