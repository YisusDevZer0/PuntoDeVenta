<?php
include_once "db_connect.php";
include_once "ControladorUsuario.php";

// Obtener parámetros (igual que DespliegaAutoCompleteSucursales.php)
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

// Consulta simple con prepared statement
$query = "SELECT DISTINCT Cod_Barra, Nombre_Prod, ID_Prod_POS FROM Stock_POS 
          WHERE (Cod_Barra LIKE ? OR Nombre_Prod LIKE ?) 
          AND Fk_Sucursal = ?
          ORDER BY Nombre_Prod ASC
          LIMIT 20";
$stmt = $conn->prepare($query);
$autocompletado = array();

if ($stmt && $sucursal > 0) {
    $term_param = "%{$term}%";
    $stmt->bind_param("ssi", $term_param, $term_param, $sucursal);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row_data = $result->fetch_assoc()) {
        $autocompletado[] = array(
            'label' => $row_data['Cod_Barra'] . ' - ' . $row_data['Nombre_Prod'],
            'value' => $row_data['Cod_Barra'] ? $row_data['Cod_Barra'] : $row_data['Nombre_Prod'],
            'id' => $row_data['ID_Prod_POS'],
            'codigo' => $row_data['Cod_Barra'],
            'nombre' => $row_data['Nombre_Prod']
        );
    }
    $stmt->close();
}

// Devuelve los resultados de autocompletado como JSON
header('Content-Type: application/json');
echo json_encode($autocompletado);
?>
