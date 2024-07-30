<?php
include "db_connect.php";

// Obtener el ID del producto desde la solicitud POST
$idProdPos = isset($_POST['ID_Prod_POS']) ? $_POST['ID_Prod_POS'] : '';

if (!empty($idProdPos)) {
    // Preparar la consulta para eliminar el producto
    $sql = "DELETE FROM Productos_POS WHERE ID_Prod_POS = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $idProdPos);

    if ($stmt->execute()) {
        // Opcionalmente eliminar registros relacionados en otras tablas si es necesario
        $stmt->close();
        echo "Producto eliminado correctamente";
    } else {
        echo "Error al eliminar el producto: " . $conn->error;
    }
} else {
    echo "ID del producto no proporcionado";
}

$conn->close();
?>
