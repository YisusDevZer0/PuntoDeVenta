<?php
include_once "db_connect.php";
include_once "ControladorUsuario.php";

// Obtener parámetros de filtro y paginación
$filtroSucursal = isset($_GET['sucursal']) ? trim($_GET['sucursal']) : '';
$filtroTipoRef = isset($_GET['tipoRef']) ? trim($_GET['tipoRef']) : '';
$fechaDesde = isset($_GET['fechaDesde']) ? trim($_GET['fechaDesde']) : '';
$fechaHasta = isset($_GET['fechaHasta']) ? trim($_GET['fechaHasta']) : '';

$pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$registrosPorPagina = isset($_GET['registros']) ? (int)$_GET['registros'] : 25;
if ($registrosPorPagina < 10) $registrosPorPagina = 10;
if ($registrosPorPagina > 100) $registrosPorPagina = 100;
$offset = ($pagina - 1) * $registrosPorPagina;

$sql = "SELECT 
    im.id,
    im.Fk_sucursal,
    s.Nombre_Sucursal,
    im.Cod_Barra,
    im.Nombre_Prod,
    im.movement_type,
    im.reference_type,
    im.reference_id,
    im.quantity,
    im.stock_before,
    im.stock_after,
    im.reason,
    im.AgregadoPor,
    im.created_at
FROM inventory_movements im
LEFT JOIN Sucursales s ON im.Fk_sucursal = s.ID_Sucursal
WHERE 1=1";

$params = [];
$types = "";

if ($filtroSucursal !== '') {
    $sql .= " AND im.Fk_sucursal = ?";
    $params[] = $filtroSucursal;
    $types .= "i";
}

if ($filtroTipoRef !== '') {
    $sql .= " AND im.reference_type = ?";
    $params[] = $filtroTipoRef;
    $types .= "s";
}

if ($fechaDesde !== '') {
    $sql .= " AND DATE(im.created_at) >= ?";
    $params[] = $fechaDesde;
    $types .= "s";
}

if ($fechaHasta !== '') {
    $sql .= " AND DATE(im.created_at) <= ?";
    $params[] = $fechaHasta;
    $types .= "s";
}

$sqlCount = "SELECT COUNT(*) as total FROM inventory_movements im LEFT JOIN Sucursales s ON im.Fk_sucursal = s.ID_Sucursal WHERE 1=1";
if ($filtroSucursal !== '') $sqlCount .= " AND im.Fk_sucursal = ?";
if ($filtroTipoRef !== '') $sqlCount .= " AND im.reference_type = ?";
if ($fechaDesde !== '') $sqlCount .= " AND DATE(im.created_at) >= ?";
if ($fechaHasta !== '') $sqlCount .= " AND DATE(im.created_at) <= ?";

$paramsCount = $params;
$stmtCount = $conn->prepare($sqlCount);
$totalRegistros = 0;
if ($stmtCount) {
    if (!empty($paramsCount)) {
        $stmtCount->bind_param($types, ...$paramsCount);
    }
    $stmtCount->execute();
    $resultCount = $stmtCount->get_result();
    if ($rowCount = $resultCount->fetch_assoc()) {
        $totalRegistros = (int)$rowCount['total'];
    }
    $stmtCount->close();
}

$totalPaginas = $totalRegistros > 0 ? (int)ceil($totalRegistros / $registrosPorPagina) : 1;
if ($pagina < 1) $pagina = 1;
if ($pagina > $totalPaginas) $pagina = $totalPaginas;

$sql .= " ORDER BY im.created_at DESC LIMIT ? OFFSET ?";
$params[] = $registrosPorPagina;
$params[] = $offset;
$types .= "ii";

