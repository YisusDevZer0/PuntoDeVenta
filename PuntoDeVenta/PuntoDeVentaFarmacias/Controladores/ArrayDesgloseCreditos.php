<?php
header('Content-Type: application/json');
include("db_connect.php");
include_once "ControladorUsuario.php";

// Inicialización de $row como un arreglo vacío si no está definida anteriormente
$row = isset($row) ? $row : [];

// Obtener el valor de la licencia y la sucursal de la fila, asegurándote de que estén correctamente formateados
$licencia = isset($row['Licencia']) ? $row['Licencia'] : '';
$Fk_Sucursal = isset($row['Fk_Sucursal']) ? $row['Fk_Sucursal'] : '';

// Consulta segura utilizando una sentencia preparada
$sql = "SELECT 
    c.Folio_Credito, 
    c.Nombre_Cred, 
    c.Edad, 
    c.Sexo, 
    c.Telefono, 
    c.Fecha_Apertura, 
    c.Fecha_Termino, 
    c.Fk_Sucursal, 
    s.Nombre_Sucursal, 
    c.Agrega, 
    c.AgregadoEl, 
    c.Sistema, 
    c.Licencia, 
    c.Saldo
FROM 
    Creditos_POS c
JOIN 
    Sucursales s ON c.Fk_Sucursal = s.ID_Sucursal
WHERE 
    c.Licencia = ? 
    AND c.Fk_Sucursal = ?";

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
        "Folio_Credito" => $fila["Folio_Credito"],
        "Nombre_Cred" => $fila["Nombre_Cred"],
        "Edad" => $fila["Edad"],
        "Sexo" => $fila["Sexo"],
        "Telefono" => $fila["Telefono"],
        "Fecha_Apertura" => $fila["Fecha_Apertura"],
        "Fecha_Termino" => $fila["Fecha_Termino"],
        "Fk_Sucursal" => $fila["Fk_Sucursal"],
        "Nombre_Sucursal" => $fila["Nombre_Sucursal"],
        "Agrega" => $fila["Agrega"],
        "AgregadoEl" => $fila["AgregadoEl"],
        "Sistema" => $fila["Sistema"],
        "Licencia" => $fila["Licencia"],
        "Saldo" => $fila["Saldo"]
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
