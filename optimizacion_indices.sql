-- =====================================================
-- SCRIPT SUPER MEGA HIPER OPTIMIZACIÓN COMPLETA CON VALIDACIÓN
-- Base de datos: u858848268_doctorpez
-- Fecha: 2026-02-02
-- =====================================================
-- Este script optimiza TODAS las tablas de la base de datos
-- agregando índices estratégicos para máximo rendimiento
-- CON VALIDACIÓN AUTOMÁTICA - No genera errores de índices duplicados
-- =====================================================

USE `u858848268_doctorpez`;

-- =====================================================
-- PROCEDIMIENTO PARA CREAR ÍNDICES DE FORMA SEGURA
-- =====================================================
-- Este procedimiento verifica si un índice existe antes de crearlo
-- Uso: CALL crear_indice_si_no_existe('tabla', 'nombre_indice', 'columna1, columna2');
-- =====================================================

DELIMITER $$

DROP PROCEDURE IF EXISTS crear_indice_si_no_existe$$

CREATE PROCEDURE crear_indice_si_no_existe(
    IN tabla_nombre VARCHAR(255),
    IN indice_nombre VARCHAR(255),
    IN columnas VARCHAR(500)
)
BEGIN
    DECLARE indice_existe INT DEFAULT 0;
    DECLARE CONTINUE HANDLER FOR SQLEXCEPTION
    BEGIN
        -- Ignorar errores y continuar
    END;
    
    -- Verificar si el índice ya existe
    SELECT COUNT(*) INTO indice_existe
    FROM INFORMATION_SCHEMA.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = tabla_nombre
      AND INDEX_NAME = indice_nombre;
    
    -- Si no existe, crearlo
    IF indice_existe = 0 THEN
        SET @sql = CONCAT('ALTER TABLE `', tabla_nombre, '` ADD INDEX `', indice_nombre, '` (', columnas, ')');
        PREPARE stmt FROM @sql;
        EXECUTE stmt;
        DEALLOCATE PREPARE stmt;
    END IF;
END$$

DELIMITER ;

-- =====================================================
-- PROCEDIMIENTO PARA CREAR ÍNDICES FULLTEXT DE FORMA SEGURA
-- =====================================================

DELIMITER $$

DROP PROCEDURE IF EXISTS crear_fulltext_si_no_existe$$

CREATE PROCEDURE crear_fulltext_si_no_existe(
    IN tabla_nombre VARCHAR(255),
    IN indice_nombre VARCHAR(255),
    IN columnas VARCHAR(500)
)
BEGIN
    DECLARE indice_existe INT DEFAULT 0;
    DECLARE CONTINUE HANDLER FOR SQLEXCEPTION
    BEGIN
        -- Ignorar errores y continuar
    END;
    
    -- Verificar si el índice FULLTEXT ya existe
    SELECT COUNT(*) INTO indice_existe
    FROM INFORMATION_SCHEMA.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = tabla_nombre
      AND INDEX_NAME = indice_nombre
      AND INDEX_TYPE = 'FULLTEXT';
    
    -- Si no existe, crearlo
    IF indice_existe = 0 THEN
        SET @sql = CONCAT('ALTER TABLE `', tabla_nombre, '` ADD FULLTEXT INDEX `', indice_nombre, '` (', columnas, ')');
        PREPARE stmt FROM @sql;
        EXECUTE stmt;
        DEALLOCATE PREPARE stmt;
    END IF;
END$$

DELIMITER ;

-- =====================================================
-- SECCIÓN 1: TABLAS PRINCIPALES DE VENTAS
-- =====================================================

-- Ventas_POS - Tabla más crítica del sistema
CALL crear_indice_si_no_existe('Ventas_POS', 'idx_ventas_fecha_sucursal', '`Fecha_venta`, `Fk_sucursal`');
CALL crear_indice_si_no_existe('Ventas_POS', 'idx_ventas_fecha_estatus', '`Fecha_venta`, `Estatus`');
CALL crear_indice_si_no_existe('Ventas_POS', 'idx_ventas_sucursal_estatus', '`Fk_sucursal`, `Estatus`');
CALL crear_indice_si_no_existe('Ventas_POS', 'idx_ventas_producto', '`ID_Prod_POS`');
CALL crear_indice_si_no_existe('Ventas_POS', 'idx_ventas_cod_barra', '`Cod_Barra`');
CALL crear_indice_si_no_existe('Ventas_POS', 'idx_ventas_folio_ticket', '`Folio_Ticket`');
CALL crear_indice_si_no_existe('Ventas_POS', 'idx_ventas_agregado_el', '`AgregadoEl`');
CALL crear_indice_si_no_existe('Ventas_POS', 'idx_ventas_fecha_estatus_importe', '`Fecha_venta`, `Estatus`, `Importe`');
CALL crear_indice_si_no_existe('Ventas_POS', 'idx_ventas_caja', '`Fk_Caja`');
CALL crear_indice_si_no_existe('Ventas_POS', 'idx_ventas_producto_fecha', '`ID_Prod_POS`, `Fecha_venta`');
CALL crear_indice_si_no_existe('Ventas_POS', 'idx_ventas_turno', '`Turno`');
CALL crear_indice_si_no_existe('Ventas_POS', 'idx_ventas_forma_pago', '`FormaDePago`');
CALL crear_indice_si_no_existe('Ventas_POS', 'idx_ventas_cliente', '`Cliente`(100)');
CALL crear_indice_si_no_existe('Ventas_POS', 'idx_ventas_sucursal_fecha_estatus', '`Fk_sucursal`, `Fecha_venta`, `Estatus`');

-- Ventas_POSV2
CALL crear_indice_si_no_existe('Ventas_POSV2', 'idx_ventasv2_fecha_sucursal', '`Fecha_venta`, `Fk_sucursal`');
CALL crear_indice_si_no_existe('Ventas_POSV2', 'idx_ventasv2_estatus', '`Estatus`');
CALL crear_indice_si_no_existe('Ventas_POSV2', 'idx_ventasv2_producto', '`ID_Prod_POS`');
CALL crear_indice_si_no_existe('Ventas_POSV2', 'idx_ventasv2_folio_ticket', '`Folio_Ticket`');

-- Ventas_POS_Audita
CALL crear_indice_si_no_existe('Ventas_POS_Audita', 'idx_ventas_audita_fecha', '`AgregadoEl`');
CALL crear_indice_si_no_existe('Ventas_POS_Audita', 'idx_ventas_audita_ticket', '`Folio_Ticket`');

-- Ventas_POS_Cancelaciones
CALL crear_indice_si_no_existe('Ventas_POS_Cancelaciones', 'idx_cancelaciones_fecha', '`AgregadoEl`');
CALL crear_indice_si_no_existe('Ventas_POS_Cancelaciones', 'idx_cancelaciones_ticket', '`Folio_Ticket`');
CALL crear_indice_si_no_existe('Ventas_POS_Cancelaciones', 'idx_cancelaciones_sucursal', '`Fk_sucursal`');
CALL crear_indice_si_no_existe('Ventas_POS_Cancelaciones', 'idx_cancelaciones_producto', '`ID_Prod_POS`');

-- =====================================================
-- SECCIÓN 2: TABLAS DE STOCK E INVENTARIO
-- =====================================================

-- Stock_POS - Tabla crítica de inventario
CALL crear_indice_si_no_existe('Stock_POS', 'idx_stock_cod_barra_sucursal', '`Cod_Barra`, `Fk_sucursal`');
CALL crear_indice_si_no_existe('Stock_POS', 'idx_stock_producto_sucursal', '`ID_Prod_POS`, `Fk_sucursal`');
CALL crear_indice_si_no_existe('Stock_POS', 'idx_stock_existencias', '`Min_Existencia`, `Existencias_R`');
CALL crear_indice_si_no_existe('Stock_POS', 'idx_stock_tipo_servicio', '`Tipo_Servicio`');
CALL crear_indice_si_no_existe('Stock_POS', 'idx_stock_sucursal_tipo', '`Fk_sucursal`, `Tipo_Servicio`');
CALL crear_indice_si_no_existe('Stock_POS', 'idx_stock_estatus', '`Estatus`');
CALL crear_indice_si_no_existe('Stock_POS', 'idx_stock_sucursal_existencias', '`Fk_sucursal`, `Existencias_R`');
CALL crear_indice_si_no_existe('Stock_POS', 'idx_stock_lote', '`Lote`');
CALL crear_indice_si_no_existe('Stock_POS', 'idx_stock_fecha_caducidad', '`Fecha_Caducidad`');
CALL crear_indice_si_no_existe('Stock_POS', 'idx_stock_caducidad_sucursal', '`Fecha_Caducidad`, `Fk_sucursal`');
CALL crear_indice_si_no_existe('Stock_POS', 'idx_stock_fecha_ingreso', '`Fecha_Ingreso`');
CALL crear_indice_si_no_existe('Stock_POS', 'idx_stock_producto_lote_sucursal', '`ID_Prod_POS`, `Lote`, `Fk_sucursal`');
CALL crear_indice_si_no_existe('Stock_POS', 'idx_stock_control_lotes', '`Control_Lotes_Caducidad`');
CALL crear_indice_si_no_existe('Stock_POS', 'idx_stock_agregado_el', '`AgregadoEl`');

