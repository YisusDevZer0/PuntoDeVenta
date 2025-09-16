<?php
class BitacoraLimpiezaAdminController {
    private $conn;
    
    public function __construct($connection) {
        $this->conn = $connection;
    }
    
    // Obtener todas las bitácoras con filtros
    public function obtenerBitacorasConFiltros($filtros = []) {
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
                    bl.observaciones,
                    bl.estado,
                    bl.created_at,
                    bl.updated_at,
                    s.Nombre_Sucursal,
                    s.ID_Sucursal
                FROM Bitacora_Limpieza bl
                LEFT JOIN Sucursales s ON bl.sucursal_id = s.ID_Sucursal
                WHERE 1=1";
        
        $params = [];
        $types = "";
        
        // Filtro por sucursal
        if (!empty($filtros['sucursal'])) {
            $sql .= " AND bl.sucursal_id = ?";
            $params[] = $filtros['sucursal'];
            $types .= "i";
        }
        
        // Filtro por área
        if (!empty($filtros['area'])) {
            $sql .= " AND bl.area = ?";
            $params[] = $filtros['area'];
            $types .= "s";
        }
        
        // Filtro por fecha inicio
        if (!empty($filtros['fecha_inicio'])) {
            $sql .= " AND bl.fecha_inicio >= ?";
            $params[] = $filtros['fecha_inicio'];
            $types .= "s";
        }
        
        // Filtro por fecha fin
        if (!empty($filtros['fecha_fin'])) {
            $sql .= " AND bl.fecha_fin <= ?";
            $params[] = $filtros['fecha_fin'];
            $types .= "s";
        }
        
        // Filtro por estado
        if (!empty($filtros['estado'])) {
            $sql .= " AND bl.estado = ?";
            $params[] = $filtros['estado'];
            $types .= "s";
        }
        
        $sql .= " ORDER BY bl.fecha_inicio DESC, bl.id_bitacora DESC";
        
        if (!empty($filtros['limit'])) {
            $sql .= " LIMIT " . intval($filtros['limit']);
        }
        
        $stmt = mysqli_prepare($this->conn, $sql);
        if (!$stmt) {
            throw new Exception("Error preparando consulta: " . mysqli_error($this->conn));
        }
        
        if (!empty($params)) {
            mysqli_stmt_bind_param($stmt, $types, ...$params);
        }
        
        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception("Error ejecutando consulta: " . mysqli_stmt_error($stmt));
        }
        
        $result = mysqli_stmt_get_result($stmt);
        $bitacoras = [];
        
        while ($row = mysqli_fetch_assoc($result)) {
            $bitacoras[] = $row;
        }
        
