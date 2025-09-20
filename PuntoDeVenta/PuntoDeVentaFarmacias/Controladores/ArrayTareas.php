<?php
header('Content-Type: application/json');
include_once "db_connect.php";
include_once "ControladorUsuario.php";
include_once "TareasController.php";

// Obtener datos del usuario actual
$userId = $row['Id_PvUser'];
$sucursalId = $row['Fk_Sucursal'];

// Debug: Verificar datos del usuario
error_log("Usuario ID: " . $userId . ", Sucursal ID: " . $sucursalId);

$tareasController = new TareasController($conn, $userId, $sucursalId);

$accion = $_POST['accion'] ?? '';

try {
    switch ($accion) {
        case 'obtener':
            $tareaId = $_POST['id'] ?? 0;
            $tarea = $tareasController->getTarea($tareaId);
            
            if ($tarea) {
                echo json_encode([
                    'success' => true,
                    'data' => $tarea
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Tarea no encontrada o no tienes permisos para verla'
                ]);
            }
            break;
            
        case 'cambiar_estado':
            $tareaId = $_POST['id'] ?? 0;
            $nuevoEstado = $_POST['estado'] ?? '';
            
            if (empty($tareaId) || empty($nuevoEstado)) {
                echo json_encode([
                    'success' => false,
                    'message' => 'ID de tarea y estado son requeridos'
                ]);
                break;
            }
            
            $resultado = $tareasController->cambiarEstado($tareaId, $nuevoEstado);
            echo json_encode($resultado);
            break;
            
        case 'marcar_completada':
            $tareaId = $_POST['id'] ?? 0;
            
            if (empty($tareaId)) {
                echo json_encode([
                    'success' => false,
                    'message' => 'ID de tarea es requerido'
                ]);
                break;
            }
            
            $resultado = $tareasController->marcarCompletada($tareaId);
            echo json_encode($resultado);
            break;
            
        case 'marcar_en_progreso':
            $tareaId = $_POST['id'] ?? 0;
            
            if (empty($tareaId)) {
                echo json_encode([
                    'success' => false,
                    'message' => 'ID de tarea es requerido'
                ]);
                break;
            }
            
            $resultado = $tareasController->marcarEnProgreso($tareaId);
            echo json_encode($resultado);
            break;
            
        case 'obtener_estadisticas':
            $estadisticas = $tareasController->getEstadisticas();
            $stats = [];
            while ($row = $estadisticas->fetch_assoc()) {
                $stats[$row['estado']] = $row['cantidad'];
            }
            
            echo json_encode([
                'success' => true,
                'data' => $stats
            ]);
            break;
            
        case 'obtener_proximas_vencer':
            $proximas = $tareasController->getTareasProximasVencer();
            $tareas = [];
            while ($row = $proximas->fetch_assoc()) {
                $tareas[] = $row;
            }
            
            echo json_encode([
                'success' => true,
                'data' => $tareas
            ]);
            break;
            
        case 'obtener_vencidas':
            $vencidas = $tareasController->getTareasVencidas();
            $tareas = [];
            while ($row = $vencidas->fetch_assoc()) {
                $tareas[] = $row;
            }
            
            echo json_encode([
                'success' => true,
                'data' => $tareas
            ]);
            break;
            
        case 'obtener_resumen':
            $resumen = $tareasController->getResumenProductividad();
            echo json_encode([
                'success' => true,
                'data' => $resumen
            ]);
            break;
            
        default:
            // Obtener todas las tareas con filtros
            $filtros = [
                'estado' => $_POST['estado'] ?? '',
                'prioridad' => $_POST['prioridad'] ?? '',
                'asignado_a' => $_POST['asignado_a'] ?? ''
            ];
            
            error_log("Filtros aplicados: " . json_encode($filtros));
            
            $tareas = $tareasController->getTareasAsignadas($filtros);
            $data = [];
            
            if ($tareas) {
                while ($row = $tareas->fetch_assoc()) {
                    $data[] = $row;
                }
                error_log("Tareas encontradas: " . count($data));
            } else {
                error_log("Error en la consulta de tareas");
            }
            
            echo json_encode([
                'success' => true,
                'data' => $data,
                'debug' => [
                    'userId' => $userId,
                    'sucursalId' => $sucursalId,
                    'filtros' => $filtros,
                    'totalTareas' => count($data)
                ]
            ]);
            break;
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?>
