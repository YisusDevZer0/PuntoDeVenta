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

    // Lote: siempre como texto; leer explícitamente del índice $i (no confundir con cantidad)
    $lote = isset($_POST["Lote"][$i]) ? trim((string) $_POST["Lote"][$i]) : '';
    $contabilizado = (int) ($_POST["Contabilizado"][$i] ?? 0);
    $fecha_cad = isset($_POST["FechaCaducidad"][$i]) ? trim((string) $_POST["FechaCaducidad"][$i]) : '';

    // No guardar "0", "NaN" o vacío como lote real (usar S/L para que no se confunda con cantidad)
    if ($lote === '' || strtolower($lote) === 'nan' || $lote === '0') {
        $lote = 'S/L';
    }
    // Si Lote es solo dígitos y coincide con la cantidad, es probable desorden de arrays
    if (strlen($lote) <= 4 && ctype_digit($lote) && (int) $lote === $contabilizado) {
        $lote = 'S/L';
    }
    if (strtolower($fecha_cad) === 'nan' || $fecha_cad === '' || $fecha_cad === '0000-00-00') {
        $fecha_cad = date('Y-m-d');
    } else {
        // Normalizar a Y-m-d para que el trigger reciba fecha válida (acepta Y-m-d o d/m/Y)
        $d = DateTime::createFromFormat('Y-m-d', $fecha_cad);
        if (!$d) $d = DateTime::createFromFormat('d/m/Y', $fecha_cad);
        if ($d) $fecha_cad = $d->format('Y-m-d');
    }

    $rows_to_insert[] = [
        'ID_Prod_POS'      => $_POST["IdBasedatos"][$i],
        'NumFactura'       => $_POST["FacturaNumber"][$i] ?? '',
        'Proveedor'        => $_POST["Proveedor"][$i] ?? '',
        'Cod_Barra'        => $_POST["CodBarras"][$i] ?? '',
        'Nombre_Prod'      => $_POST["NombreDelProducto"][$i] ?? '',
        'Fk_Sucursal'      => $_POST["FkSucursal"][$i] ?? '',
        'Contabilizado'    => $contabilizado,
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
    $types .= 'sssssisddsssssi';
}

$query = "INSERT INTO IngresosFarmacias (ID_Prod_POS, NumFactura, Proveedor, Cod_Barra, Nombre_Prod, Fk_Sucursal, Contabilizado, Fecha_Caducidad, Lote, PrecioMaximo, Precio_Venta, Precio_C, AgregadoPor, AgregadoEl, FechaInventario, Estatus, NumOrden) VALUES " . implode(', ', $placeholders);

$stmt = mysqli_prepare($conn, $query);
if (!$stmt) {
    echo json_encode(['status' => 'error', 'message' => 'Error al preparar: ' . mysqli_error($conn)]);
    mysqli_close($conn);
    exit();
}

// Transacción: si el trigger falla (Stock_POS o Historial_Lotes), todo hace rollback
mysqli_begin_transaction($conn);

mysqli_stmt_bind_param($stmt, $types, ...$values);
$ok = mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

if (!$ok) {
    mysqli_rollback($conn);
    echo json_encode(['status' => 'error', 'message' => 'Error al insertar ingresos: ' . mysqli_error($conn)]);
    mysqli_close($conn);
    exit();
}

mysqli_commit($conn);

echo json_encode(['status' => 'success', 'message' => 'Registro(s) guardado(s). Stock y lotes actualizados por el trigger.']);
mysqli_close($conn);
