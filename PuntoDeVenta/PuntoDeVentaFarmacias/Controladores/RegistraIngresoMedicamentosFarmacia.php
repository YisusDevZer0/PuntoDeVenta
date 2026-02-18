<?php
// Capturar errores fatales para devolver JSON en lugar de 500
header('Content-Type: application/json; charset=utf-8');
try {
    include_once 'db_connect.php';
} catch (Throwable $e) {
    echo json_encode(['status' => 'error', 'message' => 'Error al cargar: ' . $e->getMessage()]);
    exit;
}

if (!$conn) {
    echo json_encode(['status' => 'error', 'message' => 'Error de conexión: ' . mysqli_connect_error()]);
    exit();
}

// Normalizar POST: cuando hay 1 solo elemento, algunos entornos envían escalar en vez de array.
// Si indexamos un string $str[0] da el primer carácter (ej. "DevZero"[0]="D") → corrupción.
$normalizar = function ($key, $i) {
    $v = $_POST[$key] ?? null;
    if ($v === null) return '';
    if (is_array($v)) return $v[$i] ?? '';
    return (string) $v; // mismo valor para todas las filas
};
$idBasedatos = $_POST["IdBasedatos"] ?? [];
if (!is_array($idBasedatos)) {
    $_POST["IdBasedatos"] = [$idBasedatos];
}
$contador = count($_POST["IdBasedatos"]);
$rows_to_insert = [];
$response = ['status' => 'error', 'message' => ''];

for ($i = 0; $i < $contador; $i++) {
    if (empty($normalizar("IdBasedatos", $i)) && empty($normalizar("CodBarras", $i)) && empty($normalizar("NombreDelProducto", $i))) {
        continue;
    }

    // Usar normalizar() para evitar $str[$i] cuando el campo llega como string (da 1er carácter)
    $lote = trim((string) $normalizar("Lote", $i));
    $contabilizado = (int) ($normalizar("Contabilizado", $i) ?: 0);
    $fecha_cad = trim((string) $normalizar("FechaCaducidad", $i));

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
        'ID_Prod_POS'      => $normalizar("IdBasedatos", $i),
        'NumFactura'       => $normalizar("FacturaNumber", $i),
        'Proveedor'        => $normalizar("Proveedor", $i),
        'Cod_Barra'        => $normalizar("CodBarras", $i),
        'Nombre_Prod'      => $normalizar("NombreDelProducto", $i),
        'Fk_Sucursal'      => $normalizar("FkSucursal", $i),
        'Contabilizado'    => $contabilizado,
        'Fecha_Caducidad'  => $fecha_cad,
        'Lote'             => $lote,
        'PrecioMaximo'     => $normalizar("PrecioMaximo", $i) ?: 0,
        'Precio_Venta'     => $normalizar("PrecioVenta", $i) ?: 0,
        'Precio_C'         => $normalizar("PrecioCompra", $i) ?: 0,
        'AgregadoPor'      => $normalizar("AgregoElVendedor", $i) ?: '',
        'FechaInventario'  => $normalizar("FechaDeInventario", $i) ?: date('Y-m-d'),
        'Estatus'          => $normalizar("Estatusdesolicitud", $i) ?: 'Pendiente',
        'NumOrden'         => $normalizar("NumberOrden", $i) ?: 0,
    ];
}

if (count($rows_to_insert) === 0) {
    echo json_encode(['status' => 'error', 'message' => 'No hay registros válidos para guardar.']);
    mysqli_close($conn);
    exit();
}

// Agrupar por producto + lote + fecha de caducidad: sumar cantidades y conservar una fila por grupo
$agrupados = [];
foreach ($rows_to_insert as $r) {
    $clave = $r['ID_Prod_POS'] . '|' . $r['Lote'] . '|' . $r['Fecha_Caducidad'];
    if (!isset($agrupados[$clave])) {
        $agrupados[$clave] = $r;
    } else {
        $agrupados[$clave]['Contabilizado'] += $r['Contabilizado'];
    }
}
$rows_to_insert = array_values($agrupados);

$placeholders = [];
$values = [];
$types_arr = []; // tipos por columna: s=string, i=int, d=double

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
    // 16 tipos: s,s,s,s,s,s,i,s,s,d,d,d,s,s,s,i (uno por cada valor)
    $types_arr[] = 's'; // ID_Prod_POS
    $types_arr[] = 's'; // NumFactura
    $types_arr[] = 's'; // Proveedor
    $types_arr[] = 's'; // Cod_Barra
    $types_arr[] = 's'; // Nombre_Prod
    $types_arr[] = 's'; // Fk_Sucursal
    $types_arr[] = 'i'; // Contabilizado
    $types_arr[] = 's'; // Fecha_Caducidad
    $types_arr[] = 's'; // Lote
    $types_arr[] = 'd'; // PrecioMaximo
    $types_arr[] = 'd'; // Precio_Venta
    $types_arr[] = 'd'; // Precio_C
    $types_arr[] = 's'; // AgregadoPor
    $types_arr[] = 's'; // FechaInventario
    $types_arr[] = 's'; // Estatus
    $types_arr[] = 'i'; // NumOrden
}
$types = implode('', $types_arr);

$query = "INSERT INTO IngresosFarmacias (ID_Prod_POS, NumFactura, Proveedor, Cod_Barra, Nombre_Prod, Fk_Sucursal, Contabilizado, Fecha_Caducidad, Lote, PrecioMaximo, Precio_Venta, Precio_C, AgregadoPor, AgregadoEl, FechaInventario, Estatus, NumOrden) VALUES " . implode(', ', $placeholders);

$stmt = mysqli_prepare($conn, $query);
if (!$stmt) {
    echo json_encode(['status' => 'error', 'message' => 'Error al preparar: ' . mysqli_error($conn)]);
    mysqli_close($conn);
    exit();
}

// Transacción: si el trigger falla (Stock_POS o Historial_Lotes), todo hace rollback
mysqli_begin_transaction($conn);

try {
    // En PHP 8+ bind_param exige que el número de tipos coincida con el de valores
    if (strlen($types) !== count($values)) {
        throw new Exception('Tipos y valores no coinciden: ' . strlen($types) . ' tipos, ' . count($values) . ' valores.');
    }
    mysqli_stmt_bind_param($stmt, $types, ...$values);
    $ok = mysqli_stmt_execute($stmt);
} catch (Throwable $e) {
    mysqli_rollback($conn);
    mysqli_stmt_close($stmt);
    echo json_encode(['status' => 'error', 'message' => 'Error al guardar: ' . $e->getMessage()]);
    mysqli_close($conn);
    exit();
}

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