$stmt = $conn->prepare($sql);
if ($stmt) {
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo '<div class="table-responsive">';
        echo '<table class="table table-striped table-hover table-sm">';
        echo '<thead class="table-dark">';
        echo '<tr>';
        echo '<th>ID</th>';
        echo '<th>Fecha</th>';
        echo '<th>Sucursal</th>';
        echo '<th>Código</th>';
        echo '<th>Producto</th>';
        echo '<th>Tipo mov.</th>';
        echo '<th>Origen</th>';
        echo '<th>Ref.</th>';
        echo '<th>Cantidad</th>';
        echo '<th>Stock ant.</th>';
        echo '<th>Stock nuevo</th>';
        echo '<th>Motivo</th>';
        echo '<th>Usuario</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';

        $etiquetasTipo = [
            'entry' => 'Entrada',
            'exit' => 'Salida',
            'adjustment' => 'Ajuste',
            'count_correction' => 'Conteo'
        ];
        $etiquetasRef = [
            'sale' => 'Venta',
            'purchase' => 'Compra',
            'transfer' => 'Traspaso',
            'count' => 'Conteo',
            'manual' => 'Manual',
            'ingreso' => 'Ingreso',
            'traspaso_salida' => 'Traspaso salida',
            'traspaso_entrada' => 'Traspaso entrada',
            'inventario_sucursal' => 'Inventario suc.'
        ];

        while ($row = $result->fetch_assoc()) {
            $tipoMov = $row['movement_type'];
            $tipoRef = $row['reference_type'];
            $labelTipo = isset($etiquetasTipo[$tipoMov]) ? $etiquetasTipo[$tipoMov] : $tipoMov;
            $labelRef = isset($etiquetasRef[$tipoRef]) ? $etiquetasRef[$tipoRef] : $tipoRef;

            $claseCant = $row['quantity'] >= 0 ? 'text-success' : 'text-danger';
            $signo = $row['quantity'] >= 0 ? '+' : '';

            echo '<tr>';
            echo '<td>' . (int)$row['id'] . '</td>';
            echo '<td>' . date('d/m/Y H:i', strtotime($row['created_at'])) . '</td>';
            echo '<td>' . htmlspecialchars($row['Nombre_Sucursal'] ?: '-') . '</td>';
            echo '<td>' . htmlspecialchars($row['Cod_Barra'] ?: '-') . '</td>';
            echo '<td>' . htmlspecialchars($row['Nombre_Prod'] ?: '-') . '</td>';
            echo '<td><span class="badge bg-secondary">' . htmlspecialchars($labelTipo) . '</span></td>';
            echo '<td><span class="badge bg-info">' . htmlspecialchars($labelRef) . '</span></td>';
            echo '<td>' . htmlspecialchars($row['reference_id'] ?: '-') . '</td>';
            echo '<td class="' . $claseCant . '"><strong>' . $signo . (int)$row['quantity'] . '</strong></td>';
            echo '<td>' . ($row['stock_before'] !== null ? (int)$row['stock_before'] : '-') . '</td>';
            echo '<td>' . ($row['stock_after'] !== null ? (int)$row['stock_after'] : '-') . '</td>';
            echo '<td>' . htmlspecialchars($row['reason'] ?: '-') . '</td>';
            echo '<td>' . htmlspecialchars($row['AgregadoPor'] ?: '-') . '</td>';
            echo '</tr>';
        }

        echo '</tbody>';
        echo '</table>';
        echo '</div>';

        echo '<div class="row mt-3">';
        echo '<div class="col-md-6">';
        echo '<div class="d-flex align-items-center">';
        echo '<span class="text-muted me-3">Mostrando ' . (($pagina - 1) * $registrosPorPagina + 1) . ' a ' . min($pagina * $registrosPorPagina, $totalRegistros) . ' de ' . $totalRegistros . ' registros</span>';
        echo '<select class="form-select form-select-sm" style="width: auto;" onchange="CambiarRegistrosMovimientos(this.value)">';
        echo '<option value="10" ' . ($registrosPorPagina == 10 ? 'selected' : '') . '>10 por página</option>';
        echo '<option value="25" ' . ($registrosPorPagina == 25 ? 'selected' : '') . '>25 por página</option>';
        echo '<option value="50" ' . ($registrosPorPagina == 50 ? 'selected' : '') . '>50 por página</option>';
        echo '<option value="100" ' . ($registrosPorPagina == 100 ? 'selected' : '') . '>100 por página</option>';
        echo '</select>';
        echo '</div>';
        echo '</div>';
        echo '<div class="col-md-6">';
        echo '<nav aria-label="Paginación movimientos">';
        echo '<ul class="pagination pagination-sm justify-content-end mb-0">';

        if ($pagina > 1) {
            echo '<li class="page-item"><a class="page-link" href="#" onclick="CambiarPaginaMovimientos(' . ($pagina - 1) . '); return false;"><i class="fa-solid fa-chevron-left"></i></a></li>';
        } else {
            echo '<li class="page-item disabled"><span class="page-link"><i class="fa-solid fa-chevron-left"></i></span></li>';
        }

        $inicio = max(1, $pagina - 2);
        $fin = min($totalPaginas, $pagina + 2);
        if ($inicio > 1) {
            echo '<li class="page-item"><a class="page-link" href="#" onclick="CambiarPaginaMovimientos(1); return false;">1</a></li>';
            if ($inicio > 2) echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
        }
        for ($i = $inicio; $i <= $fin; $i++) {
            if ($i == $pagina) {
                echo '<li class="page-item active"><span class="page-link">' . $i . '</span></li>';
            } else {
                echo '<li class="page-item"><a class="page-link" href="#" onclick="CambiarPaginaMovimientos(' . $i . '); return false;">' . $i . '</a></li>';
            }
        }
        if ($fin < $totalPaginas) {
            if ($fin < $totalPaginas - 1) echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
            echo '<li class="page-item"><a class="page-link" href="#" onclick="CambiarPaginaMovimientos(' . $totalPaginas . '); return false;">' . $totalPaginas . '</a></li>';
        }
        if ($pagina < $totalPaginas) {
            echo '<li class="page-item"><a class="page-link" href="#" onclick="CambiarPaginaMovimientos(' . ($pagina + 1) . '); return false;"><i class="fa-solid fa-chevron-right"></i></a></li>';
        } else {
            echo '<li class="page-item disabled"><span class="page-link"><i class="fa-solid fa-chevron-right"></i></span></li>';
        }
        echo '</ul>';
        echo '</nav>';
        echo '</div>';
        echo '</div>';

        echo '<input type="hidden" id="totalPaginasMovimientos" value="' . $totalPaginas . '">';
    } else {
        echo '<div class="alert alert-info text-center">';
        echo '<i class="fa-solid fa-info-circle me-2"></i>No se encontraron movimientos de inventario con los filtros aplicados.';
        echo '</div>';
    }
    $stmt->close();
} else {
    echo '<div class="alert alert-danger">Error al preparar la consulta.</div>';
}

$conn->close();
