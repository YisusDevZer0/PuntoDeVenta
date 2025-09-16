<?php
include_once "db_connect.php";

class BitacoraLimpiezaAdminController {
    private $conn;
    
    public function __construct($connection) {
        $this->conn = $connection;
    }
    
    // Obtener todas las bitácoras con información de sucursales
    public function obtenerBitacorasAdmin($filtros = []) {
        $where = "1=1";
        $params = [];
        $types = "";
        
        // Filtro por sucursal
        if (!empty($filtros['sucursal'])) {
            $where .= " AND bl.sucursal = ?";
            $params[] = $filtros['sucursal'];
            $types .= "s";
        }
        
        // Filtro por área
        if (!empty($filtros['area'])) {
            $where .= " AND bl.area = ?";
            $params[] = $filtros['area'];
            $types .= "s";
        }
        
        // Filtro por fecha inicio
        if (!empty($filtros['fecha_inicio'])) {
            $where .= " AND bl.fecha_inicio >= ?";
            $params[] = $filtros['fecha_inicio'];
            $types .= "s";
        }
        
        // Filtro por fecha fin
        if (!empty($filtros['fecha_fin'])) {
            $where .= " AND bl.fecha_fin <= ?";
            $params[] = $filtros['fecha_fin'];
            $types .= "s";
        }
        
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
                    bl.firma_aux_res,
                    'N/A' as sucursal,
                    'Todas las Sucursales' as Nombre_Sucursal,
                    NOW() as created_at,
                    NOW() as updated_at,
                    COUNT(dl.id_detalle) as total_elementos,
                    SUM(CASE 
                        WHEN dl.lunes_mat = 1 THEN 1 ELSE 0 END +
                        CASE WHEN dl.lunes_vesp = 1 THEN 1 ELSE 0 END +
                        CASE WHEN dl.martes_mat = 1 THEN 1 ELSE 0 END +
                        CASE WHEN dl.martes_vesp = 1 THEN 1 ELSE 0 END +
                        CASE WHEN dl.miercoles_mat = 1 THEN 1 ELSE 0 END +
                        CASE WHEN dl.miercoles_vesp = 1 THEN 1 ELSE 0 END +
                        CASE WHEN dl.jueves_mat = 1 THEN 1 ELSE 0 END +
                        CASE WHEN dl.jueves_vesp = 1 THEN 1 ELSE 0 END +
                        CASE WHEN dl.viernes_mat = 1 THEN 1 ELSE 0 END +
                        CASE WHEN dl.viernes_vesp = 1 THEN 1 ELSE 0 END +
                        CASE WHEN dl.sabado_mat = 1 THEN 1 ELSE 0 END +
                        CASE WHEN dl.sabado_vesp = 1 THEN 1 ELSE 0 END +
                        CASE WHEN dl.domingo_mat = 1 THEN 1 ELSE 0 END +
                        CASE WHEN dl.domingo_vesp = 1 THEN 1 ELSE 0 END
                    ) as tareas_completadas,
                    (COUNT(dl.id_detalle) * 14) as total_tareas_posibles
                FROM Bitacora_Limpieza bl 
                LEFT JOIN Detalle_Limpieza dl ON bl.id_bitacora = dl.id_bitacora
                WHERE $where
                GROUP BY bl.id_bitacora, bl.area, bl.semana, bl.fecha_inicio, bl.fecha_fin, bl.responsable, bl.supervisor, bl.aux_res, bl.firma_responsable, bl.firma_supervisor, bl.firma_aux_res
                ORDER BY bl.fecha_inicio DESC";
        
        $stmt = mysqli_prepare($this->conn, $sql);
        if (!empty($params)) {
            mysqli_stmt_bind_param($stmt, $types, ...$params);
        }
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        $bitacoras = [];
        while($row = mysqli_fetch_assoc($result)) {
            $row['porcentaje_cumplimiento'] = $row['total_tareas_posibles'] > 0 
                ? round(($row['tareas_completadas'] / $row['total_tareas_posibles']) * 100, 2) 
                : 0;
            $bitacoras[] = $row;
        }
        
        return $bitacoras;
    }
    
