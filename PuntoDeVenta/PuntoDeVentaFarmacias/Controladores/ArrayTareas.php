<?php
header('Content-Type: application/json');
include_once "db_connect.php";
include_once "ControladorUsuario.php";
include_once "TareasController.php";

// Verificar que las variables del usuario estén disponibles
if (!isset($row) || !isset($row['Id_PvUser']) || !isset($row['Fk_Sucursal'])) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error: Datos de usuario no disponibles. Debe iniciar sesión.',
        'debug' => [
            'row_isset' => isset($row),
            'user_id_isset' => isset($row['Id_PvUser']),
            'sucursal_id_isset' => isset($row['Fk_Sucursal'])
        ]
    ]);
    exit;
}

// Obtener datos del usuario actual
$userId = $row['Id_PvUser'];
$sucursalId = $row['Fk_Sucursal'];

// Debug: Verificar datos del usuario
error_log("Usuario ID: " . $userId . ", Sucursal ID: " . $sucursalId);

$tareasController = new TareasController($conn, $userId, $sucursalId);

$accion = $_POST['accion'] ?? '';

try {
    switch ($accion) {
        case 'listar':
            $filtros = [
                'estado' => $_POST['estado'] ?? '',
                'prioridad' => $_POST['prioridad'] ?? '',
                'fecha' => $_POST['fecha'] ?? ''
            ];
            
            $result = $tareasController->getTareasAsignadas($filtros);
            $tareas = [];
            
            while ($row = $result->fetch_assoc()) {
                $tareas[] = [
                    'id' => $row['id'],
                    'titulo' => $row['titulo'],
                    'descripcion' => $row['descripcion'],
                    'prioridad' => $row['prioridad'],
                    'fecha_limite' => $row['fecha_limite'],
                    'estado' => $row['estado'],
                    'asignado_a' => $row['asignado_a'],
                    'asignado_nombre' => $row['asignado_nombre'],
                    'creado_por' => $row['creado_por'],
                    'creador_nombre' => $row['creador_nombre'],
                    'fecha_creacion' => $row['fecha_creacion'],
                    'fecha_actualizacion' => $row['fecha_actualizacion']
                ];
            }
            
            echo json_encode([
                'success' => true,
                'data' => $tareas
            ]);
            break;
            
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
            
        case 'crear':
            $datos = [
                'titulo' => $_POST['titulo'] ?? '',
                'descripcion' => $_POST['descripcion'] ?? '',
                'prioridad' => $_POST['prioridad'] ?? 'Media',
                'fecha_limite' => $_POST['fecha_limite'] ?? null,
                'estado' => $_POST['estado'] ?? 'Por hacer',
                'asignado_a' => $_POST['asignado_a'] ?? ''
            ];
            
            // Validaciones
            if (empty($datos['titulo'])) {
                throw new Exception('El título es obligatorio');
            }
            
            if (empty($datos['asignado_a'])) {
                throw new Exception('Debe asignar la tarea a un usuario');
            }
            
            $tareaId = $tareasController->crearTarea($datos);
            
            if ($tareaId) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Tarea creada exitosamente',
                    'id' => $tareaId
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Error al crear la tarea'
                ]);
            }
            break;
            
        case 'actualizar':
            $id = $_POST['id'] ?? 0;
            $datos = [
                'titulo' => $_POST['titulo'] ?? '',
                'descripcion' => $_POST['descripcion'] ?? '',
                'prioridad' => $_POST['prioridad'] ?? 'Media',
                'fecha_limite' => $_POST['fecha_limite'] ?? null,
                'estado' => $_POST['estado'] ?? 'Por hacer',
                'asignado_a' => $_POST['asignado_a'] ?? ''
            ];
            
            // Validaciones
            if (empty($datos['titulo'])) {
                throw new Exception('El título es obligatorio');
            }
            
            if (empty($datos['asignado_a'])) {
                throw new Exception('Debe asignar la tarea a un usuario');
            }
            
            $resultado = $tareasController->actualizarTarea($id, $datos);
            
            if ($resultado) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Tarea actualizada exitosamente'
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Error al actualizar la tarea o no tienes permisos'
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
            
        case 'eliminar':
            $tareaId = $_POST['id'] ?? 0;
            
            if (empty($tareaId)) {
                echo json_encode([
                    'success' => false,
                    'message' => 'ID de tarea es requerido'
                ]);
                break;
            }
            
            $resultado = $tareasController->eliminarTarea($tareaId);
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
                'fecha' => $_POST['fecha'] ?? ''
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