-- Stock_POS_Log
CALL crear_indice_si_no_existe('Stock_POS_Log', 'idx_stock_log_cod_barra', '`Cod_Barra`');
CALL crear_indice_si_no_existe('Stock_POS_Log', 'idx_stock_log_sucursal', '`Fk_sucursal`');
CALL crear_indice_si_no_existe('Stock_POS_Log', 'idx_stock_log_fecha', '`Fecha`');

-- Stock_registrosNuevos
CALL crear_indice_si_no_existe('Stock_registrosNuevos', 'idx_stock_nuevos_producto', '`ID_Prod_POS`');
CALL crear_indice_si_no_existe('Stock_registrosNuevos', 'idx_stock_nuevos_sucursal', '`Fk_sucursal`');
CALL crear_indice_si_no_existe('Stock_registrosNuevos', 'idx_stock_nuevos_fecha', '`AgregadoEl`');
CALL crear_indice_si_no_existe('Stock_registrosNuevos', 'idx_stock_nuevos_fecha_caducidad', '`Fecha_Caducidad`');

-- =====================================================
-- SECCIÓN 3: TABLAS DE PRODUCTOS
-- =====================================================

-- Productos_POS
CALL crear_indice_si_no_existe('Productos_POS', 'idx_productos_cod_barra', '`Cod_Barra`');
CALL crear_fulltext_si_no_existe('Productos_POS', 'idx_productos_nombre_fulltext', '`Nombre_Prod`');
CALL crear_indice_si_no_existe('Productos_POS', 'idx_productos_tipo_servicio', '`Tipo_Servicio`');
CALL crear_indice_si_no_existe('Productos_POS', 'idx_productos_categoria', '`FkCategoria`(100)');
CALL crear_indice_si_no_existe('Productos_POS', 'idx_productos_marca', '`FkMarca`(100)');
CALL crear_indice_si_no_existe('Productos_POS', 'idx_productos_categoria_marca', '`FkCategoria`(100), `FkMarca`(100)');
CALL crear_indice_si_no_existe('Productos_POS', 'idx_productos_clave_levic', '`Clave_Levic`');
CALL crear_indice_si_no_existe('Productos_POS', 'idx_productos_agregado_el', '`AgregadoEl`');
CALL crear_indice_si_no_existe('Productos_POS', 'idx_productos_actualizado_el', '`ActualizadoEl`');

-- Productos_POS_Auditoria
CALL crear_indice_si_no_existe('Productos_POS_Auditoria', 'idx_productos_audit_producto', '`ID_Prod_POS`');
CALL crear_indice_si_no_existe('Productos_POS_Auditoria', 'idx_productos_audit_fecha', '`AgregadoEl`');

-- Productos_POS_Eliminados
CALL crear_indice_si_no_existe('Productos_POS_Eliminados', 'idx_productos_eliminados_cod_barra', '`Cod_Barra`');
CALL crear_indice_si_no_existe('Productos_POS_Eliminados', 'idx_productos_eliminados_fecha', '`EliminadoEl`');

-- productos_lotes_caducidad
CALL crear_indice_si_no_existe('productos_lotes_caducidad', 'idx_lotes_caducidad_cod_barra', '`cod_barra`');
CALL crear_indice_si_no_existe('productos_lotes_caducidad', 'idx_lotes_caducidad_sucursal', '`sucursal_id`');
CALL crear_indice_si_no_existe('productos_lotes_caducidad', 'idx_lotes_caducidad_fecha', '`fecha_caducidad`');
CALL crear_indice_si_no_existe('productos_lotes_caducidad', 'idx_lotes_caducidad_estado', '`estado`');
CALL crear_indice_si_no_existe('productos_lotes_caducidad', 'idx_lotes_caducidad_sucursal_fecha', '`sucursal_id`, `fecha_caducidad`');

-- =====================================================
-- SECCIÓN 4: TABLAS CEDIS
-- =====================================================

-- CEDIS
CALL crear_indice_si_no_existe('CEDIS', 'idx_cedis_producto', '`ID_Prod_POS`');
CALL crear_indice_si_no_existe('CEDIS', 'idx_cedis_cod_barra', '`Cod_Barra`');
CALL crear_indice_si_no_existe('CEDIS', 'idx_cedis_tipo_servicio', '`Tipo_Servicio`');
CALL crear_indice_si_no_existe('CEDIS', 'idx_cedis_estatus', '`Estatus`');
CALL crear_indice_si_no_existe('CEDIS', 'idx_cedis_fecha_caducidad', '`Fecha_Caducidad`');
CALL crear_indice_si_no_existe('CEDIS', 'idx_cedis_agregado_el', '`AgregadoEl`');
CALL crear_indice_si_no_existe('CEDIS', 'idx_cedis_actualizado_el', '`ActualizadoEl`');

-- CEDIS_Eliminados
CALL crear_indice_si_no_existe('CEDIS_Eliminados', 'idx_cedis_eliminados_producto', '`ID_Prod_POS`');
CALL crear_indice_si_no_existe('CEDIS_Eliminados', 'idx_cedis_eliminados_fecha', '`AgregadoEl`');

-- Cedis_Inventarios
CALL crear_indice_si_no_existe('Cedis_Inventarios', 'idx_cedis_inv_producto', '`ID_Prod_POS`');
CALL crear_indice_si_no_existe('Cedis_Inventarios', 'idx_cedis_inv_cod_barra', '`Cod_Barra`');
CALL crear_indice_si_no_existe('Cedis_Inventarios', 'idx_cedis_inv_fecha', '`FechaInventario`');
CALL crear_indice_si_no_existe('Cedis_Inventarios', 'idx_cedis_inv_agregado_el', '`AgregadoEl`');

-- =====================================================
-- SECCIÓN 5: TABLAS DE CAJAS Y CORTES
-- =====================================================

-- Cajas
CALL crear_indice_si_no_existe('Cajas', 'idx_cajas_sucursal_estatus', '`Sucursal`, `Estatus`');
CALL crear_indice_si_no_existe('Cajas', 'idx_cajas_estatus', '`Estatus`');
CALL crear_indice_si_no_existe('Cajas', 'idx_cajas_sucursal', '`Sucursal`');
CALL crear_indice_si_no_existe('Cajas', 'idx_cajas_fecha_apertura', '`Fecha_Apertura`');

-- Cajas_POS_Audita
CALL crear_indice_si_no_existe('Cajas_POS_Audita', 'idx_cajas_audita_caja', '`ID_Caja`');
CALL crear_indice_si_no_existe('Cajas_POS_Audita', 'idx_cajas_audita_fecha', '`AgregadoEl`');

-- Cortes_Cajas_POS
CALL crear_indice_si_no_existe('Cortes_Cajas_POS', 'idx_cortes_caja', '`ID_Caja`');
CALL crear_indice_si_no_existe('Cortes_Cajas_POS', 'idx_cortes_fecha', '`Hora_Cierre`');
CALL crear_indice_si_no_existe('Cortes_Cajas_POS', 'idx_cortes_sucursal', '`Sucursal`');
CALL crear_indice_si_no_existe('Cortes_Cajas_POS', 'idx_cortes_fk_caja', '`Fk_Caja`');

-- Fondos_Cajas
CALL crear_indice_si_no_existe('Fondos_Cajas', 'idx_fondos_sucursal', '`Fk_Sucursal`');
CALL crear_indice_si_no_existe('Fondos_Cajas', 'idx_fondos_estatus', '`Estatus`');
CALL crear_indice_si_no_existe('Fondos_Cajas', 'idx_fondos_fecha', '`AgregadoEl`');

-- Fondos_Cajas_Audita
CALL crear_indice_si_no_existe('Fondos_Cajas_Audita', 'idx_fondos_audita_sucursal', '`Fk_Sucursal`');
CALL crear_indice_si_no_existe('Fondos_Cajas_Audita', 'idx_fondos_audita_fecha', '`AgregadoEl`');

-- =====================================================
-- SECCIÓN 6: TABLAS DE ABONOS Y CRÉDITOS
-- =====================================================

