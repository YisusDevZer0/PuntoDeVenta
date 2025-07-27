<?php
header('Content-Type: application/json');
include_once "../Controladores/db_connect.php";
include_once "../Controladores/ControladorUsuario.php";

// Verificar sesión
if(!isset($_SESSION['ControlMaestro']) && !isset($_SESSION['AdministradorRH']) && !isset($_SESSION['Marketing'])){
    echo json_encode(['success' => false, 'message' => 'Sesión no válida']);
    exit();
}

try {
    // Consulta para encargos disponibles usando la tabla correcta
    $sql = "SELECT 
                id,
                nombre_paciente,
                medicamento,
                cantidad,
                precioventa,
                fecha_encargo,
                estado,
                costo,
                abono_parcial,
                NumTicket,
                Fk_Sucursal,
                Fk_Caja,
                Empleado,
                FormaDePago
            FROM encargos
            WHERE estado = 'pendiente'
              AND fecha_encargo >= DATE_SUB(NOW(), INTERVAL 30 DAY)
            ORDER BY fecha_encargo DESC";
    
    $result = $conn->query($sql);
    
    if (!$result) {
        throw new Exception("Error en la consulta: " . $conn->error);
    }
    
    $encargos = [];
    while ($row = $result->fetch_assoc()) {
        $encargos[] = [
            'id' => $row['id'],
            'descripcion' => $row['medicamento'],
            'cliente' => $row['nombre_paciente'],
            'fecha' => $row['fecha_encargo'],
            'observaciones' => "Cantidad: " . $row['cantidad'] . " | Empleado: " . $row['Empleado'] . " | Ticket: " . $row['NumTicket'],
            'estado' => $row['estado'],
            'precio' => $row['precioventa'],
            'cantidad' => $row['cantidad'],
            'costo' => $row['costo'],
            'abono_parcial' => $row['abono_parcial'],
            'num_ticket' => $row['NumTicket'],
            'empleado' => $row['Empleado'],
            'forma_pago' => $row['FormaDePago']
        ];
    }
    
    echo json_encode([
        'success' => true,
        'encargos' => $encargos,
        'total' => count($encargos)
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error al obtener encargos: ' . $e->getMessage()
    ]);
}
?> 