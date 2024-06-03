<?php
$servername = "localhost";
$username = "u858848268_devpezer0";
$password = "F9+nIIOuCh8yI6wu4!08";
$dbname = "u858848268_doctorpez";

// Crear la conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Obtener el código de barras o nombre de producto enviado por AJAX
$codigo = $_POST['codigoEscaneado'];

// Consultar la base de datos para obtener el artículo correspondiente al código de barras o nombre de producto
$sql = "SELECT Cod_Barra, GROUP_CONCAT(ID_Prod_POS) AS IDs, GROUP_CONCAT(Nombre_Prod) AS descripciones, GROUP_CONCAT(Precio_Venta) AS precios, GROUP_CONCAT(Lote) AS lotes, GROUP_CONCAT(Clave_adicional) AS claves, GROUP_CONCAT(Tipo_Servicio) AS tipos
        FROM Stock_POS
        WHERE Cod_Barra = ? OR Nombre_Prod LIKE ? OR Clave_adicional = ?
        GROUP BY Cod_Barra";
$stmt = $conn->prepare($sql);
$likeCodigo = '%' . $codigo . '%';
$stmt->bind_param("sss", $codigo, $likeCodigo, $codigo);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Si se encontró el artículo, obtener los valores concatenados
    $row = $result->fetch_assoc();
    $ids = explode(',', $row['IDs']);
    $descripciones = explode(',', $row['descripciones']);
    $precios = explode(',', $row['precios']);
    $lotes = explode(',', $row['lotes']);
    $claves = explode(',', $row['claves']);
    $tipos = explode(',', $row['tipos']);
    
    // Tomar el primer valor de cada columna para evitar la repetición
    $data = array(
        "id" => $ids[0],
        "codigo" => $row["Cod_Barra"],
        "descripcion" => $descripciones[0],
        "cantidad" => [1],
        "precio" => $precios[0],
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