-- AbonosCreditosVentas
CALL crear_indice_si_no_existe('AbonosCreditosVentas', 'idx_abonos_num_ticket', '`NumTicket`');
CALL crear_indice_si_no_existe('AbonosCreditosVentas', 'idx_abonos_sucursal_fecha', '`Sucursal`, `FechaHora`');
CALL crear_indice_si_no_existe('AbonosCreditosVentas', 'idx_abonos_caja', '`FkCaja`');
CALL crear_indice_si_no_existe('AbonosCreditosVentas', 'idx_abonos_fecha', '`FechaHora`');
CALL crear_indice_si_no_existe('AbonosCreditosVentas', 'idx_abonos_cobrado_por', '`CobradoPor`');

-- AbonosCreditosLiquidaciones
CALL crear_indice_si_no_existe('AbonosCreditosLiquidaciones', 'idx_abonos_liq_num_ticket', '`NumTicket`');
CALL crear_indice_si_no_existe('AbonosCreditosLiquidaciones', 'idx_abonos_liq_sucursal_fecha', '`Sucursal`, `FechaHora`');
CALL crear_indice_si_no_existe('AbonosCreditosLiquidaciones', 'idx_abonos_liq_fecha', '`FechaHora`');
CALL crear_indice_si_no_existe('AbonosCreditosLiquidaciones', 'idx_abonos_liq_caja', '`FkCaja`');

-- Creditos_POS
CALL crear_indice_si_no_existe('Creditos_POS', 'idx_creditos_sucursal', '`Fk_Sucursal`');
CALL crear_indice_si_no_existe('Creditos_POS', 'idx_creditos_fecha_apertura', '`Fecha_Apertura`');
CALL crear_indice_si_no_existe('Creditos_POS', 'idx_creditos_fecha_termino', '`Fecha_Termino`');
CALL crear_indice_si_no_existe('Creditos_POS', 'idx_creditos_nombre', '`Nombre_Cred`(100)');

-- Creditos_POS_Audita
CALL crear_indice_si_no_existe('Creditos_POS_Audita', 'idx_creditos_audita_folio', '`Folio_Credito`');
CALL crear_indice_si_no_existe('Creditos_POS_Audita', 'idx_creditos_audita_sucursal', '`Fk_Sucursal`');
CALL crear_indice_si_no_existe('Creditos_POS_Audita', 'idx_creditos_audita_fecha', '`AgregadoEl`');

-- Areas_Credit_POS
CALL crear_indice_si_no_existe('Areas_Credit_POS', 'idx_areas_cred_nombre', '`Nombre_Area_Cred`');

-- Areas_Credit_POS_Audita
CALL crear_indice_si_no_existe('Areas_Credit_POS_Audita', 'idx_areas_cred_audita_area', '`ID_Area_Cred`');
CALL crear_indice_si_no_existe('Areas_Credit_POS_Audita', 'idx_areas_cred_audita_fecha', '`AgregadoEl`');

-- =====================================================
-- SECCIÓN 7: TABLAS DE TRASPASOS
-- =====================================================

-- Traspasos_generados
CALL crear_indice_si_no_existe('Traspasos_generados', 'idx_traspasos_estatus', '`Estatus`');
CALL crear_indice_si_no_existe('Traspasos_generados', 'idx_traspasos_sucursal_destino', '`Fk_SucDestino`');
CALL crear_indice_si_no_existe('Traspasos_generados', 'idx_traspasos_destino_estatus', '`Fk_SucDestino`, `Estatus`');
CALL crear_indice_si_no_existe('Traspasos_generados', 'idx_traspasos_fecha', '`FechaEntrega`');
CALL crear_indice_si_no_existe('Traspasos_generados', 'idx_traspasos_fecha_agregado', '`AgregadoEl`');
CALL crear_indice_si_no_existe('Traspasos_generados', 'idx_traspasos_cod_barra', '`Cod_Barra`');
CALL crear_indice_si_no_existe('Traspasos_generados', 'idx_traspasos_producto', '`Nombre_Prod`(100)');

-- Traspasos_generados_audita
CALL crear_indice_si_no_existe('Traspasos_generados_audita', 'idx_traspasos_audita_traspaso', '`ID_Traspaso_Generado`');
CALL crear_indice_si_no_existe('Traspasos_generados_audita', 'idx_traspasos_audita_fecha', '`AgregadoEl`');
CALL crear_indice_si_no_existe('Traspasos_generados_audita', 'idx_traspasos_audita_producto', '`ID_Prod_POS`');
CALL crear_indice_si_no_existe('Traspasos_generados_audita', 'idx_traspasos_audita_sucursal', '`Fk_sucursal`');

-- Traspasos_generados_Eliminados
CALL crear_indice_si_no_existe('Traspasos_generados_Eliminados', 'idx_traspasos_eliminados_producto', '`ID_Prod_POS`');
CALL crear_indice_si_no_existe('Traspasos_generados_Eliminados', 'idx_traspasos_eliminados_fecha', '`AgregadoEl`');
CALL crear_indice_si_no_existe('Traspasos_generados_Eliminados', 'idx_traspasos_eliminados_traspaso', '`ID_Traspaso_Generado`');
CALL crear_indice_si_no_existe('Traspasos_generados_Eliminados', 'idx_traspasos_eliminados_cod_barra', '`Cod_Barra`');

-- Traspasos_generados_Entre_sucursales
CALL crear_indice_si_no_existe('Traspasos_generados_Entre_sucursales', 'idx_traspasos_entre_sucursal_destino', '`Fk_SucDestino`');
CALL crear_indice_si_no_existe('Traspasos_generados_Entre_sucursales', 'idx_traspasos_entre_estatus', '`Estatus`');
CALL crear_indice_si_no_existe('Traspasos_generados_Entre_sucursales', 'idx_traspasos_entre_fecha', '`FechaEntrega`');
CALL crear_indice_si_no_existe('Traspasos_generados_Entre_sucursales', 'idx_traspasos_entre_cod_barra', '`Cod_Barra`');

-- Traspasos_Recepcionados
CALL crear_indice_si_no_existe('Traspasos_Recepcionados', 'idx_recepcionados_traspaso', '`ID_Traspaso_Generado`');
CALL crear_indice_si_no_existe('Traspasos_Recepcionados', 'idx_recepcionados_fecha', '`AgregadoEl`');
CALL crear_indice_si_no_existe('Traspasos_Recepcionados', 'idx_recepcionados_sucursal', '`Fk_SucDestino`');
CALL crear_indice_si_no_existe('Traspasos_Recepcionados', 'idx_recepcionados_estatus', '`Estatus`');
CALL crear_indice_si_no_existe('Traspasos_Recepcionados', 'idx_recepcionados_cod_barra', '`Cod_Barra`');

-- TraspasosYNotasC
CALL crear_indice_si_no_existe('TraspasosYNotasC', 'idx_traspasos_notas_estatus', '`Estatus`');
CALL crear_indice_si_no_existe('TraspasosYNotasC', 'idx_traspasos_notas_sucursal', '`Fk_sucursal`');
CALL crear_indice_si_no_existe('TraspasosYNotasC', 'idx_traspasos_notas_destino', '`Fk_SucursalDestino`');
CALL crear_indice_si_no_existe('TraspasosYNotasC', 'idx_traspasos_notas_fecha', '`Fecha_venta`');

-- =====================================================
-- SECCIÓN 8: TABLAS DE CONTEO E INVENTARIO
-- =====================================================

-- ConteosDiarios
CALL crear_indice_si_no_existe('ConteosDiarios', 'idx_conteos_cod_barra', '`Cod_Barra`');
CALL crear_indice_si_no_existe('ConteosDiarios', 'idx_conteos_usuario_fecha', '`AgregadoPor`, `AgregadoEl`');
CALL crear_indice_si_no_existe('ConteosDiarios', 'idx_conteos_sucursal', '`Fk_sucursal`');
CALL crear_indice_si_no_existe('ConteosDiarios', 'idx_conteos_fecha', '`AgregadoEl`');

-- ConteosDiarios_Pausados
CALL crear_indice_si_no_existe('ConteosDiarios_Pausados', 'idx_conteos_pausados_usuario_sucursal', '`AgregadoPor`, `Fk_sucursal`, `EnPausa`');
CALL crear_indice_si_no_existe('ConteosDiarios_Pausados', 'idx_conteos_pausados_cod_barra', '`Cod_Barra`');
CALL crear_indice_si_no_existe('ConteosDiarios_Pausados', 'idx_conteos_pausados_fecha', '`AgregadoEl`');

-- Inventario_Turnos
CALL crear_indice_si_no_existe('Inventario_Turnos', 'idx_turnos_sucursal_estado', '`Fk_sucursal`, `Estado`');
CALL crear_indice_si_no_existe('Inventario_Turnos', 'idx_turnos_usuario_actual', '`Usuario_Actual`');
CALL crear_indice_si_no_existe('Inventario_Turnos', 'idx_turnos_fecha', '`Fecha_Turno`');
CALL crear_indice_si_no_existe('Inventario_Turnos', 'idx_turnos_folio', '`Folio_Turno`');

