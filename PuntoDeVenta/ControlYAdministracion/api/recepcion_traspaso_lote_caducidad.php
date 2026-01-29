<?php
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
$fecha_caducidad = isset($_POST['fecha_caducidad']) ? trim($_POST['fecha_caducidad']) : '';
$observaciones = isset($_POST['observaciones']) ? trim($_POST['observaciones']) : '';

if ($id_traspaso <= 0 || $fk_sucursal <= 0 || $cantidad_recibida < 1 || $lote === '' || $fecha_caducidad === '') {
    echo json_encode(['success' => false, 'error' => 'Faltan datos requeridos (traspaso, sucursal, cantidad, lote, fecha caducidad).']);
    exit;
}

$fecha_ok = DateTime::createFromFormat('Y-m-d', $fecha_caducidad);
if (!$fecha_ok) {
    echo json_encode(['success' => false, 'error' => 'Fecha de caducidad inválida.']);
    exit;
}

try {
    $stmt = $conn->prepare("
        SELECT ID_Traspaso_Generado, Cod_Barra, Nombre_Prod, Cantidad_Enviada, Fk_SucDestino
        FROM Traspasos_generados
        WHERE ID_Traspaso_Generado = ? AND Estatus = 'Generado'
    ");
    $stmt->bind_param('i', $id_traspaso);
    $stmt->execute();
    $res = $stmt->get_result();
    $tg = $res->fetch_assoc();
    $stmt->close();

    if (!$tg) {
        echo json_encode(['success' => false, 'error' => 'Traspaso no encontrado o ya fue recibido.']);
        exit;
    }

    if ((int) $tg['Fk_SucDestino'] !== $fk_sucursal) {
        echo json_encode(['success' => false, 'error' => 'La sucursal no coincide con el traspaso.']);
        exit;
    }

    $sucursal_usuario = isset($row['Fk_Sucursal']) ? (int) $row['Fk_Sucursal'] : 0;
    if ($sucursal_usuario > 0 && $fk_sucursal !== $sucursal_usuario) {
        echo json_encode(['success' => false, 'error' => 'Solo puede recibir traspasos destinados a su sucursal.']);
        exit;
    }

    $cantidad_enviada = (int) $tg['Cantidad_Enviada'];
    if ($cantidad_recibida > $cantidad_enviada) {
        echo json_encode(['success' => false, 'error' => "La cantidad recibida no puede ser mayor a la enviada ({$cantidad_enviada})."]);
        exit;
    }

    $cod_barra = $tg['Cod_Barra'];

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

    $conn->begin_transaction();

    $stmt = $conn->prepare("
        UPDATE Traspasos_generados
        SET Estatus = 'Entregado',
            TraspasoRecibidoPor = ?,
            Fecha_recepcion = NOW()
        WHERE ID_Traspaso_Generado = ?
    ");
    $stmt->bind_param('si', $usuario, $id_traspaso);
    $stmt->execute();
    $stmt->close();

    $stmt = $conn->prepare("
        UPDATE Stock_POS
        SET Existencias_R = Existencias_R + ?,
            JustificacionAjuste = 'Recepción traspaso'
        WHERE Cod_Barra = ? AND Fk_sucursal = ?
    ");
    $stmt->bind_param('isi', $cantidad_recibida, $cod_barra, $fk_sucursal);
    $stmt->execute();
    $stmt->close();

    $stmt = $conn->prepare("
        INSERT INTO Historial_Lotes (ID_Prod_POS, Fk_sucursal, Lote, Fecha_Caducidad, Fecha_Ingreso, Existencias, Usuario_Modifico)
        VALUES (?, ?, ?, ?, CURDATE(), ?, ?)
    ");
    $stmt->bind_param('iissis', $id_prod, $fk_sucursal, $lote, $fecha_caducidad, $cantidad_recibida, $usuario);
    $stmt->execute();
    $stmt->close();

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
        if ($observaciones !== '') {
            $obs .= '. ' . $observaciones;
        }
        $stmt = $conn->prepare("
            INSERT INTO Gestion_Lotes_Movimientos (ID_Prod_POS, Cod_Barra, Fk_sucursal, Lote_Nuevo, Fecha_Caducidad_Nueva, Cantidad, Tipo_Movimiento, Usuario_Modifico, Observaciones)
            VALUES (?, ?, ?, ?, ?, ?, 'actualizacion', ?, ?)
        ");
        $stmt->bind_param('isississ', $id_prod, $cod_barra, $fk_sucursal, $lote, $fecha_caducidad, $cantidad_recibida, $usuario, $obs);
        $stmt->execute();
        $stmt->close();
    }

    $conn->commit();
    echo json_encode(['success' => true, 'message' => 'Traspaso recibido. Stock actualizado y lote registrado.']);
} catch (Exception $e) {
    if ($conn && method_exists($conn, 'rollback')) {
        @$conn->rollback();
    }
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
