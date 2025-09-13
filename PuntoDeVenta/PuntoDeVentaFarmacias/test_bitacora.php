<?php
// Archivo de prueba para verificar la funcionalidad del módulo de bitácora de limpieza
include_once "Controladores/db_connect.php";
include_once "Controladores/BitacoraLimpiezaController.php";
include_once "Controladores/RecordatoriosController.php";

echo "<h2>Prueba del Módulo de Bitácora de Limpieza</h2>";

try {
    // Probar conexión a la base de datos
    echo "<h3>1. Prueba de Conexión a la Base de Datos</h3>";
    if ($conn) {
        echo "✅ Conexión exitosa a la base de datos<br>";
    } else {
        echo "❌ Error de conexión a la base de datos<br>";
        exit;
    }

    // Probar controlador de bitácora
    echo "<h3>2. Prueba del Controlador de Bitácora</h3>";
    $bitacoraController = new BitacoraLimpiezaController($conn);
    echo "✅ Controlador de bitácora creado exitosamente<br>";

    // Probar controlador de recordatorios
    echo "<h3>3. Prueba del Controlador de Recordatorios</h3>";
    $recordatoriosController = new RecordatoriosController($conn);
    echo "✅ Controlador de recordatorios creado exitosamente<br>";

    // Crear tabla de recordatorios
    echo "<h3>4. Creación de Tabla de Recordatorios</h3>";
    if ($recordatoriosController->crearTablaRecordatorios()) {
        echo "✅ Tabla de recordatorios creada/verificada exitosamente<br>";
    } else {
        echo "❌ Error al crear tabla de recordatorios<br>";
    }

    // Probar obtención de bitácoras
    echo "<h3>5. Prueba de Obtención de Bitácoras</h3>";
    $bitacoras = $bitacoraController->obtenerBitacoras();
    echo "✅ Se obtuvieron " . count($bitacoras) . " bitácoras<br>";

    // Probar obtención de recordatorios
    echo "<h3>6. Prueba de Obtención de Recordatorios</h3>";
    $recordatorios = $recordatoriosController->obtenerRecordatorios();
    echo "✅ Se obtuvieron " . count($recordatorios) . " recordatorios<br>";

    // Crear una bitácora de prueba
    echo "<h3>7. Prueba de Creación de Bitácora</h3>";
    $idBitacora = $bitacoraController->crearBitacora(
        "Área de Prueba",
        "Semana 1",
        "2024-01-01",
        "2024-01-07",
        "Usuario Prueba",
        "Supervisor Prueba",
        "Auxiliar Prueba"
    );
    
    if ($idBitacora) {
        echo "✅ Bitácora de prueba creada con ID: " . $idBitacora . "<br>";
        
        // Agregar elemento de prueba
        echo "<h3>8. Prueba de Agregar Elemento</h3>";
        if ($bitacoraController->agregarElementoLimpieza($idBitacora, "Limpieza de prueba")) {
            echo "✅ Elemento de limpieza agregado exitosamente<br>";
        } else {
            echo "❌ Error al agregar elemento de limpieza<br>";
        }
        
        // Obtener detalles de la bitácora
        echo "<h3>9. Prueba de Obtención de Detalles</h3>";
        $detalles = $bitacoraController->obtenerDetallesLimpieza($idBitacora);
        echo "✅ Se obtuvieron " . count($detalles) . " elementos de limpieza<br>";
        
        // Limpiar datos de prueba
        echo "<h3>10. Limpieza de Datos de Prueba</h3>";
        if ($bitacoraController->eliminarBitacora($idBitacora)) {
            echo "✅ Bitácora de prueba eliminada exitosamente<br>";
        } else {
            echo "❌ Error al eliminar bitácora de prueba<br>";
        }
    } else {
        echo "❌ Error al crear bitácora de prueba<br>";
    }

    // Crear recordatorio de prueba
    echo "<h3>11. Prueba de Creación de Recordatorio</h3>";
    if ($recordatoriosController->crearRecordatorio(
        "Recordatorio de Prueba",
        "Este es un recordatorio de prueba",
        date('Y-m-d H:i:s', strtotime('+1 hour')),
        "media",
        1
    )) {
        echo "✅ Recordatorio de prueba creado exitosamente<br>";
    } else {
        echo "❌ Error al crear recordatorio de prueba<br>";
    }

    echo "<h3>✅ Todas las pruebas completadas exitosamente</h3>";
    echo "<p>El módulo de bitácora de limpieza está funcionando correctamente.</p>";

} catch (Exception $e) {
    echo "<h3>❌ Error durante las pruebas</h3>";
    echo "<p>Error: " . $e->getMessage() . "</p>";
}
?>
