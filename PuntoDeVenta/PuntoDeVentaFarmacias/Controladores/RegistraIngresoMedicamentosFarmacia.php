<?php
include_once 'db_connect.php';

if (!$conn) {
    echo json_encode(['status' => 'error', 'message' => 'Error de conexión: ' . mysqli_connect_error()]);
    exit();
}

$contador = isset($_POST["IdBasedatos"]) ? count($_POST["IdBasedatos"]) : 0;
$rows_to_insert = [];
$response = ['status' => 'error', 'message' => ''];

for ($i = 0; $i < $contador; $i++) {
    if (empty($_POST["IdBasedatos"][$i]) && empty($_POST["CodBarras"][$i]) && empty($_POST["NombreDelProducto"][$i])) {
        continue;
    }

    $lote = isset($_POST["Lote"][$i]) ? trim((string) $_POST["Lote"][$i]) : '';
    $fecha_cad = isset($_POST["FechaCaducidad"][$i]) ? trim((string) $_POST["FechaCaducidad"][$i]) : '';

    // Evitar guardar "NaN" o vacío en Lote (para que Stock_POS e Historial_Lotes queden bien)
    if (strtolower($lote) === 'nan' || $lote === '') {
        $lote = 'S/L';
    }
    if (strtolower($fecha_cad) === 'nan' || $fecha_cad === '' || $fecha_cad === '0000-00-00') {
        $fecha_cad = date('Y-m-d');
    }

    $rows_to_insert[] = [
        'ID_Prod_POS'      => $_POST["IdBasedatos"][$i],
        'NumFactura'       => $_POST["FacturaNumber"][$i] ?? '',
        'Proveedor'        => $_POST["Proveedor"][$i] ?? '',
        'Cod_Barra'        => $_POST["CodBarras"][$i] ?? '',
        'Nombre_Prod'      => $_POST["NombreDelProducto"][$i] ?? '',
        'Fk_Sucursal'      => $_POST["FkSucursal"][$i] ?? '',
        'Contabilizado'    => (int) ($_POST["Contabilizado"][$i] ?? 0),
        'Fecha_Caducidad'  => $fecha_cad,
        'Lote'             => $lote,
        'PrecioMaximo'     => $_POST["PrecioMaximo"][$i] ?? 0,
        'Precio_Venta'     => $_POST["PrecioVenta"][$i] ?? 0,
        'Precio_C'        => $_POST["PrecioCompra"][$i] ?? 0,
        'AgregadoPor'      => $_POST["AgregoElVendedor"][$i] ?? '',
        'FechaInventario'  => $_POST["FechaDeInventario"][$i] ?? date('Y-m-d'),
        'Estatus'          => $_POST["Estatusdesolicitud"][$i] ?? 'Pendiente',
        'NumOrden'         => $_POST["NumberOrden"][$i] ?? 0,
    ];
}

if (count($rows_to_insert) === 0) {
    echo json_encode(['status' => 'error', 'message' => 'No hay registros válidos para guardar.']);
    mysqli_close($conn);
    exit();
}

$placeholders = [];
$values = [];
$types = '';

foreach ($rows_to_insert as $r) {
    $placeholders[] = "(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?, ?, ?)";
    $values[] = $r['ID_Prod_POS'];
    $values[] = $r['NumFactura'];
    $values[] = $r['Proveedor'];
    $values[] = $r['Cod_Barra'];
    $values[] = $r['Nombre_Prod'];
    $values[] = $r['Fk_Sucursal'];
    $values[] = $r['Contabilizado'];
    $values[] = $r['Fecha_Caducidad'];
    $values[] = $r['Lote'];
    $values[] = $r['PrecioMaximo'];
    $values[] = $r['Precio_Venta'];
    $values[] = $r['Precio_C'];
    $values[] = $r['AgregadoPor'];
    $values[] = $r['FechaInventario'];
    $values[] = $r['Estatus'];
    $values[] = $r['NumOrden'];
    $types .= 'sssssisddsssssii';
}

$query = "INSERT INTO IngresosFarmacias (ID_Prod_POS, NumFactura, Proveedor, Cod_Barra, Nombre_Prod, Fk_Sucursal, Contabilizado, Fecha_Caducidad, Lote, PrecioMaximo, Precio_Venta, Precio_C, AgregadoPor, AgregadoEl, FechaInventario, Estatus, NumOrden) VALUES " . implode(', ', $placeholders);

$stmt = mysqli_prepare($conn, $query);
if (!$stmt) {
    echo json_encode(['status' => 'error', 'message' => 'Error al preparar: ' . mysqli_error($conn)]);
    mysqli_close($conn);
    exit();
}

mysqli_stmt_bind_param($stmt, $types, ...$values);
$ok = mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

if (!$ok) {
    echo json_encode(['status' => 'error', 'message' => 'Error al insertar ingresos: ' . mysqli_error($conn)]);
    mysqli_close($conn);
    exit();
}

