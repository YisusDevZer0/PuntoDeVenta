<?php
include_once "db_connect.php";
include_once "ControladorUsuario.php";

$codigo = $_POST['codigoEscaneado'];

// Actualizar la consulta para incluir el campo Tipo
$sql = "SELECT Cod_Barra, GROUP_CONCAT(ID_Prod_POS) AS IDs, GROUP_CONCAT(Nombre_Prod) AS descripciones, GROUP_CONCAT(Precio_Venta) AS precios, GROUP_CONCAT(Tipo_Servicio) AS tiposervicios,GROUP_CONCAT(Lote) AS lotes, GROUP_CONCAT(Clave_adicional) AS claves, GROUP_CONCAT(Tipo) AS tipos
        FROM Stock_POS
        WHERE Cod_Barra = ? OR Nombre_Prod LIKE ? OR Clave_adicional = ?
        GROUP BY Cod_Barra";
$stmt = $conn->prepare($sql);
$likeCodigo = '%' . $codigo . '%';
$stmt->bind_param("sss", $codigo, $likeCodigo, $codigo);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $descripciones = explode(',', $row['descripciones']);
    $tipos = explode(',', $row['tipos']);
    
    // Verificar si "ANTIBIOTICO" está en los tipos
    $isAntibiotico = in_array('ANTIBIOTICO', $tipos);
    
    $data = array(
        "id" => explode(',', $row['IDs'])[0],
        "codigo" => $row["Cod_Barra"],
        "descripcion" => $descripciones[0],
        "cantidad" => [1],
        "precio" => explode(',', $row['precios'])[0],
        "lote" => explode(',', $row['lotes'])[0],
        "clave" => explode(',', $row['claves'])[0],
        "tipo" => $tipos[0], // Aquí asigna el primer tipo si es necesario
        "tiposervicios" => explode(',', $row['tiposervicios'])[0],
        "eliminar" => "",
        "esAntibiotico" => $isAntibiotico // Añade esta clave para la verificación
    );
    
    header('Content-Type: application/json');
    echo json_encode($data);
} else {
    $data = array();
    header('Content-Type: application/json');
    echo json_encode($data);
}

$stmt->close();
$conn->close();
?>
