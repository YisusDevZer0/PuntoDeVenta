<?php
/**
 * Script de Instalación del Sistema de Devoluciones
 * Doctor Pez - Sistema de Clínicas y Farmacias
 */

session_start();
require_once '../Consultas/db_connect.php';

// Verificar que el usuario sea administrador
if (!isset($_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] !== 'Administrador') {
    die('Error: Solo los administradores pueden ejecutar este script.');
}

$errores = [];
$exitos = [];

echo "<h2>Instalador del Sistema de Devoluciones - Doctor Pez</h2>";
echo "<hr>";

// Función para ejecutar SQL y mostrar resultado
function ejecutarSQL($sql, $descripcion) {
    global $conn, $errores, $exitos;
    
    echo "<p><strong>$descripcion...</strong> ";
    
    try {
        if ($conn->multi_query($sql)) {
            // Procesar todos los resultados
            do {
                if ($result = $conn->store_result()) {
                    $result->free();
                }
            } while ($conn->next_result());
            
            echo "<span style='color: green;'>✓ ÉXITO</span></p>";
            $exitos[] = $descripcion;
            return true;
        } else {
            echo "<span style='color: red;'>✗ ERROR: " . $conn->error . "</span></p>";
            $errores[] = "$descripcion: " . $conn->error;
            return false;
        }
    } catch (Exception $e) {
        echo "<span style='color: red;'>✗ EXCEPCIÓN: " . $e->getMessage() . "</span></p>";
        $errores[] = "$descripcion: " . $e->getMessage();
        return false;
    }
}

// Leer el archivo SQL
$sqlFile = 'sql/devoluciones_final.sql';
if (!file_exists($sqlFile)) {
    die("<p style='color: red;'>Error: No se encontró el archivo SQL: $sqlFile</p>");
}

$sqlContent = file_get_contents($sqlFile);
if ($sqlContent === false) {
    die("<p style='color: red;'>Error: No se pudo leer el archivo SQL</p>");
}

echo "<h3>Ejecutando instalación...</h3>";

// Ejecutar el script SQL completo
ejecutarSQL($sqlContent, "Instalando tablas y estructuras del sistema de devoluciones");

// Verificar que las tablas se crearon correctamente
echo "<h3>Verificando instalación...</h3>";

$tablasRequeridas = [
    'Devoluciones',
    'Devoluciones_Detalle', 
    'Tipos_Devolucion',
    'Devoluciones_Autorizaciones',
    'Devoluciones_Acciones',
    'Devoluciones_Reportes'
];

foreach ($tablasRequeridas as $tabla) {
    $sql = "SHOW TABLES LIKE '$tabla'";
    $result = $conn->query($sql);
    
    if ($result && $result->num_rows > 0) {
        echo "<p><strong>Verificando tabla $tabla...</strong> <span style='color: green;'>✓ EXISTE</span></p>";
    } else {
        echo "<p><strong>Verificando tabla $tabla...</strong> <span style='color: red;'>✗ NO EXISTE</span></p>";
        $errores[] = "La tabla $tabla no se creó correctamente";
    }
}

// Verificar vistas
$vistasRequeridas = [
    'v_devoluciones_completas',
    'v_devoluciones_detalle_completo',
    'v_estadisticas_devoluciones',
    'v_productos_mas_devueltos'
];

foreach ($vistasRequeridas as $vista) {
    $sql = "SHOW TABLES LIKE '$vista'";
    $result = $conn->query($sql);
    
    if ($result && $result->num_rows > 0) {
        echo "<p><strong>Verificando vista $vista...</strong> <span style='color: green;'>✓ EXISTE</span></p>";
    } else {
        echo "<p><strong>Verificando vista $vista...</strong> <span style='color: red;'>✗ NO EXISTE</span></p>";
        $errores[] = "La vista $vista no se creó correctamente";
    }
}

// Verificar datos iniciales
echo "<h3>Verificando datos iniciales...</h3>";

$sql = "SELECT COUNT(*) as total FROM Tipos_Devolucion";
$result = $conn->query($sql);
$row = $result->fetch_assoc();

if ($row['total'] > 0) {
    echo "<p><strong>Verificando tipos de devolución...</strong> <span style='color: green;'>✓ {$row['total']} tipos configurados</span></p>";
} else {
    echo "<p><strong>Verificando tipos de devolución...</strong> <span style='color: red;'>✗ No hay tipos configurados</span></p>";
    $errores[] = "No se insertaron los tipos de devolución por defecto";
}

// Verificar permisos de archivos
echo "<h3>Verificando archivos del sistema...</h3>";

$archivosRequeridos = [
    'Devoluciones.php',
    'ReportesDevoluciones.php',
    'api/devoluciones_api.php',
    'js/devoluciones_scanner.js'
];

