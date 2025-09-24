<?php
/**
 * Controlador principal para el módulo de caducados
 * Maneja la lógica de negocio para lotes y fechas de caducidad
 */

class CaducadosController {
    private $conn;
    
    public function __construct($connection) {
        $this->conn = $connection;
    }
    
    /**
     * Obtener estadísticas generales de caducados
     */
    public function obtenerEstadisticas($sucursal_id = null) {
        try {
            $sql = "SELECT 
                        COUNT(*) as total_lotes,
                        SUM(CASE WHEN DATEDIFF(fecha_caducidad, CURDATE()) BETWEEN 0 AND 90 THEN 1 ELSE 0 END) as alerta_3_meses,
                        SUM(CASE WHEN DATEDIFF(fecha_caducidad, CURDATE()) BETWEEN 91 AND 180 THEN 1 ELSE 0 END) as alerta_6_meses,
                        SUM(CASE WHEN DATEDIFF(fecha_caducidad, CURDATE()) BETWEEN 181 AND 270 THEN 1 ELSE 0 END) as alerta_9_meses,
                        SUM(CASE WHEN DATEDIFF(fecha_caducidad, CURDATE()) < 0 THEN 1 ELSE 0 END) as vencidos,
                        SUM(cantidad_actual) as total_cantidad,
                        SUM(cantidad_actual * precio_venta) as valor_total
                    FROM productos_lotes_caducidad 
                    WHERE estado IN ('activo', 'agotado')";
            
            $params = [];
            $types = '';
            
            if ($sucursal_id) {
                $sql .= " AND sucursal_id = ?";
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
            throw new Exception("Error al obtener estadísticas: " . $e->getMessage());
        }
    }
    
    /**
     * Obtener productos próximos a caducar
     */
    public function obtenerProductosProximosCaducar($filtros = []) {
        try {
            $sql = "SELECT 
                        plc.id_lote,
                        plc.cod_barra,
                        plc.nombre_producto,
                        plc.lote,
                        plc.fecha_caducidad,
                        plc.cantidad_actual,
                        plc.estado,
                        s.Nombre_Sucursal,
                        plc.proveedor,
                        plc.precio_compra,
                        plc.precio_venta,
                        plc.fecha_registro,
                        DATEDIFF(plc.fecha_caducidad, CURDATE()) as dias_restantes,
                        CASE 
                            WHEN DATEDIFF(plc.fecha_caducidad, CURDATE()) < 0 THEN 'vencido'
                            WHEN DATEDIFF(plc.fecha_caducidad, CURDATE()) <= 90 THEN '3_meses'
                            WHEN DATEDIFF(plc.fecha_caducidad, CURDATE()) <= 180 THEN '6_meses'
                            WHEN DATEDIFF(plc.fecha_caducidad, CURDATE()) <= 270 THEN '9_meses'
                            ELSE 'normal'
                        END as tipo_alerta
                    FROM productos_lotes_caducidad plc
                    LEFT JOIN Sucursales s ON plc.sucursal_id = s.ID_Sucursal
                    WHERE plc.estado IN ('activo', 'agotado')";
            
            $params = [];
            $types = '';
            
            // Aplicar filtros
            if (isset($filtros['sucursal_id']) && $filtros['sucursal_id']) {
                $sql .= " AND plc.sucursal_id = ?";
                $params[] = $filtros['sucursal_id'];
                $types .= 'i';
            }
            
            if (isset($filtros['estado']) && $filtros['estado']) {
                $sql .= " AND plc.estado = ?";
                $params[] = $filtros['estado'];
                $types .= 's';
            }
            
            if (isset($filtros['tipo_alerta']) && $filtros['tipo_alerta']) {
                switch ($filtros['tipo_alerta']) {
                    case 'vencido':
                        $sql .= " AND DATEDIFF(plc.fecha_caducidad, CURDATE()) < 0";
                        break;
                    case '3_meses':
                        $sql .= " AND DATEDIFF(plc.fecha_caducidad, CURDATE()) BETWEEN 0 AND 90";
                        break;
                    case '6_meses':
                        $sql .= " AND DATEDIFF(plc.fecha_caducidad, CURDATE()) BETWEEN 91 AND 180";
                        break;
                    case '9_meses':
                        $sql .= " AND DATEDIFF(plc.fecha_caducidad, CURDATE()) BETWEEN 181 AND 270";
                        break;
                }
            }
            
            $sql .= " ORDER BY plc.fecha_caducidad ASC, plc.nombre_producto ASC";
            
            $stmt = $this->conn->prepare($sql);
            if (!empty($params)) {
                $stmt->bind_param($types, ...$params);
            }
            $stmt->execute();
            $result = $stmt->get_result();
            
            $productos = [];
            while ($row = $result->fetch_assoc()) {
                $productos[] = $row;
            }
            
            return $productos;
            
        } catch (Exception $e) {
            throw new Exception("Error al obtener productos: " . $e->getMessage());
        }
    }
    
    /**
     * Registrar nuevo lote
     */
    public function registrarLote($datos) {
        try {
            // Validar datos requeridos
            $required_fields = ['cod_barra', 'lote', 'fecha_caducidad', 'cantidad', 'sucursal_id', 'usuario_registro'];
            foreach ($required_fields as $field) {
                if (!isset($datos[$field]) || empty($datos[$field])) {
                    throw new Exception("Campo requerido: $field");
                }
            }
            
            // Buscar el producto en Stock_POS
            $sql_producto = "SELECT sp.Folio_Prod_Stock, sp.Nombre_Prod, sp.Precio_Venta, sp.Precio_C 
                             FROM Stock_POS sp 
                             WHERE sp.Cod_Barra = ? AND sp.Fk_sucursal = ?";
            $stmt_producto = $this->conn->prepare($sql_producto);
            $stmt_producto->bind_param("si", $datos['cod_barra'], $datos['sucursal_id']);
            $stmt_producto->execute();
            $result_producto = $stmt_producto->get_result();
            
            if ($result_producto->num_rows === 0) {
                throw new Exception('Producto no encontrado en la sucursal especificada');
            }
            
            $producto = $result_producto->fetch_assoc();
            
            // Verificar si ya existe un lote con el mismo código
            $sql_verificar = "SELECT id_lote FROM productos_lotes_caducidad 
                              WHERE cod_barra = ? AND lote = ? AND sucursal_id = ? AND estado != 'retirado'";
            $stmt_verificar = $this->conn->prepare($sql_verificar);
            $stmt_verificar->bind_param("ssi", $datos['cod_barra'], $datos['lote'], $datos['sucursal_id']);
            $stmt_verificar->execute();
            $result_verificar = $stmt_verificar->get_result();
            
            if ($result_verificar->num_rows > 0) {
                throw new Exception('Ya existe un lote activo con este código en la sucursal');
            }
            
            // Iniciar transacción
            $this->conn->begin_transaction();
            
            try {
                // Insertar nuevo lote
                $sql_insert = "INSERT INTO productos_lotes_caducidad 
                               (folio_stock, cod_barra, nombre_producto, lote, fecha_caducidad, fecha_ingreso, 
                                cantidad_inicial, cantidad_actual, sucursal_id, proveedor, precio_compra, precio_venta, 
                                estado, usuario_registro, observaciones) 
                               VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'activo', ?, ?)";
                
                $stmt_insert = $this->conn->prepare($sql_insert);
                $fecha_ingreso = date('Y-m-d');
                $proveedor = $datos['proveedor'] ?? null;
                $precio_compra = $datos['precio_compra'] ?? null;
                $observaciones = $datos['observaciones'] ?? null;
                
                $stmt_insert->bind_param("isssssiiisssis", 
                    $producto['Folio_Prod_Stock'],
                    $datos['cod_barra'],
                    $producto['Nombre_Prod'],
                    $datos['lote'],
                    $datos['fecha_caducidad'],
                    $fecha_ingreso,
                    $datos['cantidad'],
                    $datos['cantidad'],
                    $datos['sucursal_id'],
                    $proveedor,
                    $precio_compra,
                    $producto['Precio_Venta'],
                    $datos['usuario_registro'],
                    $observaciones
                );
                
                $stmt_insert->execute();
                $id_lote = $this->conn->insert_id;
                
                // Registrar en historial
                $sql_historial = "INSERT INTO caducados_historial 
                                 (id_lote, tipo_movimiento, cantidad_nueva, fecha_caducidad_nueva, 
                                  sucursal_origen, usuario_movimiento, observaciones) 
                                 VALUES (?, 'registro', ?, ?, ?, ?, ?)";
                
                $stmt_historial = $this->conn->prepare($sql_historial);
                $observaciones_historial = "Registro inicial del lote: " . $datos['lote'];
                $stmt_historial->bind_param("iisiiis", 
                    $id_lote,
                    $datos['cantidad'],
                    $datos['fecha_caducidad'],
                    $datos['sucursal_id'],
                    $datos['usuario_registro'],
                    $observaciones_historial
                );
                $stmt_historial->execute();
                
                // Actualizar stock en Stock_POS
                $sql_update_stock = "UPDATE Stock_POS 
                                     SET Lote = ?, Fecha_Caducidad = ?, Existencias_R = Existencias_R + ? 
                                     WHERE Folio_Prod_Stock = ?";
                $stmt_update_stock = $this->conn->prepare($sql_update_stock);
                $stmt_update_stock->bind_param("ssii", 
                    $datos['lote'], 
                    $datos['fecha_caducidad'], 
                    $datos['cantidad'],
                    $producto['Folio_Prod_Stock']
                );
                $stmt_update_stock->execute();
                
                // Confirmar transacción
                $this->conn->commit();
                
                return [
                    'success' => true,
                    'id_lote' => $id_lote,
                    'message' => 'Lote registrado exitosamente'
                ];
                
            } catch (Exception $e) {
                $this->conn->rollback();
                throw $e;
            }
            
        } catch (Exception $e) {
            throw new Exception("Error al registrar lote: " . $e->getMessage());
        }
    }
    
