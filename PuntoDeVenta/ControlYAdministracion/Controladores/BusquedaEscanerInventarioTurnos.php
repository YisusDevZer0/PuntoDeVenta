<?php
include_once "db_connect.php";
include_once "ControladorUsuario.php";

$codigo = isset($_POST['codigo']) ? trim($_POST['codigo']) : '';
$id_turno = isset($_POST['id_turno']) ? (int)$_POST['id_turno'] : 0;
$sucursal = isset($row['Fk_Sucursal']) ? (int)$row['Fk_Sucursal'] : 0;

if (empty($codigo)) {
    echo json_encode(['success' => false, 'message' => 'Código vacío']);
    exit;
}

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
        $bloqueado_por_otro = false;
        $usuario_actual = isset($row['Nombre_Apellidos']) ? trim($row['Nombre_Apellidos']) : '';
        $ya_contado_otro_turno = false;
        $folio_turno_anterior = null;
        
        if ($id_turno > 0 && $sucursal > 0) {
            $sql_ya_contado = "SELECT it.Folio_Turno 
                              FROM Inventario_Turnos_Productos itp
                              INNER JOIN Inventario_Turnos it ON itp.ID_Turno = it.ID_Turno
                              WHERE itp.ID_Prod_POS = ? 
                                AND itp.Fk_sucursal = ?
                                AND itp.Estado = 'completado'
                                AND it.ID_Turno != ?
                                AND it.Fk_sucursal = ?
                                AND DATE(it.Fecha_Turno) = CURDATE()
                              LIMIT 1";
            $stmt_ya = $conn->prepare($sql_ya_contado);
            if ($stmt_ya) {
                $stmt_ya->bind_param("iiii", $producto['ID_Prod_POS'], $sucursal, $id_turno, $sucursal);
                $stmt_ya->execute();
                $ya_data = $stmt_ya->get_result()->fetch_assoc();
                $stmt_ya->close();
                if ($ya_data) {
                    $ya_contado_otro_turno = true;
                    $folio_turno_anterior = $ya_data['Folio_Turno'];
                }
            }
        }
        
        if ($id_turno > 0 && !empty($usuario_actual) && !$ya_contado_otro_turno) {
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
            'usuario_bloqueador' => $bloqueado_por_otro ? $usuario_bloqueador : null,
            'ya_contado_otro_turno' => $ya_contado_otro_turno,
            'folio_turno_anterior' => $folio_turno_anterior
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
