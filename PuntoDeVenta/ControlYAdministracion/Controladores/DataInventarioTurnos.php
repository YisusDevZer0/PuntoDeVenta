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
                "Lote" => '-',
                "Fecha_Caducidad" => '-',
                "Lotes_Caducidad_Texto" => '-',
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

// Verificar si existen columnas Lote y Fecha_Caducidad en Inventario_Turnos_Productos
$tiene_lote_caducidad = false;
$chk_l = $conn->query("SHOW COLUMNS FROM Inventario_Turnos_Productos LIKE 'Lote'");
$chk_f = $conn->query("SHOW COLUMNS FROM Inventario_Turnos_Productos LIKE 'Fecha_Caducidad'");
if ($chk_l && $chk_l->num_rows > 0 && $chk_f && $chk_f->num_rows > 0) {
    $tiene_lote_caducidad = true;
}

$extra_select = $tiene_lote_caducidad ? ", itp.Lote, itp.Fecha_Caducidad" : "";

// Subconsulta: productos ya contados en OTRO turno (misma sucursal, mismo día)
$sql_ya_contado = "SELECT itp2.ID_Prod_POS, itp2.Fk_sucursal, it2.Folio_Turno
    FROM Inventario_Turnos_Productos itp2
    INNER JOIN Inventario_Turnos it2 ON itp2.ID_Turno = it2.ID_Turno
    WHERE itp2.Estado = 'completado'
      AND it2.ID_Turno != ?
      AND it2.Fk_sucursal = ?
      AND DATE(it2.Fecha_Turno) = CURDATE()";

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
        WHEN ya_contado.ID_Prod_POS IS NOT NULL THEN 'ya_contado_otro_turno'
        ELSE 'disponible'
    END as Estado_Producto,
    itp.ID_Registro,
    itp.Existencias_Fisicas,
    itp.Diferencia,
    itp.Usuario_Selecciono,
    ipb.Usuario_Bloqueo,
    ya_contado.Folio_Turno as Folio_Turno_Anterior
    " . $extra_select . "
FROM Stock_POS sp
INNER JOIN Sucursales s ON sp.Fk_sucursal = s.ID_Sucursal
LEFT JOIN Inventario_Turnos_Productos itp ON sp.ID_Prod_POS = itp.ID_Prod_POS 
    AND sp.Fk_sucursal = itp.Fk_sucursal 
    AND itp.ID_Turno = ?
LEFT JOIN Inventario_Productos_Bloqueados ipb ON sp.ID_Prod_POS = ipb.ID_Prod_POS 
    AND sp.Fk_sucursal = ipb.Fk_sucursal 
    AND ipb.ID_Turno = ?
    AND ipb.Estado = 'bloqueado'
LEFT JOIN ($sql_ya_contado) ya_contado ON sp.ID_Prod_POS = ya_contado.ID_Prod_POS AND sp.Fk_sucursal = ya_contado.Fk_sucursal
WHERE sp.Fk_sucursal = ?";

$params = [$id_turno, $turno['Fk_sucursal'], $turno['Usuario_Actual'], $id_turno, $id_turno, $turno['Fk_sucursal']];
$types = "iisiii";

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
                  AND (ipb.ID_Bloqueo IS NULL OR ipb.Usuario_Bloqueo = ?)
                  AND ya_contado.ID_Prod_POS IS NULL";
        $params[] = $turno['Usuario_Actual'];
        $types .= "s";
    } elseif ($filtro_estado == 'en_proceso') {
        $sql .= " AND itp.ID_Registro IS NOT NULL 
                  AND (itp.Estado = 'seleccionado' OR itp.Estado = 'en_conteo')";
    } elseif ($filtro_estado == 'bloqueado') {
        $sql .= " AND ipb.ID_Bloqueo IS NOT NULL AND ipb.Usuario_Bloqueo != ? AND ipb.Estado = 'bloqueado'";
        $params[] = $turno['Usuario_Actual'];
        $types .= "s";
    } elseif ($filtro_estado == 'completado') {
        $sql .= " AND itp.Estado = 'completado'";
    } elseif ($filtro_estado == 'ya_contado_otro_turno') {
        $sql .= " AND ya_contado.ID_Prod_POS IS NOT NULL";
    }
}

$sql .= " ORDER BY sp.Nombre_Prod ASC LIMIT 500";

// Preparar y ejecutar consulta
$stmt = $conn->prepare($sql);
$data = [];
$pares_producto_sucursal = []; // (ID_Prod_POS, Fk_sucursal) para consultar Historial_Lotes

