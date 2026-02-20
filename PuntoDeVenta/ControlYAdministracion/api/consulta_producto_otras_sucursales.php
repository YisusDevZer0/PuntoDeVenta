<?php
header('Content-Type: application/json');
include_once "../Controladores/db_connect.php";
include_once "../Controladores/ControladorUsuario.php";

session_start();
if (!isset($_SESSION['ControlMaestro']) && !isset($_SESSION['AdministradorRH']) && !isset($_SESSION['Marketing'])) {
    echo json_encode(['success' => false, 'message' => 'Sesión no válida']);
    exit();
}

$sucursal_actual = (int) $row['Fk_Sucursal'];
$query = isset($_POST['query']) ? trim($_POST['query']) : (isset($_GET['q']) ? trim($_GET['q']) : '');

if ($query === '') {
    echo json_encode([
        'success' => true,
        'productos' => [],
        'total' => 0,
        'query' => '',
        'sucursal_actual' => $sucursal_actual
    ]);
    exit();
}

try {
    $like = '%' . $query . '%';

    // Buscar productos que coincidan por código, nombre o clave (en todas las sucursales)
    $sql = "SELECT
                sp.ID_Prod_POS,
                sp.Cod_Barra,
                sp.Nombre_Prod,
                sp.Clave_adicional,
                sp.Fk_sucursal,
                sp.Existencias_R,
                sp.Min_Existencia,
                sp.Max_Existencia,
                s.Nombre_Sucursal
            FROM Stock_POS sp
            INNER JOIN Sucursales s ON sp.Fk_sucursal = s.ID_Sucursal
            WHERE (sp.Cod_Barra = ? OR sp.Nombre_Prod LIKE ? OR sp.Clave_adicional LIKE ?)
            ORDER BY sp.Cod_Barra, sp.Fk_sucursal = ? DESC, sp.Existencias_R DESC";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $query, $like, $like, $sucursal_actual);
    $stmt->execute();
    $result = $stmt->get_result();

    $filas = [];
    $stock_mi_sucursal = []; // por ID_Prod_POS + Cod_Barra, guardar existencias de la sucursal del usuario

    while ($r = $result->fetch_assoc()) {
        $key = $r['ID_Prod_POS'] . '|' . $r['Cod_Barra'];
        if ($r['Fk_sucursal'] == $sucursal_actual) {
            $stock_mi_sucursal[$key] = (int) $r['Existencias_R'];
        }
        $filas[] = $r;
    }

    // Marcar "es_mi_sucursal" y "menos_que_mi_sucursal" (solo donde tenga existencia y sea menor que la propia)
    $productos = [];
    foreach ($filas as $r) {
        $key = $r['ID_Prod_POS'] . '|' . $r['Cod_Barra'];
        $mi_stock = isset($stock_mi_sucursal[$key]) ? $stock_mi_sucursal[$key] : null;
        $existencias = (int) $r['Existencias_R'];
        $es_mi_sucursal = ($r['Fk_sucursal'] == $sucursal_actual);
        $menos_que_mi_sucursal = false;
        if ($mi_stock !== null && !$es_mi_sucursal && $existencias > 0 && $existencias < $mi_stock) {
            $menos_que_mi_sucursal = true;
        }

        $productos[] = [
            'ID_Prod_POS' => $r['ID_Prod_POS'],
            'Cod_Barra' => $r['Cod_Barra'],
            'Nombre_Prod' => $r['Nombre_Prod'],
            'Clave_adicional' => $r['Clave_adicional'],
            'Fk_sucursal' => (int) $r['Fk_sucursal'],
            'Nombre_Sucursal' => $r['Nombre_Sucursal'],
            'Existencias_R' => $existencias,
            'Min_Existencia' => (int) $r['Min_Existencia'],
            'Max_Existencia' => (int) $r['Max_Existencia'],
            'es_mi_sucursal' => $es_mi_sucursal,
            'menos_que_mi_sucursal' => $menos_que_mi_sucursal
        ];
    }

    echo json_encode([
        'success' => true,
        'productos' => $productos,
        'total' => count($productos),
        'query' => $query,
        'sucursal_actual' => $sucursal_actual,
        'nombre_sucursal_actual' => $row['Nombre_Sucursal'] ?? ''
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error en la consulta: ' . $e->getMessage()
    ]);
}
