<?php
/**
 * Registra líneas de venta en Ventas_POS (POST desde RealizarVentas / VentasAlmomento).
 * Respuesta JSON: { "status": "success"|"error", "message": "..." }
 */
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Método no permitido']);
    exit;
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (empty($_SESSION['VentasPos'])) {
    echo json_encode(['status' => 'error', 'message' => 'Sesión no válida o expirada']);
    exit;
}

require_once __DIR__ . '/db_connect.php';

if (!isset($conn) || !$conn instanceof mysqli) {
    echo json_encode(['status' => 'error', 'message' => 'Sin conexión a la base de datos']);
    exit;
}

/**
 * @param mixed $v
 */
function fdp_post_first($v): string
{
    if ($v === null || $v === '') {
        return '';
    }
    if (is_array($v)) {
        return isset($v[0]) ? (string) $v[0] : '';
    }
    return (string) $v;
}

/**
 * @param mixed $v
 * @return array<int, string>
 */
function fdp_post_arr($v): array
{
    if (!isset($v)) {
        return [];
    }
    if (!is_array($v)) {
        return [(string) $v];
    }
    return $v;
}

function fdp_parse_date_ymd(string $raw): string
{
    $raw = trim($raw);
    if ($raw === '') {
        return date('Y-m-d');
    }
    if (preg_match('/^(\d{4}-\d{2}-\d{2})/', $raw, $m)) {
        return $m[1];
    }
    $t = strtotime($raw);
    return $t ? date('Y-m-d', $t) : date('Y-m-d');
}

function fdp_parse_decimal($v): float
{
    if ($v === null || $v === '') {
        return 0.0;
    }
    if (is_array($v)) {
        $v = $v[0] ?? '';
    }
    $s = preg_replace('/[^\d.\-]/', '', (string) $v);
    return $s === '' || $s === '-' ? 0.0 : (float) $s;
}

function fdp_parse_int($v): int
{
    if ($v === null || $v === '') {
        return 0;
    }
    if (is_array($v)) {
        $v = $v[0] ?? 0;
    }
    return (int) preg_replace('/[^\d\-]/', '', (string) $v);
}

/**
 * mysqli_stmt::bind_param requiere referencias (para uso con call_user_func_array).
 *
 * @param array<int, mixed> $arr
 * @return array<int, mixed>
 */
function fdp_bind_ref_values(array $arr): array
{
    $refs = [];
    foreach ($arr as $k => $_) {
        $refs[$k] = &$arr[$k];
    }
    return $refs;
}

$codBarras = fdp_post_arr($_POST['CodBarras'] ?? null);
$n = count($codBarras);
if ($n === 0) {
    echo json_encode(['status' => 'error', 'message' => 'No se recibieron productos en la venta']);
    exit;
}

$nombres = fdp_post_arr($_POST['NombreDelProducto'] ?? []);
$cants = fdp_post_arr($_POST['CantidadVendida'] ?? []);
$importes = fdp_post_arr($_POST['ImporteGenerado'] ?? []);
$idsProd = fdp_post_arr($_POST['IdBasedatos'] ?? []);
$lotes = fdp_post_arr($_POST['LoteDelProducto'] ?? []);
$claves = fdp_post_arr($_POST['ClaveAdicional'] ?? []);
$tipos = fdp_post_arr($_POST['Tipo'] ?? []);
$tiposServ = fdp_post_arr($_POST['TiposDeServicio'] ?? []);
$turnos = fdp_post_arr($_POST['TurnoEnTurno'] ?? []);
$cajas = fdp_post_arr($_POST['CajaDeSucursal'] ?? []);
$tickets = fdp_post_arr($_POST['NumeroDeTickeT'] ?? []);
$rifas = fdp_post_arr($_POST['NumeroDeTickeRifa'] ?? []);
$vendedores = fdp_post_arr($_POST['AgregoElVendedor'] ?? []);
$sucursales = fdp_post_arr($_POST['SucursalEnVenta'] ?? []);
$sistemas = fdp_post_arr($_POST['Sistema'] ?? []);
$estatuses = fdp_post_arr($_POST['Estatus'] ?? []);
$fechas = fdp_post_arr($_POST['FechaDeVenta'] ?? []);
$formas = fdp_post_arr($_POST['FormaDePago'] ?? []);
$descuentos = fdp_post_arr($_POST['DescuentoAplicado'] ?? []);

