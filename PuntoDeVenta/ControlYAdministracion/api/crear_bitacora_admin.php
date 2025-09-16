<?php
include_once "../Controladores/ControladorUsuario.php";
include_once "../Controladores/BitacoraLimpiezaAdminControllerSimple.php";

// Verificar sesión administrativa
if(!isset($_SESSION['ControlMaestro']) && !isset($_SESSION['AdministradorRH']) && !isset($_SESSION['Marketing'])){
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Acceso denegado']);
    exit;
}

$controller = new BitacoraLimpiezaAdminControllerSimple($conn);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Validar datos recibidos
        $datos = [
            'sucursal_id' => $_POST['sucursal_id'] ?? null,
            'area' => trim($_POST['area'] ?? ''),
            'semana' => trim($_POST['semana'] ?? ''),
            'fecha_inicio' => $_POST['fecha_inicio'] ?? null,
            'fecha_fin' => $_POST['fecha_fin'] ?? null,
            'responsable' => trim($_POST['responsable'] ?? ''),
            'supervisor' => trim($_POST['supervisor'] ?? ''),
            'aux_res' => trim($_POST['aux_res'] ?? '')
        ];
        
        // Validar campos requeridos
        if (empty($datos['sucursal_id']) || empty($datos['area']) || empty($datos['semana']) || 
            empty($datos['fecha_inicio']) || empty($datos['fecha_fin']) || 
            empty($datos['responsable']) || empty($datos['supervisor'])) {
            throw new Exception('Todos los campos obligatorios deben ser completados');
        }
        
        // Validar fechas
        $fechaInicio = new DateTime($datos['fecha_inicio']);
        $fechaFin = new DateTime($datos['fecha_fin']);
        
        if ($fechaFin <= $fechaInicio) {
            throw new Exception('La fecha fin debe ser posterior a la fecha inicio');
        }
        
        // Validar que la sucursal existe
        $sucursales = $controller->obtenerSucursales();
        $sucursalExiste = false;
        foreach($sucursales as $sucursal) {
            if ($sucursal['Id_Sucursal'] == $datos['sucursal_id']) {
                $sucursalExiste = true;
                break;
            }
        }
        
        if (!$sucursalExiste) {
            throw new Exception('La sucursal seleccionada no existe');
        }
        
        // Crear la bitácora
        $id_bitacora = $controller->crearBitacoraAdmin($datos);
        
        if ($id_bitacora) {
            echo json_encode([
                'success' => true,
                'message' => 'Bitácora creada exitosamente',
                'id_bitacora' => $id_bitacora
            ]);
        } else {
            throw new Exception('Error al crear la bitácora');
        }
        
    } catch (Exception $e) {
        error_log("Error creando bitácora: " . $e->getMessage());
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
} else {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
}
?>
