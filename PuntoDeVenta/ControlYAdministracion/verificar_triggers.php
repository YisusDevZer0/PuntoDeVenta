<?php
// Script para verificar si los triggers están instalados
include_once "Controladores/db_connect.php";

echo "<h2>Verificando Triggers de Abonos</h2>";

// Verificar si existen los triggers
$sql = "SHOW TRIGGERS WHERE `Table` = 'encargos'";
$result = mysqli_query($conn, $sql);

$triggers_encontrados = [];
while ($row = mysqli_fetch_assoc($result)) {
    $triggers_encontrados[] = $row['Trigger'];
}

echo "<h3>Triggers encontrados en la tabla 'encargos':</h3>";
if (empty($triggers_encontrados)) {
    echo "<p style='color: red;'>❌ No se encontraron triggers</p>";
} else {
    echo "<ul>";
    foreach ($triggers_encontrados as $trigger) {
        echo "<li style='color: green;'>✓ $trigger</li>";
    }
    echo "</ul>";
}

// Verificar si los triggers específicos están instalados
$triggers_requeridos = [
    'tr_abono_encargo_insert',
    'tr_abono_encargo_update', 
    'tr_abono_encargo_delete'
];

echo "<h3>Verificando triggers requeridos:</h3>";
foreach ($triggers_requeridos as $trigger) {
    if (in_array($trigger, $triggers_encontrados)) {
        echo "<p style='color: green;'>✅ $trigger - INSTALADO</p>";
    } else {
        echo "<p style='color: red;'>❌ $trigger - NO INSTALADO</p>";
    }
}

// Mostrar información de la tabla encargos
echo "<h3>Información de la tabla 'encargos':</h3>";
$sql_info = "SELECT COUNT(*) as total_encargos FROM encargos";
$result_info = mysqli_query($conn, $sql_info);
$row_info = mysqli_fetch_assoc($result_info);

echo "<p>Total de encargos registrados: <strong>" . $row_info['total_encargos'] . "</strong></p>";

// Mostrar últimos encargos con abonos
$sql_abonos = "SELECT id, nombre_paciente, abono_parcial, NumTicket, Fk_Caja 
               FROM encargos 
               WHERE abono_parcial > 0 
               ORDER BY id DESC 
               LIMIT 5";
$result_abonos = mysqli_query($conn, $sql_abonos);

echo "<h3>Últimos 5 encargos con abonos:</h3>";
if (mysqli_num_rows($result_abonos) > 0) {
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>ID</th><th>Paciente</th><th>Abono</th><th>Ticket</th><th>Caja</th></tr>";
    while ($row = mysqli_fetch_assoc($result_abonos)) {
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . $row['nombre_paciente'] . "</td>";
        echo "<td>$" . number_format($row['abono_parcial'], 2) . "</td>";
        echo "<td>" . $row['NumTicket'] . "</td>";
        echo "<td>" . $row['Fk_Caja'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>No hay encargos con abonos registrados.</p>";
}

mysqli_close($conn);
?> 