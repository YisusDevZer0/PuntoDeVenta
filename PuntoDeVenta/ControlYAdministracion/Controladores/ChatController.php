<?php
/**
 * Controlador principal para el sistema de chat
 * Maneja todas las operaciones relacionadas con mensajes y conversaciones
 */

class ChatController {
    private $conn;
    private $usuario_id;
    private $sucursal_id;
    
    public function __construct($connection, $usuario_id, $sucursal_id = null) {
        $this->conn = $connection;
        $this->usuario_id = $usuario_id;
        $this->sucursal_id = $sucursal_id;
    }
    
    /**
     * Obtener todas las conversaciones del usuario
     */
    public function obtenerConversaciones() {
        $sql = "SELECT 
                    c.id_conversacion,
                    c.nombre_conversacion,
                    c.tipo_conversacion,
                    c.sucursal_id,
                    s.Nombre_Sucursal,
                    c.ultimo_mensaje,
                    c.ultimo_mensaje_fecha,
                    COUNT(p.id_participante) as total_participantes,
                    p.ultima_lectura,
                    (SELECT COUNT(*) FROM chat_mensajes m 
                     WHERE m.conversacion_id = c.id_conversacion 
                     AND m.fecha_envio > p.ultima_lectura 
                     AND m.usuario_id != ?) as mensajes_no_leidos
                FROM chat_conversaciones c
                LEFT JOIN Sucursales s ON c.sucursal_id = s.ID_Sucursal
                INNER JOIN chat_participantes p ON c.id_conversacion = p.conversacion_id
                WHERE p.usuario_id = ? AND p.activo = 1 AND c.activo = 1
                ORDER BY c.ultimo_mensaje_fecha DESC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $this->usuario_id, $this->usuario_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    /**
     * Obtener mensajes de una conversación específica
     */
    public function obtenerMensajes($conversacion_id, $limite = 50, $offset = 0) {
        // Verificar que el usuario es participante de la conversación
        if (!$this->esParticipante($conversacion_id)) {
            return ['error' => 'No tienes acceso a esta conversación'];
        }
        
        $sql = "SELECT 
                    m.id_mensaje,
                    m.conversacion_id,
                    m.usuario_id,
                    u.Nombre_Apellidos as usuario_nombre,
                    u.file_name as usuario_avatar,
                    m.mensaje,
                    m.tipo_mensaje,
                    m.archivo_url,
                    m.archivo_nombre,
                    m.archivo_tipo,
                    m.archivo_tamaño,
                    m.fecha_envio,
                    m.fecha_edicion,
                    m.editado,
                    m.eliminado,
                    m.mensaje_respuesta_id,
                    mr.mensaje as mensaje_respuesta,
                    mr.usuario_id as mensaje_respuesta_usuario_id,
                    ur.Nombre_Apellidos as mensaje_respuesta_usuario_nombre
                FROM chat_mensajes m
                LEFT JOIN Usuarios_PV u ON m.usuario_id = u.Id_PvUser
                LEFT JOIN chat_mensajes mr ON m.mensaje_respuesta_id = mr.id_mensaje
                LEFT JOIN Usuarios_PV ur ON mr.usuario_id = ur.Id_PvUser
                WHERE m.conversacion_id = ? AND m.eliminado = 0
                ORDER BY m.fecha_envio DESC
                LIMIT ? OFFSET ?";
        
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            error_log("Error al preparar consulta: " . $this->conn->error);
            return ['error' => 'Error en la consulta de base de datos'];
        }
        
        $stmt->bind_param("iii", $conversacion_id, $limite, $offset);
        
        if (!$stmt->execute()) {
            error_log("Error al ejecutar consulta: " . $stmt->error);
            return ['error' => 'Error al ejecutar la consulta'];
        }
        
        $result = $stmt->get_result();
        $mensajes = $result->fetch_all(MYSQLI_ASSOC);
        
        // Asegurar que siempre devolvamos un array
        if (!is_array($mensajes)) {
            $mensajes = [];
        }
        
        // Marcar mensajes como leídos
        $this->marcarMensajesComoLeidos($conversacion_id);
        
        return array_reverse($mensajes); // Ordenar cronológicamente
    }
    
