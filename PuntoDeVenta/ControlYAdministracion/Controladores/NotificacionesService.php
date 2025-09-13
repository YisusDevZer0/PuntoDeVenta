<?php
/**
 * Servicio de Notificaciones Internas - Doctor Pez
 * Maneja el envío de notificaciones internas del sistema
 */

class NotificacionesService {
    private $conn;
    
    public function __construct($connection) {
        $this->conn = $connection;
    }
    
    /**
     * Enviar notificación interna
     */
    public function enviarNotificacion($usuario_id, $titulo, $mensaje, $tipo = 'sistema', $sucursal_id = null, $recordatorio_id = null) {
        try {
            // Insertar notificación en la tabla principal
            $sql = "INSERT INTO Notificaciones (Tipo, Mensaje, SucursalID, Fecha, Leido) VALUES (?, ?, ?, NOW(), 0)";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("ssi", $tipo, $mensaje, $sucursal_id);
            
            if (!$stmt->execute()) {
                throw new Exception("Error al crear notificación: " . $stmt->error);
            }
            
            $notificacion_id = $this->conn->insert_id;
            
            // Enviar notificación push si está disponible
            $this->enviarNotificacionPush($usuario_id, $titulo, $mensaje, $tipo);
            
            // Registrar log si es un recordatorio
            if ($recordatorio_id) {
                $this->registrarLog($recordatorio_id, $usuario_id, $titulo, $mensaje, 'exitoso');
            }
            
            return [
                'success' => true,
                'notificacion_id' => $notificacion_id,
                'message' => 'Notificación enviada exitosamente'
            ];
            
        } catch (Exception $e) {
            // Registrar error si es un recordatorio
            if ($recordatorio_id) {
                $this->registrarLog($recordatorio_id, $usuario_id, $titulo, $mensaje, 'error', $e->getMessage());
            }
            
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Enviar notificación a múltiples usuarios
     */
    public function enviarNotificacionMultiple($usuario_ids, $titulo, $mensaje, $tipo = 'sistema', $sucursal_id = null, $recordatorio_id = null) {
        $resultados = [];
        $exitosos = 0;
        $errores = 0;
        
        foreach ($usuario_ids as $usuario_id) {
            $resultado = $this->enviarNotificacion($usuario_id, $titulo, $mensaje, $tipo, $sucursal_id, $recordatorio_id);
            
            if ($resultado['success']) {
                $exitosos++;
            } else {
                $errores++;
            }
            
            $resultados[] = [
                'usuario_id' => $usuario_id,
                'resultado' => $resultado
            ];
        }
        
        return [
            'success' => $errores === 0,
            'exitosos' => $exitosos,
            'errores' => $errores,
            'resultados' => $resultados
        ];
    }
    
    /**
     * Enviar notificación push usando OneSignal
     */
    private function enviarNotificacionPush($usuario_id, $titulo, $mensaje, $tipo) {
        try {
            // Verificar si OneSignal está configurado
            if (!file_exists('onesignal_config.php')) {
                return; // OneSignal no configurado
            }
            
            $config = require_once 'onesignal_config.php';
            
            // Preparar datos de la notificación
            $fields = [
                'app_id' => $config['app_id'],
                'contents' => ['es' => $mensaje],
                'headings' => ['es' => $titulo],
                'include_external_user_ids' => [$usuario_id],
                'data' => [
                    'tipo' => $tipo,
                    'timestamp' => time(),
                    'usuario_id' => $usuario_id
                ]
            ];
            
            // Enviar usando cURL
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json; charset=utf-8',
                'Authorization: Basic ' . $config['rest_api_key']
            ]);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            
            $response = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            if ($http_code !== 200) {
                error_log("Error OneSignal: HTTP $http_code - $response");
            }
            
        } catch (Exception $e) {
            error_log("Error en notificación push: " . $e->getMessage());
        }
    }
    
