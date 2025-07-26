<?php
include_once "db_connect.php";
include_once "ControladorUsuario.php";
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';
    
    // Buscar productos para autocompletado
    if ($accion === 'buscar_producto') {
        $q = $_POST['q'] ?? '';
        $sucursal_id = $row['Fk_Sucursal'] ?? '';
        
        $sql = "SELECT 
                    s.ID_Prod_POS, 
                    s.Nombre_Prod, 
                    s.Cod_Barra,
                    s.Clave_adicional,
                    s.Existencias_R,
                    s.Min_Existencia,
                    s.Max_Existencia,
                    p.Precio_Venta,
                    p.Precio_C
                FROM Stock_POS s
                LEFT JOIN Productos_POS p ON s.ID_Prod_POS = p.ID_Prod_POS
                WHERE s.Fk_sucursal = ? 
                AND (s.Nombre_Prod LIKE ? OR s.Cod_Barra LIKE ? OR s.Clave_adicional LIKE ?)
                ORDER BY s.Nombre_Prod ASC 
                LIMIT 20";
        
        $stmt = $conn->prepare($sql);
        $like = "%$q%";
        $stmt->bind_param("ssss", $sucursal_id, $like, $like, $like);
        $stmt->execute();
        $res = $stmt->get_result();
        $productos = [];
        
        while($prod = $res->fetch_assoc()) {
            $productos[] = $prod;
        }
        
        echo json_encode(['status' => 'ok', 'data' => $productos]);
        exit;
    }
    
    // Obtener productos con stock bajo
    if ($accion === 'productos_stock_bajo') {
        $sucursal_id = $row['Fk_Sucursal'] ?? '';
        
        $sql = "SELECT 
                    s.ID_Prod_POS, 
                    s.Nombre_Prod, 
                    s.Cod_Barra,
                    s.Existencias_R,
                    s.Min_Existencia,
                    p.Precio_Venta,
                    p.Precio_C,
                    (s.Min_Existencia - s.Existencias_R) as cantidad_necesaria
                FROM Stock_POS s
                LEFT JOIN Productos_POS p ON s.ID_Prod_POS = p.ID_Prod_POS
                WHERE s.Fk_sucursal = ? 
                AND s.Existencias_R < s.Min_Existencia
                ORDER BY (s.Min_Existencia - s.Existencias_R) DESC";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $sucursal_id);
        $stmt->execute();
        $res = $stmt->get_result();
        $productos = [];
        
        while($prod = $res->fetch_assoc()) {
            $productos[] = $prod;
        }
        
        echo json_encode(['status' => 'ok', 'data' => $productos]);
        exit;
    }
    
    // Crear nuevo pedido
    if ($accion === 'crear_pedido') {
        try {
            $conn->begin_transaction();
            
            // Generar folio único
            $stmt = $conn->prepare("CALL generar_folio_pedido(?)");
            $folio = '';
            $stmt->bind_param("s", $folio);
            $stmt->execute();
            $stmt->close();
            
            // Insertar pedido principal
            $sucursal_id = $row['Fk_Sucursal'];
            $usuario_id = $row['Id_PvUser'];
            $observaciones = $_POST['observaciones'] ?? '';
            $prioridad = $_POST['prioridad'] ?? 'normal';
            
            $sql = "INSERT INTO pedidos (folio, sucursal_id, usuario_id, observaciones, prioridad, tipo_origen) 
                    VALUES (?, ?, ?, ?, ?, 'admin')";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("siiss", $folio, $sucursal_id, $usuario_id, $observaciones, $prioridad);
            $stmt->execute();
            $pedido_id = $conn->insert_id;
            
            // Insertar detalles del pedido
            $productos = json_decode($_POST['productos'], true);
            
            foreach ($productos as $producto) {
                $sql_det = "INSERT INTO pedido_detalles (pedido_id, producto_id, cantidad_solicitada, precio_unitario, subtotal) 
                           VALUES (?, ?, ?, ?, ?)";
                $stmt2 = $conn->prepare($sql_det);
                $subtotal = $producto['cantidad'] * $producto['precio'];
                $stmt2->bind_param("iiddd", $pedido_id, $producto['id'], $producto['cantidad'], $producto['precio'], $subtotal);
                $stmt2->execute();
            }
            
            // Registrar en historial
            $sql_hist = "INSERT INTO pedido_historial (pedido_id, usuario_id, estado_anterior, estado_nuevo, comentario) 
                        VALUES (?, ?, NULL, 'pendiente', 'Pedido creado')";
            $stmt3 = $conn->prepare($sql_hist);
            $stmt3->bind_param("ii", $pedido_id, $usuario_id);
            $stmt3->execute();
            
            $conn->commit();
            echo json_encode(['status' => 'ok', 'msg' => 'Pedido creado exitosamente', 'pedido_id' => $pedido_id, 'folio' => $folio]);
            
        } catch (Exception $e) {
            $conn->rollback();
            echo json_encode(['status' => 'error', 'msg' => 'Error al crear pedido: ' . $e->getMessage()]);
        }
        exit;
    }
    
    // Listar pedidos con filtros
    if ($accion === 'listar_pedidos') {
        $sucursal_id = $row['Fk_Sucursal'];
        $filtro_sucursal = $_POST['filtro_sucursal'] ?? '';
        $filtro_estado = $_POST['filtro_estado'] ?? '';
        $filtro_fecha_inicio = $_POST['filtro_fecha_inicio'] ?? '';
        $filtro_fecha_fin = $_POST['filtro_fecha_fin'] ?? '';
        $busqueda = $_POST['busqueda'] ?? '';
        
        $where_conditions = [];
        $params = [];
        $types = '';
        
        // Filtro por sucursal
        if ($filtro_sucursal === 'todas') {
            // Ver todas las sucursales (solo para administradores)
        } else {
            $where_conditions[] = "p.sucursal_id = ?";
            $params[] = $sucursal_id;
            $types .= 'i';
        }
        
        // Filtro por estado
        if (!empty($filtro_estado)) {
            $where_conditions[] = "p.estado = ?";
            $params[] = $filtro_estado;
            $types .= 's';
        }
        
        // Filtro por fecha
        if (!empty($filtro_fecha_inicio)) {
            $where_conditions[] = "DATE(p.fecha_creacion) >= ?";
            $params[] = $filtro_fecha_inicio;
            $types .= 's';
        }
        
        if (!empty($filtro_fecha_fin)) {
            $where_conditions[] = "DATE(p.fecha_creacion) <= ?";
            $params[] = $filtro_fecha_fin;
            $types .= 's';
        }
        
        // Búsqueda general
        if (!empty($busqueda)) {
            $where_conditions[] = "(p.folio LIKE ? OR s.Nombre_Prod LIKE ? OR u.Nombre LIKE ? OR u.Apellido LIKE ?)";
            $busqueda_like = "%$busqueda%";
            $params[] = $busqueda_like;
            $params[] = $busqueda_like;
            $params[] = $busqueda_like;
            $params[] = $busqueda_like;
            $types .= 'ssss';
        }
        
        $where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';
        
        $sql = "SELECT 
                    p.id,
                    p.folio,
                    p.estado,
                    p.prioridad,
                    p.observaciones,
                    p.fecha_creacion,
                    p.fecha_aprobacion,
                    p.total_estimado,
                    s.Nombre_Sucursal,
                    CONCAT(u.Nombre, ' ', u.Apellido) as usuario_nombre,
                    CONCAT(aprobador.Nombre, ' ', aprobador.Apellido) as aprobador_nombre,
                    COUNT(pd.id) as total_productos,
                    SUM(pd.cantidad_solicitada) as total_cantidad
                FROM pedidos p
                LEFT JOIN Sucursales s ON p.sucursal_id = s.ID_Sucursal
                LEFT JOIN usuarios u ON p.usuario_id = u.ID_Usuario
                LEFT JOIN usuarios aprobador ON p.aprobado_por = aprobador.ID_Usuario
                LEFT JOIN pedido_detalles pd ON p.id = pd.pedido_id
                $where_clause
                GROUP BY p.id
                ORDER BY p.fecha_creacion DESC
                LIMIT 100";
        
        $stmt = $conn->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $res = $stmt->get_result();
        $pedidos = [];
        
        while($pedido = $res->fetch_assoc()) {
            $pedidos[] = $pedido;
        }
        
        echo json_encode(['status' => 'ok', 'data' => $pedidos]);
        exit;
    }
    
    // Obtener detalle de pedido
    if ($accion === 'detalle_pedido') {
        $pedido_id = intval($_POST['pedido_id']);
        
        // Información del pedido
        $sql = "SELECT 
                    p.*,
                    s.Nombre_Sucursal,
                    CONCAT(u.Nombre, ' ', u.Apellido) as usuario_nombre,
                    CONCAT(aprobador.Nombre, ' ', aprobador.Apellido) as aprobador_nombre
                FROM pedidos p
                LEFT JOIN Sucursales s ON p.sucursal_id = s.ID_Sucursal
                LEFT JOIN usuarios u ON p.usuario_id = u.ID_Usuario
                LEFT JOIN usuarios aprobador ON p.aprobado_por = aprobador.ID_Usuario
                WHERE p.id = ?";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $pedido_id);
        $stmt->execute();
        $pedido = $stmt->get_result()->fetch_assoc();
        
        // Detalles del pedido
        $sql_det = "SELECT 
                        pd.*,
                        s.Nombre_Prod,
                        s.Cod_Barra,
                        s.Existencias_R,
                        p.Precio_Venta,
                        p.Precio_C
                    FROM pedido_detalles pd
                    LEFT JOIN Stock_POS s ON pd.producto_id = s.ID_Prod_POS
                    LEFT JOIN Productos_POS p ON s.ID_Prod_POS = p.ID_Prod_POS
                    WHERE pd.pedido_id = ?";
        
        $stmt2 = $conn->prepare($sql_det);
        $stmt2->bind_param("i", $pedido_id);
        $stmt2->execute();
        $detalles = $stmt2->get_result()->fetch_all(MYSQLI_ASSOC);
        
        // Historial del pedido
        $sql_hist = "SELECT 
                        ph.*,
                        CONCAT(u.Nombre, ' ', u.Apellido) as usuario_nombre
                    FROM pedido_historial ph
                    LEFT JOIN usuarios u ON ph.usuario_id = u.ID_Usuario
                    WHERE ph.pedido_id = ?
                    ORDER BY ph.fecha_cambio DESC";
        
        $stmt3 = $conn->prepare($sql_hist);
        $stmt3->bind_param("i", $pedido_id);
        $stmt3->execute();
        $historial = $stmt3->get_result()->fetch_all(MYSQLI_ASSOC);
        
        echo json_encode([
            'status' => 'ok', 
            'pedido' => $pedido,
            'detalles' => $detalles,
            'historial' => $historial
        ]);
        exit;
    }
    
    // Cambiar estado del pedido
    if ($accion === 'cambiar_estado') {
        $pedido_id = intval($_POST['pedido_id']);
        $nuevo_estado = $_POST['nuevo_estado'];
        $comentario = $_POST['comentario'] ?? '';
        $usuario_id = $row['Id_PvUser'];
        
        try {
            $conn->begin_transaction();
            
            // Obtener estado actual
            $sql_estado = "SELECT estado FROM pedidos WHERE id = ?";
            $stmt = $conn->prepare($sql_estado);
            $stmt->bind_param("i", $pedido_id);
            $stmt->execute();
            $estado_actual = $stmt->get_result()->fetch_assoc()['estado'];
            
            // Actualizar estado
            $sql_update = "UPDATE pedidos SET estado = ?";
            $params = [$nuevo_estado];
            $types = 's';
            
            if ($nuevo_estado === 'aprobado') {
                $sql_update .= ", fecha_aprobacion = NOW(), aprobado_por = ?";
                $params[] = $usuario_id;
                $types .= 'i';
            } elseif ($nuevo_estado === 'completado') {
                $sql_update .= ", fecha_completado = NOW()";
            }
            
            $sql_update .= " WHERE id = ?";
            $params[] = $pedido_id;
            $types .= 'i';
            
            $stmt2 = $conn->prepare($sql_update);
            $stmt2->bind_param($types, ...$params);
            $stmt2->execute();
            
            // Registrar en historial
            $sql_hist = "INSERT INTO pedido_historial (pedido_id, usuario_id, estado_anterior, estado_nuevo, comentario) 
                        VALUES (?, ?, ?, ?, ?)";
            $stmt3 = $conn->prepare($sql_hist);
            $stmt3->bind_param("iisss", $pedido_id, $usuario_id, $estado_actual, $nuevo_estado, $comentario);
            $stmt3->execute();
            
            $conn->commit();
            echo json_encode(['status' => 'ok', 'msg' => 'Estado actualizado correctamente']);
            
        } catch (Exception $e) {
            $conn->rollback();
            echo json_encode(['status' => 'error', 'msg' => 'Error al actualizar estado: ' . $e->getMessage()]);
        }
        exit;
    }
    
    // Eliminar pedido
    if ($accion === 'eliminar_pedido') {
        $pedido_id = intval($_POST['pedido_id']);
        
        try {
            $sql = "DELETE FROM pedidos WHERE id = ? AND estado = 'pendiente'";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $pedido_id);
            $stmt->execute();
            
            if ($stmt->affected_rows > 0) {
                echo json_encode(['status' => 'ok', 'msg' => 'Pedido eliminado correctamente']);
            } else {
                echo json_encode(['status' => 'error', 'msg' => 'No se puede eliminar el pedido']);
            }
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'msg' => 'Error al eliminar pedido: ' . $e->getMessage()]);
        }
        exit;
    }
    
    // Obtener estadísticas
    if ($accion === 'estadisticas') {
        $sucursal_id = $row['Fk_Sucursal'];
        
        $sql = "SELECT 
                    estado,
                    COUNT(*) as total,
                    SUM(total_estimado) as total_valor
                FROM pedidos 
                WHERE sucursal_id = ?
                GROUP BY estado";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $sucursal_id);
        $stmt->execute();
        $estadisticas = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        
        echo json_encode(['status' => 'ok', 'data' => $estadisticas]);
        exit;
    }
    
    // Obtener sucursales para filtro
    if ($accion === 'obtener_sucursales') {
        $sql = "SELECT ID_Sucursal, Nombre_Sucursal FROM Sucursales WHERE Sucursal_Activa = 1 ORDER BY Nombre_Sucursal";
        $result = $conn->query($sql);
        $sucursales = $result->fetch_all(MYSQLI_ASSOC);
        
        echo json_encode(['status' => 'ok', 'data' => $sucursales]);
        exit;
    }
}

echo json_encode(['status' => 'error', 'msg' => 'Acción no válida']);
?> 