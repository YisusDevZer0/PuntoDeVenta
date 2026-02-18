<?php
include_once "db_connect.php";
include_once "ControladorUsuario.php";

// Obtener el código de barras y la sucursal enviados por AJAX
$codigo = isset($_POST['codigoEscaneado']) ? trim($_POST['codigoEscaneado']) : '';
$fkSucursal = isset($_POST['fkSucursal']) ? (int)$_POST['fkSucursal'] : 0;

// Consultar CEDIS para obtener el artículo por código de barras
$sql = "SELECT Cod_Barra, Fecha_Caducidad, GROUP_CONCAT(ID_Prod_POS) AS IDs, GROUP_CONCAT(Nombre_Prod) AS descripciones, GROUP_CONCAT(Precio_Venta) AS precios, GROUP_CONCAT(Lote_Med) AS lotes,
GROUP_CONCAT(Clave_adicional) AS claves, GROUP_CONCAT(Tipo_Servicio) AS tipos, GROUP_CONCAT(Existencias) AS stockactual, GROUP_CONCAT(Precio_C) AS precioscompra
FROM CEDIS
WHERE Cod_Barra = ?
GROUP BY Cod_Barra";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $codigo);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $ids = explode(',', $row['IDs']);
    $descripciones = explode(',', $row['descripciones']);
    $precios = explode(',', $row['precios']);
    $precioscompra = explode(',', $row['precioscompra']);
    $fechacaducidad = explode(',', $row['Fecha_Caducidad']);
    $lotes = explode(',', $row['lotes']);
    $claves = explode(',', $row['claves']);
    $tipos = explode(',', $row['tipos']);

    $fechaCad = isset($fechacaducidad[0]) ? trim($fechacaducidad[0]) : '';
    $loteVal = isset($lotes[0]) ? trim($lotes[0]) : '';

    // Precargar lote y fecha de caducidad desde Stock_POS de la sucursal (datos existentes en la sucursal)
    if ($fkSucursal > 0) {
        $stmtSp = $conn->prepare("SELECT Lote, Fecha_Caducidad FROM Stock_POS WHERE Cod_Barra = ? AND Fk_sucursal = ? LIMIT 1");
        if ($stmtSp) {
            $stmtSp->bind_param("si", $codigo, $fkSucursal);
            $stmtSp->execute();
            $resSp = $stmtSp->get_result();
            if ($resSp && $filaSp = $resSp->fetch_assoc()) {
                $loteSp = isset($filaSp['Lote']) ? trim((string)$filaSp['Lote']) : '';
                $fechaSp = isset($filaSp['Fecha_Caducidad']) ? trim((string)$filaSp['Fecha_Caducidad']) : '';
                if ($loteSp !== '' || $fechaSp !== '') {
                    $loteVal = $loteSp;
                    $fechaCad = $fechaSp;
                }
            }
            $stmtSp->close();
        }
        // Si en la sucursal no hay lote/fecha pero sí en Historial_Lotes (varios lotes), usar el de mayor existencia
        if ($loteVal === '' && $fechaCad === '') {
            $stmtHl = $conn->prepare("SELECT Lote, Fecha_Caducidad FROM Historial_Lotes hl INNER JOIN Stock_POS sp ON hl.ID_Prod_POS = sp.ID_Prod_POS AND hl.Fk_sucursal = sp.Fk_sucursal WHERE sp.Cod_Barra = ? AND hl.Fk_sucursal = ? AND hl.Existencias > 0 ORDER BY hl.Existencias DESC LIMIT 1");
            if ($stmtHl) {
                $stmtHl->bind_param("si", $codigo, $fkSucursal);
                $stmtHl->execute();
                $resHl = $stmtHl->get_result();
                if ($resHl && $filaHl = $resHl->fetch_assoc()) {
                    $loteVal = isset($filaHl['Lote']) ? trim((string)$filaHl['Lote']) : '';
                    $fechaCad = isset($filaHl['Fecha_Caducidad']) ? trim((string)$filaHl['Fecha_Caducidad']) : '';
                }
                $stmtHl->close();
            }
        }
    }

    $data = array(
        "id" => $ids[0],
        "codigo" => $row["Cod_Barra"],
        "descripcion" => $descripciones[0],
        "cantidad" => [1],
        "existencia" => $fechaCad,
        "fechacaducidad" => $fechaCad,
        "precio" => $precios[0],
        "preciocompra" => $precioscompra[0],
        "lote" => $loteVal,
        "clave" => $claves[0],
        "tipo" => $tipos[0],
        "eliminar" => ""
    );

    header('Content-Type: application/json');
    echo json_encode($data);
} else {
    $data = array();
    header('Content-Type: application/json');
    echo json_encode($data);
}

$conn->close();
?>
