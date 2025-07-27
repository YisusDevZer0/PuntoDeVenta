<?php
header('Content-Type: application/json');
include_once "../Controladores/db_connect.php";
include_once "../Controladores/ControladorUsuario.php";

// Verificar sesión
if(!isset($_SESSION['ControlMaestro']) && !isset($_SESSION['AdministradorRH']) && !isset($_SESSION['Marketing'])){
    echo json_encode(['success' => false, 'message' => 'Sesión no válida']);
    exit();
}

try {
    // Consulta para productos con bajo stock
    $sql = "SELECT 
                p.ID_Prod_POS,
                p.Nombre_Prod,
                p.Cod_Barra,
                p.Precio_Venta,
                p.Existencias_R,
                p.Min_Existencia,
                p.Estatus
            FROM Productos_POS p
            WHERE p.Estatus = 'Activo'
              AND p.Existencias_R <= p.Min_Existencia
            ORDER BY (p.Min_Existencia - p.Existencias_R) DESC, p.Nombre_Prod ASC";
    
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
            'Estatus' => $row['Estatus']
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
        'message' => 'Error al obtener productos con bajo stock: ' . $e->getMessage()
    ]);
}
?> 