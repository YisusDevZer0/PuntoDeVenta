<?php
// Archivo de prueba simple para verificar el funcionamiento básico
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Prueba Simple del Sistema de Bitácoras</h2>";

try {
    // Incluir archivos necesarios
    include_once "Controladores/ControladorUsuario.php";
    include_once "Controladores/BitacoraLimpiezaAdminControllerSimple.php";
    
    echo "<p>✅ Archivos incluidos correctamente</p>";
    
    // Verificar conexión
    if (!isset($conn) || !$conn) {
        throw new Exception("No hay conexión a la base de datos");
    }
    
    echo "<p>✅ Conexión a base de datos disponible</p>";
    
    // Crear controlador
    $controller = new BitacoraLimpiezaAdminControllerSimple($conn);
    echo "<p>✅ Controlador creado correctamente</p>";
    
    // Probar obtener áreas
    $areas = $controller->obtenerAreas();
    echo "<p>✅ Áreas obtenidas: " . implode(', ', $areas) . "</p>";
    
    // Probar obtener bitácoras
    $bitacoras = $controller->obtenerBitacorasAdmin();
    echo "<p>✅ Bitácoras obtenidas: " . count($bitacoras) . "</p>";
    
    // Probar estadísticas
    $estadisticas = $controller->obtenerEstadisticasGenerales();
    echo "<p>✅ Estadísticas obtenidas: " . json_encode($estadisticas) . "</p>";
    
    echo "<h3>🎉 Todas las pruebas pasaron exitosamente</h3>";
    echo "<p><a href='BitacoraLimpieza.php'>Ir a BitacoraLimpieza.php</a></p>";
    
} catch (Exception $e) {
    echo "<h3>❌ Error encontrado:</h3>";
    echo "<p style='color: red;'>" . $e->getMessage() . "</p>";
    echo "<p>Archivo: " . $e->getFile() . "</p>";
    echo "<p>Línea: " . $e->getLine() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
?>
