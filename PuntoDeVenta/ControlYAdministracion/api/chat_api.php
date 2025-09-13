<?php
/**
 * API REST para el sistema de chat
 * Maneja todas las peticiones AJAX del frontend
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

// Incluir dependencias
include_once '../Controladores/db_connect.php';
include_once '../Controladores/ControladorUsuario.php';
include_once '../Controladores/ChatController.php';

// Verificar sesión
if (!isset($_SESSION['ControlMaestro']) && !isset($_SESSION['AdministradorRH']) && !isset($_SESSION['Marketing'])) {
    echo json_encode(['error' => 'Sesión no válida']);
    exit();
}

// Obtener datos del usuario
$usuario_id = isset($_SESSION['ControlMaestro']) ? $_SESSION['ControlMaestro'] : 
              (isset($_SESSION['AdministradorRH']) ? $_SESSION['AdministradorRH'] : $_SESSION['Marketing']);
$sucursal_id = $row['Fk_Sucursal'] ?? null;

// Inicializar controlador de chat
$chatController = new ChatController($conn, $usuario_id, $sucursal_id);

// Obtener método y acción
$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

try {
    switch ($method) {
        case 'GET':
            handleGetRequest($action, $chatController);
            break;
        case 'POST':
            handlePostRequest($action, $chatController);
            break;
        case 'PUT':
            handlePutRequest($action, $chatController);
            break;
        case 'DELETE':
            handleDeleteRequest($action, $chatController);
            break;
        default:
            echo json_encode(['error' => 'Método no soportado']);
    }
} catch (Exception $e) {
    echo json_encode(['error' => 'Error interno: ' . $e->getMessage()]);
}

/**
 * Manejar peticiones GET
 */
function handleGetRequest($action, $chatController) {
    switch ($action) {
        case 'conversaciones':
            $conversaciones = $chatController->obtenerConversaciones();
            echo json_encode(['success' => true, 'data' => $conversaciones]);
            break;
            
        case 'mensajes':
            $conversacion_id = $_GET['conversacion_id'] ?? null;
            $limite = $_GET['limite'] ?? 50;
            $offset = $_GET['offset'] ?? 0;
            
            if (!$conversacion_id) {
                echo json_encode(['success' => false, 'error' => 'ID de conversación requerido']);
                return;
            }
            
            $mensajes = $chatController->obtenerMensajes($conversacion_id, $limite, $offset);
            
            // Verificar si hay error en la respuesta
            if (isset($mensajes['error'])) {
                echo json_encode(['success' => false, 'error' => $mensajes['error']]);
                return;
            }
            
            // Asegurar que siempre devolvamos un array
            if (!is_array($mensajes)) {
                $mensajes = [];
            }
            
            echo json_encode(['success' => true, 'data' => $mensajes]);
            break;
            
        case 'usuarios':
            $conversacion_id = $_GET['conversacion_id'] ?? null;
            $usuarios = $chatController->obtenerUsuariosDisponibles($conversacion_id);
            echo json_encode(['success' => true, 'data' => $usuarios]);
            break;
            
        case 'configuracion':
            $config = obtenerConfiguracionUsuario($chatController);
            echo json_encode(['success' => true, 'data' => $config]);
            break;
            
        case 'notificaciones':
            $usuario_id = $_GET['usuario_id'] ?? $usuario_id;
            if (!$usuario_id) {
                echo json_encode(['success' => false, 'error' => 'ID de usuario requerido']);
                return;
            }
            
            include_once '../Controladores/NotificacionesController.php';
            $notificacionesController = new NotificacionesController($conn);
            $notificaciones = $notificacionesController->obtenerNotificacionesNoLeidas($usuario_id);
            echo json_encode(['success' => true, 'data' => $notificaciones]);
            break;
            
        case 'contador_notificaciones':
            $usuario_id = $_GET['usuario_id'] ?? $usuario_id;
            if (!$usuario_id) {
                echo json_encode(['success' => false, 'error' => 'ID de usuario requerido']);
                return;
            }
            
            $sql = "SELECT COUNT(*) as count FROM Notificaciones WHERE UsuarioID = ? AND Leido = 0";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $usuario_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $count = $result->fetch_assoc()['count'];
            
            echo json_encode(['success' => true, 'count' => $count]);
            break;
            
        case 'participantes':
            $conversacion_id = $_GET['conversacion_id'] ?? null;
            if (!$conversacion_id) {
                echo json_encode(['success' => false, 'error' => 'ID de conversación requerido']);
                return;
            }
            
            $participantes = $chatController->obtenerParticipantes($conversacion_id);
            echo json_encode(['success' => true, 'data' => $participantes]);
            break;
            
        default:
            echo json_encode(['error' => 'Acción no válida']);
    }
}

