<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    include("db_connect.php");
    include_once "ControladorUsuario.php";
    
    // Obtener parámetros de filtro
    $fecha_inicio = isset($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : date('Y-m-01');
    $fecha_fin = isset($_GET['fecha_fin']) ? $_GET['fecha_fin'] : date('Y-m-d');
    $sucursal = isset($_GET['sucursal']) ? $_GET['sucursal'] : '';
    
    // Consulta SQL para pagos de servicios
    $sql = "SELECT 
        ps.id,
        ps.NumTicket,
        ps.nombre_paciente,
        ps.Servicio,
        ps.costo,
        ps.FormaDePago,
        ps.Fk_Sucursal,
        ps.Fk_Caja,
        ps.Empleado,
        s.Nombre_Sucursal,
        c.ID_Caja
    FROM PagosServicios ps
    LEFT JOIN Sucursales s ON ps.Fk_Sucursal = s.ID_Sucursal
    LEFT JOIN Cajas c ON ps.Fk_Caja = c.ID_Caja
    WHERE 1=1";
    
    // Agregar filtro de fecha si se proporciona (asumiendo que hay un campo de fecha en PagosServicios)
    // Si no hay campo de fecha, podrías usar un campo de registro o eliminar este filtro
    // Por ahora, comentamos el filtro de fecha hasta confirmar qué campo usar
    // $sql .= " AND ps.fecha_registro BETWEEN ? AND ?";
    
    // Agregar filtro de sucursal si se especifica
    if (!empty($sucursal)) {
        $sql .= " AND ps.Fk_Sucursal = ?";
    }
    
    $sql .= " ORDER BY ps.id DESC";
    
    // Preparar la consulta
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception('Error al preparar la consulta: ' . $conn->error);
    }
    
    // Bind parameters
    if (!empty($sucursal)) {
        $stmt->bind_param("s", $sucursal);
    }
    
    if (!$stmt->execute()) {
        throw new Exception('Error al ejecutar la consulta: ' . $stmt->error);
    }
    
    $result = $stmt->get_result();
    if (!$result) {
        throw new Exception('Error al obtener resultados: ' . $stmt->error);
    }
    
    // Procesar resultados
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = [
            "id" => $row['id'],
            "NumTicket" => $row['NumTicket'] ?: '',
            "Cliente" => $row['nombre_paciente'] ?: 'Sin nombre',
            "Servicio" => $row['Servicio'] ?: 'Sin servicio',
            "Costo" => '$' . number_format($row['costo'] ?: 0, 2),
            "FormaDePago" => $row['FormaDePago'] ?: 'N/A',
            "Sucursal" => $row['Nombre_Sucursal'] ?: 'Sucursal no encontrada',
            "Empleado" => $row['Empleado'] ?: '',
            "Caja" => $row['ID_Caja'] ?: ''
        ];
    }
    
    // Construir respuesta JSON para DataTables
    $response = [
        "draw" => isset($_GET['draw']) ? intval($_GET['draw']) : 1,
        "recordsTotal" => count($data),
        "recordsFiltered" => count($data),
        "data" => $data
    ];
    
    // Configurar headers para JSON
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($response);
    
    // Cerrar conexión
    $stmt->close();
    $conn->close();
    
} catch (Exception $e) {
    error_log('Error en ArrayDeReportePagosDeServicios.php: ' . $e->getMessage());
    
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        "error" => true,
        "message" => 'Error al generar el reporte: ' . $e->getMessage()
    ]);
}
?>