$historial_errores = [];
// Normalizar fecha a Y-m-d (acepta Y-m-d o d/m/Y)
$normalizar_fecha = function ($f) {
    if ($f === '' || $f === '0000-00-00') return '';
    $d = DateTime::createFromFormat('Y-m-d', $f);
    if (!$d) $d = DateTime::createFromFormat('d/m/Y', $f);
    return $d ? $d->format('Y-m-d') : $f;
};

// Actualizar Stock_POS e Historial_Lotes por cada fila desde PHP (así siempre queda registro y lote).
// Si en la BD tienes el trigger actualizar_existencias, quítalo (DROP TRIGGER actualizar_existencias)
// para no sumar existencias dos veces.
$lote_valido_list = ['nan', 'null', 'n/a', 'na', 'sin lote', 's/l', ''];

foreach ($rows_to_insert as $r) {
    $cod_barra = $r['Cod_Barra'];
    $fk_sucursal = (int) $r['Fk_Sucursal'];
    $id_prod = (int) $r['ID_Prod_POS'];
    $lote = $r['Lote'];
    $fecha_cad = $normalizar_fecha($r['Fecha_Caducidad']);
    $fecha_ingreso = $normalizar_fecha($r['FechaInventario'] ?: date('Y-m-d'));
    if ($fecha_ingreso === '') $fecha_ingreso = date('Y-m-d');
    $cant = (int) $r['Contabilizado'];
    $usuario = $r['AgregadoPor'];

    if ($cant <= 0) {
        continue;
    }

    // 1) Actualizar Stock_POS (sumar existencias, lote, caducidad, fecha ingreso)
    $up_stock = $conn->prepare("
        UPDATE Stock_POS
        SET Existencias_R = Existencias_R + ?,
            Lote = ?,
            Fecha_Caducidad = ?,
            Fecha_Ingreso = ?,
            ActualizadoPor = ?
        WHERE Cod_Barra = ? AND Fk_sucursal = ?
    ");
    if ($up_stock) {
        $up_stock->bind_param("isssssi", $cant, $lote, $fecha_cad, $fecha_ingreso, $usuario, $cod_barra, $fk_sucursal);
        $up_stock->execute();
        $up_stock->close();
    }

    // 2) Escribir en Historial_Lotes solo si lote y fecha son válidos (no S/L, no vacíos)
    $lote_ok = $lote !== '' && !in_array(strtolower(trim($lote)), $lote_valido_list, true);
    $fecha_ok = $fecha_cad !== '' && $fecha_cad !== '0000-00-00' && $fecha_cad > '1900-01-01';

    if ($lote_ok && $fecha_ok) {
        $num = 0;
        $existe = $conn->prepare("
            SELECT COUNT(*) FROM Historial_Lotes
            WHERE ID_Prod_POS = ? AND Lote = ? AND Fk_sucursal = ?
        ");
        if ($existe) {
            $existe->bind_param("isi", $id_prod, $lote, $fk_sucursal);
            $existe->execute();
            $existe->bind_result($num);
            $existe->fetch();
            $existe->close();
        }

        if ($num > 0) {
            $up_hl = $conn->prepare("
                UPDATE Historial_Lotes
                SET Existencias = Existencias + ?,
                    Fecha_Ingreso = ?,
                    Usuario_Modifico = ?
                WHERE ID_Prod_POS = ? AND Lote = ? AND Fk_sucursal = ?
            ");
            if ($up_hl) {
                $up_hl->bind_param("issisi", $cant, $fecha_ingreso, $usuario, $id_prod, $lote, $fk_sucursal);
                $up_hl->execute();
                if ($up_hl->error) $historial_errores[] = 'UPDATE Historial_Lotes: ' . $up_hl->error;
                $up_hl->close();
            }
        } else {
            $ins_hl = $conn->prepare("
                INSERT INTO Historial_Lotes (ID_Prod_POS, Fk_sucursal, Lote, Fecha_Caducidad, Fecha_Ingreso, Existencias, Usuario_Modifico)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            if ($ins_hl) {
                $ins_hl->bind_param("iisssis", $id_prod, $fk_sucursal, $lote, $fecha_cad, $fecha_ingreso, $cant, $usuario);
                $ins_hl->execute();
                if ($ins_hl->error) $historial_errores[] = 'INSERT Historial_Lotes: ' . $ins_hl->error;
                $ins_hl->close();
            } else {
                $historial_errores[] = 'prepare INSERT Historial_Lotes: ' . $conn->error;
            }
        }
    }
}

$response = ['status' => 'success', 'message' => 'Registro(s) guardado(s). Stock y lotes actualizados.'];
if (!empty($historial_errores)) {
    $response['historial_errores'] = $historial_errores;
    $response['message'] .= ' (Algunos lotes no se guardaron en historial: ' . implode('; ', $historial_errores) . ')';
}

echo json_encode($response);
mysqli_close($conn);
