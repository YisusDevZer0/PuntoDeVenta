<?php
include_once "db_connect.php";

// Obtener parámetros de filtro y paginación
$filtroSucursal = isset($_GET['sucursal']) ? $_GET['sucursal'] : '';
$filtroEstado = isset($_GET['estado']) ? $_GET['estado'] : '';
$fechaDesde = isset($_GET['fechaDesde']) ? $_GET['fechaDesde'] : '';
$fechaHasta = isset($_GET['fechaHasta']) ? $_GET['fechaHasta'] : '';

// Parámetros de paginación
$pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$registrosPorPagina = isset($_GET['registros']) ? (int)$_GET['registros'] : 10;
$offset = ($pagina - 1) * $registrosPorPagina;

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

$sql .= " ORDER BY cd.AgregadoEl DESC LIMIT ? OFFSET ?";

// Consulta de conteo
$sqlCount = "SELECT COUNT(*) as total
FROM ConteosDiarios cd
LEFT JOIN Sucursales s ON cd.Fk_sucursal = s.ID_Sucursal
LEFT JOIN Stock_POS sp ON cd.Cod_Barra = sp.Cod_Barra AND cd.Fk_sucursal = sp.Fk_sucursal
LEFT JOIN InventariosSucursales iss ON cd.Cod_Barra = iss.Cod_Barra AND cd.Fk_sucursal = iss.Fk_Sucursal
WHERE 1=1";

// Aplicar los mismos filtros a la consulta de conteo
if (!empty($filtroSucursal)) {
    $sqlCount .= " AND cd.Fk_sucursal = ?";
}

if ($filtroEstado !== '') {
    if ($filtroEstado == '0') {
        $sqlCount .= " AND cd.EnPausa = 0 AND cd.ExistenciaFisica IS NOT NULL";
    } elseif ($filtroEstado == '1') {
        $sqlCount .= " AND cd.EnPausa = 1";
    }
}

if (!empty($fechaDesde)) {
    $sqlCount .= " AND DATE(cd.AgregadoEl) >= ?";
}

if (!empty($fechaHasta)) {
    $sqlCount .= " AND DATE(cd.AgregadoEl) <= ?";
}

// Obtener total de registros
$stmtCount = $conn->prepare($sqlCount);
if ($stmtCount) {
    if (!empty($params)) {
        $stmtCount->bind_param($types, ...$params);
    }
    $stmtCount->execute();
    $resultCount = $stmtCount->get_result();
    $totalRegistros = $resultCount->fetch_assoc()['total'];
    $stmtCount->close();
} else {
    $totalRegistros = 0;
}

$totalPaginas = ceil($totalRegistros / $registrosPorPagina);

// Preparar y ejecutar consulta principal
$stmt = $conn->prepare($sql);
if ($stmt) {
    // Agregar parámetros de paginación
    $params[] = $registrosPorPagina;
    $params[] = $offset;
    $types .= "ii";
    
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
        
        // Información de paginación
        echo '<div class="row mt-3">';
        echo '<div class="col-md-6">';
        echo '<div class="d-flex align-items-center">';
        echo '<span class="text-muted me-3">Mostrando ' . (($pagina - 1) * $registrosPorPagina + 1) . ' a ' . min($pagina * $registrosPorPagina, $totalRegistros) . ' de ' . $totalRegistros . ' registros</span>';
        echo '<select class="form-select form-select-sm" style="width: auto;" onchange="CambiarRegistrosPorPagina(this.value)">';
        echo '<option value="10" ' . ($registrosPorPagina == 10 ? 'selected' : '') . '>10 por página</option>';
        echo '<option value="25" ' . ($registrosPorPagina == 25 ? 'selected' : '') . '>25 por página</option>';
        echo '<option value="50" ' . ($registrosPorPagina == 50 ? 'selected' : '') . '>50 por página</option>';
        echo '<option value="100" ' . ($registrosPorPagina == 100 ? 'selected' : '') . '>100 por página</option>';
        echo '</select>';
        echo '</div>';
        echo '</div>';
        
        echo '<div class="col-md-6">';
        echo '<nav aria-label="Paginación de conteos">';
        echo '<ul class="pagination pagination-sm justify-content-end mb-0">';
        
        // Botón anterior
        if ($pagina > 1) {
            echo '<li class="page-item">';
            echo '<a class="page-link" href="#" onclick="CambiarPagina(' . ($pagina - 1) . ')">';
            echo '<i class="fa-solid fa-chevron-left"></i>';
            echo '</a>';
            echo '</li>';
        } else {
            echo '<li class="page-item disabled">';
            echo '<span class="page-link"><i class="fa-solid fa-chevron-left"></i></span>';
            echo '</li>';
        }
        
        // Números de página
        $inicio = max(1, $pagina - 2);
        $fin = min($totalPaginas, $pagina + 2);
        
        if ($inicio > 1) {
            echo '<li class="page-item">';
            echo '<a class="page-link" href="#" onclick="CambiarPagina(1)">1</a>';
            echo '</li>';
            if ($inicio > 2) {
                echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
            }
        }
        
        for ($i = $inicio; $i <= $fin; $i++) {
            if ($i == $pagina) {
                echo '<li class="page-item active">';
                echo '<span class="page-link">' . $i . '</span>';
                echo '</li>';
            } else {
                echo '<li class="page-item">';
                echo '<a class="page-link" href="#" onclick="CambiarPagina(' . $i . ')">' . $i . '</a>';
                echo '</li>';
            }
        }
        
        if ($fin < $totalPaginas) {
            if ($fin < $totalPaginas - 1) {
                echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
            }
            echo '<li class="page-item">';
            echo '<a class="page-link" href="#" onclick="CambiarPagina(' . $totalPaginas . ')">' . $totalPaginas . '</a>';
            echo '</li>';
        }
        
        // Botón siguiente
        if ($pagina < $totalPaginas) {
            echo '<li class="page-item">';
            echo '<a class="page-link" href="#" onclick="CambiarPagina(' . ($pagina + 1) . ')">';
            echo '<i class="fa-solid fa-chevron-right"></i>';
            echo '</a>';
            echo '</li>';
        } else {
            echo '<li class="page-item disabled">';
            echo '<span class="page-link"><i class="fa-solid fa-chevron-right"></i></span>';
            echo '</li>';
        }
        
        echo '</ul>';
        echo '</nav>';
        echo '</div>';
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