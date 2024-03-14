<?php
header('Content-Type: application/json');
include("db_connection.php");
include_once "ControladorUsuario.php";

// Obtener el valor de la licencia de la fila, asegurándote de que esté correctamente formateado
$licencia = isset($row['Licencia']) ? $row['Licencia'] : '';

// Consulta segura utilizando una sentencia preparada
$sql = "SELECT * FROM Servicios_POS WHERE Licencia = ?";
 
// Preparar la declaración
$stmt = $conn->prepare($sql);

// Vincular parámetro
$stmt->bind_param("s", $licencia);

// Ejecutar la declaración
$stmt->execute();

// Obtener resultado
$result = $stmt->get_result();

// Inicializar array para almacenar los resultados
$data = [];

// Procesar resultados
while ($fila = $result->fetch_assoc()) {
    // Construir el array de datos
    // Aquí puedes seguir con la lógica que ya tienes para construir los datos de salida
    
    $data[] = [
        "ServicioID" => $fila["ID_Prod_POS"],
        "Nombre_Servicio" => $fila["Cod_Barra"],
        "Estado" => $fila["Nombre_Prod"],
        "AgregadoPor" => $fila["Clave_adicional"],
        "FechaAgregado" => $fila["Clave_Levic"],
        "Sistema" => $fila["Precio_C"],
        "Licencia" => $fila["Precio_Venta"],
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
