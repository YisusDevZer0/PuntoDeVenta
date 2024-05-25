<?php
header('Content-Type: application/json');
include("db_connect.php");
include_once "ControladorUsuario.php";

// Inicialización de $row como un arreglo vacío si no está definida anteriormente
$row = isset($row) ? $row : [];

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
        WHEN t.Estado = 0 THEN '<span style=\"color: red;\">Sin realizar</span>'
        ELSE 'Completado'
    END AS Estado
FROM 
    TareasPorHacer t
JOIN 
    Sucursales s ON t.Sucursal = s.ID_Sucursal
WHERE 
    t.Licencia = ? 
    AND t.Sucursal = ?";

// Preparar la declaración
$stmt = $conn->prepare($sql);

if (!$stmt) {
    // En caso de error en la preparación de la declaración, devolver error 500 y mensaje
    http_response_code(500);
    echo json_encode(["error" => "Error en la preparación de la declaración SQL: " . $conn->error]);
    exit();
}

// Vincular parámetros
$stmt->bind_param("si", $licencia, $Fk_Sucursal); // Asegúrate de que los tipos de datos sean correctos

// Ejecutar la declaración
$stmt->execute();

// Obtener resultado
$result = $stmt->get_result();

if (!$result) {
    // En caso de error en la ejecución de la declaración, devolver error 500 y mensaje
    http_response_code(500);
    echo json_encode(["error" => "Error en la ejecución de la declaración SQL: " . $stmt->error]);
    exit();
}

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
        "Editar" => '<td><a data-id="' . $fila["ID_Tarea"] . '" class="btn btn-success btn-sm btn-edita " style="background-color: #0172b6 !important;" ><i class="fa-solid fa-pen-to-square"></i></a></td>',
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