    /**
     * Enviar notificación usando plantilla
     */
    public function enviarNotificacionConPlantilla($usuario_id, $plantilla_id, $variables = [], $sucursal_id = null, $recordatorio_id = null) {
        try {
            // Obtener plantilla
            $plantilla = $this->obtenerPlantilla($plantilla_id);
            
            if (!$plantilla) {
                throw new Exception("Plantilla no encontrada");
            }
            
            // Procesar plantilla con variables
            $titulo = $this->procesarPlantilla($plantilla['titulo'] ?? 'Notificación', $variables);
            $mensaje = $this->procesarPlantilla($plantilla['plantilla_notificacion'], $variables);
            
            // Enviar notificación
            return $this->enviarNotificacion($usuario_id, $titulo, $mensaje, $plantilla['tipo'], $sucursal_id, $recordatorio_id);
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Obtener plantilla por ID
     */
    private function obtenerPlantilla($plantilla_id) {
        $sql = "SELECT * FROM recordatorios_plantillas WHERE id_plantilla = ? AND activo = 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $plantilla_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_assoc();
    }
    
    /**
     * Procesar plantilla con variables
     */
    private function procesarPlantilla($plantilla, $variables) {
        $mensaje = $plantilla;
        
        foreach ($variables as $key => $value) {
            $mensaje = str_replace("{{$key}}", $value, $mensaje);
        }
        
        return $mensaje;
    }
    
    /**
     * Obtener notificaciones de un usuario
     */
    public function obtenerNotificacionesUsuario($usuario_id, $sucursal_id = null, $limit = 20) {
        $sql = "SELECT ID_Notificacion, Tipo, Mensaje, Fecha, Leido,
                TIMESTAMPDIFF(MINUTE, Fecha, NOW()) as MinutosTranscurridos
                FROM Notificaciones 
                WHERE (SucursalID = ? OR SucursalID = 0)
                ORDER BY Fecha DESC 
                LIMIT ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $sucursal_id, $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $notificaciones = [];
        while ($row = $result->fetch_assoc()) {
            // Formatear tiempo transcurrido
            $tiempo = "";
            $minutos = $row['MinutosTranscurridos'];
            
            if ($minutos < 60) {
                $tiempo = $minutos . " minutos";
            } else if ($minutos < 1440) {
                $tiempo = floor($minutos / 60) . " horas";
            } else {
                $tiempo = floor($minutos / 1440) . " días";
            }
            
            $row['TiempoTranscurridos'] = $tiempo;
            $notificaciones[] = $row;
        }
        
        return $notificaciones;
    }
    
    /**
     * Marcar notificación como leída
     */
    public function marcarComoLeida($notificacion_id) {
        $sql = "UPDATE Notificaciones SET Leido = 1 WHERE ID_Notificacion = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $notificacion_id);
        
        return $stmt->execute();
    }
    
    /**
     * Obtener contador de notificaciones no leídas
     */
    public function obtenerContadorNoLeidas($sucursal_id = null) {
        $sql = "SELECT COUNT(*) as total FROM Notificaciones 
                WHERE Leido = 0 AND (SucursalID = ? OR SucursalID = 0)";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $sucursal_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_assoc()['total'];
    }
    
    /**
     * Limpiar notificaciones antiguas
     */
    public function limpiarNotificacionesAntiguas($dias = 30) {
        $sql = "DELETE FROM Notificaciones 
                WHERE Fecha < DATE_SUB(NOW(), INTERVAL ? DAY)";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $dias);
        $stmt->execute();
        
        return $stmt->affected_rows;
    }
    
    /**
     * Registrar log de notificación
     */
    private function registrarLog($recordatorio_id, $usuario_id, $titulo, $mensaje, $estado, $error = null) {
        $sql = "INSERT INTO recordatorios_logs 
                (recordatorio_id, tipo_envio, estado, mensaje, detalles_tecnico) 
                VALUES (?, 'notificacion', ?, ?, ?)";
        
        $stmt = $this->conn->prepare($sql);
        $detalles = json_encode([
            'usuario_id' => $usuario_id,
            'titulo' => $titulo,
            'mensaje' => $mensaje,
            'error' => $error,
            'fecha_envio' => date('Y-m-d H:i:s')
        ]);
        
        $stmt->bind_param("isss", $recordatorio_id, $estado, $mensaje, $detalles);
        $stmt->execute();
    }
    
    /**
     * Obtener estadísticas de notificaciones
     */
    public function obtenerEstadisticas($fecha_inicio = null, $fecha_fin = null) {
        $fecha_inicio = $fecha_inicio ?? date('Y-m-01');
        $fecha_fin = $fecha_fin ?? date('Y-m-t');
        
        $sql = "SELECT 
                    COUNT(*) as total_notificaciones,
                    SUM(CASE WHEN Leido = 1 THEN 1 ELSE 0 END) as leidas,
                    SUM(CASE WHEN Leido = 0 THEN 1 ELSE 0 END) as no_leidas,
                    Tipo,
                    DATE(Fecha) as fecha
                FROM Notificaciones 
                WHERE DATE(Fecha) BETWEEN ? AND ?
                GROUP BY Tipo, DATE(Fecha)
                ORDER BY fecha DESC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ss", $fecha_inicio, $fecha_fin);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $estadisticas = [];
        while ($row = $result->fetch_assoc()) {
            $estadisticas[] = $row;
        }
        
        return $estadisticas;
    }
    
    /**
     * Enviar notificación de recordatorio
     */
    public function enviarNotificacionRecordatorio($recordatorio, $destinatario) {
        $titulo = "🔔 Recordatorio: " . $recordatorio['titulo'];
        
        $mensaje = $recordatorio['mensaje_notificacion'] ?: 
                   "Recordatorio: " . $recordatorio['titulo'] . "\n\n" . 
                   ($recordatorio['descripcion'] ?: 'Sin descripción adicional');
        
        return $this->enviarNotificacion(
            $destinatario['usuario_id'],
            $titulo,
            $mensaje,
            'recordatorio',
            $destinatario['sucursal_id'] ?? null,
            $recordatorio['id_recordatorio']
        );
    }
}
?>
