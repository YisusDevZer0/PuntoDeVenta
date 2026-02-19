-- =====================================================
-- CONFIGURACIÓN DE INVENTARIO POR TURNOS (periodos y límites)
-- =====================================================
-- Periodos: ventana de fechas en que está permitido el inventario por turnos.
-- Config sucursal/empleado: máx. turnos por día y productos por turno.

-- Tabla: Periodos de inventario (por sucursal o global)
-- Fk_sucursal = 0 significa "global" (respaldo cuando la sucursal no tiene periodo propio)
CREATE TABLE IF NOT EXISTS `Inventario_Turnos_Periodos` (
  `ID_Periodo` int(11) NOT NULL AUTO_INCREMENT,
  `Fk_sucursal` int(11) NOT NULL DEFAULT 0,
  `Fecha_Inicio` date NOT NULL,
  `Fecha_Fin` date NOT NULL,
  `Nombre_Periodo` varchar(100) DEFAULT NULL,
  `Codigo_Externo` varchar(50) DEFAULT NULL COMMENT 'Para integración con otro sistema',
  `Activo` tinyint(1) NOT NULL DEFAULT 1,
  `Actualizado_Por` varchar(250) DEFAULT NULL,
  `Fecha_Actualizacion` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`ID_Periodo`),
  KEY `idx_sucursal_fechas` (`Fk_sucursal`, `Fecha_Inicio`, `Fecha_Fin`),
  KEY `idx_activo` (`Activo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla: Configuración por sucursal (turnos por día, productos por turno)
-- Fk_sucursal = 0 = valores por defecto globales
CREATE TABLE IF NOT EXISTS `Inventario_Turnos_Config_Sucursal` (
  `ID_Config` int(11) NOT NULL AUTO_INCREMENT,
  `Fk_sucursal` int(11) NOT NULL DEFAULT 0,
  `Max_Turnos_Por_Dia` int(11) NOT NULL DEFAULT 0 COMMENT '0 = sin límite',
  `Max_Productos_Por_Turno` int(11) NOT NULL DEFAULT 50,
  `Activo` tinyint(1) NOT NULL DEFAULT 1,
  `Actualizado_Por` varchar(250) DEFAULT NULL,
  `Fecha_Actualizacion` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`ID_Config`),
  UNIQUE KEY `uk_sucursal` (`Fk_sucursal`),
  KEY `idx_activo` (`Activo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla: Configuración por empleado (opcional)
-- Max_Turnos_Por_Dia/Max_Productos_Por_Turno = 0 significa usar el de la sucursal
CREATE TABLE IF NOT EXISTS `Inventario_Turnos_Config_Empleado` (
  `ID_Config` int(11) NOT NULL AUTO_INCREMENT,
  `Fk_usuario` int(11) NOT NULL COMMENT 'Id_PvUser de Usuarios_PV',
  `Fk_sucursal` int(11) NOT NULL DEFAULT 0 COMMENT '0 = aplica a todas las sucursales del usuario',
  `Max_Turnos_Por_Dia` int(11) NOT NULL DEFAULT 0 COMMENT '0 = usar límite de sucursal',
  `Max_Productos_Por_Turno` int(11) NOT NULL DEFAULT 0 COMMENT '0 = usar límite de sucursal',
  `Activo` tinyint(1) NOT NULL DEFAULT 1,
  `Actualizado_Por` varchar(250) DEFAULT NULL,
  `Fecha_Actualizacion` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`ID_Config`),
  UNIQUE KEY `uk_usuario_sucursal` (`Fk_usuario`, `Fk_sucursal`),
  KEY `idx_usuario` (`Fk_usuario`),
  KEY `idx_sucursal` (`Fk_sucursal`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insertar configuración global por defecto (sucursal 0) para no romper comportamiento actual
INSERT IGNORE INTO `Inventario_Turnos_Config_Sucursal` (`Fk_sucursal`, `Max_Turnos_Por_Dia`, `Max_Productos_Por_Turno`, `Activo`)
VALUES (0, 0, 50, 1);