-- Inventario_Turnos_Historial
CALL crear_indice_si_no_existe('Inventario_Turnos_Historial', 'idx_turnos_hist_turno', '`ID_Turno`');
CALL crear_indice_si_no_existe('Inventario_Turnos_Historial', 'idx_turnos_hist_fecha', '`Fecha_Accion`');
CALL crear_indice_si_no_existe('Inventario_Turnos_Historial', 'idx_turnos_hist_folio', '`Folio_Turno`');

-- Inventario_Turnos_Productos
CALL crear_indice_si_no_existe('Inventario_Turnos_Productos', 'idx_turnos_productos_turno_estado', '`ID_Turno`, `Estado`');
CALL crear_indice_si_no_existe('Inventario_Turnos_Productos', 'idx_turnos_productos_sucursal_estado', '`Fk_sucursal`, `Estado`');
CALL crear_indice_si_no_existe('Inventario_Turnos_Productos', 'idx_turnos_productos_producto', '`ID_Prod_POS`');
CALL crear_indice_si_no_existe('Inventario_Turnos_Productos', 'idx_turnos_productos_fecha', '`Fecha_Conteo`');

-- Inventario_Productos_Bloqueados
CALL crear_indice_si_no_existe('Inventario_Productos_Bloqueados', 'idx_bloqueados_turno', '`ID_Turno`');
CALL crear_indice_si_no_existe('Inventario_Productos_Bloqueados', 'idx_bloqueados_producto', '`ID_Prod_POS`');
CALL crear_indice_si_no_existe('Inventario_Productos_Bloqueados', 'idx_bloqueados_sucursal', '`Fk_sucursal`');
CALL crear_indice_si_no_existe('Inventario_Productos_Bloqueados', 'idx_bloqueados_usuario', '`Usuario_Bloqueo`');

-- InventariosStocks_Conteos
CALL crear_indice_si_no_existe('InventariosStocks_Conteos', 'idx_inv_stocks_producto', '`ID_Prod_POS`');
CALL crear_indice_si_no_existe('InventariosStocks_Conteos', 'idx_inv_stocks_cod_barra', '`Cod_Barra`');
CALL crear_indice_si_no_existe('InventariosStocks_Conteos', 'idx_inv_stocks_fecha', '`Fecha_Conteo`');

-- InventariosSucursales
CALL crear_indice_si_no_existe('InventariosSucursales', 'idx_inv_sucursales_producto', '`ID_Prod_POS`');
CALL crear_indice_si_no_existe('InventariosSucursales', 'idx_inv_sucursales_cod_barra', '`Cod_Barra`');

-- Inventarios_Clinicas
CALL crear_indice_si_no_existe('Inventarios_Clinicas', 'idx_inv_clinicas_producto', '`ID_Prod_POS`');
CALL crear_indice_si_no_existe('Inventarios_Clinicas', 'idx_inv_clinicas_cod_barra', '`Cod_Barra`');
CALL crear_indice_si_no_existe('Inventarios_Clinicas', 'idx_inv_clinicas_fecha', '`Fecha_Inventario`');

-- Inventarios_Clinicas_audita
CALL crear_indice_si_no_existe('Inventarios_Clinicas_audita', 'idx_inv_clinicas_audit_inventario', '`ID_Inv_Clic`');
CALL crear_indice_si_no_existe('Inventarios_Clinicas_audita', 'idx_inv_clinicas_audit_fecha', '`AgregadoEl`');

-- AjustesDeInventarios
CALL crear_indice_si_no_existe('AjustesDeInventarios', 'idx_ajustes_producto', '`ID_Prod_POS`');
CALL crear_indice_si_no_existe('AjustesDeInventarios', 'idx_ajustes_cod_barra', '`Cod_Barra`');
CALL crear_indice_si_no_existe('AjustesDeInventarios', 'idx_ajustes_fecha', '`AgregadoEl`');

-- Inserciones_Excel_inventarios
CALL crear_indice_si_no_existe('Inserciones_Excel_inventarios', 'idx_excel_inv_cod_barra', '`Cod_Barra`');
CALL crear_indice_si_no_existe('Inserciones_Excel_inventarios', 'idx_excel_inv_fecha', '`AgregadoEl`');

-- =====================================================
-- SECCIÓN 9: TABLAS DE DEVOLUCIONES
-- =====================================================

-- Devoluciones
CALL crear_indice_si_no_existe('Devoluciones', 'idx_devoluciones_sucursal_fecha', '`sucursal_id`, `fecha`');
CALL crear_indice_si_no_existe('Devoluciones', 'idx_devoluciones_usuario', '`usuario_id`');
CALL crear_indice_si_no_existe('Devoluciones', 'idx_devoluciones_estatus', '`estatus`');
CALL crear_indice_si_no_existe('Devoluciones', 'idx_devoluciones_folio', '`folio`');

-- Devoluciones_Detalle
CALL crear_indice_si_no_existe('Devoluciones_Detalle', 'idx_devoluciones_detalle_devolucion', '`devolucion_id`');
CALL crear_indice_si_no_existe('Devoluciones_Detalle', 'idx_devoluciones_detalle_producto', '`producto_id`');
CALL crear_indice_si_no_existe('Devoluciones_Detalle', 'idx_devoluciones_detalle_tipo', '`tipo_devolucion`');
CALL crear_indice_si_no_existe('Devoluciones_Detalle', 'idx_devoluciones_detalle_devolucion_tipo', '`devolucion_id`, `tipo_devolucion`');

-- Devoluciones_Acciones
CALL crear_indice_si_no_existe('Devoluciones_Acciones', 'idx_devoluciones_acciones_devolucion', '`devolucion_id`');
CALL crear_indice_si_no_existe('Devoluciones_Acciones', 'idx_devoluciones_acciones_usuario', '`usuario_ejecuta`');
CALL crear_indice_si_no_existe('Devoluciones_Acciones', 'idx_devoluciones_acciones_fecha', '`created_at`');

-- Devoluciones_Autorizaciones
CALL crear_indice_si_no_existe('Devoluciones_Autorizaciones', 'idx_devoluciones_auth_devolucion', '`devolucion_id`');
CALL crear_indice_si_no_existe('Devoluciones_Autorizaciones', 'idx_devoluciones_auth_usuario', '`usuario_autoriza`');
CALL crear_indice_si_no_existe('Devoluciones_Autorizaciones', 'idx_devoluciones_auth_fecha', '`created_at`');

-- Devoluciones_Reportes
CALL crear_indice_si_no_existe('Devoluciones_Reportes', 'idx_devoluciones_reportes_tipo', '`tipo_reporte`');
CALL crear_indice_si_no_existe('Devoluciones_Reportes', 'idx_devoluciones_reportes_fecha', '`fecha_generacion`');
CALL crear_indice_si_no_existe('Devoluciones_Reportes', 'idx_devoluciones_reportes_usuario', '`usuario_genera`');

-- Devolucion_POS
CALL crear_indice_si_no_existe('Devolucion_POS', 'idx_devolucion_pos_fecha', '`Fecha`');
CALL crear_indice_si_no_existe('Devolucion_POS', 'idx_devolucion_pos_cod_barra', '`Cod_Barra`');
CALL crear_indice_si_no_existe('Devolucion_POS', 'idx_devolucion_pos_sucursal', '`Fk_Suc_Salida`');
CALL crear_indice_si_no_existe('Devolucion_POS', 'idx_devolucion_pos_hora', '`HoraAgregado`');

-- =====================================================
-- SECCIÓN 10: TABLAS DE GASTOS
-- =====================================================

-- GastosPOS
CALL crear_indice_si_no_existe('GastosPOS', 'idx_gastos_sucursal_fecha', '`Fk_sucursal`, `FechaConcepto`');
CALL crear_indice_si_no_existe('GastosPOS', 'idx_gastos_tipo', '`Concepto_Categoria`');
CALL crear_indice_si_no_existe('GastosPOS', 'idx_gastos_caja', '`Fk_Caja`');
CALL crear_indice_si_no_existe('GastosPOS', 'idx_gastos_fecha', '`FechaConcepto`');
CALL crear_indice_si_no_existe('GastosPOS', 'idx_gastos_empleado', '`Empleado`(100)');

-- TiposDeGastos
CALL crear_indice_si_no_existe('TiposDeGastos', 'idx_tipos_gastos_nombre', '`Nombre_Gasto`');

-- =====================================================
-- SECCIÓN 11: TABLAS DE PEDIDOS Y ORDENES
-- =====================================================

