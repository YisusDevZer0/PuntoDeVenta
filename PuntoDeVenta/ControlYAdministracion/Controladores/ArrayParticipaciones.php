<?php
header('Content-Type: application/json');
include("db_connect.php");
include "ControladorUsuario.php";

$sorteoId = isset($_GET['sorteo_id']) ? intval($_GET['sorteo_id']) : 0;

$where = "";
if ($sorteoId > 0) {
    $where = "WHERE sp.Fk_Sorteo = " . $sorteoId;
}

$sql = "SELECT sp.*, s.Nombre_Sorteo, su.Nombre_Sucursal
        FROM Sorteo_Participaciones sp
        LEFT JOIN Sorteos s ON s.ID_Sorteo = sp.Fk_Sorteo
        LEFT JOIN Sucursales su ON su.ID_Sucursal = sp.Fk_Sucursal
        $where
        ORDER BY sp.ID_Participacion DESC";

$result = @mysqli_query($conn, $sql);

$c = 0;
$data = [];

if ($result) {
while ($fila = $result->fetch_assoc()) {
    $data[$c]["ID"] = $fila["ID_Participacion"];
    $data[$c]["Sorteo"] = $fila["Nombre_Sorteo"] ? $fila["Nombre_Sorteo"] : '-';
    $data[$c]["Ticket"] = $fila["Fk_Venta_Ticket"];
    $data[$c]["FolioRifa"] = '<span class="badge bg-success">'.$fila["FolioRifa"].'</span>';
    $data[$c]["Cliente"] = $fila["Nombre_Cliente"];
    $data[$c]["Telefono"] = $fila["Telefono_Cliente"] ? $fila["Telefono_Cliente"] : '-';
    $data[$c]["FechaNac"] = $fila["FechaNacimiento_Cliente"] ? $fila["FechaNacimiento_Cliente"] : '-';
    $data[$c]["Sucursal"] = $fila["Nombre_Sucursal"] ? $fila["Nombre_Sucursal"] : '-';
    
    if ($fila["Participa"] == 1) {
        $data[$c]["Participa"] = '<span style="background-color:#28a745;color:white;padding:4px 10px;border-radius:12px;font-size:12px;">Sí</span>';
    } else {
        $data[$c]["Participa"] = '<span style="background-color:#dc3545;color:white;padding:4px 10px;border-radius:12px;font-size:12px;">No</span>';
    }
    
    $data[$c]["Registrado"] = $fila["RegistradoEl"];
    
    $c++;
}
} // end if ($result)

$results = [
    "sEcho" => 1,
    "iTotalRecords" => count($data),
    "iTotalDisplayRecords" => count($data),
    "aaData" => $data
];

echo json_encode($results);
?>
