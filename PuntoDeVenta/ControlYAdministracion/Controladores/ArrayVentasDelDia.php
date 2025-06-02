<?php
header('Content-Type: application/json');
include("db_connection.php");
include_once "ControladorUsuario.php";

// Iniciar la sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Obtener el valor de Fk_Sucursal desde la variable $row que se establece en ControladorUsuario.php
$fk_sucursal = isset($row['Fk_Sucursal']) ? $row['Fk_Sucursal'] : '';

// Depuración: Verificar el valor de Fk_Sucursal
error_log("Fk_Sucursal: " . $fk_sucursal);

// Verificar si la sucursal tiene un valor válido
if (empty($fk_sucursal)) {
    echo json_encode(["error" => "El valor de Fk_Sucursal está vacío"]);
    exit;
}

$sql = "SELECT DISTINCT
Ventas_POS.Venta_POS_ID,
Ventas_POS.Folio_Ticket,
Ventas_POS.FolioSucursal,
Ventas_POS.Fk_Caja,
Ventas_POS.Identificador_tipo,
Ventas_POS.Fecha_venta, 
Ventas_POS.Total_Venta,
Ventas_POS.Importe,
Ventas_POS.Total_VentaG,
Ventas_POS.FormaDePago,
Ventas_POS.Turno,
Ventas_POS.FolioSignoVital,
Ventas_POS.Cliente,
Cajas.ID_Caja,
Cajas.Sucursal,
Cajas.MedicoEnturno,
Cajas.EnfermeroEnturno,
Ventas_POS.Cod_Barra,
Ventas_POS.Clave_adicional,
Ventas_POS.Nombre_Prod,
Ventas_POS.Cantidad_Venta,
Ventas_POS.Fk_sucursal,
Ventas_POS.AgregadoPor,
Ventas_POS.AgregadoEl,
Ventas_POS.Lote,
Ventas_POS.ID_H_O_D,
Sucursales.ID_Sucursal, 
Sucursales.Nombre_Sucursal,
Servicios_POS.Servicio_ID,
Servicios_POS.Nom_Serv,
Ventas_POS.DescuentoAplicado,
Stock_POS.ID_Prod_POS,
Stock_POS.Precio_Venta,
Stock_POS.Precio_C
FROM 
Ventas_POS
LEFT JOIN 
Sucursales ON Ventas_POS.Fk_sucursal = Sucursales.ID_Sucursal 
LEFT JOIN 
Servicios_POS ON Ventas_POS.Identificador_tipo = Servicios_POS.Servicio_ID 
LEFT JOIN 
Cajas ON Cajas.ID_Caja = Ventas_POS.Fk_Caja
LEFT JOIN 
Stock_POS ON Stock_POS.ID_Prod_POS = Ventas_POS.ID_Prod_POS
WHERE 
YEAR(Ventas_POS.Fecha_venta) = YEAR(CURDATE()) 
AND MONTH(Ventas_POS.Fecha_venta) = MONTH(CURDATE())
AND Ventas_POS.Fk_sucursal = ?";

// Depuración: Verificar la consulta SQL
error_log("SQL Query: " . $sql);

// Preparar la declaración
$stmt = $conn->prepare($sql);

// Verificar si la consulta fue preparada correctamente
if (!$stmt) {
    error_log("Error al preparar la consulta: " . $conn->error);
    echo json_encode(["error" => "Error al preparar la consulta: " . $conn->error]);
    exit;
}

// Enlazar el parámetro Fk_Sucursal
$stmt->bind_param("s", $fk_sucursal);

// Depuración: Verificar el tipo y valor del parámetro
error_log("Tipo de Fk_Sucursal: " . gettype($fk_sucursal));
error_log("Valor de Fk_Sucursal en bind_param: " . $fk_sucursal);

// Ejecutar y verificar la consulta
if (!$stmt->execute()) {
    error_log("Error en la ejecución de la consulta: " . $stmt->error);
    echo json_encode(["error" => "Error en la ejecución de la consulta: " . $stmt->error]);
    exit;
}

$result = $stmt->get_result();

// Depuración: Verificar el número de resultados
error_log("Número de resultados: " . $result->num_rows);

// Verificar si hay resultados
if ($result->num_rows === 0) {
    echo json_encode(["error" => "No se encontraron resultados para la sucursal: " . $fk_sucursal]);
    exit;
}

// Inicializar array para almacenar los resultados
$data = [];

while ($fila = $result->fetch_assoc()) {
    $data[] = [
        "Cod_Barra" => $fila["Cod_Barra"],
        "Nombre_Prod" => $fila["Nombre_Prod"],
        "PrecioCompra" => $fila["Precio_C"],
        "PrecioVenta" => $fila["Precio_Venta"],
        "FolioTicket" => $fila["FolioSucursal"] . '' . $fila["Folio_Ticket"],
        "Sucursal" => $fila["Nombre_Sucursal"],
        "Turno" => $fila["Turno"],
        "Cantidad_Venta" => $fila["Cantidad_Venta"],
        "Importe" => $fila["Importe"],
        "Total_Venta" => $fila["Total_Venta"],
        "Descuento" => $fila["DescuentoAplicado"],
        "FormaPago" => $fila["FormaDePago"],
        "Cliente" => $fila["Cliente"],
        "FolioSignoVital" => $fila["FolioSignoVital"],
        "NomServ" => $fila["Nom_Serv"],
        "AgregadoEl" => date("d/m/Y", strtotime($fila["Fecha_venta"])),
        "AgregadoEnMomento" => $fila["AgregadoEl"],
        "AgregadoPor" => $fila["AgregadoPor"]
    ];
}

// Depuración: Verificar el número de registros en el array
error_log("Número de registros en el array data: " . count($data));

// Cerrar la declaración
$stmt->close();

$results = [
    "sEcho" => 1,
    "iTotalRecords" => count($data),
    "iTotalDisplayRecords" => count($data),
    "aaData" => $data
];

echo json_encode($results);

// Cerrar conexión
$conn->close();
?>
