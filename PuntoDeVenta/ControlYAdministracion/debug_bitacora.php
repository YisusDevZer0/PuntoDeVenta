<?php
// Archivo de diagnóstico para identificar problemas de carga
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Diagnóstico del Sistema de Bitácoras</h2>";

try {
    echo "<p>🔄 Iniciando diagnóstico...</p>";
    
    // Paso 1: Verificar sesión
    session_start();
    echo "<p>✅ Sesión iniciada</p>";
    
    // Paso 2: Incluir archivos
    echo "<p>🔄 Incluyendo archivos...</p>";
    include_once "Controladores/ControladorUsuario.php";
    echo "<p>✅ ControladorUsuario incluido</p>";
    
    // Paso 3: Verificar conexión
    if (!isset($conn) || !$conn) {
        throw new Exception("No hay conexión a la base de datos");
    }
    echo "<p>✅ Conexión a base de datos disponible</p>";
    
    // Paso 4: Verificar sesión administrativa
    if(!isset($_SESSION['ControlMaestro']) && !isset($_SESSION['AdministradorRH']) && !isset($_SESSION['Marketing'])){
        echo "<p>⚠️ No hay sesión administrativa activa</p>";
        echo "<p>Variables de sesión disponibles:</p>";
        echo "<pre>" . print_r($_SESSION, true) . "</pre>";
    } else {
        echo "<p>✅ Sesión administrativa activa</p>";
    }
    
    // Paso 5: Probar consulta simple
    echo "<p>🔄 Probando consulta simple...</p>";
    $sql = "SELECT COUNT(*) as total FROM Bitacora_Limpieza";
    $result = mysqli_query($conn, $sql);
    
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        echo "<p>✅ Consulta exitosa. Total de bitácoras: " . $row['total'] . "</p>";
    } else {
        echo "<p>❌ Error en consulta: " . mysqli_error($conn) . "</p>";
    }
    
    // Paso 6: Probar consulta con JOIN
    echo "<p>🔄 Probando consulta con JOIN...</p>";
    $sql2 = "SELECT 
                bl.id_bitacora,
                bl.area,
                bl.semana,
                bl.fecha_inicio,
                bl.fecha_fin,
                bl.responsable,
                bl.supervisor,
                bl.aux_res,
                'N/A' as sucursal_id,
                'Sin Sucursal' as Nombre_Sucursal
              FROM Bitacora_Limpieza bl 
              ORDER BY bl.fecha_inicio DESC 
              LIMIT 5";
    
    $result2 = mysqli_query($conn, $sql2);
    
    if ($result2) {
        echo "<p>✅ Consulta con JOIN exitosa</p>";
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>ID</th><th>Área</th><th>Semana</th><th>Fecha Inicio</th><th>Responsable</th></tr>";
        
        while($row = mysqli_fetch_assoc($result2)) {
            echo "<tr>";
            echo "<td>" . $row['id_bitacora'] . "</td>";
            echo "<td>" . $row['area'] . "</td>";
            echo "<td>" . $row['semana'] . "</td>";
            echo "<td>" . $row['fecha_inicio'] . "</td>";
            echo "<td>" . $row['responsable'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>❌ Error en consulta con JOIN: " . mysqli_error($conn) . "</p>";
    }
    
    // Paso 7: Probar controlador
    echo "<p>🔄 Probando controlador...</p>";
    include_once "Controladores/BitacoraLimpiezaAdminControllerSimple.php";
    
    $controller = new BitacoraLimpiezaAdminControllerSimple($conn);
    echo "<p>✅ Controlador creado</p>";
    
    // Paso 8: Probar método simple
    $bitacoras = $controller->obtenerBitacorasAdmin();
    echo "<p>✅ Método obtenerBitacorasAdmin ejecutado. Resultados: " . count($bitacoras) . "</p>";
    
    // Paso 9: Verificar memoria
    echo "<p>📊 Uso de memoria: " . memory_get_usage(true) / 1024 / 1024 . " MB</p>";
    echo "<p>📊 Límite de memoria: " . ini_get('memory_limit') . "</p>";
    echo "<p>📊 Tiempo de ejecución: " . microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'] . " segundos</p>";
    
    echo "<div style='background-color: #d4edda; border: 1px solid #c3e6cb; padding: 15px; margin: 20px 0; border-radius: 5px;'>";
    echo "<h3 style='color: #155724; margin-top: 0;'>🎉 Diagnóstico Completado</h3>";
    echo "<p>El sistema parece estar funcionando correctamente. Si BitacoraLimpieza.php sigue cargando, el problema puede ser:</p>";
    echo "<ul>";
    echo "<li>JavaScript infinito</li>";
    echo "<li>Consulta SQL muy pesada</li>";
    echo "<li>Bucle infinito en PHP</li>";
    echo "<li>Problema de red/conexión</li>";
    echo "</ul>";
    echo "<p><a href='BitacoraLimpiezaSimple.php' style='background-color: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Probar Versión Simplificada</a></p>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<div style='background-color: #f8d7da; border: 1px solid #f5c6cb; padding: 15px; margin: 20px 0; border-radius: 5px;'>";
    echo "<h3 style='color: #721c24; margin-top: 0;'>❌ Error en Diagnóstico:</h3>";
    echo "<p style='color: #721c24;'>" . $e->getMessage() . "</p>";
    echo "<p>Archivo: " . $e->getFile() . "</p>";
    echo "<p>Línea: " . $e->getLine() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
    echo "</div>";
}
?>
