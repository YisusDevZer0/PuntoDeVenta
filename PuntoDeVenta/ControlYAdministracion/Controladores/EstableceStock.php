<?php
include_once "db_connect.php";
include_once "ControladorUsuario.php";
// Obtener datos del POST


$userajuste = $row['Nombre_Apellidos'];
$tipoAjuste = $_POST['tipoAjuste'];
$fkSucursal = $_POST['fkSucursal'];

if ($tipoAjuste === 'Inventario inicial') {
    // Actualiza la tabla de inventario
    $updateInventario = "UPDATE Stock_POS SET Existencias_R = 0 , ActualizadoPor = ? WHERE Fk_sucursal = ?";
    $stmt = $conn->prepare($updateInventario);
    $stmt->bind_param("si", $userajuste, $fkSucursal); // Cambié "i" por "si" para incluir la cadena de texto
    $stmt->execute();

    // Marca el inventario inicial como establecido y registra la fecha y quién lo estableció
    $updateEstado = "INSERT INTO inventario_inicial_estado (fkSucursal, inventario_inicial_establecido, fecha_establecido, EstablecidoPor) VALUES (?, TRUE, NOW(), ?)";
    $stmt = $conn->prepare($updateEstado);
    $stmt->bind_param("is", $fkSucursal, $userajuste); // Cambié "i" por "is" para incluir la cadena de texto
    $stmt->execute();

    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Ajuste no permitido.']);
}
?>