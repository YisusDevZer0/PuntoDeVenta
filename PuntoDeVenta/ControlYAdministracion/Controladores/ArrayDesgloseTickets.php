<?php
header('Content-Type: application/json');
include("db_connection.php");
include_once "ControladorUsuario.php";

// Función para convertir la fecha a formato español
function fechaCastellano($fecha) {
    $fecha = substr($fecha, 0, 10);
    $numeroDia = date('d', strtotime($fecha));
    $dia = date('l', strtotime($fecha));
    $mes = date('F', strtotime($fecha));
    $anio = date('Y', strtotime($fecha));
    $dias_ES = array("Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado", "Domingo");
    $dias_EN = array("Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday");
    $nombredia = str_replace($dias_EN, $dias_ES, $dia);
    $meses_ES = array("Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");
    $meses_EN = array("January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
    $nombreMes = str_replace($meses_EN, $meses_ES, $mes);
    return $nombredia . " " . $numeroDia . " de " . $nombreMes . " de " . $anio;
}
// Obtener el valor de Fk_Sucursal desde la solicitud
$fk_sucursal = isset($row['Fk_Sucursal']) ? $row['Fk_Sucursal'] : '';

// Verificar si la sucursal tiene un valor válido
if (empty($fk_sucursal)) {
    echo json_encode(["error" => "El valor de Fk_Sucursal está vacío"]);
    exit;
}

// Consulta segura utilizando una sentencia preparada con el filtro de sucursal
$sql = "SELECT Ventas_POS.Folio_Ticket, Ventas_POS.FolioSucursal, Ventas_POS.Fk_Caja, Ventas_POS.Venta_POS_ID, 
        Ventas_POS.Identificador_tipo, Ventas_POS.Cod_Barra, Ventas_POS.Clave_adicional, Ventas_POS.Nombre_Prod, 
        Ventas_POS.Cantidad_Venta, Ventas_POS.Fk_sucursal, Ventas_POS.AgregadoPor, Ventas_POS.AgregadoEl, 
        Ventas_POS.Total_Venta, Ventas_POS.Lote, Ventas_POS.ID_H_O_D, Sucursales.ID_Sucursal, Sucursales.Nombre_Sucursal 
        FROM Ventas_POS 
        JOIN Sucursales ON Ventas_POS.Fk_sucursal = Sucursales.ID_Sucursal 
        WHERE MONTH(Ventas_POS.AgregadoEl) = MONTH(CURRENT_DATE) 
        AND YEAR(Ventas_POS.AgregadoEl) = YEAR(CURRENT_DATE)
        AND Ventas_POS.Fk_sucursal = ? -- Filtrar por sucursal
        GROUP BY Ventas_POS.Folio_Ticket, Ventas_POS.FolioSucursal
        ORDER BY Ventas_POS.AgregadoEl DESC;";

// Preparar la declaración
$stmt = $conn->prepare($sql);

// Verificar si la consulta fue preparada correctamente
if (!$stmt) {
    echo json_encode(["error" => "Error al preparar la consulta: " . $conn->error]);
    exit;
}

// Enlazar el parámetro Fk_Sucursal
$stmt->bind_param("s", $fk_sucursal);

// Ejecutar y verificar la consulta
if ($stmt->execute()) {
    $result = $stmt->get_result();
} else {
    // Si la ejecución falla, imprime el error
    echo json_encode(["error" => "Error en la ejecución de la consulta: " . $stmt->error]);
    exit;
}

// Verificar si hay resultados
if ($result->num_rows === 0) {
    echo json_encode(["error" => "No se encontraron resultados para la sucursal: " . $fk_sucursal]);
    exit;
}

// Inicializar array para almacenar los resultados
$data = [];

while ($fila = $result->fetch_assoc()) {
    $data[] = [
        "NumberTicket" => $fila["Folio_Ticket"],
        
        "Fecha" => fechaCastellano($fila["AgregadoEl"]),
        "Hora" => date("g:i:s a", strtotime($fila["AgregadoEl"])),
        "Vendedor" => $fila["AgregadoPor"],
        "Desglose" => '<td><a data-id="' . $fila["Folio_Ticket"] . '" class="btn btn-success btn-sm btn-desglose dropdown-item" style="background-color: #ef7980!important; color:white"><i class="fa-solid fa-trash"></i></a></td>',
        
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
?>
