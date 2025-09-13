<?php
include_once "db_connect.php";

class RecordatoriosController {
    private $conn;
    
    public function __construct($connection) {
        $this->conn = $connection;
    }
    
    // Crear tabla de recordatorios si no existe
    public function crearTablaRecordatorios() {
        $sql = "CREATE TABLE IF NOT EXISTS recordatorios_limpieza (
            id_recordatorio INT AUTO_INCREMENT PRIMARY KEY,
            titulo VARCHAR(255) NOT NULL,
            descripcion TEXT,
            fecha_recordatorio DATETIME NOT NULL,
            prioridad ENUM('baja', 'media', 'alta') DEFAULT 'media',
            estado ENUM('activo', 'completado', 'cancelado') DEFAULT 'activo',
            id_usuario INT,
            fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )";
        
        return mysqli_query($this->conn, $sql);
    }
    
    // Crear recordatorio
    public function crearRecordatorio($titulo, $descripcion, $fecha_recordatorio, $prioridad, $id_usuario) {
        $this->crearTablaRecordatorios(); // Asegurar que la tabla existe
        
        $sql = "INSERT INTO recordatorios_limpieza (titulo, descripcion, fecha_recordatorio, prioridad, id_usuario) 
                VALUES (?, ?, ?, ?, ?)";
        
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "ssssi", $titulo, $descripcion, $fecha_recordatorio, $prioridad, $id_usuario);
        
        return mysqli_stmt_execute($stmt);
    }
    
    // Obtener recordatorios
    public function obtenerRecordatorios($estado = 'activo') {
        $sql = "SELECT * FROM recordatorios_limpieza 
                WHERE estado = ? 
                ORDER BY fecha_recordatorio ASC";
        
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $estado);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        $recordatorios = [];
        while($row = mysqli_fetch_assoc($result)) {
            $recordatorios[] = $row;
        }
        
        return $recordatorios;
    }
    
    // Actualizar estado del recordatorio
    public function actualizarEstadoRecordatorio($id_recordatorio, $estado) {
        $sql = "UPDATE recordatorios_limpieza SET estado = ? WHERE id_recordatorio = ?";
        
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "si", $estado, $id_recordatorio);
        
        return mysqli_stmt_execute($stmt);
    }
    
    // Eliminar recordatorio
    public function eliminarRecordatorio($id_recordatorio) {
        $sql = "DELETE FROM recordatorios_limpieza WHERE id_recordatorio = ?";
        
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $id_recordatorio);
        
        return mysqli_stmt_execute($stmt);
    }
    
    // Obtener recordatorios próximos (próximas 24 horas)
    public function obtenerRecordatoriosProximos() {
        $sql = "SELECT * FROM recordatorios_limpieza 
                WHERE estado = 'activo' 
                AND fecha_recordatorio BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL 24 HOUR)
                ORDER BY fecha_recordatorio ASC";
        
        $result = mysqli_query($this->conn, $sql);
        $recordatorios = [];
        
        while($row = mysqli_fetch_assoc($result)) {
            $recordatorios[] = $row;
        }
        
        return $recordatorios;
    }
}
?>