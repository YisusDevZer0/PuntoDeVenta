<?php
/**
 * API de Recordatorios del Sistema - Doctor Pez
 * Maneja las peticiones AJAX para el sistema de recordatorios
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Manejar preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Incluir dependencias
include_once "../Controladores/ControladorUsuario.php";
include_once "../Controladores/RecordatoriosSistemaController.php";

// Verificar sesión
if(!isset($_SESSION['ControlMaestro']) && !isset($_SESSION['AdministradorRH']) && !isset($_SESSION['Marketing'])){
    echo json_encode([
        'success' => false,
        'message' => 'Sesión no válida'
    ]);
    exit();
}

// Obtener ID del usuario actual
$usuario_id = isset($_SESSION['ControlMaestro']) ? $_SESSION['ControlMaestro'] : 
            (isset($_SESSION['AdministradorRH']) ? $_SESSION['AdministradorRH'] : $_SESSION['Marketing']);

$sucursal_id = $row['Fk_Sucursal'] ?? 1;

// Verificar si las tablas de recordatorios existen
$tablas_existen = true;
$tablas_requeridas = [
    'recordatorios_sistema',
    'recordatorios_destinatarios', 
    'recordatorios_grupos',
    'recordatorios_logs'
];

foreach ($tablas_requeridas as $tabla) {
    $resultado = $con->query("SHOW TABLES LIKE '$tabla'");
    if ($resultado->num_rows == 0) {
        $tablas_existen = false;
        break;
    }
}

if (!$tablas_existen) {
    echo json_encode([
        'success' => false,
        'message' => 'Las tablas de recordatorios no están instaladas. Ejecuta el script de instalación primero.'
    ]);
    exit();
}

// Inicializar controlador
$recordatoriosController = new RecordatoriosSistemaController($con, $usuario_id);

// Obtener acción
$action = $_GET['action'] ?? $_POST['action'] ?? '';

try {
    switch ($action) {
        case 'crear':
            $resultado = crearRecordatorio($recordatoriosController, $usuario_id);
            break;
            
        case 'obtener':
            $resultado = obtenerRecordatorio($recordatoriosController);
            break;
            
        case 'listar':
            $resultado = listarRecordatorios($recordatoriosController);
            break;
            
        case 'actualizar':
            $resultado = actualizarRecordatorio($recordatoriosController, $usuario_id);
            break;
            
        case 'eliminar':
            $resultado = eliminarRecordatorio($recordatoriosController);
            break;
            
        case 'enviar':
            $resultado = enviarRecordatorio($recordatoriosController);
            break;
            
        case 'procesar_pendientes':
            $resultado = procesarPendientes($recordatoriosController);
            break;
            
        case 'estadisticas':
            $resultado = obtenerEstadisticas($recordatoriosController);
            break;
            
        case 'grupos':
            $resultado = gestionarGrupos($con, $usuario_id);
            break;
            
        case 'plantillas':
            $resultado = gestionarPlantillas($con, $usuario_id);
            break;
            
        default:
            $resultado = [
                'success' => false,
                'message' => 'Acción no válida'
            ];
    }
    
    echo json_encode($resultado);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error interno: ' . $e->getMessage()
    ]);
}

/**
 * Crear nuevo recordatorio
 */
function crearRecordatorio($controller, $usuario_id) {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        return [
            'success' => false,
            'message' => 'Datos no válidos'
        ];
    }
    
    // Agregar usuario creador
    $input['usuario_creador'] = $usuario_id;
    
    return $controller->crearRecordatorio($input);
}

/**
 * Obtener recordatorio específico
 */
function obtenerRecordatorio($controller) {
    $id = $_GET['id'] ?? null;
    
    if (!$id) {
        return [
            'success' => false,
            'message' => 'ID de recordatorio requerido'
        ];
    }
    
    // Obtener recordatorio usando consulta directa
    $sql = "SELECT * FROM v_recordatorios_completos WHERE id_recordatorio = ?";
    $stmt = $controller->conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        return [
            'success' => true,
            'recordatorio' => $row
        ];
    } else {
        return [
            'success' => false,
            'message' => 'Recordatorio no encontrado'
        ];
    }
}

