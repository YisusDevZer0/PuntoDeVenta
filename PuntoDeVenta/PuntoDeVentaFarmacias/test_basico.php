<?php
// Habilitar reporte de errores
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Test Básico - PuntoDeVentaFarmacias</h1>";

try {
    echo "<h2>1. Incluyendo ControladorUsuario.php</h2>";
    include_once "Controladores/ControladorUsuario.php";
    echo "✅ ControladorUsuario.php incluido<br>";
    
    echo "<h2>2. Verificando variables</h2>";
    if (isset($row)) {
        echo "✅ \$row disponible<br>";
        echo "Usuario ID: " . ($row['Id_PvUser'] ?? 'NO DEFINIDO') . "<br>";
        echo "Sucursal ID: " . ($row['Fk_Sucursal'] ?? 'NO DEFINIDO') . "<br>";
    } else {
        echo "❌ \$row NO disponible<br>";
    }
    
    if (isset($userId)) {
        echo "✅ \$userId disponible: $userId<br>";
    } else {
        echo "❌ \$userId NO disponible<br>";
    }
    
    if (isset($sucursalId)) {
        echo "✅ \$sucursalId disponible: $sucursalId<br>";
    } else {
        echo "❌ \$sucursalId NO disponible<br>";
    }
    
    echo "<h2>3. Verificando conexión a BD</h2>";
    if (isset($conn) && $conn) {
        echo "✅ Conexión a BD disponible<br>";
    } else {
        echo "❌ Conexión a BD NO disponible<br>";
    }
    
    echo "<h2>4. Incluyendo TareasController.php</h2>";
    include_once "Controladores/TareasController.php";
    echo "✅ TareasController.php incluido<br>";
    
    echo "<h2>5. Instanciando TareasController</h2>";
    if (isset($conn) && isset($userId) && isset($sucursalId)) {
        $tareasController = new TareasController($conn, $userId, $sucursalId);
        echo "✅ TareasController instanciado correctamente<br>";
    } else {
        echo "❌ No se puede instanciar TareasController<br>";
        echo "conn: " . (isset($conn) ? 'SÍ' : 'NO') . "<br>";
        echo "userId: " . (isset($userId) ? 'SÍ' : 'NO') . "<br>";
        echo "sucursalId: " . (isset($sucursalId) ? 'SÍ' : 'NO') . "<br>";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
    echo "Archivo: " . $e->getFile() . "<br>";
    echo "Línea: " . $e->getLine() . "<br>";
}

echo "<h2>✅ Test Completado</h2>";
?>