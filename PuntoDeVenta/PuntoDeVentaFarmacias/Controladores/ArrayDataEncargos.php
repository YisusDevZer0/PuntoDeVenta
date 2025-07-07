<?php
header('Content-Type: application/json');
include("db_connect.php");
include_once "ControladorUsuario.php";

$licencia = isset($row['Licencia']) ? $row['Licencia'] : '';

$sql = "SELECT 
    e.`id`, 
    e.`nombre_paciente`, 
    e.`medicamento`, 
    e.`cantidad`, 
    e.`precioventa`, 
    e.`fecha_encargo`, 
    e.`estado`, 
    e.`costo`, 
    e.`abono_parcial`, 
    e.`NumTicket`, 
    e.`Fk_Sucursal`, 
    e.`Fk_Caja`, 
    e.`Empleado`,
    s.`Nombre_Sucursal`,
    c.`ID_Caja`
FROM 
    encargos e
JOIN 
    Sucursales s ON e.`Fk_Sucursal` = s.`ID_Sucursal`
LEFT JOIN 
    Cajas c ON e.`Fk_Caja` = c.`ID_Caja`
WHERE 
    e.`Fk_Sucursal` = ?";
 
$stmt = $conn->prepare($sql);
$fk_sucursal = isset($row['Fk_Sucursal']) ? $row['Fk_Sucursal'] : '';
$stmt->bind_param("s", $fk_sucursal);
$stmt->execute();
$result = $stmt->get_result();
$data = [];
while ($fila = $result->fetch_assoc()) {
    $saldo_pendiente = $fila["precioventa"] - $fila["abono_parcial"];
    $estado_estilo = '';
    $estado_leyenda = '';
    switch ($fila["estado"]) {
        case 'Pendiente':
            $estado_estilo = 'background-color: #ffc107; color: white; padding: 5px; border-radius: 5px;';
            $estado_leyenda = 'Pendiente';
            break;
        case 'Pagado':
            $estado_estilo = 'background-color: #28a745; color: white; padding: 5px; border-radius: 5px;';
            $estado_leyenda = 'Pagado';
            break;
        case 'Cancelado':
            $estado_estilo = 'background-color: #dc3545; color: white; padding: 5px; border-radius: 5px;';
            $estado_leyenda = 'Cancelado';
            break;
        default:
            $estado_estilo = 'background-color: #6c757d; color: white; padding: 5px; border-radius: 5px;';
            $estado_leyenda = $fila["estado"];
            break;
    }
    $data[] = [
        "IdEncargo" => $fila["id"],
        "NumTicket" => $fila["NumTicket"],
        "NombrePaciente" => $fila["nombre_paciente"],
        "Medicamento" => $fila["medicamento"],
        "Cantidad" => $fila["cantidad"],
        "PrecioVenta" => '$' . number_format($fila["precioventa"], 2),
        "SaldoPendiente" => '$' . number_format($saldo_pendiente, 2),
        "FechaEncargo" => date('d/m/Y', strtotime($fila["fecha_encargo"])),
        "Estado" => "<div style=\"$estado_estilo\">$estado_leyenda</div>",
        "Sucursal" => $fila["Nombre_Sucursal"],
        "Empleado" => $fila["Empleado"],
        "Caja" => $fila["ID_Caja"],
        "Acciones" => '<div class="btn-group-vertical">'+
            '<button type="button" class="btn btn-info btn-sm btn-DesglosarEncargo" data-id="' . $fila["id"] . '" title="Ver detalles">'+
                '<i class="fas fa-eye"></i> Ver'+
            '</button>'+ 
            '<button type="button" class="btn btn-success btn-sm btn-CobrarEncargo" data-id="' . $fila["id"] . '" title="Cobrar encargo">'+
                '<i class="fas fa-money-bill"></i> Cobrar'+
            '</button>'+ 
            '<button type="button" class="btn btn-warning btn-sm btn-AbonarEncargo" data-id="' . $fila["id"] . '" title="Abonar saldo">'+
                '<i class="fas fa-plus-circle"></i> Abonar'+
            '</button>'+ 
        '</div>'
    ];
}
$stmt->close();
$results = [
    "sEcho" => 1,
    "iTotalRecords" => count($data),
    "iTotalDisplayRecords" => count($data),
    "aaData" => $data
];
echo json_encode($results);
$conn->close();
?> 