    /**
     * Enviar un nuevo mensaje
     */
    public function enviarMensaje($conversacion_id, $mensaje, $tipo_mensaje = 'texto', $archivo_data = null, $mensaje_respuesta_id = null) {
        // Verificar que el usuario es participante de la conversación
        if (!$this->esParticipante($conversacion_id)) {
            return ['error' => 'No tienes acceso a esta conversación'];
        }
        
        $archivo_url = null;
        $archivo_nombre = null;
        $archivo_tipo = null;
        $archivo_tamaño = null;
        
        // Procesar archivo si se proporciona
        if ($archivo_data && $tipo_mensaje !== 'texto') {
            $archivo_result = $this->procesarArchivo($archivo_data);
            if (isset($archivo_result['error'])) {
                return $archivo_result;
            }
            $archivo_url = $archivo_result['url'];
            $archivo_nombre = $archivo_result['nombre'];
            $archivo_tipo = $archivo_result['tipo'];
            $archivo_tamaño = $archivo_result['tamaño'];
        }
        
        $sql = "INSERT INTO chat_mensajes 
                (conversacion_id, usuario_id, mensaje, tipo_mensaje, archivo_url, archivo_nombre, archivo_tipo, archivo_tamaño, mensaje_respuesta_id)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("iisssssii", 
            $conversacion_id, 
            $this->usuario_id, 
            $mensaje, 
            $tipo_mensaje, 
            $archivo_url, 
            $archivo_nombre, 
            $archivo_tipo, 
            $archivo_tamaño, 
            $mensaje_respuesta_id
        );
        
        if ($stmt->execute()) {
            $mensaje_id = $this->conn->insert_id;
            
            // Enviar notificaciones push a otros participantes
            $this->enviarNotificacionesPush($conversacion_id, $mensaje, $mensaje_id);
            
            return [
                'success' => true,
                'mensaje_id' => $mensaje_id,
                'fecha_envio' => date('Y-m-d H:i:s')
            ];
        } else {
            return ['error' => 'Error al enviar el mensaje: ' . $stmt->error];
        }
    }
    
    /**
     * Crear una nueva conversación
     */
    public function crearConversacion($nombre, $tipo_conversacion = 'individual', $participantes = []) {
        $sql = "INSERT INTO chat_conversaciones (nombre_conversacion, tipo_conversacion, sucursal_id, creado_por)
                VALUES (?, ?, ?, ?)";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ssii", $nombre, $tipo_conversacion, $this->sucursal_id, $this->usuario_id);
        
