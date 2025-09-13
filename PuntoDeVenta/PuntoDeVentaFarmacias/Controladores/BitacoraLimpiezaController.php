<?php
include_once "db_connect.php";

class BitacoraLimpiezaController {
    private $conn;
    
    public function __construct($connection) {
        $this->conn = $connection;
    }
    
    // Obtener todas las bitácoras de limpieza
    public function obtenerBitacoras() {
        $sql = "SELECT 
                    bl.id_bitacora,
                    bl.area,
                    bl.semana,
                    bl.fecha_inicio,
                    bl.fecha_fin,
                    bl.responsable,
                    bl.supervisor,
                    bl.aux_res,
                    bl.firma_responsable,
                    bl.firma_supervisor,
                    bl.firma_aux_res
                FROM Bitacora_Limpieza bl 
                ORDER BY bl.fecha_inicio DESC";
        
        $result = mysqli_query($this->conn, $sql);
        $bitacoras = [];
        
        while($row = mysqli_fetch_assoc($result)) {
            $bitacoras[] = $row;
        }
        
        return $bitacoras;
    }
    
    // Obtener detalles de limpieza por bitácora
    public function obtenerDetallesLimpieza($id_bitacora) {
        $sql = "SELECT 
                    dl.id_detalle,
                    dl.elemento,
                    dl.lunes_mat,
                    dl.lunes_vesp,
                    dl.martes_mat,
                    dl.martes_vesp,
                    dl.miercoles_mat,
                    dl.miercoles_vesp,
                    dl.jueves_mat,
                    dl.jueves_vesp,
                    dl.viernes_mat,
                    dl.viernes_vesp,
                    dl.sabado_mat,
                    dl.sabado_vesp,
                    dl.domingo_mat,
                    dl.domingo_vesp
                FROM Detalle_Limpieza dl 
                WHERE dl.id_bitacora = ? 
                ORDER BY dl.elemento";
        
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $id_bitacora);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        $detalles = [];
        while($row = mysqli_fetch_assoc($result)) {
            $detalles[] = $row;
        }
        
        return $detalles;
    }
    
    // Crear nueva bitácora de limpieza
    public function crearBitacora($area, $semana, $fecha_inicio, $fecha_fin, $responsable, $supervisor, $aux_res) {
        $sql = "INSERT INTO Bitacora_Limpieza (area, semana, fecha_inicio, fecha_fin, responsable, supervisor, aux_res) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "sssssss", $area, $semana, $fecha_inicio, $fecha_fin, $responsable, $supervisor, $aux_res);
        
        if(mysqli_stmt_execute($stmt)) {
            return mysqli_insert_id($this->conn);
        }
        return false;
    }
    
    // Agregar elemento de limpieza
    public function agregarElementoLimpieza($id_bitacora, $elemento) {
        $sql = "INSERT INTO Detalle_Limpieza (id_bitacora, elemento) VALUES (?, ?)";
        
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "is", $id_bitacora, $elemento);
        
        return mysqli_stmt_execute($stmt);
    }
    
    // Actualizar estado de limpieza
    public function actualizarEstadoLimpieza($id_detalle, $campo, $valor) {
        $sql = "UPDATE Detalle_Limpieza SET $campo = ? WHERE id_detalle = ?";
        
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "ii", $valor, $id_detalle);
        
        return mysqli_stmt_execute($stmt);
    }
    
    // Eliminar bitácora
    public function eliminarBitacora($id_bitacora) {
        // Primero eliminar detalles
        $sql1 = "DELETE FROM Detalle_Limpieza WHERE id_bitacora = ?";
        $stmt1 = mysqli_prepare($this->conn, $sql1);
        mysqli_stmt_bind_param($stmt1, "i", $id_bitacora);
        mysqli_stmt_execute($stmt1);
        
        // Luego eliminar bitácora
        $sql2 = "DELETE FROM Bitacora_Limpieza WHERE id_bitacora = ?";
        $stmt2 = mysqli_prepare($this->conn, $sql2);
        mysqli_stmt_bind_param($stmt2, "i", $id_bitacora);
        
        return mysqli_stmt_execute($stmt2);
    }
    
    // Obtener bitácora por ID
    public function obtenerBitacoraPorId($id_bitacora) {
        $sql = "SELECT * FROM Bitacora_Limpieza WHERE id_bitacora = ?";
        
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $id_bitacora);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        return mysqli_fetch_assoc($result);
    }
    
    // Actualizar bitácora
    public function actualizarBitacora($id_bitacora, $area, $semana, $fecha_inicio, $fecha_fin, $responsable, $supervisor, $aux_res) {
        $sql = "UPDATE Bitacora_Limpieza SET 
                    area = ?, 
                    semana = ?, 
                    fecha_inicio = ?, 
                    fecha_fin = ?, 
                    responsable = ?, 
                    supervisor = ?, 
                    aux_res = ? 
                WHERE id_bitacora = ?";
        
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "sssssssi", $area, $semana, $fecha_inicio, $fecha_fin, $responsable, $supervisor, $aux_res, $id_bitacora);
        
        return mysqli_stmt_execute($stmt);
    }
}
?>
