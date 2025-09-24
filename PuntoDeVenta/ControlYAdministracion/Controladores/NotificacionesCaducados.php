<?php
/**
 * Controlador para notificaciones de caducados
 * Maneja el envío de alertas automáticas
 */

class NotificacionesCaducados {
    private $conn;
    
    public function __construct($connection) {
        $this->conn = $connection;
    }
    
    /**
     * Generar notificaciones automáticas
     */
    public function generarNotificaciones() {
        try {
            // Obtener productos próximos a caducar
            $productos = $this->obtenerProductosParaNotificar();
            
            $notificaciones_generadas = 0;
            
            foreach ($productos as $producto) {
                $tipo_alerta = $this->determinarTipoAlerta($producto['dias_restantes']);
                
                if ($tipo_alerta) {
                    $this->crearNotificacion($producto, $tipo_alerta);
                    $notificaciones_generadas++;
                }
            }
            
            return [
                'success' => true,
                'notificaciones_generadas' => $notificaciones_generadas,
                'message' => "Se generaron $notificaciones_generadas notificaciones"
            ];
            
        } catch (Exception $e) {
            throw new Exception("Error al generar notificaciones: " . $e->getMessage());
        }
    }
    
    /**
     * Obtener productos que necesitan notificación
     */
    private function obtenerProductosParaNotificar() {
        $sql = "SELECT 
                    plc.id_lote,
                    plc.cod_barra,
                    plc.nombre_producto,
                    plc.lote,
                    plc.fecha_caducidad,
                    plc.cantidad_actual,
                    plc.sucursal_id,
                    s.Nombre_Sucursal,
                    cc.dias_alerta_3_meses,
                    cc.dias_alerta_6_meses,
                    cc.dias_alerta_9_meses,
                    cc.notificaciones_activas,
                    cc.email_responsable,
                    cc.telefono_whatsapp,
                    DATEDIFF(plc.fecha_caducidad, CURDATE()) as dias_restantes
                FROM productos_lotes_caducidad plc
                LEFT JOIN Sucursales s ON plc.sucursal_id = s.ID_Sucursal
                LEFT JOIN caducados_configuracion cc ON plc.sucursal_id = cc.sucursal_id
                WHERE plc.estado IN ('activo', 'agotado')
                AND cc.notificaciones_activas = 1
                AND DATEDIFF(plc.fecha_caducidad, CURDATE()) <= cc.dias_alerta_9_meses
                AND DATEDIFF(plc.fecha_caducidad, CURDATE()) >= 0";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $productos = [];
        while ($row = $result->fetch_assoc()) {
            $productos[] = $row;
        }
        
        return $productos;
    }
    
    /**
     * Determinar tipo de alerta según días restantes
     */
    private function determinarTipoAlerta($dias_restantes) {
        if ($dias_restantes <= 90) {
            return '3_meses';
        } elseif ($dias_restantes <= 180) {
            return '6_meses';
        } elseif ($dias_restantes <= 270) {
            return '9_meses';
        }
        
        return null;
    }
    
    /**
     * Crear notificación en la base de datos
     */
    private function crearNotificacion($producto, $tipo_alerta) {
        // Verificar si ya existe una notificación pendiente para este lote
        $sql_verificar = "SELECT id_notificacion FROM caducados_notificaciones 
                          WHERE id_lote = ? AND tipo_alerta = ? AND estado = 'pendiente'";
        $stmt_verificar = $this->conn->prepare($sql_verificar);
        $stmt_verificar->bind_param("is", $producto['id_lote'], $tipo_alerta);
        $stmt_verificar->execute();
        $result_verificar = $stmt_verificar->get_result();
        
        if ($result_verificar->num_rows > 0) {
            return; // Ya existe una notificación pendiente
        }
        
        // Crear mensaje de notificación
        $mensaje = $this->generarMensaje($producto, $tipo_alerta);
        
        // Calcular fecha programada
        $fecha_programada = $this->calcularFechaProgramada($producto, $tipo_alerta);
        
        // Insertar notificación
        $sql_insert = "INSERT INTO caducados_notificaciones 
                       (id_lote, tipo_alerta, fecha_programada, mensaje, destinatario, estado) 
                       VALUES (?, ?, ?, ?, ?, 'pendiente')";
        
        $stmt_insert = $this->conn->prepare($sql_insert);
        $destinatario = $producto['email_responsable'] ?? 'admin@doctorpez.mx';
        $stmt_insert->bind_param("issss", 
            $producto['id_lote'],
            $tipo_alerta,
            $fecha_programada,
            $mensaje,
            $destinatario
        );
        $stmt_insert->execute();
    }
    
    /**
     * Generar mensaje de notificación
     */
    private function generarMensaje($producto, $tipo_alerta) {
        $dias_texto = '';
        switch ($tipo_alerta) {
            case '3_meses':
                $dias_texto = '3 meses';
                break;
            case '6_meses':
                $dias_texto = '6 meses';
                break;
            case '9_meses':
                $dias_texto = '9 meses';
                break;
        }
        
        $mensaje = "🚨 ALERTA DE CADUCIDAD 🚨\n\n";
        $mensaje .= "Producto: " . $producto['nombre_producto'] . "\n";
        $mensaje .= "Código: " . $producto['cod_barra'] . "\n";
        $mensaje .= "Lote: " . $producto['lote'] . "\n";
        $mensaje .= "Fecha de caducidad: " . $producto['fecha_caducidad'] . "\n";
        $mensaje .= "Cantidad: " . $producto['cantidad_actual'] . " unidades\n";
        $mensaje .= "Sucursal: " . $producto['Nombre_Sucursal'] . "\n";
        $mensaje .= "Días restantes: " . $producto['dias_restantes'] . "\n\n";
        $mensaje .= "⚠️ El producto caducará en " . $dias_texto . "\n";
        $mensaje .= "📅 Fecha: " . $producto['fecha_caducidad'] . "\n\n";
        $mensaje .= "Por favor, tome las acciones necesarias.";
        
        return $mensaje;
    }
    
