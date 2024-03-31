<?php
// Establecer la configuración de la base de datos
$servername = "localhost";
$username = "u858848268_devpezer0";
$password = "F9+nIIOuCh8yI6wu4!08";
$dbname = "u858848268_doctorpez";


// Crear la conexión a la base de datos
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar si hay errores de conexión
if ($conn->connect_error) {
  die("Error de conexión a la base de datos: " . $conn->connect_error);
}

// Obtener el número de ticket enviado por el formulario
$ticket = $_POST['NumeroDeTickeT'];

// Consulta para verificar si el ticket existe en la base de datos
$sql = "SELECT COUNT(*) AS count FROM Ventas_POS_Pruebas WHERE Folio_Ticket = '$ticket'";  

$result = $conn->query($sql);

// Verificar si hay resultados y si el ticket existe
if ($result && $result->num_rows > 0) {
  $row = $result->fetch_assoc();
  $existe = $row['count'] > 0;
} else {
  $existe = false;
}

// Devolver la respuesta en formato JSON
$response = array('existe' => $existe);
echo json_encode($response);

// Cerrar la conexión a la base de datos
$conn->close();
?>
