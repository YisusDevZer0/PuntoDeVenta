<?php
header('Content-Type: application/json');
include("db_connect.php");
include_once "ControladorUsuario.php";

// Obtener parámetros
$id_turno = isset($_GET['id_turno']) ? (int)$_GET['id_turno'] : 0;
$buscar = isset($_GET['buscar']) ? trim($_GET['buscar']) : '';
$filtro_estado = isset($_GET['estado']) ? $_GET['estado'] : '';
$sucursal_usuario = isset($row['Fk_Sucursal']) ? (int)$row['Fk_Sucursal'] : 0;

// Si no hay turno, mostrar productos disponibles de la sucursal
if ($id_turno <= 0) {
    // Consulta simple para productos disponibles sin turno
    $sql = "SELECT 
        sp.ID_Prod_POS,
        sp.Cod_Barra,
        sp.Nombre_Prod,
        sp.Existencias_R,
        s.Nombre_Sucursal,
        'disponible' as Estado_Producto
    FROM Stock_POS sp
    INNER JOIN Sucursales s ON sp.Fk_sucursal = s.ID_Sucursal
    WHERE sp.Fk_sucursal = ?";
    
    $params = [$sucursal_usuario];
    $types = "i";
    
    if (!empty($buscar)) {
        $sql .= " AND (sp.Cod_Barra LIKE ? OR sp.Nombre_Prod LIKE ?)";
        $buscar_param = "%$buscar%";
        $params[] = $buscar_param;
        $params[] = $buscar_param;
        $types .= "ss";
    }
    
    $sql .= " ORDER BY sp.Nombre_Prod ASC LIMIT 500";
    
    $stmt = $conn->prepare($sql);
    $data = [];
    
    if ($stmt && $sucursal_usuario > 0) {
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
        
        while ($fila = $result->fetch_assoc()) {
            $data[] = [
                "Cod_Barra" => $fila["Cod_Barra"],
                "Nombre_Prod" => $fila["Nombre_Prod"],
                "Existencias_R" => number_format($fila["Existencias_R"]),
                "Existencias_Fisicas" => '-',
                "Diferencia" => '-',
                "Estado" => '<span class="badge bg-info">Disponible (Sin turno activo)</span>',
                "Sucursal" => $fila["Nombre_Sucursal"],
                "Acciones" => '<button class="btn btn-sm btn-secondary" disabled><i class="fa-solid fa-info-circle"></i> Inicia un turno para seleccionar</button>',
                "clase_fila" => 'producto-disponible'
            ];
        }
        
        $stmt->close();
    }
    
    echo json_encode([
        "sEcho" => 1,
        "iTotalRecords" => count($data),
        "iTotalDisplayRecords" => count($data),
        "aaData" => $data
    ]);
    exit;
}

// Obtener información del turno
$sql_turno = "SELECT * FROM Inventario_Turnos WHERE ID_Turno = ?";
$stmt_turno = $conn->prepare($sql_turno);
$stmt_turno->bind_param("i", $id_turno);
$stmt_turno->execute();
$turno = $stmt_turno->get_result()->fetch_assoc();
$stmt_turno->close();

if (!$turno) {
    echo json_encode([
        "sEcho" => 1,
        "iTotalRecords" => 0,
        "iTotalDisplayRecords" => 0,
        "aaData" => []
    ]);
    exit;
}

// Construir consulta base para productos disponibles (no bloqueados por otros usuarios)
$sql = "SELECT 
    sp.Folio_Prod_Stock,
    sp.ID_Prod_POS,
    sp.Cod_Barra,
    sp.Nombre_Prod,
    sp.Fk_sucursal,
    sp.Existencias_R,
    s.Nombre_Sucursal,
    CASE 
        WHEN ipb.ID_Bloqueo IS NOT NULL AND ipb.Usuario_Bloqueo != ? AND ipb.Estado = 'bloqueado' THEN 'bloqueado_otro'
        WHEN itp.ID_Registro IS NOT NULL THEN itp.Estado
        ELSE 'disponible'
    END as Estado_Producto,
    itp.ID_Registro,
    itp.Existencias_Fisicas,
    itp.Diferencia,
    itp.Usuario_Selecciono,
    ipb.Usuario_Bloqueo
FROM Stock_POS sp
INNER JOIN Sucursales s ON sp.Fk_sucursal = s.ID_Sucursal
LEFT JOIN Inventario_Turnos_Productos itp ON sp.ID_Prod_POS = itp.ID_Prod_POS 
    AND sp.Fk_sucursal = itp.Fk_sucursal 
    AND itp.ID_Turno = ?
