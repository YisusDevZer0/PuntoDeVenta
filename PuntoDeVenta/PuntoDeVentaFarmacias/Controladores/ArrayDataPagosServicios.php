<?php
header('Content-Type: application/json');
include("db_connect.php");
include_once "ControladorUsuario.php";

// Obtener el valor de la licencia de la fila, asegurándote de que esté correctamente formateado
$licencia = isset($row['Licencia']) ? $row['Licencia'] : '';

// Consulta segura utilizando una sentencia preparada para la tabla PagosServicios
$sql = "SELECT 
    ps.`id`, 
    ps.`nombre_paciente`, 
    ps.`Servicio`, 
    ps.`costo`, 
    ps.`NumTicket`, 
    ps.`Fk_Sucursal`, 
    ps.`Fk_Caja`, 
    ps.`Empleado`,
    ps.`FormaDePago`,
    s.`Nombre_Sucursal`,
    c.`ID_Caja`
FROM 
    PagosServicios ps
JOIN 
    Sucursales s ON ps.`Fk_Sucursal` = s.`ID_Sucursal`
LEFT JOIN 
    Cajas c ON ps.`Fk_Caja` = c.`ID_Caja`
WHERE 
    ps.`Fk_Sucursal` = ?";
 
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
    // Definir el estilo del estado
    $estado_estilo = '';
    $estado_leyenda = '';
    
    // Construir el array de datos
    $data[] = [
        "IdPago" => $fila["id"],
        "NumTicket" => $fila["NumTicket"],
        "NombrePaciente" => $fila["nombre_paciente"],
        "Servicio" => $fila["Servicio"],
        "Costo" => '$' . number_format($fila["costo"], 2),
        "FormaDePago" => $fila["FormaDePago"],
        "Sucursal" => $fila["Nombre_Sucursal"],
        "Empleado" => $fila["Empleado"],
        "Caja" => $fila["ID_Caja"],
        "Acciones" => '<div class="btn-group-vertical">
            <button type="button" class="btn btn-info btn-sm btn-VerPago" data-id="' . $fila["id"] . '" title="Ver detalles">
                <i class="fas fa-eye"></i> Ver
            </button>
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
