<?php
include_once "db_connect.php";
include_once "ControladorUsuario.php";

// Obtener el código de barras o nombre enviado por AJAX
$codigo = $_POST['codigoEscaneado'];

// Inicializa el array de datos vacío
$data = array();

// Primero, intenta buscar por código de barras
$sql = "SELECT Cod_Barra, ID_Prod_POS AS id, Nombre_Prod AS descripcion, Precio_Venta AS precio, Existencias AS stockactual, Precio_C AS precioscompra
        FROM CEDIS
        WHERE Cod_Barra = ?
        LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $codigo);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Si se encontró el artículo, devuelve los datos
    $row = $result->fetch_assoc();
    $data = array(
        "id" => $row["id"],
        "codigo" => $row["Cod_Barra"],
        "descripcion" => $row["descripcion"],
        "cantidad" => 1,
        "existencia" => $row["stockactual"],
        "precio" => $row["precio"],
        "preciocompra" => $row["precioscompra"],
        "lote" => "",
        "clave" => "",
        "tipo" => ""
    );
} else {
    // Si no se encontró por código de barras, busca por nombre
    $sql = "SELECT Cod_Barra, ID_Prod_POS AS id, Nombre_Prod AS descripcion, Precio_Venta AS precio, Existencias AS stockactual, Precio_C AS precioscompra
            FROM CEDIS
            WHERE Nombre_Prod LIKE ?
            LIMIT 1";
    $stmt = $conn->prepare($sql);
    $likeName = "%{$codigo}%";
    $stmt->bind_param("s", $likeName);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Si se encontró el artículo por nombre, devuelve los datos
        $row = $result->fetch_assoc();
        $data = array(
            "id" => $row["id"],
            "codigo" => $row["Cod_Barra"],
            "descripcion" => $row["descripcion"],
            "cantidad" => 1,
            "existencia" => $row["stockactual"],
            "precio" => $row["precio"],
            "preciocompra" => $row["precioscompra"],
            "lote" => "",
            "clave" => "",
            "tipo" => ""
        );
    }
}

header('Content-Type: application/json');
echo json_encode($data);

// Cierra la conexión a la base de datos
$stmt->close();
$conn->close();
?>
