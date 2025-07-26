<?php
header('Content-Type: application/json');
include("db_connect.php");

// Habilitar reporte de errores para debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    // Verificar conexión
    if (!isset($conn) || !$conn) {
        throw new Exception("Error de conexión a la base de datos");
    }

    // Consulta simple para verificar si hay datos
    $sql = "SELECT COUNT(*) as total FROM Ventas_POS WHERE Estatus = 'vendido'";
    $result = $conn->query($sql);
    
    if (!$result) {
        throw new Exception("Error en consulta simple: " . $conn->error);
    }
    
    $row = $result->fetch_assoc();
    $total_ventas = $row['total'];
    
    // Consulta para verificar fechas disponibles
    $sql_fechas = "SELECT MIN(Fecha_venta) as fecha_min, MAX(Fecha_venta) as fecha_max FROM Ventas_POS WHERE Estatus = 'vendido'";
    $result_fechas = $conn->query($sql_fechas);
    
    if (!$result_fechas) {
        throw new Exception("Error en consulta de fechas: " . $conn->error);
    }
    
    $fechas = $result_fechas->fetch_assoc();
    
    // Consulta de prueba con datos reales
    $fecha_inicio = date('Y-m-01'); // Primer día del mes actual
    $fecha_fin = date('Y-m-d'); // Día actual
    
    $sql_prueba = "SELECT 
        v.ID_Prod_POS,
        v.Cod_Barra,
        v.Nombre_Prod,
        COUNT(*) as ventas_count
    FROM Ventas_POS v
    WHERE v.Fecha_venta BETWEEN ? AND ?
    AND v.Estatus = 'vendido'
    GROUP BY v.ID_Prod_POS, v.Cod_Barra, v.Nombre_Prod
    LIMIT 10";
    
    $stmt = $conn->prepare($sql_prueba);
    if (!$stmt) {
        throw new Exception("Error al preparar consulta de prueba: " . $conn->error);
    }
    
    $stmt->bind_param("ss", $fecha_inicio, $fecha_fin);
    $stmt->execute();
    $result_prueba = $stmt->get_result();
    
    $datos_prueba = array();
    while ($row = $result_prueba->fetch_assoc()) {
        $datos_prueba[] = $row;
    }
    
    $response = array(
        "success" => true,
        "total_ventas" => $total_ventas,
        "fecha_min" => $fechas['fecha_min'],
        "fecha_max" => $fechas['fecha_max'],
        "fecha_inicio_busqueda" => $fecha_inicio,
        "fecha_fin_busqueda" => $fecha_fin,
        "datos_prueba" => $datos_prueba,
        "count_datos_prueba" => count($datos_prueba)
    );
    
    echo json_encode($response);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(array(
        "success" => false,
        "error" => $e->getMessage()
    ));
}
?> 