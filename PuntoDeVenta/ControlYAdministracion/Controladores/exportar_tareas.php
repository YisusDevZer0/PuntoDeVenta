<?php
include_once "db_connect.php";
include_once "ControladorUsuario.php";
include_once "TareasController.php";

$tareasController = new TareasController($conn, $userId);

// Obtener filtros de la URL
$filtrosJson = $_GET['filtros'] ?? '{}';
$filtros = json_decode($filtrosJson, true);

// Obtener las tareas con los filtros aplicados
$result = $tareasController->getTareas($filtros);

// Configurar headers para descarga de Excel
$filename = 'tareas_' . date('Y-m-d_H-i-s') . '.xls';
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Cache-Control: max-age=0');

// Iniciar salida
echo "\xEF\xBB\xBF"; // BOM para UTF-8

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
</head>
<body>
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
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                echo "<td>" . htmlspecialchars($row['titulo']) . "</td>";
                echo "<td>" . htmlspecialchars($row['descripcion']) . "</td>";
                echo "<td>" . htmlspecialchars($row['prioridad']) . "</td>";
                echo "<td>" . htmlspecialchars($row['fecha_limite'] ?? 'Sin fecha') . "</td>";
                echo "<td>" . htmlspecialchars($row['estado']) . "</td>";
                echo "<td>" . htmlspecialchars($row['asignado_nombre']) . "</td>";
                echo "<td>" . htmlspecialchars($row['creador_nombre']) . "</td>";
                echo "<td>" . htmlspecialchars($row['fecha_creacion']) . "</td>";
                echo "<td>" . htmlspecialchars($row['fecha_actualizacion']) . "</td>";
                echo "</tr>";
            }
            ?>
        </tbody>
    </table>
</body>
</html>