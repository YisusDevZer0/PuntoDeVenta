<?php
include_once "db_connect.php";

$sql = "SELECT ID_Sucursal, Nombre_Sucursal FROM Sucursales ORDER BY Nombre_Sucursal";

$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo '<option value="' . $row['ID_Sucursal'] . '">' . htmlspecialchars($row['Nombre_Sucursal']) . '</option>';
    }
} else {
    echo '<option value="">No hay sucursales disponibles</option>';
}

$conn->close();
?>
