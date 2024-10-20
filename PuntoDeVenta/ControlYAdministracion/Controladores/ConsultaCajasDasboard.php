<?php
// Incluir el archivo de conexión
include_once("db_connect.php");

// Asegúrate de que la conexión esté disponible
global $conn;

if ($conn && $conn->ping()) {
    // Consulta SQL
    $sql = "SELECT COUNT(*) AS CajasAbiertas FROM Cajas WHERE Estatus = 'Abierta' AND Sucursal != 4";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $CajasAbiertas = $result->fetch_assoc();
        echo "Cajas abiertas: " . $CajasAbiertas['CajasAbiertas'];
    } else {
        echo "No se encontraron resultados.";
    }
} else {
    echo "Error: la conexión a la base de datos no está disponible.";
}

// La conexión se debe cerrar después de usarla
// conn->close(); si ya no vas a usar la conexión
?>
