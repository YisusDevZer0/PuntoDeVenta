<?php
header('Content-Type: application/json');
include("db_connection.php");
include_once "ControladorUsuario.php";

// Verificar que se recibieron los datos necesarios
if (!isset($_POST['folio_prod_stock']) || !isset($_POST['cantidad_sugerida'])) {
    echo json_encode(['success' => false, 'message' => 'Faltan datos requeridos']);
    exit;
}

// Obtener los datos del POST
$folio_prod_stock = $_POST['folio_prod_stock'];
$cantidad_sugerida = $_POST['cantidad_sugerida'];
$existencias_actuales = $_POST['existencias_actuales'];
$min_existencia = $_POST['min_existencia'];
$max_existencia = $_POST['max_existencia'];
$proveedor = $_POST['proveedor'];

// Obtener información adicional del producto
$sql = "SELECT Cod_Barra, Nombre_Prod, Fk_sucursal, ID_H_O_D FROM Stock_POS WHERE Folio_Prod_Stock = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $folio_prod_stock);
$stmt->execute();
$result = $stmt->get_result();
$producto = $result->fetch_assoc();

if (!$producto) {
    echo json_encode(['success' => false, 'message' => 'Producto no encontrado']);
    exit;
}

// Insertar la orden sugerida
$sql = "INSERT INTO Ordenes_Compra_Sugeridas (
    Folio_Prod_Stock, 
    Cod_Barra, 
    Nombre_Prod, 
    Cantidad_Sugerida, 
    Existencias_Actuales,
    Min_Existencia,
    Max_Existencia,
    Fk_Sucursal,
    ID_H_O_D,
    Proveedor,
    Estatus
) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Pendiente')";

$stmt = $conn->prepare($sql);
$stmt->bind_param(
    "sssiiiisss",
    $folio_prod_stock,
    $producto['Cod_Barra'],
    $producto['Nombre_Prod'],
    $cantidad_sugerida,
    $existencias_actuales,
    $min_existencia,
    $max_existencia,
    $producto['Fk_sucursal'],
    $producto['ID_H_O_D'],
    $proveedor
);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Orden guardada correctamente']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error al guardar la orden: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?> 