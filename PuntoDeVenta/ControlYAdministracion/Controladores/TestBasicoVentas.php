<?php
// Configurar headers para JSON
header('Content-Type: application/json');

// Incluir la conexión a la base de datos
include_once "../Controladores/db_connect.php";

// Verificar conexión
if (!$conn) {
    echo json_encode([
        "error" => true,
        "message" => "Error de conexión a la base de datos",
        "data" => []
    ]);
    exit;
}

// Consulta muy simple
$sql = "SELECT 
    Cod_Barra,
    Nombre_Prod,
    Total_Venta as PrecioVenta,
    Folio_Ticket as FolioTicket,
    Fk_sucursal as ID_Sucursal,
    Turno,
    Cantidad_Venta,
    Total_Venta,
    Importe,
    DescuentoAplicado as Descuento,
    FormaDePago as FormaPago,
    Cliente,
    FolioSignoVital,
    Tipo as NomServ,
    AgregadoEl,
    AgregadoEl as AgregadoEnMomento,
    AgregadoPor,
    'Sin Sucursal' as Sucursal
FROM Ventas_POS 
ORDER BY AgregadoEl DESC 
LIMIT 10";

$result = $conn->query($sql);

if (!$result) {
    echo json_encode([
        "error" => true,
        "message" => "Error en consulta: " . $conn->error,
        "data" => []
    ]);
    exit;
}

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode([
    "data" => $data,
    "total" => count($data)
]);

$conn->close();
?>
