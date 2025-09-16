<?php
// Script simplificado para actualizar la tabla Bitacora_Limpieza
// Evita problemas de permisos en hosting compartido
error_reporting(E_ALL);
ini_set('display_errors', 1);

include_once "Controladores/ControladorUsuario.php";

// Verificar sesión administrativa
if(!isset($_SESSION['ControlMaestro']) && !isset($_SESSION['AdministradorRH']) && !isset($_SESSION['Marketing'])){
    die("Acceso denegado. Solo administradores pueden ejecutar este script.");
}

echo "<h2>Actualización Simplificada - Bitácoras de Limpieza</h2>";
echo "<p><strong>Nota:</strong> Este script evita problemas de permisos en hosting compartido</p>";

try {
    // Verificar conexión
    if (!isset($conn) || !$conn) {
        throw new Exception("No hay conexión a la base de datos");
    }
    
    echo "<p>✅ Conexión a base de datos establecida</p>";
    
    // Paso 1: Verificar si el campo sucursal_id ya existe
    $checkField = "SHOW COLUMNS FROM Bitacora_Limpieza LIKE 'sucursal_id'";
    $result = mysqli_query($conn, $checkField);
    
    if (mysqli_num_rows($result) > 0) {
        echo "<p>✅ El campo 'sucursal_id' ya existe</p>";
    } else {
        echo "<p>🔄 Agregando campo 'sucursal_id'...</p>";
        
        $sql1 = "ALTER TABLE `Bitacora_Limpieza` ADD COLUMN `sucursal_id` int(11) DEFAULT NULL AFTER `aux_res`";
        
        if (mysqli_query($conn, $sql1)) {
            echo "<p>✅ Campo 'sucursal_id' agregado exitosamente</p>";
        } else {
            throw new Exception("Error agregando sucursal_id: " . mysqli_error($conn));
        }
    }
    
    // Paso 2: Verificar y agregar created_at
    $checkCreatedAt = "SHOW COLUMNS FROM Bitacora_Limpieza LIKE 'created_at'";
    $result2 = mysqli_query($conn, $checkCreatedAt);
    
    if (mysqli_num_rows($result2) == 0) {
        echo "<p>🔄 Agregando campo 'created_at'...</p>";
        
        $sql2 = "ALTER TABLE `Bitacora_Limpieza` ADD COLUMN `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP AFTER `firma_aux_res`";
        
        if (mysqli_query($conn, $sql2)) {
            echo "<p>✅ Campo 'created_at' agregado exitosamente</p>";
        } else {
            echo "<p>⚠️ Error agregando created_at: " . mysqli_error($conn) . "</p>";
        }
    } else {
        echo "<p>✅ Campo 'created_at' ya existe</p>";
    }
    
    // Paso 3: Verificar y agregar updated_at
    $checkUpdatedAt = "SHOW COLUMNS FROM Bitacora_Limpieza LIKE 'updated_at'";
    $result3 = mysqli_query($conn, $checkUpdatedAt);
    
    if (mysqli_num_rows($result3) == 0) {
        echo "<p>🔄 Agregando campo 'updated_at'...</p>";
        
        $sql3 = "ALTER TABLE `Bitacora_Limpieza` ADD COLUMN `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP AFTER `created_at`";
        
        if (mysqli_query($conn, $sql3)) {
            echo "<p>✅ Campo 'updated_at' agregado exitosamente</p>";
        } else {
            echo "<p>⚠️ Error agregando updated_at: " . mysqli_error($conn) . "</p>";
        }
    } else {
        echo "<p>✅ Campo 'updated_at' ya existe</p>";
    }
    
    // Paso 4: Obtener sucursales disponibles
    echo "<p>🔄 Obteniendo sucursales disponibles...</p>";
    $getSucursales = "SELECT ID_Sucursal, Nombre_Sucursal FROM Sucursales WHERE Sucursal_Activa = 'Si' ORDER BY ID_Sucursal";
    $resultSucursales = mysqli_query($conn, $getSucursales);
    
    if ($resultSucursales && mysqli_num_rows($resultSucursales) > 0) {
        echo "<p>✅ Sucursales encontradas:</p>";
        echo "<ul>";
        $sucursales = [];
        while ($sucursal = mysqli_fetch_assoc($resultSucursales)) {
            $sucursales[] = $sucursal;
            echo "<li>ID: {$sucursal['ID_Sucursal']} - {$sucursal['Nombre_Sucursal']}</li>";
        }
        echo "</ul>";
        
        // Usar la primera sucursal como predeterminada
        $sucursalPredeterminada = $sucursales[0]['ID_Sucursal'];
        $nombreSucursal = $sucursales[0]['Nombre_Sucursal'];
        
        echo "<p>🔄 Asignando bitácoras existentes a la sucursal: <strong>$nombreSucursal (ID: $sucursalPredeterminada)</strong></p>";
        
        $sql4 = "UPDATE `Bitacora_Limpieza` SET `sucursal_id` = $sucursalPredeterminada WHERE `sucursal_id` IS NULL";
        
        if (mysqli_query($conn, $sql4)) {
            $affected = mysqli_affected_rows($conn);
            echo "<p>✅ $affected bitácoras existentes asignadas a la sucursal predeterminada</p>";
        } else {
            echo "<p>⚠️ Error asignando bitácoras: " . mysqli_error($conn) . "</p>";
        }
    } else {
        echo "<p>⚠️ No se encontraron sucursales activas. Las bitácoras no se asignarán a ninguna sucursal.</p>";
    }
    
    // Paso 5: Verificar estructura final
    echo "<h3>Estructura final de la tabla Bitacora_Limpieza:</h3>";
    $showTable = "SHOW COLUMNS FROM Bitacora_Limpieza";
    $resultTable = mysqli_query($conn, $showTable);
    
    if ($resultTable) {
        echo "<table border='1' style='border-collapse: collapse; width: 100%; margin: 10px 0;'>";
        echo "<tr style='background-color: #f0f0f0;'><th>Campo</th><th>Tipo</th><th>Nulo</th><th>Clave</th><th>Por defecto</th><th>Extra</th></tr>";
        
        while ($row = mysqli_fetch_assoc($resultTable)) {
            $highlight = ($row['Field'] == 'sucursal_id' || $row['Field'] == 'created_at' || $row['Field'] == 'updated_at') ? 'background-color: #e8f5e8;' : '';
            echo "<tr style='$highlight'>";
            echo "<td><strong>" . $row['Field'] . "</strong></td>";
            echo "<td>" . $row['Type'] . "</td>";
            echo "<td>" . $row['Null'] . "</td>";
            echo "<td>" . $row['Key'] . "</td>";
            echo "<td>" . $row['Default'] . "</td>";
            echo "<td>" . $row['Extra'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    // Paso 6: Verificar datos
    echo "<h3>Verificación de datos:</h3>";
    $checkData = "SELECT id_bitacora, area, sucursal_id FROM Bitacora_Limpieza LIMIT 5";
    $resultData = mysqli_query($conn, $checkData);
    
    if ($resultData) {
        echo "<table border='1' style='border-collapse: collapse; width: 100%; margin: 10px 0;'>";
        echo "<tr style='background-color: #f0f0f0;'><th>ID Bitácora</th><th>Área</th><th>Sucursal ID</th></tr>";
        
        while ($row = mysqli_fetch_assoc($resultData)) {
            echo "<tr>";
            echo "<td>" . $row['id_bitacora'] . "</td>";
            echo "<td>" . $row['area'] . "</td>";
            echo "<td>" . ($row['sucursal_id'] ? $row['sucursal_id'] : 'NULL') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    echo "<div style='background-color: #d4edda; border: 1px solid #c3e6cb; padding: 15px; margin: 20px 0; border-radius: 5px;'>";
    echo "<h3 style='color: #155724; margin-top: 0;'>🎉 ¡Actualización Completada Exitosamente!</h3>";
    echo "<p><strong>Ahora puedes:</strong></p>";
    echo "<ul>";
    echo "<li>✅ Crear bitácoras asignadas a sucursales específicas</li>";
    echo "<li>✅ Filtrar bitácoras por sucursal</li>";
    echo "<li>✅ Ver el nombre de la sucursal en la tabla</li>";
    echo "<li>✅ Exportar datos con información de sucursal</li>";
    echo "</ul>";
    echo "<p><a href='BitacoraLimpieza.php' style='background-color: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Ir al Control de Bitácoras</a></p>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<div style='background-color: #f8d7da; border: 1px solid #f5c6cb; padding: 15px; margin: 20px 0; border-radius: 5px;'>";
    echo "<h3 style='color: #721c24; margin-top: 0;'>❌ Error durante la actualización:</h3>";
    echo "<p style='color: #721c24;'>" . $e->getMessage() . "</p>";
    echo "</div>";
}
?>
