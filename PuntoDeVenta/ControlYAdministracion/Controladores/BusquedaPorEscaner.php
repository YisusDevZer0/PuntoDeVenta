<?php
include_once "db_connect.php";
include_once "ControladorUsuario.php";

$codigo = $_POST['codigoEscaneado'];

// Consultar la base de datos para obtener el artículo correspondiente al código de barras
$sql = "SELECT Cod_Barra, GROUP_CONCAT(ID_Prod_POS) AS IDs, GROUP_CONCAT(Nombre_Prod) AS descripciones, GROUP_CONCAT(Precio_Venta) AS precios, GROUP_CONCAT(Lote_Med) AS lotes,
GROUP_CONCAT(Clave_adicional) AS claves, GROUP_CONCAT(Tipo_Servicio) AS tipos, GROUP_CONCAT(Existencias) AS stockactual, GROUP_CONCAT(Precio_C) as precioscompra
FROM CEDIS
WHERE Cod_Barra = ?
GROUP BY Cod_Barra";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $codigo);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
} else {
    // Si no se encuentra por código, buscar por nombre
    $sql = "SELECT Cod_Barra, GROUP_CONCAT(ID_Prod_POS) AS IDs, GROUP_CONCAT(Nombre_Prod) AS descripciones, GROUP_CONCAT(Precio_Venta) AS precios, GROUP_CONCAT(Lote_Med) AS lotes,
    GROUP_CONCAT(Clave_adicional) AS claves, GROUP_CONCAT(Tipo_Servicio) AS tipos, GROUP_CONCAT(Existencias) AS stockactual, GROUP_CONCAT(Precio_C) as precioscompra
    FROM CEDIS
    WHERE Nombre_Prod LIKE ?
    GROUP BY Nombre_Prod";

    $stmt = $conn->prepare($sql);
    $nombre = '%' . $codigo . '%'; // Buscar por nombre similar al código escaneado
    $stmt->bind_param("s", $nombre);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
    } else {
        // Si no se encuentra por nombre, devolver un artículo vacío
        $data = array(
            "id" => null,
            "codigo" => '',
            "descripcion" => 'Artículo no encontrado',
            "cantidad" => 1,
            "existencia" => 0,
            "precio" => 0,
            "preciocompra" => 0,
            "lote" => '',
            "clave" => '',
            "tipo" => ''
        );

        echo json_encode($data);
        $stmt->close();
        $conn->close();
        exit;
    }
}

$ids = explode(',', $row['IDs']);
$descripciones = explode(',', $row['descripciones']);
$precios = explode(',', $row['precios']);
$precioscompra = explode(',', $row['precioscompra']);
$stockactual = explode(',', $row['stockactual']);
$lotes = explode(',', $row['lotes']);
$claves = explode(',', $row['claves']);
$tipos = explode(',', $row['tipos']);

$data = array(
    "id" => $ids[0],
    "codigo" => $row["Cod_Barra"] ?: '', // Si no hay código de barras, devolver una cadena vacía
    "descripcion" => $descripciones[0],
    "cantidad" => 1,
    "existencia" => $stockactual[0],
    "precio" => $precios[0],
    "preciocompra" => $precioscompra[0],
    "lote" => $lotes[0],
    "clave" => $claves[0],
    "tipo" => $tipos[0]
);

echo json_encode($data);

$stmt->close();
$conn->close();
?>
