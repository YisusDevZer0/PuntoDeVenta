<?php
header('Content-Type: application/json');
include_once "../Controladores/db_connect.php";
include_once "../Controladores/ControladorUsuario.php";

// Verificar sesión
session_start();
if(!isset($_SESSION['VentasPos'])){
    echo json_encode(['success' => false, 'message' => 'Sesión no válida']);
    exit();
}

try {
    // Verificar conexión a la base de datos
    if ($conn->connect_error) {
        throw new Exception("Error de conexión: " . $conn->connect_error);
    }
    
    // Verificar datos del usuario
    $usuario_data = [
        'id' => $row['Id_PvUser'],
        'nombre' => $row['Nombre_Apellidos'],
        'sucursal_id' => $row['Fk_Sucursal'],
        'sucursal_nombre' => $row['Nombre_Sucursal']
    ];
    
    // Verificar si las tablas existen
    $tables = ['pedidos', 'pedido_detalles', 'pedido_historial'];
    $existing_tables = [];
    
    foreach ($tables as $table) {
        $result = mysqli_query($conn, "SHOW TABLES LIKE '$table'");
        if (mysqli_num_rows($result) > 0) {
            $existing_tables[] = $table;
        }
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Conexión exitosa',
        'usuario' => $usuario_data,
        'tablas_existentes' => $existing_tables,
        'tablas_faltantes' => array_diff($tables, $existing_tables)
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?>
