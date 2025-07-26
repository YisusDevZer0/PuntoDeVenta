<?php 
date_default_timezone_set("America/Monterrey");
if (session_status() == PHP_SESSION_NONE) {
	session_start();
}

if(!isset($_SESSION['ControlMaestro']) && !isset($_SESSION['AdministradorRH']) && !isset($_SESSION['Marketing'])){
	header("Location: Expiro.php");
}
include_once("db_connect.php");

// Determinar el ID de usuario según la sesión activa
$userId = isset($_SESSION['ControlMaestro']) ? $_SESSION['ControlMaestro'] : (isset($_SESSION['AdministradorRH']) ? $_SESSION['AdministradorRH'] : $_SESSION['Marketing']);

$sql = "SELECT
Usuarios_PV.Id_PvUser,
Usuarios_PV.Nombre_Apellidos,
Usuarios_PV.file_name,
Usuarios_PV.Fk_Usuario,
Usuarios_PV.Fecha_Nacimiento,
Usuarios_PV.Correo_Electronico,
Usuarios_PV.Telefono,
Usuarios_PV.AgregadoPor,
Usuarios_PV.AgregadoEl,
Usuarios_PV.Estatus,
Usuarios_PV.Licencia,
Usuarios_PV.Fk_Sucursal,
Tipos_Usuarios.ID_User,
Tipos_Usuarios.TipoUsuario,
Sucursales.ID_Sucursal,
Sucursales.Nombre_Sucursal
FROM
Usuarios_PV
INNER JOIN Tipos_Usuarios ON Usuarios_PV.Fk_Usuario = Tipos_Usuarios.ID_User
INNER JOIN Sucursales ON Usuarios_PV.Fk_Sucursal = Sucursales.ID_Sucursal 
WHERE Usuarios_PV.Id_PvUser = ?";

// Preparar la consulta
$stmt = $conn->prepare($sql);
if (!$stmt) {
	error_log("Error al preparar la consulta en ControladorUsuario: " . $conn->error);
	die("Error en la preparación de la consulta");
}

// Vincular parámetro
$stmt->bind_param("s", $userId);

// Ejecutar la consulta
if (!$stmt->execute()) {
	error_log("Error al ejecutar la consulta en ControladorUsuario: " . $stmt->error);
	die("Error en la ejecución de la consulta");
}

// Obtener resultado
$result = $stmt->get_result();
$row = $result->fetch_assoc();

// Verificar si se obtuvo el resultado
if (!$row) {
	error_log("No se encontró el usuario con ID: " . $userId);
	// En lugar de die(), redirigir a la página de expiración
	header("Location: Expiro.php");
	exit();
}

// Cerrar la declaración
$stmt->close();

// Guardar Fk_Sucursal en la sesión para uso posterior
$_SESSION['Fk_Sucursal'] = $row['Fk_Sucursal'];
