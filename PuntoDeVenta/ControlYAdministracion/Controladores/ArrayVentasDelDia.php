<?php
// Habilitar reporte de errores (deshabilitado para producción)
// error_reporting(E_ALL);
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);

// Configurar headers para JSON
header('Content-Type: application/json');

// Incluir la conexión a la base de datos
include_once "../Controladores/db_connect.php";

// Obtener parámetros de filtro
$fecha_inicio = isset($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : date('Y-m-01');
$fecha_fin = isset($_GET['fecha_fin']) ? $_GET['fecha_fin'] : date('Y-m-d');
$sucursal = isset($_GET['sucursal']) ? $_GET['sucursal'] : '';

// Verificar conexión
if (!$conn) {
    echo json_encode([
        "error" => true,
        "message" => "Error de conexión a la base de datos",
        "data" => []
    ]);
    exit;
}

// Construir la consulta SQL básica primero
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
    AgregadoPor
FROM Ventas_POS 
WHERE AgregadoEl BETWEEN ? AND ?";

$params = [$fecha_inicio . ' 00:00:00', $fecha_fin . ' 23:59:59'];
$types = "ss";

// Agregar filtro de sucursal si se especifica
if (!empty($sucursal)) {
    $sql .= " AND Fk_sucursal = ?";
    $params[] = $sucursal;
    $types .= "i";
}

$sql .= " ORDER BY AgregadoEl DESC";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode([
        "error" => true,
        "message" => "Error en la preparación de la consulta: " . $conn->error,
        "data" => []
    ]);
    exit;
}

$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

$data = [];
while ($row = $result->fetch_assoc()) {
    // Obtener nombre de sucursal por separado
    $sucursal_id = $row['ID_Sucursal'];
    $sucursal_nombre = 'Sin Sucursal';
    
    if ($sucursal_id) {
        $sql_sucursal = "SELECT Nombre_Sucursal FROM Sucursales WHERE ID_Sucursal = ?";
        $stmt_sucursal = $conn->prepare($sql_sucursal);
        if ($stmt_sucursal) {
            $stmt_sucursal->bind_param("i", $sucursal_id);
            $stmt_sucursal->execute();
            $result_sucursal = $stmt_sucursal->get_result();
            if ($sucursal_row = $result_sucursal->fetch_assoc()) {
                $sucursal_nombre = $sucursal_row['Nombre_Sucursal'];
            }
            $stmt_sucursal->close();
        }
    }
    
    // Agregar el nombre de sucursal al array
    $row['Sucursal'] = $sucursal_nombre;
    $data[] = $row;
}

// Devolver los datos en formato JSON para DataTables
echo json_encode([
    "data" => $data
]);

$stmt->close();
$conn->close();
?>