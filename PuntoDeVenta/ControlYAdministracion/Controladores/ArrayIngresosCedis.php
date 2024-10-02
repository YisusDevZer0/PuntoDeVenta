<?php
header('Content-Type: application/json');
include("db_connect.php");
include_once "ControladorUsuario.php";

// Consulta segura utilizando una sentencia preparada
$sql = "SELECT 
    IDIngreso, 
    ID_Prod_POS, 
    NumFactura, 
    Cod_Barra, 
    Nombre_Prod, 
    Piezas, 
    Fecha_Caducidad, 
    Lote, 
    AgregadoPor, 
    AgregadoEl 
FROM 
    IngresosCedis";

// Preparar la declaración
$stmt = $conn->prepare($sql);

// Ejecutar la declaración
$stmt->execute();

// Obtener resultado
$result = $stmt->get_result();

// Inicializar array para almacenar los resultados
$data = [];

// Procesar resultados
while ($fila = $result->fetch_assoc()) {
    // Aquí puedes agregar el estilo o la lógica que necesites, si aplica.
    $realizar_corte = '<td><a data-id="' . $fila["IDIngreso"] . '" class="btn btn-success btn-sm btn-AutorizaIngreso" style="color:white;"><i class="fa-solid fa-check"></i></a></td>';
    $editar_datos_ingreso = '<td><a data-id="' . $fila["IDIngreso"] . '" class="btn btn-primary btn-sm btn-EditaIngreso" style="color:white;"><i class="fa-solid fa-file-pen"></i></a></td>';
    $eliminar_ingreso = '<td><a data-id="' . $fila["IDIngreso"] . '" class="btn btn-danger btn-sm btn-EliminaIngreso" style="color:white;"><i class="fa-solid fa-trash"></i></a></td>';

    // Construir el array de datos incluyendo las columnas de la consulta
    $data[] = [
        "IDIngreso" => $fila["IDIngreso"],
        "ID_Prod_POS" => $fila["ID_Prod_POS"],
        "NumFactura" => $fila["NumFactura"],
        "Cod_Barra" => $fila["Cod_Barra"],
        "Nombre_Prod" => $fila["Nombre_Prod"],
        "Piezas" => $fila["Piezas"],
        "Fecha_Caducidad" => $fila["Fecha_Caducidad"],
        "Lote" => $fila["Lote"],
        "AgregadoPor" => $fila["AgregadoPor"],
        "AgregadoEl" => $fila["AgregadoEl"],
     
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
