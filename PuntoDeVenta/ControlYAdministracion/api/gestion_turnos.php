<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

include_once "../db_connect.php";
include_once "../Controladores/ControladorUsuario.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

$accion = isset($_POST['accion']) ? $_POST['accion'] : '';
$usuario = isset($row['Nombre_Apellidos']) ? $row['Nombre_Apellidos'] : 'Sistema';
$sucursal = isset($row['Fk_sucursal']) ? (int)$row['Fk_sucursal'] : 0;

try {
    $conn->begin_transaction();
    
    switch ($accion) {
        case 'iniciar':
            // Verificar si ya hay un turno activo
            $sql_verificar = "SELECT ID_Turno FROM Inventario_Turnos 
                             WHERE Usuario_Actual = ? 
                             AND Fk_sucursal = ? 
                             AND Estado IN ('activo', 'pausado')
                             AND Fecha_Turno = CURDATE()";
            $stmt_ver = $conn->prepare($sql_verificar);
            $stmt_ver->bind_param("si", $usuario, $sucursal);
            $stmt_ver->execute();
            $turno_existente = $stmt_ver->get_result()->fetch_assoc();
            $stmt_ver->close();
            
            if ($turno_existente) {
                throw new Exception('Ya tienes un turno activo');
            }
            
            // Generar folio
            $sql_folio = "CALL sp_generar_folio_turno(?, @folio)";
            $stmt_folio = $conn->prepare($sql_folio);
            $stmt_folio->bind_param("i", $sucursal);
            $stmt_folio->execute();
            $stmt_folio->close();
            
            $result_folio = $conn->query("SELECT @folio as folio");
            $folio_data = $result_folio->fetch_assoc();
            $folio = $folio_data['folio'];
            
            // Crear turno
            $sql_insert = "INSERT INTO Inventario_Turnos (
                Folio_Turno, Fk_sucursal, Usuario_Inicio, Usuario_Actual,
                Fecha_Turno, Estado
            ) VALUES (?, ?, ?, ?, CURDATE(), 'activo')";
            
            $stmt_insert = $conn->prepare($sql_insert);
            $stmt_insert->bind_param("siss", $folio, $sucursal, $usuario, $usuario);
            $stmt_insert->execute();
            $id_turno = $conn->insert_id;
            $stmt_insert->close();
            
            // Registrar en historial
            $sql_hist = "INSERT INTO Inventario_Turnos_Historial (
                ID_Turno, Folio_Turno, Accion, Usuario
            ) VALUES (?, ?, 'inicio', ?)";
            $stmt_hist = $conn->prepare($sql_hist);
            $stmt_hist->bind_param("iss", $id_turno, $folio, $usuario);
            $stmt_hist->execute();
            $stmt_hist->close();
            
            echo json_encode(['success' => true, 'message' => 'Turno iniciado correctamente', 'id_turno' => $id_turno, 'folio' => $folio]);
            break;
            
        case 'pausar':
            $id_turno = isset($_POST['id_turno']) ? (int)$_POST['id_turno'] : 0;
            if ($id_turno <= 0) {
                throw new Exception('ID de turno inválido');
            }
            
            // Verificar que el turno pertenece al usuario
            $sql_ver = "SELECT * FROM Inventario_Turnos WHERE ID_Turno = ? AND Usuario_Actual = ?";
            $stmt_ver = $conn->prepare($sql_ver);
            $stmt_ver->bind_param("is", $id_turno, $usuario);
            $stmt_ver->execute();
            $turno = $stmt_ver->get_result()->fetch_assoc();
            $stmt_ver->close();
            
            if (!$turno || $turno['Estado'] != 'activo') {
                throw new Exception('Turno no válido para pausar');
            }
            
            // Actualizar turno
            $sql_update = "UPDATE Inventario_Turnos SET
                Estado = 'pausado',
                Hora_Pausa = NOW()
            WHERE ID_Turno = ?";
            $stmt_update = $conn->prepare($sql_update);
            $stmt_update->bind_param("i", $id_turno);
            $stmt_update->execute();
            $stmt_update->close();
            
            echo json_encode(['success' => true, 'message' => 'Turno pausado correctamente']);
            break;
            
        case 'reanudar':
            $id_turno = isset($_POST['id_turno']) ? (int)$_POST['id_turno'] : 0;
            if ($id_turno <= 0) {
                throw new Exception('ID de turno inválido');
            }
            
            // Verificar que el turno pertenece al usuario
            $sql_ver = "SELECT * FROM Inventario_Turnos WHERE ID_Turno = ? AND Usuario_Actual = ?";
            $stmt_ver = $conn->prepare($sql_ver);
            $stmt_ver->bind_param("is", $id_turno, $usuario);
            $stmt_ver->execute();
            $turno = $stmt_ver->get_result()->fetch_assoc();
            $stmt_ver->close();
            
            if (!$turno || $turno['Estado'] != 'pausado') {
                throw new Exception('Turno no válido para reanudar');
            }
            
            // Actualizar turno
            $sql_update = "UPDATE Inventario_Turnos SET
                Estado = 'activo',
                Hora_Reanudacion = NOW()
            WHERE ID_Turno = ?";
            $stmt_update = $conn->prepare($sql_update);
            $stmt_update->bind_param("i", $id_turno);
            $stmt_update->execute();
            $stmt_update->close();
            
            echo json_encode(['success' => true, 'message' => 'Turno reanudado correctamente']);
            break;
            
        case 'finalizar':
            $id_turno = isset($_POST['id_turno']) ? (int)$_POST['id_turno'] : 0;
            if ($id_turno <= 0) {
                throw new Exception('ID de turno inválido');
            }
            
            // Verificar que el turno pertenece al usuario
            $sql_ver = "SELECT * FROM Inventario_Turnos WHERE ID_Turno = ? AND Usuario_Actual = ?";
            $stmt_ver = $conn->prepare($sql_ver);
            $stmt_ver->bind_param("is", $id_turno, $usuario);
            $stmt_ver->execute();
            $turno = $stmt_ver->get_result()->fetch_assoc();
            $stmt_ver->close();
            
            if (!$turno) {
                throw new Exception('Turno no encontrado');
            }
            
            // Actualizar turno
            $sql_update = "UPDATE Inventario_Turnos SET
                Estado = 'finalizado',
                Hora_Finalizacion = NOW()
            WHERE ID_Turno = ?";
            $stmt_update = $conn->prepare($sql_update);
            $stmt_update->bind_param("i", $id_turno);
            $stmt_update->execute();
            $stmt_update->close();
            
            // Liberar todos los bloqueos del turno
            $sql_liberar = "UPDATE Inventario_Productos_Bloqueados SET
                Estado = 'liberado',
                Fecha_Liberacion = NOW()
            WHERE ID_Turno = ? AND Estado = 'bloqueado'";
            $stmt_liberar = $conn->prepare($sql_liberar);
            $stmt_liberar->bind_param("i", $id_turno);
            $stmt_liberar->execute();
            $stmt_liberar->close();
            
            echo json_encode(['success' => true, 'message' => 'Turno finalizado correctamente']);
            break;
            
        case 'seleccionar_producto':
            $id_turno = isset($_POST['id_turno']) ? (int)$_POST['id_turno'] : 0;
            $id_producto = isset($_POST['id_producto']) ? (int)$_POST['id_producto'] : 0;
            $cod_barra = isset($_POST['cod_barra']) ? trim($_POST['cod_barra']) : '';
            
            if ($id_turno <= 0 || $id_producto <= 0) {
                throw new Exception('Datos inválidos');
            }
            
            // Verificar que el producto no esté bloqueado por otro usuario
            $sql_bloqueo = "SELECT * FROM Inventario_Productos_Bloqueados 
                           WHERE ID_Turno = ? AND ID_Prod_POS = ? 
                           AND Usuario_Bloqueo != ? AND Estado = 'bloqueado'";
            $stmt_bloq = $conn->prepare($sql_bloqueo);
            $stmt_bloq->bind_param("iis", $id_turno, $id_producto, $usuario);
            $stmt_bloq->execute();
            $bloqueado = $stmt_bloq->get_result()->fetch_assoc();
            $stmt_bloq->close();
            
            if ($bloqueado) {
                throw new Exception('Este producto está siendo contado por otro usuario');
            }
            
            // Obtener información del producto
            $sql_prod = "SELECT Nombre_Prod, Existencias_R FROM Stock_POS 
                         WHERE ID_Prod_POS = ? AND Fk_sucursal = ?";
            $stmt_prod = $conn->prepare($sql_prod);
            $stmt_prod->bind_param("ii", $id_producto, $sucursal);
            $stmt_prod->execute();
            $producto = $stmt_prod->get_result()->fetch_assoc();
            $stmt_prod->close();
            
            if (!$producto) {
                throw new Exception('Producto no encontrado');
            }
            
            // Verificar si ya está seleccionado
            $sql_existe = "SELECT ID_Registro FROM Inventario_Turnos_Productos 
                          WHERE ID_Turno = ? AND ID_Prod_POS = ?";
            $stmt_existe = $conn->prepare($sql_existe);
            $stmt_existe->bind_param("ii", $id_turno, $id_producto);
            $stmt_existe->execute();
            $existe = $stmt_existe->get_result()->fetch_assoc();
            $stmt_existe->close();
            
            if ($existe) {
                throw new Exception('Este producto ya está seleccionado');
            }
            
            // Obtener folio del turno
            $sql_folio = "SELECT Folio_Turno FROM Inventario_Turnos WHERE ID_Turno = ?";
            $stmt_folio = $conn->prepare($sql_folio);
            $stmt_folio->bind_param("i", $id_turno);
            $stmt_folio->execute();
            $turno_data = $stmt_folio->get_result()->fetch_assoc();
            $folio_turno = $turno_data['Folio_Turno'];
            $stmt_folio->close();
            
            // Insertar producto en el turno
            $sql_insert = "INSERT INTO Inventario_Turnos_Productos (
                ID_Turno, Folio_Turno, ID_Prod_POS, Cod_Barra, Nombre_Producto,
                Fk_sucursal, Existencias_Sistema, Usuario_Selecciono, Estado
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'seleccionado')";
            
            $stmt_insert = $conn->prepare($sql_insert);
            $stmt_insert->bind_param("isissiis", 
                $id_turno, $folio_turno, $id_producto, $cod_barra, 
                $producto['Nombre_Prod'], $sucursal, $producto['Existencias_R'], $usuario);
            $stmt_insert->execute();
            $stmt_insert->close();
            
            // Actualizar total de productos del turno
            $sql_total = "UPDATE Inventario_Turnos SET Total_Productos = Total_Productos + 1 WHERE ID_Turno = ?";
            $stmt_total = $conn->prepare($sql_total);
            $stmt_total->bind_param("i", $id_turno);
            $stmt_total->execute();
            $stmt_total->close();
            
            echo json_encode(['success' => true, 'message' => 'Producto seleccionado correctamente']);
            break;
            
        case 'contar_producto':
            $id_registro = isset($_POST['id_registro']) ? (int)$_POST['id_registro'] : 0;
            $existencias_fisicas = isset($_POST['existencias_fisicas']) ? (int)$_POST['existencias_fisicas'] : 0;
            
            if ($id_registro <= 0) {
                throw new Exception('Registro inválido');
            }
            
            // Obtener información del registro
            $sql_reg = "SELECT * FROM Inventario_Turnos_Productos WHERE ID_Registro = ?";
            $stmt_reg = $conn->prepare($sql_reg);
            $stmt_reg->bind_param("i", $id_registro);
            $stmt_reg->execute();
            $registro = $stmt_reg->get_result()->fetch_assoc();
            $stmt_reg->close();
            
            if (!$registro) {
                throw new Exception('Registro no encontrado');
            }
            
            // Calcular diferencia
            $diferencia = $existencias_fisicas - $registro['Existencias_Sistema'];
            
            // Actualizar registro
            $sql_update = "UPDATE Inventario_Turnos_Productos SET
                Existencias_Fisicas = ?,
                Diferencia = ?,
                Estado = 'completado',
                Fecha_Conteo = NOW()
            WHERE ID_Registro = ?";
            
            $stmt_update = $conn->prepare($sql_update);
            $stmt_update->bind_param("iii", $existencias_fisicas, $diferencia, $id_registro);
            $stmt_update->execute();
            $stmt_update->close();
            
            // Actualizar contador de completados
            $sql_completados = "UPDATE Inventario_Turnos SET 
                Productos_Completados = Productos_Completados + 1 
            WHERE ID_Turno = ?";
            $stmt_comp = $conn->prepare($sql_completados);
            $stmt_comp->bind_param("i", $registro['ID_Turno']);
            $stmt_comp->execute();
            $stmt_comp->close();
            
            echo json_encode(['success' => true, 'message' => 'Conteo registrado correctamente', 'diferencia' => $diferencia]);
            break;
            
        default:
            throw new Exception('Acción no válida');
    }
    
    $conn->commit();
    
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

$conn->close();
?>
