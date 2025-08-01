<?php
header('Content-Type: application/json');
include("db_connect.php");
include_once "ControladorUsuario.php";

// Obtener el valor de la licencia de la fila, asegurándote de que esté correctamente formateado
$licencia = isset($row['Licencia']) ? $row['Licencia'] : '';

// Debug: Verificar si la licencia está disponible
error_log("Licencia obtenida: " . $licencia);

// Obtener parámetros de filtro
$tipo_usuario = isset($_GET['tipo_usuario']) ? $_GET['tipo_usuario'] : '';
$sucursal = isset($_GET['sucursal']) ? $_GET['sucursal'] : '';
$estatus = isset($_GET['estatus']) ? $_GET['estatus'] : '';

// Consulta base
$sql = "SELECT Usuarios_PV.Id_PvUser, Usuarios_PV.Nombre_Apellidos, Usuarios_PV.file_name, 
Usuarios_PV.Fk_Usuario, Usuarios_PV.Fecha_Nacimiento, Usuarios_PV.Correo_Electronico, 
Usuarios_PV.Telefono, Usuarios_PV.AgregadoPor, Usuarios_PV.AgregadoEl, Usuarios_PV.Estatus,
Usuarios_PV.Licencia, Tipos_Usuarios.ID_User, Tipos_Usuarios.TipoUsuario,
Sucursales.ID_Sucursal, Sucursales.Nombre_Sucursal 
FROM Usuarios_PV 
INNER JOIN Tipos_Usuarios ON Usuarios_PV.Fk_Usuario = Tipos_Usuarios.ID_User 
INNER JOIN Sucursales ON Usuarios_PV.Fk_Sucursal = Sucursales.ID_Sucursal
WHERE Usuarios_PV.Licencia = ?";

// Agregar filtros si están presentes
$params = [$licencia];
$types = "s";

if (!empty($tipo_usuario)) {
    $sql .= " AND Tipos_Usuarios.TipoUsuario = ?";
    $params[] = $tipo_usuario;
    $types .= "s";
}

if (!empty($sucursal)) {
    $sql .= " AND Sucursales.ID_Sucursal = ?";
    $params[] = $sucursal;
    $types .= "s";
}

if (!empty($estatus)) {
    $sql .= " AND Usuarios_PV.Estatus = ?";
    $params[] = $estatus;
    $types .= "s";
} else {
    // Por defecto mostrar solo activos si no se especifica estatus
    $sql .= " AND Usuarios_PV.Estatus = 'Activo'";
}

$sql .= " ORDER BY Usuarios_PV.Id_PvUser DESC";

// Preparar la declaración
$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode([
        "sEcho" => 1,
        "iTotalRecords" => 0,
        "iTotalDisplayRecords" => 0,
        "aaData" => [],
        "error" => "Error al preparar la consulta: " . $conn->error
    ]);
    exit;
}

// Vincular parámetros
$stmt->bind_param($types, ...$params);

// Ejecutar la declaración
if (!$stmt->execute()) {
    echo json_encode([
        "sEcho" => 1,
        "iTotalRecords" => 0,
        "iTotalDisplayRecords" => 0,
        "aaData" => [],
        "error" => "Error al ejecutar la consulta: " . $stmt->error
    ]);
    exit;
}

// Obtener resultado
$result = $stmt->get_result();

// Inicializar array para almacenar los resultados
$data = [];

// Procesar resultados
while ($fila = $result->fetch_assoc()) {
    // Formatear fecha de nacimiento
    $fecha_nacimiento = '';
    if ($fila["Fecha_Nacimiento"]) {
        $fecha_nacimiento = date('d/m/Y', strtotime($fila["Fecha_Nacimiento"]));
    }
    
    // Formatear fecha de creación
    $fecha_creacion = '';
    if ($fila["AgregadoEl"]) {
        $fecha_creacion = date('d/m/Y H:i', strtotime($fila["AgregadoEl"]));
    }
    
    // Construir el array de datos
    $data[] = [
        "Idpersonal" => $fila["Id_PvUser"],
        "NombreApellidos" => $fila["Nombre_Apellidos"],
        "Foto" => '<img src="https://doctorpez.mx/PuntoDeVenta/PerfilesImg/' . $fila["file_name"] . '" alt="Foto" class="profile-img" onerror="this.src=\'https://doctorpez.mx/PuntoDeVenta/PerfilesImg/Administrativos.jpeg\'">',
        "Tipousuario" => $fila["TipoUsuario"],
        "Sucursal" => $fila["Nombre_Sucursal"],
        "CorreoElectronico" => $fila["Correo_Electronico"] ?: 'No especificado',
        "Telefono" => $fila["Telefono"] ?: 'No especificado',
        "FechaNacimiento" => $fecha_nacimiento,
        "CreadoEl" => $fecha_creacion,
        "Estatus" => $fila["Estatus"],
        "CreadoPor" => $fila["AgregadoPor"] ?: 'Sistema'
    ];
}

// Cerrar la declaración
$stmt->close();

// Construir el array de resultados para la respuesta JSON
$results = [
    "sEcho" => 1,
    "iTotalRecords" => count($data),
    "iTotalDisplayRecords" => count($data),
    "aaData" => $data
];

// Imprimir la respuesta JSON
echo json_encode($results);

// Cerrar conexión
$conn->close();
?>
