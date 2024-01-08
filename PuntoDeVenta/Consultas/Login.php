<?php
// Incluir archivo de conexión a la base de datos
include "https://doctorpez.mx/PuntoDeVenta/Config/Conexion.php";

// Obtener datos del formulario
$Correo_Electronico = $_POST['username'];
$Password = $_POST['passwordd'];

// Consulta para obtener la contraseña y rol almacenados en la base de datos
$query = "SELECT * FROM Usuarios_PV WHERE nombre_usuario = '$Correo_Electronico'";
$result = mysqli_query($con, $query);

if ($row = mysqli_fetch_assoc($result)) {
    // Verificar la contraseña utilizando la función password_verify
    if (password_verify($Password, $row['Password'])) {
        // Verificar el rol del usuario
        $Fk_Usuario = $row['Fk_Usuario'];

        if ($Fk_Usuario === '1') {
            // Usuario administrador
            echo json_encode(['success' => true, 'message' => 'Inicio de sesión exitoso como administrador']);
        } elseif ($Fk_Usuario === '2') {
            // Usuario vendedor
            echo json_encode(['success' => true, 'message' => 'Inicio de sesión exitoso como vendedor']);
        } else {
            // Rol no reconocido
            echo json_encode(['success' => false, 'message' => 'Rol no reconocido']);
        }
    } else {
        // Credenciales inválidas
        echo json_encode(['success' => false, 'message' => 'Credenciales inválidas']);
    }
} else {
    // Usuario no encontrado
    echo json_encode(['success' => false, 'message' => 'Usuario no encontrado']);
}

// Cerrar conexión a la base de datos
mysqli_close($con);
?>
