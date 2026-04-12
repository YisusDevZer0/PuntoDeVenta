<?php
/**
 * Registra líneas de venta en Ventas_POS (POST desde RealizarVentas / VentasAlmomento).
 * No usa db_connect.php para evitar config/app.php (chequeo DOCUMENT_ROOT → 500 en algunos hosts).
 *
 * Respuesta JSON: { "status": "success"|"error", "message": "..." }
 */
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

$respond = static function (string $status, string $message, int $http = 200): void {
    if ($http !== 200) {
        http_response_code($http);
    }
    echo json_encode(['status' => $status, 'message' => $message], JSON_UNESCAPED_UNICODE);
    exit;
};

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        $respond('error', 'Método no permitido', 405);
    }

    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (empty($_SESSION['VentasPos'])) {
        $respond('error', 'Sesión no válida o expirada', 401);
    }

    require_once __DIR__ . '/mysqli_conn_local.php';

    if (!isset($conn) || !$conn instanceof mysqli) {
        $respond('error', 'Sin conexión a la base de datos', 500);
    }

    /**
     * @param mixed $v
     */
    $postFirst = static function ($v): string {
        if ($v === null || $v === '') {
            return '';
        }
        if (is_array($v)) {
            return isset($v[0]) ? (string) $v[0] : '';
        }
        return (string) $v;
    };

    /**
     * @param mixed $v
     * @return array<int, string>
     */
    $postArr = static function ($v): array {
        if (!isset($v)) {
            return [];
        }
        if (!is_array($v)) {
            return [(string) $v];
        }
        return $v;
    };

    $parseDateYmd = static function (string $raw): string {
        $raw = trim($raw);
        if ($raw === '') {
            return date('Y-m-d');
        }
        if (preg_match('/^(\d{4}-\d{2}-\d{2})/', $raw, $m)) {
            return $m[1];
        }
        $t = strtotime($raw);
        return $t ? date('Y-m-d', $t) : date('Y-m-d');
    };

    $parseDecimal = static function ($v): float {
        if ($v === null || $v === '') {
            return 0.0;
        }
        if (is_array($v)) {
            $v = $v[0] ?? '';
        }
        $s = preg_replace('/[^\d.\-]/', '', (string) $v);
        return $s === '' || $s === '-' ? 0.0 : (float) $s;
    };

    $parseInt = static function ($v): int {
        if ($v === null || $v === '') {
            return 0;
        }
        if (is_array($v)) {
            $v = $v[0] ?? 0;
        }
        return (int) preg_replace('/[^\d\-]/', '', (string) $v);
    };

    $codBarras = $postArr($_POST['CodBarras'] ?? null);
    $n = count($codBarras);
    if ($n === 0) {
        $respond('error', 'No se recibieron productos en la venta');
    }

    $nombres = $postArr($_POST['NombreDelProducto'] ?? []);
    $cants = $postArr($_POST['CantidadVendida'] ?? []);
    $importes = $postArr($_POST['ImporteGenerado'] ?? []);
    $idsProd = $postArr($_POST['IdBasedatos'] ?? []);
    $lotes = $postArr($_POST['LoteDelProducto'] ?? []);
    $claves = $postArr($_POST['ClaveAdicional'] ?? []);
    $tipos = $postArr($_POST['Tipo'] ?? []);
    $tiposServ = $postArr($_POST['TiposDeServicio'] ?? []);
    $turnos = $postArr($_POST['TurnoEnTurno'] ?? []);
    $cajas = $postArr($_POST['CajaDeSucursal'] ?? []);
    $tickets = $postArr($_POST['NumeroDeTickeT'] ?? []);
    $rifas = $postArr($_POST['NumeroDeTickeRifa'] ?? []);
    $vendedores = $postArr($_POST['AgregoElVendedor'] ?? []);
    $sucursales = $postArr($_POST['SucursalEnVenta'] ?? []);
    $sistemas = $postArr($_POST['Sistema'] ?? []);
    $estatuses = $postArr($_POST['Estatus'] ?? []);
    $fechas = $postArr($_POST['FechaDeVenta'] ?? []);
    $formas = $postArr($_POST['FormaDePago'] ?? []);
    $descuentos = $postArr($_POST['DescuentoAplicado'] ?? []);

    $cliente = $postFirst($_POST['NombreDelCliente'] ?? '');
    $signoVital = $postFirst($_POST['SignoVital'] ?? '');
    $ticketAnt = $postFirst($_POST['TicketAnterior'] ?? '');
    $cambio = $parseDecimal($_POST['CambioDelCliente'] ?? 0);
    $totalVentaG = $parseDecimal($_POST['TotalDeVenta'] ?? 0);
    $pagoTarjeta = $parseDecimal($_POST['iptTarjetaCreditosOculto'] ?? $_POST['iptTarjeta'] ?? 0);
    $pagoEfectivo = $parseDecimal($_POST['iptEfectivoOculto'] ?? $_POST['iptEfectivo'] ?? 0);
    $folioRifaExtra = $postFirst($_POST['FolioRifaConPrefijo'] ?? $_POST['FolioRifaGlobal'] ?? '');

    $cantidadPago = $pagoTarjeta + $pagoEfectivo;
    if ($cantidadPago <= 0 && $totalVentaG > 0) {
        $cantidadPago = $totalVentaG;
    }

    $folioTicket = $postFirst($tickets);
    if ($folioTicket === '') {
        $respond('error', 'Falta el folio de ticket');
    }

    $folioSucursal = strlen($folioTicket) >= 3 ? substr($folioTicket, 0, 3) : $folioTicket;
    $folioAleatorio = str_replace('.', '', uniqid('VTA', true));

    $sumaImportes = 0.0;
    for ($j = 0; $j < $n; $j++) {
        $sumaImportes += $parseDecimal($importes[$j] ?? 0);
    }
    $totalVentaGlobal = $totalVentaG > 0 ? $totalVentaG : $sumaImportes;
    if ($totalVentaGlobal <= 0 && $sumaImportes > 0) {
        $totalVentaGlobal = $sumaImportes;
    }

    // 33 columnas: Venta_POS_ID = NULL obliga a AUTO_INCREMENT (evita varios INSERT con 0 y Duplicate entry '0')
    $sql = 'INSERT INTO Ventas_POS (
    Venta_POS_ID,
    ID_Prod_POS, Identificador_tipo, Turno, FolioSucursal, Folio_Ticket, Folio_Ticket_Aleatorio,
    Clave_adicional, Cod_Barra, Nombre_Prod, Cantidad_Venta, Fk_sucursal, Total_Venta, Importe, Total_VentaG,
    DescuentoAplicado, FormaDePago, CantidadPago, Cambio, Cliente, Fecha_venta, Fk_Caja, Lote,
    Motivo_Cancelacion, Estatus, Sistema, AgregadoPor, ID_H_O_D, FolioSignoVital, TicketAnterior,
    Pagos_tarjeta, Tipo, FolioRifa
) VALUES (
    NULL,
    ?,?,?,?,?,?,
    ?,?,?,?,?,?,?,?,
    ?,?,?,?,?,?,?,?,
    ?,?,?,?,?,?,?,
    ?,?,?
)';

    $stmtIns = $conn->prepare($sql);
    if ($stmtIns === false) {
        $respond('error', 'Error al preparar inserción: ' . $conn->error, 500);
    }

    // 32 tipos: 6 + 8 + 8 + 7 + 3 → iissss + sssiiddd + isddssis + 7×s (motivo..ticket ant.) + dss
    $types = 'iissss' . 'sssiiddd' . 'isddssis' . str_repeat('s', 7) . 'dss';

    $lookupHod = static function (mysqli $mysqli, int $idProd, int $fkSuc): string {
        $q = sprintf(
            'SELECT ID_H_O_D FROM Stock_POS WHERE ID_Prod_POS = %d AND Fk_sucursal = %d LIMIT 1',
            $idProd,
            $fkSuc
        );
        $res = $mysqli->query($q);
        if ($res && ($row = $res->fetch_assoc())) {
            $v = trim((string) ($row['ID_H_O_D'] ?? ''));
            if ($v !== '') {
                return $v;
            }
        }
        return 'N/A';
    };

    $conn->begin_transaction();
    $insertadas = 0;
    $motivoCancel = 'N/A';

    for ($i = 0; $i < $n; $i++) {
        $cod = trim((string) ($codBarras[$i] ?? ''));
        if ($cod === '') {
            continue;
        }

        $idProd = $parseInt($idsProd[$i] ?? 0);
        $fkSuc = $parseInt($sucursales[$i] ?? $postFirst($sucursales));
        $fkCaja = $parseInt($cajas[$i] ?? $postFirst($cajas));
        if ($idProd <= 0 || $fkSuc <= 0 || $fkCaja <= 0) {
            throw new RuntimeException('Datos de producto, sucursal o caja incompletos en la línea ' . ($i + 1));
        }

        $idHod = $lookupHod($conn, $idProd, $fkSuc);

        $identTipo = $parseInt($tiposServ[$i] ?? 0);
        if ($identTipo <= 0) {
            $identTipo = 1;
        }

        $turno = (string) ($turnos[$i] ?? $postFirst($turnos));
        $ticketLine = trim((string) ($tickets[$i] ?? $folioTicket));
        $nombreProd = (string) ($nombres[$i] ?? '');
        $cantV = $parseInt($cants[$i] ?? 0);
        if ($cantV <= 0) {
            throw new RuntimeException('Cantidad inválida en la línea ' . ($i + 1));
        }

        $importeLinea = $parseDecimal($importes[$i] ?? 0);
        if ($importeLinea < 0) {
            $importeLinea = 0.0;
        }

        $tvLinea = $importeLinea;

        $desc = $parseInt($descuentos[$i] ?? 0);
        $forma = (string) ($formas[$i] ?? $postFirst($formas));
        if ($forma === '') {
            $forma = 'Efectivo';
        }

        $fechaV = $parseDateYmd((string) ($fechas[$i] ?? $postFirst($fechas)));
        $lote = trim((string) ($lotes[$i] ?? ''));
        if ($lote === '') {
            $lote = 'SIN LOTE';
        }

        $claveRaw = $claves[$i] ?? '';
        $clave = trim(is_array($claveRaw) ? (string) ($claveRaw[0] ?? '') : (string) $claveRaw);

        $est = (string) ($estatuses[$i] ?? $postFirst($estatuses));
        if ($est === '') {
            $est = 'Pagado';
        }

        $sis = (string) ($sistemas[$i] ?? $postFirst($sistemas));
        if ($sis === '') {
            $sis = 'POSVENTAS';
        }

        $agr = (string) ($vendedores[$i] ?? $postFirst($vendedores));
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

        $stmtIns->bind_param(
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
            $motivoCancel,
            $est,
            $sis,
            $agr,
            $idHod,
            $sv,
            $ta,
            $pagoTarjeta,
            $tipoProd,
            $folioRifaLine
        );

        if (!$stmtIns->execute()) {
            throw new RuntimeException($stmtIns->error);
        }
        $insertadas++;
    }

    if ($insertadas === 0) {
        throw new RuntimeException('No hay líneas de venta válidas (códigos de barras vacíos)');
    }

    $conn->commit();
    $stmtIns->close();
    $conn->close();

    $respond('success', 'Venta registrada correctamente');
} catch (Throwable $e) {
    if (isset($conn) && $conn instanceof mysqli) {
        if ($conn->errno) {
            @$conn->rollback();
        }
        @$conn->close();
    }
    $respond('error', $e->getMessage(), 500);
}
