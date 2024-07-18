<?php
include_once 'db_connect.php';

// Verificar si se recibieron todos los datos necesarios
$requiredFields = array('Encabezado', 'TipoMensaje', 'Mensaje_Recordatorio', 'Registrado', 'Sistema', 'Sucursal', 'Estado', 'Licencia');
$missingFields = array();
foreach ($requiredFields as $field) {
    if (!isset($_POST[$field])) {
        $missingFields[] = $field;
    }
}

if (!empty($missingFields)) {
    $errorMessage = "Faltan los siguientes campos: " . implode(', ', $missingFields);
    echo json_encode(array("statusCode" => 500, "error" => $errorMessage)); // Error de datos faltantes
} else {
    // Escapar y asignar los valores recibidos
    $Encabezado = mysqli_real_escape_string($conn, $_POST['Encabezado']);
    $TipoMensaje = mysqli_real_escape_string($conn, $_POST['TipoMensaje']);
    $Mensaje_Recordatorio = mysqli_real_escape_string($conn, $_POST['Mensaje_Recordatorio']);
    $Registrado = mysqli_real_escape_string($conn, $_POST['Registrado']);
    $Sistema = mysqli_real_escape_string($conn, $_POST['Sistema']);
    $Sucursal = mysqli_real_escape_string($conn, $_POST['Sucursal']);
    $Estado = mysqli_real_escape_string($conn, $_POST['Estado']);
    $Licencia = mysqli_real_escape_string($conn, $_POST['Licencia']);

    // Consulta para verificar si ya existe un registro con los mismos valores
    $sql = "SELECT Encabezado, TipoMensaje FROM Recordatorios_Pendientes WHERE Encabezado='$Encabezado' AND TipoMensaje='$TipoMensaje'";
    $resultset = mysqli_query($conn, $sql);

    if($resultset && mysqli_num_rows($resultset) > 0) {
        echo json_encode(array("statusCode" => 250)); // El registro ya existe
    } else {
        // Consulta de inserción para agregar un nuevo registro
        $sql_insert = "INSERT INTO `Recordatorios_Pendientes`(`Encabezado`, `TipoMensaje`, `Mensaje_Recordatorio`, `Registrado`, `Sistema`, `Sucursal`, `Estado`, `Licencia`) 
                       VALUES ('$Encabezado', '$TipoMensaje', '$Mensaje_Recordatorio', '$Registrado', '$Sistema', '$Sucursal', '$Estado', '$Licencia')";

        if(mysqli_query($conn, $sql_insert)) {
            echo json_encode(array("statusCode" => 200)); // Inserción exitosa
        } else {
            echo json_encode(array("statusCode" => 201, "error" => mysqli_error($conn))); // Error en la inserción
        }
    }
}

// Cerrar la conexión a la base de datos
mysqli_close($conn);
?>
