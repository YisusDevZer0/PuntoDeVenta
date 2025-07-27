<?php
header('Content-Type: application/json');
include_once "../Controladores/db_connect.php";
include_once "../Controladores/ControladorUsuario.php";

// Verificar sesión
if(!isset($_SESSION['ControlMaestro']) && !isset($_SESSION['AdministradorRH']) && !isset($_SESSION['Marketing'])){
    echo json_encode(['success' => false, 'message' => 'Sesión no válida']);
    exit();
}

try {
    // Consulta para encargos disponibles
    $sql = "SELECT 
                e.ID_Encargo,
                e.Descripcion,
                e.Cliente,
                e.Fecha_Solicitud,
                e.Observaciones,
                e.Estatus,
                e.Precio_Estimado
            FROM Encargos e
            WHERE e.Estatus = 'Pendiente'
              AND e.Fecha_Solicitud >= DATE_SUB(NOW(), INTERVAL 30 DAY)
            ORDER BY e.Fecha_Solicitud DESC";
    
    $result = $conn->query($sql);
    
    if (!$result) {
        throw new Exception("Error en la consulta: " . $conn->error);
    }
    
    $encargos = [];
    while ($row = $result->fetch_assoc()) {
        $encargos[] = [
            'id' => $row['ID_Encargo'],
            'descripcion' => $row['Descripcion'],
            'cliente' => $row['Cliente'],
            'fecha' => $row['Fecha_Solicitud'],
            'observaciones' => $row['Observaciones'],
            'estado' => $row['Estatus'],
            'precio' => $row['Precio_Estimado']
        ];
    }
    
    echo json_encode([
        'success' => true,
        'encargos' => $encargos,
        'total' => count($encargos)
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error al obtener encargos: ' . $e->getMessage()
    ]);
}
?> 