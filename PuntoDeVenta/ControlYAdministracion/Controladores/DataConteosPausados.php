<?php
include_once "db_connect.php";

// Obtener parámetros de filtro
$filtroSucursal = isset($_GET['sucursal']) ? $_GET['sucursal'] : '';
$filtroUsuario = isset($_GET['usuario']) ? $_GET['usuario'] : '';
$fechaDesde = isset($_GET['fechaDesde']) ? $_GET['fechaDesde'] : '';
$fechaHasta = isset($_GET['fechaHasta']) ? $_GET['fechaHasta'] : '';

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

$sql .= " ORDER BY cd.AgregadoEl ASC";

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
        
        // Mostrar resumen
        echo '<div class="row mt-3">';
        echo '<div class="col-12">';
        echo '<div class="alert alert-info">';
        echo '<h6><i class="fa-solid fa-info-circle me-2"></i>Resumen de Conteos Pausados</h6>';
        echo '<div class="row">';
        echo '<div class="col-md-3">';
        echo '<strong>Total Pausados:</strong> ' . $result->num_rows;
        echo '</div>';
        echo '<div class="col-md-3">';
        echo '<strong>Con Alta Prioridad (>24h):</strong> <span class="text-danger">' . 
             array_sum(array_column($result->fetch_all(MYSQLI_ASSOC), 'HorasPausado')) . '</span>';
        echo '</div>';
        echo '</div>';
        echo '</div>';
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