        if ($stmt->execute()) {
            $conversacion_id = $this->conn->insert_id;
            
            // Agregar creador como participante
            $this->agregarParticipante($conversacion_id, $this->usuario_id);
            
            // Agregar otros participantes
            foreach ($participantes as $participante_id) {
                $this->agregarParticipante($conversacion_id, $participante_id);
            }
            
            return [
                'success' => true,
                'conversacion_id' => $conversacion_id
            ];
        } else {
            return ['error' => 'Error al crear la conversación: ' . $stmt->error];
        }
    }
    
    /**
     * Agregar participante a una conversación
     */
    public function agregarParticipante($conversacion_id, $usuario_id) {
        // Verificar que el usuario actual puede agregar participantes
        if (!$this->puedeGestionarConversacion($conversacion_id)) {
            return ['error' => 'No tienes permisos para gestionar esta conversación'];
        }
        
        $sql = "INSERT INTO chat_participantes (conversacion_id, usuario_id)
                VALUES (?, ?)
                ON DUPLICATE KEY UPDATE activo = 1";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $conversacion_id, $usuario_id);
        
        if ($stmt->execute()) {
            return ['success' => true];
        } else {
            return ['error' => 'Error al agregar participante: ' . $stmt->error];
        }
    }
    
    /**
     * Editar un mensaje
     */
    public function editarMensaje($mensaje_id, $nuevo_mensaje) {
        // Verificar que el mensaje pertenece al usuario
        $sql = "SELECT usuario_id FROM chat_mensajes WHERE id_mensaje = ? AND usuario_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $mensaje_id, $this->usuario_id);
        $stmt->execute();
        
        if ($stmt->get_result()->num_rows === 0) {
            return ['error' => 'No tienes permisos para editar este mensaje'];
        }
        
        $sql = "UPDATE chat_mensajes 
                SET mensaje = ?, fecha_edicion = NOW(), editado = 1
                WHERE id_mensaje = ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("si", $nuevo_mensaje, $mensaje_id);
        
        if ($stmt->execute()) {
            return ['success' => true];
        } else {
            return ['error' => 'Error al editar el mensaje: ' . $stmt->error];
        }
    }
    
    /**
     * Eliminar un mensaje (soft delete)
     */
    public function eliminarMensaje($mensaje_id) {
        // Verificar que el mensaje pertenece al usuario
        $sql = "SELECT usuario_id FROM chat_mensajes WHERE id_mensaje = ? AND usuario_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $mensaje_id, $this->usuario_id);
        $stmt->execute();
        
        if ($stmt->get_result()->num_rows === 0) {
            return ['error' => 'No tienes permisos para eliminar este mensaje'];
        }
        
        $sql = "UPDATE chat_mensajes SET eliminado = 1 WHERE id_mensaje = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $mensaje_id);
        
        if ($stmt->execute()) {
            return ['success' => true];
        } else {
            return ['error' => 'Error al eliminar el mensaje: ' . $stmt->error];
        }
    }
    
    /**
     * Obtener usuarios disponibles para agregar a conversaciones
     */
    public function obtenerUsuariosDisponibles($conversacion_id = null) {
        $sql = "SELECT 
                    u.Id_PvUser,
                    u.Nombre_Apellidos,
                    u.file_name,
                    s.Nombre_Sucursal,
                    t.TipoUsuario
                FROM Usuarios_PV u
                LEFT JOIN Sucursales s ON u.Fk_Sucursal = s.ID_Sucursal
                LEFT JOIN Tipos_Usuarios t ON u.Fk_Usuario = t.ID_User
                WHERE u.Estatus = 'Activo'";
        
        // Excluir participantes actuales si se especifica conversación
        if ($conversacion_id) {
            $sql .= " AND u.Id_PvUser NOT IN (
                        SELECT usuario_id FROM chat_participantes 
                        WHERE conversacion_id = ? AND activo = 1
                     )";
        }
        
        $sql .= " ORDER BY u.Nombre_Apellidos";
        
        $stmt = $this->conn->prepare($sql);
        if ($conversacion_id) {
            $stmt->bind_param("i", $conversacion_id);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    /**
     * Verificar si el usuario es participante de una conversación
     */
    private function esParticipante($conversacion_id) {
        $sql = "SELECT 1 FROM chat_participantes 
                WHERE conversacion_id = ? AND usuario_id = ? AND activo = 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $conversacion_id, $this->usuario_id);
        $stmt->execute();
        
        return $stmt->get_result()->num_rows > 0;
    }
    
    /**
     * Verificar si el usuario puede gestionar una conversación
     */
    private function puedeGestionarConversacion($conversacion_id) {
        $sql = "SELECT creado_por FROM chat_conversaciones WHERE id_conversacion = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $conversacion_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        
        return $result && $result['creado_por'] == $this->usuario_id;
    }
    
    /**
     * Marcar mensajes como leídos
     */
    private function marcarMensajesComoLeidos($conversacion_id) {
        $sql = "UPDATE chat_participantes 
                SET ultima_lectura = NOW() 
                WHERE conversacion_id = ? AND usuario_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $conversacion_id, $this->usuario_id);
        $stmt->execute();
    }
    
    /**
     * Procesar archivo subido
     */
    private function procesarArchivo($archivo_data) {
        $upload_dir = '../uploads/chat/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $file_extension = pathinfo($archivo_data['name'], PATHINFO_EXTENSION);
        $file_name = uniqid() . '_' . time() . '.' . $file_extension;
        $file_path = $upload_dir . $file_name;
        
        if (move_uploaded_file($archivo_data['tmp_name'], $file_path)) {
            return [
                'url' => 'uploads/chat/' . $file_name,
                'nombre' => $archivo_data['name'],
                'tipo' => $archivo_data['type'],
                'tamaño' => $archivo_data['size']
            ];
        } else {
            return ['error' => 'Error al subir el archivo'];
        }
    }
    
    /**
     * Enviar notificaciones push
     */
    private function enviarNotificacionesPush($conversacion_id, $mensaje, $mensaje_id) {
        // Obtener participantes de la conversación (excepto el remitente)
        $sql = "SELECT p.usuario_id, u.Nombre_Apellidos, c.nombre_conversacion
                FROM chat_participantes p
                LEFT JOIN Usuarios_PV u ON p.usuario_id = u.Id_PvUser
                LEFT JOIN chat_conversaciones c ON p.conversacion_id = c.id_conversacion
                WHERE p.conversacion_id = ? AND p.usuario_id != ? AND p.activo = 1";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $conversacion_id, $this->usuario_id);
        $stmt->execute();
        $participantes = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        
        // Aquí se integraría con el sistema de notificaciones push existente
        // Por ahora solo registramos en log
        error_log("Notificación de chat enviada a " . count($participantes) . " usuarios");
    }
}
?>