/**
 * Manejar peticiones POST
 */
function handlePostRequest($action, $chatController) {
    $input = json_decode(file_get_contents('php://input'), true);
    
    switch ($action) {
        case 'enviar_mensaje':
            $conversacion_id = $input['conversacion_id'] ?? null;
            $mensaje = $input['mensaje'] ?? '';
            $tipo_mensaje = $input['tipo_mensaje'] ?? 'texto';
            $mensaje_respuesta_id = $input['mensaje_respuesta_id'] ?? null;
            
            if (!$conversacion_id || empty($mensaje)) {
                echo json_encode(['error' => 'Datos requeridos faltantes']);
                return;
            }
            
            $resultado = $chatController->enviarMensaje($conversacion_id, $mensaje, $tipo_mensaje, null, $mensaje_respuesta_id);
            echo json_encode($resultado);
            break;
            
        case 'crear_conversacion':
            $nombre = $input['nombre'] ?? '';
            $tipo_conversacion = $input['tipo_conversacion'] ?? 'individual';
            $participantes = $input['participantes'] ?? [];
            
            if (empty($nombre)) {
                echo json_encode(['error' => 'Nombre de conversación requerido']);
                return;
            }
            
            $resultado = $chatController->crearConversacion($nombre, $tipo_conversacion, $participantes);
            echo json_encode($resultado);
            break;
            
        case 'agregar_participante':
            $conversacion_id = $input['conversacion_id'] ?? null;
            $usuario_id = $input['usuario_id'] ?? null;
            
            if (!$conversacion_id || !$usuario_id) {
                echo json_encode(['error' => 'IDs requeridos']);
                return;
            }
            
            $resultado = $chatController->agregarParticipante($conversacion_id, $usuario_id);
            echo json_encode($resultado);
            break;
            
        case 'subir_archivo':
            $conversacion_id = $_POST['conversacion_id'] ?? null;
            $mensaje = $_POST['mensaje'] ?? '';
            $mensaje_respuesta_id = $_POST['mensaje_respuesta_id'] ?? null;
            
            if (!$conversacion_id || !isset($_FILES['archivo'])) {
                echo json_encode(['error' => 'Datos requeridos faltantes']);
                return;
            }
            
            $archivo_data = $_FILES['archivo'];
            $tipo_mensaje = determinarTipoMensaje($archivo_data['type']);
            
            $resultado = $chatController->enviarMensaje($conversacion_id, $mensaje, $tipo_mensaje, $archivo_data, $mensaje_respuesta_id);
            echo json_encode($resultado);
            break;
            
        default:
            echo json_encode(['error' => 'Acción no válida']);
    }
}

/**
 * Manejar peticiones PUT
 */
