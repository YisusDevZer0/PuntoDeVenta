-- Tabla: Solicitudes de traspaso entre sucursales
-- La sucursal que tiene el producto (sucursal_solicitada) y el Admin ven estas solicitudes.
-- Quien solicita es la sucursal del usuario (sucursal_solicitante).

CREATE TABLE IF NOT EXISTS Solicitudes_Traspaso_Sucursales (
    ID_Solicitud INT NOT NULL AUTO_INCREMENT,
    Fk_sucursal_solicitante INT NOT NULL COMMENT 'Sucursal que pide el producto (quien solicita)',
    Fk_sucursal_solicitada INT NOT NULL COMMENT 'Sucursal que tiene el producto (a quien se le solicita)',
    ID_Prod_POS INT NOT NULL,
    Cod_Barra VARCHAR(100) DEFAULT NULL,
    Nombre_Prod VARCHAR(255) DEFAULT NULL,
    Cantidad_solicitada INT NOT NULL DEFAULT 1,
    Estatus VARCHAR(50) NOT NULL DEFAULT 'Pendiente' COMMENT 'Pendiente, Aceptada, Rechazada, En traspaso',
    Solicitado_por VARCHAR(255) DEFAULT NULL COMMENT 'Nombre del usuario que solicitó',
    Solicitado_el DATETIME DEFAULT CURRENT_TIMESTAMP,
    ID_H_O_D VARCHAR(100) DEFAULT NULL,
    Observaciones TEXT DEFAULT NULL,
    PRIMARY KEY (ID_Solicitud),
    KEY idx_sucursal_solicitada (Fk_sucursal_solicitada),
    KEY idx_sucursal_solicitante (Fk_sucursal_solicitante),
    KEY idx_estatus (Estatus),
    KEY idx_solicitado_el (Solicitado_el)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Solicitudes de traspaso entre sucursales; visible para Admin y sucursal solicitada';