LEFT JOIN Inventario_Productos_Bloqueados ipb ON sp.ID_Prod_POS = ipb.ID_Prod_POS 
    AND sp.Fk_sucursal = ipb.Fk_sucursal 
    AND ipb.ID_Turno = ?
    AND ipb.Estado = 'bloqueado'
WHERE sp.Fk_sucursal = ?";

$params = [$turno['Usuario_Actual'], $id_turno, $id_turno, $turno['Fk_sucursal']];
$types = "siii";

// Aplicar filtros
if (!empty($buscar)) {
    $sql .= " AND (sp.Cod_Barra LIKE ? OR sp.Nombre_Prod LIKE ?)";
    $buscar_param = "%$buscar%";
    $params[] = $buscar_param;
    $params[] = $buscar_param;
    $types .= "ss";
}

if (!empty($filtro_estado)) {
    if ($filtro_estado == 'disponible') {
        $sql .= " AND (itp.ID_Registro IS NULL OR itp.Estado = 'liberado') 
                  AND (ipb.ID_Bloqueo IS NULL OR ipb.Usuario_Bloqueo = ?)";
        $params[] = $turno['Usuario_Actual'];
        $types .= "s";
    } elseif ($filtro_estado == 'bloqueado') {
        $sql .= " AND ipb.ID_Bloqueo IS NOT NULL AND ipb.Usuario_Bloqueo != ? AND ipb.Estado = 'bloqueado'";
        $params[] = $turno['Usuario_Actual'];
        $types .= "s";
    } elseif ($filtro_estado == 'completado') {
        $sql .= " AND itp.Estado = 'completado'";
    }
}

$sql .= " ORDER BY sp.Nombre_Prod ASC LIMIT 500";

// Preparar y ejecutar consulta
$stmt = $conn->prepare($sql);
$data = [];

if ($stmt) {
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($fila = $result->fetch_assoc()) {
        $estado = $fila['Estado_Producto'];
        $clase_fila = '';
        $badge_estado = '';
        $botones = '';
        
        if ($estado == 'bloqueado_otro') {
            $clase_fila = 'producto-bloqueado';
            $badge_estado = '<span class="badge bg-warning">Bloqueado por: ' . htmlspecialchars($fila['Usuario_Bloqueo']) . '</span>';
            $botones = '<button class="btn btn-sm btn-secondary" disabled><i class="fa-solid fa-lock"></i> No disponible</button>';
        } elseif ($estado == 'disponible') {
            $clase_fila = 'producto-disponible';
            $badge_estado = '<span class="badge bg-success">Disponible</span>';
            $botones = '<button class="btn btn-sm btn-primary btn-seleccionar-producto" 
                        data-id="' . $fila['ID_Prod_POS'] . '" 
                        data-codigo="' . htmlspecialchars($fila['Cod_Barra']) . '">
                        <i class="fa-solid fa-hand-pointer"></i> Seleccionar
                    </button>';
        } elseif ($estado == 'seleccionado' || $estado == 'en_conteo') {
            $clase_fila = 'table-warning';
            $badge_estado = '<span class="badge bg-warning">En proceso</span>';
            $botones = '<button class="btn btn-sm btn-info btn-contar-producto" 
                        data-id="' . $fila['ID_Registro'] . '"
                        data-producto="' . $fila['ID_Prod_POS'] . '">
                        <i class="fa-solid fa-calculator"></i> Contar
                    </button>';
        } elseif ($estado == 'completado') {
            $clase_fila = 'table-success';
            $badge_estado = '<span class="badge bg-success">Completado</span>';
            $botones = '<button class="btn btn-sm btn-secondary btn-ver-detalle" 
                        data-id="' . $fila['ID_Registro'] . '">
                        <i class="fa-solid fa-eye"></i> Ver
                    </button>';
        }
        
        $data[] = [
            "Cod_Barra" => $fila["Cod_Barra"],
            "Nombre_Prod" => $fila["Nombre_Prod"],
            "Existencias_R" => number_format($fila["Existencias_R"]),
            "Existencias_Fisicas" => $fila["Existencias_Fisicas"] !== null ? number_format($fila["Existencias_Fisicas"]) : '-',
            "Diferencia" => $fila["Diferencia"] !== null ? number_format($fila["Diferencia"]) : '-',
            "Estado" => $badge_estado,
            "Sucursal" => $fila["Nombre_Sucursal"],
            "Acciones" => $botones,
            "clase_fila" => $clase_fila
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
