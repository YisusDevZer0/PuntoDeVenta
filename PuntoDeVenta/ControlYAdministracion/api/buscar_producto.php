<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 0); // No mostrar errores en producción, solo en JSON

try {
    include_once "../db_connect.php";
    
    if (!isset($conn) || !$conn) {
        throw new Exception('Error de conexión a la base de datos');
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error de conexión: ' . $e->getMessage()]);
    exit;
}

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
    // Verificar si la columna Control_Lotes_Caducidad existe
    $columna_existe = false;
    try {
        $check_result = mysqli_query($conn, "SHOW COLUMNS FROM Stock_POS LIKE 'Control_Lotes_Caducidad'");
        if ($check_result && mysqli_num_rows($check_result) > 0) {
            $columna_existe = true;
        }
        if ($check_result) {
            mysqli_free_result($check_result);
        }
    } catch (Exception $e) {
        // Si falla la verificación, asumimos que no existe
        $columna_existe = false;
    }
    
    // Construir consulta según si la columna existe
    if ($columna_existe) {
        $sql = "SELECT 
                    sp.ID_Prod_POS,
                    sp.Cod_Barra,
                    sp.Nombre_Prod,
                    sp.Fk_sucursal,
                    sp.Existencias_R,
                    s.Nombre_Sucursal,
                    COALESCE(SUM(hl.Existencias), 0) as Total_Lotes,
                    COALESCE(sp.Control_Lotes_Caducidad, 0) as Control_Lotes_Caducidad
                FROM Stock_POS sp
                INNER JOIN Sucursales s ON sp.Fk_sucursal = s.ID_Sucursal
                LEFT JOIN Historial_Lotes hl ON sp.ID_Prod_POS = hl.ID_Prod_POS 
                    AND sp.Fk_sucursal = hl.Fk_sucursal
                WHERE sp.Cod_Barra = ?
                GROUP BY sp.ID_Prod_POS, sp.Fk_sucursal
                LIMIT 1";
    } else {
        // Si la columna no existe, usar 0 como valor por defecto
        $sql = "SELECT 
                    sp.ID_Prod_POS,
                    sp.Cod_Barra,
                    sp.Nombre_Prod,
                    sp.Fk_sucursal,
                    sp.Existencias_R,
                    s.Nombre_Sucursal,
                    COALESCE(SUM(hl.Existencias), 0) as Total_Lotes,
                    0 as Control_Lotes_Caducidad
                FROM Stock_POS sp
                INNER JOIN Sucursales s ON sp.Fk_sucursal = s.ID_Sucursal
                LEFT JOIN Historial_Lotes hl ON sp.ID_Prod_POS = hl.ID_Prod_POS 
                    AND sp.Fk_sucursal = hl.Fk_sucursal
                WHERE sp.Cod_Barra = ?
                GROUP BY sp.ID_Prod_POS, sp.Fk_sucursal
                LIMIT 1";
    }
    
    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        throw new Exception('Error al preparar la consulta: ' . mysqli_error($conn));
    }
    
    mysqli_stmt_bind_param($stmt, "s", $cod_barra);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) === 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Producto no encontrado'
        ]);
    } else {
        $producto = mysqli_fetch_assoc($result);
        
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
        
        $stmt_lotes = mysqli_prepare($conn, $sql_lotes);
        if (!$stmt_lotes) {
            throw new Exception('Error al preparar consulta de lotes: ' . mysqli_error($conn));
        }
        
        mysqli_stmt_bind_param($stmt_lotes, "ii", $producto['ID_Prod_POS'], $producto['Fk_sucursal']);
        mysqli_stmt_execute($stmt_lotes);
        $result_lotes = mysqli_stmt_get_result($stmt_lotes);
        
        $lotes = [];
        while ($lote = mysqli_fetch_assoc($result_lotes)) {
            $lotes[] = $lote;
        }
        mysqli_stmt_close($stmt_lotes);
        
        $producto['lotes'] = $lotes;
        
        echo json_encode([
            'success' => true,
            'producto' => $producto
        ]);
    }
    
    mysqli_stmt_close($stmt);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage(),
        'error_details' => $e->getFile() . ':' . $e->getLine()
    ]);
} catch (Error $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error fatal: ' . $e->getMessage(),
        'error_details' => $e->getFile() . ':' . $e->getLine()
    ]);
}

if (isset($conn) && $conn) {
    mysqli_close($conn);
}
?>