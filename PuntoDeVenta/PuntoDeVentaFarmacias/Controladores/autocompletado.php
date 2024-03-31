<?php
// Conecta con la base de datos y realiza la consulta para obtener los resultados de autocompletado
$dbHost = 'localhost'; // Cambia esto por la dirección de tu servidor de base de datos
$dbUsername = 'u858848268_devpezer0'; // Cambia esto por tu nombre de usuario de la base de datos
$dbPassword = 'F9+nIIOuCh8yI6wu4!08'; // Cambia esto por tu contraseña de la base de datos
$dbName = 'u858848268_doctorpez'; // Cambia esto por el nombre de tu base de datos

$conn = mysqli_connect($dbHost, $dbUsername, $dbPassword, $dbName);

if (!$conn) {
  die('Error de conexión: ' . mysqli_connect_error());
}

// Obtén el término de búsqueda enviado desde el campo de autocompletado
$term = $_GET['term'];

// Realiza la consulta utilizando el término de búsqueda y DISTINCT
$query = "SELECT DISTINCT Cod_Barra, Nombre_Prod FROM Stock_POS WHERE Cod_Barra LIKE '%{$term}%' OR Nombre_Prod LIKE '%{$term}%'";
$result = mysqli_query($conn, $query);

// Genera un array con los resultados de autocompletado
$autocompletado = array();
while ($row = mysqli_fetch_assoc($result)) {
  $autocompletado[] = array(
    'label' => $row['Cod_Barra'] . ' - ' . $row['Nombre_Prod'], // Texto que se muestra en el autocompletado
    'value' => $row['Cod_Barra'] // Valor que se selecciona al elegir un resultado del autocompletado
  );
}

// Devuelve los resultados de autocompletado como JSON
echo json_encode($autocompletado);

// Cierra la conexión a la base de datos
mysqli_close($conn);
?>