$cliente = fdp_post_first($_POST['NombreDelCliente'] ?? '');
$signoVital = fdp_post_first($_POST['SignoVital'] ?? '');
$ticketAnt = fdp_post_first($_POST['TicketAnterior'] ?? '');
$cambio = fdp_parse_decimal($_POST['CambioDelCliente'] ?? 0);
$totalVentaG = fdp_parse_decimal($_POST['TotalDeVenta'] ?? 0);
$pagoTarjeta = fdp_parse_decimal($_POST['iptTarjetaCreditosOculto'] ?? $_POST['iptTarjeta'] ?? 0);
$pagoEfectivo = fdp_parse_decimal($_POST['iptEfectivoOculto'] ?? $_POST['iptEfectivo'] ?? 0);
$folioRifaExtra = fdp_post_first($_POST['FolioRifaConPrefijo'] ?? $_POST['FolioRifaGlobal'] ?? '');

$cantidadPago = $pagoTarjeta + $pagoEfectivo;
if ($cantidadPago <= 0 && $totalVentaG > 0) {
    $cantidadPago = $totalVentaG;
}

$folioTicket = fdp_post_first($tickets);
if ($folioTicket === '') {
    echo json_encode(['status' => 'error', 'message' => 'Falta el folio de ticket']);
    exit;
}

$folioSucursal = strlen($folioTicket) >= 3 ? substr($folioTicket, 0, 3) : $folioTicket;
$folioAleatorio = str_replace('.', '', uniqid('VTA', true));

$sumaImportes = 0.0;
for ($j = 0; $j < $n; $j++) {
    $sumaImportes += fdp_parse_decimal($importes[$j] ?? 0);
}
$totalVentaGlobal = $totalVentaG > 0 ? $totalVentaG : $sumaImportes;
if ($totalVentaGlobal <= 0 && $sumaImportes > 0) {
    $totalVentaGlobal = $sumaImportes;
}

$stmtHod = $conn->prepare(
    'SELECT ID_H_O_D FROM Stock_POS WHERE ID_Prod_POS = ? AND Fk_sucursal = ? LIMIT 1'
);
if ($stmtHod === false) {
    echo json_encode(['status' => 'error', 'message' => 'Error al preparar consulta de inventario: ' . $conn->error]);
    exit;
}

$sql = 'INSERT INTO Ventas_POS (
    ID_Prod_POS, Identificador_tipo, Turno, FolioSucursal, Folio_Ticket, Folio_Ticket_Aleatorio,
    Clave_adicional, Cod_Barra, Nombre_Prod, Cantidad_Venta, Fk_sucursal, Total_Venta, Importe, Total_VentaG,
    DescuentoAplicado, FormaDePago, CantidadPago, Cambio, Cliente, Fecha_venta, Fk_Caja, Lote,
    Motivo_Cancelacion, Estatus, Sistema, AgregadoPor, ID_H_O_D, FolioSignoVital, TicketAnterior,
    Pagos_tarjeta, Tipo, FolioRifa
) VALUES (
    ?,?,?,?,?,?,
    ?,?,?,?,?,?,?,?,?,
    ?,?,?,?,?,?,?,
    ?,?,?,?,?,?,?,?,
    ?,?,?
)';

$stmtIns = $conn->prepare($sql);
if ($stmtIns === false) {
    echo json_encode(['status' => 'error', 'message' => 'Error al preparar inserción: ' . $conn->error]);
    exit;
}

$types = 'iisssssssiidddisddsissssssssdss';

$conn->begin_transaction();

$insertadas = 0;

