<?php
include_once "db_connect.php";

$sucursal = isset($_POST['sucursal']) ? $_POST['sucursal'] : '';
$producto = isset($_POST['producto']) ? $_POST['producto'] : '';
$existenciaFisica = isset($_POST['existenciaFisica']) ? $_POST['existenciaFisica'] : '';
$observaciones = isset($_POST['observaciones']) ? $_POST['observaciones'] : '';

if (empty($sucursal) || empty($producto) || empty($existenciaFisica)) {
    echo json_encode(['success' => false, 'message' => 'Faltan parámetros requeridos']);
    exit;
}

// Validar existencia física
if (!is_numeric($existenciaFisica) || $existenciaFisica < 0) {
    echo json_encode(['success' => false, 'message' => 'La existencia física debe ser un número positivo']);
    exit;
}

// Obtener información del producto
$sqlProducto = "SELECT Nombre_Producto FROM ConteosDiarios WHERE Cod_Barra = ? LIMIT 1";
$stmtProducto = $conn->prepare($sqlProducto);
if ($stmtProducto) {
    $stmtProducto->bind_param("s", $producto);
    $stmtProducto->execute();
    $resultProducto = $stmtProducto->get_result();
    
    if ($rowProducto = $resultProducto->fetch_assoc()) {
        $nombreProducto = $rowProducto['Nombre_Producto'];
    } else {
        echo json_encode(['success' => false, 'message' => 'Producto no encontrado']);
        exit;
    }
    $stmtProducto->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Error al validar el producto']);
    exit;
}

// Obtener existencia real (asumiendo que existe una tabla de inventario)
$sqlExistencia = "SELECT Existencias_R FROM ConteosDiarios WHERE Cod_Barra = ? AND Fk_sucursal = ? ORDER BY AgregadoEl DESC LIMIT 1";
$stmtExistencia = $conn->prepare($sqlExistencia);
$existenciaReal = 0;
if ($stmtExistencia) {
    $stmtExistencia->bind_param("si", $producto, $sucursal);
    $stmtExistencia->execute();
    $resultExistencia = $stmtExistencia->get_result();
    
    if ($rowExistencia = $resultExistencia->fetch_assoc()) {
        $existenciaReal = $rowExistencia['Existencias_R'];
    }
    $stmtExistencia->close();
}

// Generar folio de ingreso
$sqlFolio = "SELECT MAX(Folio_Ingreso) as max_folio FROM ConteosDiarios";
$resultFolio = $conn->query($sqlFolio);
$nuevoFolio = 1;
if ($resultFolio && $rowFolio = $resultFolio->fetch_assoc()) {
    $nuevoFolio = $rowFolio['max_folio'] + 1;
}

// Obtener usuario actual (asumiendo que está en la sesión)
$usuarioActual = 'Sistema'; // En un caso real, obtener de la sesión

// Insertar nuevo conteo
$sql = "INSERT INTO ConteosDiarios (
    Folio_Ingreso, 
    Cod_Barra, 
    Nombre_Producto, 
    Fk_sucursal, 
    Existencias_R, 
    ExistenciaFisica, 
    AgregadoPor, 
    AgregadoEl, 
    EnPausa
) VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), 0)";

$stmt = $conn->prepare($sql);
if ($stmt) {
    $stmt->bind_param("issiiis", $nuevoFolio, $producto, $nombreProducto, $sucursal, $existenciaReal, $existenciaFisica, $usuarioActual);
    
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true, 
            'message' => 'Conteo creado correctamente',
            'folio' => $nuevoFolio
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al crear el conteo: ' . $stmt->error]);
    }
    
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Error al preparar la consulta: ' . $conn->error]);
}

$conn->close();
?>
