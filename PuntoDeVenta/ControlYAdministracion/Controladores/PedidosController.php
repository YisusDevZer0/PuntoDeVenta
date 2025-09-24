<?php
include_once "db_connect.php";
header('Content-Type: application/json');

// Iniciar sesión si no está iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Verificar que el usuario esté autenticado usando el sistema existente
if (!isset($_SESSION['ControlMaestro']) && !isset($_SESSION['AdministradorRH']) && !isset($_SESSION['Marketing'])) {
    echo json_encode(['status' => 'error', 'msg' => 'Usuario no autenticado']);
    exit;
}

// Determinar el ID de usuario según la sesión activa
$userId = isset($_SESSION['ControlMaestro']) ? $_SESSION['ControlMaestro'] : (isset($_SESSION['AdministradorRH']) ? $_SESSION['AdministradorRH'] : $_SESSION['Marketing']);

// Obtener datos del usuario
$sql = "SELECT
    Usuarios_PV.Id_PvUser,
    Usuarios_PV.Nombre_Apellidos,
    Usuarios_PV.Fk_Sucursal,
    Sucursales.Nombre_Sucursal
FROM
    Usuarios_PV
INNER JOIN Sucursales ON Usuarios_PV.Fk_Sucursal = Sucursales.ID_Sucursal 
WHERE Usuarios_PV.Id_PvUser = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $userId);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if (!$row) {
    echo json_encode(['status' => 'error', 'msg' => 'Error al obtener datos del usuario']);
    exit;
}

