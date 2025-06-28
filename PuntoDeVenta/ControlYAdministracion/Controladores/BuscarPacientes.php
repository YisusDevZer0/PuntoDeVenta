<?php
header('Content-Type: application/json');
include_once "db_connect.php";

// Obtener el término de búsqueda
$term = isset($_GET['term']) ? $_GET['term'] : '';
$sucursal = isset($_GET['sucursal']) ? $_GET['sucursal'] : '';

// Si no hay término de búsqueda, devolver array vacío
if (empty($term)) {
    echo json_encode([]);
    exit;
}

// Buscar pacientes que coincidan con el término
$sql = "SELECT DISTINCT 
            id,
            nombre_paciente,
            telefono,
            edad,
            sexo
        FROM encargos 
        WHERE nombre_paciente LIKE ? 
        AND Fk_Sucursal = ?
        ORDER BY nombre_paciente 
        LIMIT 10";

$stmt = $conn->prepare($sql);
if ($stmt) {
    $search_term = '%' . $term . '%';
    $stmt->bind_param("ss", $search_term, $sucursal);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $pacientes = [];
    while ($row = $result->fetch_assoc()) {
        $pacientes[] = [
            'id' => $row['id'],
            'nombre_paciente' => $row['nombre_paciente'],
            'telefono' => $row['telefono'],
            'edad' => $row['edad'],
            'sexo' => $row['sexo']
        ];
    }
    
    $stmt->close();
    echo json_encode($pacientes);
} else {
    // Fallback si la preparación falla
    $search_term = '%' . $term . '%';
    $sql_fallback = "SELECT DISTINCT 
                        id,
                        nombre_paciente,
                        telefono,
                        edad,
                        sexo
                    FROM encargos 
                    WHERE nombre_paciente LIKE '$search_term' 
                    AND Fk_Sucursal = '$sucursal'
                    ORDER BY nombre_paciente 
                    LIMIT 10";
    
    $result = mysqli_query($conn, $sql_fallback);
    $pacientes = [];
    
    while ($row = mysqli_fetch_assoc($result)) {
        $pacientes[] = [
            'id' => $row['id'],
            'nombre_paciente' => $row['nombre_paciente'],
            'telefono' => $row['telefono'],
            'edad' => $row['edad'],
            'sexo' => $row['sexo']
        ];
    }
    
    echo json_encode($pacientes);
}
?> 