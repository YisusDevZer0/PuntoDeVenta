<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

include_once "../Controladores/db_connect.php";
include_once "../Controladores/ControladorUsuario.php";

// Si $conn no existe, crearlo
if (!isset($conn)) {
    $conn = new mysqli(getenv('DB_HOST') ?: 'localhost', 
                       getenv('DB_USER') ?: 'u858848268_devpezer0', 
                       getenv('DB_PASS') ?: 'F9+nIIOuCh8yI6wu4!08', 
                       getenv('DB_NAME') ?: 'u858848268_doctorpez');
    
    if ($conn->connect_error) {
        echo json_encode(['success' => false, 'message' => 'Error de conexión: ' . $conn->connect_error]);
        exit;
    }
    
    // Establecer zona horaria
    $conn->query("SET time_zone = '-6:00'");
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

$accion = isset($_POST['accion']) ? $_POST['accion'] : '';
// IMPORTANTE: No usar trim() para que coincida exactamente con lo guardado en la BD
$usuario = isset($row['Nombre_Apellidos']) ? $row['Nombre_Apellidos'] : 'Sistema';

// Obtener sucursal - intentar múltiples formas (mayúscula, minúscula, sesión)
$sucursal = 0;
if (isset($row['Fk_Sucursal']) && $row['Fk_Sucursal'] > 0) {
    $sucursal = (int)$row['Fk_Sucursal'];
} elseif (isset($row['Fk_sucursal']) && $row['Fk_sucursal'] > 0) {
    $sucursal = (int)$row['Fk_sucursal'];
} elseif (isset($_SESSION['Fk_Sucursal']) && $_SESSION['Fk_Sucursal'] > 0) {
    $sucursal = (int)$_SESSION['Fk_Sucursal'];
}

// DEBUG: Verificar valores obtenidos
error_log("DEBUG gestion_turnos - Usuario: " . $usuario . ", Sucursal: " . $sucursal . ", Accion: " . $accion);
error_log("DEBUG gestion_turnos - row[Fk_Sucursal]: " . (isset($row['Fk_Sucursal']) ? $row['Fk_Sucursal'] : 'NO EXISTE'));
error_log("DEBUG gestion_turnos - row[Fk_sucursal]: " . (isset($row['Fk_sucursal']) ? $row['Fk_sucursal'] : 'NO EXISTE'));
error_log("DEBUG gestion_turnos - SESSION[Fk_Sucursal]: " . (isset($_SESSION['Fk_Sucursal']) ? $_SESSION['Fk_Sucursal'] : 'NO EXISTE'));

try {
    $conn->begin_transaction();
    
    switch ($accion) {
        case 'verificar_turno':
            // Verificar si hay un turno activo que no se detectó (SIN restricción de fecha)
            $turno_encontrado = null;
            
            if (!empty($usuario) && $sucursal > 0) {
                // Método 1: LIKE con patrón
                $sql_verificar = "SELECT * FROM Inventario_Turnos 
                                 WHERE Fk_sucursal = ? 
                                 AND Estado IN ('activo', 'pausado')
                                 AND (Usuario_Actual LIKE ? OR Usuario_Inicio LIKE ?)
                                 ORDER BY Hora_Inicio DESC 
                                 LIMIT 1";
                $stmt_ver = $conn->prepare($sql_verificar);
                if ($stmt_ver) {
                    $usuario_pattern = "%" . $usuario . "%";
                    $stmt_ver->bind_param("iss", $sucursal, $usuario_pattern, $usuario_pattern);
                    $stmt_ver->execute();
                    $turno_encontrado = $stmt_ver->get_result()->fetch_assoc();
                    $stmt_ver->close();
                }
                
                // Método 2: Búsqueda exacta
                if (!$turno_encontrado) {
                    $sql_exacto = "SELECT * FROM Inventario_Turnos 
                                  WHERE Fk_sucursal = ? 
                                  AND Estado IN ('activo', 'pausado')
                                  AND (Usuario_Actual = ? OR Usuario_Inicio = ?)
                                  ORDER BY Hora_Inicio DESC 
                                  LIMIT 1";
                    $stmt_exacto = $conn->prepare($sql_exacto);
                    if ($stmt_exacto) {
                        $stmt_exacto->bind_param("iss", $sucursal, $usuario, $usuario);
                        $stmt_exacto->execute();
                        $turno_encontrado = $stmt_exacto->get_result()->fetch_assoc();
                        $stmt_exacto->close();
                    }
                }
                
                // Método 3: Sin distinguir mayúsculas/minúsculas
                if (!$turno_encontrado) {
                    $sql_case = "SELECT * FROM Inventario_Turnos 
                                WHERE Fk_sucursal = ? 
                                AND Estado IN ('activo', 'pausado')
                                AND (LOWER(Usuario_Actual) = LOWER(?) OR LOWER(Usuario_Inicio) = LOWER(?))
                                ORDER BY Hora_Inicio DESC 
                                LIMIT 1";
                    $stmt_case = $conn->prepare($sql_case);
                    if ($stmt_case) {
                        $stmt_case->bind_param("iss", $sucursal, $usuario, $usuario);
                        $stmt_case->execute();
                        $turno_encontrado = $stmt_case->get_result()->fetch_assoc();
                        $stmt_case->close();
                    }
                }
            }
            
            // Método 4: Cualquier turno activo en la sucursal (último recurso)
            if (!$turno_encontrado && $sucursal > 0) {
                $sql_sucursal = "SELECT * FROM Inventario_Turnos 
                                WHERE Fk_sucursal = ? 
                                AND Estado IN ('activo', 'pausado')
                                ORDER BY Hora_Inicio DESC 
                                LIMIT 1";
                $stmt_sucursal = $conn->prepare($sql_sucursal);
                if ($stmt_sucursal) {
                    $stmt_sucursal->bind_param("i", $sucursal);
                    $stmt_sucursal->execute();
                    $turno_encontrado = $stmt_sucursal->get_result()->fetch_assoc();
                    $stmt_sucursal->close();
                }
            }
            
            if ($turno_encontrado) {
                echo json_encode(['success' => true, 'turno' => $turno_encontrado]);
            } else {
                echo json_encode(['success' => false, 'turno' => null]);
            }
            break;
            
        case 'iniciar':
            // Validar que la sucursal sea válida
            if ($sucursal <= 0) {
                throw new Exception('Error: No se pudo obtener la sucursal. Sucursal: ' . $sucursal);
            }
            
            // Validar que el usuario sea válido
            if (empty($usuario)) {
                throw new Exception('Error: No se pudo obtener el usuario.');
            }
            
            // Verificar si ya hay un turno activo (SIN restricción de fecha)
            $sql_verificar = "SELECT ID_Turno FROM Inventario_Turnos 
                             WHERE Usuario_Actual = ? 
                             AND Fk_sucursal = ? 
                             AND Estado IN ('activo', 'pausado')";
            $stmt_ver = $conn->prepare($sql_verificar);
            $stmt_ver->bind_param("si", $usuario, $sucursal);
            $stmt_ver->execute();
            $turno_existente = $stmt_ver->get_result()->fetch_assoc();
            $stmt_ver->close();
            
            if ($turno_existente) {
                throw new Exception('Ya tienes un turno activo o pausado. Reanúdalo o finalízalo antes de crear uno nuevo.');
            }
            
            // Generar folio usando el procedimiento almacenado
            $sql_folio = "CALL sp_generar_folio_turno(?, @folio)";
            $stmt_folio = $conn->prepare($sql_folio);
            if (!$stmt_folio) {
                throw new Exception('Error al preparar la consulta del folio');
            }
            $stmt_folio->bind_param("i", $sucursal);
            $stmt_folio->execute();
            $stmt_folio->close();
            
            // Obtener el folio generado
            $result_folio = $conn->query("SELECT @folio as folio");
            if (!$result_folio) {
                throw new Exception('Error al obtener el folio generado');
            }
            $folio_data = $result_folio->fetch_assoc();
            $folio = isset($folio_data['folio']) ? $folio_data['folio'] : null;
            
            // Si no se generó el folio, crear uno manualmente
            if (empty($folio)) {
                // Obtener código de sucursal
                $sql_suc = "SELECT Nombre_Sucursal FROM Sucursales WHERE ID_Sucursal = ? LIMIT 1";
                $stmt_suc = $conn->prepare($sql_suc);
                $stmt_suc->bind_param("i", $sucursal);
                $stmt_suc->execute();
                $suc_data = $stmt_suc->get_result()->fetch_assoc();
                $stmt_suc->close();
                
                $sucursal_cod = 'SUC';
                if ($suc_data && !empty($suc_data['Nombre_Sucursal'])) {
                    $sucursal_cod = strtoupper(substr($suc_data['Nombre_Sucursal'], 0, 3));
                } else {
                    $sucursal_cod = str_pad($sucursal, 3, '0', STR_PAD_LEFT);
                }
                
                // Obtener siguiente secuencial
                $sql_sec = "SELECT COUNT(*) + 1 as secuencial FROM Inventario_Turnos 
                           WHERE Fk_sucursal = ? AND DATE(Fecha_Turno) = CURDATE()";
                $stmt_sec = $conn->prepare($sql_sec);
                $stmt_sec->bind_param("i", $sucursal);
                $stmt_sec->execute();
                $sec_data = $stmt_sec->get_result()->fetch_assoc();
                $stmt_sec->close();
                
                $secuencial = $sec_data ? str_pad($sec_data['secuencial'], 3, '0', STR_PAD_LEFT) : '001';
                $fecha = date('Ymd');
                $folio = "INV-{$sucursal_cod}-{$fecha}-{$secuencial}";
            }
            
            // Validar que el folio no esté vacío
            if (empty($folio)) {
                throw new Exception('No se pudo generar el folio del turno');
            }
            
            // Verificar si existe la columna Limite_Productos
            $sql_check_col = "SELECT COUNT(*) as existe FROM INFORMATION_SCHEMA.COLUMNS 
                             WHERE TABLE_SCHEMA = DATABASE() 
                             AND TABLE_NAME = 'Inventario_Turnos' 
                             AND COLUMN_NAME = 'Limite_Productos'";
            $result_check = $conn->query($sql_check_col);
            $col_exists = false;
            if ($result_check) {
                $row_check = $result_check->fetch_assoc();
                $col_exists = ($row_check['existe'] > 0);
            }
            
            // Validar nuevamente antes de insertar
            if ($sucursal <= 0) {
                throw new Exception('Error: La sucursal no es válida antes de insertar. Sucursal: ' . $sucursal);
            }
            
            if (empty($usuario)) {
                throw new Exception('Error: El usuario no es válido antes de insertar.');
            }
            
            // Crear turno con o sin límite según exista la columna
            if ($col_exists) {
                $sql_insert = "INSERT INTO Inventario_Turnos (
                    Folio_Turno, Fk_sucursal, Usuario_Inicio, Usuario_Actual,
                    Fecha_Turno, Estado, Limite_Productos
                ) VALUES (?, ?, ?, ?, CURDATE(), 'activo', 50)";
                $stmt_insert = $conn->prepare($sql_insert);
                if (!$stmt_insert) {
                    throw new Exception('Error al preparar la inserción: ' . $conn->error);
                }
                // IMPORTANTE: Verificar que $sucursal sea un entero válido
                $sucursal_int = (int)$sucursal;
                if ($sucursal_int <= 0) {
                    throw new Exception('Error: La sucursal debe ser un número mayor a 0. Valor recibido: ' . $sucursal);
                }
                $stmt_insert->bind_param("siss", $folio, $sucursal_int, $usuario, $usuario);
            } else {
                $sql_insert = "INSERT INTO Inventario_Turnos (
                    Folio_Turno, Fk_sucursal, Usuario_Inicio, Usuario_Actual,
                    Fecha_Turno, Estado
                ) VALUES (?, ?, ?, ?, CURDATE(), 'activo')";
                $stmt_insert = $conn->prepare($sql_insert);
                if (!$stmt_insert) {
                    throw new Exception('Error al preparar la inserción: ' . $conn->error);
                }
                // IMPORTANTE: Verificar que $sucursal sea un entero válido
                $sucursal_int = (int)$sucursal;
                if ($sucursal_int <= 0) {
                    throw new Exception('Error: La sucursal debe ser un número mayor a 0. Valor recibido: ' . $sucursal);
                }
                $stmt_insert->bind_param("siss", $folio, $sucursal_int, $usuario, $usuario);
            }
            
            $stmt_insert->execute();
            
            if ($stmt_insert->error) {
                throw new Exception('Error al insertar turno: ' . $stmt_insert->error . ' - Sucursal usada: ' . $sucursal_int);
            }
            
            $id_turno = $conn->insert_id;
            
            // Verificar que el turno se insertó correctamente con la sucursal
            $sql_verificar_insert = "SELECT ID_Turno, Fk_sucursal, Usuario_Actual FROM Inventario_Turnos WHERE ID_Turno = ?";
            $stmt_ver_ins = $conn->prepare($sql_verificar_insert);
            $stmt_ver_ins->bind_param("i", $id_turno);
            $stmt_ver_ins->execute();
            $turno_insertado = $stmt_ver_ins->get_result()->fetch_assoc();
            $stmt_ver_ins->close();
            
            if (!$turno_insertado) {
                throw new Exception('Error: El turno no se insertó correctamente');
            }
            
            if ($turno_insertado['Fk_sucursal'] != $sucursal_int) {
                throw new Exception('Error: La sucursal no se guardó correctamente. Esperada: ' . $sucursal_int . ', Guardada: ' . $turno_insertado['Fk_sucursal']);
            }
            
            error_log("DEBUG: Turno creado - ID: " . $id_turno . ", Sucursal: " . $turno_insertado['Fk_sucursal'] . ", Usuario: " . $turno_insertado['Usuario_Actual']);
            
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
            
            // Contar productos actuales antes de pausar
            $sql_contar = "SELECT COUNT(*) as total FROM Inventario_Turnos_Productos 
                          WHERE ID_Turno = ? AND Estado != 'liberado'";
            $stmt_contar = $conn->prepare($sql_contar);
            $stmt_contar->bind_param("i", $id_turno);
            $stmt_contar->execute();
            $contar_data = $stmt_contar->get_result()->fetch_assoc();
            $total_productos = $contar_data ? (int)$contar_data['total'] : 0;
            $stmt_contar->close();
            
            // Contar productos completados
            $sql_completados = "SELECT COUNT(*) as total FROM Inventario_Turnos_Productos 
                               WHERE ID_Turno = ? AND Estado = 'completado'";
            $stmt_comp = $conn->prepare($sql_completados);
            $stmt_comp->bind_param("i", $id_turno);
            $stmt_comp->execute();
            $comp_data = $stmt_comp->get_result()->fetch_assoc();
            $productos_completados = $comp_data ? (int)$comp_data['total'] : 0;
            $stmt_comp->close();
            
            // Actualizar turno con datos actualizados
            $sql_update = "UPDATE Inventario_Turnos SET
                Estado = 'pausado',
                Hora_Pausa = NOW(),
                Total_Productos = ?,
                Productos_Completados = ?
            WHERE ID_Turno = ?";
            $stmt_update = $conn->prepare($sql_update);
            $stmt_update->bind_param("iii", $total_productos, $productos_completados, $id_turno);
            $stmt_update->execute();
            $stmt_update->close();
            
            echo json_encode([
                'success' => true, 
                'message' => 'Turno pausado correctamente. Todos los datos han sido guardados.',
                'total_productos' => $total_productos,
                'productos_completados' => $productos_completados
            ]);
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
            
            // Recalcular contadores antes de reanudar
            $sql_contar = "SELECT COUNT(*) as total FROM Inventario_Turnos_Productos 
                          WHERE ID_Turno = ? AND Estado != 'liberado'";
            $stmt_contar = $conn->prepare($sql_contar);
            $stmt_contar->bind_param("i", $id_turno);
            $stmt_contar->execute();
            $contar_data = $stmt_contar->get_result()->fetch_assoc();
            $total_productos = $contar_data ? (int)$contar_data['total'] : 0;
            $stmt_contar->close();
            
            $sql_completados = "SELECT COUNT(*) as total FROM Inventario_Turnos_Productos 
                               WHERE ID_Turno = ? AND Estado = 'completado'";
            $stmt_comp = $conn->prepare($sql_completados);
            $stmt_comp->bind_param("i", $id_turno);
            $stmt_comp->execute();
            $comp_data = $stmt_comp->get_result()->fetch_assoc();
            $productos_completados = $comp_data ? (int)$comp_data['total'] : 0;
            $stmt_comp->close();
            
            // Actualizar turno con datos actualizados
            $sql_update = "UPDATE Inventario_Turnos SET
                Estado = 'activo',
                Hora_Reanudacion = NOW(),
                Total_Productos = ?,
                Productos_Completados = ?
            WHERE ID_Turno = ?";
            $stmt_update = $conn->prepare($sql_update);
            $stmt_update->bind_param("iii", $total_productos, $productos_completados, $id_turno);
            $stmt_update->execute();
            $stmt_update->close();
            
            echo json_encode([
                'success' => true, 
                'message' => 'Turno reanudado correctamente',
                'total_productos' => $total_productos,
                'productos_completados' => $productos_completados
            ]);
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
            
            // Obtener el límite de productos del turno
            $limite_productos = isset($turno['Limite_Productos']) ? (int)$turno['Limite_Productos'] : 50;
            
            // Contar productos completados
            $sql_completados = "SELECT COUNT(*) as total FROM Inventario_Turnos_Productos 
                               WHERE ID_Turno = ? AND Estado = 'completado'";
            $stmt_comp = $conn->prepare($sql_completados);
            $stmt_comp->bind_param("i", $id_turno);
            $stmt_comp->execute();
            $comp_data = $stmt_comp->get_result()->fetch_assoc();
            $stmt_comp->close();
            
            $productos_completados = $comp_data ? (int)$comp_data['total'] : 0;
            
            // Validar que se hayan completado todos los productos requeridos
            if ($productos_completados < $limite_productos) {
                throw new Exception("No puedes finalizar el turno. Has completado {$productos_completados} de {$limite_productos} productos requeridos. Debes completar todos los productos antes de finalizar.");
            }
            
            // Actualizar turno
            $sql_update = "UPDATE Inventario_Turnos SET
                Estado = 'finalizado',
                Hora_Finalizacion = NOW(),
                Productos_Completados = ?
            WHERE ID_Turno = ?";
            $stmt_update = $conn->prepare($sql_update);
            $stmt_update->bind_param("ii", $productos_completados, $id_turno);
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
            
            // Obtener el límite de productos del turno
            $sql_limite = "SELECT Limite_Productos, Total_Productos FROM Inventario_Turnos WHERE ID_Turno = ?";
            $stmt_limite = $conn->prepare($sql_limite);
            $stmt_limite->bind_param("i", $id_turno);
            $stmt_limite->execute();
            $limite_data = $stmt_limite->get_result()->fetch_assoc();
            $stmt_limite->close();
            
            $limite_productos = isset($limite_data['Limite_Productos']) ? (int)$limite_data['Limite_Productos'] : 50;
            $total_productos = isset($limite_data['Total_Productos']) ? (int)$limite_data['Total_Productos'] : 0;
            
            // Verificar límite de productos por turno
            if ($total_productos >= $limite_productos) {
                throw new Exception("Has alcanzado el límite de {$limite_productos} productos por turno. Finaliza o libera algunos productos para agregar más.");
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
