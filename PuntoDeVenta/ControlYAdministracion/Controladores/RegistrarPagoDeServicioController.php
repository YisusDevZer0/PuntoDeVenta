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

    // Preparar la consulta SQL para insertar en PagosServicios
    // Asumiendo que la tabla tiene campos similares a encargos pero adaptados para servicios
    $sql = "INSERT INTO PagosServicios (
                Cliente,
                Servicio_ID,
                Nombre_Servicio,
                Monto,
                Fecha_Pago,
                Observaciones,
                NumTicket,
                Fk_Caja,
                Empleado,
                Fk_Sucursal,
                FormaDePago,
                Fecha_Registro,
                AgregadoPor,
                AgregadoEl
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?, NOW())";

    error_log("SQL preparado: $sql");

    $stmt = $conn->prepare($sql);
    if ($stmt) {
        // 12 parámetros: Cliente(s), Servicio_ID(i), Nombre_Servicio(s), Monto(d), 
        // Fecha_Pago(s), Observaciones(s), NumTicket(s), Fk_Caja(i), Empleado(s), 
        // Fk_Sucursal(i), FormaDePago(s), AgregadoPor(s)
        $stmt->bind_param("sisdsssissis", 
            $cliente,
            $servicio_id,
            $nombre_servicio,
            $monto,
            $fecha_pago,
            $observaciones,
            $NumTicket,
            $Fk_Caja,
            $Empleado,
            $Fk_Sucursal,
            $FormaDePago,
            $Empleado
        );

        if ($stmt->execute()) {
            $pago_id = $stmt->insert_id;
            $stmt->close();
            
            // Actualizar el valor total de la caja (sumar el monto del pago)
            if ($Fk_Caja > 0 && $monto > 0) {
                $sql_caja = "UPDATE Cajas SET Valor_Total_Caja = Valor_Total_Caja + ? WHERE ID_Caja = ?";
                $stmt_caja = $conn->prepare($sql_caja);
                if ($stmt_caja) {
                    $stmt_caja->bind_param("di", $monto, $Fk_Caja);
                    $stmt_caja->execute();
                    $stmt_caja->close();
                }
            }
            
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
        
        // Fallback si la preparación falla - intentar con estructura mínima
        $sql_fallback = "INSERT INTO PagosServicios (
                            Cliente,
                            Servicio_ID,
                            Monto,
                            Fecha_Pago,
                            NumTicket,
                            Fk_Caja,
                            Empleado,
                            Fk_Sucursal,
                            FormaDePago
                        ) VALUES (
                            '" . mysqli_real_escape_string($conn, $cliente) . "',
                            $servicio_id,
                            $monto,
                            '" . mysqli_real_escape_string($conn, $fecha_pago) . "',
                            '" . mysqli_real_escape_string($conn, $NumTicket) . "',
                            $Fk_Caja,
                            '" . mysqli_real_escape_string($conn, $Empleado) . "',
                            $Fk_Sucursal,
                            '" . mysqli_real_escape_string($conn, $FormaDePago) . "'
                        )";

        error_log("SQL fallback: $sql_fallback");

        if (mysqli_query($conn, $sql_fallback)) {
            $pago_id = mysqli_insert_id($conn);
            
            // Actualizar el valor total de la caja
            if ($Fk_Caja > 0 && $monto > 0) {
                mysqli_query($conn, "UPDATE Cajas SET Valor_Total_Caja = Valor_Total_Caja + $monto WHERE ID_Caja = $Fk_Caja");
            }
            
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