/**
 * Listar recordatorios con filtros
 */
function listarRecordatorios($controller) {
    $filtros = [
        'estado' => $_GET['estado'] ?? null,
        'prioridad' => $_GET['prioridad'] ?? null,
        'fecha_desde' => $_GET['fecha_desde'] ?? null,
        'fecha_hasta' => $_GET['fecha_hasta'] ?? null,
        'sucursal_id' => $_GET['sucursal_id'] ?? null,
        'limit' => $_GET['limit'] ?? 50
    ];
    
    return $controller->obtenerRecordatorios($filtros);
}

/**
 * Actualizar recordatorio
 */
function actualizarRecordatorio($controller, $usuario_id) {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input || !isset($input['recordatorio_id'])) {
        return [
            'success' => false,
            'message' => 'Datos no válidos'
        ];
    }
    
    $id = $input['recordatorio_id'];
    unset($input['recordatorio_id']);
    
    // Agregar usuario modificador
    $input['usuario_modificador'] = $usuario_id;
    
    return $controller->actualizarRecordatorio($id, $input);
}

/**
 * Eliminar recordatorio
 */
function eliminarRecordatorio($controller) {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input || !isset($input['id'])) {
        return [
            'success' => false,
            'message' => 'ID de recordatorio requerido'
        ];
    }
    
    return $controller->eliminarRecordatorio($input['id']);
}

/**
 * Enviar recordatorio
 */
function enviarRecordatorio($controller) {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input || !isset($input['id'])) {
        return [
            'success' => false,
            'message' => 'ID de recordatorio requerido'
        ];
    }
    
    return $controller->enviarRecordatorio($input['id']);
}

/**
 * Procesar recordatorios pendientes
 */
function procesarPendientes($controller) {
    return $controller->procesarRecordatoriosPendientes();
}

/**
 * Obtener estadísticas
 */
function obtenerEstadisticas($controller) {
    $fecha_inicio = $_GET['fecha_inicio'] ?? date('Y-m-01');
    $fecha_fin = $_GET['fecha_fin'] ?? date('Y-m-t');
    
    return $controller->obtenerEstadisticas($fecha_inicio, $fecha_fin);
}

/**
 * Gestionar grupos de destinatarios
 */
function gestionarGrupos($con, $usuario_id) {
    $action = $_GET['grupo_action'] ?? $_POST['grupo_action'] ?? 'listar';
    
    switch ($action) {
        case 'listar':
            return listarGrupos($con);
        case 'crear':
            return crearGrupo($con, $usuario_id);
        case 'actualizar':
            return actualizarGrupo($con, $usuario_id);
        case 'eliminar':
            return eliminarGrupo($con);
        case 'agregar_miembro':
            return agregarMiembroGrupo($con);
        case 'quitar_miembro':
            return quitarMiembroGrupo($con);
        default:
            return [
                'success' => false,
                'message' => 'Acción de grupo no válida'
            ];
    }
}

/**
 * Listar grupos
 */
function listarGrupos($con) {
    $sql = "SELECT g.*, COUNT(gm.id_miembro) as total_miembros
            FROM recordatorios_grupos g
            LEFT JOIN recordatorios_grupos_miembros gm ON g.id_grupo = gm.grupo_id AND gm.activo = 1
            WHERE g.activo = 1
            GROUP BY g.id_grupo
            ORDER BY g.nombre_grupo";
    
    $result = $con->query($sql);
    $grupos = [];
    
    while ($row = $result->fetch_assoc()) {
        $grupos[] = $row;
    }
    
    return [
        'success' => true,
        'grupos' => $grupos
    ];
}

/**
 * Crear grupo
 */
