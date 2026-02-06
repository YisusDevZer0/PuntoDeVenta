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

// Obtener parámetros de filtro desde GET o POST
// Usar array_key_exists para distinguir entre "no enviado" y "enviado como vacío"
$filtro_sucursal_enviado = array_key_exists('filtro_sucursal', $_REQUEST);
$filtro_sucursal = $filtro_sucursal_enviado ? $_REQUEST['filtro_sucursal'] : null;
$filtro_mes = isset($_REQUEST['filtro_mes']) ? $_REQUEST['filtro_mes'] : '';
$filtro_anio = isset($_REQUEST['filtro_anio']) ? $_REQUEST['filtro_anio'] : '';
$filtro_fecha_inicio = isset($_REQUEST['filtro_fecha_inicio']) ? $_REQUEST['filtro_fecha_inicio'] : '';
$filtro_fecha_fin = isset($_REQUEST['filtro_fecha_fin']) ? $_REQUEST['filtro_fecha_fin'] : '';

// Obtener el valor de Fk_Sucursal desde la sesión (valor por defecto)
$fk_sucursal = isset($row['Fk_Sucursal']) ? $row['Fk_Sucursal'] : '';

// Determinar si debemos filtrar por sucursal
// - Por defecto (al entrar, sin parámetros): mostrar TODAS las sucursales (libre)
// - Si filtro_sucursal fue enviado como "" (cadena vacía) -> mostrar TODAS las sucursales
// - Si filtro_sucursal tiene un valor específico -> filtrar por esa sucursal
$usar_filtro_sucursal = false;
$sucursal_filtro = '';

if ($filtro_sucursal_enviado && !empty($filtro_sucursal)) {
    // Solo filtrar por sucursal si se envió explícitamente un valor
    $usar_filtro_sucursal = true;
    $sucursal_filtro = $filtro_sucursal;
}
// Si no se envió el parámetro o está vacío -> no filtrar (mostrar todo, libre)

// Construir la consulta con filtros dinámicos
$where_conditions = [];
$params = [];
$types = "";

// Solo agregar filtro de sucursal si se debe usar
if ($usar_filtro_sucursal && !empty($sucursal_filtro)) {
    $where_conditions[] = "Ventas_POS.Fk_sucursal = ?";
    $params[] = $sucursal_filtro;
    $types .= "s";
}

// Filtro por mes y año
if (!empty($filtro_mes) && !empty($filtro_anio)) {
    $where_conditions[] = "MONTH(Ventas_POS.AgregadoEl) = ?";
    $where_conditions[] = "YEAR(Ventas_POS.AgregadoEl) = ?";
    $params[] = intval($filtro_mes);
    $params[] = intval($filtro_anio);
    $types .= "ii";
} elseif (empty($filtro_fecha_inicio) && empty($filtro_fecha_fin)) {
    // Si no hay filtros específicos, usar el mes y año actual por defecto
    $where_conditions[] = "MONTH(Ventas_POS.AgregadoEl) = MONTH(CURRENT_DATE)";
    $where_conditions[] = "YEAR(Ventas_POS.AgregadoEl) = YEAR(CURRENT_DATE)";
}

// Filtro por rango de fechas
if (!empty($filtro_fecha_inicio)) {
    $where_conditions[] = "DATE(Ventas_POS.AgregadoEl) >= ?";
    $params[] = $filtro_fecha_inicio;
    $types .= "s";
}

if (!empty($filtro_fecha_fin)) {
    $where_conditions[] = "DATE(Ventas_POS.AgregadoEl) <= ?";
    $params[] = $filtro_fecha_fin;
    $types .= "s";
}

// Construir la cláusula WHERE
$where_clause = !empty($where_conditions) ? "WHERE " . implode(" AND ", $where_conditions) : "";

// Consulta para obtener los tickets únicos
$sql = "SELECT Ventas_POS.Folio_Ticket, Ventas_POS.FolioSucursal, Ventas_POS.Fk_Caja, Ventas_POS.Venta_POS_ID, 
        Ventas_POS.Identificador_tipo, Ventas_POS.Cod_Barra, Ventas_POS.Clave_adicional, Ventas_POS.Nombre_Prod, 
        Ventas_POS.Cantidad_Venta, Ventas_POS.Fk_sucursal, Ventas_POS.AgregadoPor, Ventas_POS.AgregadoEl, 
        Ventas_POS.Total_Venta, Ventas_POS.Lote, Ventas_POS.ID_H_O_D, Sucursales.ID_Sucursal, Sucursales.Nombre_Sucursal 
        FROM Ventas_POS 
        JOIN Sucursales ON Ventas_POS.Fk_sucursal = Sucursales.ID_Sucursal 
        $where_clause
        GROUP BY Ventas_POS.Folio_Ticket, Ventas_POS.FolioSucursal
        ORDER BY Ventas_POS.AgregadoEl DESC;";