-- pedidos
CALL crear_indice_si_no_existe('pedidos', 'idx_pedidos_sucursal', '`sucursal_id`');
CALL crear_indice_si_no_existe('pedidos', 'idx_pedidos_estado', '`estado`');
CALL crear_indice_si_no_existe('pedidos', 'idx_pedidos_fecha_creacion', '`fecha_creacion`');
CALL crear_indice_si_no_existe('pedidos', 'idx_pedidos_usuario', '`usuario_id`');
CALL crear_indice_si_no_existe('pedidos', 'idx_pedidos_folio', '`folio`');
CALL crear_indice_si_no_existe('pedidos', 'idx_pedidos_sucursal_estado', '`sucursal_id`, `estado`');

-- pedido_detalles
CALL crear_indice_si_no_existe('pedido_detalles', 'idx_pedido_detalles_pedido', '`pedido_id`');
CALL crear_indice_si_no_existe('pedido_detalles', 'idx_pedido_detalles_producto', '`producto_id`');
CALL crear_indice_si_no_existe('pedido_detalles', 'idx_pedido_detalles_estado', '`estado`');

-- pedido_historial
CALL crear_indice_si_no_existe('pedido_historial', 'idx_pedido_hist_pedido', '`pedido_id`');
CALL crear_indice_si_no_existe('pedido_historial', 'idx_pedido_hist_fecha', '`fecha_cambio`');
CALL crear_indice_si_no_existe('pedido_historial', 'idx_pedido_hist_usuario', '`usuario_id`');

-- Ordenes_Compra_Sugeridas
CALL crear_indice_si_no_existe('Ordenes_Compra_Sugeridas', 'idx_ordenes_folio_stock', '`Folio_Prod_Stock`');
CALL crear_indice_si_no_existe('Ordenes_Compra_Sugeridas', 'idx_ordenes_producto', '`ID_Prod_POS`');

-- =====================================================
-- SECCIÓN 12: TABLAS DE ENCARGOS
-- =====================================================

-- encargos
CALL crear_indice_si_no_existe('encargos', 'idx_encargos_sucursal', '`Fk_Sucursal`');
CALL crear_indice_si_no_existe('encargos', 'idx_encargos_estado', '`estado`');
CALL crear_indice_si_no_existe('encargos', 'idx_encargos_fecha', '`fecha_encargo`');
CALL crear_indice_si_no_existe('encargos', 'idx_encargos_ticket', '`NumTicket`');
CALL crear_indice_si_no_existe('encargos', 'idx_encargos_caja', '`Fk_Caja`');
CALL crear_indice_si_no_existe('encargos', 'idx_encargos_paciente', '`nombre_paciente`(100)');

-- historial_abonos_encargos
CALL crear_indice_si_no_existe('historial_abonos_encargos', 'idx_abonos_encargos_encargo', '`encargo_id`');
CALL crear_indice_si_no_existe('historial_abonos_encargos', 'idx_abonos_encargos_fecha', '`fecha_abono`');
CALL crear_indice_si_no_existe('historial_abonos_encargos', 'idx_abonos_encargos_sucursal', '`sucursal`');

-- =====================================================
-- SECCIÓN 13: TABLAS DE LOTES Y CADUCIDAD
-- =====================================================

-- Historial_Lotes
CALL crear_indice_si_no_existe('Historial_Lotes', 'idx_hist_lotes_producto', '`ID_Prod_POS`');
CALL crear_indice_si_no_existe('Historial_Lotes', 'idx_hist_lotes_sucursal', '`Fk_sucursal`');
CALL crear_indice_si_no_existe('Historial_Lotes', 'idx_hist_lotes_lote', '`Lote`');
CALL crear_indice_si_no_existe('Historial_Lotes', 'idx_hist_lotes_caducidad', '`Fecha_Caducidad`');
CALL crear_indice_si_no_existe('Historial_Lotes', 'idx_hist_lotes_producto_lote', '`ID_Prod_POS`, `Lote`, `Fk_sucursal`');

-- Inventario_lotes_fechas
CALL crear_indice_si_no_existe('Inventario_lotes_fechas', 'idx_lotes_fechas_producto', '`ID_Prod_POS`');
CALL crear_indice_si_no_existe('Inventario_lotes_fechas', 'idx_lotes_fechas_fecha', '`Fecha_Caducidad`');

-- Gestion_Lotes_Movimientos
CALL crear_indice_si_no_existe('Gestion_Lotes_Movimientos', 'idx_gestion_lotes_producto_sucursal', '`ID_Prod_POS`, `Fk_sucursal`');
CALL crear_indice_si_no_existe('Gestion_Lotes_Movimientos', 'idx_gestion_lotes_cod_barra', '`Cod_Barra`');
CALL crear_indice_si_no_existe('Gestion_Lotes_Movimientos', 'idx_gestion_lotes_fecha', '`Fecha_Modificacion`');
CALL crear_indice_si_no_existe('Gestion_Lotes_Movimientos', 'idx_gestion_lotes_lote', '`Lote`');

-- Lotes_Descuentos_Ventas
CALL crear_indice_si_no_existe('Lotes_Descuentos_Ventas', 'idx_lotes_desc_venta', '`Folio_Ticket`');
CALL crear_indice_si_no_existe('Lotes_Descuentos_Ventas', 'idx_lotes_desc_producto_lote', '`ID_Prod_POS`, `Lote`, `Fk_sucursal`');
CALL crear_indice_si_no_existe('Lotes_Descuentos_Ventas', 'idx_lotes_desc_fecha_caducidad', '`Fecha_Caducidad`');

-- caducados_historial
CALL crear_indice_si_no_existe('caducados_historial', 'idx_caducados_hist_lote', '`id_lote`');
CALL crear_indice_si_no_existe('caducados_historial', 'idx_caducados_hist_tipo', '`tipo_movimiento`');
CALL crear_indice_si_no_existe('caducados_historial', 'idx_caducados_hist_fecha', '`fecha_movimiento`');

-- caducados_notificaciones
CALL crear_indice_si_no_existe('caducados_notificaciones', 'idx_caducados_notif_fecha', '`fecha_programada`');
CALL crear_indice_si_no_existe('caducados_notificaciones', 'idx_caducados_notif_estado', '`estado`');
CALL crear_indice_si_no_existe('caducados_notificaciones', 'idx_caducados_notif_tipo', '`tipo_alerta`');
CALL crear_indice_si_no_existe('caducados_notificaciones', 'idx_caducados_notif_lote', '`id_lote`');

-- caducados_configuracion
CALL crear_indice_si_no_existe('caducados_configuracion', 'idx_caducados_config_sucursal', '`sucursal_id`');

-- =====================================================
-- SECCIÓN 14: TABLAS DE INGRESOS
-- =====================================================

-- IngresosAutorizados
CALL crear_indice_si_no_existe('IngresosAutorizados', 'idx_ingresos_auth_producto', '`ID_Prod_POS`');
CALL crear_indice_si_no_existe('IngresosAutorizados', 'idx_ingresos_auth_cod_barra', '`Cod_Barra`');
CALL crear_indice_si_no_existe('IngresosAutorizados', 'idx_ingresos_auth_fecha', '`Fecha_Ingreso`');
CALL crear_indice_si_no_existe('IngresosAutorizados', 'idx_ingresos_auth_sucursal', '`Fk_Sucursal`');

-- IngresosCedis
CALL crear_indice_si_no_existe('IngresosCedis', 'idx_ingresos_cedis_producto', '`ID_Prod_POS`');
CALL crear_indice_si_no_existe('IngresosCedis', 'idx_ingresos_cedis_cod_barra', '`Cod_Barra`');
CALL crear_indice_si_no_existe('IngresosCedis', 'idx_ingresos_cedis_fecha', '`Fecha_Ingreso`');

-- IngresosFarmacias
CALL crear_indice_si_no_existe('IngresosFarmacias', 'idx_ingresos_farmacias_producto', '`ID_Prod_POS`');
CALL crear_indice_si_no_existe('IngresosFarmacias', 'idx_ingresos_farmacias_cod_barra', '`Cod_Barra`');
CALL crear_indice_si_no_existe('IngresosFarmacias', 'idx_ingresos_farmacias_fecha', '`Fecha_Ingreso`');

-- Ingresos_Medicamentos
CALL crear_indice_si_no_existe('Ingresos_Medicamentos', 'idx_ingresos_med_producto', '`ID_Prod_POS`');
CALL crear_indice_si_no_existe('Ingresos_Medicamentos', 'idx_ingresos_med_cod_barra', '`Cod_Barra`');
CALL crear_indice_si_no_existe('Ingresos_Medicamentos', 'idx_ingresos_med_fecha', '`Fecha_Ingreso`');

