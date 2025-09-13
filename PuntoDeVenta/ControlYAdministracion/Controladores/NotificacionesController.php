<?php
/**
 * Controlador de notificaciones del sistema
 * Maneja notificaciones push, email y del sistema
 */

class NotificacionesController {
    private $conn;
    
    public function __construct($connection) {
        $this->conn = $connection;
    }
    
    /**
     * Enviar notificación de chat
     */
    public function enviarNotificacionChat($conversacion_id, $mensaje, $usuario_remitente, $tipo_notificacion = 'mensaje') {
        // Obtener participantes de la conversación (excepto el remitente)
        $sql = "SELECT 
                    p.usuario_id,
                    u.Nombre_Apellidos,
                    u.Correo_Electronico,
                    c.nombre_conversacion,
                    c.tipo_conversacion,
                    s.Nombre_Sucursal,
                    config.notificaciones_push,
                    config.notificaciones_email
                FROM chat_participantes p
                LEFT JOIN Usuarios_PV u ON p.usuario_id = u.Id_PvUser
                LEFT JOIN chat_conversaciones c ON p.conversacion_id = c.id_conversacion
                LEFT JOIN Sucursales s ON u.Fk_Sucursal = s.ID_Sucursal
                LEFT JOIN chat_configuraciones config ON p.usuario_id = config.usuario_id
                WHERE p.conversacion_id = ? AND p.usuario_id != ? AND p.activo = 1 AND p.notificaciones = 1";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $conversacion_id, $usuario_remitente);
        $stmt->execute();
        $participantes = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        
        foreach ($participantes as $participante) {
            // Crear notificación en la tabla del sistema
            $this->crearNotificacionSistema($participante['usuario_id'], $conversacion_id, $mensaje, $tipo_notificacion);
            
            // Enviar notificación push si está habilitada
            if ($participante['notificaciones_push']) {
                $this->enviarNotificacionPush($participante, $mensaje, $conversacion_id);
            }
            
            // Enviar notificación por email si está habilitada
            if ($participante['notificaciones_email'] && !empty($participante['Correo_Electronico'])) {
                $this->enviarNotificacionEmail($participante, $mensaje, $conversacion_id);
            }
        }
        
        return true;
    }
    
    /**
     * Crear notificación en el sistema
     */
    private function crearNotificacionSistema($usuario_id, $conversacion_id, $mensaje, $tipo) {
        $sql = "INSERT INTO Notificaciones (Tipo, Mensaje, Fecha, SucursalID, Leido, UsuarioID, ConversacionID) 
                VALUES (?, ?, NOW(), (SELECT Fk_Sucursal FROM Usuarios_PV WHERE Id_PvUser = ?), 0, ?, ?)";
        
        $tipo_notificacion = 'chat_' . $tipo;
        $mensaje_notificacion = "Nuevo mensaje en el chat: " . substr($mensaje, 0, 100) . (strlen($mensaje) > 100 ? '...' : '');
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ssiii", $tipo_notificacion, $mensaje_notificacion, $usuario_id, $usuario_id, $conversacion_id);
        $stmt->execute();
    }
    
    /**
     * Enviar notificación push
     */
    private function enviarNotificacionPush($participante, $mensaje, $conversacion_id) {
        // Aquí se integraría con OneSignal o similar
        // Por ahora solo registramos en log
        error_log("Notificación push enviada a usuario {$participante['usuario_id']}: {$mensaje}");
        
        // Si tienes OneSignal configurado, descomenta y configura:
        /*
        $fields = array(
            'app_id' => 'TU_APP_ID',
            'include_player_ids' => [$participante['player_id']], // Necesitarías agregar este campo
            'headings' => array("en" => "Nuevo mensaje en {$participante['nombre_conversacion']}"),
            'contents' => array("en" => substr($mensaje, 0, 100)),
            'data' => array(
                'conversacion_id' => $conversacion_id,
                'tipo' => 'chat'
            )
        );
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8',
                                                   'Authorization: Basic TU_API_KEY'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        
        $response = curl_exec($ch);
        curl_close($ch);
        */
    }
    
    /**
     * Enviar notificación por email
     */
    private function enviarNotificacionEmail($participante, $mensaje, $conversacion_id) {
        $asunto = "Nuevo mensaje en {$participante['nombre_conversacion']}";
        $cuerpo = "
        <h3>Nuevo mensaje en el chat</h3>
        <p><strong>Conversación:</strong> {$participante['nombre_conversacion']}</p>
        <p><strong>Sucursal:</strong> {$participante['Nombre_Sucursal']}</p>
        <p><strong>Mensaje:</strong> " . htmlspecialchars($mensaje) . "</p>
        <p><a href='" . $this->getBaseUrl() . "/ControlYAdministracion/Mensajes.php'>Ver conversación</a></p>
        ";
        
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= "From: Sistema Doctor Pez <noreply@doctorpez.mx>" . "\r\n";
        
        mail($participante['Correo_Electronico'], $asunto, $cuerpo, $headers);
    }
    
    /**
     * Obtener URL base del sistema
     */
    private function getBaseUrl() {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'];
        $path = dirname($_SERVER['REQUEST_URI']);
        return $protocol . '://' . $host . $path;
    }
    
    /**
     * Marcar notificación como leída
     */
    public function marcarComoLeida($notificacion_id, $usuario_id) {
        $sql = "UPDATE Notificaciones SET Leido = 1 WHERE ID_Notificacion = ? AND UsuarioID = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $notificacion_id, $usuario_id);
        return $stmt->execute();
    }
    
    /**
     * Obtener notificaciones no leídas del usuario
     */
    public function obtenerNotificacionesNoLeidas($usuario_id) {
        $sql = "SELECT 
                    n.ID_Notificacion,
                    n.Tipo,
                    n.Mensaje,
                    n.Fecha,
                    n.ConversacionID,
                    c.nombre_conversacion
                FROM Notificaciones n
                LEFT JOIN chat_conversaciones c ON n.ConversacionID = c.id_conversacion
                WHERE n.UsuarioID = ? AND n.Leido = 0
                ORDER BY n.Fecha DESC
                LIMIT 10";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $usuario_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}
?>