foreach ($archivosRequeridos as $archivo) {
    if (file_exists($archivo)) {
        echo "<p><strong>Verificando archivo $archivo...</strong> <span style='color: green;'>✓ EXISTE</span></p>";
    } else {
        echo "<p><strong>Verificando archivo $archivo...</strong> <span style='color: red;'>✗ NO EXISTE</span></p>";
        $errores[] = "El archivo $archivo no existe";
    }
}

// Crear datos de prueba (opcional)
if (isset($_POST['crear_datos_prueba']) && $_POST['crear_datos_prueba'] == '1') {
    echo "<h3>Creando datos de prueba...</h3>";
    
    $sqlPrueba = "
        INSERT IGNORE INTO Devoluciones (folio, sucursal_id, usuario_id, observaciones_generales, estatus) 
        VALUES ('DEV-PRUEBA-001', 1, 1, 'Devolución de prueba del sistema', 'procesada');
        
        SET @devolucion_id = LAST_INSERT_ID();
        
        INSERT IGNORE INTO Devoluciones_Detalle 
        (devolucion_id, producto_id, codigo_barras, nombre_producto, cantidad, tipo_devolucion, 
         observaciones, precio_venta, valor_total) 
        VALUES 
        (@devolucion_id, 1, '123456789', 'Producto de Prueba', 2, 'otro', 
         'Producto de prueba para verificar funcionamiento', 10.50, 21.00);
    ";
    
    ejecutarSQL($sqlPrueba, "Creando datos de prueba");
}

// Resumen final
echo "<hr>";
echo "<h3>Resumen de Instalación</h3>";

if (empty($errores)) {
    echo "<div style='background-color: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<h4>✅ ¡Instalación Completada Exitosamente!</h4>";
    echo "<p>El Sistema de Devoluciones se ha instalado correctamente.</p>";
    echo "<ul>";
    foreach ($exitos as $exito) {
        echo "<li>$exito</li>";
    }
    echo "</ul>";
    echo "<p><strong>Próximos pasos:</strong></p>";
    echo "<ul>";
    echo "<li>Acceder al módulo desde el menú principal: Farmacia > Devoluciones</li>";
    echo "<li>Configurar tipos de devolución adicionales si es necesario</li>";
    echo "<li>Capacitar al personal en el uso del sistema</li>";
    echo "<li>Revisar los reportes de devoluciones</li>";
    echo "</ul>";
    echo "</div>";
} else {
    echo "<div style='background-color: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<h4>⚠️ Instalación con Errores</h4>";
    echo "<p>Se encontraron los siguientes errores durante la instalación:</p>";
    echo "<ul>";
    foreach ($errores as $error) {
        echo "<li style='color: red;'>$error</li>";
    }
    echo "</ul>";
    
    if (!empty($exitos)) {
        echo "<p><strong>Elementos instalados correctamente:</strong></p>";
        echo "<ul>";
        foreach ($exitos as $exito) {
            echo "<li style='color: green;'>$exito</li>";
        }
        echo "</ul>";
    }
    
    echo "<p><strong>Recomendaciones:</strong></p>";
    echo "<ul>";
    echo "<li>Revisar los errores mostrados arriba</li>";
    echo "<li>Verificar permisos de base de datos</li>";
    echo "<li>Ejecutar nuevamente el instalador</li>";
    echo "<li>Contactar al administrador del sistema si persisten los errores</li>";
    echo "</ul>";
    echo "</div>";
}

// Información adicional
echo "<hr>";
echo "<h3>Información del Sistema</h3>";
echo "<table border='1' cellpadding='10' cellspacing='0' style='border-collapse: collapse; width: 100%;'>";
echo "<tr><td><strong>Base de Datos:</strong></td><td>" . $conn->get_server_info() . "</td></tr>";
echo "<tr><td><strong>Usuario Instalador:</strong></td><td>" . ($_SESSION['usuario_nombre'] ?? 'N/A') . "</td></tr>";
echo "<tr><td><strong>Fecha de Instalación:</strong></td><td>" . date('Y-m-d H:i:s') . "</td></tr>";
echo "<tr><td><strong>Versión PHP:</strong></td><td>" . phpversion() . "</td></tr>";
echo "</table>";

// Formulario para crear datos de prueba
if (empty($_POST)) {
    echo "<hr>";
    echo "<h3>Opciones Adicionales</h3>";
    echo "<form method='POST'>";
    echo "<label><input type='checkbox' name='crear_datos_prueba' value='1'> Crear datos de prueba</label><br><br>";
    echo "<button type='submit' style='background-color: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer;'>Ejecutar Instalación</button>";
    echo "</form>";
}

$conn->close();
?>

<style>
body {
    font-family: Arial, sans-serif;
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
    background-color: #f8f9fa;
}

h2, h3 {
    color: #333;
}

p {
    margin: 5px 0;
}

hr {
    border: none;
    border-top: 2px solid #dee2e6;
    margin: 20px 0;
}

table {
    background-color: white;
    margin: 10px 0;
}

button:hover {
    background-color: #0056b3 !important;
}
</style>
