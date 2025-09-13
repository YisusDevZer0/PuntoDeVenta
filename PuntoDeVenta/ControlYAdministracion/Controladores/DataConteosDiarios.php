<?php
include_once "db_connect.php";

// Obtener parámetros de filtro
$filtroSucursal = isset($_GET['sucursal']) ? $_GET['sucursal'] : '';
$filtroEstado = isset($_GET['estado']) ? $_GET['estado'] : '';
$fechaDesde = isset($_GET['fechaDesde']) ? $_GET['fechaDesde'] : '';
$fechaHasta = isset($_GET['fechaHasta']) ? $_GET['fechaHasta'] : '';

// Construir consulta base con comparación de stock real
$sql = "SELECT 
    cd.Folio_Ingreso,
    cd.Cod_Barra,
    cd.Nombre_Producto,
    cd.Fk_sucursal,
    s.Nombre_Sucursal,
    cd.Existencias_R as ExistenciaConteo,
    cd.ExistenciaFisica,
    cd.AgregadoPor,
    cd.AgregadoEl,
    cd.EnPausa,
    COALESCE(sp.Existencias_R, 0) as StockRealSistema,
    COALESCE(iss.StockEnMomento, 0) as StockInventarioSucursal,
    CASE 
        WHEN cd.EnPausa = 1 THEN 'En Pausa'
        WHEN cd.ExistenciaFisica IS NULL THEN 'Pendiente'
        ELSE 'Completado'
    END as Estado,
    CASE 
        WHEN cd.ExistenciaFisica IS NOT NULL AND COALESCE(sp.Existencias_R, 0) > 0 THEN 
            ROUND(((cd.ExistenciaFisica - COALESCE(sp.Existencias_R, 0)) / COALESCE(sp.Existencias_R, 0)) * 100, 2)
        ELSE NULL
    END as DiferenciaPorcentajeSistema,
    CASE 
        WHEN cd.ExistenciaFisica IS NOT NULL AND COALESCE(iss.StockEnMomento, 0) > 0 THEN 
            ROUND(((cd.ExistenciaFisica - COALESCE(iss.StockEnMomento, 0)) / COALESCE(iss.StockEnMomento, 0)) * 100, 2)
        ELSE NULL
    END as DiferenciaPorcentajeInventario,
    CASE 
        WHEN cd.ExistenciaFisica IS NOT NULL THEN 
            (cd.ExistenciaFisica - COALESCE(sp.Existencias_R, 0))
        ELSE NULL
    END as DiferenciaUnidadesSistema,
    CASE 
        WHEN cd.ExistenciaFisica IS NOT NULL THEN 
            (cd.ExistenciaFisica - COALESCE(iss.StockEnMomento, 0))
        ELSE NULL
    END as DiferenciaUnidadesInventario
FROM ConteosDiarios cd
LEFT JOIN Sucursales s ON cd.Fk_sucursal = s.ID_Sucursal
LEFT JOIN Stock_POS sp ON cd.Cod_Barra = sp.Cod_Barra AND cd.Fk_sucursal = sp.Fk_sucursal
LEFT JOIN InventariosSucursales iss ON cd.Cod_Barra = iss.Cod_Barra AND cd.Fk_sucursal = iss.Fk_Sucursal
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

$sql .= " ORDER BY cd.AgregadoEl DESC";

