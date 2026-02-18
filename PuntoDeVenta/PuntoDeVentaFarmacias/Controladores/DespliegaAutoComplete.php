<?php
include_once "db_connect.php";
include_once "ControladorUsuario.php";
// Término de búsqueda y opcionalmente sucursal (para filtrar por sucursal)
$term = isset($_GET['term']) ? trim($_GET['term']) : '';
$fkSucursal = isset($_GET['fkSucursal']) ? (int)$_GET['fkSucursal'] : 0;

$autocompletado = array();
if ($term === '') {
    echo json_encode($autocompletado);
    mysqli_close($conn);
    exit;
}

$termLike = '%' . $conn->real_escape_string($term) . '%';

// Si se envía sucursal, buscar solo productos de esa sucursal (Stock_POS)
if ($fkSucursal > 0) {
    $stmt = $conn->prepare("SELECT DISTINCT Cod_Barra, Nombre_Prod FROM Stock_POS WHERE Fk_sucursal = ? AND (Cod_Barra LIKE ? OR Nombre_Prod LIKE ?) ORDER BY Nombre_Prod LIMIT 50");
    if ($stmt) {
        $stmt->bind_param("iss", $fkSucursal, $termLike, $termLike);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $autocompletado[] = array('label' => $row['Cod_Barra'] . ' - ' . $row['Nombre_Prod'], 'value' => $row['Cod_Barra']);
        }
        $stmt->close();
    }
}

// Si no hay sucursal o no hubo resultados por sucursal, buscar en CEDIS (catálogo global)
if (empty($autocompletado)) {
    $query = "SELECT DISTINCT Cod_Barra, Nombre_Prod FROM CEDIS WHERE (Cod_Barra LIKE ? OR Nombre_Prod LIKE ?) LIMIT 50";
    $stmt = $conn->prepare($query);
    if ($stmt) {
        $stmt->bind_param("ss", $termLike, $termLike);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $autocompletado[] = array('label' => $row['Cod_Barra'] . ' - ' . $row['Nombre_Prod'], 'value' => $row['Cod_Barra']);
        }
        $stmt->close();
    }
}

echo json_encode($autocompletado);
mysqli_close($conn);
?>
