<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

session_start();
require_once '../../Consultas/db_connect.php';

// Verificar autenticación
if (!isset($_SESSION['usuario_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit;
}

$usuario_id = $_SESSION['usuario_id'];
$sucursal_id = $_SESSION['sucursal_id'] ?? 1;
$tipo_usuario = $_SESSION['tipo_usuario'] ?? 'Usuario';

// Función para obtener tipos de devolución
function getTiposDevolucion() {
    global $conn;
    
    $sql = "SELECT codigo, nombre, descripcion, color, requiere_autorizacion 
            FROM Tipos_Devolucion 
            WHERE activo = 1 
            ORDER BY nombre";
    
    $result = $conn->query($sql);
    $tipos = [];
    
    while ($row = $result->fetch_assoc()) {
        $tipos[$row['codigo']] = [
            'nombre' => $row['nombre'],
            'descripcion' => $row['descripcion'],
            'color' => $row['color'],
            'requiere_autorizacion' => (bool)$row['requiere_autorizacion']
        ];
    }
    
    return $tipos;
}

// Función para buscar producto
function buscarProducto($codigo_barras, $sucursal_id) {
    global $conn;
    
    $sql = "SELECT s.*, p.Precio_Venta, p.Precio_C, p.Fecha_Caducidad, p.Lote
            FROM Stock_POS s 
            LEFT JOIN productos_lotes_caducidad p ON s.ID_Prod_POS = p.ID_Prod_POS AND s.Fk_sucursal = p.Fk_sucursal
            WHERE s.Cod_Barra = ? AND s.Fk_sucursal = ? AND s.Existencias_R > 0
            ORDER BY p.Fecha_Caducidad ASC
            LIMIT 1";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $codigo_barras, $sucursal_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    return $result->fetch_assoc();
}

// Función para obtener ventas recientes del producto
function getVentasRecientes($codigo_barras, $sucursal_id, $limite = 10) {
    global $conn;
    
    $sql = "SELECT v.*, c.Nombre_Sucursal
            FROM Ventas_POSV2 v
            LEFT JOIN Sucursales c ON v.Fk_sucursal = c.ID_Sucursal
            WHERE v.Cod_Barra = ? AND v.Fk_sucursal = ?
            ORDER BY v.Fecha_venta DESC
            LIMIT ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sii", $codigo_barras, $sucursal_id, $limite);
    $stmt->execute();
    $result = $stmt->get_result();
    
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Función para validar autorización
function requiereAutorizacion($tipo_devolucion) {
    global $conn;
    
    $sql = "SELECT requiere_autorizacion FROM Tipos_Devolucion WHERE codigo = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $tipo_devolucion);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    return $row ? (bool)$row['requiere_autorizacion'] : false;
}

// Función para generar folio único
function generarFolioDevolucion() {
    global $conn;
    
    $prefijo = 'DEV-' . date('Ymd') . '-';
    $contador = 1;
    
    do {
        $folio = $prefijo . str_pad($contador, 4, '0', STR_PAD_LEFT);
        $sql = "SELECT id FROM Devoluciones WHERE folio = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $folio);
        $stmt->execute();
        $result = $stmt->get_result();
        $contador++;
    } while ($result->num_rows > 0);
    
    return $folio;
}

// Procesar peticiones
$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? $_POST['action'] ?? '';

$response = ['success' => false, 'message' => ''];

try {
    switch ($action) {
        case 'buscar_producto':
            $codigo_barras = trim($_POST['codigo_barras'] ?? '');
            
            if (empty($codigo_barras)) {
                throw new Exception('Código de barras requerido');
            }
            
            $producto = buscarProducto($codigo_barras, $sucursal_id);
            if (!$producto) {
                throw new Exception('Producto no encontrado o sin existencias');
            }
            
            $ventas_recientes = getVentasRecientes($codigo_barras, $sucursal_id, 5);
            
            $response = [
                'success' => true,
                'producto' => $producto,
                'ventas_recientes' => $ventas_recientes
            ];
            break;
            
        case 'agregar_producto':
            $codigo_barras = trim($_POST['codigo_barras'] ?? '');
            $cantidad = intval($_POST['cantidad'] ?? 0);
            $tipo_devolucion = $_POST['tipo_devolucion'] ?? '';
            $observaciones = trim($_POST['observaciones'] ?? '');
            
            if (empty($codigo_barras) || $cantidad <= 0 || empty($tipo_devolucion)) {
                throw new Exception('Todos los campos son requeridos');
            }
            
            $producto = buscarProducto($codigo_barras, $sucursal_id);
            if (!$producto) {
                throw new Exception('Producto no encontrado o sin existencias');
            }
            
            if ($cantidad > $producto['Existencias_R']) {
                throw new Exception('La cantidad excede las existencias disponibles');
            }
            
            // Inicializar sesión temporal si no existe
            if (!isset($_SESSION['devolucion_temp'])) {
                $_SESSION['devolucion_temp'] = [];
            }
            
            $item_key = $codigo_barras . '_' . $producto['Lote'];
            
            if (isset($_SESSION['devolucion_temp'][$item_key])) {
                $_SESSION['devolucion_temp'][$item_key]['cantidad'] += $cantidad;
            } else {
                $_SESSION['devolucion_temp'][$item_key] = [
                    'codigo_barras' => $codigo_barras,
                    'producto' => $producto,
                    'cantidad' => $cantidad,
                    'tipo_devolucion' => $tipo_devolucion,
                    'observaciones' => $observaciones,
                    'valor_total' => $cantidad * $producto['Precio_Venta']
                ];
            }
            
            $response = [
                'success' => true,
                'message' => 'Producto agregado correctamente',
                'item_key' => $item_key
            ];
            break;
            
        case 'obtener_productos_temp':
            $productos = $_SESSION['devolucion_temp'] ?? [];
            $response = [
                'success' => true,
                'productos' => array_values($productos)
            ];
            break;
            
        case 'actualizar_cantidad':
            $item_key = $_POST['item_key'] ?? '';
            $nueva_cantidad = intval($_POST['cantidad'] ?? 0);
            
            if (empty($item_key) || $nueva_cantidad <= 0) {
                throw new Exception('Datos inválidos');
            }
            
            if (!isset($_SESSION['devolucion_temp'][$item_key])) {
                throw new Exception('Producto no encontrado');
            }
            
            $producto = $_SESSION['devolucion_temp'][$item_key]['producto'];
            if ($nueva_cantidad > $producto['Existencias_R']) {
                throw new Exception('La cantidad excede las existencias disponibles');
            }
            
            $_SESSION['devolucion_temp'][$item_key]['cantidad'] = $nueva_cantidad;
            $_SESSION['devolucion_temp'][$item_key]['valor_total'] = $nueva_cantidad * $producto['Precio_Venta'];
            
            $response = [
                'success' => true,
                'message' => 'Cantidad actualizada correctamente'
            ];
            break;
            
        case 'eliminar_producto':
            $item_key = $_POST['item_key'] ?? '';
            
            if (empty($item_key)) {
                throw new Exception('Item no especificado');
            }
            
            if (isset($_SESSION['devolucion_temp'][$item_key])) {
                unset($_SESSION['devolucion_temp'][$item_key]);
                $response = [
                    'success' => true,
                    'message' => 'Producto eliminado correctamente'
                ];
            } else {
                throw new Exception('Producto no encontrado');
            }
            break;
            
        case 'obtener_totales':
            $productos = $_SESSION['devolucion_temp'] ?? [];
            $total_productos = count($productos);
            $total_unidades = array_sum(array_column($productos, 'cantidad'));
            $valor_total = array_sum(array_column($productos, 'valor_total'));
            
            $response = [
                'success' => true,
                'total_productos' => $total_productos,
                'total_unidades' => $total_unidades,
                'valor_total' => $valor_total
            ];
            break;
            
        case 'procesar_devolucion':
            if (empty($_SESSION['devolucion_temp'])) {
                throw new Exception('No hay productos para procesar');
            }
            
            $observaciones_generales = trim($_POST['observaciones_generales'] ?? '');
            
            // Verificar si requiere autorización
            $requiere_auth = false;
            foreach ($_SESSION['devolucion_temp'] as $item) {
                if (requiereAutorizacion($item['tipo_devolucion'])) {
                    $requiere_auth = true;
                    break;
                }
            }
            
            $folio = generarFolioDevolucion();
            
            // Iniciar transacción
            $conn->begin_transaction();
            
            try {
                // Crear registro de devolución
                $sql = "INSERT INTO Devoluciones (folio, sucursal_id, usuario_id, fecha, estatus, observaciones_generales) 
                        VALUES (?, ?, ?, NOW(), ?, ?)";
                $stmt = $conn->prepare($sql);
                $estatus = $requiere_auth ? 'pendiente' : 'procesada';
                $stmt->bind_param("siiss", $folio, $sucursal_id, $usuario_id, $estatus, $observaciones_generales);
                $stmt->execute();
                $devolucion_id = $conn->insert_id;
                
                // Procesar cada producto
                foreach ($_SESSION['devolucion_temp'] as $item) {
                    $producto = $item['producto'];
                    
                    // Insertar detalle de devolución
                    $sql = "INSERT INTO Devoluciones_Detalle 
                            (devolucion_id, producto_id, codigo_barras, nombre_producto, cantidad, 
                             tipo_devolucion, observaciones, lote, fecha_caducidad, precio_venta, precio_costo, valor_total) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("iississssddd", 
                        $devolucion_id, 
                        $producto['ID_Prod_POS'], 
                        $item['codigo_barras'], 
                        $producto['Nombre_Prod'], 
                        $item['cantidad'], 
                        $item['tipo_devolucion'], 
                        $item['observaciones'], 
                        $producto['Lote'], 
                        $producto['Fecha_Caducidad'], 
                        $producto['Precio_Venta'],
                        $producto['Precio_C'],
                        $item['valor_total']
                    );
                    $stmt->execute();
                    
                    // Actualizar stock (reducir existencias)
                    $sql = "UPDATE Stock_POS 
                            SET Existencias_R = Existencias_R - ? 
                            WHERE ID_Prod_POS = ? AND Fk_sucursal = ? AND Lote = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("iiss", $item['cantidad'], $producto['ID_Prod_POS'], $sucursal_id, $producto['Lote']);
                    $stmt->execute();
                    
                    // Registrar en log de stock
                    $sql = "INSERT INTO Stock_POS_Log (Cod_Barra, Fk_sucursal, Cantidad, TipoDeMov, Mensaje) 
                            VALUES (?, ?, ?, 'Devolucion', ?)";
                    $stmt = $conn->prepare($sql);
                    $mensaje = "Devolución: {$item['tipo_devolucion']} - {$item['observaciones']}";
                    $stmt->bind_param("siis", $item['codigo_barras'], $sucursal_id, $item['cantidad'], $mensaje);
                    $stmt->execute();
                }
                
                // Si requiere autorización, crear registro de autorización
                if ($requiere_auth) {
                    $sql = "INSERT INTO Devoluciones_Autorizaciones (devolucion_id, usuario_autoriza, estatus) 
                            VALUES (?, ?, 'pendiente')";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("ii", $devolucion_id, $usuario_id);
                    $stmt->execute();
                } else {
                    // Marcar como procesada
                    $sql = "UPDATE Devoluciones SET estatus = 'procesada', fecha_procesada = NOW(), usuario_procesa = ? WHERE id = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("ii", $usuario_id, $devolucion_id);
                    $stmt->execute();
                }
                
                // Limpiar sesión temporal
                unset($_SESSION['devolucion_temp']);
                
                $conn->commit();
                
                $response = [
                    'success' => true,
                    'message' => 'Devolución procesada correctamente',
                    'folio' => $folio,
                    'devolucion_id' => $devolucion_id,
                    'requiere_autorizacion' => $requiere_auth
                ];
                
            } catch (Exception $e) {
                $conn->rollback();
                throw $e;
            }
            break;
            
        case 'cancelar_devolucion':
            unset($_SESSION['devolucion_temp']);
            $response = [
                'success' => true,
                'message' => 'Devolución cancelada correctamente'
            ];
            break;
            
        case 'obtener_devoluciones':
            $fecha_inicio = $_POST['fecha_inicio'] ?? null;
            $fecha_fin = $_POST['fecha_fin'] ?? null;
            $estatus = $_POST['estatus'] ?? null;
            
            $sql = "SELECT d.*, u.Nombre_Apellidos as usuario_nombre, s.Nombre_Sucursal
                    FROM Devoluciones d
                    LEFT JOIN Usuarios u ON d.usuario_id = u.ID_Usuario
                    LEFT JOIN Sucursales s ON d.sucursal_id = s.ID_Sucursal
                    WHERE 1=1";
            
            $params = [];
            $types = '';
            
            if ($sucursal_id) {
                $sql .= " AND d.sucursal_id = ?";
                $params[] = $sucursal_id;
                $types .= 'i';
            }
            
            if ($fecha_inicio) {
                $sql .= " AND DATE(d.fecha) >= ?";
                $params[] = $fecha_inicio;
                $types .= 's';
            }
            
            if ($fecha_fin) {
                $sql .= " AND DATE(d.fecha) <= ?";
                $params[] = $fecha_fin;
                $types .= 's';
            }
            
            if ($estatus) {
                $sql .= " AND d.estatus = ?";
                $params[] = $estatus;
                $types .= 's';
            }
            
            $sql .= " ORDER BY d.fecha DESC LIMIT 100";
            
            $stmt = $conn->prepare($sql);
            if (!empty($params)) {
                $stmt->bind_param($types, ...$params);
            }
            $stmt->execute();
            $result = $stmt->get_result();
            
            $devoluciones = [];
            while ($row = $result->fetch_assoc()) {
                $devoluciones[] = $row;
            }
            
            $response = [
                'success' => true,
                'devoluciones' => $devoluciones
            ];
            break;
            
        case 'obtener_detalles':
            $devolucion_id = intval($_POST['devolucion_id'] ?? 0);
            
            if (!$devolucion_id) {
                throw new Exception('ID de devolución requerido');
            }
            
            // Obtener datos de la devolución
            $sql = "SELECT d.*, u.Nombre_Apellidos as usuario_nombre, s.Nombre_Sucursal
                    FROM Devoluciones d
                    LEFT JOIN Usuarios u ON d.usuario_id = u.ID_Usuario
                    LEFT JOIN Sucursales s ON d.sucursal_id = s.ID_Sucursal
                    WHERE d.id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $devolucion_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $devolucion = $result->fetch_assoc();
            
            if (!$devolucion) {
                throw new Exception('Devolución no encontrada');
            }
            
            // Obtener detalles
            $sql = "SELECT * FROM Devoluciones_Detalle WHERE devolucion_id = ? ORDER BY id";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $devolucion_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $detalles = [];
            while ($row = $result->fetch_assoc()) {
                $detalles[] = $row;
            }
            
            $response = [
                'success' => true,
                'devolucion' => $devolucion,
                'detalles' => $detalles
            ];
            break;
            
        case 'obtener_tipos':
            $tipos = getTiposDevolucion();
            $response = [
                'success' => true,
                'tipos' => $tipos
            ];
            break;
            
        case 'autorizar_devolucion':
            $devolucion_id = intval($_POST['devolucion_id'] ?? 0);
            $observaciones = trim($_POST['observaciones'] ?? '');
            
            if (!$devolucion_id) {
                throw new Exception('ID de devolución requerido');
            }
            
            // Verificar que la devolución existe y está pendiente
            $sql = "SELECT id, estatus FROM Devoluciones WHERE id = ? AND estatus = 'pendiente'";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $devolucion_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $devolucion = $result->fetch_assoc();
            
            if (!$devolucion) {
                throw new Exception('Devolución no encontrada o ya procesada');
            }
            
            // Actualizar autorización
            $sql = "UPDATE Devoluciones_Autorizaciones 
                    SET estatus = 'aprobada', observaciones = ?, fecha_autorizacion = NOW() 
                    WHERE devolucion_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("si", $observaciones, $devolucion_id);
            $stmt->execute();
            
            // Marcar devolución como procesada
            $sql = "UPDATE Devoluciones 
                    SET estatus = 'procesada', fecha_procesada = NOW(), usuario_procesa = ? 
                    WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ii", $usuario_id, $devolucion_id);
            $stmt->execute();
            
            $response = [
                'success' => true,
                'message' => 'Devolución autorizada correctamente'
            ];
            break;
            
        case 'rechazar_devolucion':
            $devolucion_id = intval($_POST['devolucion_id'] ?? 0);
            $observaciones = trim($_POST['observaciones'] ?? '');
            
            if (!$devolucion_id) {
                throw new Exception('ID de devolución requerido');
            }
            
            // Actualizar autorización
            $sql = "UPDATE Devoluciones_Autorizaciones 
                    SET estatus = 'rechazada', observaciones = ?, fecha_autorizacion = NOW() 
                    WHERE devolucion_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("si", $observaciones, $devolucion_id);
            $stmt->execute();
            
            // Marcar devolución como cancelada
            $sql = "UPDATE Devoluciones 
                    SET estatus = 'cancelada' 
                    WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $devolucion_id);
            $stmt->execute();
            
            $response = [
                'success' => true,
                'message' => 'Devolución rechazada correctamente'
            ];
            break;
            
        default:
            throw new Exception('Acción no válida');
    }
    
} catch (Exception $e) {
    $response = [
        'success' => false,
        'message' => $e->getMessage()
    ];
}

echo json_encode($response);
?>
