<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include_once 'db_connect.php';

if (!$conn) {
    die("Error de conexión: " . mysqli_connect_error());
}

$Nombre_Categoria = mysqli_real_escape_string($conn, $_POST['NomMarca'] ?? '');
$Agregado_Por = mysqli_real_escape_string($conn, $_POST['agregoPor'] ?? '');
$Sistema = mysqli_real_escape_string($conn, $_POST['sistema'] ?? '');
$Licencia = mysqli_real_escape_string($conn, $_POST['licencia'] ?? '');
$Estado = mysqli_real_escape_string($conn, $_POST['estado'] ?? '');

// Verificar si el registro ya existe
$sql = "SELECT Nom_Cat, Licencia FROM Categorias_POS WHERE Nom_Cat = ? AND Licencia = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $Nombre_Categoria, $Licencia);
$stmt->execute();
$resultset = $stmt->get_result();

if ($resultset->num_rows > 0) {
    echo json_encode(array("statusCode" => 250)); // El registro ya existe
} else {
    // Insertar nuevo registro
    $sql = "INSERT INTO Categorias_POS (Nom_Cat, Agregado_Por, Sistema, Licencia, Estado) 
            VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $Nombre_Categoria, $Agregado_Por, $Sistema, $Licencia, $Estado);
    
    if ($stmt->execute()) {
        echo json_encode(array("statusCode" => 200)); // Inserción exitosa
    } else {
        echo json_encode(array("statusCode" => 201, "error" => $stmt->error)); // Error en la inserción
    }
}

$stmt->close();
$conn->close();
?>
