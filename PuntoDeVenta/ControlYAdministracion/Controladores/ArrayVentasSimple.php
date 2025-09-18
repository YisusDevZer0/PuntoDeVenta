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

// Consulta simple sin filtros
$sql = "SELECT 
    Cod_Barra,
    Nombre_Prod,
    PrecioCompra,
    PrecioVenta,
    FolioTicket,
    ID_Sucursal,
    Turno,
    Cantidad_Venta,
    Total_Venta,
    Importe,
    Descuento,
    FormaPago,
    Cliente,
    FolioSignoVital,
    NomServ,
    AgregadoEl,
    AgregadoEnMomento,
    AgregadoPor
FROM Ventas 
ORDER BY AgregadoEl DESC 
LIMIT 10";

$result = $conn->query($sql);

if (!$result) {
    echo json_encode([
        "error" => true,
        "message" => "Error en la consulta: " . $conn->error,
        "data" => []
    ]);
    exit;
}

$data = [];
while ($row = $result->fetch_assoc()) {
    // Agregar nombre de sucursal básico
    $row['Sucursal'] = 'Sucursal ' . $row['ID_Sucursal'];
    $data[] = $row;
}

// Devolver los datos en formato JSON para DataTables
echo json_encode([
    "data" => $data
]);
?>