    // Obtener estadísticas generales
    public function obtenerEstadisticasGenerales($filtros = []) {
        $where = "1=1";
        $params = [];
        $types = "";
        
        if (!empty($filtros['sucursal'])) {
            $where .= " AND bl.sucursal = ?";
            $params[] = $filtros['sucursal'];
            $types .= "s";
        }
        
        if (!empty($filtros['fecha_inicio'])) {
            $where .= " AND bl.fecha_inicio >= ?";
            $params[] = $filtros['fecha_inicio'];
            $types .= "s";
        }
        
        if (!empty($filtros['fecha_fin'])) {
            $where .= " AND bl.fecha_fin <= ?";
            $params[] = $filtros['fecha_fin'];
            $types .= "s";
        }
        
        $sql = "SELECT 
                    COUNT(DISTINCT bl.id_bitacora) as total_bitacoras,
                    COUNT(DISTINCT bl.sucursal) as total_sucursales,
                    COUNT(DISTINCT bl.area) as total_areas,
                    AVG(
                        CASE 
                            WHEN (COUNT(dl.id_detalle) * 14) > 0 
                            THEN (SUM(CASE 
                                WHEN dl.lunes_mat = 1 THEN 1 ELSE 0 END +
                                CASE WHEN dl.lunes_vesp = 1 THEN 1 ELSE 0 END +
                                CASE WHEN dl.martes_mat = 1 THEN 1 ELSE 0 END +
                                CASE WHEN dl.martes_vesp = 1 THEN 1 ELSE 0 END +
                                CASE WHEN dl.miercoles_mat = 1 THEN 1 ELSE 0 END +
                                CASE WHEN dl.miercoles_vesp = 1 THEN 1 ELSE 0 END +
                                CASE WHEN dl.jueves_mat = 1 THEN 1 ELSE 0 END +
                                CASE WHEN dl.jueves_vesp = 1 THEN 1 ELSE 0 END +
                                CASE WHEN dl.viernes_mat = 1 THEN 1 ELSE 0 END +
                                CASE WHEN dl.viernes_vesp = 1 THEN 1 ELSE 0 END +
                                CASE WHEN dl.sabado_mat = 1 THEN 1 ELSE 0 END +
                                CASE WHEN dl.sabado_vesp = 1 THEN 1 ELSE 0 END +
                                CASE WHEN dl.domingo_mat = 1 THEN 1 ELSE 0 END +
                                CASE WHEN dl.domingo_vesp = 1 THEN 1 ELSE 0 END
                            ) / (COUNT(dl.id_detalle) * 14)) * 100
                            ELSE 0
                        END
                    ) as promedio_cumplimiento
                FROM Bitacora_Limpieza bl 
                LEFT JOIN Detalle_Limpieza dl ON bl.id_bitacora = dl.id_bitacora
                WHERE $where
                GROUP BY bl.id_bitacora";
        
        $stmt = mysqli_prepare($this->conn, $sql);
        if (!empty($params)) {
            mysqli_stmt_bind_param($stmt, $types, ...$params);
        }
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        $stats = [
            'total_bitacoras' => 0,
            'total_sucursales' => 0,
            'total_areas' => 0,
            'promedio_cumplimiento' => 0
        ];
        
        $cumplimientos = [];
        while($row = mysqli_fetch_assoc($result)) {
            $stats['total_bitacoras']++;
            if (!empty($row['promedio_cumplimiento'])) {
                $cumplimientos[] = $row['promedio_cumplimiento'];
            }
        }
        
        if (!empty($cumplimientos)) {
            $stats['promedio_cumplimiento'] = round(array_sum($cumplimientos) / count($cumplimientos), 2);
        }
        
        // Obtener estadísticas adicionales
        $sql2 = "SELECT 
                    COUNT(DISTINCT bl.area) as total_areas
                FROM Bitacora_Limpieza bl 
                WHERE $where";
        
        $stmt2 = mysqli_prepare($this->conn, $sql2);
        if (!empty($params)) {
            mysqli_stmt_bind_param($stmt2, $types, ...$params);
        }
        mysqli_stmt_execute($stmt2);
        $result2 = mysqli_stmt_get_result($stmt2);
        $row2 = mysqli_fetch_assoc($result2);
        
        $stats['total_sucursales'] = 1; // Como no hay campo sucursal, mostramos 1
        $stats['total_areas'] = $row2['total_areas'];
        
        return $stats;
    }
    
