<?php
include_once "db_connect.php";
include_once "ControladorUsuario.php";

// Obtén el término de búsqueda enviado desde el campo de autocompletado
$term = $_GET['term'];

// Verifica que la variable $row esté definida y contenga el valor de Fk_Sucursal
if (isset($row['Fk_Sucursal'])) {
    $sucursalbusqueda = $row['Fk_Sucursal'];
} else {
    echo json_encode([]); // Si no hay sucursal, devolver un array vacío
    exit;
}

// Realiza la consulta utilizando el término de búsqueda, la sucursal y DISTINCT
$query = "SELECT DISTINCT Cod_Barra, Nombre_Prod FROM Stock_POS WHERE (Cod_Barra LIKE '%{$term}%' OR Nombre_Prod LIKE '%{$term}%') AND Fk_Sucursal = '{$sucursalbusqueda}'";

// Para depuración: imprime la consulta SQL
error_log("Consulta SQL: " . $query);

$result = mysqli_query($conn, $query);

if (!$result) {
    // Para depuración: imprime el error de MySQL si la consulta falla
    error_log("Error en la consulta: " . mysqli_error($conn));
    echo json_encode([]);
    exit;
}

// Genera un array con los resultados de autocompletado
$autocompletado = array();
while ($row = mysqli_fetch_assoc($result)) {
    $autocompletado[] = array(
        'label' => $row['Cod_Barra'] . ' - ' . $row['Nombre_Prod'], // Texto que se muestra en el autocompletado
        'value' => $row['Cod_Barra'] // Valor que se selecciona al elegir un resultado del autocompletado
    );
}

// Para depuración: imprime el array de autocompletado
error_log("Resultados de autocompletado: " . json_encode($autocompletado));

// Devuelve los resultados de autocompletado como JSON
echo json_encode($autocompletado);

// Cierra la conexión a la base de datos
mysqli_close($conn);
?>
