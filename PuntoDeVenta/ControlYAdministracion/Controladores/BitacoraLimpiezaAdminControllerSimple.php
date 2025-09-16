<?php
include_once "db_connect.php";

class BitacoraLimpiezaAdminControllerSimple {
    private $conn;
    
    public function __construct($connection) {
        $this->conn = $connection;
    }
    
    // Obtener todas las bitácoras de forma simple
    public function obtenerBitacorasAdmin($filtros = []) {
        $where = "1=1";
        $params = [];
        $types = "";
        
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
                    0 as total_elementos,
                    0 as tareas_completadas,
                    0 as total_tareas_posibles,
                    0 as porcentaje_cumplimiento
                FROM Bitacora_Limpieza bl 
                WHERE $where
                ORDER BY bl.fecha_inicio DESC";
        
        if (!empty($params)) {
            $stmt = mysqli_prepare($this->conn, $sql);
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, $types, ...$params);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                
                $bitacoras = [];
                while($row = mysqli_fetch_assoc($result)) {
                    $bitacoras[] = $row;
                }
                mysqli_stmt_close($stmt);
                return $bitacoras;
            }
        } else {
            $result = mysqli_query($this->conn, $sql);
            $bitacoras = [];
            while($row = mysqli_fetch_assoc($result)) {
                $bitacoras[] = $row;
            }
            return $bitacoras;
        }
        
        return [];
    }
    
    // Obtener estadísticas generales simples
    public function obtenerEstadisticasGenerales($filtros = []) {
        $where = "1=1";
        $params = [];
        $types = "";
        
        if (!empty($filtros['area'])) {
            $where .= " AND area = ?";
            $params[] = $filtros['area'];
            $types .= "s";
        }
        
        if (!empty($filtros['fecha_inicio'])) {
            $where .= " AND fecha_inicio >= ?";
            $params[] = $filtros['fecha_inicio'];
            $types .= "s";
        }
        
        if (!empty($filtros['fecha_fin'])) {
            $where .= " AND fecha_fin <= ?";
            $params[] = $filtros['fecha_fin'];
            $types .= "s";
        }
        
        $sql = "SELECT 
                    COUNT(*) as total_bitacoras,
                    COUNT(DISTINCT area) as total_areas
                FROM Bitacora_Limpieza 
                WHERE $where";
        
        if (!empty($params)) {
            $stmt = mysqli_prepare($this->conn, $sql);
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, $types, ...$params);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                $row = mysqli_fetch_assoc($result);
                mysqli_stmt_close($stmt);
            }
        } else {
            $result = mysqli_query($this->conn, $sql);
            $row = mysqli_fetch_assoc($result);
        }
        
        return [
            'total_bitacoras' => $row['total_bitacoras'] ?? 0,
            'total_sucursales' => 1,
            'total_areas' => $row['total_areas'] ?? 0,
            'promedio_cumplimiento' => 0
        ];
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
    
    // Obtener sucursales reales
    public function obtenerSucursales() {
        $sql = "SELECT ID_Sucursal, Nombre_Sucursal FROM Sucursales WHERE Sucursal_Activa = 'Si' ORDER BY Nombre_Sucursal";
        $result = mysqli_query($this->conn, $sql);
        
        $sucursales = [];
        if ($result) {
            while($row = mysqli_fetch_assoc($result)) {
                $sucursales[] = $row;
            }
        }
        
        return $sucursales;
    }
    
    // Obtener bitácoras por área (simulado)
    public function obtenerBitacorasPorSucursal($filtros = []) {
        $areas = $this->obtenerAreas();
        $resultado = [];
        
        foreach($areas as $area) {
            $resultado[] = [
                'Id_Sucursal' => $area,
                'Nombre_Sucursal' => $area,
                'total_bitacoras' => 0,
                'promedio_cumplimiento' => 0
            ];
        }
        
        return $resultado;
    }
    
    // Obtener detalles de una bitácora específica
    public function obtenerDetallesBitacora($id_bitacora) {
        $sql = "SELECT 
                    bl.*,
                    'Todas las Sucursales' as Nombre_Sucursal
                FROM Bitacora_Limpieza bl
                WHERE bl.id_bitacora = ?";
        
        $stmt = mysqli_prepare($this->conn, $sql);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "i", $id_bitacora);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $row = mysqli_fetch_assoc($result);
            mysqli_stmt_close($stmt);
            return $row;
        }
        
        return null;
    }
    
    // Obtener elementos de limpieza de una bitácora
    public function obtenerElementosLimpieza($id_bitacora) {
        $sql = "SELECT * FROM Detalle_Limpieza WHERE id_bitacora = ? ORDER BY elemento";
        
        $stmt = mysqli_prepare($this->conn, $sql);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "i", $id_bitacora);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            
            $elementos = [];
            while($row = mysqli_fetch_assoc($result)) {
                $elementos[] = $row;
            }
            mysqli_stmt_close($stmt);
            return $elementos;
        }
        
        return [];
    }
    
    // Eliminar bitácora (administrativo)
    public function eliminarBitacoraAdmin($id_bitacora) {
        // Primero eliminar detalles
        $sql1 = "DELETE FROM Detalle_Limpieza WHERE id_bitacora = ?";
        $stmt1 = mysqli_prepare($this->conn, $sql1);
        if ($stmt1) {
            mysqli_stmt_bind_param($stmt1, "i", $id_bitacora);
            mysqli_stmt_execute($stmt1);
            mysqli_stmt_close($stmt1);
        }
        
        // Luego eliminar bitácora
        $sql2 = "DELETE FROM Bitacora_Limpieza WHERE id_bitacora = ?";
        $stmt2 = mysqli_prepare($this->conn, $sql2);
        if ($stmt2) {
            mysqli_stmt_bind_param($stmt2, "i", $id_bitacora);
            $result = mysqli_stmt_execute($stmt2);
            mysqli_stmt_close($stmt2);
            return $result;
        }
        
        return false;
    }
    
    // Crear nueva bitácora administrativa
    public function crearBitacoraAdmin($datos) {
        // Verificar si el campo sucursal_id existe
        $checkField = "SHOW COLUMNS FROM Bitacora_Limpieza LIKE 'sucursal_id'";
        $result = mysqli_query($this->conn, $checkField);
        
        if (mysqli_num_rows($result) > 0) {
            // Campo sucursal_id existe, usar la consulta completa
            $sql = "INSERT INTO Bitacora_Limpieza (
                        area, 
                        semana, 
                        fecha_inicio, 
                        fecha_fin, 
                        responsable, 
                        supervisor, 
                        aux_res,
                        sucursal_id,
                        created_at
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())";
            
            $stmt = mysqli_prepare($this->conn, $sql);
            if (!$stmt) {
                throw new Exception("Error preparando consulta: " . mysqli_error($this->conn));
            }
            
            mysqli_stmt_bind_param($stmt, "sssssssi", 
                $datos['area'],
                $datos['semana'],
                $datos['fecha_inicio'],
                $datos['fecha_fin'],
                $datos['responsable'],
                $datos['supervisor'],
                $datos['aux_res'],
                $datos['sucursal_id']
            );
        } else {
            // Campo sucursal_id no existe, usar consulta sin sucursal
            $sql = "INSERT INTO Bitacora_Limpieza (
                        area, 
                        semana, 
                        fecha_inicio, 
                        fecha_fin, 
                        responsable, 
                        supervisor, 
                        aux_res
                    ) VALUES (?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = mysqli_prepare($this->conn, $sql);
            if (!$stmt) {
                throw new Exception("Error preparando consulta: " . mysqli_error($this->conn));
            }
            
            mysqli_stmt_bind_param($stmt, "sssssss", 
                $datos['area'],
                $datos['semana'],
                $datos['fecha_inicio'],
                $datos['fecha_fin'],
                $datos['responsable'],
                $datos['supervisor'],
                $datos['aux_res']
            );
        }
        
        $result = mysqli_stmt_execute($stmt);
        $id_bitacora = mysqli_insert_id($this->conn);
        mysqli_stmt_close($stmt);
        
        if ($result) {
            return $id_bitacora;
        } else {
            throw new Exception("Error creando bitácora: " . mysqli_error($this->conn));
        }
    }
    
    // Obtener bitácoras con información de sucursal
    public function obtenerBitacorasConSucursal($filtros = []) {
        // Verificar si el campo sucursal_id existe
        $checkField = "SHOW COLUMNS FROM Bitacora_Limpieza LIKE 'sucursal_id'";
        $result = mysqli_query($this->conn, $checkField);
        $hasSucursalField = mysqli_num_rows($result) > 0;
        
        $where = "1=1";
        $params = [];
        $types = "";
        
        // Filtro por sucursal (solo si el campo existe)
        if ($hasSucursalField && !empty($filtros['sucursal'])) {
            $where .= " AND bl.sucursal_id = ?";
            $params[] = $filtros['sucursal'];
            $types .= "i";
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
        
        if ($hasSucursalField) {
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
                        bl.sucursal_id,
                        s.Nombre_Sucursal,
                        bl.created_at,
                        bl.updated_at,
                        0 as total_elementos,
                        0 as tareas_completadas,
                        0 as total_tareas_posibles,
                        0 as porcentaje_cumplimiento
                    FROM Bitacora_Limpieza bl 
                    LEFT JOIN Sucursales s ON bl.sucursal_id = s.Id_Sucursal
                    WHERE $where
                    ORDER BY bl.fecha_inicio DESC";
        } else {
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
                        NULL as sucursal_id,
                        'N/A - Actualizar BD' as Nombre_Sucursal,
                        NOW() as created_at,
                        NOW() as updated_at,
                        0 as total_elementos,
                        0 as tareas_completadas,
                        0 as total_tareas_posibles,
                        0 as porcentaje_cumplimiento
                    FROM Bitacora_Limpieza bl 
                    WHERE $where
                    ORDER BY bl.fecha_inicio DESC";
        }
        
        if (!empty($params)) {
            $stmt = mysqli_prepare($this->conn, $sql);
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, $types, ...$params);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                
                $bitacoras = [];
                while($row = mysqli_fetch_assoc($result)) {
                    $bitacoras[] = $row;
                }
                mysqli_stmt_close($stmt);
                return $bitacoras;
            }
        } else {
            $result = mysqli_query($this->conn, $sql);
            $bitacoras = [];
            while($row = mysqli_fetch_assoc($result)) {
                $bitacoras[] = $row;
            }
            return $bitacoras;
        }
        
        return [];
    }
    
    // Exportar datos a CSV
    public function exportarDatosCSV($filtros = []) {
        $bitacoras = $this->obtenerBitacorasConSucursal($filtros);
        
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
