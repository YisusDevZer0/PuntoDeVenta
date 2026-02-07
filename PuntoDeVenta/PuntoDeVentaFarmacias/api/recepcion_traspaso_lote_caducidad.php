<?php
/**
 * Recepción de traspasos con lote y caducidad - Módulo Farmacias.
 * Solo permite recibir traspasos destinados a la sucursal del usuario.
 */
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

include_once __DIR__ . '/../Controladores/db_connect.php';
if (!isset($conn) || !$conn) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Error de conexión a la base de datos']);
    exit;
}

$usuario = 'Sistema';
if (file_exists(__DIR__ . '/../Controladores/ControladorUsuario.php')) {
    try {
        include_once __DIR__ . '/../Controladores/ControladorUsuario.php';
        if (isset($row['Nombre_Apellidos'])) {
            $usuario = $row['Nombre_Apellidos'];
        }
    } catch (Exception $e) {
        $usuario = 'Sistema';
    }
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Método no permitido']);
    exit;
}

$id_traspaso = isset($_POST['id_traspaso']) ? (int) $_POST['id_traspaso'] : 0;
$fk_sucursal = isset($_POST['fk_sucursal']) ? (int) $_POST['fk_sucursal'] : 0;
$cantidad_recibida = isset($_POST['cantidad_recibida']) ? (int) $_POST['cantidad_recibida'] : 0;
$lote = isset($_POST['lote']) ? trim($_POST['lote']) : '';
$fecha_caducidad_raw = isset($_POST['fecha_caducidad']) ? trim($_POST['fecha_caducidad']) : '';
$observaciones = isset($_POST['observaciones']) ? trim($_POST['observaciones']) : '';

$fecha_caducidad = '';
if ($fecha_caducidad_raw !== '') {
    $d = DateTime::createFromFormat('Y-m-d', $fecha_caducidad_raw);
    if (!$d) $d = DateTime::createFromFormat('d/m/Y', $fecha_caducidad_raw);
    if ($d) $fecha_caducidad = $d->format('Y-m-d');
    else $fecha_caducidad = $fecha_caducidad_raw;
}

$motivo_diferencia = isset($_POST['motivo_diferencia']) ? trim($_POST['motivo_diferencia']) : '';
$lote_adicional = isset($_POST['lote_adicional']) ? trim($_POST['lote_adicional']) : '';
$fecha_caducidad_adicional = isset($_POST['fecha_caducidad_adicional']) ? trim($_POST['fecha_caducidad_adicional']) : '';
$cantidad_lote_adicional = isset($_POST['cantidad_lote_adicional']) ? (int) $_POST['cantidad_lote_adicional'] : 0;
$cantidad_lote_principal = isset($_POST['cantidad_lote_principal']) ? (int) $_POST['cantidad_lote_principal'] : 0;

if ($id_traspaso <= 0 || $fk_sucursal <= 0 || $cantidad_recibida < 1 || $lote === '' || $fecha_caducidad === '') {
    $detalle = [];
    if ($id_traspaso <= 0) $detalle[] = 'traspaso';
    if ($fk_sucursal <= 0) $detalle[] = 'sucursal';
    if ($cantidad_recibida < 1) $detalle[] = 'cantidad';
    if ($lote === '') $detalle[] = 'lote';
    if ($fecha_caducidad === '') $detalle[] = 'fecha caducidad';
    echo json_encode(['success' => false, 'error' => 'Faltan datos requeridos: ' . implode(', ', $detalle) . '.']);
    exit;
}

$fecha_ok = DateTime::createFromFormat('Y-m-d', $fecha_caducidad);
if (!$fecha_ok) {
    echo json_encode(['success' => false, 'error' => 'Fecha de caducidad inválida.']);
    exit;
}

