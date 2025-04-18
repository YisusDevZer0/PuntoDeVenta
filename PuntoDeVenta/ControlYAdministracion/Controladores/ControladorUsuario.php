<?php 
date_default_timezone_set("America/Monterrey");
if (session_status() == PHP_SESSION_NONE) {
  session_start();
}

// Verificar si alguna de las sesiones requeridas existe
if (!isset($_SESSION['ControlMaestro']) && !isset($_SESSION['AdministradorRH'])) {
    header("Location: Expiro.php");
    exit; // Detener la ejecución después de redirigir
}

include_once("db_connect.php");

// Determinar qué ID de usuario usar basado en la sesión activa
$userId = '';
if (isset($_SESSION['ControlMaestro'])) {
    $userId = $_SESSION['ControlMaestro'];
} elseif (isset($_SESSION['AdministradorRH'])) {
    $userId = $_SESSION['AdministradorRH'];
}

// Si por alguna razón no se pudo obtener un ID (aunque la verificación anterior debería prevenirlo)
if (empty($userId)) {
     header("Location: Expiro.php");
     exit;
}


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
INNER JOIN Sucursales ON Usuarios_PV.Fk_Sucursal = Sucursales.ID_Sucursal WHERE
Usuarios_PV.Id_PvUser='" . mysqli_real_escape_string($conn, $userId) . "'"; // Usar el ID obtenido y escapar para seguridad

$resultset = mysqli_query($conn, $sql) or die("database error:". mysqli_error($conn));
$row = mysqli_fetch_assoc($resultset);

// Comentar o eliminar esta sección si ya no es necesaria
// if ($row['Nombre_Apellidos'] != 'DevZero') {
//   header("Location: https://doctorpez.mx/PuntoDeVenta/PuntoDeVentaFarmacias/Mantenimiento.php");
//   exit();
// }
?>
