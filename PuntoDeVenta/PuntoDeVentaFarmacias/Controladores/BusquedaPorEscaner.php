<?php
include_once "db_connect.php";
include_once "ControladorUsuario.php";

// Obtener el código de barras y la sucursal buscada enviado por AJAX
$codigo = $_POST['codigoEscaneado'];


// Consultar la base de datos para obtener el artículo correspondiente al código de barras
$sql = "SELECT Cod_Barra, Fecha_Caducidad, GROUP_CONCAT(ID_Prod_POS) AS IDs, GROUP_CONCAT(Nombre_Prod) AS descripciones, GROUP_CONCAT(Precio_Venta) AS precios, GROUP_CONCAT(Lote_Med) AS lotes,
GROUP_CONCAT(Clave_adicional) AS claves, GROUP_CONCAT(Tipo_Servicio) AS tipos, GROUP_CONCAT(Existencias)  AS stockactual ,GROUP_CONCAT(Precio_C) as precioscompra
FROM CEDIS
       WHERE Cod_Barra = ?
       GROUP BY Cod_Barra;";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $codigo);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Si se encontró el artículo, obtener los valores concatenados
    $row = $result->fetch_assoc();
    $ids = explode(',', $row['IDs']);
    $descripciones = explode(',', $row['descripciones']);
    $precios = explode(',', $row['precios']);
    $precioscompra = explode(',', $row['precioscompra']);
    $fechacaducidad = explode(',', $row['Fecha_Caducidad']);
    $lotes = explode(',', $row['lotes']);
    $claves = explode(',', $row['claves']);
    $tipos = explode(',', $row['tipos']);
    
    // Tomar el primer valor de cada columna para evitar la repetición
    $data = array(
        "id" => $ids[0],
        "codigo" => $row["Cod_Barra"],
        "descripcion" => $descripciones[0],
        "cantidad" => [1],
        "existencia" => $fechacaducidad[0],
        "precio" => $precios[0],
        "preciocompra" => $precioscompra[0],
        "lote" => $lotes[0],
        "clave" => $claves[0],
        "tipo" => $tipos[0],
        "eliminar" => ""
    );
    
    header('Content-Type: application/json');
    echo json_encode($data);
} else {
    // Si no se encontró el artículo, devolver un array vacío en formato JSON
    $data = array();
    header('Content-Type: application/json');
    echo json_encode($data);
}

// Cerrar la conexión a la base de datos
$stmt->close();
$conn->close();
?>
