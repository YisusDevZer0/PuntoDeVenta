<?php
include_once "db_connect.php";
include_once "ControladorUsuario.php";

// Iniciar la sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Obtener el valor de Fk_Sucursal desde la sesión
$fk_sucursal = isset($_SESSION['Fk_Sucursal']) ? $_SESSION['Fk_Sucursal'] : '';

// Verificar si la sucursal tiene un valor válido
if (empty($fk_sucursal)) {
    echo json_encode(["error" => "El valor de Fk_Sucursal está vacío en la sesión"]);
    exit;
}

$sql = "SELECT 
    e.id,
    e.NumTicket,
    e.nombre_paciente,
    e.medicamento,
    e.cantidad,
    e.precioventa,
    e.fecha_encargo,
    e.estado,
    e.abono_parcial,
    (e.precioventa - e.abono_parcial) as saldo_pendiente,
    s.Nombre_Sucursal,
    c.Empleado,
    e.FormaDePago
FROM 
    encargos e
LEFT JOIN 
    Sucursales s ON e.Fk_Sucursal = s.ID_Sucursal 
LEFT JOIN 
    Cajas c ON e.Fk_Caja = c.ID_Caja
WHERE 
    e.Fk_Sucursal = ?";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode(["error" => "Error al preparar la consulta"]);
    exit;
}

$stmt->bind_param("s", $fk_sucursal);
$stmt->execute();
$result = $stmt->get_result();

$data = array();
$c = 0;

while ($fila = $result->fetch_assoc()) {
    $data[$c]["id"] = $fila["id"];
    $data[$c]["NumTicket"] = $fila["NumTicket"];
    $data[$c]["nombre_paciente"] = $fila["nombre_paciente"];
    $data[$c]["medicamento"] = $fila["medicamento"];
    $data[$c]["cantidad"] = $fila["cantidad"];
    $data[$c]["precioventa"] = number_format($fila["precioventa"], 2);
    $data[$c]["fecha_encargo"] = date("d/m/Y", strtotime($fila["fecha_encargo"]));
    $data[$c]["estado"] = $fila["estado"];
    $data[$c]["abono_parcial"] = number_format($fila["abono_parcial"], 2);
    $data[$c]["saldo_pendiente"] = number_format($fila["saldo_pendiente"], 2);
    $data[$c]["Nombre_Sucursal"] = $fila["Nombre_Sucursal"];
    $data[$c]["Empleado"] = $fila["Empleado"];
    $data[$c]["FormaDePago"] = $fila["FormaDePago"];
    
    // Agregar botones de acción
    $data[$c]["acciones"] = '<div class="btn-group" role="group">
        <button type="button" class="btn btn-info btn-sm btn-DesglosarEncargo" data-id="' . $fila["id"] . '" title="Ver detalles">
            <i class="fas fa-eye"></i>
        </button>
        <button type="button" class="btn btn-success btn-sm btn-CobrarEncargo" data-id="' . $fila["id"] . '" title="Cobrar encargo">
            <i class="fas fa-money-bill"></i>
        </button>
        <button type="button" class="btn btn-warning btn-sm btn-AbonarEncargo" data-id="' . $fila["id"] . '" title="Abonar saldo">
            <i class="fas fa-plus-circle"></i>
        </button>
    </div>';
    
    $c++;
}

$results = [
    "sEcho" => 1,
    "iTotalRecords" => count($data),
    "iTotalDisplayRecords" => count($data),
    "aaData" => $data
];

echo json_encode($results);
?> 