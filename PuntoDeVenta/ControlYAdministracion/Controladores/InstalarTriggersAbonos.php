<?php
// Script para instalar los triggers de abonos de encargos
include_once "db_connect.php";

// Verificar conexión
if (!isset($conn) || !$conn) {
    die("Error de conexión a la base de datos");
}

echo "<h2>Instalando Triggers de Abonos de Encargos</h2>";

// Array con los triggers a crear
$triggers = [
    'tr_abono_encargo_insert' => "
        CREATE TRIGGER IF NOT EXISTS tr_abono_encargo_insert 
        AFTER INSERT ON encargos
        FOR EACH ROW
        BEGIN
            IF NEW.abono_parcial > 0 THEN
                UPDATE Cajas 
                SET Valor_Total_Caja = Valor_Total_Caja + NEW.abono_parcial
                WHERE ID_Caja = NEW.Fk_Caja;
            END IF;
        END
    ",
    
    'tr_abono_encargo_update' => "
        CREATE TRIGGER IF NOT EXISTS tr_abono_encargo_update 
        AFTER UPDATE ON encargos
        FOR EACH ROW
        BEGIN
            IF OLD.abono_parcial != NEW.abono_parcial THEN
                IF OLD.abono_parcial > 0 THEN
                    UPDATE Cajas 
                    SET Valor_Total_Caja = Valor_Total_Caja - OLD.abono_parcial
                    WHERE ID_Caja = OLD.Fk_Caja;
                END IF;
                
                IF NEW.abono_parcial > 0 THEN
                    UPDATE Cajas 
                    SET Valor_Total_Caja = Valor_Total_Caja + NEW.abono_parcial
                    WHERE ID_Caja = NEW.Fk_Caja;
                END IF;
            END IF;
        END
    ",
    
    'tr_abono_encargo_delete' => "
        CREATE TRIGGER IF NOT EXISTS tr_abono_encargo_delete 
        AFTER DELETE ON encargos
        FOR EACH ROW
        BEGIN
            IF OLD.abono_parcial > 0 THEN
                UPDATE Cajas 
                SET Valor_Total_Caja = Valor_Total_Caja - OLD.abono_parcial
                WHERE ID_Caja = OLD.Fk_Caja;
            END IF;
        END
    "
];

// Instalar cada trigger
foreach ($triggers as $trigger_name => $trigger_sql) {
    echo "<p>Instalando trigger: <strong>$trigger_name</strong></p>";
    
    // Primero eliminar el trigger si existe
    $drop_sql = "DROP TRIGGER IF EXISTS $trigger_name";
    if (mysqli_query($conn, $drop_sql)) {
        echo "<p style='color: green;'>✓ Trigger anterior eliminado (si existía)</p>";
    } else {
        echo "<p style='color: orange;'>⚠ No se pudo eliminar trigger anterior: " . mysqli_error($conn) . "</p>";
    }
    
    // Crear el nuevo trigger
    if (mysqli_query($conn, $trigger_sql)) {
        echo "<p style='color: green;'>✓ Trigger <strong>$trigger_name</strong> instalado correctamente</p>";
    } else {
        echo "<p style='color: red;'>✗ Error al instalar trigger <strong>$trigger_name</strong>: " . mysqli_error($conn) . "</p>";
    }
    
    echo "<hr>";
}

echo "<h3>Instalación completada</h3>";
echo "<p>Los triggers están configurados para:</p>";
echo "<ul>";
echo "<li><strong>INSERT:</strong> Sumar automáticamente el abono parcial al valor total de la caja</li>";
echo "<li><strong>UPDATE:</strong> Ajustar el valor de la caja si cambia el abono parcial</li>";
echo "<li><strong>DELETE:</strong> Restar el abono parcial si se elimina un encargo</li>";
echo "</ul>";

echo "<p><strong>Nota:</strong> Los triggers solo se ejecutan cuando abono_parcial > 0</p>";

mysqli_close($conn);
?> 