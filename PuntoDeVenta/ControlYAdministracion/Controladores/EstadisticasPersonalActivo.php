<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Verificar si es una petición OPTIONS (preflight)
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Inicializar array de estadísticas con valores por defecto
$stats = array(
    'totalPersonal' => 0,
    'totalAdministrativos' => 0,
    'totalSucursales' => 0,
    'personalReciente' => 0
);

try {
    include("db_connect.php");
    include "ControladorUsuario.php";

    // Obtener parámetros de filtro
    $tipo = isset($_POST['tipo']) ? $_POST['tipo'] : '';
    $sucursal = isset($_POST['sucursal']) ? $_POST['sucursal'] : '';
    $estado = isset($_POST['estado']) ? $_POST['estado'] : '';

    // Log para depuración
    error_log("Estadísticas solicitadas - Tipo: $tipo, Sucursal: $sucursal, Estado: $estado");

// Construir condiciones WHERE base
$where_conditions = "WHERE u.Estatus = 'Activo'";

// Aplicar filtros a las condiciones
if (!empty($tipo)) {
    $where_conditions .= " AND t.TipoUsuario = '" . mysqli_real_escape_string($conn, $tipo) . "'";
}
if (!empty($sucursal)) {
    $where_conditions .= " AND s.ID_Sucursal = '" . mysqli_real_escape_string($conn, $sucursal) . "'";
}
if (!empty($estado)) {
    $where_conditions .= " AND u.Estatus = '" . mysqli_real_escape_string($conn, $estado) . "'";
}

// Total de personal activo
$sql_total = "SELECT COUNT(*) as total 
              FROM Usuarios_PV u 
              INNER JOIN Tipos_Usuarios t ON u.Fk_Usuario = t.ID_User 
              INNER JOIN Sucursales s ON u.Fk_Sucursal = s.ID_Sucursal 
              $where_conditions";
$result_total = $conn->query($sql_total);
if ($result_total && $row = $result_total->fetch_assoc()) {
    $stats['totalPersonal'] = $row['total'];
} else {
    $stats['totalPersonal'] = 0;
    error_log("Error en consulta total personal: " . $conn->error);
}

// Total de administrativos (incluyendo Administrador y Administrador General)
$sql_admin = "SELECT COUNT(*) as total 
              FROM Usuarios_PV u 
              INNER JOIN Tipos_Usuarios t ON u.Fk_Usuario = t.ID_User 
              INNER JOIN Sucursales s ON u.Fk_Sucursal = s.ID_Sucursal 
              $where_conditions AND (t.TipoUsuario = 'Administrador' OR t.TipoUsuario = 'Administrador General')";
$result_admin = $conn->query($sql_admin);
if ($result_admin && $row = $result_admin->fetch_assoc()) {
    $stats['totalAdministrativos'] = $row['total'];
} else {
    $stats['totalAdministrativos'] = 0;
    error_log("Error en consulta administrativos: " . $conn->error);
}

// Total de sucursales activas con personal
$sql_sucursales = "SELECT COUNT(DISTINCT s.ID_Sucursal) as total 
                   FROM Sucursales s 
                   INNER JOIN Usuarios_PV u ON s.ID_Sucursal = u.Fk_Sucursal 
                   INNER JOIN Tipos_Usuarios t ON u.Fk_Usuario = t.ID_User 
                   WHERE s.Sucursal_Activa = 'Si' AND u.Estatus = 'Activo'";
// Aplicar filtros adicionales si existen
if (!empty($tipo)) {
    $sql_sucursales .= " AND t.TipoUsuario = '" . mysqli_real_escape_string($conn, $tipo) . "'";
}
if (!empty($sucursal)) {
    $sql_sucursales .= " AND s.ID_Sucursal = '" . mysqli_real_escape_string($conn, $sucursal) . "'";
}
$result_sucursales = $conn->query($sql_sucursales);
if ($result_sucursales && $row = $result_sucursales->fetch_assoc()) {
    $stats['totalSucursales'] = $row['total'];
} else {
    $stats['totalSucursales'] = 0;
    error_log("Error en consulta sucursales: " . $conn->error);
}

// Personal agregado este mes
$sql_reciente = "SELECT COUNT(*) as total 
                 FROM Usuarios_PV u 
                 INNER JOIN Tipos_Usuarios t ON u.Fk_Usuario = t.ID_User 
                 INNER JOIN Sucursales s ON u.Fk_Sucursal = s.ID_Sucursal 
                 WHERE u.Estatus = 'Activo' 
                 AND MONTH(u.AgregadoEl) = MONTH(CURRENT_DATE()) 
                 AND YEAR(u.AgregadoEl) = YEAR(CURRENT_DATE())";
// Aplicar filtros adicionales si existen
if (!empty($tipo)) {
    $sql_reciente .= " AND t.TipoUsuario = '" . mysqli_real_escape_string($conn, $tipo) . "'";
}
if (!empty($sucursal)) {
    $sql_reciente .= " AND s.ID_Sucursal = '" . mysqli_real_escape_string($conn, $sucursal) . "'";
}
$result_reciente = $conn->query($sql_reciente);
if ($result_reciente && $row = $result_reciente->fetch_assoc()) {
    $stats['personalReciente'] = $row['total'];
} else {
    $stats['personalReciente'] = 0;
    error_log("Error en consulta personal reciente: " . $conn->error);
}

    // Log para depuración
    error_log("Estadísticas calculadas: " . json_encode($stats));

} catch (Exception $e) {
    error_log("Error en EstadisticasPersonalActivo.php: " . $e->getMessage());
    $stats = array(
        'error' => true,
        'message' => 'Error al cargar estadísticas: ' . $e->getMessage(),
        'totalPersonal' => 0,
        'totalAdministrativos' => 0,
        'totalSucursales' => 0,
        'personalReciente' => 0
    );
}

echo json_encode($stats);
?> 