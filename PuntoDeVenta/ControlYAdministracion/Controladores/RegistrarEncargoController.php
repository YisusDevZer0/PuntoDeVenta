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
    $nombre_paciente = isset($_POST['nombre_paciente']) ? trim($_POST['nombre_paciente']) : '';
    $medicamento = isset($_POST['medicamento']) ? trim($_POST['medicamento']) : '';
    $cantidad = isset($_POST['cantidad']) ? (int)$_POST['cantidad'] : 0;
    $precioventa = isset($_POST['precioventa']) ? (float)$_POST['precioventa'] : 0.00;
    $fecha_encargo = isset($_POST['fecha_encargo']) ? $_POST['fecha_encargo'] : date('Y-m-d');
    $costo = isset($_POST['costo']) ? (float)$_POST['costo'] : 0.00;
    $abono_parcial = isset($_POST['abono_parcial']) ? (float)$_POST['abono_parcial'] : 0.00;
    $NumTicket = isset($_POST['NumTicket']) ? trim($_POST['NumTicket']) : '';
    $Fk_Caja = isset($_POST['Fk_Caja']) ? (int)$_POST['Fk_Caja'] : 0;
    $Empleado = isset($_POST['Empleado']) ? trim($_POST['Empleado']) : '';
    $AgregadoPor = isset($_POST['AgregadoPor']) ? trim($_POST['AgregadoPor']) : '';
    $Fk_sucursal = isset($_POST['Fk_sucursal']) ? (int)$_POST['Fk_sucursal'] : 0;
    $Sistema = isset($_POST['Sistema']) ? trim($_POST['Sistema']) : '';
    $Licencia = isset($_POST['Licencia']) ? trim($_POST['Licencia']) : '';
    $estado = isset($_POST['estado']) ? trim($_POST['estado']) : 'Pendiente';

    // Debug: Mostrar valores procesados
    error_log("Valores procesados: nombre_paciente=$nombre_paciente, medicamento=$medicamento, cantidad=$cantidad, precioventa=$precioventa");

    // Validar campos requeridos
    if (empty($nombre_paciente) || empty($medicamento) || $cantidad <= 0 || $precioventa <= 0) {
        echo json_encode(['success' => false, 'message' => 'Todos los campos son requeridos y deben tener valores válidos']);
        exit;
    }

    // Preparar la consulta SQL
    $sql = "INSERT INTO encargos (
                nombre_paciente, 
                medicamento, 
                cantidad, 
                precioventa, 
                fecha_encargo, 
                estado, 
                costo, 
                abono_parcial, 
                NumTicket, 
                Fk_Caja, 
                Empleado, 
                AgregadoPor, 
                Fk_Sucursal, 
                Sistema, 
                Licencia
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    error_log("SQL preparado: $sql");

    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("ssidssddsssssss", 
            $nombre_paciente,
            $medicamento,
            $cantidad,
            $precioventa,
            $fecha_encargo,
            $estado,
            $costo,
            $abono_parcial,
            $NumTicket,
            $Fk_Caja,
            $Empleado,
            $AgregadoPor,
            $Fk_sucursal,
            $Sistema,
            $Licencia
        );

        if ($stmt->execute()) {
            $encargo_id = $stmt->insert_id;
            $stmt->close();
            
            echo json_encode([
                'success' => true, 
                'message' => 'Encargo registrado exitosamente',
                'encargo_id' => $encargo_id,
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
        // Fallback si la preparación falla
        $sql_fallback = "INSERT INTO encargos (
                            nombre_paciente, 
                            medicamento, 
                            cantidad, 
                            precioventa, 
                            fecha_encargo, 
                            estado, 
                            costo, 
                            abono_parcial, 
                            NumTicket, 
                            Fk_Caja, 
                            Empleado, 
                            AgregadoPor, 
                            Fk_Sucursal, 
                            Sistema, 
                            Licencia
                        ) VALUES (
                            '$nombre_paciente',
                            '$medicamento',
                            $cantidad,
                            $precioventa,
                            '$fecha_encargo',
                            '$estado',
                            $costo,
                            $abono_parcial,
                            '$NumTicket',
                            $Fk_Caja,
                            '$Empleado',
                            '$AgregadoPor',
                            $Fk_sucursal,
                            '$Sistema',
                            '$Licencia'
                        )";

        error_log("SQL fallback: $sql_fallback");

        if (mysqli_query($conn, $sql_fallback)) {
            $encargo_id = mysqli_insert_id($conn);
            echo json_encode([
                'success' => true, 
                'message' => 'Encargo registrado exitosamente',
                'encargo_id' => $encargo_id,
                'NumTicket' => $NumTicket
            ]);
        } else {
            $error_msg = mysqli_error($conn);
            error_log("Error en fallback: $error_msg");
            echo json_encode(['success' => false, 'message' => 'Error al registrar el encargo: ' . $error_msg]);
        }
    }

} catch (Exception $e) {
    error_log("Excepción capturada: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error inesperado: ' . $e->getMessage()]);
}
?> 