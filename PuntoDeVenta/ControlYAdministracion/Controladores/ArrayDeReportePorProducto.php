<?php
header('Content-Type: application/json');
include("db_connect.php");
include("ControladorUsuario.php");

// Habilitar reporte de errores para debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    // Verificar conexión
    if (!isset($conn) || !$conn) {
        throw new Exception("Error de conexión a la base de datos");
    }

    // Obtener parámetros de filtro (opcional)
    $fecha_inicio = isset($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : date('Y-m-01'); // Primer día del mes actual
    $fecha_fin = isset($_GET['fecha_fin']) ? $_GET['fecha_fin'] : date('Y-m-d'); // Día actual
    $sucursal = isset($_GET['sucursal']) ? $_GET['sucursal'] : '';

    // Consulta SQL usando los campos correctos de Ventas_POS
    $sql = "SELECT 
        v.ID_Prod_POS,
        v.Cod_Barra,
        v.Nombre_Prod,
        SUM(v.Cantidad_Venta) AS Total_Vendido,
        SUM(v.Importe) AS Total_Importe,
        SUM(v.Total_Venta) AS Total_Venta,
        SUM(COALESCE(v.DescuentoAplicado, 0)) AS Total_Descuento,
        COUNT(*) AS Numero_Ventas,
        v.Fk_sucursal,
        v.Tipo,
        v.AgregadoPor,
        MAX(v.Fecha_venta) AS Ultima_Venta,
        v.FormaDePago,
        v.Estatus
    FROM Ventas_POS v
    WHERE v.Fecha_venta BETWEEN ? AND ?";

    // Agregar filtro de sucursal si se especifica
    if (!empty($sucursal)) {
        $sql .= " AND v.Fk_sucursal = ?";
    }

    $sql .= " GROUP BY v.ID_Prod_POS, v.Cod_Barra, v.Nombre_Prod, v.Fk_sucursal, v.Tipo, v.AgregadoPor, v.FormaDePago, v.Estatus
    ORDER BY Total_Vendido DESC";

    // Preparar la consulta
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("Error al preparar la consulta: " . $conn->error);
    }

    if (!empty($sucursal)) {
        $stmt->bind_param("sss", $fecha_inicio, $fecha_fin, $sucursal);
    } else {
        $stmt->bind_param("ss", $fecha_inicio, $fecha_fin);
    }

    if (!$stmt->execute()) {
        throw new Exception("Error al ejecutar la consulta: " . $stmt->error);
    }

    $result = $stmt->get_result();
    if (!$result) {
        throw new Exception("Error al obtener resultados: " . $conn->error);
    }

    $data = [];

    if ($result->num_rows > 0) {
        while ($fila = $result->fetch_assoc()) {
            $data[] = [
                "ID_Prod_POS" => $fila["ID_Prod_POS"] ?? '',
                "Cod_Barra" => $fila["Cod_Barra"] ?? '',
                "Nombre_Prod" => $fila["Nombre_Prod"] ?? '',
                "Total_Vendido" => number_format($fila["Total_Vendido"] ?? 0, 0),
                "Total_Importe" => '$' . number_format($fila["Total_Importe"] ?? 0, 2),
                "Total_Venta" => '$' . number_format($fila["Total_Venta"] ?? 0, 2),
                "Total_Descuento" => '$' . number_format($fila["Total_Descuento"] ?? 0, 2),
                "Numero_Ventas" => $fila["Numero_Ventas"] ?? 0,
                "Nombre_Sucursal" => 'Sucursal ' . ($fila["Fk_sucursal"] ?? 'N/A'),
                "Precio_Venta" => '$0.00', // No disponible en esta consulta
                "Precio_C" => '$0.00', // No disponible en esta consulta
                "Tipo" => $fila["Tipo"] ?? 'Producto',
                "AgregadoPor" => $fila["AgregadoPor"] ?? '',
                "Ultima_Venta" => $fila["Ultima_Venta"] ? date('d/m/Y H:i', strtotime($fila["Ultima_Venta"])) : 'N/A'
            ];
        }
    }

    // Construir el array de resultados para la respuesta JSON
    $results = [
        "sEcho" => 1,
        "iTotalRecords" => count($data),
        "iTotalDisplayRecords" => count($data),
        "aaData" => $data
    ];

    // Imprimir la respuesta JSON
    echo json_encode($results);

    // Cerrar la conexión
    $stmt->close();

} catch (Exception $e) {
    // En caso de error, devolver un JSON con el error
    http_response_code(500);
    echo json_encode([
        "error" => true,
        "message" => $e->getMessage(),
        "sEcho" => 1,
        "iTotalRecords" => 0,
        "iTotalDisplayRecords" => 0,
        "aaData" => []
    ]);
}

// Cerrar conexión
if (isset($conn)) {
    $conn->close();
}
?>
