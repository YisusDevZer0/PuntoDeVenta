<?php
include "db_connect.php.php";

header('Content-Type: application/json');

$search = isset($_GET['search']) ? $_GET['search'] : '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

// Preparar la consulta con búsqueda
$search = $conn->real_escape_string($search);
$sql = "SELECT ID_Paciente, Nombre, Apellido_Paterno, Apellido_Materno 
        FROM Pacientes 
        WHERE CONCAT(Nombre, ' ', Apellido_Paterno, ' ', Apellido_Materno) LIKE ? 
        ORDER BY Nombre ASC 
        LIMIT ? OFFSET ?";

$stmt = $conn->prepare($sql);
$search_param = "%{$search}%";
$stmt->bind_param("sii", $search_param, $limit, $offset);
$stmt->execute();
$result = $stmt->get_result();

$items = array();
while ($row = $result->fetch_assoc()) {
    $items[] = array(
        'id' => $row['ID_Paciente'],
        'text' => $row['Nombre'] . ' ' . $row['Apellido_Paterno'] . ' ' . $row['Apellido_Materno']
    );
}

// Verificar si hay más resultados
$sql_count = "SELECT COUNT(*) as total FROM Pacientes WHERE CONCAT(Nombre, ' ', Apellido_Paterno, ' ', Apellido_Materno) LIKE ?";
$stmt_count = $conn->prepare($sql_count);
$stmt_count->bind_param("s", $search_param);
$stmt_count->execute();
$total = $stmt_count->get_result()->fetch_assoc()['total'];

echo json_encode(array(
    'items' => $items,
    'more' => ($offset + $limit) < $total
));
?> 