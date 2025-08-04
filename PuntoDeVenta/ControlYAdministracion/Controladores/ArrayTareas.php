<?php
include_once "db_connect.php";
include_once "ControladorUsuario.php";
include_once "TareasController.php";

header('Content-Type: application/json');

$tareasController = new TareasController($conn, $userId);
$accion = $_POST['accion'] ?? '';

try {
    switch ($accion) {
        case 'listar':
            $filtros = [
                'estado' => $_POST['estado'] ?? '',
                'prioridad' => $_POST['prioridad'] ?? '',
                'asignado_a' => $_POST['asignado_a'] ?? ''
            ];
            
            $result = $tareasController->getTareas($filtros);
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
            $id = $_POST['id'] ?? 0;
            $tarea = $tareasController->getTarea($id);
            
            if ($tarea) {
                echo json_encode([
                    'success' => true,
                    'data' => $tarea
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Tarea no encontrada'
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
            $id = $_POST['id'] ?? 0;
            $nuevoEstado = $_POST['estado'] ?? '';
            
            if (empty($nuevoEstado)) {
                throw new Exception('Estado no válido');
            }
            
            $resultado = $tareasController->cambiarEstado($id, $nuevoEstado);
            
            if ($resultado) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Estado de la tarea actualizado exitosamente'
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Error al cambiar el estado o no tienes permisos'
                ]);
            }
            break;
            
        case 'eliminar':
            $id = $_POST['id'] ?? 0;
            
            $resultado = $tareasController->eliminarTarea($id);
            
            if ($resultado) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Tarea eliminada exitosamente'
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Error al eliminar la tarea o no tienes permisos'
                ]);
            }
            break;
            
        case 'estadisticas':
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
            
        case 'usuarios':
            $usuarios = $tareasController->getUsuariosDisponibles();
            $listaUsuarios = [];
            
            while ($row = $usuarios->fetch_assoc()) {
                $listaUsuarios[] = [
                    'id' => $row['Id_PvUser'],
                    'nombre' => $row['Nombre_Apellidos']
                ];
            }
            
            echo json_encode([
                'success' => true,
                'data' => $listaUsuarios
            ]);
            break;
            
        case 'proximas_vencer':
            $tareas = $tareasController->getTareasProximasVencer();
            $listaTareas = [];
            
            while ($row = $tareas->fetch_assoc()) {
                $listaTareas[] = [
                    'id' => $row['id'],
                    'titulo' => $row['titulo'],
                    'fecha_limite' => $row['fecha_limite'],
                    'prioridad' => $row['prioridad']
                ];
            }
            
            echo json_encode([
                'success' => true,
                'data' => $listaTareas
            ]);
            break;
            
        default:
            // Por defecto, devolver la lista de tareas
            $filtros = [
                'estado' => $_POST['estado'] ?? '',
                'prioridad' => $_POST['prioridad'] ?? '',
                'asignado_a' => $_POST['asignado_a'] ?? ''
            ];
            
            $result = $tareasController->getTareas($filtros);
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
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?> 