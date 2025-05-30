<?php
session_start();
include_once "../Conexion.php";

// Verificar si es una petición POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario'])) {
    echo json_encode(['success' => false, 'message' => 'Sesión no iniciada']);
    exit;
}

// Obtener y validar datos
$codigos = $_POST['CodBarra'] ?? [];
$nombres = $_POST['NombreProd'] ?? [];
$existenciasR = $_POST['Existencias_R'] ?? [];
$stockFisico = $_POST['StockFisico'] ?? [];
$sucursal = $_POST['Sucursal'][0] ?? null;
$agregadoPor = $_POST['Agrego'][0] ?? null;
$enPausa = isset($_POST['EnPausa']) ? (int)$_POST['EnPausa'] : 0;

// Validar que todos los arrays tengan la misma longitud
if (count($codigos) !== count($nombres) || 
    count($codigos) !== count($existenciasR) || 
    count($codigos) !== count($stockFisico)) {
    echo json_encode(['success' => false, 'message' => 'Datos incompletos o corruptos']);
    exit;
}

// Validar datos de sucursal y usuario
if (!$sucursal || !$agregadoPor) {
    echo json_encode(['success' => false, 'message' => 'Faltan datos de sucursal o usuario']);
    exit;
}

try {
    // Iniciar transacción
    $conn->begin_transaction();

    // Preparar la consulta
    $stmt = $conn->prepare("
        INSERT INTO ConteosDiarios (
            Cod_Barra, 
            Nombre_Producto, 
            Fk_sucursal, 
            Existencias_R, 
            ExistenciaFisica, 
            AgregadoPor, 
            AgregadoEl, 
            EnPausa
        ) VALUES (?, ?, ?, ?, ?, ?, NOW(), ?)
    ");

    // Insertar cada registro
    for ($i = 0; $i < count($codigos); $i++) {
        $stmt->bind_param(
            "sssdisi",
            $codigos[$i],
            $nombres[$i],
            $sucursal,
            $existenciasR[$i],
            $stockFisico[$i],
            $agregadoPor,
            $enPausa
        );

        if (!$stmt->execute()) {
            throw new Exception("Error al guardar el registro: " . $stmt->error);
        }
    }

    // Confirmar transacción
    $conn->commit();

    // Devolver respuesta exitosa
    echo json_encode([
        'success' => true,
        'message' => $enPausa ? 
            'Conteo guardado y pausado correctamente' : 
            'Conteo guardado correctamente',
        'registros' => count($codigos)
    ]);

} catch (Exception $e) {
    // Revertir transacción en caso de error
    $conn->rollback();
    
    // Registrar el error en el log
    error_log("Error en GuardarConteo.php: " . $e->getMessage());
    
    // Devolver respuesta de error
    echo json_encode([
        'success' => false,
        'message' => 'Error al guardar el conteo: ' . $e->getMessage()
    ]);
}

// Cerrar conexión
$stmt->close();
$conn->close();
?> 