function crearGrupo($con, $usuario_id) {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input || !isset($input['nombre_grupo'])) {
        return [
            'success' => false,
            'message' => 'Nombre del grupo requerido'
        ];
    }
    
    $sql = "INSERT INTO recordatorios_grupos (nombre_grupo, descripcion, usuario_creador) VALUES (?, ?, ?)";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("ssi", $input['nombre_grupo'], $input['descripcion'] ?? '', $usuario_id);
    
    if ($stmt->execute()) {
        return [
            'success' => true,
            'message' => 'Grupo creado exitosamente',
            'grupo_id' => $con->insert_id
        ];
    } else {
        return [
            'success' => false,
            'message' => 'Error al crear grupo: ' . $stmt->error
        ];
    }
}

/**
 * Gestionar plantillas de mensajes
 */
function gestionarPlantillas($con, $usuario_id) {
    $action = $_GET['plantilla_action'] ?? $_POST['plantilla_action'] ?? 'listar';
    
    switch ($action) {
        case 'listar':
            return listarPlantillas($con);
        case 'crear':
            return crearPlantilla($con, $usuario_id);
        case 'actualizar':
            return actualizarPlantilla($con, $usuario_id);
        case 'eliminar':
            return eliminarPlantilla($con);
        default:
            return [
                'success' => false,
                'message' => 'Acción de plantilla no válida'
            ];
    }
}

/**
 * Listar plantillas
 */
function listarPlantillas($con) {
    $sql = "SELECT * FROM recordatorios_plantillas WHERE activo = 1 ORDER BY nombre";
    $result = $con->query($sql);
    $plantillas = [];
    
    while ($row = $result->fetch_assoc()) {
        $plantillas[] = $row;
    }
    
    return [
        'success' => true,
        'plantillas' => $plantillas
    ];
}

/**
 * Crear plantilla
 */
function crearPlantilla($con, $usuario_id) {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input || !isset($input['nombre'])) {
        return [
            'success' => false,
            'message' => 'Nombre de plantilla requerido'
        ];
    }
    
    $sql = "INSERT INTO recordatorios_plantillas 
            (nombre, tipo, plantilla_whatsapp, plantilla_notificacion, variables_disponibles, usuario_creador) 
            VALUES (?, ?, ?, ?, ?, ?)";
    
    $stmt = $con->prepare($sql);
    $variables_json = isset($input['variables_disponibles']) ? json_encode($input['variables_disponibles']) : null;
    $stmt->bind_param("sssssi", 
        $input['nombre'], 
        $input['tipo'] ?? 'ambos',
        $input['plantilla_whatsapp'] ?? '',
        $input['plantilla_notificacion'] ?? '',
        $variables_json,
        $usuario_id
    );
    
    if ($stmt->execute()) {
        return [
            'success' => true,
            'message' => 'Plantilla creada exitosamente',
            'plantilla_id' => $con->insert_id
        ];
    } else {
        return [
            'success' => false,
            'message' => 'Error al crear plantilla: ' . $stmt->error
        ];
    }
}

/**
 * Obtener usuarios para selección
 */
function obtenerUsuarios($con) {
    $sucursal_id = $_GET['sucursal_id'] ?? null;
    
    $sql = "SELECT Id_PvUser, Nombre_Apellidos, Email, Telefono, ID_Sucursal 
            FROM Usuarios_PV 
            WHERE Activo = 1";
    
    $params = [];
    $types = '';
    
    if ($sucursal_id) {
        $sql .= " AND ID_Sucursal = ?";
        $params[] = $sucursal_id;
        $types .= 'i';
    }
    
    $sql .= " ORDER BY Nombre_Apellidos";
    
    $stmt = $con->prepare($sql);
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    
    $usuarios = [];
    while ($row = $result->fetch_assoc()) {
        $usuarios[] = $row;
    }
    
    return [
        'success' => true,
        'usuarios' => $usuarios
    ];
}

// Manejar acción de usuarios
if ($action === 'usuarios') {
    echo json_encode(obtenerUsuarios($con));
    exit();
}
?>