// Preparar y ejecutar consulta
$stmt = $conn->prepare($sql);
if ($stmt) {
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        echo '<div class="table-responsive">';
        echo '<table class="table table-striped table-hover">';
        echo '<thead class="table-dark">';
        echo '<tr>';
        echo '<th>Folio</th>';
        echo '<th>Código de Barras</th>';
        echo '<th>Producto</th>';
        echo '<th>Sucursal</th>';
        echo '<th>Stock Sistema</th>';
        echo '<th>Stock Inventario</th>';
        echo '<th>Conteo Físico</th>';
        echo '<th>Dif. vs Sistema</th>';
        echo '<th>Dif. vs Inventario</th>';
        echo '<th>Estado</th>';
        echo '<th>Agregado Por</th>';
        echo '<th>Fecha</th>';
        echo '<th>Acciones</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';
        
        while ($row = $result->fetch_assoc()) {
            $estadoClass = '';
            $estadoIcon = '';
            
            switch ($row['Estado']) {
                case 'Completado':
                    $estadoClass = 'success';
                    $estadoIcon = 'fa-check-circle';
                    break;
                case 'En Pausa':
                    $estadoClass = 'warning';
                    $estadoIcon = 'fa-pause-circle';
                    break;
                case 'Pendiente':
                    $estadoClass = 'info';
                    $estadoIcon = 'fa-clock';
                    break;
            }
            
            // Clases para diferencias con sistema
            $diferenciaSistemaClass = '';
            if ($row['DiferenciaPorcentajeSistema'] !== null) {
                if ($row['DiferenciaPorcentajeSistema'] > 5) {
                    $diferenciaSistemaClass = 'text-success';
                } elseif ($row['DiferenciaPorcentajeSistema'] < -5) {
                    $diferenciaSistemaClass = 'text-danger';
                } else {
                    $diferenciaSistemaClass = 'text-warning';
                }
            }
            
            // Clases para diferencias con inventario
            $diferenciaInventarioClass = '';
            if ($row['DiferenciaPorcentajeInventario'] !== null) {
                if ($row['DiferenciaPorcentajeInventario'] > 5) {
                    $diferenciaInventarioClass = 'text-success';
                } elseif ($row['DiferenciaPorcentajeInventario'] < -5) {
                    $diferenciaInventarioClass = 'text-danger';
                } else {
                    $diferenciaInventarioClass = 'text-warning';
                }
            }
            
            echo '<tr>';
            echo '<td><strong>' . htmlspecialchars($row['Folio_Ingreso']) . '</strong></td>';
            echo '<td>' . htmlspecialchars($row['Cod_Barra']) . '</td>';
            echo '<td>' . htmlspecialchars($row['Nombre_Producto']) . '</td>';
            echo '<td>' . htmlspecialchars($row['Nombre_Sucursal']) . '</td>';
            echo '<td><strong>' . number_format($row['StockRealSistema']) . '</strong></td>';
            echo '<td><strong>' . number_format($row['StockInventarioSucursal']) . '</strong></td>';
            echo '<td>' . ($row['ExistenciaFisica'] !== null ? '<strong>' . number_format($row['ExistenciaFisica']) . '</strong>' : '-') . '</td>';
            
            // Diferencia vs Sistema
            echo '<td class="' . $diferenciaSistemaClass . '">';
            if ($row['DiferenciaUnidadesSistema'] !== null) {
                $signo = $row['DiferenciaUnidadesSistema'] > 0 ? '+' : '';
                echo $signo . number_format($row['DiferenciaUnidadesSistema']) . ' (' . $row['DiferenciaPorcentajeSistema'] . '%)';
            } else {
                echo '-';
            }
            echo '</td>';
            
            // Diferencia vs Inventario
            echo '<td class="' . $diferenciaInventarioClass . '">';
            if ($row['DiferenciaUnidadesInventario'] !== null) {
                $signo = $row['DiferenciaUnidadesInventario'] > 0 ? '+' : '';
                echo $signo . number_format($row['DiferenciaUnidadesInventario']) . ' (' . $row['DiferenciaPorcentajeInventario'] . '%)';
            } else {
                echo '-';
            }
            echo '</td>';
            
            echo '<td><span class="badge bg-' . $estadoClass . '"><i class="fa-solid ' . $estadoIcon . ' me-1"></i>' . $row['Estado'] . '</span></td>';
            echo '<td>' . htmlspecialchars($row['AgregadoPor']) . '</td>';
            echo '<td>' . date('d/m/Y H:i', strtotime($row['AgregadoEl'])) . '</td>';
            echo '<td>';
            echo '<div class="btn-group" role="group">';
            
            // Botón para ver detalles del conteo
            echo '<button class="btn btn-sm btn-info btn-ver-detalles" data-folio="' . $row['Folio_Ingreso'] . '" data-codigo="' . htmlspecialchars($row['Cod_Barra']) . '" title="Ver Detalles">';
            echo '<i class="fa-solid fa-eye"></i>';
            echo '</button>';
            
            // Botón para exportar a Excel
            echo '<button class="btn btn-sm btn-success btn-exportar" data-folio="' . $row['Folio_Ingreso'] . '" data-codigo="' . htmlspecialchars($row['Cod_Barra']) . '" title="Exportar">';
            echo '<i class="fa-solid fa-file-excel"></i>';
            echo '</button>';
            
            // Botón para imprimir
            echo '<button class="btn btn-sm btn-secondary btn-imprimir" data-folio="' . $row['Folio_Ingreso'] . '" data-codigo="' . htmlspecialchars($row['Cod_Barra']) . '" title="Imprimir">';
            echo '<i class="fa-solid fa-print"></i>';
            echo '</button>';
            
            echo '</div>';
            echo '</td>';
            echo '</tr>';
        }
        
        echo '</tbody>';
        echo '</table>';
        echo '</div>';
    } else {
        echo '<div class="alert alert-info text-center">';
        echo '<i class="fa-solid fa-info-circle me-2"></i>';
        echo 'No se encontraron conteos diarios con los filtros aplicados.';
        echo '</div>';
    }
    
    $stmt->close();
} else {
    echo '<div class="alert alert-danger">Error al preparar la consulta: ' . $conn->error . '</div>';
}

$conn->close();
?>
