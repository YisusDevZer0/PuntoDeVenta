<?php
header('Content-Type: application/json');
include("db_connect.php");

// Obtener la fecha actual en formato 'YYYY-MM-DD'
$fechaActual = date("Y-m-d");

// Consulta SQL adaptada con las variables proporcionadas
$sql = "SELECT Registro_Watts, Fecha_registro, Sucursal, Comentario, Registro, Agregadoel, Licencia, file_name 
        FROM Registros_Energia 
        WHERE Fecha_registro = '$fechaActual' AND Sucursal = '" . $row['Nombre_Sucursal'] . "'";

// Ejecutar la consulta
$result = mysqli_query($conn, $sql);

$data = [];

if ($result && $result->num_rows > 0) {
    while ($fila = $result->fetch_assoc()) {
        $data[] = [
            "Id_Registro" => $fila["Id_Registro"],
            "Registro_Watts" => $fila["Registro_Watts"],
            "Fecha_registro" => $fila["Fecha_registro"],
            "Sucursal" => $fila["Sucursal"],
            "Comentario" => $fila["Comentario"],
            "Registro" => $fila["Registro"],
            "Agregadoel" => $fila["Agregadoel"],
            "Licencia" => $fila["Licencia"],
            "Foto" => "<img alt='avatar' class='img-thumbnail' src='https://doctorpez.mx/PuntoDeVenta/FotosMedidores/{$fila['file_name']}'>"
        ];
    }
}

// Construir el array de resultados para la respuesta JSON
$results = [
    "sEcho" => 1,
    "iTotalRecords" => count($data),
    "iTotalDisplayRecords" => count($data),
    "aaData" => $data
];

// Imprimir la respuesta JSON
echo json_encode($results);

// Cerrar la conexión
$conn->close();
?>
