<?php
include_once "db_connect.php";
include_once "ControladorUsuario.php";

// Configurar headers para JSON
header('Content-Type: application/json');

try {
    // Validar que se recibieron todos los datos necesarios
    $required_fields = [
        'nombre_paciente', 'medicamento', 'cantidad', 'precioventa', 
        'fecha_encargo', 'costo', 'abono_parcial', 'NumTicket',
        'Fk_Caja', 'Empleado', 'AgregadoPor', 'Fk_sucursal', 'Sistema', 'Licencia'
    ];
    
    foreach ($required_fields as $field) {
        if (!isset($_POST[$field]) || empty($_POST[$field])) {
            throw new Exception("El campo '$field' es requerido.");
        }
    }
    
    // Obtener y limpiar los datos
    $nombre_paciente = $conn->real_escape_string($_POST['nombre_paciente']);
    $medicamento = $conn->real_escape_string($_POST['medicamento']);
    $cantidad = floatval($_POST['cantidad']);
    $precioventa = floatval($_POST['precioventa']);
    $fecha_encargo = $conn->real_escape_string($_POST['fecha_encargo']);
    $costo = floatval($_POST['costo']);
    $abono_parcial = floatval($_POST['abono_parcial']);
    $num_ticket = $conn->real_escape_string($_POST['NumTicket']);
    $fk_caja = intval($_POST['Fk_Caja']);
    $empleado = $conn->real_escape_string($_POST['Empleado']);
    $agregado_por = $conn->real_escape_string($_POST['AgregadoPor']);
    $fk_sucursal = intval($_POST['Fk_sucursal']);
    $sistema = $conn->real_escape_string($_POST['Sistema']);
    $licencia = $conn->real_escape_string($_POST['Licencia']);
    
    // Validaciones adicionales
    if ($cantidad <= 0) {
        throw new Exception("La cantidad debe ser mayor a 0.");
    }
    
    if ($precioventa <= 0) {
        throw new Exception("El precio de venta debe ser mayor a 0.");
    }
    
    if ($costo <= 0) {
        throw new Exception("El costo debe ser mayor a 0.");
    }
    
    if ($abono_parcial < 0) {
        throw new Exception("El abono no puede ser negativo.");
    }
    
    if ($abono_parcial > $costo) {
        throw new Exception("El abono no puede ser mayor al costo total.");
    }
    
    // Calcular saldo pendiente
    $saldo_pendiente = $costo - $abono_parcial;
    
    // Determinar el estatus del encargo
    $estatus = ($saldo_pendiente > 0) ? 'Pendiente' : 'Pagado';
    
    // Insertar el encargo en la base de datos
    $sql = "INSERT INTO Encargos (
        Nombre_Paciente, Medicamento, Cantidad, Precio_Venta, 
        Fecha_Encargo, Costo, Abono_Parcial, Saldo_Pendiente, 
        Num_Ticket, Fk_Caja, Empleado, Agregado_Por, 
        Fk_Sucursal, Sistema, Licencia, Estatus, Fecha_Registro
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
    
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("Error al preparar la consulta: " . $conn->error);
    }
    
    $stmt->bind_param("ssdddsdssssssss", 
        $nombre_paciente, $medicamento, $cantidad, $precioventa,
        $fecha_encargo, $costo, $abono_parcial, $saldo_pendiente,
        $num_ticket, $fk_caja, $empleado, $agregado_por,
        $fk_sucursal, $sistema, $licencia, $estatus
    );
    
    if (!$stmt->execute()) {
        throw new Exception("Error al ejecutar la consulta: " . $stmt->error);
    }
    
    $encargo_id = $conn->insert_id;
    $stmt->close();
    
    // Respuesta exitosa
    echo json_encode([
        'success' => true,
        'message' => 'Encargo registrado exitosamente. ID: ' . $encargo_id,
        'encargo_id' => $encargo_id,
        'ticket' => $num_ticket,
        'estatus' => $estatus
    ]);
    
} catch (Exception $e) {
    // Respuesta de error
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

$conn->close();
?> 