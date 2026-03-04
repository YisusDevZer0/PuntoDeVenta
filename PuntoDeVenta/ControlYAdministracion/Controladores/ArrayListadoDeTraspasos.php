<?php
header('Content-Type: application/json');
include("db_connect.php");
include_once "ControladorUsuario.php";

// Parámetros de filtro
$fechaDesde = isset($_GET['fechaDesde']) ? trim($_GET['fechaDesde']) : '';
$fechaHasta = isset($_GET['fechaHasta']) ? trim($_GET['fechaHasta']) : '';
$sucursalOrigen = isset($_GET['sucursalOrigen']) ? trim($_GET['sucursalOrigen']) : '';
$sucursalDestino = isset($_GET['sucursalDestino']) ? trim($_GET['sucursalDestino']) : '';
$estatus = isset($_GET['estatus']) ? trim($_GET['estatus']) : '';

// Consulta segura con JOIN a la tabla Sucursales para obtener nombres de origen y destino
$sql = "SELECT 
    tyc.TraspaNotID, 
    tyc.Folio_Ticket, 
    tyc.Cod_Barra, 
    tyc.Nombre_Prod, 
    tyc.Cantidad, 
    tyc.Fk_sucursal, 
    suc_origen.Nombre_Sucursal AS Sucursal_Origen,
    tyc.Fk_SucursalDestino, 
    suc_destino.Nombre_Sucursal AS Sucursal_Destino,
    tyc.Total_VentaG, 
    tyc.Pc, 
    tyc.TipoDeMov, 
    tyc.Fecha_venta, 
    tyc.Estatus, 
    tyc.Sistema, 
    tyc.AgregadoPor, 
    tyc.AgregadoEl, 
    tyc.ID_H_O_D
FROM 
    TraspasosYNotasC tyc
INNER JOIN 
    Sucursales suc_origen ON tyc.Fk_sucursal = suc_origen.ID_Sucursal
INNER JOIN 
    Sucursales suc_destino ON tyc.Fk_SucursalDestino = suc_destino.ID_Sucursal
WHERE 1=1";

$params = [];
$types = "";

// Si no hay filtros de fecha, usar mes/año actual por defecto (comportamiento anterior)
if ($fechaDesde !== '' && $fechaHasta !== '') {
    $sql .= " AND DATE(tyc.AgregadoEl) >= ? AND DATE(tyc.AgregadoEl) <= ?";
    $params[] = $fechaDesde;
    $params[] = $fechaHasta;
    $types .= "ss";
} elseif ($fechaDesde === '' && $fechaHasta === '') {
    $sql .= " AND MONTH(tyc.AgregadoEl) = MONTH(CURRENT_DATE) AND YEAR(tyc.AgregadoEl) = YEAR(CURRENT_DATE)";
}

if ($sucursalOrigen !== '') {
    $sql .= " AND tyc.Fk_sucursal = ?";
    $params[] = $sucursalOrigen;
    $types .= "i";
}
if ($sucursalDestino !== '') {
    $sql .= " AND tyc.Fk_SucursalDestino = ?";
    $params[] = $sucursalDestino;
    $types .= "i";
}
if ($estatus !== '') {
    $sql .= " AND tyc.Estatus = ?";
    $params[] = $estatus;
    $types .= "s";
}

$sql .= " ORDER BY tyc.AgregadoEl DESC";

// Preparar la consulta
$stmt = $conn->prepare($sql);
if ($params) {
    $stmt->bind_param($types, ...$params);
}

// Ejecutar la consulta
$stmt->execute();

// Obtener resultado
$result = $stmt->get_result();

// Inicializar array para almacenar los resultados
$data = [];

// Procesar resultados
while ($fila = $result->fetch_assoc()) {
    $data[] = [
        "TraspaNotID" => $fila["TraspaNotID"],
        "Folio_Ticket" => $fila["Folio_Ticket"],
        "Cod_Barra" => $fila["Cod_Barra"],
        "Nombre_Prod" => $fila["Nombre_Prod"],
        "Cantidad" => $fila["Cantidad"],
        "Fk_sucursal" => $fila["Fk_sucursal"],
        "Sucursal_Origen" => $fila["Sucursal_Origen"],
        "Fk_SucursalDestino" => $fila["Fk_SucursalDestino"],
        "Sucursal_Destino" => $fila["Sucursal_Destino"],
        "Total_VentaG" => $fila["Total_VentaG"],
        "Pc" => $fila["Pc"],
        "TipoDeMov" => $fila["TipoDeMov"],
        "Fecha_venta" => $fila["Fecha_venta"],
        "Estatus" => $fila["Estatus"],
        "Sistema" => $fila["Sistema"],
        "AgregadoPor" => $fila["AgregadoPor"],
        "AgregadoEl" => $fila["AgregadoEl"],
  
    ];
}

// Cerrar la declaración
$stmt->close();

// Construir el array de resultados para la respuesta JSON
$results = [
    "sEcho" => 1,
    "iTotalRecords" => count($data),
    "iTotalDisplayRecords" => count($data),
    "aaData" => $data
];

// Imprimir la respuesta JSON
echo json_encode($results);

// Cerrar conexión
$conn->close();
?>