    // Obtener bitácoras por área (ya que no hay campo sucursal)
    public function obtenerBitacorasPorSucursal($filtros = []) {
        $where = "1=1";
        $params = [];
        $types = "";
        
        if (!empty($filtros['fecha_inicio'])) {
            $where .= " AND bl.fecha_inicio >= ?";
            $params[] = $filtros['fecha_inicio'];
            $types .= "s";
        }
        
        if (!empty($filtros['fecha_fin'])) {
            $where .= " AND bl.fecha_fin <= ?";
            $params[] = $filtros['fecha_fin'];
            $types .= "s";
        }
        
        $sql = "SELECT 
                    bl.area as Id_Sucursal,
                    bl.area as Nombre_Sucursal,
                    COUNT(bl.id_bitacora) as total_bitacoras,
                    AVG(
                        CASE 
                            WHEN (COUNT(dl.id_detalle) * 14) > 0 
                            THEN (SUM(CASE 
                                WHEN dl.lunes_mat = 1 THEN 1 ELSE 0 END +
                                CASE WHEN dl.lunes_vesp = 1 THEN 1 ELSE 0 END +
                                CASE WHEN dl.martes_mat = 1 THEN 1 ELSE 0 END +
                                CASE WHEN dl.martes_vesp = 1 THEN 1 ELSE 0 END +
                                CASE WHEN dl.miercoles_mat = 1 THEN 1 ELSE 0 END +
                                CASE WHEN dl.miercoles_vesp = 1 THEN 1 ELSE 0 END +
                                CASE WHEN dl.jueves_mat = 1 THEN 1 ELSE 0 END +
                                CASE WHEN dl.jueves_vesp = 1 THEN 1 ELSE 0 END +
                                CASE WHEN dl.viernes_mat = 1 THEN 1 ELSE 0 END +
                                CASE WHEN dl.viernes_vesp = 1 THEN 1 ELSE 0 END +
                                CASE WHEN dl.sabado_mat = 1 THEN 1 ELSE 0 END +
                                CASE WHEN dl.sabado_vesp = 1 THEN 1 ELSE 0 END +
                                CASE WHEN dl.domingo_mat = 1 THEN 1 ELSE 0 END +
                                CASE WHEN dl.domingo_vesp = 1 THEN 1 ELSE 0 END
                            ) / (COUNT(dl.id_detalle) * 14)) * 100
                            ELSE 0
                        END
                    ) as promedio_cumplimiento
                FROM Bitacora_Limpieza bl
                LEFT JOIN Detalle_Limpieza dl ON bl.id_bitacora = dl.id_bitacora
                WHERE $where
                GROUP BY bl.area
                ORDER BY bl.area";
        
        $stmt = mysqli_prepare($this->conn, $sql);
        if (!empty($params)) {
            mysqli_stmt_bind_param($stmt, $types, ...$params);
        }
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        $sucursales = [];
        while($row = mysqli_fetch_assoc($result)) {
            $row['promedio_cumplimiento'] = round($row['promedio_cumplimiento'] ?? 0, 2);
            $sucursales[] = $row;
        }
        
        return $sucursales;
    }
    
    // Obtener áreas disponibles
    public function obtenerAreas() {
        $sql = "SELECT DISTINCT area FROM Bitacora_Limpieza ORDER BY area";
        $result = mysqli_query($this->conn, $sql);
        
        $areas = [];
        while($row = mysqli_fetch_assoc($result)) {
            $areas[] = $row['area'];
        }
        
        return $areas;
    }
    
    // Obtener sucursales con bitácoras (simulado ya que no hay campo sucursal)
    public function obtenerSucursales() {
        // Como no hay campo sucursal en Bitacora_Limpieza, retornamos sucursales genéricas
        $sql = "SELECT Id_Sucursal, Nombre_Sucursal FROM Sucursales ORDER BY Nombre_Sucursal";
        $result = mysqli_query($this->conn, $sql);
        
        $sucursales = [];
        while($row = mysqli_fetch_assoc($result)) {
            $sucursales[] = $row;
        }
        
        return $sucursales;
    }
    
    // Obtener detalles de una bitácora específica
    public function obtenerDetallesBitacora($id_bitacora) {
        $sql = "SELECT 
                    bl.*,
                    'Todas las Sucursales' as Nombre_Sucursal
                FROM Bitacora_Limpieza bl
                WHERE bl.id_bitacora = ?";
        
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $id_bitacora);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        return mysqli_fetch_assoc($result);
    }
    
    // Obtener elementos de limpieza de una bitácora
    public function obtenerElementosLimpieza($id_bitacora) {
        $sql = "SELECT * FROM Detalle_Limpieza WHERE id_bitacora = ? ORDER BY elemento";
        
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $id_bitacora);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        $elementos = [];
        while($row = mysqli_fetch_assoc($result)) {
            $elementos[] = $row;
        }
        
        return $elementos;
    }
    
    // Eliminar bitácora (administrativo)
    public function eliminarBitacoraAdmin($id_bitacora) {
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
    
    // Exportar datos a CSV
    public function exportarDatosCSV($filtros = []) {
        $bitacoras = $this->obtenerBitacorasAdmin($filtros);
        
        $csv = "ID,Área,Semana,Fecha Inicio,Fecha Fin,Responsable,Supervisor,Auxiliar,Sucursal,Total Elementos,Tareas Completadas,Porcentaje Cumplimiento\n";
        
        foreach($bitacoras as $bitacora) {
            $csv .= sprintf("%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s%%\n",
                $bitacora['id_bitacora'],
                $bitacora['area'],
                $bitacora['semana'],
                $bitacora['fecha_inicio'],
                $bitacora['fecha_fin'],
                $bitacora['responsable'],
                $bitacora['supervisor'],
                $bitacora['aux_res'],
                $bitacora['Nombre_Sucursal'] ?? 'N/A',
                $bitacora['total_elementos'],
                $bitacora['tareas_completadas'],
                $bitacora['porcentaje_cumplimiento']
            );
        }
        
        return $csv;
    }
}
?>
