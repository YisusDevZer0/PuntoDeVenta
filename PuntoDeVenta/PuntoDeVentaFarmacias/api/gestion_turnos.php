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
$id_usuario = isset($row['Id_PvUser']) ? (int)$row['Id_PvUser'] : 0;

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
            
            // IMPORTANTE: NO buscar turnos de otros usuarios, incluso si están pausados
            // El conteo es por usuario, cada usuario solo debe ver sus propios turnos
            // El método 4 fue eliminado para evitar que usuarios vean turnos de otros
            
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
            
            // --- Configuración por periodo y límites (misma lógica que ControlYAdministracion) ---
            $limite_productos = 50;
            $hoy = date('Y-m-d');
            
            $chk_periodos = $conn->query("SELECT 1 FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'Inventario_Turnos_Periodos' LIMIT 1");
            if ($chk_periodos && $chk_periodos->num_rows > 0) {
                $hay_alguno = $conn->query("SELECT 1 FROM Inventario_Turnos_Periodos WHERE Activo = 1 LIMIT 1");
                if ($hay_alguno && $hay_alguno->num_rows > 0) {
                    $sql_periodo = "SELECT 1 FROM Inventario_Turnos_Periodos WHERE Activo = 1 AND ? BETWEEN Fecha_Inicio AND Fecha_Fin AND (Fk_sucursal = ? OR Fk_sucursal = 0) LIMIT 1";
                    $stmt_p = $conn->prepare($sql_periodo);
                    if ($stmt_p) {
                        $stmt_p->bind_param("si", $hoy, $sucursal);
                        $stmt_p->execute();
                        $hay_periodo = $stmt_p->get_result()->fetch_assoc();
                        $stmt_p->close();
                        if (!$hay_periodo) {
                            throw new Exception('El inventario por turnos no está habilitado para esta sucursal en la fecha actual. Verifica los periodos configurados.');
                        }
                    }
                }
            }
            
            $max_turnos_sucursal = 0;
            $chk_config_suc = $conn->query("SELECT 1 FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'Inventario_Turnos_Config_Sucursal' LIMIT 1");
            if ($chk_config_suc && $chk_config_suc->num_rows > 0) {
                $stmt_cs = $conn->prepare("SELECT Max_Turnos_Por_Dia, Max_Productos_Por_Turno FROM Inventario_Turnos_Config_Sucursal WHERE Activo = 1 AND (Fk_sucursal = ? OR Fk_sucursal = 0) ORDER BY Fk_sucursal DESC LIMIT 1");
                if ($stmt_cs) {
                    $stmt_cs->bind_param("i", $sucursal);
                    $stmt_cs->execute();
                    $cfg_suc = $stmt_cs->get_result()->fetch_assoc();
                    $stmt_cs->close();
                    if ($cfg_suc) {
                        $max_turnos_sucursal = (int)$cfg_suc['Max_Turnos_Por_Dia'];
                        if ((int)$cfg_suc['Max_Productos_Por_Turno'] > 0) {
                            $limite_productos = (int)$cfg_suc['Max_Productos_Por_Turno'];
                        }
                    }
                }
            }
            
            if ($max_turnos_sucursal > 0) {
                $stmt_cnt = $conn->prepare("SELECT COUNT(*) as total FROM Inventario_Turnos WHERE Fk_sucursal = ? AND DATE(Fecha_Turno) = CURDATE()");
                if ($stmt_cnt) {
                    $stmt_cnt->bind_param("i", $sucursal);
                    $stmt_cnt->execute();
                    $cnt = $stmt_cnt->get_result()->fetch_assoc();
                    $stmt_cnt->close();
                    if ($cnt && (int)$cnt['total'] >= $max_turnos_sucursal) {
                        throw new Exception('Se alcanzó el máximo de turnos de inventario permitidos hoy para esta sucursal.');
                    }
                }
            }
            
            $max_turnos_empleado = 0;
            if ($id_usuario > 0) {
                $chk_emp = $conn->query("SELECT 1 FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'Inventario_Turnos_Config_Empleado' LIMIT 1");
                if ($chk_emp && $chk_emp->num_rows > 0) {
                    $stmt_ce = $conn->prepare("SELECT Max_Turnos_Por_Dia, Max_Productos_Por_Turno FROM Inventario_Turnos_Config_Empleado WHERE Activo = 1 AND Fk_usuario = ? AND (Fk_sucursal = ? OR Fk_sucursal = 0) ORDER BY Fk_sucursal DESC LIMIT 1");
                    if ($stmt_ce) {
                        $stmt_ce->bind_param("ii", $id_usuario, $sucursal);
                        $stmt_ce->execute();
                        $cfg_emp = $stmt_ce->get_result()->fetch_assoc();
                        $stmt_ce->close();
                        if ($cfg_emp) {
                            $max_turnos_empleado = (int)$cfg_emp['Max_Turnos_Por_Dia'];
                            if ((int)$cfg_emp['Max_Productos_Por_Turno'] > 0) {
                                $limite_productos = (int)$cfg_emp['Max_Productos_Por_Turno'];
                            }
                        }
                    }
                }
            }
            
            if ($max_turnos_empleado > 0) {
                $stmt_cu = $conn->prepare("SELECT COUNT(*) as total FROM Inventario_Turnos WHERE Fk_sucursal = ? AND DATE(Fecha_Turno) = CURDATE() AND (Usuario_Actual = ? OR Usuario_Inicio = ?)");
                if ($stmt_cu) {
                    $stmt_cu->bind_param("iss", $sucursal, $usuario, $usuario);
                    $stmt_cu->execute();
                    $cnt_u = $stmt_cu->get_result()->fetch_assoc();
                    $stmt_cu->close();
                    if ($cnt_u && (int)$cnt_u['total'] >= $max_turnos_empleado) {
                        throw new Exception('Has alcanzado el máximo de turnos de inventario permitidos para hoy.');
                    }
                }
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
                
                // Obtener siguiente secuencial y verificar unicidad del folio
                $intentos = 0;
                $folio = null;
                $max_intentos = 100; // Prevenir bucle infinito
                
                while ($intentos < $max_intentos && empty($folio)) {
                    $sql_sec = "SELECT COUNT(*) + 1 as secuencial FROM Inventario_Turnos 
                               WHERE Fk_sucursal = ? AND DATE(Fecha_Turno) = CURDATE()";
                    $stmt_sec = $conn->prepare($sql_sec);
                    $stmt_sec->bind_param("i", $sucursal);
                    $stmt_sec->execute();
                    $sec_data = $stmt_sec->get_result()->fetch_assoc();
                    $stmt_sec->close();
                    
                    $secuencial = $sec_data ? str_pad($sec_data['secuencial'], 3, '0', STR_PAD_LEFT) : '001';
                    $fecha = date('Ymd');
                    $folio_tentativo = "INV-{$sucursal_cod}-{$fecha}-{$secuencial}";
                    
                    // Verificar que el folio no exista
                    $sql_verificar_folio = "SELECT ID_Turno FROM Inventario_Turnos WHERE Folio_Turno = ? LIMIT 1";
                    $stmt_ver_folio = $conn->prepare($sql_verificar_folio);
                    $stmt_ver_folio->bind_param("s", $folio_tentativo);
                    $stmt_ver_folio->execute();
                    $folio_existe = $stmt_ver_folio->get_result()->fetch_assoc();
                    $stmt_ver_folio->close();
                    
                    if (!$folio_existe) {
                        $folio = $folio_tentativo;
                    } else {
                        // Si el folio existe, incrementar el secuencial y volver a intentar
                        $intentos++;
                        // Esperar un momento para evitar colisiones en alta concurrencia
                        usleep(10000); // 10ms
                    }
                }
                
                if (empty($folio)) {
                    throw new Exception('No se pudo generar un folio único después de ' . $max_intentos . ' intentos');
                }
            }
            
            // Validar que el folio no esté vacío
            if (empty($folio)) {
                throw new Exception('No se pudo generar el folio del turno');
            }
            
            // Verificación final de unicidad antes de insertar
            $sql_verificar_folio_final = "SELECT ID_Turno FROM Inventario_Turnos WHERE Folio_Turno = ? LIMIT 1";
            $stmt_ver_folio_final = $conn->prepare($sql_verificar_folio_final);
            $stmt_ver_folio_final->bind_param("s", $folio);
            $stmt_ver_folio_final->execute();
            $folio_existe_final = $stmt_ver_folio_final->get_result()->fetch_assoc();
            $stmt_ver_folio_final->close();
            
            if ($folio_existe_final) {
                throw new Exception('El folio generado ya existe. Por favor, intenta nuevamente.');
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
            
            // Crear turno con límite de productos desde config (periodo/sucursal/empleado) o 50 por defecto
            if ($col_exists) {
                $sql_insert = "INSERT INTO Inventario_Turnos (
                    Folio_Turno, Fk_sucursal, Usuario_Inicio, Usuario_Actual,
                    Fecha_Turno, Estado, Limite_Productos
                ) VALUES (?, ?, ?, ?, CURDATE(), 'activo', ?)";
                $stmt_insert = $conn->prepare($sql_insert);
                if (!$stmt_insert) {
                    throw new Exception('Error al preparar la inserción: ' . $conn->error);
                }
                // IMPORTANTE: Verificar que $sucursal sea un entero válido
                $sucursal_int = (int)$sucursal;
                if ($sucursal_int <= 0) {
                    throw new Exception('Error: La sucursal debe ser un número mayor a 0. Valor recibido: ' . $sucursal);
                }
                $stmt_insert->bind_param("sissi", $folio, $sucursal_int, $usuario, $usuario, $limite_productos);
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
            
            // Contar productos totales seleccionados (no liberados)
            $sql_total = "SELECT COUNT(*) as total FROM Inventario_Turnos_Productos 
                          WHERE ID_Turno = ? AND Estado != 'liberado'";
            $stmt_total = $conn->prepare($sql_total);
            $stmt_total->bind_param("i", $id_turno);
            $stmt_total->execute();
            $total_data = $stmt_total->get_result()->fetch_assoc();
            $stmt_total->close();
            
            $total_productos = $total_data ? (int)$total_data['total'] : 0;
            
            // Contar productos completados
            $sql_completados = "SELECT COUNT(*) as total FROM Inventario_Turnos_Productos 
                               WHERE ID_Turno = ? AND Estado = 'completado'";
            $stmt_comp = $conn->prepare($sql_completados);
            $stmt_comp->bind_param("i", $id_turno);
            $stmt_comp->execute();
            $comp_data = $stmt_comp->get_result()->fetch_assoc();
            $stmt_comp->close();
            
            $productos_completados = $comp_data ? (int)$comp_data['total'] : 0;
            
            // El turno solo se cierra cuando tenga 50 productos contados (completados)
            $limite_requerido = isset($turno['Limite_Productos']) ? (int)$turno['Limite_Productos'] : 50;
            if ($productos_completados < $limite_requerido) {
                throw new Exception("No puedes finalizar el turno. Debes tener {$limite_requerido} productos contados. Tienes {$productos_completados} de {$limite_requerido}.");
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
            
            // Verificar si este producto ya fue contado en OTRO turno (misma sucursal, mismo día)
            // para evitar que dos turnos distintos cuenten el mismo producto el mismo día
            $sql_ya_contado = "SELECT it.Folio_Turno 
                              FROM Inventario_Turnos_Productos itp
                              INNER JOIN Inventario_Turnos it ON itp.ID_Turno = it.ID_Turno
                              WHERE itp.ID_Prod_POS = ? 
                                AND itp.Fk_sucursal = ?
                                AND itp.Estado = 'completado'
                                AND it.ID_Turno != ?
                                AND it.Fk_sucursal = ?
                                AND DATE(it.Fecha_Turno) = CURDATE()
                              LIMIT 1";
            $stmt_ya = $conn->prepare($sql_ya_contado);
            $stmt_ya->bind_param("iiii", $id_producto, $sucursal, $id_turno, $sucursal);
            $stmt_ya->execute();
            $ya_contado = $stmt_ya->get_result()->fetch_assoc();
            $stmt_ya->close();
            
            if ($ya_contado) {
                throw new Exception('Este producto ya fue contado en el turno ' . $ya_contado['Folio_Turno'] . ' hoy. No se puede contar el mismo producto en otro turno el mismo día.');
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
            
            // Obtener estado actualizado del turno para enviar al frontend
            $sql_estado = "SELECT Total_Productos, Productos_Completados FROM Inventario_Turnos WHERE ID_Turno = ?";
            $stmt_estado = $conn->prepare($sql_estado);
            $stmt_estado->bind_param("i", $id_turno);
            $stmt_estado->execute();
            $estado_data = $stmt_estado->get_result()->fetch_assoc();
            $stmt_estado->close();
            
            echo json_encode([
                'success' => true,
                'message' => 'Producto seleccionado correctamente',
                'total_productos' => $estado_data['Total_Productos'] ?? 0,
                'productos_completados' => $estado_data['Productos_Completados'] ?? 0
            ]);
            break;
            
        case 'obtener_estado':
            $id_turno = isset($_POST['id_turno']) ? (int)$_POST['id_turno'] : 0;
            if ($id_turno <= 0) {
                throw new Exception('ID de turno inválido');
            }
            
            // Contar productos totales (no liberados)
            $sql_total = "SELECT COUNT(*) as total FROM Inventario_Turnos_Productos 
                          WHERE ID_Turno = ? AND Estado != 'liberado'";
            $stmt_total = $conn->prepare($sql_total);
            $stmt_total->bind_param("i", $id_turno);
            $stmt_total->execute();
            $total_data = $stmt_total->get_result()->fetch_assoc();
            $stmt_total->close();
            
            $total_productos = $total_data ? (int)$total_data['total'] : 0;
            
            // Contar productos completados
            $sql_completados = "SELECT COUNT(*) as total FROM Inventario_Turnos_Productos 
                               WHERE ID_Turno = ? AND Estado = 'completado'";
            $stmt_comp = $conn->prepare($sql_completados);
            $stmt_comp->bind_param("i", $id_turno);
            $stmt_comp->execute();
            $comp_data = $stmt_comp->get_result()->fetch_assoc();
            $stmt_comp->close();
            
            $productos_completados = $comp_data ? (int)$comp_data['total'] : 0;
            
            echo json_encode([
                'success' => true,
                'total_productos' => $total_productos,
                'productos_completados' => $productos_completados
            ]);
            break;
            
        case 'contar_producto':
            $id_registro = isset($_POST['id_registro']) ? (int)$_POST['id_registro'] : 0;
            $existencias_fisicas = isset($_POST['existencias_fisicas']) ? (int)$_POST['existencias_fisicas'] : 0;
            $lote = isset($_POST['lote']) ? trim($_POST['lote']) : '';
            $fecha_caducidad = isset($_POST['fecha_caducidad']) ? trim($_POST['fecha_caducidad']) : '';
            $lotes_json = isset($_POST['lotes_json']) ? $_POST['lotes_json'] : '';
            
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
            
            $id_prod = (int) $registro['ID_Prod_POS'];
            $fk_suc = (int) $registro['Fk_sucursal'];
            $cod_barra = $registro['Cod_Barra'];
            $nombre_prod = $registro['Nombre_Producto'] ?? '';
            
            // Lista de lotes desde frontend (cada uno: lote, fecha_caducidad, cantidad)
            $lotes_array = [];
            if ($lotes_json !== '') {
                $dec = json_decode($lotes_json, true);
                if (is_array($dec)) {
                    $lotes_array = $dec;
                }
            }
            // Compatibilidad: si no hay lotes_json pero sí lote/fecha_caducidad, usar un solo lote
            if (empty($lotes_array) && ($lote !== '' || ($fecha_caducidad !== '' && preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha_caducidad)))) {
                $lotes_array = [['lote' => $lote, 'fecha_caducidad' => $fecha_caducidad, 'cantidad' => $existencias_fisicas]];
            }
            
            $ya_completado = ($registro['Estado'] == 'completado');
            $diferencia = $existencias_fisicas - (int)$registro['Existencias_Sistema'];
            
            // Historial_Lotes: SOLO sumar si el lote YA EXISTE; si NO existe, insertar nuevo. Nada más.
            $chk_hl_table = $conn->query("SHOW TABLES LIKE 'Historial_Lotes'");
            if ($chk_hl_table && $chk_hl_table->num_rows > 0) {
                foreach ($lotes_array as $item) {
                    $lote_val = isset($item['lote']) ? trim($item['lote']) : '';
                    $fecha_val = isset($item['fecha_caducidad']) ? trim($item['fecha_caducidad']) : '';
                    $cant = isset($item['cantidad']) ? (int)$item['cantidad'] : 0;
                    if ($fecha_val !== '' && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha_val)) {
                        $fecha_val = '';
                    }
                    if ($lote_val === '' && $fecha_val === '') {
                        continue;
                    }
                    if ($lote_val === '') {
                        $lote_val = 'S/LOTE';
                    }
                    $existe_hl = $conn->prepare("SELECT ID_Historial, Existencias FROM Historial_Lotes WHERE ID_Prod_POS = ? AND Fk_sucursal = ? AND Lote = ? LIMIT 1");
                    $existe_hl->bind_param("iis", $id_prod, $fk_suc, $lote_val);
                    $existe_hl->execute();
                    $hl_row = $existe_hl->get_result()->fetch_assoc();
                    $existe_hl->close();
                    if ($hl_row) {
                        // Lote YA EXISTE: solo sumar cantidad a ese lote
                        $nueva_exist = (int)$hl_row['Existencias'] + $cant;
                        $up_hl = $conn->prepare("UPDATE Historial_Lotes SET Existencias = ?, Usuario_Modifico = ?, Fecha_Registro = NOW() WHERE ID_Historial = ?");
                        $up_hl->bind_param("isi", $nueva_exist, $usuario, $hl_row['ID_Historial']);
                        $up_hl->execute();
                        $up_hl->close();
                    } else {
                        // Lote NO existe: insertar nuevo registro
                        $ins_hl = $conn->prepare("INSERT INTO Historial_Lotes (ID_Prod_POS, Fk_sucursal, Lote, Fecha_Caducidad, Fecha_Ingreso, Existencias, Usuario_Modifico) VALUES (?, ?, ?, ?, CURDATE(), ?, ?)");
                        $fecha_cad_hl = $fecha_val !== '' ? $fecha_val : null;
                        $ins_hl->bind_param("iissis", $id_prod, $fk_suc, $lote_val, $fecha_cad_hl, $cant, $usuario);
                        $ins_hl->execute();
                        $ins_hl->close();
                        $chk_control = $conn->query("SHOW COLUMNS FROM Stock_POS LIKE 'Control_Lotes_Caducidad'");
                        if ($chk_control && $chk_control->num_rows > 0) {
                            $up_ctrl = $conn->prepare("UPDATE Stock_POS SET Control_Lotes_Caducidad = 1 WHERE ID_Prod_POS = ? AND Fk_sucursal = ? AND (Control_Lotes_Caducidad IS NULL OR Control_Lotes_Caducidad = 0)");
                            $up_ctrl->bind_param("ii", $id_prod, $fk_suc);
                            $up_ctrl->execute();
                            $up_ctrl->close();
                        }
                    }
                }
            }
            
            // Verificar columnas Lote/Fecha_Caducidad en Inventario_Turnos_Productos (guardar primer lote o null)
            $tiene_lote = false;
            $tiene_fecha = false;
            $chk_col = $conn->query("SHOW COLUMNS FROM Inventario_Turnos_Productos LIKE 'Lote'");
            if ($chk_col && $chk_col->num_rows > 0) $tiene_lote = true;
            $chk_col = $conn->query("SHOW COLUMNS FROM Inventario_Turnos_Productos LIKE 'Fecha_Caducidad'");
            if ($chk_col && $chk_col->num_rows > 0) $tiene_fecha = true;
            $primer_lote = null;
            $primer_fecha = null;
            if (!empty($lotes_array)) {
                $p = $lotes_array[0];
                $primer_lote = isset($p['lote']) && trim($p['lote']) !== '' ? trim($p['lote']) : null;
                $primer_fecha = isset($p['fecha_caducidad']) && preg_match('/^\d{4}-\d{2}-\d{2}$/', trim($p['fecha_caducidad'] ?? '')) ? trim($p['fecha_caducidad']) : null;
            }
            if ($primer_lote === null && $lote !== '') {
                $primer_lote = $lote;
                $primer_fecha = $fecha_caducidad !== '' && preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha_caducidad) ? $fecha_caducidad : null;
            }
            
            if ($tiene_lote && $tiene_fecha) {
                $sql_update = "UPDATE Inventario_Turnos_Productos SET
                    Existencias_Fisicas = ?,
                    Diferencia = ?,
                    Estado = 'completado',
                    Fecha_Conteo = NOW(),
                    Lote = ?,
                    Fecha_Caducidad = ?
                WHERE ID_Registro = ?";
                $stmt_update = $conn->prepare($sql_update);
                $stmt_update->bind_param("iissi", $existencias_fisicas, $diferencia, $primer_lote, $primer_fecha, $id_registro);
            } else {
                $sql_update = "UPDATE Inventario_Turnos_Productos SET
                    Existencias_Fisicas = ?,
                    Diferencia = ?,
                    Estado = 'completado',
                    Fecha_Conteo = NOW()
                WHERE ID_Registro = ?";
                $stmt_update = $conn->prepare($sql_update);
                $stmt_update->bind_param("iii", $existencias_fisicas, $diferencia, $id_registro);
            }
            $stmt_update->execute();
            $stmt_update->close();
            
            // Afectar Stock_POS con la diferencia del conteo (físico - sistema). Si existe tabla
            // InventariosStocks_Conteos y el trigger, usarla; si falla o no existe, actualizar Stock_POS directo
            // para que el guardado NUNCA falle por esto.
            $stock_actualizado = false;
            $chk_inv = $conn->query("SHOW TABLES LIKE 'InventariosStocks_Conteos'");
            if ($chk_inv && $chk_inv->num_rows > 0) {
                try {
                    $sql_stock = "SELECT Nombre_Prod, Cod_Barra FROM Stock_POS WHERE ID_Prod_POS = ? AND Fk_sucursal = ? LIMIT 1";
                    $stmt_stock = $conn->prepare($sql_stock);
                    if ($stmt_stock) {
                        $stmt_stock->bind_param("ii", $id_prod, $fk_suc);
                        $stmt_stock->execute();
                        $sp = $stmt_stock->get_result()->fetch_assoc();
                        $stmt_stock->close();
                        if ($sp) {
                            $nombre_prod = $nombre_prod !== '' ? $nombre_prod : ($sp['Nombre_Prod'] ?? '');
                            $cod_barra = $cod_barra !== '' ? $cod_barra : ($sp['Cod_Barra'] ?? '');
                            $precio_v = 0.0;
                            $precio_c = 0.0;
                            $contabilizado = (int)$registro['Existencias_Sistema'];
                            $sistema = 'Conteo turno farmacias';
                            $id_hod = isset($_SESSION['ID_H_O_D']) ? (string)$_SESSION['ID_H_O_D'] : '0';
                            $fecha_inv = date('Y-m-d');
                            $tipo_ajuste = 'Conteo diario';
                            $anaquel = '';
                            $repisa = '';
                            $col_folio = $conn->query("SHOW COLUMNS FROM InventariosStocks_Conteos WHERE Field = 'Folio_Prod_Stock'");
                            $folio_auto = false;
                            if ($col_folio && $row_f = $col_folio->fetch_assoc()) {
                                $folio_auto = (stripos($row_f['Extra'] ?? '', 'auto_increment') !== false);
                            }
                            if ($folio_auto) {
                                $ins_inv = $conn->prepare("INSERT INTO InventariosStocks_Conteos (ID_Prod_POS, Cod_Barra, Nombre_Prod, Fk_sucursal, Precio_Venta, Precio_C, Contabilizado, StockEnMomento, Diferencia, Sistema, AgregadoPor, ID_H_O_D, FechaInventario, Tipo_Ajuste, Anaquel, Repisa) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                                $ins_inv->bind_param("issiddiiisssssss", $id_prod, $cod_barra, $nombre_prod, $fk_suc, $precio_v, $precio_c, $contabilizado, $existencias_fisicas, $diferencia, $sistema, $usuario, $id_hod, $fecha_inv, $tipo_ajuste, $anaquel, $repisa);
                            } else {
                                $res_max = $conn->query("SELECT COALESCE(MAX(Folio_Prod_Stock), 0) + 1 AS next_folio FROM InventariosStocks_Conteos");
                                $next_folio = 1;
                                if ($res_max && $row_max = $res_max->fetch_assoc()) {
                                    $next_folio = (int)$row_max['next_folio'];
                                }
                                $ins_inv = $conn->prepare("INSERT INTO InventariosStocks_Conteos (Folio_Prod_Stock, ID_Prod_POS, Cod_Barra, Nombre_Prod, Fk_sucursal, Precio_Venta, Precio_C, Contabilizado, StockEnMomento, Diferencia, Sistema, AgregadoPor, ID_H_O_D, FechaInventario, Tipo_Ajuste, Anaquel, Repisa) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                                $ins_inv->bind_param("iissiddiiisssssss", $next_folio, $id_prod, $cod_barra, $nombre_prod, $fk_suc, $precio_v, $precio_c, $contabilizado, $existencias_fisicas, $diferencia, $sistema, $usuario, $id_hod, $fecha_inv, $tipo_ajuste, $anaquel, $repisa);
                            }
                            $ins_inv->execute();
                            $ins_inv->close();
                            $stock_actualizado = true;
                        }
                    }
                } catch (Exception $e) {
                    // Si falla el insert en InventariosStocks_Conteos, no romper el guardado
                }
            }
            if (!$stock_actualizado && $diferencia != 0) {
                $up_stock = $conn->prepare("UPDATE Stock_POS SET Existencias_R = IFNULL(Existencias_R, 0) + ? WHERE ID_Prod_POS = ? AND Fk_sucursal = ?");
                $up_stock->bind_param("iii", $diferencia, $id_prod, $fk_suc);
                $up_stock->execute();
                $up_stock->close();
            }
            
            // Actualizar contador de completados solo si no estaba completado antes
            if (!$ya_completado) {
                $sql_completados = "UPDATE Inventario_Turnos SET 
                    Productos_Completados = Productos_Completados + 1 
                WHERE ID_Turno = ?";
                $stmt_comp = $conn->prepare($sql_completados);
                $stmt_comp->bind_param("i", $registro['ID_Turno']);
                $stmt_comp->execute();
                $stmt_comp->close();
            }
            
            // Obtener estado actualizado del turno
            $sql_estado = "SELECT Total_Productos, Productos_Completados FROM Inventario_Turnos WHERE ID_Turno = ?";
            $stmt_estado = $conn->prepare($sql_estado);
            $stmt_estado->bind_param("i", $registro['ID_Turno']);
            $stmt_estado->execute();
            $estado_data = $stmt_estado->get_result()->fetch_assoc();
            $stmt_estado->close();
            
            echo json_encode([
                'success' => true,
                'message' => 'Conteo registrado correctamente. Stock actualizado.',
                'diferencia' => $diferencia,
                'total_productos' => $estado_data['Total_Productos'] ?? 0,
                'productos_completados' => $estado_data['Productos_Completados'] ?? 0
            ]);
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
