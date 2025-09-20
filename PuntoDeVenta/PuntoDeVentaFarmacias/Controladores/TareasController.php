<?php
class TareasController {
    private $conn;
    private $userId;
    private $sucursalId;
    
    public function __construct($conn, $userId, $sucursalId) {
        $this->conn = $conn;
        $this->userId = $userId;
        $this->sucursalId = $sucursalId;
    }
    
    /**
     * Obtener tareas asignadas al usuario actual de la sucursal actual
     */
    public function getTareasAsignadas($filtros = []) {
        $where = "WHERE (t.asignado_a = ? OR t.creado_por = ?)";
        $params = [$this->userId, $this->userId];
        $types = "ii";
        
        // Aplicar filtros
        if (!empty($filtros['estado'])) {
            $where .= " AND t.estado = ?";
            $params[] = $filtros['estado'];
            $types .= "s";
        }
        
        if (!empty($filtros['prioridad'])) {
            $where .= " AND t.prioridad = ?";
            $params[] = $filtros['prioridad'];
            $types .= "s";
        }
        
        // Filtro de fecha
        if (!empty($filtros['fecha'])) {
            switch ($filtros['fecha']) {
                case 'hoy':
                    $where .= " AND t.fecha_limite = CURDATE()";
                    break;
                case 'vencidas':
                    $where .= " AND t.fecha_limite < CURDATE() AND t.estado IN ('Por hacer', 'En progreso')";
                    break;
                case 'proximas':
                    $where .= " AND t.fecha_limite BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 3 DAY)";
                    break;
            }
        }
        
        $sql = "SELECT 
                    t.id,
                    t.titulo,
                    t.descripcion,
                    t.prioridad,
                    t.fecha_limite,
                    t.estado,
                    t.asignado_a,
                    t.creado_por,
                    t.fecha_creacion,
                    t.fecha_actualizacion,
                    u_asignado.Nombre_Apellidos as asignado_nombre,
                    u_creador.Nombre_Apellidos as creador_nombre
                FROM Tareas t
                LEFT JOIN Usuarios_PV u_asignado ON t.asignado_a = u_asignado.Id_PvUser
                LEFT JOIN Usuarios_PV u_creador ON t.creado_por = u_creador.Id_PvUser
                $where
                ORDER BY 
                    CASE t.prioridad 
                        WHEN 'Alta' THEN 1 
                        WHEN 'Media' THEN 2 
                        WHEN 'Baja' THEN 3 
                    END,
                    t.fecha_limite ASC,
                    t.fecha_creacion DESC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        
        return $stmt->get_result();
    }
    
    /**
     * Obtener tareas asignadas al usuario actual con filtro de sucursal opcional
     */
    public function getTareasAsignadasConSucursal($filtros = []) {
        $sql = "SELECT 
                    t.id,
                    t.titulo,
                    t.descripcion,
                    t.prioridad,
                    t.fecha_limite,
                    t.estado,
                    t.asignado_a,
                    t.creado_por,
                    t.fecha_creacion,
                    t.fecha_actualizacion,
                    u_asignado.Nombre_Apellidos as asignado_nombre,
                    u_creador.Nombre_Apellidos as creador_nombre
                FROM Tareas t
                LEFT JOIN Usuarios_PV u_asignado ON t.asignado_a = u_asignado.Id_PvUser
                LEFT JOIN Usuarios_PV u_creador ON t.creado_por = u_creador.Id_PvUser
                WHERE t.asignado_a = ?";
        
        $params = [$this->userId];
        $types = "i";
        
        // Filtro opcional de sucursal
        if (isset($filtros['filtrar_por_sucursal']) && $filtros['filtrar_por_sucursal']) {
            $sql .= " AND u_asignado.Fk_Sucursal = ?";
            $params[] = $this->sucursalId;
            $types .= "i";
        }
        
        // Aplicar otros filtros
        if (!empty($filtros['estado'])) {
            $sql .= " AND t.estado = ?";
            $params[] = $filtros['estado'];
            $types .= "s";
        }
        
        if (!empty($filtros['prioridad'])) {
            $sql .= " AND t.prioridad = ?";
            $params[] = $filtros['prioridad'];
            $types .= "s";
        }
        
        $sql .= " ORDER BY 
                    CASE t.prioridad 
                        WHEN 'Alta' THEN 1 
                        WHEN 'Media' THEN 2 
                        WHEN 'Baja' THEN 3 
                    END,
                    t.fecha_limite ASC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        
        return $stmt->get_result();
    }
    
