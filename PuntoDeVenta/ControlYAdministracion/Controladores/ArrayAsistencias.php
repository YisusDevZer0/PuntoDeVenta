<?php
header('Content-Type: application/json');
include("db_connection_Huellas.php");

// Consulta de asistencias
$sql = "SELECT id_notificacion, id_asistencia, id_personal, nombre_completo, domicilio, nombre_dia, hora_registro, tipo_evento, fecha_notificacion, estado_notificacion FROM notificaciones_asistencia WHERE 1";

$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = [
        "id_notificacion" => $row["id_notificacion"],
        "id_asistencia" => $row["id_asistencia"],
        "id_personal" => $row["id_personal"],
        "nombre_completo" => $row["nombre_completo"],
        "domicilio" => $row["domicilio"],
        "nombre_dia" => $row["nombre_dia"],
        "hora_registro" => $row["hora_registro"],
        "tipo_evento" => $row["tipo_evento"],
        "fecha_notificacion" => $row["fecha_notificacion"],
        "estado_notificacion" => $row["estado_notificacion"],
    ];
}
$stmt->close();

$results = [
    "sEcho" => 1,
    "iTotalRecords" => count($data),
    "iTotalDisplayRecords" => count($data),
    "aaData" => $data
];
echo json_encode($results);
$conn->close(); 