    /**
     * Obtener historial de un lote
     */
    public function obtenerHistorialLote($id_lote) {
        try {
            $sql = "SELECT 
                        ch.*,
                        u.Nombre_Apellidos as usuario_nombre
                    FROM caducados_historial ch
                    LEFT JOIN Usuarios_PV u ON ch.usuario_movimiento = u.Id_PvUser
                    WHERE ch.id_lote = ?
                    ORDER BY ch.fecha_movimiento DESC";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("i", $id_lote);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $historial = [];
            while ($row = $result->fetch_assoc()) {
                $historial[] = $row;
            }
            
            return $historial;
            
        } catch (Exception $e) {
            throw new Exception("Error al obtener historial: " . $e->getMessage());
        }
    }
    
    /**
     * Obtener configuración de alertas por sucursal
     */
    public function obtenerConfiguracion($sucursal_id) {
        try {
            $sql = "SELECT * FROM caducados_configuracion WHERE sucursal_id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("i", $sucursal_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                return $result->fetch_assoc();
            } else {
                // Crear configuración por defecto
                $sql_insert = "INSERT INTO caducados_configuracion 
                               (sucursal_id, dias_alerta_3_meses, dias_alerta_6_meses, dias_alerta_9_meses, notificaciones_activas) 
                               VALUES (?, 90, 180, 270, 1)";
                $stmt_insert = $this->conn->prepare($sql_insert);
                $stmt_insert->bind_param("i", $sucursal_id);
                $stmt_insert->execute();
                
                return [
                    'sucursal_id' => $sucursal_id,
                    'dias_alerta_3_meses' => 90,
                    'dias_alerta_6_meses' => 180,
                    'dias_alerta_9_meses' => 270,
                    'notificaciones_activas' => 1
                ];
            }
            
        } catch (Exception $e) {
            throw new Exception("Error al obtener configuración: " . $e->getMessage());
        }
    }
}
?>
