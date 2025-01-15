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
    $dias_ES = ["Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado", "Domingo"];
    $dias_EN = ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"];
    $nombredia = str_replace($dias_EN, $dias_ES, $dia);
    $meses_ES = ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];
    $meses_EN = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
    $nombreMes = str_replace($meses_EN, $meses_ES, $mes);
    return $nombredia . " " . $numeroDia . " de " . $nombreMes . " de " . $anio;
}

// Obtener el valor de Fk_Sucursal desde la solicitud
$fk_sucursal = isset($row['Fk_Sucursal']) ? $row['Fk_Sucursal'] : '';

if (empty($fk_sucursal)) {
    echo json_encode(["error" => "El valor de Fk_Sucursal está vacío"]);
    exit;
}

// Consulta segura utilizando una sentencia preparada
$sql = "SELECT 
            e.NumTicket, 
            e.nombre_paciente, 
            e.medicamento, 
            e.cantidad, 
            e.precioventa, 
            e.fecha_encargo, 
            e.estado, 
            e.costo, 
            e.abono_parcial, 
            s.nombre_sucursal
        FROM 
            encargos e
        JOIN 
            Sucursales s
        ON 
            e.Fk_Sucursal = s.ID_Sucursal
        WHERE 
            e.Fk_Sucursal = ?
        GROUP BY 
            e.NumTicket
        ORDER BY 
            e.fecha_encargo DESC";

// Preparar la consulta
$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode(["error" => "Error al preparar la consulta: " . $conn->error]);
    exit;
}

// Enlazar el parámetro
$stmt->bind_param("s", $fk_sucursal);

// Ejecutar la consulta
if ($stmt->execute()) {
    $result = $stmt->get_result();
} else {
    echo json_encode(["error" => "Error en la ejecución de la consulta: " . $stmt->error]);
    exit;
}

// Verificar si hay resultados
if ($result->num_rows === 0) {
    echo json_encode(["error" => "No se encontraron resultados para la sucursal: " . $fk_sucursal]);
    exit;
}

// Construir los datos para la respuesta
$data = [];
while ($fila = $result->fetch_assoc()) {
    $data[] = [
        "NumberTicket" => $fila["NumTicket"],
        "Paciente" => $fila["nombre_paciente"],
        "Estado" => $fila["estado"],
        "FechaEncargo" => fechaCastellano($fila["fecha_encargo"]),
       
        "Costo" => $fila["costo"],
        "AbonoParcial" => $fila["abono_parcial"],
        "Sucursal" => $fila["nombre_sucursal"]
          "Saldar" => '<td><a data-id="' . $fila["NumTicket"] . '" class="btn btn-primary btn-sm btn-Abonar dropdown-item" style="background-color: #ef7980 !important; color:white"><i class="fa-solid fa-money-bill-transfer"></i></a></td>'
    ];
}

// Cerrar la declaración
$stmt->close();

// Construir la respuesta JSON
$results = [
    "sEcho" => 1,
    "iTotalRecords" => count($data),
    "iTotalDisplayRecords" => count($data),
    "aaData" => $data
];

echo json_encode($results);
?>
