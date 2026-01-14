<?php
header('Content-Type: application/json');
include("db_connect.php");
include_once "ControladorUsuario.php";

// Obtener parámetros de filtro
$filtro_codigo = isset($_GET['codigo']) ? trim($_GET['codigo']) : '';
$filtro_sucursal = isset($_GET['sucursal']) ? (int)$_GET['sucursal'] : 0;
$filtro_estado = isset($_GET['estado']) ? $_GET['estado'] : '';

// Construir consulta base
$sql = "SELECT 
    hl.ID_Historial,
    hl.ID_Prod_POS,
    hl.Fk_sucursal,
    hl.Lote,
    hl.Fecha_Caducidad,
    hl.Fecha_Ingreso,
    hl.Existencias,
    hl.Fecha_Registro,
    hl.Usuario_Modifico,
    sp.Cod_Barra,
    sp.Nombre_Prod,
    s.Nombre_Sucursal,
    DATEDIFF(hl.Fecha_Caducidad, CURDATE()) as Dias_restantes,
    CASE 
        WHEN DATEDIFF(hl.Fecha_Caducidad, CURDATE()) < 0 THEN 'vencido'
        WHEN DATEDIFF(hl.Fecha_Caducidad, CURDATE()) <= 15 THEN 'proximo'
        ELSE 'ok'
    END as Estado_Caducidad
FROM Historial_Lotes hl
INNER JOIN Stock_POS sp ON hl.ID_Prod_POS = sp.ID_Prod_POS AND hl.Fk_sucursal = sp.Fk_sucursal
INNER JOIN Sucursales s ON hl.Fk_sucursal = s.ID_Sucursal
WHERE hl.Existencias > 0";

$params = [];
$types = "";

// Aplicar filtros
if (!empty($filtro_codigo)) {
    $sql .= " AND sp.Cod_Barra LIKE ?";
    $params[] = "%$filtro_codigo%";
    $types .= "s";
}

if ($filtro_sucursal > 0) {
    $sql .= " AND hl.Fk_sucursal = ?";
    $params[] = $filtro_sucursal;
    $types .= "i";
}

if (!empty($filtro_estado)) {
    if ($filtro_estado == 'proximo') {
        $sql .= " AND DATEDIFF(hl.Fecha_Caducidad, CURDATE()) BETWEEN 0 AND 15";
    } elseif ($filtro_estado == 'vencido') {
        $sql .= " AND DATEDIFF(hl.Fecha_Caducidad, CURDATE()) < 0";
    } elseif ($filtro_estado == 'ok') {
        $sql .= " AND DATEDIFF(hl.Fecha_Caducidad, CURDATE()) > 15";
    }
}

$sql .= " ORDER BY hl.Fecha_Caducidad ASC, hl.Existencias DESC";

// Preparar y ejecutar consulta
$stmt = $conn->prepare($sql);
$data = [];

if ($stmt) {
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($fila = $result->fetch_assoc()) {
        // Determinar badge de estado
        $badge_class = '';
        $badge_text = '';
        if ($fila['Estado_Caducidad'] == 'vencido') {
            $badge_class = 'badge-vencido';
            $badge_text = 'Vencido';
        } elseif ($fila['Estado_Caducidad'] == 'proximo') {
            $badge_class = 'badge-proximo';
            $badge_text = 'Próximo a vencer';
        } else {
            $badge_class = 'badge-ok';
            $badge_text = 'Vigente';
        }
        
        $data[] = [
            "ID_Historial" => $fila["ID_Historial"],
            "Cod_Barra" => $fila["Cod_Barra"],
            "Nombre_Prod" => $fila["Nombre_Prod"],
            "Lote" => $fila["Lote"],
            "Fecha_Caducidad" => date('d/m/Y', strtotime($fila["Fecha_Caducidad"])),
            "Fecha_Ingreso" => date('d/m/Y', strtotime($fila["Fecha_Ingreso"])),
            "Existencias" => number_format($fila["Existencias"]),
            "Dias_restantes" => $fila["Dias_restantes"],
            "Estado" => "<span class='badge badge-caducidad $badge_class'>$badge_text</span>",
            "Sucursal" => $fila["Nombre_Sucursal"],
            "Usuario_Modifico" => $fila["Usuario_Modifico"],
            "Fecha_Registro" => date('d/m/Y H:i', strtotime($fila["Fecha_Registro"])),
            "Editar" => "<button class='btn btn-sm btn-primary btn-editar-lote' data-id='{$fila['ID_Historial']}' title='Editar lote'>
                <i class='fa-solid fa-edit'></i>
            </button>"
        ];
    }
    
    $stmt->close();
}

// Construir respuesta JSON
$results = [
    "sEcho" => 1,
    "iTotalRecords" => count($data),
    "iTotalDisplayRecords" => count($data),
    "aaData" => $data
];

echo json_encode($results);
$conn->close();
?>
