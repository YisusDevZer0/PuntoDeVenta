<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Habilitar reporte de errores para debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Verificar que sea una petición POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

try {
    // Incluir conexión a la base de datos
    include_once "db_connect.php";
    include_once "ControladorUsuario.php";
    
    // Verificar conexión
    if (!isset($conn) || !$conn) {
        echo json_encode(['success' => false, 'message' => 'Error de conexión a la base de datos']);
        exit;
    }

    // Debug: Mostrar datos recibidos
    error_log("Datos POST recibidos: " . print_r($_POST, true));

    // Obtener los datos del formulario
    $encargo_id = isset($_POST['encargo_id']) ? (int)$_POST['encargo_id'] : 0;
    $monto_abono = isset($_POST['monto_abono']) ? (float)$_POST['monto_abono'] : 0.00;
    $forma_pago_abono = isset($_POST['forma_pago_abono']) ? trim($_POST['forma_pago_abono']) : '';
    $efectivo_recibido_abono = isset($_POST['efectivo_recibido_abono']) ? (float)$_POST['efectivo_recibido_abono'] : 0.00;
    $observaciones_abono = isset($_POST['observaciones_abono']) ? trim($_POST['observaciones_abono']) : '';

    // Validar campos requeridos
    if ($encargo_id <= 0 || $monto_abono <= 0 || empty($forma_pago_abono)) {
        echo json_encode(['success' => false, 'message' => 'Todos los campos son requeridos y deben tener valores válidos']);
        exit;
    }

    // Obtener datos del encargo
    $sql_encargo = "SELECT e.*, c.ID_Caja, c.Empleado, c.Sucursal 
                    FROM encargos e 
                    INNER JOIN Cajas c ON e.Fk_Caja = c.ID_Caja 
                    WHERE e.id = ?";
    
    $stmt_encargo = $conn->prepare($sql_encargo);
    if (!$stmt_encargo) {
        echo json_encode(['success' => false, 'message' => 'Error al preparar consulta del encargo']);
        exit;
    }

    $stmt_encargo->bind_param("i", $encargo_id);
    $stmt_encargo->execute();
    $result_encargo = $stmt_encargo->get_result();
    $encargo = $result_encargo->fetch_object();
    $stmt_encargo->close();

    if (!$encargo) {
        echo json_encode(['success' => false, 'message' => 'No se encontró el encargo especificado']);
        exit;
    }

    // Calcular saldo pendiente
    $saldo_pendiente = $encargo->precioventa - $encargo->abono_parcial;

    // Verificar que el monto a abonar no exceda el saldo pendiente
    if ($monto_abono > $saldo_pendiente) {
        echo json_encode(['success' => false, 'message' => 'El monto a abonar no puede exceder el saldo pendiente']);
        exit;
    }

    // Iniciar transacción
    $conn->begin_transaction();

    try {
        // 1. Actualizar el encargo (sumar el abono)
        $nuevo_abono = $encargo->abono_parcial + $monto_abono;
        $estado_final = ($nuevo_abono >= $encargo->precioventa) ? 'Pagado' : 'Pendiente';
        
        $sql_update = "UPDATE encargos 
                       SET abono_parcial = ?, estado = ?, FormaDePago = ? 
                       WHERE id = ?";
        
        $stmt_update = $conn->prepare($sql_update);
        if (!$stmt_update) {
            throw new Exception('Error al preparar actualización del encargo');
        }

        $stmt_update->bind_param("dssi", $nuevo_abono, $estado_final, $forma_pago_abono, $encargo_id);
        
        if (!$stmt_update->execute()) {
            throw new Exception('Error al actualizar el encargo');
        }
        $stmt_update->close();

        // 2. Registrar el abono en una tabla de historial (opcional)
        $fecha_abono = date('Y-m-d H:i:s');
        $sql_historial = "INSERT INTO historial_abonos_encargos (
                            encargo_id, monto_abonado, forma_pago, efectivo_recibido, 
                            observaciones, fecha_abono, empleado, sucursal
                          ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt_historial = $conn->prepare($sql_historial);
        if ($stmt_historial) {
            $stmt_historial->bind_param("idssssss", 
                $encargo_id, $monto_abono, $forma_pago_abono, $efectivo_recibido_abono,
                $observaciones_abono, $fecha_abono, $encargo->Empleado, $encargo->Sucursal
            );
            $stmt_historial->execute();
            $stmt_historial->close();
        }

        // 3. Actualizar el valor total de la caja (sumar el monto abonado)
        $sql_caja = "UPDATE Cajas 
                     SET Valor_Total_Caja = Valor_Total_Caja + ? 
                     WHERE ID_Caja = ?";
        
        $stmt_caja = $conn->prepare($sql_caja);
        if (!$stmt_caja) {
            throw new Exception('Error al preparar actualización de caja');
        }

        $stmt_caja->bind_param("di", $monto_abono, $encargo->ID_Caja);
        
        if (!$stmt_caja->execute()) {
            throw new Exception('Error al actualizar la caja');
        }
        $stmt_caja->close();

        // 4. Si el encargo se pagó completamente, registrar la venta
        if ($estado_final === 'Pagado') {
            // Generar folio de ticket para la venta
            $folio_ticket = 'VEN-' . date('Ymd') . '-' . str_pad($encargo_id, 4, '0', STR_PAD_LEFT);
            
            $sql_venta = "INSERT INTO Ventas_POS (
                            Folio_Ticket, Nombre_Prod, Cantidad_Venta, Total_Venta, 
                            Importe, FormaDePago, Cliente, Fecha_venta, Fk_Caja, 
                            Fk_sucursal, Sistema, Estatus, AgregadoPor, AgregadoEl
                          ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt_venta = $conn->prepare($sql_venta);
            if (!$stmt_venta) {
                throw new Exception('Error al preparar inserción de venta: ' . $conn->error);
            }

            $sistema = 'POSVENTAS';
            $estatus_venta = 'Pagado';
            $agregado_por = $encargo->Empleado;
            
            $stmt_venta->bind_param("ssiddssssssss", 
                $folio_ticket, $encargo->medicamento, $encargo->cantidad, 
                $encargo->precioventa, $encargo->precioventa, $forma_pago_abono, 
                $encargo->nombre_paciente, $fecha_abono, $encargo->ID_Caja, 
                $encargo->Fk_Sucursal, $sistema, $estatus_venta, $agregado_por, $fecha_abono
            );
            
            if (!$stmt_venta->execute()) {
                throw new Exception('Error al registrar la venta: ' . $stmt_venta->error);
            }
            $stmt_venta->close();
        }

        // Confirmar transacción
        $conn->commit();

        // Calcular nuevo saldo pendiente
        $nuevo_saldo_pendiente = $saldo_pendiente - $monto_abono;

        // Mensaje de éxito
        $mensaje = $estado_final === 'Pagado' 
            ? "¡Encargo pagado completamente! Venta registrada con folio: $folio_ticket"
            : "Abono registrado exitosamente. Nuevo saldo pendiente: $" . number_format($nuevo_saldo_pendiente, 2);

        echo json_encode([
            'success' => true, 
            'message' => $mensaje,
            'encargo_id' => $encargo_id,
            'estado_final' => $estado_final,
            'monto_abonado' => $monto_abono,
            'nuevo_saldo_pendiente' => $nuevo_saldo_pendiente
        ]);

    } catch (Exception $e) {
        // Revertir transacción en caso de error
        $conn->rollback();
        throw $e;
    }

} catch (Exception $e) {
    error_log("Excepción capturada: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error inesperado: ' . $e->getMessage()]);
}
?> 