// Preparar la declaración
$stmt = $conn->prepare($sql);

// Verificar si la consulta fue preparada correctamente
if (!$stmt) {
    echo json_encode([
        "sEcho" => 1,
        "iTotalRecords" => 0,
        "iTotalDisplayRecords" => 0,
        "aaData" => [],
        "error" => "Error al preparar la consulta: " . $conn->error,
        "estadisticas" => [
            "total_tickets" => 0,
            "total_ventas" => 0,
            "tickets_hoy" => 0,
            "promedio_ticket" => 0
        ]
    ]);
    exit;
}

// Enlazar los parámetros si hay alguno
if (!empty($params) && !empty($types)) {
    $stmt->bind_param($types, ...$params);
}

// Ejecutar y verificar la consulta
if ($stmt->execute()) {
    $result = $stmt->get_result();
} else {
    echo json_encode([
        "sEcho" => 1,
        "iTotalRecords" => 0,
        "iTotalDisplayRecords" => 0,
        "aaData" => [],
        "error" => "Error en la ejecución de la consulta: " . $stmt->error,
        "estadisticas" => [
            "total_tickets" => 0,
            "total_ventas" => 0,
            "tickets_hoy" => 0,
            "promedio_ticket" => 0
        ]
    ]);
    exit;
}

// Consulta para calcular totales de cada ticket (estadísticas)
$sql_totales = "SELECT Folio_Ticket, FolioSucursal, SUM(Total_Venta) as total_ticket, DATE(AgregadoEl) as fecha_ticket
                FROM Ventas_POS 
                $where_clause
                GROUP BY Folio_Ticket, FolioSucursal, DATE(AgregadoEl)";

$stmt_totales = $conn->prepare($sql_totales);
$total_ventas = 0;
$tickets_hoy = 0;
$fecha_hoy = date('Y-m-d');
$tickets_unicos = [];

if ($stmt_totales) {
    if (!empty($params) && !empty($types)) {
        $stmt_totales->bind_param($types, ...$params);
    }
    if ($stmt_totales->execute()) {
        $result_totales = $stmt_totales->get_result();
        while ($row_total = $result_totales->fetch_assoc()) {
            $folio_key = $row_total['Folio_Ticket'] . '_' . $row_total['FolioSucursal'];
            if (!isset($tickets_unicos[$folio_key])) {
                $tickets_unicos[$folio_key] = $row_total['total_ticket'];
                $total_ventas += floatval($row_total['total_ticket']);
                
                if ($row_total['fecha_ticket'] == $fecha_hoy) {
                    $tickets_hoy++;
                }
            }
        }
    }
    $stmt_totales->close();
}

// Inicializar array para almacenar los resultados
$data = [];

while ($fila = $result->fetch_assoc()) {
    $data[] = [
        "NumberTicket" => $fila["Folio_Ticket"],
        "Fecha" => fechaCastellano($fila["AgregadoEl"]),
        "Hora" => date("g:i:s a", strtotime($fila["AgregadoEl"])),
        "Vendedor" => $fila["AgregadoPor"],
        "Desglose" => '<td><a data-id="' . $fila["Folio_Ticket"] . '" class="btn btn-success btn-sm btn-desglose dropdown-item" style="background-color: #ef7980!important; color:white"><i class="fas fa-receipt"></i></a></td>',
        "Reimpresion" => '<td><a data-id="' . $fila["Folio_Ticket"] . '" class="btn btn-primary btn-sm btn-Reimpresion dropdown-item" style="background-color: #ef7980 !important; color:white"><i class="fas fa-print"></i></a></td>',
        "Eliminar" => '<td><a data-id="' . $fila["Folio_Ticket"] . '" class="btn btn-success btn-sm btn-eliminar dropdown-item" style="background-color: #ef7980!important; color:white"><i class="fa-solid fa-trash"></i></a></td>'
    ];
}

// Cerrar la declaración
$stmt->close();

// Calcular promedio por ticket
$total_tickets = count($data);
$promedio_ticket = $total_tickets > 0 ? $total_ventas / $total_tickets : 0;

// Construir el array de resultados para la respuesta JSON
$results = [
    "sEcho" => 1,
    "iTotalRecords" => $total_tickets,
    "iTotalDisplayRecords" => $total_tickets,
    "aaData" => $data,
    "estadisticas" => [
        "total_tickets" => $total_tickets,
        "total_ventas" => round($total_ventas, 2),
        "tickets_hoy" => $tickets_hoy,
        "promedio_ticket" => round($promedio_ticket, 2)
    ]
];

// Imprimir la respuesta JSON
echo json_encode($results);
?>
