<?php
include_once "db_connect.php";
include_once "ControladorUsuario.php";
// Obtén el término de búsqueda enviado desde el campo de autocompletado
$term = $_GET['term'];



// Realiza la consulta utilizando el término de búsqueda, la sucursal y DISTINCT
$query = "SELECT DISTINCT Cod_Barra, Nombre_Prod FROM CEDIS WHERE (Cod_Barra LIKE '%{$term}%' OR Nombre_Prod LIKE '%{$term}%') ";
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