if ($stmt) {
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($fila = $result->fetch_assoc()) {
        $estado = $fila['Estado_Producto'];
        $clase_fila = '';
        $badge_estado = '';
        $botones = '';
        
        $id_prod = (int) $fila['ID_Prod_POS'];
        $fk_suc = (int) $fila['Fk_sucursal'];
        $key_lotes = $id_prod . '_' . $fk_suc;
        $pares_producto_sucursal[$key_lotes] = [$id_prod, $fk_suc];
        
        if ($estado == 'bloqueado_otro') {
            $clase_fila = 'producto-bloqueado';
            $badge_estado = '<span class="badge bg-warning">Bloqueado por: ' . htmlspecialchars($fila['Usuario_Bloqueo']) . '</span>';
            $botones = '<button class="btn btn-sm btn-secondary" disabled><i class="fa-solid fa-lock"></i> No disponible</button>';
        } elseif ($estado == 'ya_contado_otro_turno') {
            $folio_ant = htmlspecialchars($fila['Folio_Turno_Anterior'] ?? '');
            $clase_fila = 'producto-ya-contado-otro';
            $badge_estado = '<span class="badge bg-secondary">Ya contado en turno ' . $folio_ant . ' hoy</span>';
            $botones = '<button class="btn btn-sm btn-secondary" disabled><i class="fa-solid fa-ban"></i> No disponible (otro turno)</button>';
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
                        data-producto="' . $fila['ID_Prod_POS'] . '"
                        data-fk-sucursal="' . $fk_suc . '">
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
        
        $row_data = [
            "Cod_Barra" => $fila["Cod_Barra"],
            "Nombre_Prod" => $fila["Nombre_Prod"],
            "Existencias_R" => number_format($fila["Existencias_R"]),
            "Existencias_Fisicas" => $fila["Existencias_Fisicas"] !== null ? number_format($fila["Existencias_Fisicas"]) : '-',
            "Diferencia" => $fila["Diferencia"] !== null ? number_format($fila["Diferencia"]) : '-',
            "Estado" => $badge_estado,
            "Sucursal" => $fila["Nombre_Sucursal"],
            "Acciones" => $botones,
            "clase_fila" => $clase_fila,
            "ID_Prod_POS" => $id_prod,
            "Fk_sucursal" => $fk_suc,
            "ID_Registro" => isset($fila["ID_Registro"]) ? (int) $fila["ID_Registro"] : null
        ];
        if ($tiene_lote_caducidad) {
            $row_data["Lote"] = isset($fila["Lote"]) && $fila["Lote"] !== null && $fila["Lote"] !== '' ? htmlspecialchars($fila["Lote"]) : '-';
            $row_data["Fecha_Caducidad"] = isset($fila["Fecha_Caducidad"]) && $fila["Fecha_Caducidad"] ? date('d/m/Y', strtotime($fila["Fecha_Caducidad"])) : '-';
        }
        $data[] = $row_data;
    }
    
    $stmt->close();
}

// Obtener lotes y fechas de caducidad de Historial_Lotes por producto/sucursal
$lotes_por_producto = [];
if (!empty($pares_producto_sucursal)) {
    $chk_hl = $conn->query("SHOW TABLES LIKE 'Historial_Lotes'");
    if ($chk_hl && $chk_hl->num_rows > 0) {
        $placeholders = [];
        $tipos = '';
        $vals = [];
        foreach ($pares_producto_sucursal as $par) {
            $placeholders[] = '(ID_Prod_POS = ? AND Fk_sucursal = ?)';
            $tipos .= 'ii';
            $vals[] = $par[0];
            $vals[] = $par[1];
        }
        $sql_hl = "SELECT ID_Prod_POS, Fk_sucursal, Lote, Fecha_Caducidad, Existencias 
                   FROM Historial_Lotes 
                   WHERE Existencias > 0 
                     AND Lote IS NOT NULL AND TRIM(Lote) != '' 
                     AND Fecha_Caducidad IS NOT NULL AND Fecha_Caducidad > '1900-01-01' 
                     AND (" . implode(' OR ', $placeholders) . ") 
                   ORDER BY Fecha_Caducidad ASC";
        $stmt_hl = $conn->prepare($sql_hl);
        if ($stmt_hl) {
            $stmt_hl->bind_param($tipos, ...$vals);
            $stmt_hl->execute();
            $res_hl = $stmt_hl->get_result();
            while ($hl = $res_hl->fetch_assoc()) {
                $k = $hl['ID_Prod_POS'] . '_' . $hl['Fk_sucursal'];
                if (!isset($lotes_por_producto[$k])) {
                    $lotes_por_producto[$k] = [];
                }
                $lotes_por_producto[$k][] = [
                    'Lote' => $hl['Lote'],
                    'Fecha_Caducidad' => $hl['Fecha_Caducidad'],
                    'Existencias' => (int) $hl['Existencias']
                ];
            }
            $stmt_hl->close();
        }
    }
}

// Añadir columna de lotes/caducidad y datos para el modal de conteo
foreach ($data as &$row) {
    $key = $row['ID_Prod_POS'] . '_' . $row['Fk_sucursal'];
    $lotes = isset($lotes_por_producto[$key]) ? $lotes_por_producto[$key] : [];
    $row['Lotes_Producto'] = $lotes;
    $row['Lotes_Caducidad_Texto'] = '-';
    if (!empty($lotes)) {
        $partes = [];
        foreach ($lotes as $l) {
            $fecha_f = $l['Fecha_Caducidad'] ? date('d/m/Y', strtotime($l['Fecha_Caducidad'])) : '-';
            $partes[] = $l['Lote'] . ' (' . $fecha_f . ') · ' . $l['Existencias'] . ' und';
        }
        $row['Lotes_Caducidad_Texto'] = implode(' | ', $partes);
    }
    // Pasar lotes en el botón Contar para el modal (solo si es btn-contar-producto)
    if (strpos($row['Acciones'], 'btn-contar-producto') !== false && isset($row['ID_Registro'])) {
        $lotes_json = htmlspecialchars(json_encode($lotes), ENT_QUOTES, 'UTF-8');
        $row['Acciones'] = '<button class="btn btn-sm btn-info btn-contar-producto" 
                        data-id="' . $row['ID_Registro'] . '"
                        data-producto="' . $row['ID_Prod_POS'] . '"
                        data-fk-sucursal="' . $row['Fk_sucursal'] . '"
                        data-lotes="' . $lotes_json . '">
                        <i class="fa-solid fa-calculator"></i> Contar
                    </button>';
    }
}
unset($row);

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
