<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: text/csv');
header('Content-Disposition: attachment;filename=inventario_sucursal.csv');

include("Controladores/db_connection.php");

if (!isset($_GET['id_sucursal']) || !is_numeric($_GET['id_sucursal'])) {
    die("ID de sucursal no válido. Parámetro recibido: " . htmlspecialchars($_GET['id_sucursal']));
}

$id_sucursal = intval($_GET['id_sucursal']);
if ($id_sucursal <= 0) {
    die("ID de sucursal no válido.");
}

$sql = "SELECT Stock_POS.Folio_Prod_Stock, Stock_POS.Clave_adicional, Stock_POS.ID_Prod_POS, Stock_POS.AgregadoEl,
               Stock_POS.Clave_Levic, Stock_POS.Cod_Barra, Stock_POS.Nombre_Prod, Stock_POS.Tipo_Servicio, Stock_POS.Tipo,
               Stock_POS.Fk_sucursal, Stock_POS.Max_Existencia, Stock_POS.Min_Existencia, Stock_POS.Existencias_R,
               Stock_POS.Proveedor1, Stock_POS.Proveedor2, Stock_POS.Estatus, Stock_POS.ID_H_O_D, Sucursales.ID_Sucursal,
               Sucursales.Nombre_Sucursal, Servicios_POS.Servicio_ID, Servicios_POS.Nom_Serv, Productos_POS.ID_Prod_POS,
               Productos_POS.Precio_Venta, Productos_POS.Precio_C
        FROM Stock_POS
        INNER JOIN Sucursales ON Stock_POS.Fk_sucursal = Sucursales.ID_Sucursal
        INNER JOIN Servicios_POS ON Stock_POS.Tipo_Servicio = Servicios_POS.Servicio_ID
        INNER JOIN Productos_POS ON Productos_POS.ID_Prod_POS = Stock_POS.ID_Prod_POS
        WHERE Stock_POS.Fk_sucursal = ?";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Error en la preparación de la consulta: " . htmlspecialchars($conn->error));
}

$stmt->bind_param("i", $id_sucursal);
if (!$stmt->execute()) {
    die("Error en la ejecución de la consulta: " . htmlspecialchars($stmt->error));
}

$result = $stmt->get_result();
if (!$result) {
    die("Error al obtener los resultados: " . htmlspecialchars($conn->error));
}

$output = fopen('php://output', 'w');
if (!$output) {
    die("Error al abrir el flujo de salida.");
}

fputcsv($output, [
    'Cod_Barra', 'Clave_adicional', 'Clave_Levic', 'Nombre_Prod', 'Precio_Venta', 'Nom_Serv', 'Tipo',
    'Proveedor1', 'Proveedor2', 'Sucursal', 'UltimoMovimiento', 'Existencias_R', 'Min_Existencia', 'Max_Existencia'
]);

while ($fila = $result->fetch_assoc()) {
    fputcsv($output, [
        $fila["Cod_Barra"],
        $fila["Clave_adicional"],
        $fila["Clave_Levic"],
        $fila["Nombre_Prod"],
        $fila["Precio_Venta"],
        $fila["Nom_Serv"],
        $fila["Tipo"],
        $fila["Proveedor1"],
        $fila["Proveedor2"],
        $fila["Nombre_Sucursal"],
        $fila["AgregadoEl"],
        $fila["Existencias_R"],
        $fila["Min_Existencia"],
        $fila["Max_Existencia"]
    ]);
}

fclose($output);
$stmt->close();
$conn->close();
?>
