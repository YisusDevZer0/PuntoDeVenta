<?php
// Script de debug para verificar la generación del número de ticket
include_once "Controladores/db_connect.php";

echo "<h2>Debug - Generación de Número de Ticket</h2>";

// Simular los datos que vienen del modal
$caja_id = isset($_GET['caja_id']) ? (int)$_GET['caja_id'] : 1;

// Obtener datos de la caja
$sql1 = "SELECT c.ID_Caja, c.Sucursal, s.Nombre_Sucursal 
         FROM Cajas c 
         INNER JOIN Sucursales s ON c.Sucursal = s.ID_Sucursal 
         WHERE c.ID_Caja = ?";

$stmt = $conn->prepare($sql1);
if ($stmt) {
    $stmt->bind_param("i", $caja_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $caja = $result->fetch_object();
    $stmt->close();
    
    if ($caja) {
        echo "<h3>Datos de la caja:</h3>";
        echo "<p><strong>ID_Caja:</strong> " . $caja->ID_Caja . "</p>";
        echo "<p><strong>Sucursal:</strong> " . $caja->Sucursal . "</p>";
        echo "<p><strong>Nombre_Sucursal:</strong> " . $caja->Nombre_Sucursal . "</p>";
        
        // Obtener las 3 primeras letras de la sucursal
        $primeras_tres_letras = substr($caja->Nombre_Sucursal, 0, 3);
        $primeras_tres_letras = strtoupper($primeras_tres_letras);
        
        echo "<p><strong>Primeras 3 letras:</strong> " . $primeras_tres_letras . "</p>";
        
        // Consulta para obtener el último ticket
        $sql_ticket = "SELECT NumTicket 
                       FROM encargos 
                       WHERE Fk_Sucursal = ? 
                       AND NumTicket LIKE ? 
                       ORDER BY CAST(SUBSTRING(NumTicket, ?) AS UNSIGNED) DESC 
                       LIMIT 1";
        
        $patron = $primeras_tres_letras . 'ENC-%';
        $posicion = strlen($primeras_tres_letras) + 4;
        
        echo "<h3>Parámetros de búsqueda:</h3>";
        echo "<p><strong>Patrón:</strong> " . $patron . "</p>";
        echo "<p><strong>Posición:</strong> " . $posicion . "</p>";
        
        $stmt_ticket = $conn->prepare($sql_ticket);
        if ($stmt_ticket) {
            $stmt_ticket->bind_param("isi", $caja->Sucursal, $patron, $posicion);
            $stmt_ticket->execute();
            $result_ticket = $stmt_ticket->get_result();
            $row_ticket = $result_ticket->fetch_assoc();
            
            echo "<h3>Resultado de la consulta:</h3>";
            if ($row_ticket) {
                $ultimo_ticket = $row_ticket['NumTicket'];
                echo "<p><strong>Último ticket encontrado:</strong> " . $ultimo_ticket . "</p>";
                
                // Extraer el número usando la nueva lógica
                $pos_guion = strpos($ultimo_ticket, '-');
                if ($pos_guion !== false) {
                    $numero_actual = (int)substr($ultimo_ticket, $pos_guion + 1);
                    echo "<p><strong>Posición del guión:</strong> " . $pos_guion . "</p>";
                    echo "<p><strong>Número extraído:</strong> " . $numero_actual . "</p>";
                } else {
                    $numero_actual = 0;
                    echo "<p><strong>No se encontró guión en el ticket</strong></p>";
                }
                
                $siguiente_numero = $numero_actual + 1;
                echo "<p><strong>Siguiente número:</strong> " . $siguiente_numero . "</p>";
            } else {
                echo "<p><strong>No se encontraron tickets previos</strong></p>";
                $siguiente_numero = 1;
            }
            $stmt_ticket->close();
        } else {
            echo "<p style='color: red;'>Error al preparar la consulta del ticket</p>";
            $siguiente_numero = 1;
        }
        
        // Generar el nuevo ticket
        $NumTicket = $primeras_tres_letras . 'ENC-' . str_pad($siguiente_numero, 4, '0', STR_PAD_LEFT);
        
        echo "<h3>Nuevo ticket generado:</h3>";
        echo "<p><strong>Ticket:</strong> " . $NumTicket . "</p>";
        
        // Mostrar todos los tickets existentes para esta sucursal
        echo "<h3>Todos los tickets existentes para esta sucursal:</h3>";
        $sql_todos = "SELECT id, NumTicket, Fk_Sucursal 
                      FROM encargos 
                      WHERE Fk_Sucursal = ? 
                      AND NumTicket LIKE ? 
                      ORDER BY id DESC";
        
        $stmt_todos = $conn->prepare($sql_todos);
        if ($stmt_todos) {
            $stmt_todos->bind_param("is", $caja->Sucursal, $patron);
            $stmt_todos->execute();
            $result_todos = $stmt_todos->get_result();
            
            if (mysqli_num_rows($result_todos) > 0) {
                echo "<table border='1' style='border-collapse: collapse;'>";
                echo "<tr><th>ID</th><th>Ticket</th><th>Sucursal</th></tr>";
                while ($row = mysqli_fetch_assoc($result_todos)) {
                    echo "<tr>";
                    echo "<td>" . $row['id'] . "</td>";
                    echo "<td>" . $row['NumTicket'] . "</td>";
                    echo "<td>" . $row['Fk_Sucursal'] . "</td>";
                    echo "</tr>";
                }
                echo "</table>";
            } else {
                echo "<p>No hay tickets registrados para esta sucursal.</p>";
            }
            $stmt_todos->close();
        }
        
    } else {
        echo "<p style='color: red;'>No se encontró la caja especificada</p>";
    }
} else {
    echo "<p style='color: red;'>Error al preparar la consulta de la caja</p>";
}

mysqli_close($conn);
?> 