<?php
include_once "db_connect.php";

// Obtener parámetros de filtro
$filtroSucursal = isset($_GET['sucursal']) ? $_GET['sucursal'] : '';
$filtroUsuario = isset($_GET['usuario']) ? $_GET['usuario'] : '';
$fechaDesde = isset($_GET['fechaDesde']) ? $_GET['fechaDesde'] : '';
$fechaHasta = isset($_GET['fechaHasta']) ? $_GET['fechaHasta'] : '';

// Construir consulta base para conteos pausados
$sqlPausados = "SELECT 
    COUNT(*) as totalPausados,
    COUNT(DISTINCT Fk_sucursal) as sucursalesAfectadas,
    SUM(CASE WHEN TIMESTAMPDIFF(HOUR, AgregadoEl, NOW()) > 24 THEN 1 ELSE 0 END) as conteosAntiguos
FROM ConteosDiarios 
WHERE EnPausa = 1";

$params = [];
$types = "";

// Aplicar filtros
if (!empty($filtroSucursal)) {
    $sqlPausados .= " AND Fk_sucursal = ?";
    $params[] = $filtroSucursal;
    $types .= "i";
}

if (!empty($filtroUsuario)) {
    $sqlPausados .= " AND AgregadoPor = ?";
    $params[] = $filtroUsuario;
    $types .= "s";
}

if (!empty($fechaDesde)) {
    $sqlPausados .= " AND DATE(AgregadoEl) >= ?";
    $params[] = $fechaDesde;
    $types .= "s";
}

if (!empty($fechaHasta)) {
    $sqlPausados .= " AND DATE(AgregadoEl) <= ?";
    $params[] = $fechaHasta;
    $types .= "s";
}

// Consulta para conteos reanudados hoy
$sqlReanudados = "SELECT COUNT(*) as reanudadosHoy
FROM ConteosDiarios 
WHERE EnPausa = 0 
AND DATE(AgregadoEl) = CURDATE()";

if (!empty($filtroSucursal)) {
    $sqlReanudados .= " AND Fk_sucursal = ?";
}

if (!empty($filtroUsuario)) {
    $sqlReanudados .= " AND AgregadoPor = ?";
}

if (!empty($fechaDesde)) {
    $sqlReanudados .= " AND DATE(AgregadoEl) >= ?";
}

if (!empty($fechaHasta)) {
    $sqlReanudados .= " AND DATE(AgregadoEl) <= ?";
}

// Preparar y ejecutar consulta de conteos pausados
$stmt = $conn->prepare($sqlPausados);
if ($stmt) {
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $resultPausados = $stmt->get_result();
    $statsPausados = $resultPausados->fetch_assoc();
    $stmt->close();
} else {
    $statsPausados = ['totalPausados' => 0, 'sucursalesAfectadas' => 0, 'conteosAntiguos' => 0];
}

// Preparar y ejecutar consulta de conteos reanudados
$stmt2 = $conn->prepare($sqlReanudados);
if ($stmt2) {
    if (!empty($params)) {
        $stmt2->bind_param($types, ...$params);
    }
    $stmt2->execute();
    $resultReanudados = $stmt2->get_result();
    $statsReanudados = $resultReanudados->fetch_assoc();
    $stmt2->close();
} else {
    $statsReanudados = ['reanudadosHoy' => 0];
}

// Preparar respuesta
$response = [
    'success' => true,
    'totalPausados' => $statsPausados['totalPausados'],
    'sucursalesAfectadas' => $statsPausados['sucursalesAfectadas'],
    'conteosAntiguos' => $statsPausados['conteosAntiguos'],
    'reanudadosHoy' => $statsReanudados['reanudadosHoy']
];

header('Content-Type: application/json');
echo json_encode($response);

$conn->close();
?>
