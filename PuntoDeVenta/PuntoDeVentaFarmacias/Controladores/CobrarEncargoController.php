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
    $monto_cobro = isset($_POST['monto_cobro']) ? (float)$_POST['monto_cobro'] : 0.00;
    $forma_pago_cobro = isset($_POST['forma_pago_cobro']) ? trim($_POST['forma_pago_cobro']) : '';
    $efectivo_recibido = isset($_POST['efectivo_recibido']) ? (float)$_POST['efectivo_recibido'] : 0.00;

    // Validar campos requeridos
    if ($encargo_id <= 0 || $monto_cobro <= 0 || empty($forma_pago_cobro)) {
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

    // Verificar que el monto a cobrar no exceda el saldo pendiente
    if ($monto_cobro > $saldo_pendiente) {
        echo json_encode(['success' => false, 'message' => 'El monto a cobrar no puede exceder el saldo pendiente']);
        exit;
    }

    // Verificar y crear tabla historial_abonos_encargos si no existe
    $sql_check_table = "SHOW TABLES LIKE 'historial_abonos_encargos'";
    $result_check = $conn->query($sql_check_table);
    
    if (!$result_check || $result_check->num_rows == 0) {
        // Crear la tabla si no existe
        $sql_create_table = "CREATE TABLE IF NOT EXISTS `historial_abonos_encargos` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `encargo_id` int(11) NOT NULL,
            `monto_abonado` decimal(10,2) NOT NULL,
            `forma_pago` varchar(300) NOT NULL,
            `efectivo_recibido` decimal(10,2) DEFAULT 0.00,
            `observaciones` text,
            `fecha_abono` datetime NOT NULL,
            `empleado` varchar(300) NOT NULL,
            `sucursal` varchar(300) NOT NULL,
            PRIMARY KEY (`id`),
            KEY `idx_encargo_id` (`encargo_id`),
            KEY `idx_fecha_abono` (`fecha_abono`),
            KEY `idx_sucursal` (`sucursal`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        if (!$conn->query($sql_create_table)) {
            error_log("Error al crear tabla historial_abonos_encargos: " . $conn->error);
            // No lanzar excepción aquí, solo registrar el error
        }
    }

    // Iniciar transacción
    $conn->begin_transaction();

    try {
        // 1. Actualizar el encargo (marcar como pagado)
        $nuevo_abono = $encargo->abono_parcial + $monto_cobro;
        $estado_final = ($nuevo_abono >= $encargo->precioventa) ? 'Pagado' : 'Pendiente';
        
        $sql_update = "UPDATE encargos 
                       SET abono_parcial = ?, estado = ?, FormaDePago = ? 
                       WHERE id = ?";
        
        $stmt_update = $conn->prepare($sql_update);
        if (!$stmt_update) {
            throw new Exception('Error al preparar actualización del encargo');
        }

        $stmt_update->bind_param("dssi", $nuevo_abono, $estado_final, $forma_pago_cobro, $encargo_id);
        
        if (!$stmt_update->execute()) {
            throw new Exception('Error al actualizar el encargo');
        }
        $stmt_update->close();

        // 2. Registrar el cobro en el historial de abonos
        // Verificar nuevamente si la tabla existe antes de insertar
        $sql_check_table2 = "SHOW TABLES LIKE 'historial_abonos_encargos'";
        $result_check2 = $conn->query($sql_check_table2);
        
        if ($result_check2 && $result_check2->num_rows > 0) {
            $fecha_cobro = date('Y-m-d H:i:s');
            $sql_historial = "INSERT INTO historial_abonos_encargos (
                                encargo_id, monto_abonado, forma_pago, efectivo_recibido, 
                                observaciones, fecha_abono, empleado, sucursal
                              ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt_historial = $conn->prepare($sql_historial);
            if ($stmt_historial) {
                $observaciones_cobro = 'Cobro realizado';
                $stmt_historial->bind_param("idssssss", 
                    $encargo_id, $monto_cobro, $forma_pago_cobro, $efectivo_recibido,
                    $observaciones_cobro, $fecha_cobro, $encargo->Empleado, $encargo->Sucursal
                );
                $stmt_historial->execute();
                $stmt_historial->close();
            }
        }

        // 3. Registrar la venta en la tabla de ventas (si el encargo se pagó completamente)
        if ($estado_final === 'Pagado') {
            // Generar folio de ticket para la venta
            $fecha_actual = date('Y-m-d H:i:s');
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
            
            $stmt_venta->bind_param("ssiddsssssssss", 
                $folio_ticket, $encargo->medicamento, $encargo->cantidad, 
                $encargo->precioventa, $encargo->precioventa, $forma_pago_cobro, 
                $encargo->nombre_paciente, $fecha_actual, $encargo->ID_Caja, 
                $encargo->Fk_Sucursal, $sistema, $estatus_venta, $agregado_por, $fecha_actual
            );
            
            if (!$stmt_venta->execute()) {
                throw new Exception('Error al registrar la venta: ' . $stmt_venta->error);
            }
            $stmt_venta->close();
        }

        // 4. Actualizar el valor total de la caja (sumar el monto cobrado)
        $sql_caja = "UPDATE Cajas 
                     SET Valor_Total_Caja = Valor_Total_Caja + ? 
                     WHERE ID_Caja = ?";
        
        $stmt_caja = $conn->prepare($sql_caja);
        if (!$stmt_caja) {
            throw new Exception('Error al preparar actualización de caja');
        }

        $stmt_caja->bind_param("di", $monto_cobro, $encargo->ID_Caja);
        
        if (!$stmt_caja->execute()) {
            throw new Exception('Error al actualizar la caja');
        }
        $stmt_caja->close();

        // Confirmar transacción
        $conn->commit();

        // Mensaje de éxito
        $mensaje = $estado_final === 'Pagado' 
            ? "Encargo cobrado completamente. Venta registrada con folio: $folio_ticket"
            : "Abono registrado. Saldo pendiente: $" . number_format($saldo_pendiente - $monto_cobro, 2);

        echo json_encode([
            'success' => true, 
            'message' => $mensaje,
            'encargo_id' => $encargo_id,
            'estado_final' => $estado_final,
            'monto_cobrado' => $monto_cobro
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