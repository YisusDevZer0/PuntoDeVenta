<?php
include_once "db_connect.php";
include_once "ControladorUsuario.php";

// Obtener el término de búsqueda enviado por AJAX
$termino = $_POST['term'];

// Consultar la base de datos para obtener los pacientes que coincidan con el término de búsqueda
$sql = "SELECT ID_Data_Paciente, Nombre_Paciente, Fecha_Nacimiento, Edad, Sexo, 
        Alergias, Telefono, Correo, Fk_Sucursal, SucursalVisita, Licencia, 
        Ingreso, Ingresadoen, Sistema 
        FROM Data_Pacientes 
        WHERE Nombre_Paciente LIKE ? 
        OR Telefono LIKE ? 
        OR Correo LIKE ?
        ORDER BY Nombre_Paciente ASC
        LIMIT 10";

$stmt = $conn->prepare($sql);
$terminoBusqueda = "%" . $termino . "%";
$stmt->bind_param("sss", $terminoBusqueda, $terminoBusqueda, $terminoBusqueda);
$stmt->execute();
$result = $stmt->get_result();

$pacientes = array();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Crear un array con la información del paciente
        $paciente = array(
            "id" => $row["ID_Data_Paciente"],
            "value" => $row["Nombre_Paciente"], // Este es el valor que se mostrará en el autocomplete
            "label" => $row["Nombre_Paciente"], // Este es el texto que se mostrará en las sugerencias
            "fecha_nacimiento" => $row["Fecha_Nacimiento"],
            "edad" => $row["Edad"],
            "sexo" => $row["Sexo"],
            "alergias" => $row["Alergias"],
            "telefono" => $row["Telefono"],
            "correo" => $row["Correo"],
            "sucursal" => $row["Fk_Sucursal"],
            "sucursal_visita" => $row["SucursalVisita"],
            "licencia" => $row["Licencia"],
            "ingreso" => $row["Ingreso"],
            "ingresado_en" => $row["Ingresadoen"],
            "sistema" => $row["Sistema"]
        );
        $pacientes[] = $paciente;
    }
}

// Devolver los resultados en formato JSON
header('Content-Type: application/json');
echo json_encode($pacientes);

// Cerrar la conexión a la base de datos
$stmt->close();
$conn->close();
?>
