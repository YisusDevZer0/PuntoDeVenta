<?php
include_once "db_connect.php";
include_once "ControladorUsuario.php";

class TareasController {
    private $conn;
    private $userId;
    private $userRole;
    
    public function __construct($conn, $userId) {
        $this->conn = $conn;
        $this->userId = $userId;
        $this->userRole = $this->getUserRole();
    }
    
    private function getUserRole() {
        $sql = "SELECT Tipos_Usuarios.TipoUsuario 
                FROM Usuarios_PV 
                INNER JOIN Tipos_Usuarios ON Usuarios_PV.Fk_Usuario = Tipos_Usuarios.ID_User 
                WHERE Usuarios_PV.Id_PvUser = ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $this->userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        return $row ? $row['TipoUsuario'] : 'Usuario';
    }
    
    public function getTareas($filtros = []) {
        $where = "WHERE 1=1";
        $params = [];
        $types = "";
        
        // Filtros de permisos
        if ($this->userRole !== 'Administrador') {
            $where .= " AND (asignado_a = ? OR creado_por = ?)";
            $params[] = $this->userId;
            $params[] = $this->userId;
            $types .= "ss";
        }
        
        // Filtros adicionales
        if (!empty($filtros['estado'])) {
            $where .= " AND estado = ?";
            $params[] = $filtros['estado'];
            $types .= "s";
        }
        
        if (!empty($filtros['prioridad'])) {
            $where .= " AND prioridad = ?";
            $params[] = $filtros['prioridad'];
            $types .= "s";
        }
        
        if (!empty($filtros['asignado_a'])) {
            $where .= " AND asignado_a = ?";
            $params[] = $filtros['asignado_a'];
            $types .= "s";
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
                    asignado.Nombre_Apellidos as asignado_nombre,
                    creador.Nombre_Apellidos as creador_nombre
                FROM Tareas t
                LEFT JOIN Usuarios_PV asignado ON t.asignado_a = asignado.Id_PvUser
                LEFT JOIN Usuarios_PV creador ON t.creado_por = creador.Id_PvUser
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
        
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        
        $stmt->execute();
        return $stmt->get_result();
    }
    
    public function getTarea($id) {
        $sql = "SELECT 
                    t.*,
                    asignado.Nombre_Apellidos as asignado_nombre,
                    creador.Nombre_Apellidos as creador_nombre
                FROM Tareas t
                LEFT JOIN Usuarios_PV asignado ON t.asignado_a = asignado.Id_PvUser
                LEFT JOIN Usuarios_PV creador ON t.creado_por = creador.Id_PvUser
                WHERE t.id = ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_assoc();
    }
    
    public function crearTarea($datos) {
        $sql = "INSERT INTO Tareas (titulo, descripcion, prioridad, fecha_limite, estado, asignado_a, creado_por) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        
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
            $tareaId = $this->conn->insert_id;
            $this->enviarNotificacionAsignacion($datos['asignado_a'], $datos['titulo']);
            return $tareaId;
        }
        
        return false;
    }
    
    public function actualizarTarea($id, $datos) {
        // Verificar permisos
        $tarea = $this->getTarea($id);
        if (!$tarea || ($tarea['creado_por'] != $this->userId && $tarea['asignado_a'] != $this->userId && $this->userRole !== 'Administrador')) {
            return false;
        }
        
        $sql = "UPDATE Tareas SET 
                    titulo = ?, 
                    descripcion = ?, 
                    prioridad = ?, 
                    fecha_limite = ?, 
                    estado = ?, 
                    asignado_a = ?
                WHERE id = ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ssssssi", 
            $datos['titulo'],
            $datos['descripcion'],
            $datos['prioridad'],
            $datos['fecha_limite'],
            $datos['estado'],
            $datos['asignado_a'],
            $id
        );
        
        return $stmt->execute();
    }
    
    public function cambiarEstado($id, $nuevoEstado) {
        // Verificar permisos
        $tarea = $this->getTarea($id);
        if (!$tarea || ($tarea['asignado_a'] != $this->userId && $this->userRole !== 'Administrador')) {
            return false;
        }
        
        $sql = "UPDATE Tareas SET estado = ? WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("si", $nuevoEstado, $id);
        
        return $stmt->execute();
    }
    
    public function eliminarTarea($id) {
        // Solo el creador o administrador puede eliminar
        $tarea = $this->getTarea($id);
        if (!$tarea || ($tarea['creado_por'] != $this->userId && $this->userRole !== 'Administrador')) {
            return false;
        }
        
        $sql = "DELETE FROM Tareas WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        
        return $stmt->execute();
    }
    
    public function getUsuariosDisponibles() {
        $sql = "SELECT Id_PvUser, Nombre_Apellidos 
                FROM Usuarios_PV 
                WHERE Estatus = 'Activo'
                ORDER BY Nombre_Apellidos";
        
        $result = $this->conn->query($sql);
        return $result;
    }
    
    public function getEstadisticas() {
        $where = "WHERE 1=1";
        $params = [];
        $types = "";
        
        if ($this->userRole !== 'Administrador') {
            $where .= " AND (asignado_a = ? OR creado_por = ?)";
            $params[] = $this->userId;
            $params[] = $this->userId;
            $types .= "ss";
        }
        
        $sql = "SELECT 
                    estado,
                    COUNT(*) as cantidad
                FROM Tareas 
                $where
                GROUP BY estado";
        
        $stmt = $this->conn->prepare($sql);
        
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        
        $stmt->execute();
        return $stmt->get_result();
    }
    
    private function enviarNotificacionAsignacion($usuarioId, $tituloTarea) {
        // Aquí se implementaría la lógica de notificaciones
        // Por ahora solo registramos en el log
        error_log("Nueva tarea asignada: Usuario $usuarioId, Tarea: $tituloTarea");
    }
    
    public function getTareasProximasVencer() {
        $where = "WHERE fecha_limite BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 3 DAY)";
        
        if ($this->userRole !== 'Administrador') {
            $where .= " AND (asignado_a = ? OR creado_por = ?)";
        }
        
        $sql = "SELECT * FROM Tareas $where ORDER BY fecha_limite ASC";
        
        $stmt = $this->conn->prepare($sql);
        
        if ($this->userRole !== 'Administrador') {
            $stmt->bind_param("ss", $this->userId, $this->userId);
        }
        
        $stmt->execute();
        return $stmt->get_result();
    }
}
?> 