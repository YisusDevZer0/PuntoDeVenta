<?php
// Debug completo de la API
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include_once "Controladores/db_connect.php";
include_once "Controladores/ControladorUsuario.php";

echo "<h1>Debug Completo de API</h1>";

echo "<h2>1. Verificar Sesión:</h2>";
if(isset($_SESSION['VentasPos'])){
    echo "✅ Sesión válida: " . $_SESSION['VentasPos'];
} else {
    echo "❌ Sesión no válida";
    echo "<pre>" . print_r($_SESSION, true) . "</pre>";
    exit;
}

echo "<h2>2. Verificar Conexión a BD:</h2>";
if($conn) {
    echo "✅ Conexión a BD exitosa";
} else {
    echo "❌ Error de conexión a BD";
    exit;
}

echo "<h2>3. Obtener Datos del Usuario:</h2>";
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

if ($row) {
    echo "✅ Usuario encontrado: " . $row['Nombre_Apellidos'];
    echo "<br>Sucursal: " . $row['Nombre_Sucursal'] . " (ID: " . $row['Fk_Sucursal'] . ")";
} else {
    echo "❌ Usuario no encontrado";
    exit;
}

echo "<h2>4. Probar Búsqueda de Productos:</h2>";
$sucursal_id = $row['Fk_Sucursal'];
$q = 'test';

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

echo "✅ Búsqueda ejecutada. Productos encontrados: " . count($productos);
echo "<pre>" . print_r($productos, true) . "</pre>";

echo "<h2>5. Simular Respuesta de API:</h2>";
$response = [
    'status' => 'ok',
    'data' => $productos
];
echo "<pre>" . json_encode($response, JSON_PRETTY_PRINT) . "</pre>";
?>