        mysqli_stmt_close($stmt);
        return $bitacoras;
    }
    
    // Obtener sucursales
    public function obtenerSucursales() {
        $sql = "SELECT ID_Sucursal, Nombre_Sucursal FROM Sucursales WHERE Sucursal_Activa = 'Si' ORDER BY Nombre_Sucursal";
        $result = mysqli_query($this->conn, $sql);
        
        if (!$result) {
            throw new Exception("Error obteniendo sucursales: " . mysqli_error($this->conn));
        }
        
        $sucursales = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $sucursales[] = $row;
        }
        
        return $sucursales;
    }
    
    // Obtener áreas
    public function obtenerAreas() {
        $sql = "SELECT DISTINCT area FROM Bitacora_Limpieza WHERE area IS NOT NULL AND area != '' ORDER BY area";
        $result = mysqli_query($this->conn, $sql);
        
        if (!$result) {
            throw new Exception("Error obteniendo áreas: " . mysqli_error($this->conn));
        }
        
        $areas = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $areas[] = $row['area'];
        }
        
        return $areas;
    }
    
    // Obtener detalles de una bitácora específica
    public function obtenerDetallesBitacora($id_bitacora) {
        $sql = "SELECT 
                    bl.*,
                    s.Nombre_Sucursal,
                    s.ID_Sucursal
                FROM Bitacora_Limpieza bl
                LEFT JOIN Sucursales s ON bl.sucursal_id = s.ID_Sucursal
                WHERE bl.id_bitacora = ?";
        
        $stmt = mysqli_prepare($this->conn, $sql);
        if (!$stmt) {
            throw new Exception("Error preparando consulta: " . mysqli_error($this->conn));
        }
        
        mysqli_stmt_bind_param($stmt, "i", $id_bitacora);
        
        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception("Error ejecutando consulta: " . mysqli_stmt_error($stmt));
        }
        
        $result = mysqli_stmt_get_result($stmt);
        $bitacora = mysqli_fetch_assoc($result);
        
        mysqli_stmt_close($stmt);
        return $bitacora;
    }
    
    // Obtener elementos de limpieza de una bitácora
    public function obtenerElementosLimpieza($id_bitacora) {
        $sql = "SELECT 
                    dl.id_detalle,
                    dl.elemento_limpieza,
                    dl.estado,
                    dl.observaciones,
                    dl.fecha_realizacion,
                    dl.hora_realizacion
                FROM Detalle_Limpieza dl
                WHERE dl.id_bitacora = ?
                ORDER BY dl.id_detalle";
        
        $stmt = mysqli_prepare($this->conn, $sql);
        if (!$stmt) {
            throw new Exception("Error preparando consulta: " . mysqli_error($this->conn));
        }
        
        mysqli_stmt_bind_param($stmt, "i", $id_bitacora);
        
        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception("Error ejecutando consulta: " . mysqli_stmt_error($stmt));
        }
        
        $result = mysqli_stmt_get_result($stmt);
        $elementos = [];
        
        while ($row = mysqli_fetch_assoc($result)) {
            $elementos[] = $row;
        }
        
        mysqli_stmt_close($stmt);
        return $elementos;
    }
    
    // Crear nueva bitácora
    public function crearBitacora($datos) {
        // Verificar si existe la columna sucursal_id
        $check_column = "SHOW COLUMNS FROM Bitacora_Limpieza LIKE 'sucursal_id'";
        $result = mysqli_query($this->conn, $check_column);
        $has_sucursal_id = mysqli_num_rows($result) > 0;
        
        if ($has_sucursal_id) {
            $sql = "INSERT INTO Bitacora_Limpieza (
                        area, semana, fecha_inicio, fecha_fin, 
                        responsable, supervisor, aux_res, 
                        sucursal_id, observaciones, estado, created_at
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
            
            $stmt = mysqli_prepare($this->conn, $sql);
            if (!$stmt) {
                throw new Exception("Error preparando consulta: " . mysqli_error($this->conn));
            }
            
            mysqli_stmt_bind_param($stmt, "sssssssiss", 
                $datos['area'], 
                $datos['semana'], 
                $datos['fecha_inicio'], 
                $datos['fecha_fin'], 
                $datos['responsable'], 
                $datos['supervisor'], 
                $datos['aux_res'], 
                $datos['sucursal_id'], 
                $datos['observaciones'], 
                $datos['estado']
            );
        } else {
            $sql = "INSERT INTO Bitacora_Limpieza (
                        area, semana, fecha_inicio, fecha_fin, 
                        responsable, supervisor, aux_res, 
                        observaciones, estado, created_at
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
            
            $stmt = mysqli_prepare($this->conn, $sql);
            if (!$stmt) {
                throw new Exception("Error preparando consulta: " . mysqli_error($this->conn));
            }
            
            mysqli_stmt_bind_param($stmt, "ssssssss", 
                $datos['area'], 
                $datos['semana'], 
                $datos['fecha_inicio'], 
                $datos['fecha_fin'], 
                $datos['responsable'], 
                $datos['supervisor'], 
                $datos['aux_res'], 
                $datos['observaciones'], 
                $datos['estado']
            );
        }
        
        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception("Error ejecutando consulta: " . mysqli_stmt_error($stmt));
        }
        
        $id_bitacora = mysqli_insert_id($this->conn);
        mysqli_stmt_close($stmt);
        
        return $id_bitacora;
    }
    
    // Eliminar bitácora
    public function eliminarBitacora($id_bitacora) {
        // Primero eliminar detalles
        $sql_detalles = "DELETE FROM Detalle_Limpieza WHERE id_bitacora = ?";
        $stmt_detalles = mysqli_prepare($this->conn, $sql_detalles);
        if ($stmt_detalles) {
            mysqli_stmt_bind_param($stmt_detalles, "i", $id_bitacora);
            mysqli_stmt_execute($stmt_detalles);
            mysqli_stmt_close($stmt_detalles);
        }
        
        // Luego eliminar bitácora
        $sql = "DELETE FROM Bitacora_Limpieza WHERE id_bitacora = ?";
        $stmt = mysqli_prepare($this->conn, $sql);
        if (!$stmt) {
            throw new Exception("Error preparando consulta: " . mysqli_error($this->conn));
        }
        
        mysqli_stmt_bind_param($stmt, "i", $id_bitacora);
        
        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception("Error ejecutando consulta: " . mysqli_stmt_error($stmt));
        }
        
        $affected_rows = mysqli_stmt_affected_rows($stmt);
        mysqli_stmt_close($stmt);
        
        return $affected_rows > 0;
    }
    
    // Obtener estadísticas generales
    public function obtenerEstadisticasGenerales() {
        $stats = [];
        
        // Total bitácoras
        $sql_total = "SELECT COUNT(*) as total FROM Bitacora_Limpieza";
        $result = mysqli_query($this->conn, $sql_total);
        $stats['total_bitacoras'] = mysqli_fetch_assoc($result)['total'];
        
        // Bitácoras activas
        $sql_activas = "SELECT COUNT(*) as activas FROM Bitacora_Limpieza WHERE estado = 'Activa'";
        $result = mysqli_query($this->conn, $sql_activas);
        $stats['bitacoras_activas'] = mysqli_fetch_assoc($result)['activas'];
        
        // Total sucursales
        $sql_sucursales = "SELECT COUNT(*) as total FROM Sucursales WHERE Sucursal_Activa = 'Si'";
        $result = mysqli_query($this->conn, $sql_sucursales);
        $stats['total_sucursales'] = mysqli_fetch_assoc($result)['total'];
        
        // Total áreas
        $sql_areas = "SELECT COUNT(DISTINCT area) as total FROM Bitacora_Limpieza WHERE area IS NOT NULL AND area != ''";
        $result = mysqli_query($this->conn, $sql_areas);
        $stats['total_areas'] = mysqli_fetch_assoc($result)['total'];
        
        return $stats;
    }
    
    // Exportar datos a CSV
    public function exportarDatosCSV($filtros = []) {
        $bitacoras = $this->obtenerBitacorasConFiltros($filtros);
        
        $csv_data = [];
        $csv_data[] = [
            'ID Bitácora',
            'Área',
            'Semana',
            'Fecha Inicio',
            'Fecha Fin',
            'Responsable',
            'Supervisor',
            'Auxiliar',
            'Sucursal',
            'Estado',
            'Observaciones',
            'Fecha Creación'
        ];
        
        foreach ($bitacoras as $bitacora) {
            $csv_data[] = [
                $bitacora['id_bitacora'],
                $bitacora['area'],
                $bitacora['semana'],
                $bitacora['fecha_inicio'],
                $bitacora['fecha_fin'],
                $bitacora['responsable'],
                $bitacora['supervisor'],
                $bitacora['aux_res'],
                $bitacora['Nombre_Sucursal'] ?? 'N/A',
                $bitacora['estado'],
                $bitacora['observaciones'],
                $bitacora['created_at']
            ];
        }
        
        return $csv_data;
    }
}
?>