<?php
header('Content-Type: application/json');
include_once "Controladores/db_connect.php";
include_once "Controladores/ControladorUsuario.php";

// Verificar sesión
session_start();
if(!isset($_SESSION['VentasPos'])){
    echo json_encode(['success' => false, 'message' => 'Sesión no válida']);
    exit();
}

// Obtener datos del usuario
$userId = $_SESSION['VentasPos'];
$sql = "SELECT
    Usuarios_PV.Id_PvUser,
    Usuarios_PV.Nombre_Apellidos,
    Usuarios_PV.Fk_Sucursal,
    Sucursales.Nombre_Sucursal
FROM
    Usuarios_PV
INNER JOIN Sucursales ON Usuarios_PV.Fk_Sucursal = Sucursales.ID_Sucursal 
WHERE Usuarios_PV.Id_PvUser = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $userId);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if (!$row) {
    echo json_encode(['success' => false, 'message' => 'Error al obtener datos del usuario']);
    exit();
}

$sucursal_id = $row['Fk_Sucursal'];
$q = 'test'; // Búsqueda de prueba

// Buscar productos
$sql = "SELECT 
            s.ID_Prod_POS, 
            s.Nombre_Prod, 
            s.Cod_Barra,
            s.Clave_adicional,
            s.Existencias_R,
            s.Min_Existencia,
            s.Max_Existencia,
            p.Precio_Venta,
            p.Precio_C
        FROM Stock_POS s
        LEFT JOIN Productos_POS p ON s.ID_Prod_POS = p.ID_Prod_POS
        WHERE s.Fk_sucursal = ? 
        AND (s.Nombre_Prod LIKE ? OR s.Cod_Barra LIKE ? OR s.Clave_adicional LIKE ?)
        ORDER BY s.Nombre_Prod ASC 
        LIMIT 20";

$stmt = $conn->prepare($sql);
$like = "%$q%";
$stmt->bind_param("isss", $sucursal_id, $like, $like, $like);
$stmt->execute();
$res = $stmt->get_result();
$productos = [];

while($prod = $res->fetch_assoc()) {
    $productos[] = $prod;
}

echo json_encode([
    'success' => true,
    'message' => 'Búsqueda exitosa',
    'usuario' => $row,
    'sucursal_id' => $sucursal_id,
    'query' => $q,
    'productos' => $productos,
    'total' => count($productos)
]);
?>
