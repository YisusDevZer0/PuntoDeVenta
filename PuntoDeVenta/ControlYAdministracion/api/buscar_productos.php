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
    
    // Consulta para buscar productos usando Stock_POS
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
            LEFT JOIN Productos_POS p ON s.ID_Prod_POS = p.ID_Prod_POS
            WHERE s.Estatus = 'Activo'
              AND (s.Nombre_Prod LIKE ? OR s.Cod_Barra LIKE ?)
            ORDER BY s.Nombre_Prod ASC
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
            'Max_Existencia' => $row['Max_Existencia'],
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