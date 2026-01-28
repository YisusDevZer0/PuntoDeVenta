<?php
/**
 * Búsqueda de producto para Registrar Lote (Farmacias).
 * Solo devuelve datos útiles si el producto tiene stock sin cubrir por lote/caducidad.
 * GET: codigo, sucursal (requeridos).
 */
header('Content-Type: application/json');
include_once __DIR__ . '/../dbconect.php';

$codigo = isset($_GET['codigo']) ? trim($_GET['codigo']) : '';
$sucursal_id = isset($_GET['sucursal']) ? (int)$_GET['sucursal'] : 0;

if (empty($codigo) || $sucursal_id <= 0) {
    echo json_encode([
        'success' => false,
        'error'   => 'Código de barras y sucursal son requeridos.'
    ]);
    exit;
}

try {
    $stmt = $con->prepare("
        SELECT 
            sp.ID_Prod_POS,
            sp.Cod_Barra,
            sp.Nombre_Prod,
            sp.Fk_sucursal,
            sp.Existencias_R,
            sp.Precio_Venta,
            sp.Precio_C
        FROM Stock_POS sp
        WHERE sp.Cod_Barra = ? AND sp.Fk_sucursal = ?
        LIMIT 1
    ");
    $stmt->bind_param("si", $codigo, $sucursal_id);
    $stmt->execute();
    $res = $stmt->get_result();
    $sp = $res->fetch_assoc();
    $stmt->close();

    if (!$sp) {
        echo json_encode([
            'success' => false,
            'error'   => 'Producto no encontrado en esta sucursal.'
        ]);
        exit;
    }

    $existencia_total = (int) $sp['Existencias_R'];
    if ($existencia_total <= 0) {
        echo json_encode([
            'success' => false,
            'error'   => 'El producto no tiene stock en esta sucursal.'
        ]);
        exit;
    }

    $stmt = $con->prepare("
        SELECT COALESCE(SUM(Existencias), 0) AS en_lotes
        FROM Historial_Lotes
        WHERE ID_Prod_POS = ? AND Fk_sucursal = ? AND Existencias > 0
    ");
    $stmt->bind_param("ii", $sp['ID_Prod_POS'], $sp['Fk_sucursal']);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    $en_lotes = (int) ($row['en_lotes'] ?? 0);
    $sin_cubrir = $existencia_total - $en_lotes;
    $permite_registrar_lote = $sin_cubrir > 0;

    $lotes = [];
    $stmt = $con->prepare("
        SELECT Lote, Fecha_Caducidad, Existencias,
               DATEDIFF(Fecha_Caducidad, CURDATE()) AS Dias_restantes
        FROM Historial_Lotes
        WHERE ID_Prod_POS = ? AND Fk_sucursal = ? AND Existencias > 0
        ORDER BY Fecha_Caducidad ASC
    ");
    $stmt->bind_param("ii", $sp['ID_Prod_POS'], $sp['Fk_sucursal']);
    $stmt->execute();
    $res_hl = $stmt->get_result();
    while ($r = $res_hl->fetch_assoc()) {
        $lotes[] = $r;
    }
    $stmt->close();

    echo json_encode([
        'success' => true,
        'producto' => [
            'id_prod_pos'           => $sp['ID_Prod_POS'],
            'ID_Prod_POS'           => $sp['ID_Prod_POS'],
            'cod_barra'             => $sp['Cod_Barra'],
            'Nombre_Prod'           => $sp['Nombre_Prod'],
            'nombre_producto'       => $sp['Nombre_Prod'],
            'Fk_sucursal'           => (int) $sp['Fk_sucursal'],
            'precio_venta'          => $sp['Precio_Venta'] ?? 0,
            'precio_compra'         => $sp['Precio_C'] ?? 0,
            'Existencias_R'         => $existencia_total,
            'existencia_total'      => $existencia_total,
            'Total_Lotes'           => $en_lotes,
            'en_lotes'              => $en_lotes,
            'sin_cubrir'            => $sin_cubrir,
            'permite_registrar_lote'=> $permite_registrar_lote,
            'lotes'                 => $lotes,
        ],
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error'   => 'Error al buscar: ' . $e->getMessage(),
    ]);
}
