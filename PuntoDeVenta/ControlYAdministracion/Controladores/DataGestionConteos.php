<?php
header('Content-Type: application/json');
include("db_connect.php");
include_once "ControladorUsuario.php";

// Si $conn no existe, crearlo
if (!isset($conn)) {
    $conn = new mysqli(getenv('DB_HOST') ?: 'localhost', 
                       getenv('DB_USER') ?: 'u858848268_devpezer0', 
                       getenv('DB_PASS') ?: 'F9+nIIOuCh8yI6wu4!08', 
                       getenv('DB_NAME') ?: 'u858848268_doctorpez');
    
    if ($conn->connect_error) {
        echo json_encode(['success' => false, 'message' => 'Error de conexión: ' . $conn->connect_error]);
        exit;
    }
    
    // Establecer zona horaria
    $conn->query("SET time_zone = '-6:00'");
}

// Obtener parámetros de filtros
$sucursal_param = isset($_GET['sucursal']) ? $_GET['sucursal'] : '';
$usuario_param = isset($_GET['usuario']) ? $_GET['usuario'] : '';
$fecha_desde = isset($_GET['fecha_desde']) ? trim($_GET['fecha_desde']) : '';
$fecha_hasta = isset($_GET['fecha_hasta']) ? trim($_GET['fecha_hasta']) : '';

// Validar y convertir sucursal
$sucursal = 0;
if (!empty($sucursal_param) && $sucursal_param !== '' && $sucursal_param !== '0') {
    $sucursal = (int)$sucursal_param;
}

// Validar usuario
$usuario = '';
if (!empty($usuario_param) && $usuario_param !== '') {
    $usuario = trim($usuario_param);
}

// Construir consulta para productos contados (completados)
$sql = "SELECT 
    itp.ID_Registro,
    itp.ID_Turno,
    itp.Folio_Turno,
    itp.ID_Prod_POS,
    itp.Cod_Barra,
    itp.Nombre_Producto,
    itp.Fk_sucursal,
    s.Nombre_Sucursal,
    itp.Existencias_Sistema,
    itp.Existencias_Fisicas,
    itp.Diferencia,
    itp.Usuario_Selecciono,
    itp.Fecha_Seleccion,
    itp.Fecha_Conteo,
    itp.Estado,
    it.Fecha_Turno,
    it.Hora_Inicio,
    it.Hora_Finalizacion,
    it.Estado as Estado_Turno
FROM Inventario_Turnos_Productos itp
INNER JOIN Inventario_Turnos it ON itp.ID_Turno = it.ID_Turno
INNER JOIN Sucursales s ON itp.Fk_sucursal = s.ID_Sucursal
WHERE itp.Estado = 'completado'";

$params = [];
$types = "";

// Aplicar filtros
if ($sucursal > 0) {
    $sql .= " AND itp.Fk_sucursal = ?";
    $params[] = $sucursal;
    $types .= "i";
}

if (!empty($usuario)) {
    $sql .= " AND itp.Usuario_Selecciono = ?";
    $params[] = $usuario;
    $types .= "s";
}

if (!empty($fecha_desde)) {
    $sql .= " AND DATE(it.Fecha_Turno) >= ?";
    $params[] = $fecha_desde;
    $types .= "s";
}

if (!empty($fecha_hasta)) {
    $sql .= " AND DATE(it.Fecha_Turno) <= ?";
    $params[] = $fecha_hasta;
    $types .= "s";
}

$sql .= " ORDER BY it.Fecha_Turno DESC, itp.Fecha_Conteo DESC LIMIT 1000";

// Ejecutar consulta
$stmt = $conn->prepare($sql);
$data = [];
$sin_diferencias = 0;
$con_diferencias = 0;
$usuarios_unicos = [];

if ($stmt) {
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($fila = $result->fetch_assoc()) {
        // Calcular estadísticas antes de formatear
        $diferencia_valor = (int)$fila['Diferencia'];
        $tiene_diferencia = abs($diferencia_valor) > 0;
        
        if ($tiene_diferencia) {
            $con_diferencias++;
        } else {
            $sin_diferencias++;
        }
        
        // Agregar usuario único
        if (!empty($fila['Usuario_Selecciono']) && !in_array($fila['Usuario_Selecciono'], $usuarios_unicos)) {
            $usuarios_unicos[] = $fila['Usuario_Selecciono'];
        }
        
        // Formatear datos para la tabla
        $clase_fila = $tiene_diferencia ? 'producto-con-diferencia' : 'producto-sin-diferencia';
        
        $badge_diferencia = '';
        if ($tiene_diferencia) {
            $color = $diferencia_valor > 0 ? 'success' : 'danger';
            $signo = $diferencia_valor > 0 ? '+' : '';
            $badge_diferencia = '<span class="badge bg-' . $color . '">' . $signo . number_format($diferencia_valor) . '</span>';
        } else {
            $badge_diferencia = '<span class="badge bg-success">Sin diferencia</span>';
        }
        
        $data[] = [
            "Folio_Turno" => $fila['Folio_Turno'],
            "Cod_Barra" => $fila['Cod_Barra'],
            "Nombre_Producto" => $fila['Nombre_Producto'],
            "Sucursal" => $fila['Nombre_Sucursal'],
            "Usuario" => $fila['Usuario_Selecciono'],
            "Existencias_Sistema" => number_format($fila['Existencias_Sistema']),
            "Existencias_Fisicas" => number_format($fila['Existencias_Fisicas']),
            "Diferencia" => $badge_diferencia,
            "Fecha_Turno" => date('d/m/Y', strtotime($fila['Fecha_Turno'])),
            "Fecha_Conteo" => $fila['Fecha_Conteo'] ? date('d/m/Y H:i', strtotime($fila['Fecha_Conteo'])) : '-',
            "Estado_Turno" => '<span class="badge bg-' . ($fila['Estado_Turno'] == 'finalizado' ? 'success' : 'warning') . '">' . ucfirst($fila['Estado_Turno']) . '</span>',
            "clase_fila" => $clase_fila
        ];
    }
    
    $stmt->close();
}

// Calcular total de productos
$total_productos = count($data);

// Construir respuesta JSON
$results = [
    "success" => true,
    "sEcho" => 1,
    "iTotalRecords" => $total_productos,
    "iTotalDisplayRecords" => $total_productos,
    "aaData" => $data,
    "estadisticas" => [
        "total_productos" => $total_productos,
        "sin_diferencias" => $sin_diferencias,
        "con_diferencias" => $con_diferencias,
        "usuarios_activos" => count($usuarios_unicos)
    ]
];

echo json_encode($results);
$conn->close();
?>
