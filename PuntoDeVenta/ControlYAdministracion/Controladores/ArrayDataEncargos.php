<?php
header('Content-Type: application/json');
include("db_connect.php");
include_once "ControladorUsuario.php";

// Obtener el valor de la licencia de la fila, asegurándote de que esté correctamente formateado
$licencia = isset($row['Licencia']) ? $row['Licencia'] : '';

// Consulta segura utilizando una sentencia preparada para la tabla encargos
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
 
// Preparar la declaración
$stmt = $conn->prepare($sql);

// Vincular parámetro - usar la sucursal del usuario actual
$fk_sucursal = isset($row['Fk_Sucursal']) ? $row['Fk_Sucursal'] : '';
$stmt->bind_param("s", $fk_sucursal);

// Ejecutar la declaración
$stmt->execute();

// Obtener resultado
$result = $stmt->get_result();

// Inicializar array para almacenar los resultados
$data = [];

// Procesar resultados
while ($fila = $result->fetch_assoc()) {
    // Calcular el saldo pendiente
    $saldo_pendiente = $fila["precioventa"] - $fila["abono_parcial"];
    
    // Definir el estilo del estado
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
    
    // Construir el array de datos
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
        "Acciones" => '<div class="btn-group">
            <button class="btn btn-primary btn-sm dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-th-list fa-1x"></i>
            </button>
            <div class="dropdown-menu">
                <a data-id="' . $fila["id"] . '" class="btn-DesglosarEncargo dropdown-item">Desglosar Encargo <i class="fas fa-receipt"></i></a>
                <a data-id="' . $fila["id"] . '" class="btn-CobrarEncargo dropdown-item">Cobrar Encargo <i class="fas fa-money-bill"></i></a>
                <a data-id="' . $fila["id"] . '" class="btn-AbonarEncargo dropdown-item">Abonar Saldo <i class="fas fa-plus-circle"></i></a>
            </div>
        </div>'
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
