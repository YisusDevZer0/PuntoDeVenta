<?php
// Test del ChecadorController corregido
session_start();

// Simular sesión para testing
$_SESSION['ControlMaestro'] = 1;

// Incluir el controlador
include_once 'Controladores/ChecadorController.php';

echo "<h1>🧪 Test del ChecadorController Corregido</h1>";

// Test 1: Verificar que el controlador se carga sin errores
echo "<h2>✅ Test 1: Carga del Controlador</h2>";
try {
    $controller = new ChecadorController($conn);
    echo "<p style='color: green;'>✅ Controlador cargado exitosamente</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error cargando controlador: " . $e->getMessage() . "</p>";
}

// Test 2: Probar registrarAsistencia con datos válidos
echo "<h2>✅ Test 2: Registrar Asistencia</h2>";
try {
    $resultado = $controller->registrarAsistencia(
        1, // usuario_id
        'entrada', // tipo
        19.4326, // latitud
        -99.1332, // longitud
        date('Y-m-d H:i:s') // timestamp
    );
    
    echo "<p><strong>Resultado:</strong></p>";
    echo "<pre>" . json_encode($resultado, JSON_PRETTY_PRINT) . "</pre>";
    
    if ($resultado['success']) {
        echo "<p style='color: green;'>✅ Registro de asistencia exitoso</p>";
    } else {
        echo "<p style='color: orange;'>⚠️ " . $resultado['message'] . "</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error en registro: " . $e->getMessage() . "</p>";
}

// Test 3: Probar obtenerUbicacionesUsuario
echo "<h2>✅ Test 3: Obtener Ubicaciones</h2>";
try {
    $resultado = $controller->obtenerUbicacionesUsuario(1);
    
    echo "<p><strong>Resultado:</strong></p>";
    echo "<pre>" . json_encode($resultado, JSON_PRETTY_PRINT) . "</pre>";
    
    if ($resultado['success']) {
        echo "<p style='color: green;'>✅ Obtención de ubicaciones exitosa</p>";
    } else {
        echo "<p style='color: orange;'>⚠️ " . $resultado['message'] . "</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error obteniendo ubicaciones: " . $e->getMessage() . "</p>";
}

// Test 4: Probar verificarUbicacion
echo "<h2>✅ Test 4: Verificar Ubicación</h2>";
try {
    $resultado = $controller->verificarUbicacion(1, 19.4326, -99.1332);
    
    echo "<p><strong>Resultado:</strong></p>";
    echo "<pre>" . json_encode($resultado, JSON_PRETTY_PRINT) . "</pre>";
    
    if ($resultado['success']) {
        echo "<p style='color: green;'>✅ Verificación de ubicación exitosa</p>";
    } else {
        echo "<p style='color: orange;'>⚠️ " . $resultado['message'] . "</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error verificando ubicación: " . $e->getMessage() . "</p>";
}

echo "<h2>🎉 Tests Completados</h2>";
echo "<p>Si todos los tests muestran ✅, el error de bind_param está corregido.</p>";
?>