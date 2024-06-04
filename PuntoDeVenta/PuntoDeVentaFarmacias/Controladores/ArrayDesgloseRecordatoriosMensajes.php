<?php
header('Content-Type: application/json');
include("db_connect.php");
include_once "ControladorUsuario.php";

// Obtener el valor de la licencia de la fila, asegurándote de que esté correctamente formateado
$licencia = isset($row['Licencia']) ? $row['Licencia'] : '';
$Fk_Sucursal = isset($row['Fk_Sucursal']) ? $row['Fk_Sucursal'] : '';

// Consulta segura utilizando una sentencia preparada
$sql = "SELECT 
RP.ID_Notificacion, 
RP.Encabezado, 
RP.TipoMensaje, 
RP.Mensaje_Recordatorio, 
RP.Registrado, 
RP.Sistema, 
RP.Sucursal, 
CASE 
    WHEN RP.Estado = 0 THEN 'Pendiente'
    ELSE RP.Estado 
END AS Estado, 
RP.Licencia,
S.Nombre_Sucursal
FROM 
Recordatorios_Pendientes RP
JOIN 
Sucursales S ON RP.Sucursal = S.ID_Sucursal AND RP.TipoMensaje='Mensaje'
AND RP.Licencia = ? AND RP.Sucursal = ?"; // Ajuste de la condición WHERE

// Preparar la declaración
$stmt = $conn->prepare($sql);

// Vincular parámetros
$stmt->bind_param("ss", $licencia, $Fk_Sucursal); // Cambio en la cantidad de parámetros

// Ejecutar la declaración
$stmt->execute();

// Obtener resultado
$result = $stmt->get_result();

// Inicializar array para almacenar los resultados
$data = [];

// Procesar resultados
while ($fila = $result->fetch_assoc()) {
    // Construir el array de datos
    $data[] = [
        "Id" => $fila["ID_Notificacion"],
        "TipMensaje" => $fila["TipoMensaje"],
        "MensajeRecordatorio" => $fila["Mensaje_Recordatorio"],
        "NombreSucursal" => $fila["Nombre_Sucursal"],
        "Estatus" => $fila["Estado"],
        
        "Editar" => "<a href='https://saludapos.com/AdminPOS/CoincidenciaSucursales?Disid=" . base64_encode($fila["ID_Prod_POS"]) . "' type='button' class='btn btn-info btn-sm'><i class='fas fa-capsules'></i></a>",
        
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
