<?php
header('Content-Type: application/json');
include("db_connect.php");
include_once "ControladorUsuario.php";

// Obtener el valor de la licencia de la fila, asegurándote de que esté correctamente formateado
$licencia = isset($row['Licencia']) ? $row['Licencia'] : '';
$Fk_Sucursal = isset($row['Fk_Sucursal']) ? $row['Fk_Sucursal'] : '';

// Consulta segura utilizando una sentencia preparada
$sql = "SELECT 
t.ID_Tarea, 
t.NombreTarea, 
t.Descripcion, 
t.Registrado, 
t.Sistema, 
t.Sucursal,
t.Licencia, 
s.Nombre_Sucursal, 
CASE 
    WHEN t.Estado = 0 THEN 'Sin realizar'
    ELSE 'Completado'
END AS Estado
FROM 
TareasPorHacer t
JOIN 
Sucursales s ON t.Sucursal = s.ID_Sucursal
WHERE 
t.Licencia = 'Doctor Pez' 
AND t.Sucursal = 1;" ;// Ajuste de la condición WHERE

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
        "Id" => $fila["ID_Tarea"],
        "TipMensaje" => $fila["NombreTarea"],
        "MensajeRecordatorio" => $fila["Descripcion"],
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
