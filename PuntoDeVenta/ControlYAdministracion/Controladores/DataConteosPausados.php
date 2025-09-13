<?php
include_once "db_connect.php";

// Obtener parámetros de filtro y paginación
$filtroSucursal = isset($_GET['sucursal']) ? $_GET['sucursal'] : '';
$filtroUsuario = isset($_GET['usuario']) ? $_GET['usuario'] : '';
$fechaDesde = isset($_GET['fechaDesde']) ? $_GET['fechaDesde'] : '';
$fechaHasta = isset($_GET['fechaHasta']) ? $_GET['fechaHasta'] : '';

// Parámetros de paginación
$pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$registrosPorPagina = isset($_GET['registros']) ? (int)$_GET['registros'] : 10;
$offset = ($pagina - 1) * $registrosPorPagina;

// Construir consulta base
$sql = "SELECT 
    cd.Folio_Ingreso,
    cd.Cod_Barra,
    cd.Nombre_Producto,
    cd.Fk_sucursal,
    s.Nombre_Sucursal,
    cd.Existencias_R,
    cd.ExistenciaFisica,
    cd.AgregadoPor,
    cd.AgregadoEl,
    cd.EnPausa,
    TIMESTAMPDIFF(HOUR, cd.AgregadoEl, NOW()) as HorasPausado,
    CASE 
        WHEN TIMESTAMPDIFF(HOUR, cd.AgregadoEl, NOW()) > 24 THEN 'danger'
        WHEN TIMESTAMPDIFF(HOUR, cd.AgregadoEl, NOW()) > 12 THEN 'warning'
        ELSE 'info'
    END as PrioridadClass,
    CASE 
        WHEN TIMESTAMPDIFF(HOUR, cd.AgregadoEl, NOW()) > 24 THEN 'Alta'
        WHEN TIMESTAMPDIFF(HOUR, cd.AgregadoEl, NOW()) > 12 THEN 'Media'
        ELSE 'Baja'
    END as Prioridad
FROM ConteosDiarios cd
LEFT JOIN Sucursales s ON cd.Fk_sucursal = s.ID_Sucursal
WHERE cd.EnPausa = 1";

$params = [];
$types = "";

// Aplicar filtros
if (!empty($filtroSucursal)) {
    $sql .= " AND cd.Fk_sucursal = ?";
    $params[] = $filtroSucursal;
    $types .= "i";
}