    /**
     * Obtener estadísticas de tareas del usuario actual
     */
    public function getEstadisticas() {
        $sql = "SELECT 
                    t.estado,
                    COUNT(*) as cantidad
                FROM Tareas t
                WHERE (t.asignado_a = ? OR t.creado_por = ?)
                GROUP BY t.estado";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $this->userId, $this->userId);
        $stmt->execute();
        
        return $stmt->get_result();
    }
    
    /**
     * Obtener tareas próximas a vencer (próximos 3 días)
     */
    public function getTareasProximasVencer() {
        $sql = "SELECT 
                    t.id,
                    t.titulo,
                    t.fecha_limite,
                    t.prioridad,
                    t.estado
                FROM Tareas t
                WHERE (t.asignado_a = ? OR t.creado_por = ?)
                AND t.estado IN ('Por hacer', 'En progreso')
                AND t.fecha_limite IS NOT NULL
                AND t.fecha_limite BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 3 DAY)
                ORDER BY t.fecha_limite ASC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $this->userId, $this->userId);
        $stmt->execute();
        
        return $stmt->get_result();
    }
    
    /**
     * Cambiar estado de una tarea
     */
    public function cambiarEstado($tareaId, $nuevoEstado) {
        // Verificar que la tarea pertenece al usuario actual
        if (!$this->verificarPropiedadTarea($tareaId)) {
            return ['success' => false, 'message' => 'No tienes permisos para modificar esta tarea'];
        }
        
        $sql = "UPDATE Tareas 
                SET estado = ?, fecha_actualizacion = NOW() 
                WHERE id = ? AND (asignado_a = ? OR creado_por = ?)";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("siii", $nuevoEstado, $tareaId, $this->userId, $this->userId);
        
        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'Estado actualizado correctamente'];
        } else {
            return ['success' => false, 'message' => 'Error al actualizar el estado'];
        }
    }
    
    /**
     * Obtener una tarea específica
     */
    public function getTarea($tareaId) {
        $sql = "SELECT 
                    t.id,
                    t.titulo,
                    t.descripcion,
                    t.prioridad,
                    t.fecha_limite,
                    t.estado,
                    t.asignado_a,
                    t.creado_por,
                    t.fecha_creacion,
                    t.fecha_actualizacion,
                    u_asignado.Nombre_Apellidos as asignado_nombre,
                    u_creador.Nombre_Apellidos as creador_nombre
                FROM Tareas t
                LEFT JOIN Usuarios_PV u_asignado ON t.asignado_a = u_asignado.Id_PvUser
                LEFT JOIN Usuarios_PV u_creador ON t.creado_por = u_creador.Id_PvUser
                WHERE t.id = ? AND (t.asignado_a = ? OR t.creado_por = ?)";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("iii", $tareaId, $this->userId, $this->userId);
        $stmt->execute();
        
        return $stmt->get_result()->fetch_assoc();
    }
    
    /**
     * Verificar que una tarea pertenece al usuario actual
     */
    private function verificarPropiedadTarea($tareaId) {
        $sql = "SELECT COUNT(*) as count 
                FROM Tareas t
                WHERE t.id = ? AND (t.asignado_a = ? OR t.creado_por = ?)";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("iii", $tareaId, $this->userId, $this->userId);
        $stmt->execute();
        
        $result = $stmt->get_result()->fetch_assoc();
        return $result['count'] > 0;
    }
    
    /**
     * Obtener tareas vencidas del usuario actual
     */
    public function getTareasVencidas() {
        $sql = "SELECT 
                    t.id,
                    t.titulo,
                    t.fecha_limite,
                    t.prioridad,
                    t.estado,
                    DATEDIFF(CURDATE(), t.fecha_limite) as dias_vencida
                FROM Tareas t
                WHERE (t.asignado_a = ? OR t.creado_por = ?)
                AND t.estado IN ('Por hacer', 'En progreso')
                AND t.fecha_limite IS NOT NULL
                AND t.fecha_limite < CURDATE()
                ORDER BY t.fecha_limite ASC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $this->userId, $this->userId);
        $stmt->execute();
        
        return $stmt->get_result();
    }
    
    /**
     * Marcar tarea como completada
     */
    public function marcarCompletada($tareaId) {
        return $this->cambiarEstado($tareaId, 'Completada');
    }
    
    /**
     * Marcar tarea como en progreso
     */
    public function marcarEnProgreso($tareaId) {
        return $this->cambiarEstado($tareaId, 'En progreso');
    }
    
    /**
     * Obtener resumen de productividad del usuario
     */
    public function getResumenProductividad() {
        $sql = "SELECT 
                    COUNT(*) as total_tareas,
                    SUM(CASE WHEN estado = 'Completada' THEN 1 ELSE 0 END) as completadas,
                    SUM(CASE WHEN estado = 'En progreso' THEN 1 ELSE 0 END) as en_progreso,
                    SUM(CASE WHEN estado = 'Por hacer' THEN 1 ELSE 0 END) as por_hacer,
                    SUM(CASE WHEN estado = 'Cancelada' THEN 1 ELSE 0 END) as canceladas,
                    SUM(CASE WHEN fecha_limite < CURDATE() AND estado IN ('Por hacer', 'En progreso') THEN 1 ELSE 0 END) as vencidas
                FROM Tareas t
                WHERE (t.asignado_a = ? OR t.creado_por = ?)";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $this->userId, $this->userId);
        $stmt->execute();
        
        return $stmt->get_result()->fetch_assoc();
    }
    
    /**
     * Obtener todas las tareas (para administradores o vista general)
     */
    public function getTareas($filtros = []) {
        return $this->getTareasAsignadas($filtros);
    }
    
    /**
     * Crear una nueva tarea
     */
    public function crearTarea($datos) {
        $sql = "INSERT INTO Tareas (titulo, descripcion, prioridad, fecha_limite, estado, asignado_a, creado_por, fecha_creacion) 
                VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("sssssss", 
            $datos['titulo'],
            $datos['descripcion'],
            $datos['prioridad'],
            $datos['fecha_limite'],
            $datos['estado'],
            $datos['asignado_a'],
            $this->userId
        );
        
        if ($stmt->execute()) {
            return $this->conn->insert_id;
        }
        
        return false;
    }
    
    /**
     * Actualizar una tarea existente
     */
    public function actualizarTarea($id, $datos) {
        // Verificar que la tarea pertenece al usuario actual
        if (!$this->verificarPropiedadTarea($id)) {
            return false;
        }
        
        $sql = "UPDATE Tareas SET 
                    titulo = ?, 
                    descripcion = ?, 
                    prioridad = ?, 
                    fecha_limite = ?, 
                    estado = ?, 
                    asignado_a = ?,
                    fecha_actualizacion = NOW()
                WHERE id = ? AND asignado_a = ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ssssssii", 
            $datos['titulo'],
            $datos['descripcion'],
            $datos['prioridad'],
            $datos['fecha_limite'],
            $datos['estado'],
            $datos['asignado_a'],
            $id,
            $this->userId
        );
        
        return $stmt->execute();
    }
    
    /**
     * Eliminar una tarea
     */
    public function eliminarTarea($id) {
        // Verificar que la tarea pertenece al usuario actual
        if (!$this->verificarPropiedadTarea($id)) {
            return ['success' => false, 'message' => 'No tienes permisos para eliminar esta tarea'];
        }
        
        $sql = "DELETE FROM Tareas WHERE id = ? AND (asignado_a = ? OR creado_por = ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("iii", $id, $this->userId, $this->userId);
        
        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'Tarea eliminada correctamente'];
        } else {
            return ['success' => false, 'message' => 'Error al eliminar la tarea'];
        }
    }
    
    /**
     * Obtener usuarios disponibles para asignar tareas
     */
    public function getUsuariosDisponibles() {
        $sql = "SELECT Id_PvUser, Nombre_Apellidos 
                FROM Usuarios_PV 
                WHERE Estatus = 'Activo' AND Fk_Sucursal = ?
                ORDER BY Nombre_Apellidos";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $this->sucursalId);
        $stmt->execute();
        
        return $stmt->get_result();
    }
}
?>