function handlePutRequest($action, $chatController) {
    $input = json_decode(file_get_contents('php://input'), true);
    
    switch ($action) {
        case 'editar_mensaje':
            $mensaje_id = $input['mensaje_id'] ?? null;
            $nuevo_mensaje = $input['mensaje'] ?? '';
            
            if (!$mensaje_id || empty($nuevo_mensaje)) {
                echo json_encode(['error' => 'Datos requeridos faltantes']);
                return;
            }
            
            $resultado = $chatController->editarMensaje($mensaje_id, $nuevo_mensaje);
            echo json_encode($resultado);
            break;
            
        case 'actualizar_configuracion':
            $configuracion = $input['configuracion'] ?? [];
            $resultado = actualizarConfiguracionUsuario($chatController, $configuracion);
            echo json_encode($resultado);
            break;
            
        default:
            echo json_encode(['error' => 'Acción no válida']);
    }
}

/**
 * Manejar peticiones DELETE
 */
function handleDeleteRequest($action, $chatController) {
    $input = json_decode(file_get_contents('php://input'), true);
    
    switch ($action) {
        case 'eliminar_mensaje':
            $mensaje_id = $input['mensaje_id'] ?? null;
            
            if (!$mensaje_id) {
                echo json_encode(['error' => 'ID de mensaje requerido']);
                return;
            }
            
            $resultado = $chatController->eliminarMensaje($mensaje_id);
            echo json_encode($resultado);
            break;
            
        default:
            echo json_encode(['error' => 'Acción no válida']);
    }
}

/**
 * Determinar tipo de mensaje basado en el tipo de archivo
 */
function determinarTipoMensaje($mime_type) {
    if (strpos($mime_type, 'image/') === 0) {
        return 'imagen';
    } elseif (strpos($mime_type, 'video/') === 0) {
        return 'video';
    } elseif (strpos($mime_type, 'audio/') === 0) {
        return 'audio';
    } else {
        return 'archivo';
    }
}

/**
 * Obtener configuración del usuario
 */
function obtenerConfiguracionUsuario($chatController) {
    global $conn, $usuario_id;
    
    $sql = "SELECT * FROM chat_configuraciones WHERE usuario_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $usuario_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        return $result->fetch_assoc();
    } else {
        // Crear configuración por defecto
        $sql = "INSERT INTO chat_configuraciones (usuario_id) VALUES (?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $usuario_id);
        $stmt->execute();
        
        return [
            'usuario_id' => $usuario_id,
            'notificaciones_sonido' => 1,
            'notificaciones_push' => 1,
            'tema_oscuro' => 0,
            'mensajes_por_pagina' => 50,
            'auto_borrar_mensajes' => null
        ];
    }
}

/**
 * Actualizar configuración del usuario
 */
function actualizarConfiguracionUsuario($chatController, $configuracion) {
    global $conn, $usuario_id;
    
    $sql = "UPDATE chat_configuraciones SET 
            notificaciones_sonido = ?,
            notificaciones_push = ?,
            tema_oscuro = ?,
            mensajes_por_pagina = ?,
            auto_borrar_mensajes = ?
            WHERE usuario_id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiiiii",
        $configuracion['notificaciones_sonido'],
        $configuracion['notificaciones_push'],
        $configuracion['tema_oscuro'],
        $configuracion['mensajes_por_pagina'],
        $configuracion['auto_borrar_mensajes'],
        $usuario_id
    );
    
    if ($stmt->execute()) {
        return ['success' => true];
    } else {
        return ['error' => 'Error al actualizar configuración: ' . $stmt->error];
    }
}

// Agregar endpoint para marcar notificaciones como leídas
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action']) && $_GET['action'] === 'marcar_notificacion_leida') {
    $input = json_decode(file_get_contents('php://input'), true);
    $notificacion_id = $input['notificacion_id'] ?? null;
    
    if (!$notificacion_id) {
        echo json_encode(['success' => false, 'error' => 'ID de notificación requerido']);
        return;
    }
    
    include_once '../Controladores/NotificacionesController.php';
    $notificacionesController = new NotificacionesController($conn);
    $resultado = $notificacionesController->marcarComoLeida($notificacion_id, $usuario_id);
    
    if ($resultado) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Error al marcar notificación como leída']);
    }
}
?>
