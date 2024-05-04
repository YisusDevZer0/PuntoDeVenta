<?php
// Verifica la conexión a la base de datos
require_once "db_connect.php";

// Verifica que el ID del usuario se haya proporcionado y es un número entero
if (isset($_GET['user_id']) && is_numeric($_GET['user_id'])) {
    // Sanitiza el ID del usuario para prevenir inyecciones SQL
    $user_id = mysqli_real_escape_string($conn, $_GET['user_id']);

    // Prepara la consulta SQL utilizando sentencias preparadas
    $sql = "SELECT * FROM Tipos_Usuarios WHERE ID_User = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id); // "i" indica que el parámetro es un entero
    $stmt->execute();

    // Verifica si la ejecución fue exitosa
    if ($stmt->execute()) {
        // Obtiene el resultado de la consulta
        $result = $stmt->get_result();

        // Verifica si se encontró algún usuario
        if ($result->num_rows > 0) {
            // Retorna los datos del usuario como JSON
            $row = $result->fetch_assoc();
            echo json_encode($row);
        } else {
            echo "Usuario no encontrado";
        }
    } else {
        // Hubo un error en la ejecución de la consulta
        echo "Error en la consulta: " . $stmt->error;
    }

    // Cierra la declaración y la conexión a la base de datos
    $stmt->close();
} else {
    // El ID del usuario no se proporcionó o no es válido
    echo "ID de usuario no válido";
}

// Cierra la conexión a la base de datos
$conn->close();
?>
