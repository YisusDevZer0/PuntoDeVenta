<?php
header('Content-Type: application/json');
include("db_connect.php");
include_once "ControladorUsuario.php";

// Obtener el valor de la licencia de la fila, asegurándote de que esté correctamente formateado
$licencia = isset($row['Licencia']) ? $row['Licencia'] : '';

// Consulta segura utilizando una sentencia preparada
$sql = "SELECT Cat_ID, Nom_Cat, Estado, Cod_Estado, Agregado_Por, Agregadoel, Sistema, Licencia FROM Categorias_POS WHERE Licencia = ?";

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
    $data[] = [
        "CategoriaID" => $fila["Cat_ID"],
        "Nombre_Categoria" => $fila["Nom_Cat"],
        "Estado" => $fila["Estado"],
      
        "AgregadoPor" => $fila["Agregado_Por"],
        "FechaAgregado" => $fila["Agregadoel"],
        "Sistema" => $fila["Sistema"],
        "Licencia" => $fila["Licencia"],
        "Editar" => '<td><a data-id="' . $fila["Cat_ID"] . '" class="btn btn-success btn-sm btn-edita " style="background-color: #0172b6 !important;" ><i class="fa-solid fa-pen-to-square"></i></a></td>',
        "Eliminar" => '<td><a data-id="' . $fila["Cat_ID"] . '" class="btn btn-danger btn-sm btn-elimina " style="background-color: #ff3131 !important;" ><i class="fa-solid fa-trash"></i></a></td>'
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