try {
    $stmt = $conn->prepare("
        SELECT TraspaNotID, Cod_Barra, Nombre_Prod, Cantidad, Fk_SucursalDestino
        FROM TraspasosYNotasC
        WHERE TraspaNotID = ? AND Estatus = 'Generado'
    ");
    $stmt->bind_param('i', $id_traspaso);
    $stmt->execute();
    $res = $stmt->get_result();
    $tyc = $res->fetch_assoc();
    $stmt->close();

    if (!$tyc) {
        echo json_encode(['success' => false, 'error' => 'Traspaso no encontrado o ya fue recibido.']);
        exit;
    }

    if ((int) $tyc['Fk_SucursalDestino'] !== $fk_sucursal) {
        echo json_encode(['success' => false, 'error' => 'La sucursal no coincide con el traspaso.']);
        exit;
    }

    // Farmacias: solo puede recibir traspasos destinados a su sucursal
    $sucursal_usuario = (int) ($row['Fk_Sucursal'] ?? $row['Fk_sucursal'] ?? 0);
    if ($sucursal_usuario > 0 && $fk_sucursal !== $sucursal_usuario) {
        echo json_encode(['success' => false, 'error' => 'Solo puede recibir traspasos destinados a su sucursal.']);
        exit;
    }

    $cantidad_enviada = (int) $tyc['Cantidad'];

    if ($motivo_diferencia === 'otro_lote') {
        if ($cantidad_lote_principal < 1) {
            echo json_encode(['success' => false, 'error' => 'Debe ingresar la cantidad del lote principal.']);
            exit;
        }
        if ($cantidad_lote_adicional < 1) {
            echo json_encode(['success' => false, 'error' => 'Debe ingresar la cantidad del lote adicional.']);
            exit;
        }
        if (empty($lote_adicional) || empty($fecha_caducidad_adicional)) {
            echo json_encode(['success' => false, 'error' => 'Debe completar todos los campos del lote adicional.']);
            exit;
        }
        $fecha_adicional_ok = DateTime::createFromFormat('Y-m-d', $fecha_caducidad_adicional);
        if (!$fecha_adicional_ok) {
            echo json_encode(['success' => false, 'error' => 'Fecha de caducidad adicional inválida.']);
            exit;
        }
        $cantidad_recibida = $cantidad_lote_principal + $cantidad_lote_adicional;
    } else {
        $cantidad_lote_principal = $cantidad_recibida;
    }

    if ($cantidad_recibida > $cantidad_enviada) {
        echo json_encode(['success' => false, 'error' => "La cantidad recibida ({$cantidad_recibida}) no puede ser mayor a la enviada ({$cantidad_enviada})."]);
        exit;
    }

    $hay_diferencia = ($cantidad_recibida !== $cantidad_enviada);
    if ($hay_diferencia && $motivo_diferencia !== 'otro_lote' && empty($motivo_diferencia)) {
        echo json_encode(['success' => false, 'error' => 'Debe indicar el motivo de la diferencia en la cantidad.']);
        exit;
    }

    $cod_barra = $tyc['Cod_Barra'];

    $stmt = $conn->prepare("
        SELECT ID_Prod_POS FROM Stock_POS
        WHERE Cod_Barra = ? AND Fk_sucursal = ?
        LIMIT 1
    ");
    $stmt->bind_param('si', $cod_barra, $fk_sucursal);
    $stmt->execute();
    $sp = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$sp) {
        echo json_encode([
            'success' => false,
            'error' => 'El producto no existe en stock en la sucursal destino. Dé de alta el producto en esta sucursal antes de recibir el traspaso.'
        ]);
        exit;
    }

    $id_prod = (int) $sp['ID_Prod_POS'];

    $stmt = $conn->prepare("
        SELECT ID_Historial FROM Historial_Lotes
        WHERE ID_Prod_POS = ? AND Fk_sucursal = ? AND Lote = ?
        LIMIT 1
    ");
    $stmt->bind_param('iis', $id_prod, $fk_sucursal, $lote);
    $stmt->execute();
    if ($stmt->get_result()->fetch_assoc()) {
        $stmt->close();
        echo json_encode(['success' => false, 'error' => 'Ya existe un lote con ese número en esta sucursal.']);
        exit;
    }
    $stmt->close();

    if ($hay_diferencia && $motivo_diferencia === 'otro_lote' && !empty($lote_adicional)) {
        $stmt = $conn->prepare("
            SELECT ID_Historial FROM Historial_Lotes
            WHERE ID_Prod_POS = ? AND Fk_sucursal = ? AND Lote = ?
            LIMIT 1
        ");
        $stmt->bind_param('iis', $id_prod, $fk_sucursal, $lote_adicional);
        $stmt->execute();
        if ($stmt->get_result()->fetch_assoc()) {
            $stmt->close();
            echo json_encode(['success' => false, 'error' => 'Ya existe un lote adicional con ese número en esta sucursal.']);
            exit;
        }
        $stmt->close();
        if ($lote === $lote_adicional) {
            echo json_encode(['success' => false, 'error' => 'El lote adicional debe ser diferente al lote principal.']);
            exit;
        }
    }

    $conn->begin_transaction();

    if ($hay_diferencia) {
        $stmt = $conn->prepare("
            UPDATE TraspasosYNotasC
            SET Estatus = 'Recibido', Cantidad = ?
            WHERE TraspaNotID = ?
        ");
        $stmt->bind_param('ii', $cantidad_recibida, $id_traspaso);
        $stmt->execute();
        $stmt->close();
        $diferencia_stock = $cantidad_enviada - $cantidad_recibida;
        if ($diferencia_stock > 0) {
            $stmt = $conn->prepare("
                UPDATE Stock_POS SET Existencias_R = Existencias_R - ?
                WHERE ID_Prod_POS = ? AND Fk_sucursal = ?
            ");
            $stmt->bind_param('iii', $diferencia_stock, $id_prod, $fk_sucursal);
            $stmt->execute();
            $stmt->close();
        }
    } else {
        $stmt = $conn->prepare("
            UPDATE TraspasosYNotasC SET Estatus = 'Recibido' WHERE TraspaNotID = ?
        ");
        $stmt->bind_param('i', $id_traspaso);
        $stmt->execute();
        $stmt->close();
    }

    $stmt = $conn->prepare("
        INSERT INTO Historial_Lotes (ID_Prod_POS, Fk_sucursal, Lote, Fecha_Caducidad, Fecha_Ingreso, Existencias, Usuario_Modifico)
        VALUES (?, ?, ?, ?, CURDATE(), ?, ?)
    ");
    $stmt->bind_param('iissis', $id_prod, $fk_sucursal, $lote, $fecha_caducidad, $cantidad_lote_principal, $usuario);
    $stmt->execute();
    $stmt->close();

    if ($hay_diferencia && $motivo_diferencia === 'otro_lote' && !empty($lote_adicional)) {
        $stmt = $conn->prepare("
            INSERT INTO Historial_Lotes (ID_Prod_POS, Fk_sucursal, Lote, Fecha_Caducidad, Fecha_Ingreso, Existencias, Usuario_Modifico)
            VALUES (?, ?, ?, ?, CURDATE(), ?, ?)
        ");
        $stmt->bind_param('iissis', $id_prod, $fk_sucursal, $lote_adicional, $fecha_caducidad_adicional, $cantidad_lote_adicional, $usuario);
        $stmt->execute();
        $stmt->close();
    }

    $chk = $conn->query("SHOW COLUMNS FROM Stock_POS LIKE 'Control_Lotes_Caducidad'");
    if ($chk && $chk->num_rows > 0) {
        $stmt = $conn->prepare("
            UPDATE Stock_POS SET Control_Lotes_Caducidad = 1
            WHERE ID_Prod_POS = ? AND Fk_sucursal = ? AND (Control_Lotes_Caducidad IS NULL OR Control_Lotes_Caducidad = 0)
        ");
        $stmt->bind_param('ii', $id_prod, $fk_sucursal);
        $stmt->execute();
        $stmt->close();
    }

    $chk = $conn->query("SHOW COLUMNS FROM Gestion_Lotes_Movimientos LIKE 'Tipo_Movimiento'");
    if ($chk && $chk->num_rows > 0) {
        $obs = 'Recepción traspaso ID ' . $id_traspaso;
        if ($hay_diferencia) {
            $obs .= '. Cantidad enviada: ' . $cantidad_enviada . ', cantidad recibida: ' . $cantidad_recibida;
            if ($motivo_diferencia === 'otro_lote') {
                $obs .= '. Recibido en dos lotes: ' . $lote . ' (' . $cantidad_lote_principal . ') y ' . $lote_adicional . ' (' . $cantidad_lote_adicional . ')';
            } elseif ($motivo_diferencia === 'no_completo') $obs .= '. Motivo: No llegó completo';
            elseif ($motivo_diferencia === 'otra_razon') $obs .= '. Motivo: Otra razón';
        }
        if ($observaciones !== '') $obs .= '. ' . $observaciones;
        $stmt = $conn->prepare("
            INSERT INTO Gestion_Lotes_Movimientos (ID_Prod_POS, Cod_Barra, Fk_sucursal, Lote_Nuevo, Fecha_Caducidad_Nueva, Cantidad, Tipo_Movimiento, Usuario_Modifico, Observaciones)
            VALUES (?, ?, ?, ?, ?, ?, 'actualizacion', ?, ?)
        ");
        $stmt->bind_param('isississ', $id_prod, $cod_barra, $fk_sucursal, $lote, $fecha_caducidad, $cantidad_lote_principal, $usuario, $obs);
        $stmt->execute();
        $stmt->close();
        if ($hay_diferencia && $motivo_diferencia === 'otro_lote' && !empty($lote_adicional)) {
            $obs_adicional = 'Recepción traspaso ID ' . $id_traspaso . ' - Lote adicional. Cantidad: ' . $cantidad_lote_adicional;
            $stmt = $conn->prepare("
                INSERT INTO Gestion_Lotes_Movimientos (ID_Prod_POS, Cod_Barra, Fk_sucursal, Lote_Nuevo, Fecha_Caducidad_Nueva, Cantidad, Tipo_Movimiento, Usuario_Modifico, Observaciones)
                VALUES (?, ?, ?, ?, ?, ?, 'actualizacion', ?, ?)
            ");
            $stmt->bind_param('isississ', $id_prod, $cod_barra, $fk_sucursal, $lote_adicional, $fecha_caducidad_adicional, $cantidad_lote_adicional, $usuario, $obs_adicional);
            $stmt->execute();
            $stmt->close();
        }
    }

    $conn->commit();
    $mensaje = 'Traspaso recibido. Lote y fecha de caducidad registrados correctamente.';
    if ($hay_diferencia && $motivo_diferencia === 'otro_lote') {
        $mensaje = 'Traspaso recibido. Se registraron dos lotes correctamente.';
    }
    echo json_encode(['success' => true, 'message' => $mensaje]);
} catch (Exception $e) {
    if ($conn && method_exists($conn, 'rollback')) {
        @$conn->rollback();
    }
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
