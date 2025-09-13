<?php
/**
 * Controlador de Recordatorios del Sistema - Doctor Pez
 * Maneja la lógica de negocio para el sistema de recordatorios
 * con envío por WhatsApp y notificaciones internas
 */

include_once "../Consultas/db_connect.php";
include_once "WhatsAppService.php";
include_once "NotificacionesService.php";

class RecordatoriosSistemaController {
    private $conn;
    private $usuario_id;
    private $whatsappService;
    private $notificacionesService;
    
    public function __construct($connection, $usuario_id = null) {
        $this->conn = $connection;
        $this->usuario_id = $usuario_id;
        $this->whatsappService = new WhatsAppService($connection);
        $this->notificacionesService = new NotificacionesService($connection);
    }
    
    /**
     * Crear un nuevo recordatorio
     */
    public function crearRecordatorio($datos) {
        try {
            // Validar datos requeridos
            $this->validarDatosRecordatorio($datos);
            
            // Preparar datos
            $titulo = $datos['titulo'];
            $descripcion = $datos['descripcion'] ?? '';
            $mensaje_whatsapp = $datos['mensaje_whatsapp'] ?? '';
            $mensaje_notificacion = $datos['mensaje_notificacion'] ?? '';
            $fecha_programada = $datos['fecha_programada'];
            $prioridad = $datos['prioridad'] ?? 'media';
            $tipo_envio = $datos['tipo_envio'] ?? 'ambos';
            $destinatarios = $datos['destinatarios'] ?? 'todos';
            $sucursal_id = $datos['sucursal_id'] ?? null;
            $grupo_id = $datos['grupo_id'] ?? null;
            $configuracion_adicional = isset($datos['configuracion_adicional']) ? 
                json_encode($datos['configuracion_adicional']) : null;
            
            // Insertar recordatorio
            $sql = "INSERT INTO recordatorios_sistema 
                    (titulo, descripcion, mensaje_whatsapp, mensaje_notificacion, 
                     fecha_programada, prioridad, tipo_envio, destinatarios, 
                     sucursal_id, grupo_id, usuario_creador, configuracion_adicional) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("ssssssssiiis", 
                $titulo, $descripcion, $mensaje_whatsapp, $mensaje_notificacion,
                $fecha_programada, $prioridad, $tipo_envio, $destinatarios,
                $sucursal_id, $grupo_id, $this->usuario_id, $configuracion_adicional);
            
            if (!$stmt->execute()) {
                throw new Exception("Error al crear recordatorio: " . $stmt->error);
            }
            
            $recordatorio_id = $this->conn->insert_id;
            
            // Procesar destinatarios
            $this->procesarDestinatarios($recordatorio_id, $destinatarios, $datos);
            
            // Log de creación
            $this->registrarLog($recordatorio_id, 'ambos', 'iniciado', 
                'Recordatorio creado exitosamente');
            
            return [
                'success' => true,
                'message' => 'Recordatorio creado exitosamente',
                'recordatorio_id' => $recordatorio_id
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Obtener recordatorios con filtros
     */
    public function obtenerRecordatorios($filtros = []) {
        try {
            $where_conditions = [];
            $params = [];
            $types = '';
            
            // Filtro por estado
            if (isset($filtros['estado'])) {
                $where_conditions[] = "r.estado = ?";
                $params[] = $filtros['estado'];
                $types .= 's';
            }
            
            // Filtro por prioridad
            if (isset($filtros['prioridad'])) {
                $where_conditions[] = "r.prioridad = ?";
                $params[] = $filtros['prioridad'];
                $types .= 's';
            }
            
            // Filtro por fecha desde
            if (isset($filtros['fecha_desde'])) {
                $where_conditions[] = "DATE(r.fecha_programada) >= ?";
                $params[] = $filtros['fecha_desde'];
                $types .= 's';
            }
            
            // Filtro por fecha hasta
            if (isset($filtros['fecha_hasta'])) {
                $where_conditions[] = "DATE(r.fecha_programada) <= ?";
                $params[] = $filtros['fecha_hasta'];
                $types .= 's';
            }
            
            // Filtro por sucursal
            if (isset($filtros['sucursal_id'])) {
                $where_conditions[] = "(r.sucursal_id = ? OR r.sucursal_id IS NULL)";
                $params[] = $filtros['sucursal_id'];
                $types .= 'i';
            }
            
            $where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';
            
            $sql = "SELECT * FROM v_recordatorios_completos r $where_clause 
                    ORDER BY r.fecha_programada DESC 
                    LIMIT " . ($filtros['limit'] ?? 50);
            
            $stmt = $this->conn->prepare($sql);
            if (!empty($params)) {
                $stmt->bind_param($types, ...$params);
            }
            $stmt->execute();
            $result = $stmt->get_result();
            
            $recordatorios = [];
            while ($row = $result->fetch_assoc()) {
                $recordatorios[] = $row;
            }
            
            return [
                'success' => true,
                'recordatorios' => $recordatorios
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'recordatorios' => []
            ];
        }
    }
    
    /**
     * Obtener recordatorios pendientes para envío
     */
    public function obtenerRecordatoriosPendientes() {
        try {
            $sql = "SELECT * FROM v_recordatorios_completos 
                    WHERE estado = 'programado' 
                    AND fecha_programada <= NOW()
                    ORDER BY prioridad DESC, fecha_programada ASC";
            
            $result = $this->conn->query($sql);
            $recordatorios = [];
            
            while ($row = $result->fetch_assoc()) {
                $recordatorios[] = $row;
            }
            
            return [
                'success' => true,
                'recordatorios' => $recordatorios
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'recordatorios' => []
            ];
        }
    }
    
    /**
     * Enviar un recordatorio específico
     */
    public function enviarRecordatorio($recordatorio_id) {
        try {
            // Obtener recordatorio
            $recordatorio = $this->obtenerRecordatorio($recordatorio_id);
            if (!$recordatorio['success']) {
                throw new Exception("Recordatorio no encontrado");
            }
            
            $recordatorio_data = $recordatorio['recordatorio'];
            
            // Actualizar estado a enviando
            $this->actualizarEstadoRecordatorio($recordatorio_id, 'enviando');
            
            // Obtener destinatarios
            $destinatarios = $this->obtenerDestinatarios($recordatorio_id);
            
            $enviados = 0;
            $errores = 0;
            
            foreach ($destinatarios as $destinatario) {
                try {
                    // Enviar por WhatsApp si está configurado
                    if (strpos($recordatorio_data['tipo_envio'], 'whatsapp') !== false) {
                        $this->enviarWhatsApp($destinatario, $recordatorio_data);
                    }
                    
                    // Enviar notificación interna si está configurado
                    if (strpos($recordatorio_data['tipo_envio'], 'notificacion') !== false) {
                        $this->enviarNotificacionInterna($destinatario, $recordatorio_data);
                    }
                    
                    // Actualizar estado del destinatario
                    $this->actualizarEstadoDestinatario($destinatario['id_destinatario'], 'enviado');
                    $enviados++;
                    
                } catch (Exception $e) {
                    $this->actualizarEstadoDestinatario($destinatario['id_destinatario'], 'error', $e->getMessage());
                    $errores++;
                }
            }
            
            // Actualizar estado final del recordatorio
            $estado_final = $errores > 0 ? 'error' : 'enviado';
            $this->actualizarEstadoRecordatorio($recordatorio_id, $estado_final);
            
            return [
                'success' => true,
                'message' => "Recordatorio procesado: $enviados enviados, $errores errores",
                'enviados' => $enviados,
                'errores' => $errores
            ];
            
        } catch (Exception $e) {
            $this->actualizarEstadoRecordatorio($recordatorio_id, 'error');
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
    
    // ========== MÉTODOS PRIVADOS ==========
    
    private function validarDatosRecordatorio($datos) {
        $campos_requeridos = ['titulo', 'fecha_programada'];
        
        foreach ($campos_requeridos as $campo) {
            if (!isset($datos[$campo]) || empty($datos[$campo])) {
                throw new Exception("El campo '$campo' es requerido");
            }
        }
        
        // Validar fecha
        if (!strtotime($datos['fecha_programada'])) {
            throw new Exception("La fecha programada no es válida");
        }
        
        // Validar prioridad
        $prioridades_validas = ['baja', 'media', 'alta', 'urgente'];
        if (isset($datos['prioridad']) && !in_array($datos['prioridad'], $prioridades_validas)) {
            throw new Exception("La prioridad debe ser: " . implode(', ', $prioridades_validas));
        }
    }
    
    private function procesarDestinatarios($recordatorio_id, $tipo_destinatarios, $datos) {
        $destinatarios = [];
        
        switch ($tipo_destinatarios) {
            case 'todos':
                $destinatarios = $this->obtenerTodosUsuarios();
                break;
            case 'sucursal':
                $sucursal_id = $datos['sucursal_id'] ?? null;
                if (!$sucursal_id) {
                    throw new Exception("Se requiere sucursal_id para destinatarios de sucursal");
                }
                $destinatarios = $this->obtenerUsuariosSucursal($sucursal_id);
                break;
            case 'grupo':
                $grupo_id = $datos['grupo_id'] ?? null;
                if (!$grupo_id) {
                    throw new Exception("Se requiere grupo_id para destinatarios de grupo");
                }
                $destinatarios = $this->obtenerUsuariosGrupo($grupo_id);
                break;
            case 'individual':
                $usuarios_especificos = $datos['usuarios_especificos'] ?? [];
                if (empty($usuarios_especificos)) {
                    throw new Exception("Se requiere lista de usuarios para destinatarios individuales");
                }
                $destinatarios = $this->obtenerUsuariosEspecificos($usuarios_especificos);
                break;
        }
        
        // Insertar destinatarios
        foreach ($destinatarios as $usuario) {
            $this->insertarDestinatario($recordatorio_id, $usuario);
        }
    }
    
    private function insertarDestinatario($recordatorio_id, $usuario) {
        $sql = "INSERT INTO recordatorios_destinatarios 
                (recordatorio_id, usuario_id, telefono_whatsapp, tipo_envio) 
                VALUES (?, ?, ?, 'ambos')";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("iis", $recordatorio_id, $usuario['Id_PvUser'], $usuario['Telefono']);
        $stmt->execute();
    }
    
    private function obtenerTodosUsuarios() {
        $sql = "SELECT Id_PvUser, Nombre_Apellidos, Email, Telefono, ID_Sucursal 
                FROM Usuarios_PV WHERE Activo = 1";
        $result = $this->conn->query($sql);
        
        $usuarios = [];
        while ($row = $result->fetch_assoc()) {
            $usuarios[] = $row;
        }
        return $usuarios;
    }
    
    private function obtenerUsuariosSucursal($sucursal_id) {
        $sql = "SELECT Id_PvUser, Nombre_Apellidos, Email, Telefono, ID_Sucursal 
                FROM Usuarios_PV 
                WHERE Activo = 1 AND ID_Sucursal = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $sucursal_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $usuarios = [];
        while ($row = $result->fetch_assoc()) {
            $usuarios[] = $row;
        }
        return $usuarios;
    }
    
    private function obtenerUsuariosGrupo($grupo_id) {
        $sql = "SELECT u.Id_PvUser, u.Nombre_Apellidos, u.Email, u.Telefono, u.ID_Sucursal
                FROM Usuarios_PV u
                INNER JOIN recordatorios_grupos_miembros rgm ON u.Id_PvUser = rgm.usuario_id
                WHERE rgm.grupo_id = ? AND rgm.activo = 1 AND u.Activo = 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $grupo_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $usuarios = [];
        while ($row = $result->fetch_assoc()) {
            $usuarios[] = $row;
        }
        return $usuarios;
    }
    
    private function obtenerUsuariosEspecificos($usuario_ids) {
        $placeholders = str_repeat('?,', count($usuario_ids) - 1) . '?';
        $sql = "SELECT Id_PvUser, Nombre_Apellidos, Email, Telefono, ID_Sucursal 
                FROM Usuarios_PV 
                WHERE Id_PvUser IN ($placeholders) AND Activo = 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param(str_repeat('i', count($usuario_ids)), ...$usuario_ids);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $usuarios = [];
        while ($row = $result->fetch_assoc()) {
            $usuarios[] = $row;
        }
        return $usuarios;
    }
    
    private function obtenerRecordatorio($id) {
        $sql = "SELECT * FROM v_recordatorios_completos WHERE id_recordatorio = ?";
        $stmt = $this->conn->prepare($sql);
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
    
    private function obtenerDestinatarios($recordatorio_id) {
        $sql = "SELECT * FROM v_recordatorios_destinatarios_completos 
                WHERE recordatorio_id = ? AND estado_envio = 'pendiente'";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $recordatorio_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $destinatarios = [];
        while ($row = $result->fetch_assoc()) {
            $destinatarios[] = $row;
        }
        return $destinatarios;
    }
    
    private function actualizarEstadoRecordatorio($id, $estado) {
        $sql = "UPDATE recordatorios_sistema SET estado = ? WHERE id_recordatorio = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("si", $estado, $id);
        $stmt->execute();
    }
    
    private function actualizarEstadoDestinatario($id, $estado, $error = null) {
        $sql = "UPDATE recordatorios_destinatarios 
                SET estado_envio = ?, fecha_envio = NOW(), error_envio = ? 
                WHERE id_destinatario = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ssi", $estado, $error, $id);
        $stmt->execute();
    }
    
    private function enviarWhatsApp($destinatario, $recordatorio) {
        try {
            $mensaje = $recordatorio['mensaje_whatsapp'] ?: 
                      "🔔 Recordatorio: " . $recordatorio['titulo'] . "\n\n" . 
                      ($recordatorio['descripcion'] ?: 'Sin descripción adicional');
            
            $resultado = $this->whatsappService->enviarMensaje(
                $destinatario['telefono_whatsapp'],
                $mensaje,
                $recordatorio['id_recordatorio']
            );
            
            if (!$resultado['success']) {
                throw new Exception($resultado['error']);
            }
            
        } catch (Exception $e) {
            throw new Exception("Error al enviar WhatsApp: " . $e->getMessage());
        }
    }
    
    private function enviarNotificacionInterna($destinatario, $recordatorio) {
        try {
            $resultado = $this->notificacionesService->enviarNotificacionRecordatorio(
                $recordatorio,
                $destinatario
            );
            
            if (!$resultado['success']) {
                throw new Exception($resultado['message']);
            }
            
        } catch (Exception $e) {
            throw new Exception("Error al enviar notificación: " . $e->getMessage());
        }
    }
    
    private function registrarLog($recordatorio_id, $tipo_envio, $estado, $mensaje, $detalles = null) {
        $sql = "INSERT INTO recordatorios_logs 
                (recordatorio_id, tipo_envio, estado, mensaje, detalles_tecnico) 
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $detalles_json = $detalles ? json_encode($detalles) : null;
        $stmt->bind_param("issss", $recordatorio_id, $tipo_envio, $estado, $mensaje, $detalles_json);
        $stmt->execute();
    }
}
?>
