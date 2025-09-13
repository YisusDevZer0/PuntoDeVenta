<?php
/**
 * Servicio de WhatsApp - Doctor Pez
 * Maneja el envío de mensajes por WhatsApp
 */

class WhatsAppService {
    private $api_url;
    private $api_token;
    private $numero_telefono;
    private $conn;
    
    public function __construct($connection) {
        $this->conn = $connection;
        $this->cargarConfiguracion();
    }
    
    /**
     * Cargar configuración de WhatsApp desde la base de datos
     */
    private function cargarConfiguracion() {
        $sql = "SELECT api_url, api_token, numero_telefono FROM recordatorios_config_whatsapp WHERE activo = 1 LIMIT 1";
        $result = $this->conn->query($sql);
        
        if ($row = $result->fetch_assoc()) {
            $this->api_url = $row['api_url'];
            $this->api_token = $row['api_token'];
            $this->numero_telefono = $row['numero_telefono'];
        } else {
            // Configuración por defecto
            $this->api_url = 'https://api.whatsapp.com/send';
            $this->api_token = '';
            $this->numero_telefono = '';
        }
    }
    
    /**
     * Enviar mensaje por WhatsApp
     */
    public function enviarMensaje($numero_destino, $mensaje, $recordatorio_id = null) {
        try {
            // Validar configuración
            if (empty($this->api_token) || empty($this->numero_telefono)) {
                throw new Exception("Configuración de WhatsApp no válida");
            }
            
            // Limpiar número de teléfono
            $numero_destino = $this->limpiarNumeroTelefono($numero_destino);
            
            if (!$this->validarNumeroTelefono($numero_destino)) {
                throw new Exception("Número de teléfono no válido: $numero_destino");
            }
            
            // Preparar datos del mensaje
            $datos = [
                'to' => $numero_destino,
                'message' => $mensaje,
                'from' => $this->numero_telefono
            ];
            
            // Enviar mensaje usando la API
            $resultado = $this->enviarViaAPI($datos);
            
            // Registrar log
            if ($recordatorio_id) {
                $this->registrarLog($recordatorio_id, $numero_destino, $mensaje, $resultado);
            }
            
            return $resultado;
            
        } catch (Exception $e) {
            // Registrar error
            if ($recordatorio_id) {
                $this->registrarLog($recordatorio_id, $numero_destino, $mensaje, [
                    'success' => false,
                    'error' => $e->getMessage()
                ]);
            }
            
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Enviar mensaje usando la API de WhatsApp
     */
    private function enviarViaAPI($datos) {
        // Configurar cURL
        $ch = curl_init();
        
        curl_setopt($ch, CURLOPT_URL, $this->api_url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($datos));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->api_token
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        
        curl_close($ch);
        
        if ($error) {
            throw new Exception("Error de cURL: $error");
        }
        
        if ($http_code !== 200) {
            throw new Exception("Error HTTP: $http_code - $response");
        }
        
        $resultado = json_decode($response, true);
        
        if (!$resultado) {
            throw new Exception("Respuesta no válida de la API");
        }
        
        return [
            'success' => true,
            'response' => $resultado,
            'http_code' => $http_code
        ];
    }
    
    /**
     * Enviar mensaje usando WhatsApp Web (método alternativo)
     */
    public function enviarMensajeWeb($numero_destino, $mensaje) {
        try {
            $numero_destino = $this->limpiarNumeroTelefono($numero_destino);
            $mensaje_codificado = urlencode($mensaje);
            
            $url = "https://wa.me/$numero_destino?text=$mensaje_codificado";
            
            return [
                'success' => true,
                'url' => $url,
                'metodo' => 'web'
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Enviar mensaje usando plantilla
     */
    public function enviarMensajeConPlantilla($numero_destino, $plantilla_id, $variables = []) {
        try {
            // Obtener plantilla
            $plantilla = $this->obtenerPlantilla($plantilla_id);
            
            if (!$plantilla) {
                throw new Exception("Plantilla no encontrada");
            }
            
            // Procesar plantilla con variables
            $mensaje = $this->procesarPlantilla($plantilla['plantilla_whatsapp'], $variables);
            
            // Enviar mensaje
            return $this->enviarMensaje($numero_destino, $mensaje);
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
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
     * Limpiar número de teléfono
     */
    private function limpiarNumeroTelefono($numero) {
        // Remover caracteres no numéricos excepto +
        $numero = preg_replace('/[^0-9+]/', '', $numero);
        
        // Si no tiene código de país, agregar +52 (México)
        if (!str_starts_with($numero, '+')) {
            if (str_starts_with($numero, '52')) {
                $numero = '+' . $numero;
            } else {
                $numero = '+52' . $numero;
            }
        }
        
        return $numero;
    }
    
    /**
     * Validar número de teléfono
     */
    private function validarNumeroTelefono($numero) {
        // Validar formato básico de número internacional
        return preg_match('/^\+[1-9]\d{1,14}$/', $numero);
    }
    
    /**
     * Registrar log de envío
     */
    private function registrarLog($recordatorio_id, $numero_destino, $mensaje, $resultado) {
        $sql = "INSERT INTO recordatorios_logs 
                (recordatorio_id, tipo_envio, estado, mensaje, detalles_tecnico) 
                VALUES (?, 'whatsapp', ?, ?, ?)";
        
        $stmt = $this->conn->prepare($sql);
        $estado = $resultado['success'] ? 'exitoso' : 'error';
        $detalles = json_encode([
            'numero_destino' => $numero_destino,
            'mensaje' => $mensaje,
            'resultado' => $resultado,
            'fecha_envio' => date('Y-m-d H:i:s')
        ]);
        
        $stmt->bind_param("isss", $recordatorio_id, $estado, $mensaje, $detalles);
        $stmt->execute();
    }
    
    /**
     * Obtener estadísticas de envío
     */
    public function obtenerEstadisticas($fecha_inicio = null, $fecha_fin = null) {
        $fecha_inicio = $fecha_inicio ?? date('Y-m-01');
        $fecha_fin = $fecha_fin ?? date('Y-m-t');
        
        $sql = "SELECT 
                    COUNT(*) as total_envios,
                    SUM(CASE WHEN estado = 'exitoso' THEN 1 ELSE 0 END) as exitosos,
                    SUM(CASE WHEN estado = 'error' THEN 1 ELSE 0 END) as errores,
                    DATE(fecha_log) as fecha
                FROM recordatorios_logs 
                WHERE tipo_envio = 'whatsapp' 
                AND DATE(fecha_log) BETWEEN ? AND ?
                GROUP BY DATE(fecha_log)
                ORDER BY fecha";
        
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
     * Verificar estado de la API
     */
    public function verificarEstadoAPI() {
        try {
            $ch = curl_init();
            
            curl_setopt($ch, CURLOPT_URL, $this->api_url . '/status');
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer ' . $this->api_token
            ]);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            
            $response = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            
            curl_close($ch);
            
            if ($error) {
                return [
                    'success' => false,
                    'error' => "Error de conexión: $error"
                ];
            }
            
            return [
                'success' => $http_code === 200,
                'http_code' => $http_code,
                'response' => json_decode($response, true)
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Actualizar configuración
     */
    public function actualizarConfiguracion($api_url, $api_token, $numero_telefono, $usuario_id) {
        try {
            // Verificar si existe configuración
            $sql = "SELECT id_config FROM recordatorios_config_whatsapp WHERE activo = 1 LIMIT 1";
            $result = $this->conn->query($sql);
            
            if ($result->num_rows > 0) {
                // Actualizar configuración existente
                $sql = "UPDATE recordatorios_config_whatsapp 
                        SET api_url = ?, api_token = ?, numero_telefono = ?, 
                            fecha_actualizacion = NOW(), usuario_configurador = ?
                        WHERE activo = 1";
            } else {
                // Crear nueva configuración
                $sql = "INSERT INTO recordatorios_config_whatsapp 
                        (api_url, api_token, numero_telefono, usuario_configurador) 
                        VALUES (?, ?, ?, ?)";
            }
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("sssi", $api_url, $api_token, $numero_telefono, $usuario_id);
            
            if ($stmt->execute()) {
                // Actualizar propiedades de la clase
                $this->api_url = $api_url;
                $this->api_token = $api_token;
                $this->numero_telefono = $numero_telefono;
                
                return [
                    'success' => true,
                    'message' => 'Configuración actualizada exitosamente'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Error al actualizar configuración: ' . $stmt->error
                ];
            }
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ];
        }
    }
}
?>