if (!empty($filtroUsuario)) {
    $sql .= " AND cd.AgregadoPor = ?";
    $params[] = $filtroUsuario;
    $types .= "s";
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

$sql .= " ORDER BY cd.AgregadoEl ASC LIMIT ? OFFSET ?";

// Primero obtener el total de registros para la paginación
$sqlCount = str_replace("SELECT 
    cd.Folio_Ingreso,
    cd.Cod_Barra,
    cd.Nombre_Producto,
    cd.Fk_sucursal,
    s.Nombre_Sucursal,
    cd.Existencias_R,
    cd.ExistenciaFisica,
    cd.AgregadoPor,
    cd.AgregadoEl,
    cd.EnPausa,
    TIMESTAMPDIFF(HOUR, cd.AgregadoEl, NOW()) as HorasPausado,
    CASE 
        WHEN TIMESTAMPDIFF(HOUR, cd.AgregadoEl, NOW()) > 24 THEN 'danger'
        WHEN TIMESTAMPDIFF(HOUR, cd.AgregadoEl, NOW()) > 12 THEN 'warning'
        ELSE 'info'
    END as PrioridadClass,
    CASE 
        WHEN TIMESTAMPDIFF(HOUR, cd.AgregadoEl, NOW()) > 24 THEN 'Alta'
        WHEN TIMESTAMPDIFF(HOUR, cd.AgregadoEl, NOW()) > 12 THEN 'Media'
        ELSE 'Baja'
    END as Prioridad", "SELECT COUNT(*) as total", $sql);

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
        echo '<th>Prioridad</th>';
        echo '<th>Folio</th>';
        echo '<th>Código de Barras</th>';
        echo '<th>Producto</th>';
        echo '<th>Sucursal</th>';
        echo '<th>Existencia Real</th>';
        echo '<th>Existencia Física</th>';
        echo '<th>Agregado Por</th>';
        echo '<th>Fecha Pausa</th>';
        echo '<th>Tiempo Pausado</th>';
        echo '<th>Acciones</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';
        
        while ($row = $result->fetch_assoc()) {
            $tiempoPausado = '';
            if ($row['HorasPausado'] < 24) {
                $tiempoPausado = $row['HorasPausado'] . ' horas';
            } else {
                $dias = floor($row['HorasPausado'] / 24);
                $horas = $row['HorasPausado'] % 24;
                $tiempoPausado = $dias . ' días, ' . $horas . ' horas';
            }
            
            echo '<tr class="table-' . $row['PrioridadClass'] . '">';
            echo '<td>';
            echo '<span class="badge bg-' . $row['PrioridadClass'] . '">';
            echo '<i class="fa-solid fa-exclamation-triangle me-1"></i>' . $row['Prioridad'];
            echo '</span>';
            echo '</td>';
            echo '<td><strong>' . htmlspecialchars($row['Folio_Ingreso']) . '</strong></td>';
            echo '<td>' . htmlspecialchars($row['Cod_Barra']) . '</td>';
            echo '<td>' . htmlspecialchars($row['Nombre_Producto']) . '</td>';
            echo '<td>' . htmlspecialchars($row['Nombre_Sucursal']) . '</td>';
            echo '<td>' . number_format($row['Existencias_R']) . '</td>';
            echo '<td>' . ($row['ExistenciaFisica'] !== null ? number_format($row['ExistenciaFisica']) : '-') . '</td>';
            echo '<td>' . htmlspecialchars($row['AgregadoPor']) . '</td>';
            echo '<td>' . date('d/m/Y H:i', strtotime($row['AgregadoEl'])) . '</td>';
            echo '<td><strong>' . $tiempoPausado . '</strong></td>';
            echo '<td>';
            echo '<div class="btn-group" role="group">';
            
            echo '<button class="btn btn-sm btn-info btn-ver-detalles" data-folio="' . $row['Folio_Ingreso'] . '" data-codigo="' . htmlspecialchars($row['Cod_Barra']) . '" title="Ver Detalles">';
            echo '<i class="fa-solid fa-eye"></i>';
            echo '</button>';
            
            echo '<button class="btn btn-sm btn-success btn-reanudar-conteo" data-folio="' . $row['Folio_Ingreso'] . '" data-codigo="' . htmlspecialchars($row['Cod_Barra']) . '" title="Reanudar">';
            echo '<i class="fa-solid fa-play"></i>';
            echo '</button>';
            
            echo '<button class="btn btn-sm btn-warning btn-finalizar-conteo" data-folio="' . $row['Folio_Ingreso'] . '" data-codigo="' . htmlspecialchars($row['Cod_Barra']) . '" title="Finalizar">';
            echo '<i class="fa-solid fa-check"></i>';
            echo '</button>';
            
            if ($row['HorasPausado'] > 12) {
                echo '<button class="btn btn-sm btn-primary btn-recordatorio" data-folio="' . $row['Folio_Ingreso'] . '" data-codigo="' . htmlspecialchars($row['Cod_Barra']) . '" title="Enviar Recordatorio">';
                echo '<i class="fa-solid fa-bell"></i>';
                echo '</button>';
            }
            
            if ($row['HorasPausado'] > 24) {
                echo '<button class="btn btn-sm btn-danger btn-urgente" data-folio="' . $row['Folio_Ingreso'] . '" data-codigo="' . htmlspecialchars($row['Cod_Barra']) . '" title="Marcar Urgente">';
                echo '<i class="fa-solid fa-exclamation"></i>';
                echo '</button>';
            }
            
            echo '<button class="btn btn-sm btn-outline-danger btn-eliminar-conteo" data-folio="' . $row['Folio_Ingreso'] . '" data-codigo="' . htmlspecialchars($row['Cod_Barra']) . '" title="Eliminar">';
            echo '<i class="fa-solid fa-trash"></i>';
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
        echo '<select class="form-select form-select-sm" style="width: auto;" onchange="CambiarRegistrosPorPaginaPausados(this.value)">';
        echo '<option value="10" ' . ($registrosPorPagina == 10 ? 'selected' : '') . '>10 por página</option>';
        echo '<option value="25" ' . ($registrosPorPagina == 25 ? 'selected' : '') . '>25 por página</option>';
        echo '<option value="50" ' . ($registrosPorPagina == 50 ? 'selected' : '') . '>50 por página</option>';
        echo '<option value="100" ' . ($registrosPorPagina == 100 ? 'selected' : '') . '>100 por página</option>';
        echo '</select>';
        echo '</div>';
        echo '</div>';
        
        echo '<div class="col-md-6">';
        echo '<nav aria-label="Paginación de conteos pausados">';
        echo '<ul class="pagination pagination-sm justify-content-end mb-0">';
        
        // Botón anterior
        if ($pagina > 1) {
            echo '<li class="page-item">';
            echo '<a class="page-link" href="#" onclick="CambiarPaginaPausados(' . ($pagina - 1) . ')">';
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
            echo '<a class="page-link" href="#" onclick="CambiarPaginaPausados(1)">1</a>';
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
                echo '<a class="page-link" href="#" onclick="CambiarPaginaPausados(' . $i . ')">' . $i . '</a>';
                echo '</li>';
            }
        }
        
        if ($fin < $totalPaginas) {
            if ($fin < $totalPaginas - 1) {
                echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
            }
            echo '<li class="page-item">';
            echo '<a class="page-link" href="#" onclick="CambiarPaginaPausados(' . $totalPaginas . ')">' . $totalPaginas . '</a>';
            echo '</li>';
        }
        
        // Botón siguiente
        if ($pagina < $totalPaginas) {
            echo '<li class="page-item">';
            echo '<a class="page-link" href="#" onclick="CambiarPaginaPausados(' . ($pagina + 1) . ')">';
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
        echo '<div class="alert alert-success text-center">';
        echo '<i class="fa-solid fa-check-circle me-2"></i>';
        echo '¡Excelente! No hay conteos pausados en este momento.';
        echo '</div>';
    }
    
    $stmt->close();
} else {
    echo '<div class="alert alert-danger">Error al preparar la consulta: ' . $conn->error . '</div>';
}

$conn->close();
?>
