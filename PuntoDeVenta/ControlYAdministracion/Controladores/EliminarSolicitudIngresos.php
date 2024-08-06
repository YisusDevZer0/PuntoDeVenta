<?php
include "db_connect.php";

// Obtener el ID del producto desde la solicitud POST
$idProdCedis = isset($_POST['IdProdCedis']) ? $_POST['IdProdCedis'] : '';

if (!empty($idProdCedis)) {
    // Preparar la consulta para eliminar el producto
    $sql = "DELETE FROM Solicitudes_Ingresos WHERE IdProdCedis = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $idProdCedis);

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
