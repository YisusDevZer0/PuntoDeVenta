<?php
include_once "db_connect.php";

$sql = "SELECT DISTINCT AgregadoPor FROM ConteosDiarios WHERE AgregadoPor IS NOT NULL ORDER BY AgregadoPor";

$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo '<option value="' . htmlspecialchars($row['AgregadoPor']) . '">' . htmlspecialchars($row['AgregadoPor']) . '</option>';
    }
} else {
    echo '<option value="">No hay usuarios disponibles</option>';
}

$conn->close();
?>
