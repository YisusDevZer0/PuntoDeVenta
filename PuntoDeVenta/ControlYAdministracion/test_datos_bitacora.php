<?php
// Archivo de prueba para verificar datos de bitácoras
include_once "Consultas/db_connect.php";
include_once "Controladores/BitacoraLimpiezaAdminController.php";

echo "<h2>Prueba de Datos de Bitácoras</h2>";

try {
    // Crear controlador
    $controller = new BitacoraLimpiezaAdminController($conn);
    
    echo "<h3>1. Verificar conexión a base de datos:</h3>";
    if ($conn) {
        echo "✅ Conexión exitosa<br>";
    } else {
        echo "❌ Error de conexión<br>";
    }
    
    echo "<h3>2. Verificar estructura de tabla Bitacora_Limpieza:</h3>";
    $sql_columns = "SHOW COLUMNS FROM Bitacora_Limpieza";
    $result_columns = mysqli_query($conn, $sql_columns);
    if ($result_columns) {
        echo "Columnas disponibles:<br>";
        while ($row = mysqli_fetch_assoc($result_columns)) {
            echo "- " . $row['Field'] . " (" . $row['Type'] . ")<br>";
        }
    } else {
        echo "❌ Error al obtener columnas: " . mysqli_error($conn) . "<br>";
    }
    
    echo "<h3>3. Verificar datos en Bitacora_Limpieza:</h3>";
    $sql_count = "SELECT COUNT(*) as total FROM Bitacora_Limpieza";
    $result_count = mysqli_query($conn, $sql_count);
    if ($result_count) {
        $count = mysqli_fetch_assoc($result_count)['total'];
        echo "Total de bitácoras: " . $count . "<br>";
        
        if ($count > 0) {
            echo "<h4>Primeras 5 bitácoras:</h4>";
            $sql_sample = "SELECT * FROM Bitacora_Limpieza LIMIT 5";
            $result_sample = mysqli_query($conn, $sql_sample);
            if ($result_sample) {
                echo "<table border='1' style='border-collapse: collapse;'>";
                echo "<tr><th>ID</th><th>Área</th><th>Semana</th><th>Fecha Inicio</th><th>Responsable</th></tr>";
                while ($row = mysqli_fetch_assoc($result_sample)) {
                    echo "<tr>";
                    echo "<td>" . $row['id_bitacora'] . "</td>";
                    echo "<td>" . $row['area'] . "</td>";
                    echo "<td>" . $row['semana'] . "</td>";
                    echo "<td>" . $row['fecha_inicio'] . "</td>";
                    echo "<td>" . $row['responsable'] . "</td>";
                    echo "</tr>";
                }
                echo "</table>";
            }
        } else {
            echo "⚠️ No hay bitácoras en la base de datos<br>";
        }
    } else {
        echo "❌ Error al contar bitácoras: " . mysqli_error($conn) . "<br>";
    }
    
    echo "<h3>4. Probar controlador:</h3>";
    try {
        $bitacoras = $controller->obtenerBitacorasConFiltros(['limit' => 5]);
        echo "✅ Controlador funcionando. Bitácoras obtenidas: " . count($bitacoras) . "<br>";
        
        if (count($bitacoras) > 0) {
            echo "<h4>Datos del controlador:</h4>";
            echo "<pre>" . print_r($bitacoras[0], true) . "</pre>";
        }
    } catch (Exception $e) {
        echo "❌ Error en controlador: " . $e->getMessage() . "<br>";
    }
    
    echo "<h3>5. Verificar sucursales:</h3>";
    try {
        $sucursales = $controller->obtenerSucursales();
        echo "✅ Sucursales obtenidas: " . count($sucursales) . "<br>";
        if (count($sucursales) > 0) {
            echo "Primera sucursal: " . $sucursales[0]['Nombre_Sucursal'] . "<br>";
        }
    } catch (Exception $e) {
        echo "❌ Error al obtener sucursales: " . $e->getMessage() . "<br>";
    }
    
} catch (Exception $e) {
    echo "❌ Error general: " . $e->getMessage() . "<br>";
}
?>