-- Solicitudes_Ingresos
CALL crear_indice_si_no_existe('Solicitudes_Ingresos', 'idx_solicitudes_producto', '`ID_Prod_POS`');
CALL crear_indice_si_no_existe('Solicitudes_Ingresos', 'idx_solicitudes_sucursal', '`Fk_Sucursal`');
CALL crear_indice_si_no_existe('Solicitudes_Ingresos', 'idx_solicitudes_estatus', '`Estatus`');
CALL crear_indice_si_no_existe('Solicitudes_Ingresos', 'idx_solicitudes_num_orden', '`NumOrden`');
CALL crear_indice_si_no_existe('Solicitudes_Ingresos', 'idx_solicitudes_fecha', '`AgregadoEl`');
CALL crear_indice_si_no_existe('Solicitudes_Ingresos', 'idx_solicitudes_proveedor', '`Proveedor`(100)');

-- Solicitudes_Ingresos_Eliminados
CALL crear_indice_si_no_existe('Solicitudes_Ingresos_Eliminados', 'idx_solicitudes_elim_producto', '`ID_Prod_POS`');
CALL crear_indice_si_no_existe('Solicitudes_Ingresos_Eliminados', 'idx_solicitudes_elim_fecha', '`AgregadoEl`');
CALL crear_indice_si_no_existe('Solicitudes_Ingresos_Eliminados', 'idx_solicitudes_elim_num_orden', '`NumOrden`');

-- logsingresosmedicamentos
CALL crear_indice_si_no_existe('logsingresosmedicamentos', 'idx_logs_ingresos_fecha', '`fecha_log`');
CALL crear_indice_si_no_existe('logsingresosmedicamentos', 'idx_logs_ingresos_producto', '`producto_id`');

-- =====================================================
-- SECCIÓN 15: TABLAS DE CATEGORÍAS, MARCAS, PRESENTACIONES
-- =====================================================

-- Categorias_POS
CALL crear_indice_si_no_existe('Categorias_POS', 'idx_categorias_nombre', '`Nom_Categoria`');

-- Categorias_POS_Updates
CALL crear_indice_si_no_existe('Categorias_POS_Updates', 'idx_categorias_updates_categoria', '`Cat_ID`');
CALL crear_indice_si_no_existe('Categorias_POS_Updates', 'idx_categorias_updates_fecha', '`AgregadoEl`');

-- Marcas_POS
CALL crear_indice_si_no_existe('Marcas_POS', 'idx_marcas_nombre', '`Nom_Marca`');

-- Marcas_POS_Updates
CALL crear_indice_si_no_existe('Marcas_POS_Updates', 'idx_marcas_updates_marca', '`Marca_ID`');
CALL crear_indice_si_no_existe('Marcas_POS_Updates', 'idx_marcas_updates_fecha', '`AgregadoEl`');

-- Presentaciones
CALL crear_indice_si_no_existe('Presentaciones', 'idx_presentaciones_nombre', '`Nom_Presentacion`');
CALL crear_indice_si_no_existe('Presentaciones', 'idx_presentaciones_estado', '`Estado`');

-- Presentacion_Prod_POS_Updates
CALL crear_indice_si_no_existe('Presentacion_Prod_POS_Updates', 'idx_presentaciones_updates_presentacion', '`Pprod_ID`');
CALL crear_indice_si_no_existe('Presentacion_Prod_POS_Updates', 'idx_presentaciones_updates_fecha', '`Agregadoel`');

-- TipProd_POS
CALL crear_indice_si_no_existe('TipProd_POS', 'idx_tip_prod_nombre', '`Nom_TipProd`');

-- TipProd_POS_Audita
CALL crear_indice_si_no_existe('TipProd_POS_Audita', 'idx_tip_prod_audit_tipo', '`Tip_Prod_ID`');
CALL crear_indice_si_no_existe('TipProd_POS_Audita', 'idx_tip_prod_audit_fecha', '`AgregadoEl`');

-- =====================================================
-- SECCIÓN 16: TABLAS DE PROVEEDORES
-- =====================================================

-- Proveedores
CALL crear_indice_si_no_existe('Proveedores', 'idx_proveedores_nombre', '`Nombre_Proveedor`');
CALL crear_fulltext_si_no_existe('Proveedores', 'idx_proveedores_nombre_fulltext', '`Nombre_Proveedor`');

-- producto_proveedor
CALL crear_indice_si_no_existe('producto_proveedor', 'idx_prod_prov_producto', '`producto_id`');
CALL crear_indice_si_no_existe('producto_proveedor', 'idx_prod_prov_proveedor', '`proveedor_id`');

-- proveedores_pedidos
CALL crear_indice_si_no_existe('proveedores_pedidos', 'idx_prov_pedidos_activo', '`activo`');
CALL crear_indice_si_no_existe('proveedores_pedidos', 'idx_prov_pedidos_proveedor', '`proveedor_id`');

-- =====================================================
-- SECCIÓN 17: TABLAS DE SERVICIOS
-- =====================================================

-- Servicios_POS
CALL crear_indice_si_no_existe('Servicios_POS', 'idx_servicios_nombre', '`Nom_Serv`');
CALL crear_indice_si_no_existe('Servicios_POS', 'idx_servicios_estado', '`Estado`');

-- Servicios_POS_Audita
CALL crear_indice_si_no_existe('Servicios_POS_Audita', 'idx_servicios_audit_servicio', '`Servicio_ID`');
CALL crear_indice_si_no_existe('Servicios_POS_Audita', 'idx_servicios_audit_fecha', '`Agregadoel`');

-- ListadoServicios
CALL crear_indice_si_no_existe('ListadoServicios', 'idx_listado_servicios_nombre', '`Nom_Servicio`');

-- =====================================================
-- SECCIÓN 18: TABLAS DE PACIENTES Y FACTURACIÓN
-- =====================================================

-- Data_Pacientes
CALL crear_indice_si_no_existe('Data_Pacientes', 'idx_pacientes_nombre', '`Nombre_Paciente`(100)');
CALL crear_indice_si_no_existe('Data_Pacientes', 'idx_pacientes_sucursal', '`Fk_Sucursal`');
CALL crear_indice_si_no_existe('Data_Pacientes', 'idx_pacientes_telefono', '`Telefono`');
CALL crear_indice_si_no_existe('Data_Pacientes', 'idx_pacientes_fecha_nacimiento', '`Fecha_Nacimiento`');
CALL crear_indice_si_no_existe('Data_Pacientes', 'idx_pacientes_ingreso', '`Ingresadoen`');

-- Data_Pacientes_Updates
CALL crear_indice_si_no_existe('Data_Pacientes_Updates', 'idx_pacientes_updates_paciente', '`ID_Data_Paciente`');
CALL crear_indice_si_no_existe('Data_Pacientes_Updates', 'idx_pacientes_updates_fecha', '`Ingresadoen`');

-- Data_Facturacion_POS
CALL crear_indice_si_no_existe('Data_Facturacion_POS', 'idx_facturacion_ticket', '`Fk_Ticket`');
CALL crear_indice_si_no_existe('Data_Facturacion_POS', 'idx_facturacion_sucursal', '`Fk_Sucursal`');
CALL crear_indice_si_no_existe('Data_Facturacion_POS', 'idx_facturacion_estatus', '`Estatus`');
CALL crear_indice_si_no_existe('Data_Facturacion_POS', 'idx_facturacion_rfc', '`RFC`');
CALL crear_indice_si_no_existe('Data_Facturacion_POS', 'idx_facturacion_fecha', '`Agregadoel`');

-- =====================================================
-- SECCIÓN 19: TABLAS DE NOTIFICACIONES
-- =====================================================

-- Notificaciones
CALL crear_indice_si_no_existe('Notificaciones', 'idx_notificaciones_sucursal', '`SucursalID`');
CALL crear_indice_si_no_existe('Notificaciones', 'idx_notificaciones_tipo', '`Tipo`');
CALL crear_indice_si_no_existe('Notificaciones', 'idx_notificaciones_fecha', '`Fecha`');
CALL crear_indice_si_no_existe('Notificaciones', 'idx_notificaciones_sucursal_tipo', '`SucursalID`, `Tipo`');
CALL crear_indice_si_no_existe('Notificaciones', 'idx_notificaciones_tipo_fecha', '`Tipo`, `Fecha`');

-- Area_De_Notificaciones
CALL crear_indice_si_no_existe('Area_De_Notificaciones', 'idx_area_notif_sucursal', '`SucursalID`');
CALL crear_indice_si_no_existe('Area_De_Notificaciones', 'idx_area_notif_tipo', '`Tipo`');
CALL crear_indice_si_no_existe('Area_De_Notificaciones', 'idx_area_notif_fecha', '`Fecha`');

