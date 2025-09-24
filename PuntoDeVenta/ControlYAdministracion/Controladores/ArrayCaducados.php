<?php
header('Content-Type: application/json');
include_once "../dbconect.php";

try {
    // Obtener parámetros de DataTables
    $draw = intval($_GET['draw'] ?? 1);
    $start = intval($_GET['start'] ?? 0);
    $length = intval($_GET['length'] ?? 10);
    $searchValue = $_GET['search']['value'] ?? '';
    
    // Construir consulta base
    $sql = "SELECT 
                plc.id_lote,
                plc.cod_barra,
                plc.nombre_producto,
                plc.lote,
                plc.fecha_caducidad,
                plc.cantidad_actual,
                plc.estado,
                s.Nombre_Sucursal as sucursal,
                plc.proveedor,
                plc.precio_compra,
                plc.precio_venta,
                plc.fecha_registro,
                DATEDIFF(plc.fecha_caducidad, CURDATE()) as dias_restantes,
                CASE 
                    WHEN DATEDIFF(plc.fecha_caducidad, CURDATE()) < 0 THEN 'vencido'
                    WHEN DATEDIFF(plc.fecha_caducidad, CURDATE()) <= 90 THEN '3_meses'
                    WHEN DATEDIFF(plc.fecha_caducidad, CURDATE()) <= 180 THEN '6_meses'
                    WHEN DATEDIFF(plc.fecha_caducidad, CURDATE()) <= 270 THEN '9_meses'
                    ELSE 'normal'
                END as tipo_alerta
            FROM productos_lotes_caducidad plc
            LEFT JOIN Sucursales s ON plc.sucursal_id = s.Id_Sucursal
            WHERE plc.estado != 'retirado'";
    
    // Agregar filtros de búsqueda
    if (!empty($searchValue)) {
        $sql .= " AND (plc.cod_barra LIKE '%$searchValue%' 
                      OR plc.nombre_producto LIKE '%$searchValue%' 
                      OR plc.lote LIKE '%$searchValue%'
                      OR s.Nombre_Sucursal LIKE '%$searchValue%')";
    }
    
    // Contar total de registros
    $countSql = "SELECT COUNT(*) as total FROM productos_lotes_caducidad plc 
                 LEFT JOIN Sucursales s ON plc.sucursal_id = s.Id_Sucursal 
                 WHERE plc.estado != 'retirado'";
    
    if (!empty($searchValue)) {
        $countSql .= " AND (plc.cod_barra LIKE '%$searchValue%' 
                          OR plc.nombre_producto LIKE '%$searchValue%' 
                          OR plc.lote LIKE '%$searchValue%'
                          OR s.Nombre_Sucursal LIKE '%$searchValue%')";
    }
    
    $countResult = $con->query($countSql);
    $totalRecords = $countResult->fetch_assoc()['total'];
    
    // Agregar ordenamiento y límites
    $sql .= " ORDER BY plc.fecha_caducidad ASC LIMIT $start, $length";
    
    $result = $con->query($sql);
    $data = [];
    
    while ($row = $result->fetch_assoc()) {
        // Generar badges
        $estadoBadge = generarBadgeEstado($row['estado']);
        $alertaBadge = generarBadgeAlerta($row['tipo_alerta'], $row['dias_restantes']);
        
        // Generar botones de acción
        $acciones = generarBotonesAccion($row);
        
        $data[] = [
            'cod_barra' => $row['cod_barra'],
            'nombre_producto' => $row['nombre_producto'],
            'lote' => $row['lote'],
            'fecha_caducidad' => $row['fecha_caducidad'],
            'cantidad_actual' => $row['cantidad_actual'],
            'sucursal' => $row['sucursal'],
            'estado' => $estadoBadge,
            'alerta' => $alertaBadge,
            'acciones' => $acciones,
            'dias_restantes' => $row['dias_restantes'],
            'proveedor' => $row['proveedor'],
            'id_lote' => $row['id_lote']
        ];
    }
    
    echo json_encode([
        'draw' => $draw,
        'recordsTotal' => $totalRecords,
        'recordsFiltered' => $totalRecords,
        'data' => $data
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'draw' => 1,
        'recordsTotal' => 0,
        'recordsFiltered' => 0,
        'data' => [],
        'error' => $e->getMessage()
    ]);
}

function generarBadgeEstado($estado) {
    $estados = [
        'activo' => ['class' => 'bg-success', 'text' => 'Activo'],
        'agotado' => ['class' => 'bg-warning', 'text' => 'Agotado'],
        'vencido' => ['class' => 'bg-danger', 'text' => 'Vencido'],
        'retirado' => ['class' => 'bg-secondary', 'text' => 'Retirado']
    ];
    
    $estadoInfo = $estados[$estado] ?? ['class' => 'bg-secondary', 'text' => $estado];
    return "<span class='badge {$estadoInfo['class']}'>{$estadoInfo['text']}</span>";
}

function generarBadgeAlerta($tipoAlerta, $diasRestantes) {
    if ($diasRestantes < 0) {
        return '<span class="badge bg-danger">VENCIDO</span>';
    }
    
    $alertas = [
        '3_meses' => ['class' => 'bg-warning', 'text' => '3 MESES'],
        '6_meses' => ['class' => 'bg-danger', 'text' => '6 MESES'],
        '9_meses' => ['class' => 'bg-info', 'text' => '9 MESES'],
        'normal' => ['class' => 'bg-success', 'text' => 'NORMAL']
    ];
    
    $alertaInfo = $alertas[$tipoAlerta] ?? ['class' => 'bg-secondary', 'text' => $tipoAlerta];
    return "<span class='badge {$alertaInfo['class']}'>{$alertaInfo['text']}</span>";
}

function generarBotonesAccion($row) {
    $productoJson = htmlspecialchars(json_encode($row), ENT_QUOTES, 'UTF-8');
    
    return "
        <div class='btn-group' role='group'>
            <button class='btn btn-sm btn-outline-info' onclick='abrirModalDetallesLote({$row['id_lote']})' title='Ver detalles'>
                <i class='fa fa-eye'></i>
            </button>
            <button class='btn btn-sm btn-outline-warning' onclick='abrirModalActualizarCaducidad({$row['id_lote']}, \"$productoJson\")' title='Actualizar fecha'>
                <i class='fa fa-edit'></i>
            </button>
            <button class='btn btn-sm btn-outline-primary' onclick='abrirModalTransferirLote({$row['id_lote']}, \"$productoJson\")' title='Transferir'>
                <i class='fa fa-truck'></i>
            </button>
        </div>
    ";
}
?>
