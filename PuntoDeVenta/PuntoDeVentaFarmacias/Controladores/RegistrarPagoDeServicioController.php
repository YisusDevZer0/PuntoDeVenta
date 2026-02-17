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
    
    // Verificar conexión
    if (!isset($conn) || !$conn) {
        echo json_encode(['success' => false, 'message' => 'Error de conexión a la base de datos']);
        exit;
    }

    // Debug: Mostrar datos recibidos
    error_log("Datos POST recibidos: " . print_r($_POST, true));

    // Obtener los datos del formulario
    $cliente = isset($_POST['cliente']) ? trim($_POST['cliente']) : '';
    $servicio_id = isset($_POST['servicio_id']) ? (int)$_POST['servicio_id'] : 0;
    $monto = isset($_POST['monto']) ? (float)$_POST['monto'] : 0.00;
    $fecha_pago = isset($_POST['fecha_pago']) ? $_POST['fecha_pago'] : date('Y-m-d');
    $observaciones = isset($_POST['observaciones']) ? trim($_POST['observaciones']) : '';
    $NumTicket = isset($_POST['NumTicket']) ? trim($_POST['NumTicket']) : '';
    $Fk_Caja = isset($_POST['Fk_Caja']) ? (int)$_POST['Fk_Caja'] : 0;
    $Empleado = isset($_POST['Empleado']) ? trim($_POST['Empleado']) : '';
    $Fk_Sucursal = isset($_POST['Fk_Sucursal']) ? (int)$_POST['Fk_Sucursal'] : 0;
    $FormaDePago = isset($_POST['FormaDePago']) ? trim($_POST['FormaDePago']) : 'Efectivo';

    // Debug: Mostrar valores procesados
    error_log("Valores procesados: cliente=$cliente, servicio_id=$servicio_id, monto=$monto, Fk_Caja=$Fk_Caja, Fk_Sucursal=$Fk_Sucursal, FormaDePago=$FormaDePago");

    // Validar campos requeridos
    if (empty($cliente) || $servicio_id <= 0 || $monto <= 0 || empty($NumTicket)) {
        echo json_encode(['success' => false, 'message' => 'Todos los campos requeridos deben tener valores válidos']);
        exit;
    }

    // Obtener el nombre del servicio desde ListadoServicios
    $sql_servicio = "SELECT `Servicio` FROM `ListadoServicios` WHERE `Servicio_ID` = ?";
    $stmt_servicio = $conn->prepare($sql_servicio);
    $nombre_servicio = '';
    
    if ($stmt_servicio) {
        $stmt_servicio->bind_param("i", $servicio_id);
        $stmt_servicio->execute();
        $result_servicio = $stmt_servicio->get_result();
        if ($row_servicio = $result_servicio->fetch_assoc()) {
            $nombre_servicio = $row_servicio['Servicio'];
        }
        $stmt_servicio->close();
    }

    // Validar que se obtuvo el nombre del servicio
    if (empty($nombre_servicio)) {
        echo json_encode(['success' => false, 'message' => 'No se encontró el servicio seleccionado']);
        exit;
    }

    // Estado por defecto para el pago de servicio
    $estado = 'Pagado';

    // Preparar la consulta SQL para insertar en PagosServicios
    // Estructura real: id, nombre_paciente, Servicio, estado, costo, NumTicket, Fk_Sucursal, Fk_Caja, Empleado, FormaDePago
    $sql = "INSERT INTO PagosServicios (
                nombre_paciente,
                Servicio,
                estado,
                costo,
                NumTicket,
                Fk_Sucursal,
                Fk_Caja,
                Empleado,
                FormaDePago
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

    error_log("SQL preparado: $sql");

    $stmt = $conn->prepare($sql);
    if ($stmt) {
        // 9 parámetros: nombre_paciente(s), Servicio(s), estado(s), costo(d), 
        // NumTicket(s), Fk_Sucursal(i), Fk_Caja(i), Empleado(s), FormaDePago(s)
        $stmt->bind_param("ssdssiiss", 
            $cliente,
            $nombre_servicio,
            $estado,
            $monto,
            $NumTicket,
            $Fk_Sucursal,
            $Fk_Caja,
            $Empleado,
            $FormaDePago
        );

        if ($stmt->execute()) {
            $pago_id = $stmt->insert_id;
            $stmt->close();
            
            // El valor de la caja se actualiza automáticamente por el trigger tr_pago_servicio_insert
            // que suma costo + comisión. No actualizar aquí para evitar doble suma.

            echo json_encode([
                'success' => true, 
                'message' => 'Pago de servicio registrado exitosamente',
                'pago_id' => $pago_id,
                'NumTicket' => $NumTicket
            ]);
        } else {
            $error_msg = $stmt->error;
            $stmt->close();
            error_log("Error en execute: $error_msg");
            echo json_encode(['success' => false, 'message' => 'Error al ejecutar la consulta: ' . $error_msg]);
        }
    } else {
        error_log("Error en prepare: " . $conn->error);
        
        // Intentar con una estructura alternativa más simple si falla la preparación
        // Primero intentar obtener información del servicio
        if (empty($nombre_servicio)) {
            $result_servicio_alt = $conn->query("SELECT `Servicio` FROM `ListadoServicios` WHERE `Servicio_ID` = $servicio_id");
            if ($row_servicio_alt = $result_servicio_alt->fetch_assoc()) {
                $nombre_servicio = $row_servicio_alt['Servicio'];
            }
        }
        
        // Validar que se obtuvo el nombre del servicio
        if (empty($nombre_servicio)) {
            echo json_encode(['success' => false, 'message' => 'No se encontró el servicio seleccionado']);
            exit;
        }
        
        // Estado por defecto para el pago de servicio
        $estado = 'Pagado';
        
        // Fallback si la preparación falla - usar estructura correcta de la tabla
        $sql_fallback = "INSERT INTO PagosServicios (
                            nombre_paciente,
                            Servicio,
                            estado,
                            costo,
                            NumTicket,
                            Fk_Sucursal,
                            Fk_Caja,
                            Empleado,
                            FormaDePago
                        ) VALUES (
                            '" . mysqli_real_escape_string($conn, $cliente) . "',
                            '" . mysqli_real_escape_string($conn, $nombre_servicio) . "',
                            '" . mysqli_real_escape_string($conn, $estado) . "',
                            $monto,
                            '" . mysqli_real_escape_string($conn, $NumTicket) . "',
                            $Fk_Sucursal,
                            $Fk_Caja,
                            '" . mysqli_real_escape_string($conn, $Empleado) . "',
                            '" . mysqli_real_escape_string($conn, $FormaDePago) . "'
                        )";

        error_log("SQL fallback: $sql_fallback");

        if (mysqli_query($conn, $sql_fallback)) {
            $pago_id = mysqli_insert_id($conn);
            
            // El valor de la caja se actualiza automáticamente por el trigger tr_pago_servicio_insert
            // que suma costo + comisión. No actualizar aquí para evitar doble suma.

            echo json_encode([
                'success' => true, 
                'message' => 'Pago de servicio registrado exitosamente',
                'pago_id' => $pago_id,
                'NumTicket' => $NumTicket
            ]);
        } else {
            $error_msg = mysqli_error($conn);
            error_log("Error en fallback: $error_msg");
            echo json_encode(['success' => false, 'message' => 'Error al registrar el pago de servicio: ' . $error_msg]);
        }
    }

} catch (Exception $e) {
    error_log("Excepción capturada: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error inesperado: ' . $e->getMessage()]);
}
?>
