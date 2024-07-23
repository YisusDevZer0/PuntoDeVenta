<?php
include_once "db_connect.php";
include_once "ControladorUsuario.php";

// Obtener el código de barras y la sucursal enviada por AJAX
$codigo = $_POST['codigoEscaneado'];
$sucursalbusqueda = $_POST['sucursalbusqueda']; // Asegúrate de enviar la sucursal desde el front-end

// Consultar la base de datos para obtener el artículo correspondiente al código de barras
$sql = "SELECT Cod_Barra, Fk_sucursal, GROUP_CONCAT(ID_Prod_POS) AS IDs, GROUP_CONCAT(Nombre_Prod) AS descripciones, GROUP_CONCAT(Precio_Venta) AS precios, GROUP_CONCAT(Lote) AS lotes,
GROUP_CONCAT(Clave_adicional) AS claves, GROUP_CONCAT(Tipo_Servicio) AS tipos, GROUP_CONCAT(Existencias_R) AS stockactual, GROUP_CONCAT(Precio_C) AS precioscompra
FROM Stock_POS
WHERE Cod_Barra = ? AND Fk_sucursal = ?
GROUP BY Cod_Barra";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $codigo, $sucursalbusqueda);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Si se encontró el artículo, obtener los valores concatenados
    $row = $result->fetch_assoc();
    $ids = explode(',', $row['IDs']);
    $descripciones = explode(',', $row['descripciones']);
    $precios = explode(',', $row['precios']);
    $precioscompra = explode(',', $row['precioscompra']);
    $stockactual = explode(',', $row['stockactual']);
    $lotes = explode(',', $row['lotes']);
    $claves = explode(',', $row['claves']);
    $tipos = explode(',', $row['tipos']);
    
    // Tomar el primer valor de cada columna para evitar la repetición
    $data = array(
        "id" => $ids[0],
        "codigo" => $row["Cod_Barra"],
        "descripcion" => $descripciones[0],
        "cantidad" => 1,
        "existencia" => $stockactual[0],
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
    // Si no se encontró el artículo con el código de barras, buscar por nombre
    $sql = "SELECT Cod_Barra, Fk_sucursal, GROUP_CONCAT(ID_Prod_POS) AS IDs, GROUP_CONCAT(Nombre_Prod) AS descripciones, GROUP_CONCAT(Precio_Venta) AS precios, GROUP_CONCAT(Lote) AS lotes,
    GROUP_CONCAT(Clave_adicional) AS claves, GROUP_CONCAT(Tipo_Servicio) AS tipos, GROUP_CONCAT(Existencias_R) AS stockactual, GROUP_CONCAT(Precio_C) AS precioscompra
    FROM Stock_POS
    WHERE Nombre_Prod LIKE ? AND Fk_sucursal = ?
    GROUP BY Nombre_Prod";
    
    $stmt = $conn->prepare($sql);
    $likeNombre = "%$codigo%"; // Usa el código para buscar por nombre
    $stmt->bind_param("ss", $likeNombre, $sucursalbusqueda);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Si se encontró el artículo por nombre, obtener los valores concatenados
        $row = $result->fetch_assoc();
        $ids = explode(',', $row['IDs']);
        $descripciones = explode(',', $row['descripciones']);
        $precios = explode(',', $row['precios']);
        $precioscompra = explode(',', $row['precioscompra']);
        $stockactual = explode(',', $row['stockactual']);
        $lotes = explode(',', $row['lotes']);
        $claves = explode(',', $row['claves']);
        $tipos = explode(',', $row['tipos']);
        
        // Tomar el primer valor de cada columna para evitar la repetición
        $data = array(
            "id" => $ids[0],
            "codigo" => $row["Cod_Barra"], // Puede estar vacío si no se encontró por código
            "descripcion" => $descripciones[0],
            "cantidad" => 1,
            "existencia" => $stockactual[0],
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
        // Si no se encontró el artículo ni por código ni por nombre, devolver un array vacío en formato JSON
        $data = array();
        header('Content-Type: application/json');
        echo json_encode($data);
    }
}

// Cerrar la conexión a la base de datos
$stmt->close();
$conn->close();
?>