-- Recordatorios_Pendientes
CALL crear_indice_si_no_existe('Recordatorios_Pendientes', 'idx_recordatorios_sucursal', '`Sucursal`');
CALL crear_indice_si_no_existe('Recordatorios_Pendientes', 'idx_recordatorios_estado', '`Estado`');
CALL crear_indice_si_no_existe('Recordatorios_Pendientes', 'idx_recordatorios_tipo', '`TipoMensaje`');
CALL crear_indice_si_no_existe('Recordatorios_Pendientes', 'idx_recordatorios_fecha', '`Registrado`');

-- recordatorios_sistema
CALL crear_indice_si_no_existe('recordatorios_sistema', 'idx_recordatorios_sis_fecha', '`fecha_programada`');
CALL crear_indice_si_no_existe('recordatorios_sistema', 'idx_recordatorios_sis_estado', '`estado`');
CALL crear_indice_si_no_existe('recordatorios_sistema', 'idx_recordatorios_sis_prioridad', '`prioridad`');
CALL crear_indice_si_no_existe('recordatorios_sistema', 'idx_recordatorios_sis_sucursal', '`sucursal_id`');
CALL crear_indice_si_no_existe('recordatorios_sistema', 'idx_recordatorios_sis_usuario', '`usuario_creador`');

-- recordatorios_destinatarios
CALL crear_indice_si_no_existe('recordatorios_destinatarios', 'idx_recordatorios_dest_recordatorio', '`recordatorio_id`');
CALL crear_indice_si_no_existe('recordatorios_destinatarios', 'idx_recordatorios_dest_usuario', '`usuario_id`');
CALL crear_indice_si_no_existe('recordatorios_destinatarios', 'idx_recordatorios_dest_estado', '`estado_envio`');

-- recordatorios_grupos
CALL crear_indice_si_no_existe('recordatorios_grupos', 'idx_recordatorios_grupos_sucursal', '`sucursal_id`');
CALL crear_indice_si_no_existe('recordatorios_grupos', 'idx_recordatorios_grupos_activo', '`activo`');

-- recordatorios_plantillas
CALL crear_indice_si_no_existe('recordatorios_plantillas', 'idx_recordatorios_plant_tipo', '`tipo`');
CALL crear_indice_si_no_existe('recordatorios_plantillas', 'idx_recordatorios_plant_activo', '`activo`');

-- recordatorios_logs
CALL crear_indice_si_no_existe('recordatorios_logs', 'idx_recordatorios_logs_recordatorio', '`recordatorio_id`');
CALL crear_indice_si_no_existe('recordatorios_logs', 'idx_recordatorios_logs_fecha', '`fecha_log`');

-- =====================================================
-- SECCIÓN 20: TABLAS DE CHAT
-- =====================================================

-- chat_conversaciones (agregar índices adicionales si no existen)
CALL crear_indice_si_no_existe('chat_conversaciones', 'idx_chat_conv_fecha_creacion', '`fecha_creacion`');
CALL crear_indice_si_no_existe('chat_conversaciones', 'idx_chat_conv_activo_fecha', '`activo`, `ultimo_mensaje_fecha`');

-- chat_mensajes (agregar índices adicionales si no existen)
CALL crear_indice_si_no_existe('chat_mensajes', 'idx_chat_msg_conversacion_eliminado', '`conversacion_id`, `eliminado`');
CALL crear_indice_si_no_existe('chat_mensajes', 'idx_chat_msg_usuario_fecha', '`usuario_id`, `fecha_envio`');

-- chat_participantes (agregar índices adicionales si no existen)
CALL crear_indice_si_no_existe('chat_participantes', 'idx_chat_part_usuario_activo', '`usuario_id`, `activo`');

-- =====================================================
-- SECCIÓN 21: TABLAS DE TAREAS
-- =====================================================

-- Tareas
CALL crear_indice_si_no_existe('Tareas', 'idx_tareas_asignado_estado', '`asignado_a`, `estado`');
CALL crear_indice_si_no_existe('Tareas', 'idx_tareas_creado_por', '`creado_por`');
CALL crear_indice_si_no_existe('Tareas', 'idx_tareas_fecha_limite', '`fecha_limite`');
CALL crear_indice_si_no_existe('Tareas', 'idx_tareas_prioridad_estado', '`prioridad`, `estado`');

-- tareas
CALL crear_indice_si_no_existe('tareas', 'idx_tareas_simple_asignado_estado', '`asignado_a`, `estado`');
CALL crear_indice_si_no_existe('tareas', 'idx_tareas_simple_prioridad', '`prioridad`');

-- TareasPorHacer
CALL crear_indice_si_no_existe('TareasPorHacer', 'idx_tareas_por_hacer_estado', '`Estado`');
CALL crear_indice_si_no_existe('TareasPorHacer', 'idx_tareas_por_hacer_fecha', '`Fecha_Tarea`');

-- =====================================================
-- SECCIÓN 22: TABLAS DE ASISTENCIAS Y RECURSOS HUMANOS
-- =====================================================

-- logs_checador
CALL crear_indice_si_no_existe('logs_checador', 'idx_logs_checador_usuario_fecha', '`usuario_id`, `created_at`');
CALL crear_indice_si_no_existe('logs_checador', 'idx_logs_checador_accion_fecha', '`accion`, `created_at`');

-- =====================================================
-- SECCIÓN 23: TABLAS DE ACTUALIZACIONES MASIVAS
-- =====================================================

-- ActualizacionesMasivasProductosPOS
CALL crear_indice_si_no_existe('ActualizacionesMasivasProductosPOS', 'idx_actualizaciones_masivas_producto', '`Id_Actualizado`');
CALL crear_indice_si_no_existe('ActualizacionesMasivasProductosPOS', 'idx_actualizaciones_masivas_fecha', '`ActualizadoEl`');

-- ActualizacionMasivaProductosGlobales
CALL crear_indice_si_no_existe('ActualizacionMasivaProductosGlobales', 'idx_actualizacion_global_producto', '`ID_Prod_POS`');
CALL crear_indice_si_no_existe('ActualizacionMasivaProductosGlobales', 'idx_actualizacion_global_fecha', '`ActualizadoEl`');

-- ActualizacionMaxMin
CALL crear_indice_si_no_existe('ActualizacionMaxMin', 'idx_actualizacion_maxmin_producto', '`ID_Prod_POS`');
CALL crear_indice_si_no_existe('ActualizacionMaxMin', 'idx_actualizacion_maxmin_fecha', '`Fecha_Actualizacion`');

-- =====================================================
-- SECCIÓN 24: TABLAS DE COTIZACIONES
-- =====================================================

-- Cotizaciones
CALL crear_indice_si_no_existe('Cotizaciones', 'idx_cotizaciones_num', '`NumCotizacion`');
CALL crear_indice_si_no_existe('Cotizaciones', 'idx_cotizaciones_sucursal', '`Fk_Sucursal`');
CALL crear_indice_si_no_existe('Cotizaciones', 'idx_cotizaciones_proveedor', '`Proveedor`(100)');
CALL crear_indice_si_no_existe('Cotizaciones', 'idx_cotizaciones_fecha', '`AgregadoEl`');

-- =====================================================
-- SECCIÓN 25: TABLAS DE AGENDA Y LABORATORIOS
-- =====================================================

-- Agenda_Laboratorios
CALL crear_indice_si_no_existe('Agenda_Laboratorios', 'idx_agenda_lab_fecha', '`Fecha_Agenda`');
CALL crear_indice_si_no_existe('Agenda_Laboratorios', 'idx_agenda_lab_paciente', '`Nombre_Paciente`(100)');

-- Agenda_revaloraciones
CALL crear_indice_si_no_existe('Agenda_revaloraciones', 'idx_agenda_reval_fecha', '`Fecha_Agenda`');
CALL crear_indice_si_no_existe('Agenda_revaloraciones', 'idx_agenda_reval_paciente', '`Nombre_Paciente`(100)');

-- =====================================================
-- SECCIÓN 26: TABLAS DE LIMPIEZA
-- =====================================================

-- Bitacora_Limpieza
CALL crear_indice_si_no_existe('Bitacora_Limpieza', 'idx_bitacora_limpieza_fecha', '`fecha_limpieza`');
CALL crear_indice_si_no_existe('Bitacora_Limpieza', 'idx_bitacora_limpieza_usuario', '`usuario_responsable`');

-- Detalle_Limpieza
CALL crear_indice_si_no_existe('Detalle_Limpieza', 'idx_detalle_limpieza_bitacora', '`id_bitacora`');

-- =====================================================
-- SECCIÓN 27: TABLAS DE ERRORES Y LOGS
-- =====================================================

-- Errores_POS
CALL crear_indice_si_no_existe('Errores_POS', 'idx_errores_pos_fecha', '`fecha`');
CALL crear_indice_si_no_existe('Errores_POS', 'idx_errores_pos_sucursal', '`Fk_sucursal`');
CALL crear_indice_si_no_existe('Errores_POS', 'idx_errores_pos_cod_barra', '`Cod_Barra`');