try {
    for ($i = 0; $i < $n; $i++) {
        $cod = trim((string) ($codBarras[$i] ?? ''));
        if ($cod === '') {
            continue;
        }

        $idProd = fdp_parse_int($idsProd[$i] ?? 0);
        $fkSuc = fdp_parse_int($sucursales[$i] ?? fdp_post_first($sucursales));
        $fkCaja = fdp_parse_int($cajas[$i] ?? fdp_post_first($cajas));
        if ($idProd <= 0 || $fkSuc <= 0 || $fkCaja <= 0) {
            throw new RuntimeException('Datos de producto, sucursal o caja incompletos en la línea ' . ($i + 1));
        }

        $idHod = '';
        $stmtHod->bind_param('ii', $idProd, $fkSuc);
        if (!$stmtHod->execute()) {
            throw new RuntimeException('Error al consultar ID_H_O_D: ' . $stmtHod->error);
        }
        $resH = $stmtHod->get_result();
        if ($resH && ($rowH = $resH->fetch_assoc())) {
            $idHod = (string) $rowH['ID_H_O_D'];
        }
        if ($idHod === '') {
            $idHod = 'N/A';
        }

        $identTipo = fdp_parse_int($tiposServ[$i] ?? 0);
        if ($identTipo <= 0) {
            $identTipo = 1;
        }

        $turno = (string) ($turnos[$i] ?? fdp_post_first($turnos));
        $ticketLine = trim((string) ($tickets[$i] ?? $folioTicket));
        $nombreProd = (string) ($nombres[$i] ?? '');
        $cantV = fdp_parse_int($cants[$i] ?? 0);
        if ($cantV <= 0) {
            throw new RuntimeException('Cantidad inválida en la línea ' . ($i + 1));
        }

        $importeLinea = fdp_parse_decimal($importes[$i] ?? 0);
        if ($importeLinea < 0) {
            $importeLinea = 0;
        }

        $tvLinea = $importeLinea;

        $desc = fdp_parse_int($descuentos[$i] ?? 0);
        $forma = (string) ($formas[$i] ?? fdp_post_first($formas));
        if ($forma === '') {
            $forma = 'Efectivo';
        }

        $fechaV = fdp_parse_date_ymd((string) ($fechas[$i] ?? fdp_post_first($fechas)));
        $lote = trim((string) ($lotes[$i] ?? ''));
        if ($lote === '') {
            $lote = 'SIN LOTE';
        }

        $claveRaw = $claves[$i] ?? '';
        $clave = trim(is_array($claveRaw) ? (string) ($claveRaw[0] ?? '') : (string) $claveRaw);

        $est = (string) ($estatuses[$i] ?? fdp_post_first($estatuses));
        if ($est === '') {
            $est = 'Pagado';
        }

        $sis = (string) ($sistemas[$i] ?? fdp_post_first($sistemas));
        if ($sis === '') {
            $sis = 'POSVENTAS';
        }

        $agr = (string) ($vendedores[$i] ?? fdp_post_first($vendedores));
        if ($agr === '') {
            $agr = 'Sistema';
        }

        $tipoProd = (string) ($tipos[$i] ?? '');
        if ($tipoProd === '') {
            $tipoProd = 'Producto';
        }

        $folioRifaLine = trim((string) ($rifas[$i] ?? ''));
        if ($folioRifaLine === '' && $folioRifaExtra !== '') {
            $folioRifaLine = $folioRifaExtra;
        }
        if ($folioRifaLine === '') {
            $folioRifaLine = '0';
        }

        $sv = $signoVital !== '' ? $signoVital : 'N/A';
        $ta = $ticketAnt !== '' ? $ticketAnt : 'N/A';
        $cli = $cliente !== '' ? $cliente : 'Público general';

        $bindArgs = [
            $types,
            $idProd,
            $identTipo,
            $turno,
            $folioSucursal,
            $ticketLine,
            $folioAleatorio,
            $clave,
            $cod,
            $nombreProd,
            $cantV,
            $fkSuc,
            $tvLinea,
            $importeLinea,
            $totalVentaGlobal,
            $desc,
            $forma,
            $cantidadPago,
            $cambio,
            $cli,
            $fechaV,
            $fkCaja,
            $lote,
            'N/A',
            $est,
            $sis,
            $agr,
            $idHod,
            $sv,
            $ta,
            $pagoTarjeta,
            $tipoProd,
            $folioRifaLine,
        ];

        if (!call_user_func_array([$stmtIns, 'bind_param'], fdp_bind_ref_values($bindArgs))) {
            throw new RuntimeException('Error bind_param línea ' . ($i + 1));
        }

        if (!$stmtIns->execute()) {
            throw new RuntimeException($stmtIns->error);
        }
        $insertadas++;
    }

    if ($insertadas === 0) {
        throw new RuntimeException('No hay líneas de venta válidas (códigos de barras vacíos)');
    }

    $conn->commit();
    echo json_encode(['status' => 'success', 'message' => 'Venta registrada correctamente']);
} catch (Throwable $e) {
    $conn->rollback();
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}

$stmtHod->close();
$stmtIns->close();
$conn->close();
