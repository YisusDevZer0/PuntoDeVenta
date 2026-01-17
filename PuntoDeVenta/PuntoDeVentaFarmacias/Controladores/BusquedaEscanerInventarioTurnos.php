<?php
include_once "db_connect.php";
include_once "ControladorUsuario.php";

// Obtener parámetros
$codigo = isset($_POST['codigo']) ? trim($_POST['codigo']) : '';
$id_turno = isset($_POST['id_turno']) ? (int)$_POST['id_turno'] : 0;
$sucursal = isset($row['Fk_Sucursal']) ? (int)$row['Fk_Sucursal'] : 0;

if (empty($codigo)) {
    echo json_encode(['success' => false, 'message' => 'Código vacío']);
    exit;
}

// Buscar producto por código de barras o nombre
$sql = "SELECT ID_Prod_POS, Cod_Barra, Nombre_Prod, Existencias_R, Fk_sucursal
        FROM Stock_POS
        WHERE Fk_sucursal = ?
        AND (Cod_Barra = ? OR Nombre_Prod LIKE ?)
        LIMIT 1";

$stmt = $conn->prepare($sql);
$producto = null;

if ($stmt && $sucursal > 0) {
    $likeCodigo = "%" . $codigo . "%";
    $stmt->bind_param("iss", $sucursal, $codigo, $likeCodigo);
    $stmt->execute();
    $result = $stmt->get_result();
    $producto = $result->fetch_assoc();
    $stmt->close();
    
    if ($producto) {
        // Verificar si está bloqueado por otro usuario en el turno
        $bloqueado_por_otro = false;
        $usuario_actual = isset($row['Nombre_Apellidos']) ? trim($row['Nombre_Apellidos']) : '';
        
        if ($id_turno > 0 && !empty($usuario_actual)) {
            $sql_bloqueo = "SELECT Usuario_Bloqueo 
                           FROM Inventario_Productos_Bloqueados 
                           WHERE ID_Turno = ? 
                           AND ID_Prod_POS = ? 
                           AND Usuario_Bloqueo != ? 
                           AND Estado = 'bloqueado'
                           LIMIT 1";
            $stmt_bloq = $conn->prepare($sql_bloqueo);
            if ($stmt_bloq) {
                $stmt_bloq->bind_param("iis", $id_turno, $producto['ID_Prod_POS'], $usuario_actual);
                $stmt_bloq->execute();
                $bloq_data = $stmt_bloq->get_result()->fetch_assoc();
                $stmt_bloq->close();
                
                if ($bloq_data) {
                    $bloqueado_por_otro = true;
                    $usuario_bloqueador = $bloq_data['Usuario_Bloqueo'];
                }
            }
        }
        
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'producto' => [
                'id' => $producto['ID_Prod_POS'],
                'codigo' => $producto['Cod_Barra'],
                'nombre' => $producto['Nombre_Prod'],
                'existencias' => $producto['Existencias_R']
            ],
            'bloqueado_por_otro' => $bloqueado_por_otro,
            'usuario_bloqueador' => $bloqueado_por_otro ? $usuario_bloqueador : null
        ]);
    } else {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Producto no encontrado']);
    }
} else {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Error en parámetros']);
}

$conn->close();
?>
