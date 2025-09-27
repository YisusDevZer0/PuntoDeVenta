<?php
header('Content-Type: application/json');
include_once "../dbconect.php";

try {
    // Consulta para obtener productos próximos a caducar
    $sql = "SELECT 
                plc.id_lote,
                plc.cod_barra,
                plc.nombre_producto,
                plc.lote,
                plc.fecha_caducidad,
                plc.cantidad_actual,
                plc.estado,
                plc.sucursal_id,
                plc.proveedor,
                plc.precio_compra,
                plc.precio_venta,
                plc.fecha_registro,
                plc.observaciones,
                s.Nombre as sucursal,
                DATEDIFF(plc.fecha_caducidad, CURDATE()) as dias_restantes
            FROM productos_lotes_caducidad plc
            LEFT JOIN Sucursales s ON plc.sucursal_id = s.id
            WHERE plc.estado = 'activo' 
            AND plc.cantidad_actual > 0
            ORDER BY plc.fecha_caducidad ASC";

    $result = $con->query($sql);
    
    if (!$result) {
        throw new Exception('Error en la consulta: ' . $con->error);
    }

    $productos = [];
    
    while ($row = $result->fetch_assoc()) {
        // Calcular tipo de alerta basado en días restantes
        $diasRestantes = $row['dias_restantes'];
        $tipoAlerta = '';
        
        if ($diasRestantes < 0) {
            $tipoAlerta = 'vencido';
        } elseif ($diasRestantes <= 90) {
            $tipoAlerta = '3_meses';
        } elseif ($diasRestantes <= 180) {
            $tipoAlerta = '6_meses';
        } elseif ($diasRestantes <= 270) {
            $tipoAlerta = '9_meses';
        } else {
            $tipoAlerta = 'normal';
        }
        
        // Generar badges para estado y alerta
        $estadoBadge = generarBadgeEstado($row['estado']);
        $alertaBadge = generarBadgeAlerta($tipoAlerta, $diasRestantes);
        
        $productos[] = [
            'id_lote' => $row['id_lote'],
            'cod_barra' => $row['cod_barra'],
            'nombre_producto' => $row['nombre_producto'],
            'lote' => $row['lote'],
            'fecha_caducidad' => $row['fecha_caducidad'],
            'cantidad_actual' => $row['cantidad_actual'],
            'estado' => $estadoBadge,
            'alerta' => $alertaBadge,
            'sucursal' => $row['sucursal'],
            'sucursal_id' => $row['sucursal_id'],
            'proveedor' => $row['proveedor'],
            'precio_compra' => $row['precio_compra'],
            'precio_venta' => $row['precio_venta'],
            'fecha_registro' => $row['fecha_registro'],
            'observaciones' => $row['observaciones'],
            'dias_restantes' => $diasRestantes,
            'tipo_alerta' => $tipoAlerta
        ];
    }
    
    // Calcular estadísticas
    $estadisticas = [
        'total' => count($productos),
        'alerta_3_meses' => count(array_filter($productos, function($p) { return $p['tipo_alerta'] === '3_meses'; })),
        'alerta_6_meses' => count(array_filter($productos, function($p) { return $p['tipo_alerta'] === '6_meses'; })),
        'alerta_9_meses' => count(array_filter($productos, function($p) { return $p['tipo_alerta'] === '9_meses'; })),
        'vencidos' => count(array_filter($productos, function($p) { return $p['tipo_alerta'] === 'vencido'; }))
    ];
    
    echo json_encode([
        'success' => true,
        'productos' => $productos,
        'estadisticas' => $estadisticas
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}

function generarBadgeEstado($estado) {
    $badges = [
        'activo' => '<span class="badge bg-success">Activo</span>',
        'inactivo' => '<span class="badge bg-secondary">Inactivo</span>',
        'agotado' => '<span class="badge bg-danger">Agotado</span>',
        'vencido' => '<span class="badge bg-dark">Vencido</span>'
    ];
    
    return $badges[$estado] ?? '<span class="badge bg-secondary">' . ucfirst($estado) . '</span>';
}

function generarBadgeAlerta($tipoAlerta, $diasRestantes) {
    if ($diasRestantes < 0) {
        return '<span class="badge bg-danger">Vencido</span>';
    }
    
    $badges = [
        '3_meses' => '<span class="badge bg-warning">3 Meses</span>',
        '6_meses' => '<span class="badge bg-info">6 Meses</span>',
        '9_meses' => '<span class="badge bg-secondary">9 Meses</span>',
        'normal' => '<span class="badge bg-success">Normal</span>'
    ];
    
    return $badges[$tipoAlerta] ?? '<span class="badge bg-light text-dark">Normal</span>';
}

function generarBotonesAccion($row) {
    // Crear un array con solo los datos necesarios para evitar problemas de escape
    $datosLote = [
        'id_lote' => $row['id_lote'],
        'cod_barra' => $row['cod_barra'],
        'nombre_producto' => $row['nombre_producto'],
        'lote' => $row['lote'],
        'fecha_caducidad' => $row['fecha_caducidad'],
        'cantidad_actual' => $row['cantidad_actual'],
        'sucursal' => $row['sucursal'],
        'sucursal_id' => $row['sucursal_id'] ?? 1
    ];

    $productoJson = htmlspecialchars(json_encode($datosLote), ENT_QUOTES, 'UTF-8');

    return "
        <div class='btn-group' role='group'>
            <button class='btn btn-sm btn-outline-info' onclick='abrirModalDetallesLote({$row['id_lote']})' title='Ver detalles'>
                <i class='fa fa-eye'></i>
            </button>
            <button class='btn btn-sm btn-outline-warning' onclick='abrirModalActualizarCaducidad({$row['id_lote']}, `$productoJson`)' title='Actualizar fecha'>
                <i class='fa fa-edit'></i>
            </button>
            <button class='btn btn-sm btn-outline-primary' onclick='abrirModalTransferirLote({$row['id_lote']}, `$productoJson`)' title='Transferir'>
                <i class='fa fa-truck'></i>
            </button>
        </div>
    ";
}
?>
