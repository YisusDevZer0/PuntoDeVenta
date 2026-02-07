<?php
/**
 * Registrar o actualizar lote (Farmacias).
 * - id_historial > 0: actualizar lote existente.
 * - id_historial = 0: alta nueva, solo si hay stock sin cubrir (restricción).
 */
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
include_once __DIR__ . '/../dbconect.php';

$id_historial = isset($_POST['id_historial']) ? (int)$_POST['id_historial'] : 0;
$cod_barra = isset($_POST['cod_barra']) ? trim($_POST['cod_barra']) : '';
$lote_nuevo = isset($_POST['lote_nuevo']) ? trim($_POST['lote_nuevo']) : '';
$fecha_cad = isset($_POST['fecha_caducidad_nueva']) ? trim($_POST['fecha_caducidad_nueva']) : '';
$cantidad = isset($_POST['cantidad']) ? (int)$_POST['cantidad'] : 0;
$observaciones = isset($_POST['observaciones']) ? trim($_POST['observaciones']) : '';
$tipo_accion = isset($_POST['tipo_accion']) ? trim($_POST['tipo_accion']) : 'editar';
$usuario = 'Farmacia';

if (empty($cod_barra) || empty($lote_nuevo) || empty($fecha_cad)) {
    echo json_encode(['success' => false, 'error' => 'Faltan datos requeridos.']);
    exit;
}
$fecha_ok = DateTime::createFromFormat('Y-m-d', $fecha_cad);
if (!$fecha_ok) {
    echo json_encode(['success' => false, 'error' => 'Fecha de caducidad inválida.']);
    exit;
}

