<?php
// Script simple para instalar triggers de abonos
include_once "Controladores/db_connect.php";

echo "<h2>Instalando Triggers de Abonos</h2>";

// Trigger 1: INSERT
$sql1 = "DROP TRIGGER IF EXISTS tr_abono_encargo_insert";
mysqli_query($conn, $sql1);

$sql2 = "CREATE TRIGGER tr_abono_encargo_insert 
         AFTER INSERT ON encargos 
         FOR EACH ROW 
         BEGIN 
             IF NEW.abono_parcial > 0 THEN 
                 UPDATE Cajas 
                 SET Valor_Total_Caja = Valor_Total_Caja + NEW.abono_parcial 
                 WHERE ID_Caja = NEW.Fk_Caja; 
             END IF; 
         END";

if (mysqli_query($conn, $sql2)) {
    echo "<p style='color: green;'>✓ Trigger INSERT instalado</p>";
} else {
    echo "<p style='color: red;'>✗ Error: " . mysqli_error($conn) . "</p>";
}

// Trigger 2: UPDATE
$sql3 = "DROP TRIGGER IF EXISTS tr_abono_encargo_update";
mysqli_query($conn, $sql3);

$sql4 = "CREATE TRIGGER tr_abono_encargo_update 
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
         END";

if (mysqli_query($conn, $sql4)) {
    echo "<p style='color: green;'>✓ Trigger UPDATE instalado</p>";
} else {
    echo "<p style='color: red;'>✗ Error: " . mysqli_error($conn) . "</p>";
}

// Trigger 3: DELETE
$sql5 = "DROP TRIGGER IF EXISTS tr_abono_encargo_delete";
mysqli_query($conn, $sql5);

$sql6 = "CREATE TRIGGER tr_abono_encargo_delete 
         AFTER DELETE ON encargos 
         FOR EACH ROW 
         BEGIN 
             IF OLD.abono_parcial > 0 THEN 
                 UPDATE Cajas 
                 SET Valor_Total_Caja = Valor_Total_Caja - OLD.abono_parcial 
                 WHERE ID_Caja = OLD.Fk_Caja; 
             END IF; 
         END";

if (mysqli_query($conn, $sql6)) {
    echo "<p style='color: green;'>✓ Trigger DELETE instalado</p>";
} else {
    echo "<p style='color: red;'>✗ Error: " . mysqli_error($conn) . "</p>";
}

echo "<h3>✅ Instalación completada</h3>";
echo "<p>Ahora los abonos se sumarán automáticamente a las cajas.</p>";

mysqli_close($conn);
?> 