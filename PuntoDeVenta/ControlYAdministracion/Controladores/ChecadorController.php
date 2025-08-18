<?php
// Habilitar reporte de errores para debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Logging para debugging
function logError($message) {
    error_log("ChecadorController Error: " . $message);
}

try {
    // Verificar si los archivos existen antes de incluirlos
    $controladorUsuarioPath = "../Controladores/ControladorUsuario.php";
    $dbConnectPath = "../Controladores/db_connect.php";
    
    if (!file_exists($controladorUsuarioPath)) {
        throw new Exception("Archivo ControladorUsuario.php no encontrado en: " . $controladorUsuarioPath);
    }
    
    if (!file_exists($dbConnectPath)) {
        throw new Exception("Archivo db_connect.php no encontrado en: " . $dbConnectPath);
    }
    
    include_once $controladorUsuarioPath;
    include_once $dbConnectPath;
    
    // Verificar si la conexión a la base de datos está disponible
    if (!isset($conn) || !$conn) {
        throw new Exception("Conexión a la base de datos no disponible");
    }
    
} catch (Exception $e) {
    logError($e->getMessage());
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false, 
        'message' => 'Error de configuración: ' . $e->getMessage(),
        'debug_info' => [
            'file' => __FILE__,
            'line' => __LINE__,
            'error' => $e->getMessage()
        ]
    ]);
    exit();
}

// Verificar sesión solo si no es una prueba directa
$isTestMode = isset($_GET['test']) || isset($_POST['test_mode']);
$skipAuth = $isTestMode;

if (!$skipAuth) {
    // Verificar sesión usando las variables correctas del sistema
    if(!isset($_SESSION['ControlMaestro']) && !isset($_SESSION['AdministradorRH']) && !isset($_SESSION['Marketing'])){
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false, 
            'message' => 'Sesión no válida o expirada',
            'redirect' => '../Expiro.php'
        ]);
        exit();
    }
}

// Asegurar que $row esté disponible
if (!isset($row)) {
    try {
        include_once "../Controladores/ControladorUsuario.php";
    } catch (Exception $e) {
        logError("Error incluyendo ControladorUsuario: " . $e->getMessage());
    }
}

// Determinar el ID de usuario según la sesión activa o modo de prueba
if ($skipAuth) {
    $userId = isset($_POST['usuario_id']) ? $_POST['usuario_id'] : 1; // ID por defecto para pruebas
} else {
    $userId = isset($_SESSION['ControlMaestro']) ? $_SESSION['ControlMaestro'] : (isset($_SESSION['AdministradorRH']) ? $_SESSION['AdministradorRH'] : $_SESSION['Marketing']);
}

class ChecadorController {
    private $conn;
    
    public function __construct($conn) {
        $this->conn = $conn;
    }
    
