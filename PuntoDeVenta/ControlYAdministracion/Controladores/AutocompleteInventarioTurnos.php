<?php
header('Content-Type: application/json');
include_once "db_connect.php";
include_once "ControladorUsuario.php";

// Obtener parámetros
$term = isset($_GET['term']) ? trim($_GET['term']) : '';
$id_turno = isset($_GET['id_turno']) ? (int)$_GET['id_turno'] : 0;
$sucursal = isset($row['Fk_Sucursal']) ? (int)$row['Fk_Sucursal'] : 0;

if (empty($term) || strlen($term) < 2) {
    echo json_encode([]);
    exit;
}

// Obtener información del turno si se proporciona
$usuario_turno = '';
if ($id_turno > 0) {
    $sql_turno = "SELECT Usuario_Actual, Fk_sucursal FROM Inventario_Turnos WHERE ID_Turno = ? LIMIT 1";
    $stmt_turno = $conn->prepare($sql_turno);
    if ($stmt_turno) {
        $stmt_turno->bind_param("i", $id_turno);
        $stmt_turno->execute();
        $turno_data = $stmt_turno->get_result()->fetch_assoc();
        $stmt_turno->close();
        if ($turno_data) {
            $usuario_turno = $turno_data['Usuario_Actual'];
            if ($sucursal == 0) {
                $sucursal = $turno_data['Fk_sucursal'];
            }
        }
    }
}

// Búsqueda de productos
$sql = "SELECT DISTINCT 
    sp.ID_Prod_POS,
    sp.Cod_Barra,
    sp.Nombre_Prod,
    sp.Existencias_R,
    sp.Fk_sucursal
FROM Stock_POS sp
WHERE sp.Fk_sucursal = ?
AND (sp.Cod_Barra LIKE ? OR sp.Nombre_Prod LIKE ?)
ORDER BY sp.Nombre_Prod ASC
LIMIT 15";

$stmt = $conn->prepare($sql);
$autocompletado = [];

if ($stmt && $sucursal > 0) {
    $likeTerm = "%$term%";
    $stmt->bind_param("iss", $sucursal, $likeTerm, $likeTerm);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        // Verificar si el producto está bloqueado por otro usuario en el turno
        $bloqueado = false;
        if ($id_turno > 0 && !empty($usuario_turno)) {
            $sql_bloqueo = "SELECT COUNT(*) as bloqueado 
                           FROM Inventario_Productos_Bloqueados 
                           WHERE ID_Turno = ? 
                           AND ID_Prod_POS = ? 
                           AND Usuario_Bloqueo != ? 
                           AND Estado = 'bloqueado'";
            $stmt_bloq = $conn->prepare($sql_bloqueo);
            if ($stmt_bloq) {
                $stmt_bloq->bind_param("iis", $id_turno, $row['ID_Prod_POS'], $usuario_turno);
                $stmt_bloq->execute();
                $bloq_data = $stmt_bloq->get_result()->fetch_assoc();
                $stmt_bloq->close();
                $bloqueado = ($bloq_data && $bloq_data['bloqueado'] > 0);
            }
        }
        
        $label = $row['Cod_Barra'] . ' - ' . $row['Nombre_Prod'];
        if ($bloqueado) {
            $label .= ' [BLOQUEADO]';
        }
        
        $autocompletado[] = [
            'label' => $label,
            'value' => $row['Cod_Barra'] ? $row['Cod_Barra'] : $row['Nombre_Prod'],
            'id' => $row['ID_Prod_POS'],
            'codigo' => $row['Cod_Barra'],
            'nombre' => $row['Nombre_Prod'],
            'existencias' => $row['Existencias_R'],
            'bloqueado' => $bloqueado
        ];
    }
    
    $stmt->close();
}

echo json_encode($autocompletado);
$conn->close();
?>
