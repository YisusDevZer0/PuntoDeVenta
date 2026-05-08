<?php
// Controlador para registrar un cliente rápido (datos mínimos) en Data_Pacientes
include_once 'db_connect.php';

header('Content-Type: application/json');

$nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
$telefono = isset($_POST['telefono']) ? trim($_POST['telefono']) : '';
$fechaNacimiento = isset($_POST['fecha_nacimiento']) ? trim($_POST['fecha_nacimiento']) : '';
$sucursal = isset($_POST['sucursal']) ? intval($_POST['sucursal']) : 0;
$sucursalNombre = isset($_POST['sucursal_nombre']) ? trim($_POST['sucursal_nombre']) : '';
$licencia = isset($_POST['licencia']) ? trim($_POST['licencia']) : '';
$ingreso = isset($_POST['ingreso']) ? trim($_POST['ingreso']) : '';

if (empty($nombre)) {
    echo json_encode(['status' => 'error', 'message' => 'El nombre es obligatorio']);
    exit;
}

// Verificar si ya existe un cliente con el mismo nombre y teléfono
$sqlCheck = "SELECT ID_Data_Paciente, Nombre_Paciente, Telefono, Fecha_Nacimiento 
             FROM Data_Pacientes 
             WHERE Nombre_Paciente = ? AND Telefono = ? 
             LIMIT 1";
$stmtCheck = $conn->prepare($sqlCheck);
$stmtCheck->bind_param("ss", $nombre, $telefono);
$stmtCheck->execute();
$resultCheck = $stmtCheck->get_result();

if ($resultCheck->num_rows > 0) {
    // El cliente ya existe, devolver sus datos
    $existente = $resultCheck->fetch_assoc();
    echo json_encode([
        'status' => 'exists',
        'message' => 'El cliente ya existe',
        'cliente' => [
            'id' => $existente['ID_Data_Paciente'],
            'nombre' => $existente['Nombre_Paciente'],
            'telefono' => $existente['Telefono'],
            'fecha_nacimiento' => $existente['Fecha_Nacimiento']
        ]
    ]);
    $stmtCheck->close();
    mysqli_close($conn);
    exit;
}
$stmtCheck->close();

// Calcular edad
$edad = '';
if (!empty($fechaNacimiento)) {
    $nacimiento = new DateTime($fechaNacimiento);
    $hoy = new DateTime();
    $diff = $hoy->diff($nacimiento);
    $edad = $diff->y . ' años';
}

// Insertar nuevo cliente
$sql = "INSERT INTO Data_Pacientes (Nombre_Paciente, Fecha_Nacimiento, Edad, Sexo, Telefono, 
        Fk_Sucursal, SucursalVisita, Licencia, Ingreso, Sistema) 
        VALUES (?, ?, ?, '', ?, ?, ?, ?, ?, 'POSVENTAS')";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssisss", $nombre, $fechaNacimiento, $edad, $telefono, $sucursal, $sucursalNombre, $licencia, $ingreso);

if ($stmt->execute()) {
    $nuevoId = $stmt->insert_id;
    echo json_encode([
        'status' => 'success',
        'message' => 'Cliente registrado correctamente',
        'cliente' => [
            'id' => $nuevoId,
            'nombre' => $nombre,
            'telefono' => $telefono,
            'fecha_nacimiento' => $fechaNacimiento
        ]
    ]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Error al registrar cliente: ' . $conn->error]);
}

$stmt->close();
mysqli_close($conn);
?>
