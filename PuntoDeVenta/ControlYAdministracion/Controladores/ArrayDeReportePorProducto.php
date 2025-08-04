<?php
header('Content-Type: application/json');

// Iniciar sesión si no está iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Incluir archivos necesarios
include("db_connect.php");
include_once "ControladorUsuario.php";

// Verificar sesión
if(!isset($_SESSION['ControlMaestro']) && !isset($_SESSION['AdministradorRH']) && !isset($_SESSION['Marketing'])){
    http_response_code(401);
    echo json_encode(array(
        "draw" => isset($_GET['draw']) ? intval($_GET['draw']) : 1,
        "recordsTotal" => 0,
        "recordsFiltered" => 0,
        "data" => array(),
        "error" => "Sesión expirada. Por favor, inicie sesión nuevamente."
    ));
    exit();
}

// Habilitar reporte de errores para debugging
error_reporting(E_ALL);
ini_set('display_errors', 0); // Cambiar a 0 para evitar que los errores se muestren en HTML
ini_set('log_errors', 1);

// Capturar cualquier salida de error
ob_start();

try {
    // Verificar conexión
    if (!isset($conn) || !$conn) {
        throw new Exception("Error de conexión a la base de datos");
    }

    // Obtener parámetros de filtro (opcional)
    $fecha_inicio = isset($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : date('Y-m-01'); // Primer día del mes actual
    $fecha_fin = isset($_GET['fecha_fin']) ? $_GET['fecha_fin'] : date('Y-m-d'); // Día actual
    $sucursal = isset($_GET['sucursal']) ? $_GET['sucursal'] : '';

    // Consulta SQL con JOINs para obtener precios y nombres de sucursales
    $sql = "SELECT 
        v.ID_Prod_POS,
        v.Cod_Barra,
        v.Nombre_Prod,
        v.Tipo,
        v.Fk_sucursal,
        s.Nombre_Sucursal,
        p.Precio_Venta,
        p.Precio_C,
        p.Tipo_Servicio,
        p.Componente_Activo,
        st.Existencias_R,
        SUM(v.Cantidad_Venta) AS Total_Vendido,
        SUM(v.Importe) AS Total_Importe,
        SUM(v.Total_Venta) AS Total_Venta,
        SUM(v.DescuentoAplicado) AS Total_Descuento,
        COUNT(*) AS Numero_Ventas,
        v.AgregadoPor,
        MIN(v.Fecha_venta) AS Primera_Venta,
        MAX(v.Fecha_venta) AS Ultima_Venta
    FROM Ventas_POS v
    LEFT JOIN Productos_POS p ON v.ID_Prod_POS = p.ID_Prod_POS
    LEFT JOIN Stock_POS st ON v.ID_Prod_POS = st.ID_Prod_POS AND v.Fk_sucursal = st.Fk_sucursal
    LEFT JOIN Sucursales s ON v.Fk_sucursal = s.ID_Sucursal
    WHERE v.Fecha_venta BETWEEN ? AND ?
    AND v.Estatus = 'Pagado'";

    // Agregar filtro de sucursal si se especifica
    if (!empty($sucursal)) {
        $sql .= " AND v.Fk_sucursal = ?";
    }

    $sql .= " GROUP BY v.ID_Prod_POS, v.Cod_Barra, v.Nombre_Prod, v.Tipo, v.Fk_sucursal, s.Nombre_Sucursal, p.Precio_Venta, p.Precio_C, p.Tipo_Servicio, p.Componente_Activo, st.Existencias_R, v.AgregadoPor
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
        throw new Exception("Error en la consulta: " . $conn->error);
    }

    $data = array();
    while ($row = $result->fetch_assoc()) {
        // Formatear los datos
        $data[] = array(
            "ID_Prod_POS" => $row['ID_Prod_POS'],
            "Cod_Barra" => $row['Cod_Barra'] ?: '',
            "Nombre_Prod" => $row['Nombre_Prod'] ?: 'Sin nombre',
            "Tipo" => $row['Tipo'] ?: '',
            "Fk_sucursal" => $row['Fk_sucursal'],
            "Nombre_Sucursal" => $row['Nombre_Sucursal'] ?: 'Sucursal no encontrada',
            "Precio_Venta" => $row['Precio_Venta'] ? '$' . number_format($row['Precio_Venta'], 2) : '',
            "Precio_C" => $row['Precio_C'] ? '$' . number_format($row['Precio_C'], 2) : '',
            "Tipo_Servicio" => $row['Tipo_Servicio'] ?: '',
            "Componente_Activo" => $row['Componente_Activo'] ?: '',
            "Existencias_R" => $row['Existencias_R'] ? number_format($row['Existencias_R']) : '0',
            "Total_Vendido" => number_format($row['Total_Vendido']),
            "Total_Importe" => '$' . number_format($row['Total_Importe'], 2),
            "Total_Venta" => '$' . number_format($row['Total_Venta'], 2),
            "Total_Descuento" => '$' . number_format($row['Total_Descuento'], 2),
            "Numero_Ventas" => number_format($row['Numero_Ventas']),
            "AgregadoPor" => $row['AgregadoPor'] ?: '',
            "Primera_Venta" => $row['Primera_Venta'] ? date('d/m/Y', strtotime($row['Primera_Venta'])) : '',
            "Ultima_Venta" => $row['Ultima_Venta'] ? date('d/m/Y', strtotime($row['Ultima_Venta'])) : ''
        );
    }

    // Formato correcto para DataTables
    $response = array(
        "draw" => isset($_GET['draw']) ? intval($_GET['draw']) : 1,
        "recordsTotal" => count($data),
        "recordsFiltered" => count($data),
        "data" => $data
    );

    echo json_encode($response);

} catch (Exception $e) {
    http_response_code(500);
    $response = array(
        "draw" => isset($_GET['draw']) ? intval($_GET['draw']) : 1,
        "recordsTotal" => 0,
        "recordsFiltered" => 0,
        "data" => array(),
        "error" => $e->getMessage()
    );
    echo json_encode($response);
}

// Cerrar conexión
if (isset($stmt)) {
    $stmt->close();
}
if (isset($conn)) {
    $conn->close();
}
?>