    /**
     * Registrar entrada o salida del empleado
     */
    public function registrarAsistencia($usuario_id, $tipo, $latitud, $longitud, $timestamp) {
        try {
            // Verificar conexión a la base de datos
            if (!$this->conn) {
                return ['success' => false, 'message' => 'Error de conexión a la base de datos'];
            }
            
            // Validar parámetros
            if (empty($usuario_id) || empty($tipo) || empty($latitud) || empty($longitud) || empty($timestamp)) {
                return ['success' => false, 'message' => 'Parámetros incompletos'];
            }
            
            // Validar que el usuario existe usando la tabla correcta
            $stmt = $this->conn->prepare("SELECT Id_PvUser, Nombre_Apellidos FROM Usuarios_PV WHERE Id_PvUser = ?");
            if (!$stmt) {
                return ['success' => false, 'message' => 'Error preparando consulta de usuario: ' . $this->conn->error];
            }
            
            $stmt->bind_param("i", $usuario_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows === 0) {
                return ['success' => false, 'message' => 'Usuario no encontrado (ID: ' . $usuario_id . ')'];
            }
            
            $usuario = $result->fetch_assoc();
            
            // Verificar si ya existe un registro para hoy del mismo tipo
            $fecha_hoy = date('Y-m-d');
            $stmt = $this->conn->prepare("
                SELECT id FROM asistencias 
                WHERE usuario_id = ? AND tipo = ? AND DATE(fecha_hora) = ?
            ");
            if (!$stmt) {
                return ['success' => false, 'message' => 'Error preparando consulta de verificación: ' . $this->conn->error];
            }
            
            $stmt->bind_param("iss", $usuario_id, $tipo, $fecha_hoy);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                return ['success' => false, 'message' => "Ya existe un registro de $tipo para hoy"];
            }
            
            // Insertar el registro de asistencia
            $stmt = $this->conn->prepare("
                INSERT INTO asistencias (usuario_id, tipo, latitud, longitud, fecha_hora, created_at) 
                VALUES (?, ?, ?, ?, ?, NOW())
            ");
            if (!$stmt) {
                return ['success' => false, 'message' => 'Error preparando inserción: ' . $this->conn->error];
            }
            
            $stmt->bind_param("isdd", $usuario_id, $tipo, $latitud, $longitud, $timestamp);
            
            if ($stmt->execute()) {
                return [
                    'success' => true, 
                    'message' => "$tipo registrada exitosamente",
                    'data' => [
                        'usuario' => $usuario['Nombre_Apellidos'],
                        'tipo' => $tipo,
                        'fecha' => $timestamp
                    ]
                ];
            } else {
                return ['success' => false, 'message' => 'Error al ejecutar inserción: ' . $stmt->error];
            }
            
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }
    
    /**
     * Obtener ubicaciones de trabajo del usuario
     */
    public function obtenerUbicacionesUsuario($usuario_id) {
        try {
            $stmt = $this->conn->prepare("
                SELECT id, nombre, descripcion, latitud, longitud, radio, direccion, estado, created_at
                FROM ubicaciones_trabajo 
                WHERE usuario_id = ? AND estado = 'active'
                ORDER BY created_at DESC
            ");
            $stmt->bind_param("i", $usuario_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $ubicaciones = [];
            while ($row = $result->fetch_assoc()) {
                $ubicaciones[] = $row;
            }
            
            return ['success' => true, 'data' => $ubicaciones];
            
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }
    
    /**
     * Guardar ubicación de trabajo
     */
    public function guardarUbicacion($usuario_id, $nombre, $descripcion, $latitud, $longitud, $radio, $direccion, $estado) {
        try {
            $stmt = $this->conn->prepare("
                INSERT INTO ubicaciones_trabajo 
                (usuario_id, nombre, descripcion, latitud, longitud, radio, direccion, estado, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())
            ");
            $stmt->bind_param("issddiss", $usuario_id, $nombre, $descripcion, $latitud, $longitud, $radio, $direccion, $estado);
            
            if ($stmt->execute()) {
                return ['success' => true, 'message' => 'Ubicación guardada exitosamente', 'id' => $stmt->insert_id];
            } else {
                return ['success' => false, 'message' => 'Error al guardar la ubicación'];
            }
            
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }
    
    /**
     * Actualizar ubicación de trabajo
     */
    public function actualizarUbicacion($ubicacion_id, $usuario_id, $nombre, $descripcion, $latitud, $longitud, $radio, $direccion, $estado) {
        try {
            $stmt = $this->conn->prepare("
                UPDATE ubicaciones_trabajo 
                SET nombre = ?, descripcion = ?, latitud = ?, longitud = ?, radio = ?, direccion = ?, estado = ?, updated_at = NOW()
                WHERE id = ? AND usuario_id = ?
            ");
            $stmt->bind_param("ssddissii", $nombre, $descripcion, $latitud, $longitud, $radio, $direccion, $estado, $ubicacion_id, $usuario_id);
            
            if ($stmt->execute()) {
                return ['success' => true, 'message' => 'Ubicación actualizada exitosamente'];
            } else {
                return ['success' => false, 'message' => 'Error al actualizar la ubicación'];
            }
            
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }
    
    /**
     * Eliminar ubicación de trabajo
     */
    public function eliminarUbicacion($ubicacion_id, $usuario_id) {
        try {
            $stmt = $this->conn->prepare("DELETE FROM ubicaciones_trabajo WHERE id = ? AND usuario_id = ?");
            $stmt->bind_param("ii", $ubicacion_id, $usuario_id);
            
            if ($stmt->execute()) {
                return ['success' => true, 'message' => 'Ubicación eliminada exitosamente'];
            } else {
                return ['success' => false, 'message' => 'Error al eliminar la ubicación'];
            }
            
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }
    
    /**
     * Verificar si el usuario está en el área de trabajo
     */
    public function verificarUbicacion($usuario_id, $latitud_actual, $longitud_actual) {
        try {
            // Obtener ubicaciones activas del usuario
            $stmt = $this->conn->prepare("
                SELECT id, nombre, latitud, longitud, radio
                FROM ubicaciones_trabajo 
                WHERE usuario_id = ? AND estado = 'active'
            ");
            $stmt->bind_param("i", $usuario_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $ubicaciones = [];
            while ($row = $result->fetch_assoc()) {
                $distancia = $this->calcularDistancia(
                    $latitud_actual, $longitud_actual,
                    $row['latitud'], $row['longitud']
                );
                
                if ($distancia <= $row['radio'] / 1000) { // Convertir radio a km
                    $ubicaciones[] = [
                        'id' => $row['id'],
                        'nombre' => $row['nombre'],
                        'distancia' => $distancia * 1000 // Convertir a metros
                    ];
                }
            }
            
            return [
                'success' => true,
                'en_area' => count($ubicaciones) > 0,
                'ubicaciones' => $ubicaciones
            ];
            
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }
    
    /**
     * Calcular distancia entre dos puntos usando la fórmula de Haversine
     */
    private function calcularDistancia($lat1, $lon1, $lat2, $lon2) {
        $R = 6371; // Radio de la Tierra en km
        
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        
        $a = sin($dLat/2) * sin($dLat/2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon/2) * sin($dLon/2);
        
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        
        return $R * $c; // Distancia en km
    }
    
    /**
     * Obtener historial de asistencias del usuario
     */
    public function obtenerHistorialAsistencias($usuario_id, $fecha_inicio = null, $fecha_fin = null) {
        try {
            $sql = "
                SELECT id, tipo, latitud, longitud, fecha_hora, created_at
                FROM asistencias 
                WHERE usuario_id = ?
            ";
            
            $params = [$usuario_id];
            $types = "i";
            
            if ($fecha_inicio && $fecha_fin) {
                $sql .= " AND DATE(fecha_hora) BETWEEN ? AND ?";
                $params[] = $fecha_inicio;
                $params[] = $fecha_fin;
                $types .= "ss";
            }
            
            $sql .= " ORDER BY fecha_hora DESC";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param($types, ...$params);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $asistencias = [];
            while ($row = $result->fetch_assoc()) {
                $asistencias[] = $row;
            }
            
            return ['success' => true, 'data' => $asistencias];
            
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }
    
    /**
     * Obtener estadísticas de asistencia del usuario
     */
    public function obtenerEstadisticasAsistencia($usuario_id, $mes = null, $año = null) {
        try {
            if (!$mes) $mes = date('m');
            if (!$año) $año = date('Y');
            
            $stmt = $this->conn->prepare("
                SELECT 
                    COUNT(CASE WHEN tipo = 'entrada' THEN 1 END) as total_entradas,
                    COUNT(CASE WHEN tipo = 'salida' THEN 1 END) as total_salidas,
                    COUNT(*) as total_registros
                FROM asistencias 
                WHERE usuario_id = ? 
                AND MONTH(fecha_hora) = ? 
                AND YEAR(fecha_hora) = ?
            ");
            $stmt->bind_param("iii", $usuario_id, $mes, $año);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $estadisticas = $result->fetch_assoc();
            
            return ['success' => true, 'data' => $estadisticas];
            
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }
}

// Manejar requests AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $controller = new ChecadorController($conn);
        $action = $_POST['action'] ?? '';
        $response = [];
        
        // Log de la acción recibida
        logError("Acción recibida: " . $action . " - Usuario ID: " . $userId);
        
        switch ($action) {
            case 'registrar_asistencia':
                try {
                    $usuario_id = $userId;
                    $tipo = $_POST['tipo'];
                    $latitud = $_POST['latitud'];
                    $longitud = $_POST['longitud'];
                    $timestamp = $_POST['timestamp'];
                    
                    // Log de los datos recibidos
                    logError("Datos recibidos - Usuario: $usuario_id, Tipo: $tipo, Lat: $latitud, Lon: $longitud, Time: $timestamp");
                    
                    // Validar datos requeridos
                    if (empty($usuario_id) || empty($tipo) || empty($latitud) || empty($longitud) || empty($timestamp)) {
                        $response = ['success' => false, 'message' => 'Datos incompletos para registrar asistencia'];
                        logError("Datos incompletos para registrar asistencia");
                        break;
                    }
                    
                    $response = $controller->registrarAsistencia($usuario_id, $tipo, $latitud, $longitud, $timestamp);
                    logError("Respuesta del controlador: " . json_encode($response));
                } catch (Exception $e) {
                    $response = ['success' => false, 'message' => 'Error en registro: ' . $e->getMessage()];
                    logError("Error en registro: " . $e->getMessage());
                }
                break;
            
        case 'obtener_ubicaciones':
            $usuario_id = $userId;
            $response = $controller->obtenerUbicacionesUsuario($usuario_id);
            break;
            
        case 'guardar_ubicacion':
            $usuario_id = $userId;
            $nombre = $_POST['nombre'];
            $descripcion = $_POST['descripcion'] ?? '';
            $latitud = $_POST['latitud'];
            $longitud = $_POST['longitud'];
            $radio = $_POST['radio'];
            $direccion = $_POST['direccion'] ?? '';
            $estado = $_POST['estado'];
            
            $response = $controller->guardarUbicacion($usuario_id, $nombre, $descripcion, $latitud, $longitud, $radio, $direccion, $estado);
            break;
            
        case 'actualizar_ubicacion':
            $ubicacion_id = $_POST['ubicacion_id'];
            $usuario_id = $userId;
            $nombre = $_POST['nombre'];
            $descripcion = $_POST['descripcion'] ?? '';
            $latitud = $_POST['latitud'];
            $longitud = $_POST['longitud'];
            $radio = $_POST['radio'];
            $direccion = $_POST['direccion'] ?? '';
            $estado = $_POST['estado'];
            
            $response = $controller->actualizarUbicacion($ubicacion_id, $usuario_id, $nombre, $descripcion, $latitud, $longitud, $radio, $direccion, $estado);
            break;
            
        case 'eliminar_ubicacion':
            $ubicacion_id = $_POST['ubicacion_id'];
            $usuario_id = $userId;
            
            $response = $controller->eliminarUbicacion($ubicacion_id, $usuario_id);
            break;
            
        case 'verificar_ubicacion':
            $usuario_id = $userId;
            $latitud = $_POST['latitud'];
            $longitud = $_POST['longitud'];
            
            $response = $controller->verificarUbicacion($usuario_id, $latitud, $longitud);
            break;
            
        case 'obtener_historial':
            $usuario_id = $userId;
            $fecha_inicio = $_POST['fecha_inicio'] ?? null;
            $fecha_fin = $_POST['fecha_fin'] ?? null;
            
            $response = $controller->obtenerHistorialAsistencias($usuario_id, $fecha_inicio, $fecha_fin);
            break;
            
        case 'obtener_estadisticas':
            $usuario_id = $userId;
            $mes = $_POST['mes'] ?? null;
            $año = $_POST['año'] ?? null;
            
            $response = $controller->obtenerEstadisticasAsistencia($usuario_id, $mes, $año);
            break;
            
        default:
            $response = ['success' => false, 'message' => 'Acción no válida'];
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
    
} catch (Exception $e) {
    logError("Error general en el controlador: " . $e->getMessage());
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false, 
        'message' => 'Error interno del servidor: ' . $e->getMessage(),
        'debug_info' => [
            'file' => __FILE__,
            'line' => __LINE__,
            'error' => $e->getMessage()
        ]
    ]);
    exit;
}
?> 