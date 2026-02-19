<?php
/**
 * API Configuración Inventario por Turnos: periodos, config sucursal, config empleado.
 * GET: listar periodos, config sucursal, config empleados.
 * POST: guardar periodo, config_sucursal, config_empleado.
 */
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type');

include_once __DIR__ . '/../Controladores/db_connect.php';
include_once __DIR__ . '/../Controladores/ControladorUsuario.php';

$usuario_nombre = isset($row['Nombre_Apellidos']) ? $row['Nombre_Apellidos'] : 'Sistema';
$id_usuario = isset($row['Id_PvUser']) ? (int)$row['Id_PvUser'] : 0;
$tipoUsuario = isset($row['TipoUsuario']) ? $row['TipoUsuario'] : '';
$isAdmin = ($tipoUsuario === 'Administrador' || $tipoUsuario === 'MKT');

// Verificar que las tablas existan
function tabla_existe($conn, $nombre) {
    $r = $conn->query("SELECT 1 FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = '" . $conn->real_escape_string($nombre) . "' LIMIT 1");
    return $r && $r->num_rows > 0;
}

try {
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $sucursal = isset($_GET['sucursal']) ? (int)$_GET['sucursal'] : 0;
        $action = isset($_GET['action']) ? $_GET['action'] : '';

        $out = ['success' => true, 'periodos' => [], 'config_sucursal' => [], 'config_empleados' => []];

        // Periodos
        if ((!$action || $action === 'periodos') && tabla_existe($conn, 'Inventario_Turnos_Periodos')) {
            $sql = "SELECT ID_Periodo, Fk_sucursal, Fecha_Inicio, Fecha_Fin, Nombre_Periodo, Codigo_Externo, Activo, Actualizado_Por, Fecha_Actualizacion
                    FROM Inventario_Turnos_Periodos WHERE Activo = 1 ORDER BY Fecha_Inicio DESC";
            if ($sucursal > 0) {
                $stmt = $conn->prepare("SELECT ID_Periodo, Fk_sucursal, Fecha_Inicio, Fecha_Fin, Nombre_Periodo, Codigo_Externo, Activo, Actualizado_Por, Fecha_Actualizacion
                    FROM Inventario_Turnos_Periodos WHERE Activo = 1 AND (Fk_sucursal = ? OR Fk_sucursal = 0) ORDER BY Fecha_Inicio DESC");
                if ($stmt) {
                    $stmt->bind_param("i", $sucursal);
                    $stmt->execute();
                    $res = $stmt->get_result();
                    while ($f = $res->fetch_assoc()) $out['periodos'][] = $f;
                    $stmt->close();
                }
            } else {
                $res = $conn->query($sql);
                if ($res) while ($f = $res->fetch_assoc()) $out['periodos'][] = $f;
            }
        }

        // Config sucursal (todas las filas para admin, o la de la sucursal)
        if ((!$action || $action === 'config_sucursal') && tabla_existe($conn, 'Inventario_Turnos_Config_Sucursal')) {
            if ($sucursal > 0) {
                $stmt = $conn->prepare("SELECT ID_Config, Fk_sucursal, Max_Turnos_Por_Dia, Max_Productos_Por_Turno, Activo FROM Inventario_Turnos_Config_Sucursal WHERE Fk_sucursal = ? OR Fk_sucursal = 0 ORDER BY Fk_sucursal DESC");
                if ($stmt) {
                    $stmt->bind_param("i", $sucursal);
                    $stmt->execute();
                    $res = $stmt->get_result();
                    while ($f = $res->fetch_assoc()) $out['config_sucursal'][] = $f;
                    $stmt->close();
                }
            } else {
                $res = $conn->query("SELECT ID_Config, Fk_sucursal, Max_Turnos_Por_Dia, Max_Productos_Por_Turno, Activo FROM Inventario_Turnos_Config_Sucursal ORDER BY Fk_sucursal ASC");
                if ($res) while ($f = $res->fetch_assoc()) $out['config_sucursal'][] = $f;
            }
        }

        // Config empleados (solo para admin en pantalla de config)
        if ((!$action || $action === 'config_empleados') && tabla_existe($conn, 'Inventario_Turnos_Config_Empleado')) {
            $sql = "SELECT c.ID_Config, c.Fk_usuario, c.Fk_sucursal, c.Max_Turnos_Por_Dia, c.Max_Productos_Por_Turno, c.Activo, u.Nombre_Apellidos
                    FROM Inventario_Turnos_Config_Empleado c
                    LEFT JOIN Usuarios_PV u ON u.Id_PvUser = c.Fk_usuario
                    ORDER BY u.Nombre_Apellidos, c.Fk_sucursal";
            $res = $conn->query($sql);
            if ($res) while ($f = $res->fetch_assoc()) $out['config_empleados'][] = $f;
        }

        // Lista de usuarios activos (para dropdown config empleado)
        if ($action === 'usuarios') {
            $lic = isset($row['Licencia']) ? $row['Licencia'] : '';
            $out['usuarios'] = [];
            $sql_u = "SELECT Id_PvUser, Nombre_Apellidos FROM Usuarios_PV WHERE Estatus = 'Activo' ORDER BY Nombre_Apellidos ASC";
            if ($lic !== '') {
                $st = $conn->prepare("SELECT Id_PvUser, Nombre_Apellidos FROM Usuarios_PV WHERE Estatus = 'Activo' AND Licencia = ? ORDER BY Nombre_Apellidos ASC");
                if ($st) {
                    $st->bind_param("s", $lic);
                    $st->execute();
                    $res = $st->get_result();
                    while ($r = $res->fetch_assoc()) $out['usuarios'][] = $r;
                    $st->close();
                }
            } else {
                $res = $conn->query($sql_u);
                if ($res) while ($r = $res->fetch_assoc()) $out['usuarios'][] = $r;
            }
        }

        echo json_encode($out);
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['success' => false, 'message' => 'Método no permitido']);
        exit;
    }

    if (!$isAdmin) {
        echo json_encode(['success' => false, 'message' => 'Solo administradores pueden modificar la configuración.']);
        exit;
    }

    $input = $_POST;
    $accion = isset($input['accion']) ? trim($input['accion']) : '';

    if ($accion === 'guardar_periodo') {
        if (!tabla_existe($conn, 'Inventario_Turnos_Periodos')) {
            echo json_encode(['success' => false, 'message' => 'La tabla de periodos no existe. Ejecute el script SQL de configuración.']);
            exit;
        }
        $id = isset($input['ID_Periodo']) ? (int)$input['ID_Periodo'] : 0;
        $fk_sucursal = isset($input['Fk_sucursal']) ? (int)$input['Fk_sucursal'] : 0;
        $fecha_inicio = isset($input['Fecha_Inicio']) ? $input['Fecha_Inicio'] : '';
        $fecha_fin = isset($input['Fecha_Fin']) ? $input['Fecha_Fin'] : '';
        $nombre = isset($input['Nombre_Periodo']) ? trim($input['Nombre_Periodo']) : null;
        $codigo_ext = isset($input['Codigo_Externo']) ? trim($input['Codigo_Externo']) : null;
        $activo = isset($input['Activo']) ? (int)$input['Activo'] : 1;

        if (!$fecha_inicio || !$fecha_fin) {
            echo json_encode(['success' => false, 'message' => 'Fecha inicio y fecha fin son obligatorias.']);
            exit;
        }
        if (strtotime($fecha_fin) < strtotime($fecha_inicio)) {
            echo json_encode(['success' => false, 'message' => 'La fecha fin debe ser mayor o igual a la fecha inicio.']);
            exit;
        }

        if ($id > 0) {
            $stmt = $conn->prepare("UPDATE Inventario_Turnos_Periodos SET Fk_sucursal=?, Fecha_Inicio=?, Fecha_Fin=?, Nombre_Periodo=?, Codigo_Externo=?, Activo=?, Actualizado_Por=? WHERE ID_Periodo=?");
            if (!$stmt) { echo json_encode(['success' => false, 'message' => $conn->error]); exit; }
            $stmt->bind_param("issssisi", $fk_sucursal, $fecha_inicio, $fecha_fin, $nombre, $codigo_ext, $activo, $usuario_nombre, $id);
        } else {
            $stmt = $conn->prepare("INSERT INTO Inventario_Turnos_Periodos (Fk_sucursal, Fecha_Inicio, Fecha_Fin, Nombre_Periodo, Codigo_Externo, Activo, Actualizado_Por) VALUES (?,?,?,?,?,?,?)");
            if (!$stmt) { echo json_encode(['success' => false, 'message' => $conn->error]); exit; }
            $stmt->bind_param("issssis", $fk_sucursal, $fecha_inicio, $fecha_fin, $nombre, $codigo_ext, $activo, $usuario_nombre);
        }
        $stmt->execute();
        if ($stmt->errno) { echo json_encode(['success' => false, 'message' => $stmt->error]); exit; }
        $stmt->close();
        echo json_encode(['success' => true, 'message' => 'Periodo guardado.', 'id' => $id ?: $conn->insert_id]);
        exit;
    }

    if ($accion === 'guardar_config_sucursal') {
        if (!tabla_existe($conn, 'Inventario_Turnos_Config_Sucursal')) {
            echo json_encode(['success' => false, 'message' => 'La tabla de configuración por sucursal no existe.']);
            exit;
        }
        $fk_sucursal = isset($input['Fk_sucursal']) ? (int)$input['Fk_sucursal'] : 0;
        $max_turnos = isset($input['Max_Turnos_Por_Dia']) ? (int)$input['Max_Turnos_Por_Dia'] : 0;
        $max_productos = isset($input['Max_Productos_Por_Turno']) ? (int)$input['Max_Productos_Por_Turno'] : 50;
        $max_productos = $max_productos <= 0 ? 50 : $max_productos;

        $stmt = $conn->prepare("INSERT INTO Inventario_Turnos_Config_Sucursal (Fk_sucursal, Max_Turnos_Por_Dia, Max_Productos_Por_Turno, Activo, Actualizado_Por) VALUES (?,?,?,1,?)
            ON DUPLICATE KEY UPDATE Max_Turnos_Por_Dia=VALUES(Max_Turnos_Por_Dia), Max_Productos_Por_Turno=VALUES(Max_Productos_Por_Turno), Actualizado_Por=VALUES(Actualizado_Por), Fecha_Actualizacion=NOW()");
        if (!$stmt) { echo json_encode(['success' => false, 'message' => $conn->error]); exit; }
        $stmt->bind_param("iiis", $fk_sucursal, $max_turnos, $max_productos, $usuario_nombre);
        $stmt->execute();
        if ($stmt->errno) { echo json_encode(['success' => false, 'message' => $stmt->error]); exit; }
        $stmt->close();
        echo json_encode(['success' => true, 'message' => 'Configuración de sucursal guardada.']);
        exit;
    }

    if ($accion === 'guardar_config_empleado') {
        if (!tabla_existe($conn, 'Inventario_Turnos_Config_Empleado')) {
            echo json_encode(['success' => false, 'message' => 'La tabla de configuración por empleado no existe.']);
            exit;
        }
        $fk_usuario = isset($input['Fk_usuario']) ? (int)$input['Fk_usuario'] : 0;
        $fk_sucursal = isset($input['Fk_sucursal']) ? (int)$input['Fk_sucursal'] : 0;
        $max_turnos = isset($input['Max_Turnos_Por_Dia']) ? (int)$input['Max_Turnos_Por_Dia'] : 0;
        $max_productos = isset($input['Max_Productos_Por_Turno']) ? (int)$input['Max_Productos_Por_Turno'] : 0;

        if ($fk_usuario <= 0) {
            echo json_encode(['success' => false, 'message' => 'Debe seleccionar un empleado.']);
            exit;
        }

        $stmt = $conn->prepare("INSERT INTO Inventario_Turnos_Config_Empleado (Fk_usuario, Fk_sucursal, Max_Turnos_Por_Dia, Max_Productos_Por_Turno, Activo, Actualizado_Por) VALUES (?,?,?,?,1,?)
            ON DUPLICATE KEY UPDATE Max_Turnos_Por_Dia=VALUES(Max_Turnos_Por_Dia), Max_Productos_Por_Turno=VALUES(Max_Productos_Por_Turno), Actualizado_Por=VALUES(Actualizado_Por), Fecha_Actualizacion=NOW()");
        if (!$stmt) { echo json_encode(['success' => false, 'message' => $conn->error]); exit; }
        $stmt->bind_param("iiiss", $fk_usuario, $fk_sucursal, $max_turnos, $max_productos, $usuario_nombre);
        $stmt->execute();
        if ($stmt->errno) { echo json_encode(['success' => false, 'message' => $stmt->error]); exit; }
        $stmt->close();
        echo json_encode(['success' => true, 'message' => 'Configuración de empleado guardada.']);
        exit;
    }

    if ($accion === 'eliminar_periodo') {
        $id = isset($input['ID_Periodo']) ? (int)$input['ID_Periodo'] : 0;
        if ($id <= 0) { echo json_encode(['success' => false, 'message' => 'ID de periodo no válido.']); exit; }
        $conn->query("UPDATE Inventario_Turnos_Periodos SET Activo = 0 WHERE ID_Periodo = " . (int)$id);
        echo json_encode(['success' => true, 'message' => 'Periodo desactivado.']);
        exit;
    }

    echo json_encode(['success' => false, 'message' => 'Acción no válida.']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
$conn->close();