-- Errores_POS_Ventas
CALL crear_indice_si_no_existe('Errores_POS_Ventas', 'idx_errores_ventas_fecha', '`Fecha`');
CALL crear_indice_si_no_existe('Errores_POS_Ventas', 'idx_errores_ventas_producto', '`ID_Prod_POS`');
CALL crear_indice_si_no_existe('Errores_POS_Ventas', 'idx_errores_ventas_sucursal', '`Fk_sucursal`');

-- ErrorLog
CALL crear_indice_si_no_existe('ErrorLog', 'idx_error_log_fecha', '`error_time`');
CALL crear_indice_si_no_existe('ErrorLog', 'idx_error_log_caja', '`Fk_Caja`');
CALL crear_indice_si_no_existe('ErrorLog', 'idx_error_log_codigo', '`error_code`');

-- error_log_act_prod
CALL crear_indice_si_no_existe('error_log_act_prod', 'idx_error_log_act_prod_fecha', '`error_time`');

-- registro_errores_Actualizacionanaqueles
CALL crear_indice_si_no_existe('registro_errores_Actualizacionanaqueles', 'idx_registro_errores_fecha', '`fecha_error`');

-- =====================================================
-- SECCIÓN 28: TABLAS DE PAGOS DE SERVICIOS
-- =====================================================

-- PagosServicios
CALL crear_indice_si_no_existe('PagosServicios', 'idx_pagos_servicios_fecha', '`Fecha_Pago`');
CALL crear_indice_si_no_existe('PagosServicios', 'idx_pagos_servicios_paciente', '`ID_Data_Paciente`');
CALL crear_indice_si_no_existe('PagosServicios', 'idx_pagos_servicios_servicio', '`Servicio_ID`');

-- =====================================================
-- SECCIÓN 29: TABLAS DE SUCURSALES Y UBICACIONES
-- =====================================================

-- Sucursales
CALL crear_indice_si_no_existe('Sucursales', 'idx_sucursales_licencia', '`Licencia`');

-- Estados
CALL crear_indice_si_no_existe('Estados', 'idx_estados_nombre', '`Nombre_Estado`');

-- Municipios
CALL crear_indice_si_no_existe('Municipios', 'idx_municipios_estado', '`Fk_Estado`');
CALL crear_indice_si_no_existe('Municipios', 'idx_municipios_nombre', '`Nombre_Municipio`');

-- Localidades
CALL crear_indice_si_no_existe('Localidades', 'idx_localidades_municipio', '`Fk_Municipio`');
CALL crear_indice_si_no_existe('Localidades', 'idx_localidades_nombre', '`Nombre_Localidad`');

-- =====================================================
-- SECCIÓN 30: TABLAS DE USUARIOS Y TIPOS
-- =====================================================

-- Usuarios_PV
CALL crear_indice_si_no_existe('Usuarios_PV', 'idx_usuarios_pv_nombre', '`Nombre_Apellidos`');
CALL crear_indice_si_no_existe('Usuarios_PV', 'idx_usuarios_pv_usuario', '`Fk_Usuario`');

-- Tipos_Usuarios
CALL crear_indice_si_no_existe('Tipos_Usuarios', 'idx_tipos_usuarios_nombre', '`Nom_TipoUsuario`');

-- =====================================================
-- SECCIÓN 31: TABLAS DE LICENCIAS Y COMPONENTES
-- =====================================================

-- Licencias
CALL crear_indice_si_no_existe('Licencias', 'idx_licencias_numero', '`Num_Licencia`');

-- Componentes
CALL crear_indice_si_no_existe('Componentes', 'idx_componentes_nombre', '`Nom_Componente`');

-- =====================================================
-- SECCIÓN 32: TABLAS DE TIPOS DE ESTUDIOS
-- =====================================================

-- Tipos_estudios
CALL crear_indice_si_no_existe('Tipos_estudios', 'idx_tipos_estudios_tipo', '`Fk_Tipo_analisis`');
CALL crear_indice_si_no_existe('Tipos_estudios', 'idx_tipos_estudios_hod', '`ID_H_O_D`');

-- =====================================================
-- SECCIÓN 33: TABLAS DE SUSCRIPCIONES Y TEMPLATES
-- =====================================================

-- Suscripciones_Push
CALL crear_indice_si_no_existe('Suscripciones_Push', 'idx_suscripciones_usuario', '`Usuario_ID`');
CALL crear_indice_si_no_existe('Suscripciones_Push', 'idx_suscripciones_fecha', '`Fecha_Suscripcion`');

-- templates_downloads
CALL crear_indice_si_no_existe('templates_downloads', 'idx_templates_user', '`user_id`');

-- =====================================================
-- SECCIÓN 34: TABLAS DE REGISTROS DE ENERGÍA
-- =====================================================

-- Registros_Energia
CALL crear_indice_si_no_existe('Registros_Energia', 'idx_registros_energia_fecha', '`Fecha_Registro`');
CALL crear_indice_si_no_existe('Registros_Energia', 'idx_registros_energia_sucursal', '`Fk_Sucursal`');

-- =====================================================
-- SECCIÓN 35: TABLAS DE INVENTARIO INICIAL
-- =====================================================

-- inventario_inicial_estado
CALL crear_indice_si_no_existe('inventario_inicial_estado', 'idx_inv_inicial_sucursal_fecha', '`fkSucursal`, `fecha_establecido`');

-- =====================================================
-- SECCIÓN 36: TABLAS DE CONFIGURACIÓN
-- =====================================================

-- configuracion_checador
CALL crear_indice_si_no_existe('configuracion_checador', 'idx_config_checador_usuario_clave', '`usuario_id`, `clave`');

-- =====================================================
-- SECCIÓN 37: TABLAS DE PRODUCTOS GENERALES
-- =====================================================

-- Productos
CALL crear_indice_si_no_existe('Productos', 'idx_productos_generales_codigo', '`Codigo_Barra`');
CALL crear_indice_si_no_existe('Productos', 'idx_productos_generales_folio', '`Folio_Producto`');
CALL crear_indice_si_no_existe('Productos', 'idx_productos_generales_activo', '`Activo`');

-- =====================================================
-- LIMPIAR PROCEDIMIENTOS TEMPORALES
-- =====================================================

DROP PROCEDURE IF EXISTS crear_indice_si_no_existe;
DROP PROCEDURE IF EXISTS crear_fulltext_si_no_existe;

-- =====================================================
-- ACTUALIZACIÓN DE ESTADÍSTICAS DE TODAS LAS TABLAS PRINCIPALES
-- =====================================================

ANALYZE TABLE `Ventas_POS`;
ANALYZE TABLE `Stock_POS`;
ANALYZE TABLE `Productos_POS`;
ANALYZE TABLE `Cajas`;
ANALYZE TABLE `Traspasos_generados`;
ANALYZE TABLE `ConteosDiarios`;
ANALYZE TABLE `Inventario_Turnos`;
ANALYZE TABLE `Devoluciones`;
ANALYZE TABLE `GastosPOS`;
ANALYZE TABLE `pedidos`;
ANALYZE TABLE `encargos`;
ANALYZE TABLE `CEDIS`;
ANALYZE TABLE `Creditos_POS`;
ANALYZE TABLE `AbonosCreditosVentas`;
ANALYZE TABLE `Solicitudes_Ingresos`;
ANALYZE TABLE `Data_Pacientes`;
ANALYZE TABLE `Notificaciones`;
ANALYZE TABLE `chat_conversaciones`;
ANALYZE TABLE `chat_mensajes`;
ANALYZE TABLE `Tareas`;
ANALYZE TABLE `asistencias`;
ANALYZE TABLE `productos_lotes_caducidad`;
ANALYZE TABLE `Historial_Lotes`;

-- =====================================================
-- FIN DEL SCRIPT SUPER MEGA HIPER OPTIMIZACIÓN
-- =====================================================
-- 
-- NOTAS IMPORTANTES:
-- 1. Este script puede tardar MUCHO tiempo en ejecutarse (30-60 minutos o más)
--    dependiendo del tamaño de las tablas
-- 2. Se recomienda ejecutar durante horas de BAJO tráfico o en mantenimiento programado
-- 3. Los índices FULLTEXT requieren MySQL 5.6+ o MariaDB 10.0.5+
-- 4. Después de crear los índices, las estadísticas se actualizan automáticamente
-- 5. El script valida automáticamente si los índices existen antes de crearlos
-- 6. NO generará errores de índices duplicados (#1061)
-- 7. Para verificar índices: SHOW INDEX FROM nombre_tabla;
-- =====================================================
