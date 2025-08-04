<?php
include_once "db_connect.php";
include_once "ControladorUsuario.php";
include_once "TareasController.php";

$tareasController = new TareasController($conn, $userId);

// Obtener filtros de la URL
$filtrosJson = $_GET['filtros'] ?? '{}';
$filtros = json_decode($filtrosJson, true);

// Obtener tareas con filtros
$result = $tareasController->getTareas($filtros);

// Configurar headers para descarga de Excel
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename="Tareas_' . date('Y-m-d_H-i-s') . '.xls"');
header('Cache-Control: max-age=0');

// Crear el contenido del Excel
?>
<table border="1">
    <thead>
        <tr style="background-color: #ef7980; color: white; font-weight: bold;">
            <th>ID</th>
            <th>Título</th>
            <th>Descripción</th>
            <th>Prioridad</th>
            <th>Fecha Límite</th>
            <th>Estado</th>
            <th>Asignado a</th>
            <th>Creado por</th>
            <th>Fecha Creación</th>
            <th>Fecha Actualización</th>
        </tr>
    </thead>
    <tbody>
        <?php
        while ($row = $result->fetch_assoc()) {
            $fechaLimite = $row['fecha_limite'] ? date('d/m/Y', strtotime($row['fecha_limite'])) : '';
            $fechaCreacion = date('d/m/Y H:i', strtotime($row['fecha_creacion']));
            $fechaActualizacion = date('d/m/Y H:i', strtotime($row['fecha_actualizacion']));
            
            // Determinar el color de fondo según la prioridad
            $bgColor = '';
            switch ($row['prioridad']) {
                case 'Alta':
                    $bgColor = 'background-color: #ffebee;';
                    break;
                case 'Media':
                    $bgColor = 'background-color: #fff3e0;';
                    break;
                case 'Baja':
                    $bgColor = 'background-color: #e8f5e8;';
                    break;
            }
            
            // Marcar tareas vencidas
            if ($row['fecha_limite'] && strtotime($row['fecha_limite']) < time()) {
                $bgColor = 'background-color: #fff3cd; color: #856404;';
            }
            ?>
            <tr style="<?php echo $bgColor; ?>">
                <td><?php echo $row['id']; ?></td>
                <td><?php echo htmlspecialchars($row['titulo']); ?></td>
                <td><?php echo htmlspecialchars($row['descripcion']); ?></td>
                <td><?php echo $row['prioridad']; ?></td>
                <td><?php echo $fechaLimite; ?></td>
                <td><?php echo $row['estado']; ?></td>
                <td><?php echo htmlspecialchars($row['asignado_nombre']); ?></td>
                <td><?php echo htmlspecialchars($row['creador_nombre']); ?></td>
                <td><?php echo $fechaCreacion; ?></td>
                <td><?php echo $fechaActualizacion; ?></td>
            </tr>
            <?php
        }
        ?>
    </tbody>
</table>

<?php
// Agregar estadísticas al final
$estadisticas = $tareasController->getEstadisticas();
$stats = [];
while ($row = $estadisticas->fetch_assoc()) {
    $stats[$row['estado']] = $row['cantidad'];
}
?>
<br><br>
<table border="1" style="width: 50%;">
    <thead>
        <tr style="background-color: #007bff; color: white; font-weight: bold;">
            <th colspan="2">Estadísticas de Tareas</th>
        </tr>
        <tr style="background-color: #f8f9fa; font-weight: bold;">
            <th>Estado</th>
            <th>Cantidad</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>Por hacer</td>
            <td><?php echo isset($stats['Por hacer']) ? $stats['Por hacer'] : 0; ?></td>
        </tr>
        <tr>
            <td>En progreso</td>
            <td><?php echo isset($stats['En progreso']) ? $stats['En progreso'] : 0; ?></td>
        </tr>
        <tr>
            <td>Completada</td>
            <td><?php echo isset($stats['Completada']) ? $stats['Completada'] : 0; ?></td>
        </tr>
        <tr>
            <td>Cancelada</td>
            <td><?php echo isset($stats['Cancelada']) ? $stats['Cancelada'] : 0; ?></td>
        </tr>
        <tr style="background-color: #e9ecef; font-weight: bold;">
            <td>Total</td>
            <td><?php echo array_sum($stats); ?></td>
        </tr>
    </tbody>
</table>

<br><br>
<table border="1" style="width: 50%;">
    <thead>
        <tr style="background-color: #17a2b8; color: white; font-weight: bold;">
            <th colspan="2">Información del Reporte</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>Fecha de generación</td>
            <td><?php echo date('d/m/Y H:i:s'); ?></td>
        </tr>
        <tr>
            <td>Usuario</td>
            <td><?php echo htmlspecialchars($row['Nombre_Apellidos']); ?></td>
        </tr>
        <tr>
            <td>Filtros aplicados</td>
            <td>
                <?php
                $filtrosTexto = [];
                if (!empty($filtros['estado'])) $filtrosTexto[] = 'Estado: ' . $filtros['estado'];
                if (!empty($filtros['prioridad'])) $filtrosTexto[] = 'Prioridad: ' . $filtros['prioridad'];
                if (!empty($filtros['asignado_a'])) $filtrosTexto[] = 'Asignado: ' . $filtros['asignado_a'];
                
                echo !empty($filtrosTexto) ? implode(', ', $filtrosTexto) : 'Ninguno';
                ?>
            </td>
        </tr>
    </tbody>
</table> 