try {
    if (function_exists('mysqli_begin_transaction')) {
        mysqli_begin_transaction($con);
    } else {
        $con->query("START TRANSACTION");
    }

    if ($id_historial > 0) {
        $stmt = $con->prepare("SELECT * FROM Historial_Lotes WHERE ID_Historial = ?");
        $stmt->bind_param("i", $id_historial);
        $stmt->execute();
        $hist = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        if (!$hist) {
            throw new Exception('Lote no encontrado.');
        }

        if ($tipo_accion === 'ingreso') {
            // Registrar ingreso nuevo: agregar unidades al lote existente (sin cambiar lote ni fecha)
            if ($cantidad <= 0) {
                throw new Exception('Indique la cantidad de unidades a agregar.');
            }
            $cantidad_nueva = (int)$hist['Existencias'] + $cantidad;

            $ins = $con->prepare("INSERT INTO Gestion_Lotes_Movimientos (ID_Prod_POS, Cod_Barra, Fk_sucursal, Lote_Nuevo, Fecha_Caducidad_Nueva, Cantidad, Tipo_Movimiento, Usuario_Modifico, Observaciones) VALUES (?,?,?,?,?,?,'ingreso',?,?)");
            $ins->bind_param("isississ", $hist['ID_Prod_POS'], $cod_barra ?: $hist['Lote'], $hist['Fk_sucursal'], $hist['Lote'], $hist['Fecha_Caducidad'], $cantidad, $usuario, $observaciones);
            $ins->execute();
            $ins->close();

            $up = $con->prepare("UPDATE Historial_Lotes SET Existencias=?, Usuario_Modifico=?, Fecha_Registro=NOW() WHERE ID_Historial=?");
            $up->bind_param("isi", $cantidad_nueva, $usuario, $id_historial);
            $up->execute();
            $up->close();

            $up2 = $con->prepare("UPDATE Stock_POS SET Existencias_R = Existencias_R + ? WHERE ID_Prod_POS=? AND Fk_sucursal=?");
            $up2->bind_param("iii", $cantidad, $hist['ID_Prod_POS'], $hist['Fk_sucursal']);
            $up2->execute();
            $up2->close();

            if (function_exists('mysqli_commit')) {
                mysqli_commit($con);
            } else {
                $con->query("COMMIT");
            }
            echo json_encode(['success' => true, 'message' => 'Ingreso de ' . $cantidad . ' unidades registrado correctamente al lote.']);
            exit;
        }

        // Editar datos del lote (comportamiento original)
        $cod_barra_hl = $cod_barra;
        $cant_mov = $cantidad > 0 ? $cantidad : $hist['Existencias'];

        $ins = $con->prepare("INSERT INTO Gestion_Lotes_Movimientos (ID_Prod_POS, Cod_Barra, Fk_sucursal, Lote_Anterior, Lote_Nuevo, Fecha_Caducidad_Anterior, Fecha_Caducidad_Nueva, Cantidad, Tipo_Movimiento, Usuario_Modifico, Observaciones) VALUES (?,?,?,?,?,?,?,?,'actualizacion',?,?)");
        $ins->bind_param("isissssiss",
            $hist['ID_Prod_POS'], $cod_barra_hl, $hist['Fk_sucursal'],
            $hist['Lote'], $lote_nuevo, $hist['Fecha_Caducidad'], $fecha_cad,
            $cant_mov, $usuario, $observaciones);
        $ins->execute();
        $ins->close();

        $cant_final = $cantidad > 0 ? $cantidad : $hist['Existencias'];
        $up = $con->prepare("UPDATE Historial_Lotes SET Lote=?, Fecha_Caducidad=?, Existencias=?, Usuario_Modifico=?, Fecha_Registro=NOW() WHERE ID_Historial=?");
        $up->bind_param("ssisi", $lote_nuevo, $fecha_cad, $cant_final, $usuario, $id_historial);
        $up->execute();
        $up->close();

        $up2 = $con->prepare("UPDATE Stock_POS SET Lote=?, Fecha_Caducidad=? WHERE ID_Prod_POS=? AND Fk_sucursal=? AND Lote=?");
        $up2->bind_param("ssiis", $lote_nuevo, $fecha_cad, $hist['ID_Prod_POS'], $hist['Fk_sucursal'], $hist['Lote']);
        $up2->execute();
        $up2->close();

        if (function_exists('mysqli_commit')) {
            mysqli_commit($con);
        } else {
            $con->query("COMMIT");
        }
        echo json_encode(['success' => true, 'message' => 'Lote actualizado correctamente.']);
        exit;
    }

    $fk_suc = (int)($_POST['fk_sucursal'] ?? 0);
    if ($fk_suc <= 0) {
        throw new Exception('Sucursal requerida para alta de lote.');
    }

    $stmt = $con->prepare("SELECT ID_Prod_POS, Fk_sucursal FROM Stock_POS WHERE Cod_Barra = ? AND Fk_sucursal = ? LIMIT 1");
    $stmt->bind_param("si", $cod_barra, $fk_suc);
    $stmt->execute();
    $sp = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    if (!$sp) {
        throw new Exception('Producto no encontrado en esta sucursal.');
    }
    $id_prod = $sp['ID_Prod_POS'];

    $stmt = $con->prepare("SELECT COALESCE(SUM(Existencias),0) AS en_lotes FROM Historial_Lotes WHERE ID_Prod_POS=? AND Fk_sucursal=? AND Existencias>0");
    $stmt->bind_param("ii", $id_prod, $fk_suc);
    $stmt->execute();
    $ro = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    $en_lotes = (int)($ro['en_lotes'] ?? 0);

    $stmt = $con->prepare("SELECT Existencias_R FROM Stock_POS WHERE ID_Prod_POS=? AND Fk_sucursal=? LIMIT 1");
    $stmt->bind_param("ii", $id_prod, $fk_suc);
    $stmt->execute();
    $rx = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    $existencia_total = (int)($rx['Existencias_R'] ?? 0);
    $sin_cubrir = $existencia_total - $en_lotes;

    if ($sin_cubrir <= 0) {
        throw new Exception('Todo el stock tiene lote y caducidad. No se permiten más altas.');
    }
    if ($cantidad <= 0 || $cantidad > $sin_cubrir) {
        throw new Exception("Cantidad inválida. Máximo sin cubrir: {$sin_cubrir}.");
    }

    $stmt = $con->prepare("SELECT ID_Historial FROM Historial_Lotes WHERE ID_Prod_POS=? AND Fk_sucursal=? AND Lote=? LIMIT 1");
    $stmt->bind_param("iis", $id_prod, $fk_suc, $lote_nuevo);
    $stmt->execute();
    if ($stmt->get_result()->fetch_assoc()) {
        $stmt->close();
        throw new Exception('Ya existe un lote con ese número en esta sucursal.');
    }
    $stmt->close();

    $stmt = $con->prepare("INSERT INTO Historial_Lotes (ID_Prod_POS, Fk_sucursal, Lote, Fecha_Caducidad, Fecha_Ingreso, Existencias, Usuario_Modifico) VALUES (?,?,?,?,CURDATE(),?,?)");
    $stmt->bind_param("iissis", $id_prod, $fk_suc, $lote_nuevo, $fecha_cad, $cantidad, $usuario);
    $stmt->execute();
    $stmt->close();

    $chk = $con->query("SHOW COLUMNS FROM Stock_POS LIKE 'Control_Lotes_Caducidad'");
    if ($chk && $chk->num_rows > 0) {
        $up = $con->prepare("UPDATE Stock_POS SET Control_Lotes_Caducidad=1 WHERE ID_Prod_POS=? AND Fk_sucursal=? AND (Control_Lotes_Caducidad IS NULL OR Control_Lotes_Caducidad=0)");
        $up->bind_param("ii", $id_prod, $fk_suc);
        $up->execute();
        $up->close();
    }

    $mov = $con->prepare("INSERT INTO Gestion_Lotes_Movimientos (ID_Prod_POS, Cod_Barra, Fk_sucursal, Lote_Nuevo, Fecha_Caducidad_Nueva, Cantidad, Tipo_Movimiento, Usuario_Modifico, Observaciones) VALUES (?,?,?,?,?,?,'actualizacion',?,?)");
    $mov->bind_param("isississ", $id_prod, $cod_barra, $fk_suc, $lote_nuevo, $fecha_cad, $cantidad, $usuario, $observaciones);
    $mov->execute();
    $mov->close();

    if (function_exists('mysqli_commit')) {
        mysqli_commit($con);
    } else {
        $con->query("COMMIT");
    }
    echo json_encode(['success' => true, 'message' => 'Lote registrado correctamente.']);
} catch (Exception $e) {
    if (function_exists('mysqli_rollback')) {
        @mysqli_rollback($con);
    } else {
        @$con->query("ROLLBACK");
    }
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