$usuario_id = $row['Id_PvUser'];
$sucursal_id = $row['Fk_Sucursal'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';
    
    // Buscar productos para autocompletado
    if ($accion === 'buscar_producto') {
        $q = $_POST['q'] ?? '';
        
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
        $stmt->bind_param("isss", $sucursal_id, $like, $like, $like);
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
        $stmt->bind_param("i", $sucursal_id);
        $stmt->execute();
        $res = $stmt->get_result();
        $productos = [];
        
        while($prod = $res->fetch_assoc()) {
            $productos[] = $prod;
        }
        
        echo json_encode(['status' => 'ok', 'data' => $productos]);
        exit;
    }
    
    // Obtener encargos realizados para reutilizar productos
    if ($accion === 'obtener_encargos') {
        $busqueda = $_POST['busqueda'] ?? '';
        
        $sql = "SELECT DISTINCT
                    p.ID_Prod_POS,
                    p.Nombre_Prod,
                    p.Cod_Barra,
                    p.Clave_adicional,
                    p.Precio_Venta,
                    p.Precio_C,
                    s.Existencias_R,
                    s.Min_Existencia,
                    s.Max_Existencia,
                    COUNT(pd.id) as veces_solicitado,
                    AVG(pd.cantidad_solicitada) as cantidad_promedio
                FROM Productos_POS p
                LEFT JOIN Stock_POS s ON p.ID_Prod_POS = s.ID_Prod_POS AND s.Fk_sucursal = ?
                LEFT JOIN pedido_detalles pd ON p.ID_Prod_POS = pd.producto_id
                LEFT JOIN pedidos ped ON pd.pedido_id = ped.id
                WHERE ped.sucursal_id = ? 
                AND ped.estado IN ('completado', 'aprobado')
                AND (p.Nombre_Prod LIKE ? OR p.Cod_Barra LIKE ? OR p.Clave_adicional LIKE ?)
                GROUP BY p.ID_Prod_POS
                ORDER BY veces_solicitado DESC, p.Nombre_Prod ASC
                LIMIT 20";
        
        $stmt = $conn->prepare($sql);
        $like = "%$busqueda%";
        $stmt->bind_param("iisss", $sucursal_id, $sucursal_id, $like, $like, $like);
        $stmt->execute();
        $res = $stmt->get_result();
        $encargos = [];
        
        while($encargo = $res->fetch_assoc()) {
            $encargos[] = $encargo;
        }
        
        echo json_encode(['status' => 'ok', 'data' => $encargos]);
        exit;
    }
    
    // Crear nuevo pedido
    if ($accion === 'crear_pedido') {
        try {
            $conn->begin_transaction();
            
            // Generar folio único manualmente
            $fecha_actual = date('Ymd');
            $sql_count = "SELECT COUNT(*) as total FROM pedidos WHERE folio LIKE ?";
            $stmt = $conn->prepare($sql_count);
            $folio_pattern = "PED{$fecha_actual}%";
            $stmt->bind_param("s", $folio_pattern);
            $stmt->execute();
            $result = $stmt->get_result();
            $count = $result->fetch_assoc()['total'];
            $folio = "PED{$fecha_actual}" . str_pad($count + 1, 4, '0', STR_PAD_LEFT);
            
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
            
            // Actualizar total del pedido
            $sql_update_total = "UPDATE pedidos SET total_estimado = (
                SELECT SUM(subtotal) FROM pedido_detalles WHERE pedido_id = ?
            ) WHERE id = ?";
            $stmt3 = $conn->prepare($sql_update_total);
            $stmt3->bind_param("ii", $pedido_id, $pedido_id);
            $stmt3->execute();
            
            // Registrar en historial
            $sql_hist = "INSERT INTO pedido_historial (pedido_id, usuario_id, estado_anterior, estado_nuevo, comentario) 
                        VALUES (?, ?, NULL, 'pendiente', 'Pedido creado')";
            $stmt4 = $conn->prepare($sql_hist);
            $stmt4->bind_param("ii", $pedido_id, $usuario_id);
            $stmt4->execute();
            
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
        
        // Parámetros de paginación
        $pagina = intval($_POST['pagina'] ?? 1);
        $por_pagina = intval($_POST['por_pagina'] ?? 10);
        $offset = ($pagina - 1) * $por_pagina;
        
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
        
        // Filtro por estado - solo aplicar si se especifica un estado
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
            $where_conditions[] = "(p.folio LIKE ? OR s.Nombre_Prod LIKE ? OR u.Nombre_Apellidos LIKE ?)";
            $busqueda_like = "%$busqueda%";
            $params[] = $busqueda_like;
            $params[] = $busqueda_like;
            $params[] = $busqueda_like;
            $types .= 'sss';
        }
        
        $where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';
        
        // Primero obtener el total de registros para la paginación
        $sql_count = "SELECT COUNT(DISTINCT p.id) as total
                     FROM pedidos p
                     LEFT JOIN Sucursales s ON p.sucursal_id = s.ID_Sucursal
                     LEFT JOIN Usuarios_PV u ON p.usuario_id = u.Id_PvUser
                     LEFT JOIN pedido_detalles pd ON p.id = pd.pedido_id
                     $where_clause";
        
        $stmt_count = $conn->prepare($sql_count);
        if (!empty($params)) {
            $stmt_count->bind_param($types, ...$params);
        }
        $stmt_count->execute();
        $total_registros = $stmt_count->get_result()->fetch_assoc()['total'];
        $total_paginas = ceil($total_registros / $por_pagina);
        
        // Consulta principal con paginación
        $sql = "SELECT 
                    p.id,
                    p.folio,
                    p.estado,
                    p.prioridad,
                    p.observaciones,
                    p.fecha_creacion,
                    p.fecha_aprobacion,
                    p.fecha_completado,
                    p.total_estimado,
                    s.Nombre_Sucursal,
                    CONCAT(u.Nombre_Apellidos) as usuario_nombre,
                    COUNT(pd.id) as total_productos,
                    SUM(pd.cantidad_solicitada) as total_cantidad
                FROM pedidos p
                LEFT JOIN Sucursales s ON p.sucursal_id = s.ID_Sucursal
                LEFT JOIN Usuarios_PV u ON p.usuario_id = u.Id_PvUser
                LEFT JOIN pedido_detalles pd ON p.id = pd.pedido_id
                $where_clause
                GROUP BY p.id
                ORDER BY p.fecha_creacion DESC
                LIMIT ? OFFSET ?";
        
        $params[] = $por_pagina;
        $params[] = $offset;
        $types .= 'ii';
        
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
        
        echo json_encode([
            'status' => 'ok', 
            'data' => $pedidos,
            'paginacion' => [
                'pagina_actual' => $pagina,
                'total_paginas' => $total_paginas,
                'total_registros' => $total_registros,
                'por_pagina' => $por_pagina,
                'desde' => $offset + 1,
                'hasta' => min($offset + $por_pagina, $total_registros)
            ]
        ]);
        exit;
    }
    
    // Obtener detalle de un pedido específico
    if ($accion === 'detalle_pedido') {
        $pedido_id = $_POST['pedido_id'] ?? 0;
        
        // Obtener información del pedido
        $sql = "SELECT 
                    p.*,
                    s.Nombre_Sucursal,
                    CONCAT(u.Nombre_Apellidos) as usuario_nombre
                FROM pedidos p
                LEFT JOIN Sucursales s ON p.sucursal_id = s.ID_Sucursal
                LEFT JOIN Usuarios_PV u ON p.usuario_id = u.Id_PvUser
                WHERE p.id = ?";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $pedido_id);
        $stmt->execute();
        $pedido = $stmt->get_result()->fetch_assoc();
        
        // Obtener detalles del pedido
        $sql_det = "SELECT 
                        pd.*,
                        p.Nombre_Prod,
                        p.Cod_Barra
                    FROM pedido_detalles pd
                    LEFT JOIN Productos_POS p ON pd.producto_id = p.ID_Prod_POS
                    WHERE pd.pedido_id = ?";
        
        $stmt2 = $conn->prepare($sql_det);
        $stmt2->bind_param("i", $pedido_id);
        $stmt2->execute();
        $detalles = [];
        $res_det = $stmt2->get_result();
        
        while($detalle = $res_det->fetch_assoc()) {
            $detalles[] = $detalle;
        }
        
        // Obtener historial del pedido
        $sql_hist = "SELECT 
                        ph.*,
                        CONCAT(u.Nombre_Apellidos) as usuario_nombre
                    FROM pedido_historial ph
                    LEFT JOIN Usuarios_PV u ON ph.usuario_id = u.Id_PvUser
                    WHERE ph.pedido_id = ?
                    ORDER BY ph.fecha_cambio DESC";
        
        $stmt3 = $conn->prepare($sql_hist);
        $stmt3->bind_param("i", $pedido_id);
        $stmt3->execute();
        $historial = [];
        $res_hist = $stmt3->get_result();
        
        while($hist = $res_hist->fetch_assoc()) {
            $historial[] = $hist;
        }
        
        echo json_encode([
            'status' => 'ok',
            'pedido' => $pedido,
            'detalles' => $detalles,
            'historial' => $historial
        ]);
        exit;
    }
    
    // Cambiar estado de un pedido
    if ($accion === 'cambiar_estado') {
        $pedido_id = $_POST['pedido_id'] ?? 0;
        $nuevo_estado = $_POST['nuevo_estado'] ?? '';
        $comentario = $_POST['comentario'] ?? '';
        $usuario_id = $row['Id_PvUser'];
        
        try {
            $conn->begin_transaction();
            
            // Obtener estado actual
            $sql_actual = "SELECT estado FROM pedidos WHERE id = ?";
            $stmt = $conn->prepare($sql_actual);
            $stmt->bind_param("i", $pedido_id);
            $stmt->execute();
            $estado_actual = $stmt->get_result()->fetch_assoc()['estado'];
            
            // Actualizar estado
            $sql_update = "UPDATE pedidos SET estado = ?";
            $params = [$nuevo_estado];
            $types = 's';
            
            // Actualizar fechas según el estado
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
        $pedido_id = $_POST['pedido_id'] ?? 0;
        
        try {
            $conn->begin_transaction();
            
            // Verificar que el pedido esté pendiente
            $sql_check = "SELECT estado FROM pedidos WHERE id = ?";
            $stmt = $conn->prepare($sql_check);
            $stmt->bind_param("i", $pedido_id);
            $stmt->execute();
            $estado = $stmt->get_result()->fetch_assoc()['estado'];
            
            if ($estado !== 'pendiente') {
                echo json_encode(['status' => 'error', 'msg' => 'Solo se pueden eliminar pedidos pendientes']);
                exit;
            }
            
            // Eliminar detalles del pedido
            $sql_del_det = "DELETE FROM pedido_detalles WHERE pedido_id = ?";
            $stmt2 = $conn->prepare($sql_del_det);
            $stmt2->bind_param("i", $pedido_id);
            $stmt2->execute();
            
            // Eliminar historial del pedido
            $sql_del_hist = "DELETE FROM pedido_historial WHERE pedido_id = ?";
            $stmt3 = $conn->prepare($sql_del_hist);
            $stmt3->bind_param("i", $pedido_id);
            $stmt3->execute();
            
            // Eliminar pedido
            $sql_del_ped = "DELETE FROM pedidos WHERE id = ?";
            $stmt4 = $conn->prepare($sql_del_ped);
            $stmt4->bind_param("i", $pedido_id);
            $stmt4->execute();
            
            $conn->commit();
            echo json_encode(['status' => 'ok', 'msg' => 'Pedido eliminado correctamente']);
            
        } catch (Exception $e) {
            $conn->rollback();
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
        $res = $stmt->get_result();
        $stats = [];
        
        while($stat = $res->fetch_assoc()) {
            $stats[] = $stat;
        }
        
        echo json_encode(['status' => 'ok', 'data' => $stats]);
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
    
    // Obtener detalle de un pedido específico
    if ($accion === 'detalle_pedido') {
        $pedido_id = $_POST['pedido_id'] ?? 0;
        
        try {
            // Obtener información del pedido
            $sql_pedido = "SELECT 
                            p.id,
                            p.folio,
                            p.fecha_creacion,
                            p.estado,
                            p.prioridad,
                            p.observaciones,
                            p.total_estimado,
                            u.Nombre_Apellidos as usuario_nombre,
                            s.Nombre_Sucursal
                        FROM pedidos p
                        LEFT JOIN Usuarios_PV u ON p.usuario_id = u.Id_PvUser
                        LEFT JOIN Sucursales s ON p.sucursal_id = s.ID_Sucursal
                        WHERE p.id = ? AND p.tipo_origen = 'admin'";
            
            $stmt = $conn->prepare($sql_pedido);
            $stmt->bind_param("i", $pedido_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $pedido = $result->fetch_assoc();
            
            if (!$pedido) {
                echo json_encode(['status' => 'error', 'msg' => 'Pedido no encontrado']);
                exit;
            }
            
            // Obtener productos del pedido
            $sql_detalles = "SELECT 
                                pd.id,
                                pd.cantidad_solicitada,
                                pd.precio_unitario,
                                pd.subtotal,
                                pr.Nombre_Prod,
                                pr.Cod_Barra,
                                pr.Componente_Activo
                            FROM pedido_detalles pd
                            LEFT JOIN Productos_POS pr ON pd.producto_id = pr.ID_Prod_POS
                            WHERE pd.pedido_id = ?";
            
            $stmt2 = $conn->prepare($sql_detalles);
            $stmt2->bind_param("i", $pedido_id);
            $stmt2->execute();
            $result2 = $stmt2->get_result();
            $detalles = [];
            while ($row = $result2->fetch_assoc()) {
                $detalles[] = $row;
            }
            
            // Obtener historial del pedido
            $sql_historial = "SELECT 
                                ph.fecha_cambio,
                                ph.estado_anterior,
                                ph.estado_nuevo,
                                ph.comentario,
                                u.Nombre_Apellidos as usuario_nombre
                            FROM pedido_historial ph
                            LEFT JOIN Usuarios_PV u ON ph.usuario_id = u.Id_PvUser
                            WHERE ph.pedido_id = ?
                            ORDER BY ph.fecha_cambio DESC";
            
            $stmt3 = $conn->prepare($sql_historial);
            $stmt3->bind_param("i", $pedido_id);
            $stmt3->execute();
            $result3 = $stmt3->get_result();
            $historial = [];
            while ($row = $result3->fetch_assoc()) {
                $historial[] = $row;
            }
            
            echo json_encode([
                'status' => 'ok',
                'pedido' => $pedido,
                'detalles' => $detalles,
                'historial' => $historial
            ]);
            
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'msg' => 'Error al obtener detalle: ' . $e->getMessage()]);
        }
        exit;
    }
    
    // Generar traspaso desde pedido
    if ($accion === 'generar_traspaso') {
        $pedido_id = $_POST['pedido_id'] ?? 0;
        $productos = json_decode($_POST['productos'], true);
        
        try {
            $conn->begin_transaction();
            
            // Verificar que el pedido esté aprobado
            $sql_check = "SELECT estado, folio, sucursal_id FROM pedidos WHERE id = ?";
            $stmt = $conn->prepare($sql_check);
            $stmt->bind_param("i", $pedido_id);
            $stmt->execute();
            $pedido = $stmt->get_result()->fetch_assoc();
            
            if (!$pedido) {
                throw new Exception('Pedido no encontrado');
            }
            
            if ($pedido['estado'] !== 'aprobado') {
                throw new Exception('Solo se pueden generar traspasos de pedidos aprobados');
            }
            
            // Generar folio único para el traspaso
            $fecha_actual = date('Ymd');
            $sql_count = "SELECT COUNT(*) as total FROM TraspasosYNotasC WHERE Folio_Ticket LIKE ?";
            $stmt_count = $conn->prepare($sql_count);
            $folio_pattern = "TRP{$fecha_actual}%";
            $stmt_count->bind_param("s", $folio_pattern);
            $stmt_count->execute();
            $count = $stmt_count->get_result()->fetch_assoc()['total'];
            $folio_traspaso = "TRP{$fecha_actual}" . str_pad($count + 1, 4, '0', STR_PAD_LEFT);
            
            // Insertar productos en la tabla de traspasos
            $sql_insert = "INSERT INTO TraspasosYNotasC (
                Folio_Ticket, Cod_Barra, Nombre_Prod, Cantidad, 
                Fk_sucursal, Fk_SucursalDestino, Total_VentaG, Pc, 
                TipoDeMov, Fecha_venta, Estatus, Sistema, 
                AgregadoPor, ID_H_O_D
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt_insert = $conn->prepare($sql_insert);
            
            foreach ($productos as $producto) {
                $stmt_insert->bind_param("sssiiissssssss",
                    $folio_traspaso,
                    $producto['Cod_Barra'],
                    $producto['Nombre_Prod'],
                    $producto['cantidad_solicitada'],
                    $pedido['sucursal_id'], // Sucursal origen
                    $pedido['sucursal_id'], // Por defecto misma sucursal (se puede modificar)
                    $producto['subtotal'],
                    $producto['precio_unitario'],
                    'Traspaso',
                    date('Y-m-d'),
                    'Generado',
                    'POSVENTAS',
                    $row['Nombre_Apellidos'],
                    'Doctor Pez'
                );
                $stmt_insert->execute();
            }
            
            // Actualizar estado del pedido a 'traspaso_generado'
            $sql_update = "UPDATE pedidos SET estado = 'traspaso_generado' WHERE id = ?";
            $stmt_update = $conn->prepare($sql_update);
            $stmt_update->bind_param("i", $pedido_id);
            $stmt_update->execute();
            
            // Registrar en historial
            $sql_hist = "INSERT INTO pedido_historial (pedido_id, usuario_id, estado_anterior, estado_nuevo, comentario) 
                        VALUES (?, ?, 'aprobado', 'traspaso_generado', 'Traspaso generado automáticamente')";
            $stmt_hist = $conn->prepare($sql_hist);
            $stmt_hist->bind_param("ii", $pedido_id, $usuario_id);
            $stmt_hist->execute();
            
            $conn->commit();
            echo json_encode([
                'status' => 'ok', 
                'msg' => 'Traspaso generado exitosamente',
                'folio_traspaso' => $folio_traspaso
            ]);
            
        } catch (Exception $e) {
            $conn->rollback();
            echo json_encode(['status' => 'error', 'msg' => 'Error al generar traspaso: ' . $e->getMessage()]);
        }
        exit;
    }
}

echo json_encode(['status' => 'error', 'msg' => 'Acción no válida']);
?> 