    /**
     * Calcular fecha programada para la notificación
     */
    private function calcularFechaProgramada($producto, $tipo_alerta) {
        $dias_antes = 0;
        
        switch ($tipo_alerta) {
            case '3_meses':
                $dias_antes = $producto['dias_alerta_3_meses'];
                break;
            case '6_meses':
                $dias_antes = $producto['dias_alerta_6_meses'];
                break;
            case '9_meses':
                $dias_antes = $producto['dias_alerta_9_meses'];
                break;
        }
        
        $fecha_caducidad = new DateTime($producto['fecha_caducidad']);
        $fecha_programada = $fecha_caducidad->sub(new DateInterval("P{$dias_antes}D"));
        
        return $fecha_programada->format('Y-m-d');
    }
    
    /**
     * Enviar notificaciones pendientes
     */
    public function enviarNotificacionesPendientes() {
        try {
            $sql = "SELECT 
                        cn.*,
                        plc.nombre_producto,
                        plc.cod_barra,
                        plc.lote,
                        plc.fecha_caducidad,
                        s.Nombre_Sucursal
                    FROM caducados_notificaciones cn
                    LEFT JOIN productos_lotes_caducidad plc ON cn.id_lote = plc.id_lote
                    LEFT JOIN Sucursales s ON plc.sucursal_id = s.ID_Sucursal
                    WHERE cn.estado = 'pendiente' 
                    AND cn.fecha_programada <= CURDATE()";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $notificaciones_enviadas = 0;
            
            while ($row = $result->fetch_assoc()) {
                $this->enviarNotificacion($row);
                $notificaciones_enviadas++;
            }
            
            return [
                'success' => true,
                'notificaciones_enviadas' => $notificaciones_enviadas,
                'message' => "Se enviaron $notificaciones_enviadas notificaciones"
            ];
            
        } catch (Exception $e) {
            throw new Exception("Error al enviar notificaciones: " . $e->getMessage());
        }
    }
    
    /**
     * Enviar notificación individual
     */
    private function enviarNotificacion($notificacion) {
        try {
            // Aquí se implementaría el envío real por WhatsApp, email, etc.
            // Por ahora solo marcamos como enviada
            
            $sql_update = "UPDATE caducados_notificaciones 
                           SET estado = 'enviada', fecha_envio = NOW() 
                           WHERE id_notificacion = ?";
            
            $stmt_update = $this->conn->prepare($sql_update);
            $stmt_update->bind_param("i", $notificacion['id_notificacion']);
            $stmt_update->execute();
            
            // Log del envío
            error_log("Notificación enviada: " . $notificacion['mensaje']);
            
        } catch (Exception $e) {
            // Marcar como error
            $sql_error = "UPDATE caducados_notificaciones 
                          SET estado = 'error', fecha_envio = NOW() 
                          WHERE id_notificacion = ?";
            
            $stmt_error = $this->conn->prepare($sql_error);
            $stmt_error->bind_param("i", $notificacion['id_notificacion']);
            $stmt_error->execute();
            
            error_log("Error al enviar notificación: " . $e->getMessage());
        }
    }
    
    /**
     * Obtener estadísticas de notificaciones
     */
    public function obtenerEstadisticasNotificaciones($sucursal_id = null) {
        try {
            $sql = "SELECT 
                        COUNT(*) as total_notificaciones,
                        SUM(CASE WHEN estado = 'pendiente' THEN 1 ELSE 0 END) as pendientes,
                        SUM(CASE WHEN estado = 'enviada' THEN 1 ELSE 0 END) as enviadas,
                        SUM(CASE WHEN estado = 'error' THEN 1 ELSE 0 END) as errores,
                        SUM(CASE WHEN tipo_alerta = '3_meses' THEN 1 ELSE 0 END) as alertas_3_meses,
                        SUM(CASE WHEN tipo_alerta = '6_meses' THEN 1 ELSE 0 END) as alertas_6_meses,
                        SUM(CASE WHEN tipo_alerta = '9_meses' THEN 1 ELSE 0 END) as alertas_9_meses
                    FROM caducados_notificaciones cn
                    LEFT JOIN productos_lotes_caducidad plc ON cn.id_lote = plc.id_lote
                    WHERE 1=1";
            
            $params = [];
            $types = '';
            
            if ($sucursal_id) {
                $sql .= " AND plc.sucursal_id = ?";
                $params[] = $sucursal_id;
                $types .= 'i';
            }
            
            $stmt = $this->conn->prepare($sql);
            if (!empty($params)) {
                $stmt->bind_param($types, ...$params);
            }
            $stmt->execute();
            $result = $stmt->get_result();
            
            return $result->fetch_assoc();
            
        } catch (Exception $e) {
            throw new Exception("Error al obtener estadísticas de notificaciones: " . $e->getMessage());
        }
    }
}
?>
