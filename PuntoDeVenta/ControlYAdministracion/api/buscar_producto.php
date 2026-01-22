<?php
header('Content-Type: application/json');
include_once "../db_connect.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

$cod_barra = isset($_POST['cod_barra']) ? trim($_POST['cod_barra']) : '';

if (empty($cod_barra)) {
    echo json_encode(['success' => false, 'message' => 'Código de barras requerido']);
    exit;
}

try {
    // Buscar producto en Stock_POS
    $sql = "SELECT 
                sp.ID_Prod_POS,
                sp.Cod_Barra,
                sp.Nombre_Prod,
                sp.Fk_sucursal,
                sp.Existencias_R,
                s.Nombre_Sucursal,
                COALESCE(SUM(hl.Existencias), 0) as Total_Lotes
            FROM Stock_POS sp
            INNER JOIN Sucursales s ON sp.Fk_sucursal = s.ID_Sucursal
            LEFT JOIN Historial_Lotes hl ON sp.ID_Prod_POS = hl.ID_Prod_POS 
                AND sp.Fk_sucursal = hl.Fk_sucursal
            WHERE sp.Cod_Barra = ?
            GROUP BY sp.ID_Prod_POS, sp.Fk_sucursal
            LIMIT 1";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $cod_barra);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Producto no encontrado'
        ]);
    } else {
        $producto = $result->fetch_assoc();
        
        // Obtener lotes disponibles del producto
        $sql_lotes = "SELECT 
                        ID_Historial,
                        Lote,
                        Fecha_Caducidad,
                        Existencias,
                        DATEDIFF(Fecha_Caducidad, CURDATE()) as Dias_restantes
                      FROM Historial_Lotes
                      WHERE ID_Prod_POS = ? AND Fk_sucursal = ? AND Existencias > 0
                      ORDER BY Fecha_Caducidad ASC";
        
        $stmt_lotes = $conn->prepare($sql_lotes);
        $stmt_lotes->bind_param("ii", $producto['ID_Prod_POS'], $producto['Fk_sucursal']);
        $stmt_lotes->execute();
        $result_lotes = $stmt_lotes->get_result();
        
        $lotes = [];
        while ($lote = $result_lotes->fetch_assoc()) {
            $lotes[] = $lote;
        }
        $stmt_lotes->close();
        
        $producto['lotes'] = $lotes;
        
        echo json_encode([
            'success' => true,
            'producto' => $producto
        ]);
    }
    
    $stmt->close();
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}

$conn->close();
?>