<?php
// Script para actualizar la tabla Bitacora_Limpieza y agregar el campo sucursal_id
error_reporting(E_ALL);
ini_set('display_errors', 1);

include_once "Controladores/ControladorUsuario.php";

// Verificar sesión administrativa
if(!isset($_SESSION['ControlMaestro']) && !isset($_SESSION['AdministradorRH']) && !isset($_SESSION['Marketing'])){
    die("Acceso denegado. Solo administradores pueden ejecutar este script.");
}

echo "<h2>Actualización de Base de Datos - Bitácoras de Limpieza</h2>";

try {
    // Verificar conexión
    if (!isset($conn) || !$conn) {
        throw new Exception("No hay conexión a la base de datos");
    }
    
    echo "<p>✅ Conexión a base de datos establecida</p>";
    
    // Verificar si el campo ya existe
    $checkField = "SHOW COLUMNS FROM Bitacora_Limpieza LIKE 'sucursal_id'";
    $result = mysqli_query($conn, $checkField);
    
    if (mysqli_num_rows($result) > 0) {
        echo "<p>⚠️ El campo 'sucursal_id' ya existe en la tabla Bitacora_Limpieza</p>";
    } else {
        echo "<p>🔄 Agregando campo 'sucursal_id' a la tabla Bitacora_Limpieza...</p>";
        
        // Agregar el campo sucursal_id
        $sql1 = "ALTER TABLE `Bitacora_Limpieza` 
                 ADD COLUMN `sucursal_id` int(11) DEFAULT NULL AFTER `aux_res`";
        
        if (mysqli_query($conn, $sql1)) {
            echo "<p>✅ Campo 'sucursal_id' agregado exitosamente</p>";
        } else {
            throw new Exception("Error agregando campo sucursal_id: " . mysqli_error($conn));
        }
    }
    
    // Verificar si el campo created_at ya existe
    $checkCreatedAt = "SHOW COLUMNS FROM Bitacora_Limpieza LIKE 'created_at'";
    $result2 = mysqli_query($conn, $checkCreatedAt);
    
    if (mysqli_num_rows($result2) == 0) {
        echo "<p>🔄 Agregando campo 'created_at'...</p>";
        
        $sql2 = "ALTER TABLE `Bitacora_Limpieza` 
                 ADD COLUMN `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP AFTER `firma_aux_res`";
        
        if (mysqli_query($conn, $sql2)) {
            echo "<p>✅ Campo 'created_at' agregado exitosamente</p>";
        } else {
            echo "<p>⚠️ Error agregando created_at: " . mysqli_error($conn) . "</p>";
        }
    } else {
        echo "<p>✅ Campo 'created_at' ya existe</p>";
    }
    
    // Verificar si el campo updated_at ya existe
    $checkUpdatedAt = "SHOW COLUMNS FROM Bitacora_Limpieza LIKE 'updated_at'";
    $result3 = mysqli_query($conn, $checkUpdatedAt);
    
    if (mysqli_num_rows($result3) == 0) {
        echo "<p>🔄 Agregando campo 'updated_at'...</p>";
        
        $sql3 = "ALTER TABLE `Bitacora_Limpieza` 
                 ADD COLUMN `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP AFTER `created_at`";
        
        if (mysqli_query($conn, $sql3)) {
            echo "<p>✅ Campo 'updated_at' agregado exitosamente</p>";
        } else {
            echo "<p>⚠️ Error agregando updated_at: " . mysqli_error($conn) . "</p>";
        }
    } else {
        echo "<p>✅ Campo 'updated_at' ya existe</p>";
    }
    
    // Agregar índice si no existe
    $checkIndex = "SHOW INDEX FROM Bitacora_Limpieza WHERE Key_name = 'idx_sucursal_id'";
    $result4 = mysqli_query($conn, $checkIndex);
    
    if (mysqli_num_rows($result4) == 0) {
        echo "<p>🔄 Agregando índice para sucursal_id...</p>";
        
        $sql4 = "ALTER TABLE `Bitacora_Limpieza` ADD INDEX `idx_sucursal_id` (`sucursal_id`)";
        
        if (mysqli_query($conn, $sql4)) {
            echo "<p>✅ Índice agregado exitosamente</p>";
        } else {
            echo "<p>⚠️ Error agregando índice: " . mysqli_error($conn) . "</p>";
        }
    } else {
        echo "<p>✅ Índice ya existe</p>";
    }
    
    // Obtener la primera sucursal disponible para asignar a bitácoras existentes
    $getSucursal = "SELECT Id_Sucursal FROM Sucursales WHERE Estado = 1 ORDER BY Id_Sucursal LIMIT 1";
    $resultSucursal = mysqli_query($conn, $getSucursal);
    
    if ($resultSucursal && mysqli_num_rows($resultSucursal) > 0) {
        $sucursal = mysqli_fetch_assoc($resultSucursal);
        $sucursalId = $sucursal['Id_Sucursal'];
        
        echo "<p>🔄 Asignando bitácoras existentes a la sucursal ID: $sucursalId...</p>";
        
        $sql5 = "UPDATE `Bitacora_Limpieza` SET `sucursal_id` = $sucursalId WHERE `sucursal_id` IS NULL";
        
        if (mysqli_query($conn, $sql5)) {
            $affected = mysqli_affected_rows($conn);
            echo "<p>✅ $affected bitácoras existentes asignadas a la sucursal</p>";
        } else {
            echo "<p>⚠️ Error asignando bitácoras: " . mysqli_error($conn) . "</p>";
        }
    } else {
        echo "<p>⚠️ No se encontraron sucursales activas</p>";
    }
    
    // Verificar la estructura final de la tabla
    echo "<h3>Estructura final de la tabla Bitacora_Limpieza:</h3>";
    $showTable = "DESCRIBE Bitacora_Limpieza";
    $resultTable = mysqli_query($conn, $showTable);
    
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>Campo</th><th>Tipo</th><th>Nulo</th><th>Clave</th><th>Por defecto</th><th>Extra</th></tr>";
    
    while ($row = mysqli_fetch_assoc($resultTable)) {
        echo "<tr>";
        echo "<td>" . $row['Field'] . "</td>";
        echo "<td>" . $row['Type'] . "</td>";
        echo "<td>" . $row['Null'] . "</td>";
        echo "<td>" . $row['Key'] . "</td>";
        echo "<td>" . $row['Default'] . "</td>";
        echo "<td>" . $row['Extra'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<h3>🎉 Actualización completada exitosamente</h3>";
    echo "<p><a href='BitacoraLimpieza.php'>Ir al Control de Bitácoras</a></p>";
    
} catch (Exception $e) {
    echo "<h3>❌ Error durante la actualización:</h3>";
    echo "<p style='color: red;'>" . $e->getMessage() . "</p>";
}
?>
