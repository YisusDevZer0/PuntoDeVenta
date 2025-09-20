<?php
header('Content-Type: application/json');
include_once "../../Consultas/db_connect.php";

try {
    // Verificar método POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Método no permitido');
    }
    
    // Obtener datos del POST
    $titulo = $_POST['titulo'] ?? '';
    $descripcion = $_POST['descripcion'] ?? '';
    $fecha_recordatorio = $_POST['fecha_recordatorio'] ?? '';
    $prioridad = $_POST['prioridad'] ?? 'media';
    $id_usuario = $_POST['id_usuario'] ?? 1;
    
    // Validar datos requeridos
    if (empty(trim($titulo))) {
        throw new Exception('El título es requerido');
    }
    
    if (empty(trim($descripcion))) {
        throw new Exception('La descripción es requerida');
    }
    
    if (empty($fecha_recordatorio)) {
        throw new Exception('La fecha del recordatorio es requerida');
    }
    
    // Validar prioridad
    $prioridades_validas = ['baja', 'media', 'alta'];
    if (!in_array($prioridad, $prioridades_validas)) {
        $prioridad = 'media';
    }
    
    // Crear recordatorio en la base de datos
    $sql = "INSERT INTO Recordatorios_Limpieza (
                titulo, 
                descripcion, 
                fecha_recordatorio, 
                prioridad, 
                id_usuario, 
                estado, 
                fecha_creacion
            ) VALUES (?, ?, ?, ?, ?, 'activo', NOW())";
    
    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        throw new Exception('Error preparando consulta: ' . mysqli_error($conn));
    }
    
    mysqli_stmt_bind_param($stmt, "ssssi", 
        $titulo, 
        $descripcion, 
        $fecha_recordatorio, 
        $prioridad, 
        $id_usuario
    );
    
    if (mysqli_stmt_execute($stmt)) {
        echo json_encode([
            'success' => true,
            'message' => 'Recordatorio creado exitosamente'
        ]);
    } else {
        throw new Exception('Error al crear el recordatorio: ' . mysqli_stmt_error($stmt));
    }
    
    mysqli_stmt_close($stmt);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>