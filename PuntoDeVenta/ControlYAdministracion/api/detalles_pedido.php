<?php
header('Content-Type: application/json');
include_once "../Controladores/db_connect.php";
include_once "../Controladores/ControladorUsuario.php";

// Verificar sesión
if(!isset($_SESSION['ControlMaestro']) && !isset($_SESSION['AdministradorRH']) && !isset($_SESSION['Marketing'])){
    echo json_encode(['success' => false, 'message' => 'Sesión no válida']);
    exit();
}

try {
    $pedidoId = isset($_GET['id']) ? intval($_GET['id']) : 0;
    
    if ($pedidoId <= 0) {
        echo json_encode(['success' => false, 'message' => 'ID de pedido inválido']);
        exit();
    }
    
    // Obtener información del pedido usando tabla existente
    $sql = "SELECT 
                p.id,
                p.folio,
                p.fecha_creacion,
                p.estado,
                p.prioridad,
                p.observaciones,
                p.total_estimado,
                u.Nombre_Apellidos as solicitante,
                s.Nombre_Sucursal
            FROM pedidos p
            LEFT JOIN Usuarios_PV u ON p.usuario_id = u.Id_PvUser
            LEFT JOIN Sucursales s ON p.sucursal_id = s.ID_Sucursal
            WHERE p.id = ? AND p.tipo_origen = 'admin'";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $pedidoId);
    $stmt->execute();
    $result = $stmt->get_result();
    $pedido = $result->fetch_assoc();
    
    if (!$pedido) {
        echo json_encode(['success' => false, 'message' => 'Pedido no encontrado']);
        exit();
    }
    
    // Obtener productos del pedido usando tabla existente
    $sqlDetalle = "SELECT 
                        pd.id,
                        pd.cantidad_solicitada as cantidad,
                        pd.precio_unitario as precio,
                        pd.subtotal,
                        s.Nombre_Prod as nombre,
                        s.Cod_Barra as codigo,
                        s.Existencias_R as existencias,
                        s.Min_Existencia as min_existencia
                    FROM pedido_detalles pd
                    LEFT JOIN Stock_POS s ON pd.producto_id = s.ID_Prod_POS
                    WHERE pd.pedido_id = ?";
    
    $stmtDetalle = $conn->prepare($sqlDetalle);
    $stmtDetalle->bind_param("i", $pedidoId);
    $stmtDetalle->execute();
    $resultDetalle = $stmtDetalle->get_result();
    
    $productos = [];
    while ($row = $resultDetalle->fetch_assoc()) {
        // Verificar si es un encargo (producto_id = 999999 o nombre es null)
        if ($row['nombre'] === null && $row['codigo'] === null) {
            // Es un encargo - intentar obtener información de la tabla de encargos
            $sqlEncargo = "SELECT 
                            id,
                            medicamento,
                            nombre_paciente,
                            cantidad,
                            precioventa,
                            fecha_encargo,
                            NumTicket,
                            Empleado
                          FROM encargos 
                          WHERE id = ?";
            
            $stmtEncargo = $conn->prepare($sqlEncargo);
            $stmtEncargo->bind_param("i", $row['id']);
            $stmtEncargo->execute();
            $resultEncargo = $stmtEncargo->get_result();
            $encargo = $resultEncargo->fetch_assoc();
            
            if ($encargo) {
                // Es un encargo con información disponible
                $productos[] = [
                    'id' => $row['id'],
                    'nombre' => $encargo['medicamento'] ?? 'Medicamento especial',
                    'codigo' => 'ENC-' . $encargo['id'],
                    'cantidad' => $row['cantidad'],
                    'precio' => $row['precio'],
                    'subtotal' => $row['subtotal'],
                    'es_encargo' => true,
                    'cliente' => $encargo['nombre_paciente'] ?? 'N/A',
                    'ticket' => $encargo['NumTicket'] ?? 'N/A',
                    'empleado' => $encargo['Empleado'] ?? 'N/A',
                    'fecha_encargo' => $encargo['fecha_encargo'] ?? 'N/A'
                ];
            } else {
                // Es un encargo pero no encontramos la información
                $productos[] = [
                    'id' => $row['id'],
                    'nombre' => 'Producto especial (Encargo)',
                    'codigo' => 'ENC-' . $row['id'],
                    'cantidad' => $row['cantidad'],
                    'precio' => $row['precio'],
                    'subtotal' => $row['subtotal'],
                    'es_encargo' => true
                ];
            }
        } else {
            // Es un producto normal
            $productos[] = [
                'id' => $row['id'],
                'nombre' => $row['nombre'],
                'codigo' => $row['codigo'],
                'cantidad' => $row['cantidad'],
                'precio' => $row['precio'],
                'subtotal' => $row['subtotal'],
                'es_encargo' => false
            ];
        }
    }
    
    $pedido['productos'] = $productos;
    
    echo json_encode([
        'success' => true,
        'pedido' => $pedido
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error al obtener detalles: ' . $e->getMessage()
    ]);
}
?> 