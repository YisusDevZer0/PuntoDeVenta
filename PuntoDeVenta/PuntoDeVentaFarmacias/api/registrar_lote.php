<?php
/**
 * Registrar Lote (Farmacias).
 * Usa Historial_Lotes + Stock_POS. Solo permite altas si hay stock sin cubrir
 * por lote/caducidad. Cuando todo el stock tiene lote/caducidad, no se permiten más altas.
 */
header('Content-Type: application/json');
include_once __DIR__ . '/../dbconect.php';

$cod_barra   = isset($_POST['codigoBarra']) ? trim($_POST['codigoBarra']) : '';
$lote        = isset($_POST['lote']) ? trim($_POST['lote']) : '';
$fecha_cad   = isset($_POST['fechaCaducidad']) ? trim($_POST['fechaCaducidad']) : '';
$cantidad    = isset($_POST['cantidad']) ? (int) $_POST['cantidad'] : 0;
$sucursal_id = isset($_POST['sucursal']) ? (int) $_POST['sucursal'] : 0;
$observaciones = isset($_POST['observaciones']) ? trim($_POST['observaciones']) : '';

if ($cod_barra === '' || $lote === '' || $fecha_cad === '' || $cantidad <= 0 || $sucursal_id <= 0) {
    echo json_encode(['success' => false, 'error' => 'Faltan datos requeridos (código, sucursal, lote, fecha caducidad, cantidad).']);
    exit;
}

$usuario = 'Farmacia';

try {
    $stmt = $con->prepare("
        SELECT ID_Prod_POS, Fk_sucursal, Cod_Barra, Nombre_Prod
        FROM Stock_POS
        WHERE Cod_Barra = ? AND Fk_sucursal = ?
        LIMIT 1
    ");
    $stmt->bind_param("si", $cod_barra, $sucursal_id);
    $stmt->execute();
    $res = $stmt->get_result();
    $sp  = $res->fetch_assoc();
    $stmt->close();

    if (!$sp) {
        echo json_encode(['success' => false, 'error' => 'Producto no encontrado en esta sucursal.']);
        exit;
    }

    $id_prod = $sp['ID_Prod_POS'];
    $fk_suc  = (int) $sp['Fk_sucursal'];

    $stmt = $con->prepare("
        SELECT Existencias_R FROM Stock_POS
        WHERE ID_Prod_POS = ? AND Fk_sucursal = ?
        LIMIT 1
    ");
    $stmt->bind_param("ii", $id_prod, $fk_suc);
    $stmt->execute();
    $r = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    $existencia_total = (int) ($r['Existencias_R'] ?? 0);

    $stmt = $con->prepare("
        SELECT COALESCE(SUM(Existencias), 0) AS en_lotes
        FROM Historial_Lotes
        WHERE ID_Prod_POS = ? AND Fk_sucursal = ? AND Existencias > 0
    ");
    $stmt->bind_param("ii", $id_prod, $fk_suc);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    $en_lotes = (int) ($row['en_lotes'] ?? 0);
    $sin_cubrir = $existencia_total - $en_lotes;

    if ($sin_cubrir <= 0) {
        echo json_encode([
            'success' => false,
            'error'   => 'Todo el stock de este producto ya tiene lote y fecha de caducidad. No se permiten más altas.',
        ]);
        exit;
    }

    if ($cantidad > $sin_cubrir) {
        echo json_encode([
            'success' => false,
            'error'   => "Solo hay {$sin_cubrir} unidad(es) sin cubrir. La cantidad no puede ser mayor.",
        ]);
        exit;
    }

    $stmt = $con->prepare("
        SELECT ID_Historial FROM Historial_Lotes
        WHERE ID_Prod_POS = ? AND Fk_sucursal = ? AND Lote = ?
        LIMIT 1
    ");
    $stmt->bind_param("iis", $id_prod, $fk_suc, $lote);
    $stmt->execute();
    $existe = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    if ($existe) {
        echo json_encode(['success' => false, 'error' => 'Ya existe un lote con ese número en esta sucursal.']);
        exit;
    }

    $con->begin_transaction();

    $stmt = $con->prepare("
        INSERT INTO Historial_Lotes (
            ID_Prod_POS, Fk_sucursal, Lote, Fecha_Caducidad, Fecha_Ingreso,
            Existencias, Usuario_Modifico
        ) VALUES (?, ?, ?, ?, CURDATE(), ?, ?)
    ");
    $stmt->bind_param("iissis", $id_prod, $fk_suc, $lote, $fecha_cad, $cantidad, $usuario);
    $stmt->execute();
    $stmt->close();

    $chk = $con->query("SHOW COLUMNS FROM Stock_POS LIKE 'Control_Lotes_Caducidad'");
    if ($chk && $chk->num_rows > 0) {
        $up = $con->prepare("
            UPDATE Stock_POS
            SET Control_Lotes_Caducidad = 1
            WHERE ID_Prod_POS = ? AND Fk_sucursal = ?
              AND (Control_Lotes_Caducidad IS NULL OR Control_Lotes_Caducidad = 0)
        ");
        $up->bind_param("ii", $id_prod, $fk_suc);
        $up->execute();
        $up->close();
    }

    $mov = $con->prepare("
        INSERT INTO Gestion_Lotes_Movimientos (
            ID_Prod_POS, Cod_Barra, Fk_sucursal, Lote_Nuevo,
            Fecha_Caducidad_Nueva, Cantidad, Tipo_Movimiento, Usuario_Modifico, Observaciones
        ) VALUES (?, ?, ?, ?, ?, ?, 'actualizacion', ?, ?)
    ");
    $mov->bind_param("isississ", $id_prod, $cod_barra, $fk_suc, $lote, $fecha_cad, $cantidad, $usuario, $observaciones);
    $mov->execute();
    $mov->close();

    $con->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Lote registrado correctamente.',
    ]);
} catch (Exception $e) {
    if (isset($con) && $con) {
        $con->rollback();
    }
    echo json_encode([
        'success' => false,
        'error'   => 'Error al registrar: ' . $e->getMessage(),
    ]);
}
