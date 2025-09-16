<?php
// Archivo de prueba para verificar el funcionamiento del sistema de bitácoras de limpieza
include_once "Controladores/ControladorUsuario.php";
include_once "Controladores/BitacoraLimpiezaAdminController.php";

echo "<h2>Prueba del Sistema de Bitácoras de Limpieza</h2>";

try {
    $controller = new BitacoraLimpiezaAdminController($conn);
    
    echo "<h3>1. Probando obtenerBitacorasAdmin()</h3>";
    $bitacoras = $controller->obtenerBitacorasAdmin();
    echo "<p>Total de bitácoras encontradas: " . count($bitacoras) . "</p>";
    
    if (!empty($bitacoras)) {
        echo "<h4>Primera bitácora:</h4>";
        echo "<pre>";
        print_r($bitacoras[0]);
        echo "</pre>";
    }
    
    echo "<h3>2. Probando obtenerEstadisticasGenerales()</h3>";
    $estadisticas = $controller->obtenerEstadisticasGenerales();
    echo "<pre>";
    print_r($estadisticas);
    echo "</pre>";
    
    echo "<h3>3. Probando obtenerAreas()</h3>";
    $areas = $controller->obtenerAreas();
    echo "<p>Áreas encontradas: " . implode(', ', $areas) . "</p>";
    
    echo "<h3>4. Probando obtenerSucursales()</h3>";
    $sucursales = $controller->obtenerSucursales();
    echo "<p>Total de sucursales: " . count($sucursales) . "</p>";
    
    echo "<h3>5. Probando obtenerBitacorasPorSucursal()</h3>";
    $bitacorasPorSucursal = $controller->obtenerBitacorasPorSucursal();
    echo "<p>Datos por área/sucursal:</p>";
    echo "<pre>";
    print_r($bitacorasPorSucursal);
    echo "</pre>";
    
    echo "<h3>6. Probando filtros</h3>";
    $filtros = ['area' => 'Farmacia'];
    $bitacorasFiltradas = $controller->obtenerBitacorasAdmin($filtros);
    echo "<p>Bitácoras filtradas por área 'Farmacia': " . count($bitacorasFiltradas) . "</p>";
    
    echo "<h3>✅ Todas las pruebas completadas exitosamente</h3>";
    
} catch (Exception $e) {
    echo "<h3>❌ Error en las pruebas:</h3>";
    echo "<p style='color: red;'>" . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
?>
