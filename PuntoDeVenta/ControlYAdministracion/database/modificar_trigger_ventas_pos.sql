-- =====================================================
-- TRIGGER DE VENTAS: STOCK Y LOTES INDEPENDIENTES
-- =====================================================
-- La venta siempre queda registrada en Ventas_POS (el INSERT ya ocurrió).
-- Si hay fallo (stock insuficiente), se registra en Errores_POS_Ventas
-- PERO igual se descuenta lo que se pueda de stock y lotes.
--
-- STOCK: Resta la venta (o todo el stock disponible si no alcanza).
-- LOTES: Si hay lotes, se descuenta ahí (FEFO). No afecta al stock.

DROP TRIGGER IF EXISTS `RestarExistenciasDespuesInsert`;

DELIMITER $$
CREATE TRIGGER `RestarExistenciasDespuesInsert` AFTER INSERT ON `Ventas_POS` FOR EACH ROW
BEGIN
    DECLARE v_stock INT DEFAULT NULL;
    DECLARE v_error VARCHAR(255);
    DECLARE v_a_restar_stock INT DEFAULT 0;
    DECLARE v_cantidad_restante INT DEFAULT 0;
    DECLARE v_id_historial INT;
    DECLARE v_lote VARCHAR(100);
    DECLARE v_fecha_caducidad DATE;
    DECLARE v_exist_lote INT;
    DECLARE v_a_descontar INT;
    DECLARE v_done INT DEFAULT 0;

    DECLARE cur_lotes CURSOR FOR
        SELECT hl.ID_Historial, hl.Lote, hl.Fecha_Caducidad, hl.Existencias
        FROM Historial_Lotes hl
        WHERE hl.ID_Prod_POS = NEW.ID_Prod_POS
          AND hl.Fk_sucursal = NEW.Fk_sucursal
          AND hl.Existencias > 0
          AND hl.Lote IS NOT NULL AND TRIM(hl.Lote) != ''
          AND LOWER(TRIM(hl.Lote)) NOT IN ('nan', 'null', 'n/a', 'na', 'sin lote')
          AND hl.Fecha_Caducidad IS NOT NULL
          AND hl.Fecha_Caducidad > '1900-01-01'
          AND hl.Fecha_Caducidad != '0000-00-00'
        ORDER BY
          CASE
            WHEN DATEDIFF(hl.Fecha_Caducidad, CURDATE()) < 0 THEN 0
            WHEN DATEDIFF(hl.Fecha_Caducidad, CURDATE()) <= 15 THEN 1
            ELSE 2
          END,
          hl.Fecha_Caducidad ASC;

    DECLARE CONTINUE HANDLER FOR NOT FOUND SET v_done = 1;

    -- Stock actual
    SELECT Existencias_R INTO v_stock
    FROM Stock_POS
    WHERE ID_Prod_POS = NEW.ID_Prod_POS
      AND (Cod_Barra = NEW.Cod_Barra OR NEW.Cod_Barra IS NULL)
      AND Fk_sucursal = NEW.Fk_sucursal
    LIMIT 1;

    IF v_stock IS NULL THEN
        SET v_error = 'Producto no encontrado en inventario.';
        INSERT INTO Errores_POS_Ventas (ID_Prod_POS, Cod_Barra, Fk_sucursal, Cantidad_Venta, Mensaje_Error)
        VALUES (NEW.ID_Prod_POS, NEW.Cod_Barra, NEW.Fk_sucursal, NEW.Cantidad_Venta, v_error);
    ELSE
        -- Registrar fallo si no hay suficiente stock (la venta ya está en Ventas_POS)
        IF v_stock < NEW.Cantidad_Venta THEN
            SET v_error = 'No hay suficientes existencias.';
            INSERT INTO Errores_POS_Ventas (ID_Prod_POS, Cod_Barra, Fk_sucursal, Cantidad_Venta, Mensaje_Error)
            VALUES (NEW.ID_Prod_POS, NEW.Cod_Barra, NEW.Fk_sucursal, NEW.Cantidad_Venta, v_error);
        END IF;

        -- ---------- Descontar la cantidad vendida (permite stock negativo) ----------
        SET v_a_restar_stock = NEW.Cantidad_Venta;
        UPDATE Stock_POS
        SET Existencias_R = Existencias_R - v_a_restar_stock
        WHERE ID_Prod_POS = NEW.ID_Prod_POS
          AND (Cod_Barra = NEW.Cod_Barra OR NEW.Cod_Barra IS NULL)
          AND Fk_sucursal = NEW.Fk_sucursal;

        -- ---------- Siempre descontar lo que se pueda: LOTES (FEFO). No afecta al stock. ----------
        SET v_cantidad_restante = LEAST(NEW.Cantidad_Venta, (
            SELECT COALESCE(SUM(hl.Existencias), 0)
            FROM Historial_Lotes hl
            WHERE hl.ID_Prod_POS = NEW.ID_Prod_POS
              AND hl.Fk_sucursal = NEW.Fk_sucursal
              AND hl.Existencias > 0
              AND hl.Lote IS NOT NULL AND TRIM(hl.Lote) != ''
              AND LOWER(TRIM(hl.Lote)) NOT IN ('nan', 'null', 'n/a', 'na', 'sin lote')
              AND hl.Fecha_Caducidad IS NOT NULL
              AND hl.Fecha_Caducidad > '1900-01-01'
              AND hl.Fecha_Caducidad != '0000-00-00'
        ));
        SET v_done = 0;
        OPEN cur_lotes;

        read_lotes: LOOP
            FETCH cur_lotes INTO v_id_historial, v_lote, v_fecha_caducidad, v_exist_lote;
            IF v_done = 1 OR v_cantidad_restante <= 0 THEN
                LEAVE read_lotes;
            END IF;

            SET v_a_descontar = LEAST(v_cantidad_restante, v_exist_lote);

            UPDATE Historial_Lotes
            SET Existencias = Existencias - v_a_descontar,
                Usuario_Modifico = NEW.AgregadoPor,
                Fecha_Registro = NOW()
            WHERE ID_Historial = v_id_historial;

            SET v_cantidad_restante = v_cantidad_restante - v_a_descontar;
        END LOOP;

        CLOSE cur_lotes;
    END IF;
END$$
DELIMITER ;
