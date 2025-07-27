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
    $query = isset($_POST['query']) ? trim($_POST['query']) : '';
    
    if (empty($query)) {
        echo json_encode(['success' => false, 'message' => 'Query de búsqueda requerida']);
        exit();
    }
    
    // Consulta para buscar productos
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
              AND (p.Nombre_Prod LIKE ? OR p.Cod_Barra LIKE ?)
            ORDER BY p.Nombre_Prod ASC
            LIMIT 50";
    
    $stmt = $conn->prepare($sql);
    $searchTerm = "%$query%";
    $stmt->bind_param("ss", $searchTerm, $searchTerm);
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
        'message' => 'Error en la búsqueda: ' . $e->getMessage()
    ]);
}
?> 