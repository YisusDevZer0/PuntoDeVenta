<?php
include_once "db_connect.php";

// Obtener parámetros de filtro
$filtroSucursal = isset($_GET['sucursal']) ? $_GET['sucursal'] : '';
$filtroEstado = isset($_GET['estado']) ? $_GET['estado'] : '';
$fechaDesde = isset($_GET['fechaDesde']) ? $_GET['fechaDesde'] : '';
$fechaHasta = isset($_GET['fechaHasta']) ? $_GET['fechaHasta'] : '';

// Construir consulta base para estadísticas
$sql = "SELECT 
    COUNT(*) as totalConteos,
    COUNT(DISTINCT cd.Fk_sucursal) as sucursalesEvaluadas,
    SUM(CASE 
        WHEN cd.ExistenciaFisica IS NOT NULL AND 
             ABS(COALESCE(sp.Existencias_R, 0) - cd.ExistenciaFisica) <= (COALESCE(sp.Existencias_R, 0) * 0.05) THEN 1 
        ELSE 0 
    END) as conteosExactos,
    SUM(CASE 
        WHEN cd.ExistenciaFisica IS NOT NULL AND 
             ABS(COALESCE(sp.Existencias_R, 0) - cd.ExistenciaFisica) > (COALESCE(sp.Existencias_R, 0) * 0.05) THEN 1 
        ELSE 0 
    END) as diferenciasAltas
FROM ConteosDiarios cd
LEFT JOIN Stock_POS sp ON cd.Cod_Barra = sp.Cod_Barra AND cd.Fk_sucursal = sp.Fk_sucursal
WHERE 1=1";

$params = [];
$types = "";

// Aplicar filtros
if (!empty($filtroSucursal)) {
    $sql .= " AND cd.Fk_sucursal = ?";
    $params[] = $filtroSucursal;
    $types .= "i";
}

if ($filtroEstado !== '') {
    if ($filtroEstado == '0') {
        $sql .= " AND cd.EnPausa = 0 AND cd.ExistenciaFisica IS NOT NULL";
    } elseif ($filtroEstado == '1') {
        $sql .= " AND cd.EnPausa = 1";
    }
}

if (!empty($fechaDesde)) {
    $sql .= " AND DATE(cd.AgregadoEl) >= ?";
    $params[] = $fechaDesde;
    $types .= "s";
}

if (!empty($fechaHasta)) {
    $sql .= " AND DATE(cd.AgregadoEl) <= ?";
    $params[] = $fechaHasta;
    $types .= "s";
}

// Preparar y ejecutar consulta
$stmt = $conn->prepare($sql);
if ($stmt) {
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        $response = [
            'success' => true,
            'totalConteos' => $row['totalConteos'],
            'sucursalesEvaluadas' => $row['sucursalesEvaluadas'],
            'conteosExactos' => $row['conteosExactos'],
            'diferenciasAltas' => $row['diferenciasAltas']
        ];
    } else {
        $response = [
            'success' => true,
            'totalConteos' => 0,
            'sucursalesEvaluadas' => 0,
            'conteosExactos' => 0,
            'diferenciasAltas' => 0
        ];
    }
    
    $stmt->close();
} else {
    $response = [
        'success' => false,
        'message' => 'Error al preparar la consulta: ' . $conn->error
    ];
}

header('Content-Type: application/json');
echo json_encode($response);

$conn->close();
?>
