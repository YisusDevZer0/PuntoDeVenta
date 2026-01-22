-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3306
-- Tiempo de generación: 22-01-2026 a las 01:21:07
-- Versión del servidor: 11.8.3-MariaDB-log
-- Versión de PHP: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `u858848268_doctorpez`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `AbonosCreditosLiquidaciones`
--

CREATE TABLE `AbonosCreditosLiquidaciones` (
  `IdAbono` int(11) NOT NULL,
  `FkCaja` int(11) NOT NULL,
  `Turno` varchar(200) NOT NULL,
  `SaldoPrevio` double(50,2) NOT NULL,
  `Abono` double(50,2) NOT NULL,
  `CobradoPor` varchar(200) NOT NULL,
  `FormaPago` varchar(200) NOT NULL,
  `NumTicket` varchar(200) NOT NULL,
  `TicketNuevo` varchar(200) NOT NULL,
  `FechaHora` timestamp NOT NULL,
  `Sucursal` int(12) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Disparadores `AbonosCreditosLiquidaciones`
--
DELIMITER $$
CREATE TRIGGER `actualizar_pagos_tarjeta_liquidaciones` AFTER INSERT ON `AbonosCreditosLiquidaciones` FOR EACH ROW BEGIN
    UPDATE Ventas_POS
    SET Pagos_tarjeta = Pagos_tarjeta - NEW.Abono
    WHERE Folio_Ticket = NEW.NumTicket
      AND Pagos_tarjeta <> 0;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `AbonosCreditosVentas`
--

CREATE TABLE `AbonosCreditosVentas` (
  `IdAbono` int(11) NOT NULL,
  `FkCaja` int(11) NOT NULL,
  `Turno` varchar(200) NOT NULL,
  `SaldoPrevio` double(50,2) NOT NULL,
  `Abono` double(50,2) NOT NULL,
  `NuevoSaldo` double(50,2) NOT NULL,
  `CobradoPor` varchar(200) NOT NULL,
  `FormaPago` varchar(200) NOT NULL,
  `NumTicket` varchar(200) NOT NULL,
  `TicketNuevo` varchar(200) NOT NULL,
  `FechaHora` timestamp NOT NULL,
  `Sucursal` int(12) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Disparadores `AbonosCreditosVentas`
--
DELIMITER $$
CREATE TRIGGER `actualizar_pagos_tarjeta` AFTER INSERT ON `AbonosCreditosVentas` FOR EACH ROW BEGIN
    UPDATE Ventas_POS
    SET Pagos_tarjeta = Pagos_tarjeta - NEW.Abono
    WHERE Folio_Ticket = NEW.NumTicket
      AND Pagos_tarjeta <> 0;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ActualizacionesMasivasProductosPOS`
--

CREATE TABLE `ActualizacionesMasivasProductosPOS` (
  `ID_Prod_POS` int(12) UNSIGNED ZEROFILL NOT NULL,
  `Id_Actualizado` int(10) UNSIGNED ZEROFILL NOT NULL,
  `Cod_Barra` varchar(250) DEFAULT NULL,
  `Clave_adicional` varchar(15) DEFAULT NULL,
  `Nombre_Prod` varchar(1000) DEFAULT NULL,
  `Precio_Venta` decimal(50,2) DEFAULT NULL,
  `Precio_C` decimal(50,2) DEFAULT NULL,
  `Componente_Activo` varchar(250) NOT NULL,
  `Tipo` varchar(500) DEFAULT NULL,
  `FkCategoria` varchar(500) DEFAULT NULL,
  `FkMarca` varchar(500) DEFAULT NULL,
  `FkPresentacion` varchar(500) DEFAULT NULL,
  `Proveedor1` varchar(250) DEFAULT NULL,
  `Proveedor2` varchar(200) NOT NULL,
  `RecetaMedica` varchar(100) DEFAULT NULL,
  `Licencia` varchar(30) NOT NULL,
  `Ivaal16` varchar(100) NOT NULL,
  `ActualizadoPor` varchar(250) NOT NULL,
  `ActualizadoEl` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `Contable` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Disparadores `ActualizacionesMasivasProductosPOS`
--
DELIMITER $$
CREATE TRIGGER `after_insert_ActualizacionesMasivasProductosPOS` AFTER INSERT ON `ActualizacionesMasivasProductosPOS` FOR EACH ROW BEGIN
    UPDATE Productos_POS
    SET
        Cod_Barra = NEW.Cod_Barra,
        Clave_adicional = NEW.Clave_adicional,
        Nombre_Prod = NEW.Nombre_Prod,
        Precio_Venta = NEW.Precio_Venta,
        Precio_C = NEW.Precio_C,
        Componente_Activo = NEW.Componente_Activo,
        Tipo = NEW.Tipo,
        FkCategoria = NEW.FkCategoria,
        FkMarca = NEW.FkMarca,
        FkPresentacion = NEW.FkPresentacion,
        Proveedor1 = NEW.Proveedor1,
        Proveedor2 = NEW.Proveedor2,
        RecetaMedica = NEW.RecetaMedica,
        Ivaal16 = NEW.Ivaal16,
        ActualizadoPor = NEW.ActualizadoPor,
        Contable = NEW.Contable
    WHERE ID_Prod_POS = NEW.Id_Actualizado;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ActualizacionMasivaProductosGlobales`
--

CREATE TABLE `ActualizacionMasivaProductosGlobales` (
  `IdActualizador` int(11) NOT NULL,
  `ID_Prod_POS` int(12) UNSIGNED ZEROFILL NOT NULL,
  `Cod_Barra` varchar(250) DEFAULT NULL,
  `Clave_adicional` varchar(15) DEFAULT NULL,
  `Clave_Levic` varchar(100) NOT NULL,
  `Nombre_Prod` varchar(1000) DEFAULT NULL,
  `Precio_Venta` decimal(50,2) DEFAULT NULL,
  `Precio_C` decimal(50,2) DEFAULT NULL,
  `Tipo_Servicio` int(10) UNSIGNED ZEROFILL DEFAULT NULL,
  `Componente_Activo` varchar(250) NOT NULL,
  `Tipo` varchar(500) DEFAULT NULL,
  `FkCategoria` varchar(500) DEFAULT NULL,
  `FkMarca` varchar(500) DEFAULT NULL,
  `FkPresentacion` varchar(500) DEFAULT NULL,
  `Proveedor1` varchar(250) DEFAULT NULL,
  `Proveedor2` varchar(200) NOT NULL,
  `RecetaMedica` varchar(100) DEFAULT NULL,
  `AgregadoPor` varchar(250) NOT NULL,
  `AgregadoEl` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `Licencia` varchar(30) NOT NULL,
  `Ivaal16` varchar(100) NOT NULL,
  `ActualizadoPor` varchar(250) NOT NULL,
  `ActualizadoEl` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE current_timestamp(),
  `Contable` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Disparadores `ActualizacionMasivaProductosGlobales`
--
DELIMITER $$
CREATE TRIGGER `ActualizaProductosPOS` AFTER INSERT ON `ActualizacionMasivaProductosGlobales` FOR EACH ROW BEGIN
    -- Verificamos si el producto existe en Productos_POS
    IF EXISTS (
        SELECT 1 
        FROM Productos_POS 
        WHERE ID_Prod_POS = NEW.ID_Prod_POS
    ) THEN
        -- Si existe, actualizamos los datos
        UPDATE Productos_POS
        SET 
            Cod_Barra = NEW.Cod_Barra,
            Clave_adicional = NEW.Clave_adicional,
            Clave_Levic = NEW.Clave_Levic,
            Nombre_Prod = NEW.Nombre_Prod,
            Precio_Venta = NEW.Precio_Venta,
            Precio_C = NEW.Precio_C,
            Tipo_Servicio = NEW.Tipo_Servicio,
            Componente_Activo = NEW.Componente_Activo,
            Tipo = NEW.Tipo,
            FkCategoria = NEW.FkCategoria,
            FkMarca = NEW.FkMarca,
            FkPresentacion = NEW.FkPresentacion,
            Proveedor1 = NEW.Proveedor1,
            Proveedor2 = NEW.Proveedor2,
            RecetaMedica = NEW.RecetaMedica,
            AgregadoPor = NEW.AgregadoPor,
            AgregadoEl = NEW.AgregadoEl,
            Licencia = NEW.Licencia,
            Ivaal16 = NEW.Ivaal16,
            ActualizadoPor = NEW.ActualizadoPor,
            ActualizadoEl = NEW.ActualizadoEl,
            Contable = NEW.Contable
        WHERE ID_Prod_POS = NEW.ID_Prod_POS;
    ELSE
        -- Si no existe, se puede omitir o manejar según sea necesario
        -- Por ejemplo, podrías insertar un nuevo registro:
        INSERT INTO Productos_POS (
            ID_Prod_POS, Cod_Barra, Clave_adicional, Clave_Levic, 
            Nombre_Prod, Precio_Venta, Precio_C, Tipo_Servicio, 
            Componente_Activo, Tipo, FkCategoria, FkMarca, 
            FkPresentacion, Proveedor1, Proveedor2, RecetaMedica, 
            AgregadoPor, AgregadoEl, Licencia, Ivaal16, 
            ActualizadoPor, ActualizadoEl, Contable
        ) VALUES (
            NEW.ID_Prod_POS, NEW.Cod_Barra, NEW.Clave_adicional, NEW.Clave_Levic, 
            NEW.Nombre_Prod, NEW.Precio_Venta, NEW.Precio_C, NEW.Tipo_Servicio, 
            NEW.Componente_Activo, NEW.Tipo, NEW.FkCategoria, NEW.FkMarca, 
            NEW.FkPresentacion, NEW.Proveedor1, NEW.Proveedor2, NEW.RecetaMedica, 
            NEW.AgregadoPor, NEW.AgregadoEl, NEW.Licencia, NEW.Ivaal16, 
            NEW.ActualizadoPor, NEW.ActualizadoEl, NEW.Contable
        );
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ActualizacionMaxMin`
--

CREATE TABLE `ActualizacionMaxMin` (
  `id` int(11) NOT NULL,
  `Folio_Prod_Stock` varchar(50) NOT NULL,
  `ID_Prod_POS` varchar(50) NOT NULL,
  `Cod_Barra` varchar(50) DEFAULT NULL,
  `Nombre_Prod` varchar(255) DEFAULT NULL,
  `Fk_sucursal` int(11) NOT NULL,
  `Nombre_Sucursal` varchar(255) DEFAULT NULL,
  `Max_Existencia` int(11) NOT NULL,
  `Min_Existencia` int(11) NOT NULL,
  `AgregadoPor` varchar(255) NOT NULL,
  `FechaAgregado` datetime DEFAULT current_timestamp(),
  `ActualizadoPor` varchar(255) DEFAULT NULL,
  `FechaActualizado` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Disparadores `ActualizacionMaxMin`
--
DELIMITER $$
CREATE TRIGGER `ActualizarStockPOS` AFTER INSERT ON `ActualizacionMaxMin` FOR EACH ROW BEGIN
    UPDATE Stock_POS
    SET 
        Max_Existencia = NEW.Max_Existencia,
        Min_Existencia = NEW.Min_Existencia,
        ActualizadoPor = NEW.ActualizadoPor,
        ActualizoFecha = NEW.FechaActualizado,
        JustificacionAjuste = 'Actualización mínimo y/o máximo' -- Nuevo campo con valor fijo
    WHERE Folio_Prod_Stock = NEW.Folio_Prod_Stock
      AND Fk_sucursal = NEW.Fk_sucursal; -- Asegurarse de que coincida con la sucursal correcta
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Agenda_Laboratorios`
--

CREATE TABLE `Agenda_Laboratorios` (
  `Id_agenda` int(11) NOT NULL,
  `Nombres_Apellidos` varchar(150) NOT NULL,
  `Telefono` varchar(12) NOT NULL,
  `Fk_sucursal` int(11) NOT NULL,
  `Medico` varchar(150) NOT NULL,
  `Fecha` date NOT NULL,
  `Turno` varchar(100) NOT NULL,
  `Motivo_Consulta` varchar(150) NOT NULL,
  `Asistio` varchar(100) NOT NULL,
  `Agrego` varchar(150) NOT NULL,
  `ActualizoEstado` varchar(100) NOT NULL,
  `AgregadoEl` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Agenda_revaloraciones`
--

CREATE TABLE `Agenda_revaloraciones` (
  `Id_agenda` int(11) NOT NULL,
  `Nombres_Apellidos` varchar(150) NOT NULL,
  `Telefono` varchar(12) NOT NULL,
  `Fk_sucursal` int(11) NOT NULL,
  `Medico` varchar(150) NOT NULL,
  `Fecha` date NOT NULL,
  `Turno` varchar(100) NOT NULL,
  `Motivo_Consulta` varchar(150) NOT NULL,
  `Asistio` varchar(100) NOT NULL,
  `Agrego` varchar(150) NOT NULL,
  `ActualizoEstado` varchar(100) NOT NULL,
  `AgregadoEl` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `AjustesDeInventarios`
--

CREATE TABLE `AjustesDeInventarios` (
  `Folio_Ingreso` int(10) UNSIGNED ZEROFILL NOT NULL,
  `ID_Prod_POS` int(12) UNSIGNED ZEROFILL NOT NULL,
  `Fk_sucursal` int(12) NOT NULL,
  `Existencias_R` int(11) NOT NULL,
  `ExistenciaPrev` int(11) NOT NULL,
  `Recibido` int(11) NOT NULL,
  `AgregadoPor` varchar(250) NOT NULL,
  `AgregadoEl` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `ID_H_O_D` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Areas_Credit_POS`
--

CREATE TABLE `Areas_Credit_POS` (
  `ID_Area_Cred` int(10) UNSIGNED ZEROFILL NOT NULL,
  `Nombre_Area` varchar(250) NOT NULL,
  `Estatus` varchar(200) NOT NULL,
  `CodigoEstatus` varchar(200) NOT NULL,
  `Agrega` varchar(200) NOT NULL,
  `Agregadoel` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `Sistema` varchar(200) NOT NULL,
  `ID_H_O_D` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Disparadores `Areas_Credit_POS`
--
DELIMITER $$
CREATE TRIGGER `Audita_AreaCred` AFTER INSERT ON `Areas_Credit_POS` FOR EACH ROW INSERT INTO Areas_Credit_POS_Audita
    (ID_Area_Cred,Nombre_Area,	Estatus,CodigoEstatus,Agrega,Agregadoel,Sistema,ID_H_O_D)
    VALUES (NEW.ID_Area_Cred,NEW.Nombre_Area,NEW.Estatus,NEW.CodigoEstatus,NEW.Agrega,Now(),NEW.Sistema,NEW.ID_H_O_D)
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `Audita_AreaCred_Updates` AFTER UPDATE ON `Areas_Credit_POS` FOR EACH ROW INSERT INTO Areas_Credit_POS_Audita
    (ID_Area_Cred,Nombre_Area,	Estatus,CodigoEstatus,Agrega,Agregadoel,Sistema,ID_H_O_D)
    VALUES (NEW.ID_Area_Cred,NEW.Nombre_Area,NEW.Estatus,NEW.CodigoEstatus,NEW.Agrega,Now(),NEW.Sistema,NEW.ID_H_O_D)
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Areas_Credit_POS_Audita`
--

CREATE TABLE `Areas_Credit_POS_Audita` (
  `ID_Audita_Ar_Cred` int(11) NOT NULL,
  `ID_Area_Cred` int(10) UNSIGNED ZEROFILL NOT NULL,
  `Nombre_Area` varchar(250) NOT NULL,
  `Estatus` varchar(200) NOT NULL,
  `CodigoEstatus` varchar(200) NOT NULL,
  `Agrega` varchar(200) NOT NULL,
  `Agregadoel` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `Sistema` varchar(200) NOT NULL,
  `ID_H_O_D` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Area_De_Notificaciones`
--

CREATE TABLE `Area_De_Notificaciones` (
  `ID_Notificacion` int(11) NOT NULL,
  `Encabezado` varchar(200) NOT NULL,
  `Tipo_Notificacion` varchar(200) NOT NULL,
  `Mensaje_Notificacion` varchar(500) NOT NULL,
  `Registrado` varchar(200) NOT NULL,
  `Sistema` varchar(150) NOT NULL,
  `Sucursal` int(11) NOT NULL,
  `Estado` int(11) NOT NULL,
  `ID_H_O_D` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `asistencias`
--

CREATE TABLE `asistencias` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `tipo` enum('entrada','salida') NOT NULL,
  `latitud` decimal(10,8) NOT NULL,
  `longitud` decimal(11,8) NOT NULL,
  `fecha_hora` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Disparadores `asistencias`
--
DELIMITER $$
CREATE TRIGGER `tr_asistencias_log` AFTER INSERT ON `asistencias` FOR EACH ROW BEGIN
    INSERT INTO logs_checador (usuario_id, accion, detalles, created_at)
    VALUES (
        NEW.usuario_id, 
        CONCAT('registro_', NEW.tipo), 
        CONCAT('Registro de ', NEW.tipo, ' en ', NEW.latitud, ',', NEW.longitud),
        NOW()
    );
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Bitacora_Limpieza`
--

CREATE TABLE `Bitacora_Limpieza` (
  `id_bitacora` int(11) NOT NULL,
  `area` varchar(100) NOT NULL,
  `semana` varchar(50) NOT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date NOT NULL,
  `responsable` varchar(100) NOT NULL,
  `supervisor` varchar(100) NOT NULL,
  `aux_res` varchar(100) NOT NULL,
  `sucursal_id` int(11) DEFAULT NULL,
  `firma_responsable` varchar(255) DEFAULT NULL,
  `firma_supervisor` varchar(255) DEFAULT NULL,
  `firma_aux_res` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `caducados_configuracion`
--

CREATE TABLE `caducados_configuracion` (
  `id_config` int(11) NOT NULL,
  `sucursal_id` int(11) NOT NULL,
  `dias_alerta_3_meses` int(11) DEFAULT 90,
  `dias_alerta_6_meses` int(11) DEFAULT 180,
  `dias_alerta_9_meses` int(11) DEFAULT 270,
  `notificaciones_activas` tinyint(1) DEFAULT 1,
  `email_responsable` varchar(255) DEFAULT NULL,
  `telefono_whatsapp` varchar(20) DEFAULT NULL,
  `usuario_responsable` int(11) DEFAULT NULL,
  `fecha_configuracion` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `caducados_historial`
--

CREATE TABLE `caducados_historial` (
  `id_historial` int(11) NOT NULL,
  `id_lote` int(11) NOT NULL,
  `tipo_movimiento` enum('registro','actualizacion','transferencia','venta','ajuste','vencimiento') NOT NULL,
  `cantidad_anterior` int(11) DEFAULT NULL,
  `cantidad_nueva` int(11) DEFAULT NULL,
  `fecha_caducidad_anterior` date DEFAULT NULL,
  `fecha_caducidad_nueva` date DEFAULT NULL,
  `sucursal_origen` int(11) DEFAULT NULL,
  `sucursal_destino` int(11) DEFAULT NULL,
  `usuario_movimiento` int(11) NOT NULL,
  `fecha_movimiento` timestamp NULL DEFAULT current_timestamp(),
  `observaciones` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `caducados_notificaciones`
--

CREATE TABLE `caducados_notificaciones` (
  `id_notificacion` int(11) NOT NULL,
  `id_lote` int(11) NOT NULL,
  `tipo_alerta` enum('3_meses','6_meses','9_meses','vencido') NOT NULL,
  `fecha_programada` date NOT NULL,
  `estado` enum('pendiente','enviada','leida','cancelada') DEFAULT 'pendiente',
  `mensaje` text NOT NULL,
  `destinatario` varchar(255) NOT NULL,
  `fecha_envio` datetime DEFAULT NULL,
  `fecha_creacion` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Cajas`
--

CREATE TABLE `Cajas` (
  `ID_Caja` int(10) UNSIGNED ZEROFILL NOT NULL,
  `Fk_Fondo` int(10) UNSIGNED ZEROFILL NOT NULL,
  `Cantidad_Fondo` decimal(50,2) NOT NULL,
  `Empleado` varchar(250) NOT NULL,
  `Sucursal` int(11) NOT NULL,
  `Estatus` varchar(200) NOT NULL,
  `CodigoEstatus` varchar(250) NOT NULL,
  `Fecha_Apertura` date NOT NULL,
  `Turno` varchar(300) NOT NULL,
  `Asignacion` int(11) NOT NULL,
  `Valor_Total_Caja` decimal(50,2) NOT NULL,
  `Hora_apertura` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `Hora_real_apertura` timestamp NOT NULL DEFAULT current_timestamp(),
  `Sistema` varchar(200) NOT NULL,
  `Licencia` varchar(200) NOT NULL,
  `MedicoEnturno` varchar(200) NOT NULL,
  `EnfermeroEnturno` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Disparadores `Cajas`
--
DELIMITER $$
CREATE TRIGGER `Audita_Caja` AFTER INSERT ON `Cajas` FOR EACH ROW INSERT INTO Cajas_POS_Audita
    (ID_Caja,Fk_Fondo,Cantidad_Fondo,Empleado,Sucursal,Estatus,CodigoEstatus,Fecha_Apertura,Turno,Asignacion,Valor_Total_Caja,Hora_apertura,Sistema,Licencia)
    VALUES (NEW.ID_Caja,NEW.Fk_Fondo,NEW.Cantidad_Fondo,NEW.Empleado,NEW.Sucursal,NEW.Estatus,NEW.CodigoEstatus,NEW.Fecha_Apertura,NEW.Turno,NEW.Asignacion,NEW.Valor_Total_Caja,Now(),NEW.Sistema,NEW.Licencia)
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Cajas_POS_Audita`
--

CREATE TABLE `Cajas_POS_Audita` (
  `ID_Caja_Audita` int(11) NOT NULL,
  `ID_Caja` int(10) UNSIGNED ZEROFILL NOT NULL,
  `Fk_Fondo` int(10) UNSIGNED ZEROFILL NOT NULL,
  `Cantidad_Fondo` decimal(50,2) NOT NULL,
  `Empleado` varchar(250) NOT NULL,
  `Sucursal` int(11) NOT NULL,
  `Estatus` varchar(200) NOT NULL,
  `CodigoEstatus` varchar(250) NOT NULL,
  `Fecha_Apertura` date NOT NULL,
  `Turno` varchar(300) NOT NULL,
  `Asignacion` int(11) NOT NULL,
  `Valor_Total_Caja` decimal(50,2) NOT NULL,
  `Hora_apertura` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `Sistema` varchar(200) NOT NULL,
  `Licencia` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Categorias_POS`
--

CREATE TABLE `Categorias_POS` (
  `Cat_ID` int(11) UNSIGNED ZEROFILL NOT NULL,
  `Nom_Cat` varchar(200) NOT NULL,
  `Estado` varchar(100) NOT NULL,
  `Cod_Estado` varchar(200) NOT NULL,
  `Agregado_Por` varchar(250) NOT NULL,
  `Agregadoel` timestamp NOT NULL DEFAULT current_timestamp(),
  `Sistema` varchar(250) NOT NULL,
  `Licencia` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Categorias_POS_Updates`
--

CREATE TABLE `Categorias_POS_Updates` (
  `ID_Update` int(11) NOT NULL,
  `Cat_ID` int(11) UNSIGNED ZEROFILL NOT NULL,
  `Nom_Cat` varchar(200) NOT NULL,
  `Estado` varchar(100) NOT NULL,
  `Cod_Estado` varchar(200) NOT NULL,
  `Agregado_Por` varchar(250) NOT NULL,
  `Agregadoel` timestamp NOT NULL DEFAULT current_timestamp(),
  `Sistema` varchar(250) NOT NULL,
  `ID_H_O_D` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `CEDIS`
--

CREATE TABLE `CEDIS` (
  `IdProdCedis` int(12) UNSIGNED ZEROFILL NOT NULL,
  `ID_Prod_POS` int(12) UNSIGNED ZEROFILL NOT NULL,
  `Cod_Barra` varchar(250) DEFAULT NULL,
  `Clave_adicional` varchar(15) DEFAULT NULL,
  `Clave_Levic` varchar(100) NOT NULL,
  `Nombre_Prod` varchar(1000) DEFAULT NULL,
  `Precio_Venta` decimal(50,2) DEFAULT NULL,
  `Precio_C` decimal(50,2) DEFAULT NULL,
  `Lote_Med` varchar(200) DEFAULT NULL,
  `Fecha_Caducidad` date DEFAULT NULL,
  `Existencias` int(11) NOT NULL,
  `Tipo_Servicio` int(10) UNSIGNED ZEROFILL DEFAULT NULL,
  `Componente_Activo` varchar(250) NOT NULL,
  `Tipo` varchar(500) DEFAULT NULL,
  `FkCategoria` varchar(500) DEFAULT NULL,
  `FkMarca` varchar(500) DEFAULT NULL,
  `FkPresentacion` varchar(500) DEFAULT NULL,
  `Proveedor1` varchar(250) DEFAULT NULL,
  `Proveedor2` varchar(200) NOT NULL,
  `RecetaMedica` varchar(100) DEFAULT NULL,
  `Estatus` varchar(150) DEFAULT NULL,
  `AgregadoPor` varchar(250) NOT NULL,
  `AgregadoEl` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `ActualizadoEl` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE current_timestamp(),
  `ActualizadoPor` varchar(200) NOT NULL,
  `Contable` varchar(200) NOT NULL,
  `Licencia` varchar(300) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Disparadores `CEDIS`
--
DELIMITER $$
CREATE TRIGGER `BeforeDeleteFromCedis` BEFORE DELETE ON `CEDIS` FOR EACH ROW BEGIN
    INSERT INTO CEDIS_Eliminados (
        IdProdCedis, 
        ID_Prod_POS, 
        Cod_Barra, 
        Clave_adicional, 
        Clave_Levic, 
        Nombre_Prod, 
        Precio_Venta, 
        Precio_C, 
        Lote_Med, 
        Fecha_Caducidad, 
        Existencias, 
        Tipo_Servicio, 
        Componente_Activo, 
        Tipo, 
        FkCategoria, 
        FkMarca, 
        FkPresentacion, 
        Proveedor1, 
        Proveedor2, 
        RecetaMedica, 
        Estatus, 
        AgregadoPor, 
        AgregadoEl, 
        ActualizadoEl, 
        ActualizadoPor, 
        Contable
    )
    VALUES (
        OLD.IdProdCedis, 
        OLD.ID_Prod_POS, 
        OLD.Cod_Barra, 
        OLD.Clave_adicional, 
        OLD.Clave_Levic, 
        OLD.Nombre_Prod, 
        OLD.Precio_Venta, 
        OLD.Precio_C, 
        OLD.Lote_Med, 
        OLD.Fecha_Caducidad, 
        OLD.Existencias, 
        OLD.Tipo_Servicio, 
        OLD.Componente_Activo, 
        OLD.Tipo, 
        OLD.FkCategoria, 
        OLD.FkMarca, 
        OLD.FkPresentacion, 
        OLD.Proveedor1, 
        OLD.Proveedor2, 
        OLD.RecetaMedica, 
        OLD.Estatus, 
        OLD.AgregadoPor, 
        OLD.AgregadoEl, 
        OLD.ActualizadoEl, 
        OLD.ActualizadoPor, 
        OLD.Contable
    );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `insertar_en_stock_pos` AFTER INSERT ON `CEDIS` FOR EACH ROW BEGIN
    -- Insertar en Stock_POS para cada sucursal
    INSERT INTO Stock_POS (ID_Prod_POS, Clave_adicional, Clave_Levic, Cod_Barra, Nombre_Prod, Fk_sucursal, Precio_Venta, Precio_C, Max_Existencia, Min_Existencia, Existencias_R, Lote, Fecha_Caducidad, Fecha_Ingreso, Tipo_Servicio, Tipo, FkCategoria, FkMarca, FkPresentacion, Proveedor1, Proveedor2, Estatus, Sistema, AgregadoPor,ID_H_O_D,Contable)
    SELECT NEW.ID_Prod_POS, NEW.Clave_adicional, NEW.Clave_Levic, NEW.Cod_Barra, NEW.Nombre_Prod, S.ID_Sucursal, NEW.Precio_Venta, NEW.Precio_C, NULL, NULL, NULL, NEW.Lote_Med, NEW.Fecha_Caducidad, CURRENT_TIMESTAMP(), NEW.Tipo_Servicio, NEW.Tipo, NEW.FkCategoria, NEW.FkMarca, NEW.FkPresentacion, NEW.Proveedor1, NEW.Proveedor2, NEW.Estatus, 'Sistema', NEW.AgregadoPor,NEW.Licencia,NEW.Contable
    FROM Sucursales S;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `CEDIS_Eliminados`
--

CREATE TABLE `CEDIS_Eliminados` (
  `Id_CedisEliminado` int(10) UNSIGNED ZEROFILL NOT NULL,
  `IdProdCedis` int(12) UNSIGNED ZEROFILL NOT NULL,
  `ID_Prod_POS` int(12) UNSIGNED ZEROFILL NOT NULL,
  `Cod_Barra` varchar(250) DEFAULT NULL,
  `Clave_adicional` varchar(15) DEFAULT NULL,
  `Clave_Levic` varchar(100) NOT NULL,
  `Nombre_Prod` varchar(1000) DEFAULT NULL,
  `Precio_Venta` decimal(50,2) DEFAULT NULL,
  `Precio_C` decimal(50,2) DEFAULT NULL,
  `Lote_Med` varchar(200) DEFAULT NULL,
  `Fecha_Caducidad` date DEFAULT NULL,
  `Existencias` int(11) NOT NULL,
  `Tipo_Servicio` int(10) UNSIGNED ZEROFILL DEFAULT NULL,
  `Componente_Activo` varchar(250) NOT NULL,
  `Tipo` varchar(500) DEFAULT NULL,
  `FkCategoria` varchar(500) DEFAULT NULL,
  `FkMarca` varchar(500) DEFAULT NULL,
  `FkPresentacion` varchar(500) DEFAULT NULL,
  `Proveedor1` varchar(250) DEFAULT NULL,
  `Proveedor2` varchar(200) NOT NULL,
  `RecetaMedica` varchar(100) DEFAULT NULL,
  `Estatus` varchar(150) DEFAULT NULL,
  `AgregadoPor` varchar(250) NOT NULL,
  `AgregadoEl` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `ActualizadoEl` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE current_timestamp(),
  `ActualizadoPor` varchar(200) NOT NULL,
  `Contable` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Cedis_Inventarios`
--

CREATE TABLE `Cedis_Inventarios` (
  `IdProdCedis` int(12) UNSIGNED ZEROFILL NOT NULL,
  `ID_Prod_POS` int(12) UNSIGNED ZEROFILL NOT NULL,
  `Cod_Barra` varchar(250) DEFAULT NULL,
  `Nombre_Prod` varchar(1000) DEFAULT NULL,
  `Contabilizado` int(11) NOT NULL,
  `StockEnMomento` int(11) NOT NULL,
  `ExistenciasAjuste` int(11) NOT NULL,
  `Precio_Venta` decimal(50,2) DEFAULT NULL,
  `Precio_C` decimal(50,2) DEFAULT NULL,
  `AgregadoPor` varchar(250) NOT NULL,
  `AgregadoEl` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `FechaInventario` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Disparadores `Cedis_Inventarios`
--
DELIMITER $$
CREATE TRIGGER `trg_after_insert_ajustes_inventario` AFTER INSERT ON `Cedis_Inventarios` FOR EACH ROW BEGIN
    DECLARE v_existencias INT;
    DECLARE v_diferencia INT;

    -- Obtener las existencias actuales de la tabla CEDIS
    SELECT Existencias
    INTO v_existencias
    FROM CEDIS
    WHERE Cod_Barra = NEW.Cod_Barra;

    -- Si existen registros previos, calcular la diferencia y actualizar
    IF v_existencias IS NOT NULL THEN
        -- Calcular la diferencia
        SET v_diferencia = NEW.Contabilizado - v_existencias;
        
        -- Ajustar las existencias sumando solo la diferencia
        UPDATE CEDIS
        SET Existencias = Existencias + v_diferencia
        WHERE Cod_Barra = NEW.Cod_Barra;
    ELSE
        -- Si no existe el Cod_Barra en la tabla CEDIS, insertar un nuevo registro.
        INSERT INTO CEDIS (Cod_Barra, Existencias)
        VALUES (NEW.Cod_Barra, NEW.Contabilizado);
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `chat_configuraciones`
--

CREATE TABLE `chat_configuraciones` (
  `id_config` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `notificaciones_sonido` tinyint(1) NOT NULL DEFAULT 1,
  `notificaciones_push` tinyint(1) NOT NULL DEFAULT 1,
  `notificaciones_email` tinyint(1) NOT NULL DEFAULT 0,
  `tema_oscuro` tinyint(1) NOT NULL DEFAULT 0,
  `mensajes_por_pagina` int(11) NOT NULL DEFAULT 50,
  `auto_borrar_mensajes` int(11) DEFAULT NULL COMMENT 'Días para auto-eliminar mensajes (NULL = nunca)',
  `idioma` varchar(5) NOT NULL DEFAULT 'es',
  `zona_horaria` varchar(50) NOT NULL DEFAULT 'America/Monterrey',
  `mostrar_online` tinyint(1) NOT NULL DEFAULT 1,
  `mostrar_ultima_vez` tinyint(1) NOT NULL DEFAULT 1,
  `auto_descargar_archivos` tinyint(1) NOT NULL DEFAULT 0,
  `tamaño_maximo_archivo` int(11) NOT NULL DEFAULT 10485760 COMMENT 'En bytes (10MB por defecto)',
  `fecha_actualizacion` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `configuracion_avanzada` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`configuracion_avanzada`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Configuraciones de chat por usuario';

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `chat_conversaciones`
--

CREATE TABLE `chat_conversaciones` (
  `id_conversacion` int(11) NOT NULL,
  `nombre_conversacion` varchar(255) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `tipo_conversacion` enum('individual','grupo','sucursal','general','canal') NOT NULL DEFAULT 'individual',
  `sucursal_id` int(11) DEFAULT NULL,
  `creado_por` int(11) NOT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_actualizacion` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `ultimo_mensaje` text DEFAULT NULL,
  `ultimo_mensaje_fecha` timestamp NULL DEFAULT NULL,
  `ultimo_mensaje_usuario_id` int(11) DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `privado` tinyint(1) NOT NULL DEFAULT 0,
  `archivado` tinyint(1) NOT NULL DEFAULT 0,
  `configuracion` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`configuracion`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Conversaciones y grupos de chat';

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `chat_estados_usuario`
--

CREATE TABLE `chat_estados_usuario` (
  `id_estado` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `estado` enum('online','offline','ausente','ocupado','invisible') NOT NULL DEFAULT 'offline',
  `ultima_actividad` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `dispositivo` varchar(50) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `ubicacion` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Estados de conexión de usuarios';

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `chat_lecturas`
--

CREATE TABLE `chat_lecturas` (
  `id_lectura` int(11) NOT NULL,
  `mensaje_id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `fecha_lectura` timestamp NOT NULL DEFAULT current_timestamp(),
  `dispositivo` varchar(50) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Estados de lectura de mensajes';

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `chat_mensajes`
--

CREATE TABLE `chat_mensajes` (
  `id_mensaje` int(11) NOT NULL,
  `conversacion_id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `mensaje` text NOT NULL,
  `tipo_mensaje` enum('texto','imagen','video','audio','archivo','sistema','sticker','encuesta') NOT NULL DEFAULT 'texto',
  `archivo_url` varchar(500) DEFAULT NULL,
  `archivo_nombre` varchar(255) DEFAULT NULL,
  `archivo_tipo` varchar(100) DEFAULT NULL,
  `archivo_tamaño` bigint(20) DEFAULT NULL,
  `archivo_hash` varchar(64) DEFAULT NULL,
  `fecha_envio` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_edicion` timestamp NULL DEFAULT NULL,
  `fecha_eliminacion` timestamp NULL DEFAULT NULL,
  `editado` tinyint(1) NOT NULL DEFAULT 0,
  `eliminado` tinyint(1) NOT NULL DEFAULT 0,
  `eliminado_por` int(11) DEFAULT NULL,
  `mensaje_respuesta_id` int(11) DEFAULT NULL,
  `mensaje_original_id` int(11) DEFAULT NULL COMMENT 'Para mensajes reenviados',
  `metadatos` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadatos`)),
  `prioridad` enum('baja','normal','alta','urgente') NOT NULL DEFAULT 'normal',
  `destinatarios_especificos` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Para mensajes privados en grupos' CHECK (json_valid(`destinatarios_especificos`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Mensajes del chat';

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `chat_mensajes_eliminados`
--

CREATE TABLE `chat_mensajes_eliminados` (
  `id_eliminacion` int(11) NOT NULL,
  `mensaje_id` int(11) NOT NULL,
  `usuario_elimino` int(11) NOT NULL,
  `fecha_eliminacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `motivo` varchar(255) DEFAULT NULL,
  `tipo_eliminacion` enum('usuario','admin','sistema','automatica') NOT NULL DEFAULT 'usuario',
  `contenido_original` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Auditoría de mensajes eliminados';

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `chat_participantes`
--

CREATE TABLE `chat_participantes` (
  `id_participante` int(11) NOT NULL,
  `conversacion_id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `rol` enum('admin','moderador','miembro') NOT NULL DEFAULT 'miembro',
  `fecha_union` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_salida` timestamp NULL DEFAULT NULL,
  `ultima_lectura` timestamp NULL DEFAULT NULL,
  `notificaciones` tinyint(1) NOT NULL DEFAULT 1,
  `silenciado` tinyint(1) NOT NULL DEFAULT 0,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `configuracion_participante` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`configuracion_participante`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Participantes de las conversaciones';

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `chat_reacciones`
--

CREATE TABLE `chat_reacciones` (
  `id_reaccion` int(11) NOT NULL,
  `mensaje_id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `tipo_reaccion` varchar(20) NOT NULL DEFAULT '?',
  `fecha_reaccion` timestamp NOT NULL DEFAULT current_timestamp(),
  `dispositivo` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Reacciones a mensajes';

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Componentes`
--

CREATE TABLE `Componentes` (
  `ID` int(11) UNSIGNED ZEROFILL NOT NULL,
  `Nom_Com` varchar(200) NOT NULL,
  `Agregado_Por` varchar(250) NOT NULL,
  `Agregadoel` timestamp NOT NULL DEFAULT current_timestamp(),
  `ActualizadoPor` varchar(200) NOT NULL,
  `Licencia` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `configuracion_checador`
--

CREATE TABLE `configuracion_checador` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `clave` varchar(100) NOT NULL,
  `valor` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ConteosDiarios`
--

CREATE TABLE `ConteosDiarios` (
  `Folio_Ingreso` int(10) UNSIGNED ZEROFILL NOT NULL,
  `Cod_Barra` varchar(250) NOT NULL,
  `Nombre_Producto` varchar(250) NOT NULL,
  `Fk_sucursal` int(12) NOT NULL,
  `Existencias_R` int(11) NOT NULL,
  `ExistenciaFisica` int(11) DEFAULT NULL,
  `AgregadoPor` varchar(250) NOT NULL,
  `AgregadoEl` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `EnPausa` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Disparadores `ConteosDiarios`
--
DELIMITER $$
CREATE TRIGGER `ActualizaPausaAlInsertar` AFTER INSERT ON `ConteosDiarios` FOR EACH ROW BEGIN
    UPDATE ConteosDiarios_Pausados
    SET EnPausa = 0
    WHERE Cod_Barra = NEW.Cod_Barra
      AND Fk_sucursal = NEW.Fk_sucursal;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ConteosDiariosrRESPALDO`
--

CREATE TABLE `ConteosDiariosrRESPALDO` (
  `Folio_Ingreso` int(10) UNSIGNED ZEROFILL NOT NULL,
  `Cod_Barra` varchar(250) NOT NULL,
  `Nombre_Producto` varchar(250) NOT NULL,
  `Fk_sucursal` int(12) NOT NULL,
  `Existencias_R` int(11) NOT NULL,
  `ExistenciaFisica` int(11) DEFAULT NULL,
  `AgregadoPor` varchar(250) NOT NULL,
  `AgregadoEl` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `EnPausa` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ConteosDiarios_Pausados`
--

CREATE TABLE `ConteosDiarios_Pausados` (
  `Folio_Ingreso` int(10) UNSIGNED ZEROFILL NOT NULL,
  `Cod_Barra` varchar(250) NOT NULL,
  `Nombre_Producto` varchar(250) NOT NULL,
  `Fk_sucursal` int(12) NOT NULL,
  `Existencias_R` int(11) NOT NULL,
  `ExistenciaFisica` int(11) DEFAULT NULL,
  `AgregadoPor` varchar(250) NOT NULL,
  `AgregadoEl` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `EnPausa` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Cortes_Cajas_POS`
--

CREATE TABLE `Cortes_Cajas_POS` (
  `ID_Caja` int(10) UNSIGNED ZEROFILL NOT NULL,
  `Fk_Caja` int(11) UNSIGNED ZEROFILL NOT NULL,
  `Empleado` varchar(250) NOT NULL,
  `Sucursal` int(11) NOT NULL,
  `Turno` varchar(300) NOT NULL,
  `TotalTickets` int(11) NOT NULL,
  `Valor_Total_Caja` decimal(50,2) NOT NULL,
  `TotalEfectivo` double(50,10) NOT NULL,
  `TotalTarjeta` decimal(50,12) NOT NULL,
  `TotalCreditos` double(50,10) NOT NULL,
  `TotalTransferencias` decimal(50,2) NOT NULL,
  `Hora_Cierre` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `Sistema` varchar(200) NOT NULL,
  `ID_H_O_D` varchar(200) NOT NULL,
  `Comentarios` varchar(250) NOT NULL,
  `Servicios` text DEFAULT NULL,
  `Gastos` text DEFAULT NULL,
  `Encargos` text NOT NULL,
  `Abonos` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Disparadores `Cortes_Cajas_POS`
--
DELIMITER $$
CREATE TRIGGER `after_insert_cortes_cajas_pos` AFTER INSERT ON `Cortes_Cajas_POS` FOR EACH ROW BEGIN
    DECLARE err_code INT DEFAULT 0;
    DECLARE err_message TEXT DEFAULT '';

    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        -- Obtener el código de error y el mensaje de error
        GET DIAGNOSTICS CONDITION 1
            err_code = MYSQL_ERRNO,
            err_message = MESSAGE_TEXT;

        -- Insertar el mensaje de error en la tabla ErrorLog
        INSERT INTO ErrorLog (error_message, Fk_Caja, error_code) 
        VALUES (CONCAT('Error al actualizar Cajas: ', err_message), NEW.Fk_Caja, err_code);
    END;

    -- Validaciones (puedes añadir las tuyas aquí)
    IF NEW.Fk_Caja IS NULL OR NEW.Fk_Caja = 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'El valor de Fk_Caja no es válido';
    END IF;

    -- Actualizar la tabla Cajas
    UPDATE Cajas
    SET Asignacion = 3, Estatus = 'Cerrada'
    WHERE ID_Caja = NEW.Fk_Caja;

END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Cotizaciones`
--

CREATE TABLE `Cotizaciones` (
  `IdProdCedis` int(12) UNSIGNED ZEROFILL NOT NULL,
  `NumCotizacion` varchar(150) NOT NULL,
  `Proveedor` varchar(200) NOT NULL,
  `Cod_Barra` varchar(250) DEFAULT NULL,
  `Nombre_Prod` varchar(1000) DEFAULT NULL,
  `Fk_Sucursal` int(11) NOT NULL,
  `Cantidad` int(11) NOT NULL,
  `Precio_Venta` decimal(50,2) DEFAULT NULL,
  `Precio_C` decimal(50,2) DEFAULT NULL,
  `AgregadoPor` varchar(250) NOT NULL,
  `AgregadoEl` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Creditos_POS`
--

CREATE TABLE `Creditos_POS` (
  `Folio_Credito` int(10) UNSIGNED ZEROFILL NOT NULL,
  `Nombre_Cred` varchar(250) NOT NULL,
  `Edad` varchar(50) NOT NULL,
  `Sexo` varchar(100) NOT NULL,
  `Telefono` varchar(12) NOT NULL,
  `Fecha_Apertura` date NOT NULL,
  `Fecha_Termino` date NOT NULL,
  `Fk_Sucursal` int(11) NOT NULL,
  `Agrega` varchar(250) NOT NULL,
  `AgregadoEl` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `Sistema` varchar(250) NOT NULL,
  `Licencia` varchar(250) NOT NULL,
  `Saldo` decimal(52,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Creditos_POS_Audita`
--

CREATE TABLE `Creditos_POS_Audita` (
  `Audita_Credi_POS` int(11) NOT NULL,
  `Folio_Credito` int(10) UNSIGNED ZEROFILL NOT NULL,
  `Fk_tipo_Credi` int(10) UNSIGNED ZEROFILL NOT NULL,
  `Nombre_Cred` varchar(250) NOT NULL,
  `Edad` varchar(50) NOT NULL,
  `Sexo` varchar(100) NOT NULL,
  `Direccion` varchar(250) NOT NULL,
  `Telefono` varchar(12) NOT NULL,
  `Cant_Apertura` decimal(50,2) NOT NULL,
  `Costo_Tratamiento` decimal(50,2) NOT NULL,
  `Fecha_Apertura` date NOT NULL,
  `Fecha_Termino` date NOT NULL,
  `Fk_Sucursal` int(11) NOT NULL,
  `Odontologo` varchar(250) NOT NULL,
  `Estatus` varchar(250) NOT NULL,
  `CodigoEstatus` varchar(250) NOT NULL,
  `Agrega` varchar(250) NOT NULL,
  `AgregadoEl` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `Sistema` varchar(250) NOT NULL,
  `ID_H_O_D` varchar(250) NOT NULL,
  `Promocion` varchar(250) NOT NULL,
  `Costo_Descuento` decimal(50,2) NOT NULL,
  `Validez` date NOT NULL,
  `Area` varchar(250) NOT NULL,
  `Saldo` decimal(52,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Data_Facturacion_POS`
--

CREATE TABLE `Data_Facturacion_POS` (
  `ID_Factura` int(10) UNSIGNED ZEROFILL NOT NULL,
  `Nombre_RazonSocial` varchar(250) NOT NULL,
  `RFC` varchar(250) NOT NULL,
  `Direccion` varchar(250) NOT NULL,
  `Uso_CFDI` varchar(250) NOT NULL,
  `Telefono` varchar(250) NOT NULL,
  `Fk_Ticket` varchar(100) NOT NULL,
  `Fk_Sucursal` int(11) NOT NULL,
  `Correo` varchar(250) NOT NULL,
  `Estatus` varchar(200) NOT NULL,
  `CodigoEstatus` varchar(200) NOT NULL,
  `Agrega` varchar(200) NOT NULL,
  `Agregadoel` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `Sistema` varchar(200) NOT NULL,
  `ID_H_O_D` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Data_Pacientes`
--

CREATE TABLE `Data_Pacientes` (
  `ID_Data_Paciente` int(12) UNSIGNED ZEROFILL NOT NULL,
  `Nombre_Paciente` varchar(150) DEFAULT NULL,
  `Fecha_Nacimiento` date NOT NULL,
  `Edad` varchar(100) DEFAULT NULL,
  `Sexo` varchar(20) DEFAULT NULL,
  `Alergias` varchar(250) DEFAULT NULL,
  `Telefono` varchar(20) DEFAULT NULL,
  `Correo` varchar(150) DEFAULT NULL,
  `Fk_Sucursal` int(11) NOT NULL,
  `SucursalVisita` varchar(150) NOT NULL,
  `Licencia` varchar(150) NOT NULL,
  `Ingreso` varchar(250) NOT NULL,
  `Ingresadoen` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `Sistema` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Disparadores `Data_Pacientes`
--
DELIMITER $$
CREATE TRIGGER `Actualizaciones_Pacientes` BEFORE UPDATE ON `Data_Pacientes` FOR EACH ROW INSERT INTO Data_Pacientes_Updates
    (ID_Data_Paciente,Nombre_Paciente,Fecha_Nacimiento,Edad,Sexo,Alergias,Telefono,Correo,Licencia,Ingreso,Ingresadoen,Sistema)
    VALUES (NEW.ID_Data_Paciente,NEW.Nombre_Paciente,NEW.Fecha_Nacimiento,NEW.Edad,NEW.Sexo,NEW.Alergias,NEW.Telefono,NEW.Correo,NEW.Licencia,NEW.Ingreso,Now(),NEW.Sistema)
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Data_Pacientes_Updates`
--

CREATE TABLE `Data_Pacientes_Updates` (
  `ID_Update` int(11) NOT NULL,
  `ID_Data_Paciente` int(12) UNSIGNED ZEROFILL NOT NULL,
  `Nombre_Paciente` varchar(150) DEFAULT NULL,
  `Fecha_Nacimiento` date NOT NULL,
  `Edad` varchar(100) DEFAULT NULL,
  `Sexo` varchar(20) DEFAULT NULL,
  `Alergias` varchar(250) DEFAULT NULL,
  `Telefono` varchar(20) DEFAULT NULL,
  `Correo` varchar(150) DEFAULT NULL,
  `Licencia` varchar(150) NOT NULL,
  `Ingreso` varchar(250) NOT NULL,
  `Ingresadoen` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `Sistema` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Detalle_Limpieza`
--

CREATE TABLE `Detalle_Limpieza` (
  `id_detalle` int(11) NOT NULL,
  `id_bitacora` int(11) NOT NULL,
  `elemento` varchar(100) NOT NULL,
  `lunes_mat` tinyint(1) DEFAULT 0,
  `lunes_vesp` tinyint(1) DEFAULT 0,
  `martes_mat` tinyint(1) DEFAULT 0,
  `martes_vesp` tinyint(1) DEFAULT 0,
  `miercoles_mat` tinyint(1) DEFAULT 0,
  `miercoles_vesp` tinyint(1) DEFAULT 0,
  `jueves_mat` tinyint(1) DEFAULT 0,
  `jueves_vesp` tinyint(1) DEFAULT 0,
  `viernes_mat` tinyint(1) DEFAULT 0,
  `viernes_vesp` tinyint(1) DEFAULT 0,
  `sabado_mat` tinyint(1) DEFAULT 0,
  `sabado_vesp` tinyint(1) DEFAULT 0,
  `domingo_mat` tinyint(1) DEFAULT 0,
  `domingo_vesp` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Devoluciones`
--

CREATE TABLE `Devoluciones` (
  `id` int(11) NOT NULL,
  `folio` varchar(50) NOT NULL,
  `sucursal_id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp(),
  `estatus` enum('pendiente','procesada','cancelada') NOT NULL DEFAULT 'pendiente',
  `observaciones_generales` text DEFAULT NULL,
  `total_productos` int(11) DEFAULT 0,
  `total_unidades` int(11) DEFAULT 0,
  `valor_total` decimal(15,2) DEFAULT 0.00,
  `fecha_procesada` timestamp NULL DEFAULT NULL,
  `usuario_procesa` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Devoluciones_Acciones`
--

CREATE TABLE `Devoluciones_Acciones` (
  `id` int(11) NOT NULL,
  `devolucion_id` int(11) NOT NULL,
  `detalle_id` int(11) NOT NULL,
  `tipo_accion` enum('ajuste_inventario','traspaso','destruccion','reembolso','otro') NOT NULL,
  `descripcion` text NOT NULL,
  `usuario_ejecuta` int(11) NOT NULL,
  `fecha_ejecucion` timestamp NOT NULL DEFAULT current_timestamp(),
  `observaciones` text DEFAULT NULL,
  `estatus` enum('pendiente','ejecutada','cancelada') NOT NULL DEFAULT 'pendiente',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Devoluciones_Autorizaciones`
--

CREATE TABLE `Devoluciones_Autorizaciones` (
  `id` int(11) NOT NULL,
  `devolucion_id` int(11) NOT NULL,
  `usuario_autoriza` int(11) NOT NULL,
  `fecha_autorizacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `observaciones` text DEFAULT NULL,
  `estatus` enum('pendiente','aprobada','rechazada') NOT NULL DEFAULT 'pendiente',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Devoluciones_Detalle`
--

CREATE TABLE `Devoluciones_Detalle` (
  `id` int(11) NOT NULL,
  `devolucion_id` int(11) NOT NULL,
  `producto_id` int(12) UNSIGNED ZEROFILL NOT NULL,
  `codigo_barras` varchar(100) NOT NULL,
  `nombre_producto` varchar(250) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `tipo_devolucion` varchar(50) NOT NULL,
  `observaciones` text DEFAULT NULL,
  `lote` varchar(100) DEFAULT NULL,
  `fecha_caducidad` date DEFAULT NULL,
  `precio_venta` decimal(15,2) DEFAULT 0.00,
  `precio_costo` decimal(15,2) DEFAULT 0.00,
  `valor_total` decimal(15,2) DEFAULT 0.00,
  `accion_tomada` enum('ajuste_inventario','traspaso','destruccion','reembolso','otro') DEFAULT 'ajuste_inventario',
  `observaciones_accion` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Disparadores `Devoluciones_Detalle`
--
DELIMITER $$
CREATE TRIGGER `tr_devoluciones_detalle_delete` AFTER DELETE ON `Devoluciones_Detalle` FOR EACH ROW BEGIN
    UPDATE Devoluciones 
    SET total_productos = (
        SELECT COUNT(DISTINCT producto_id) 
        FROM Devoluciones_Detalle 
        WHERE devolucion_id = OLD.devolucion_id
    ),
    total_unidades = (
        SELECT COALESCE(SUM(cantidad), 0) 
        FROM Devoluciones_Detalle 
        WHERE devolucion_id = OLD.devolucion_id
    ),
    valor_total = (
        SELECT COALESCE(SUM(valor_total), 0.00) 
        FROM Devoluciones_Detalle 
        WHERE devolucion_id = OLD.devolucion_id
    )
    WHERE id = OLD.devolucion_id;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `tr_devoluciones_detalle_insert` AFTER INSERT ON `Devoluciones_Detalle` FOR EACH ROW BEGIN
    UPDATE Devoluciones 
    SET total_productos = (
        SELECT COUNT(DISTINCT producto_id) 
        FROM Devoluciones_Detalle 
        WHERE devolucion_id = NEW.devolucion_id
    ),
    total_unidades = (
        SELECT COALESCE(SUM(cantidad), 0) 
        FROM Devoluciones_Detalle 
        WHERE devolucion_id = NEW.devolucion_id
    ),
    valor_total = (
        SELECT COALESCE(SUM(valor_total), 0.00) 
        FROM Devoluciones_Detalle 
        WHERE devolucion_id = NEW.devolucion_id
    )
    WHERE id = NEW.devolucion_id;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `tr_devoluciones_detalle_update` AFTER UPDATE ON `Devoluciones_Detalle` FOR EACH ROW BEGIN
    UPDATE Devoluciones 
    SET total_productos = (
        SELECT COUNT(DISTINCT producto_id) 
        FROM Devoluciones_Detalle 
        WHERE devolucion_id = NEW.devolucion_id
    ),
    total_unidades = (
        SELECT COALESCE(SUM(cantidad), 0) 
        FROM Devoluciones_Detalle 
        WHERE devolucion_id = NEW.devolucion_id
    ),
    valor_total = (
        SELECT COALESCE(SUM(valor_total), 0.00) 
        FROM Devoluciones_Detalle 
        WHERE devolucion_id = NEW.devolucion_id
    )
    WHERE id = NEW.devolucion_id;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Devoluciones_Reportes`
--

CREATE TABLE `Devoluciones_Reportes` (
  `id` int(11) NOT NULL,
  `tipo_reporte` varchar(50) NOT NULL,
  `parametros` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`parametros`)),
  `fecha_generacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `usuario_genera` int(11) NOT NULL,
  `archivo_ruta` varchar(255) DEFAULT NULL,
  `estatus` enum('generando','completado','error') NOT NULL DEFAULT 'generando',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Devolucion_POS`
--

CREATE TABLE `Devolucion_POS` (
  `ID_Registro` int(11) NOT NULL,
  `Num_Factura` varchar(200) DEFAULT NULL,
  `Cod_Barra` varchar(200) DEFAULT NULL,
  `Nombre_Produc` varchar(100) DEFAULT NULL,
  `Cantidad` int(11) DEFAULT NULL,
  `Fk_Suc_Salida` int(12) DEFAULT NULL,
  `Motivo_Devolucion` varchar(100) DEFAULT NULL,
  `Fecha` date DEFAULT NULL,
  `Agrego` varchar(200) NOT NULL,
  `HoraAgregado` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `encargos`
--

CREATE TABLE `encargos` (
  `id` int(11) NOT NULL,
  `nombre_paciente` varchar(255) NOT NULL,
  `medicamento` varchar(255) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `precioventa` decimal(50,2) NOT NULL,
  `fecha_encargo` date NOT NULL,
  `estado` varchar(200) DEFAULT NULL,
  `costo` decimal(10,2) NOT NULL,
  `abono_parcial` decimal(10,2) DEFAULT 0.00,
  `NumTicket` varchar(120) NOT NULL,
  `Fk_Sucursal` int(11) NOT NULL,
  `Fk_Caja` int(11) NOT NULL,
  `Empleado` varchar(300) NOT NULL,
  `FormaDePago` varchar(300) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Disparadores `encargos`
--
DELIMITER $$
CREATE TRIGGER `tr_abono_encargo_delete` AFTER DELETE ON `encargos` FOR EACH ROW BEGIN
    -- Restar el abono parcial del valor total de la caja
    IF OLD.abono_parcial > 0 THEN
        UPDATE Cajas 
        SET Valor_Total_Caja = Valor_Total_Caja - OLD.abono_parcial
        WHERE ID_Caja = OLD.Fk_Caja;
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `tr_abono_encargo_insert` AFTER INSERT ON `encargos` FOR EACH ROW BEGIN
    -- Solo actualizar si hay un abono parcial mayor a 0
    IF NEW.abono_parcial > 0 THEN
        UPDATE Cajas 
        SET Valor_Total_Caja = Valor_Total_Caja + NEW.abono_parcial
        WHERE ID_Caja = NEW.Fk_Caja;
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `tr_abono_encargo_update` AFTER UPDATE ON `encargos` FOR EACH ROW BEGIN
    -- Si el abono parcial cambió, ajustar el valor de la caja
    IF OLD.abono_parcial != NEW.abono_parcial THEN
        -- Restar el abono anterior
        IF OLD.abono_parcial > 0 THEN
            UPDATE Cajas 
            SET Valor_Total_Caja = Valor_Total_Caja - OLD.abono_parcial
            WHERE ID_Caja = OLD.Fk_Caja;
        END IF;
        
        -- Sumar el nuevo abono
        IF NEW.abono_parcial > 0 THEN
            UPDATE Cajas 
            SET Valor_Total_Caja = Valor_Total_Caja + NEW.abono_parcial
            WHERE ID_Caja = NEW.Fk_Caja;
        END IF;
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Errores_POS`
--

CREATE TABLE `Errores_POS` (
  `id` int(11) NOT NULL,
  `fecha` timestamp NULL DEFAULT current_timestamp(),
  `mensaje_error` text DEFAULT NULL,
  `Cod_Barra` varchar(255) DEFAULT NULL,
  `Fk_sucursal` int(11) DEFAULT NULL,
  `Cantidad_Venta` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Errores_POS_Ventas`
--

CREATE TABLE `Errores_POS_Ventas` (
  `ID_Error` int(11) NOT NULL,
  `Fecha` timestamp NULL DEFAULT current_timestamp(),
  `ID_Prod_POS` int(11) DEFAULT NULL,
  `Cod_Barra` varchar(50) DEFAULT NULL,
  `Fk_sucursal` int(11) DEFAULT NULL,
  `Cantidad_Venta` int(11) DEFAULT NULL,
  `Mensaje_Error` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ErrorLog`
--

CREATE TABLE `ErrorLog` (
  `id` int(11) NOT NULL,
  `error_time` timestamp NULL DEFAULT current_timestamp(),
  `error_message` text DEFAULT NULL,
  `Fk_Caja` int(11) DEFAULT NULL,
  `error_code` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `error_log_act_prod`
--

CREATE TABLE `error_log_act_prod` (
  `id` int(11) NOT NULL,
  `error_message` text NOT NULL,
  `error_time` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Estados`
--

CREATE TABLE `Estados` (
  `ID_Estado` int(11) NOT NULL,
  `Nombre_Estado` varchar(200) NOT NULL,
  `Añadido` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Fondos_Cajas`
--

CREATE TABLE `Fondos_Cajas` (
  `ID_Fon_Caja` int(10) UNSIGNED ZEROFILL NOT NULL,
  `Fk_Sucursal` int(12) NOT NULL,
  `Fondo_Caja` decimal(50,2) NOT NULL,
  `Estatus` varchar(100) NOT NULL,
  `AgregadoPor` varchar(200) NOT NULL,
  `AgregadoEl` timestamp NOT NULL DEFAULT current_timestamp(),
  `Sistema` varchar(200) NOT NULL,
  `Licencia` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Disparadores `Fondos_Cajas`
--
DELIMITER $$
CREATE TRIGGER `AuditaFonCajas` AFTER INSERT ON `Fondos_Cajas` FOR EACH ROW INSERT INTO Fondos_Cajas_Audita
    (ID_Fon_Caja,Fk_Sucursal,Fondo_Caja,Estatus,AgregadoPor,AgregadoEl,Sistema,Licencia)
    VALUES (NEW.ID_Fon_Caja,NEW.Fk_Sucursal,NEW.Fondo_Caja,NEW.Estatus,NEW.AgregadoPor,Now(),NEW.Sistema,NEW.Licencia)
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Fondos_Cajas_Audita`
--

CREATE TABLE `Fondos_Cajas_Audita` (
  `ID_Audita_FonCaja` int(11) NOT NULL,
  `ID_Fon_Caja` int(10) UNSIGNED ZEROFILL NOT NULL,
  `Fk_Sucursal` int(12) NOT NULL,
  `Fondo_Caja` decimal(50,2) NOT NULL,
  `Estatus` varchar(100) NOT NULL,
  `AgregadoPor` varchar(200) NOT NULL,
  `AgregadoEl` timestamp NOT NULL DEFAULT current_timestamp(),
  `Sistema` varchar(200) NOT NULL,
  `Licencia` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `GastosPOS`
--

CREATE TABLE `GastosPOS` (
  `ID_Gastos` int(10) UNSIGNED ZEROFILL NOT NULL,
  `Concepto_Categoria` varchar(200) NOT NULL,
  `Importe_Total` decimal(50,2) NOT NULL,
  `Empleado` varchar(250) NOT NULL,
  `Fk_sucursal` int(12) NOT NULL,
  `Fk_Caja` int(10) UNSIGNED ZEROFILL NOT NULL,
  `Recibe` varchar(250) NOT NULL,
  `Sistema` varchar(200) NOT NULL,
  `AgregadoPor` varchar(250) NOT NULL,
  `AgregadoEl` timestamp NOT NULL DEFAULT current_timestamp(),
  `Licencia` varchar(200) NOT NULL,
  `FechaConcepto` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Disparadores `GastosPOS`
--
DELIMITER $$
CREATE TRIGGER `after_insert_GastosPOS` AFTER INSERT ON `GastosPOS` FOR EACH ROW BEGIN
    DECLARE caja_total DECIMAL(10, 2);

    -- Obtener el Valor_Total_Caja de la caja correspondiente
    SELECT Valor_Total_Caja INTO caja_total
    FROM Cajas
    WHERE ID_Caja = NEW.Fk_Caja;

    -- Disminuir el Valor_Total_Caja en Importe_Total
    UPDATE Cajas
    SET Valor_Total_Caja = caja_total - NEW.Importe_Total
    WHERE ID_Caja = NEW.Fk_Caja;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Gestion_Lotes_Movimientos`
--

CREATE TABLE `Gestion_Lotes_Movimientos` (
  `ID_Movimiento` int(11) NOT NULL,
  `ID_Prod_POS` int(12) UNSIGNED ZEROFILL NOT NULL,
  `Cod_Barra` varchar(250) NOT NULL,
  `Fk_sucursal` int(11) NOT NULL,
  `Lote_Anterior` varchar(100) DEFAULT NULL,
  `Lote_Nuevo` varchar(100) NOT NULL,
  `Fecha_Caducidad_Anterior` date DEFAULT NULL,
  `Fecha_Caducidad_Nueva` date NOT NULL,
  `Cantidad` int(11) NOT NULL DEFAULT 0,
  `Tipo_Movimiento` enum('actualizacion','ajuste','correccion') NOT NULL DEFAULT 'actualizacion',
  `Usuario_Modifico` varchar(250) NOT NULL,
  `Fecha_Modificacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `Observaciones` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `historial_abonos_encargos`
--

CREATE TABLE `historial_abonos_encargos` (
  `id` int(11) NOT NULL,
  `encargo_id` int(11) NOT NULL,
  `monto_abonado` decimal(10,2) NOT NULL,
  `forma_pago` varchar(300) NOT NULL,
  `efectivo_recibido` decimal(10,2) DEFAULT 0.00,
  `observaciones` text DEFAULT NULL,
  `fecha_abono` datetime NOT NULL,
  `empleado` varchar(300) NOT NULL,
  `sucursal` varchar(300) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Historial_Lotes`
--

CREATE TABLE `Historial_Lotes` (
  `ID_Historial` int(11) NOT NULL,
  `ID_Prod_POS` int(11) NOT NULL,
  `Fk_sucursal` int(11) NOT NULL,
  `Lote` varchar(100) NOT NULL,
  `Fecha_Caducidad` date NOT NULL,
  `Fecha_Ingreso` date NOT NULL,
  `Existencias` int(11) NOT NULL DEFAULT 0,
  `Fecha_Registro` timestamp NULL DEFAULT current_timestamp(),
  `Usuario_Modifico` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `IngresosAutorizados`
--

CREATE TABLE `IngresosAutorizados` (
  `IDIngreso` int(12) UNSIGNED ZEROFILL NOT NULL,
  `ID_Prod_POS` int(12) UNSIGNED ZEROFILL NOT NULL,
  `NumFactura` varchar(200) NOT NULL,
  `Proveedor` varchar(200) NOT NULL,
  `Cod_Barra` varchar(250) DEFAULT NULL,
  `Nombre_Prod` varchar(1000) DEFAULT NULL,
  `Fk_Sucursal` int(11) NOT NULL,
  `Contabilizado` int(11) NOT NULL,
  `Fecha_Caducidad` date NOT NULL,
  `Lote` varchar(100) NOT NULL,
  `PrecioMaximo` double(50,2) NOT NULL,
  `Precio_Venta` decimal(50,2) DEFAULT NULL,
  `Precio_C` decimal(50,2) DEFAULT NULL,
  `PrecioVentaAutorizado` double(50,2) NOT NULL,
  `AgregadoPor` varchar(250) NOT NULL,
  `AgregadoEl` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `FechaInventario` date NOT NULL,
  `Estatus` varchar(200) NOT NULL,
  `NumOrden` int(10) UNSIGNED ZEROFILL NOT NULL,
  `SolicitadoPor` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Disparadores `IngresosAutorizados`
--
DELIMITER $$
CREATE TRIGGER `after_insert_IngresosAutorizados` AFTER INSERT ON `IngresosAutorizados` FOR EACH ROW BEGIN
    -- Declarar variables para manejar errores
    DECLARE error_message VARCHAR(255) DEFAULT NULL;
    DECLARE error_code INT DEFAULT NULL;

    -- Actualizar la tabla Solicitudes_Ingresos
    BEGIN
        -- Manejador de errores para la actualización de Solicitudes_Ingresos
        DECLARE CONTINUE HANDLER FOR SQLEXCEPTION
        BEGIN
            GET DIAGNOSTICS CONDITION 1
                error_code = MYSQL_ERRNO, error_message = MESSAGE_TEXT;
        END;

        UPDATE Solicitudes_Ingresos
        SET Estatus = NEW.Estatus
        WHERE Cod_Barra = NEW.Cod_Barra;

        -- Verificar si ocurrió un error durante la actualización
        IF error_message IS NOT NULL THEN
            -- Registrar el error en la tabla logsingresosmedicamentos
            INSERT INTO logsingresosmedicamentos (error_time, error_description)
            VALUES (NOW(), CONCAT('Error al actualizar Solicitudes_Ingresos: ', error_message));
        END IF;
    END;

    -- Actualizar la tabla Stock_POS
    BEGIN
        -- Manejador de errores para la actualización de Stock_POS
        DECLARE CONTINUE HANDLER FOR SQLEXCEPTION
        BEGIN
            GET DIAGNOSTICS CONDITION 1
                error_code = MYSQL_ERRNO, error_message = MESSAGE_TEXT;
        END;

        UPDATE Stock_POS
        SET Existencias_R = Existencias_R + NEW.Contabilizado,
            Fecha_Ingreso = CURDATE()
        WHERE Cod_Barra = NEW.Cod_Barra AND Fk_Sucursal = NEW.Fk_Sucursal;

        -- Verificar si ocurrió un error durante la actualización
        IF error_message IS NOT NULL THEN
            -- Registrar el error en la tabla logsingresosmedicamentos
            INSERT INTO logsingresosmedicamentos (error_time, error_description)
            VALUES (NOW(), CONCAT('Error al actualizar Stock_POS: ', error_message));
        END IF;
    END;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `IngresosCedis`
--

CREATE TABLE `IngresosCedis` (
  `IDIngreso` int(12) UNSIGNED ZEROFILL NOT NULL,
  `ID_Prod_POS` int(12) UNSIGNED ZEROFILL NOT NULL,
  `NumFactura` varchar(200) NOT NULL,
  `Cod_Barra` varchar(250) DEFAULT NULL,
  `Nombre_Prod` varchar(1000) DEFAULT NULL,
  `Piezas` int(11) NOT NULL,
  `Fecha_Caducidad` date NOT NULL,
  `Lote` varchar(100) NOT NULL,
  `AgregadoPor` varchar(250) NOT NULL,
  `AgregadoEl` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Disparadores `IngresosCedis`
--
DELIMITER $$
CREATE TRIGGER `update_cedis_after_insert` AFTER INSERT ON `IngresosCedis` FOR EACH ROW BEGIN
    -- Verificar si existe el código de barra en la tabla CEDIS
    IF EXISTS (SELECT 1 FROM CEDIS WHERE Cod_Barra = NEW.Cod_Barra) THEN
        -- Actualizar la tabla CEDIS
        UPDATE CEDIS
        SET 
            Existencias = Existencias + NEW.Piezas,
            Fecha_Caducidad = NEW.Fecha_Caducidad,
            Lote_Med = NEW.Lote,
            ActualizadoPor = NEW.AgregadoPor
        WHERE ID_Prod_POS = NEW.ID_Prod_POS;
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `IngresosFarmacias`
--

CREATE TABLE `IngresosFarmacias` (
  `IdProdCedis` int(12) UNSIGNED ZEROFILL NOT NULL,
  `ID_Prod_POS` int(12) UNSIGNED ZEROFILL NOT NULL,
  `NumFactura` varchar(200) NOT NULL,
  `Proveedor` varchar(200) NOT NULL,
  `Cod_Barra` varchar(250) DEFAULT NULL,
  `Nombre_Prod` varchar(1000) DEFAULT NULL,
  `Fk_Sucursal` int(11) NOT NULL,
  `Contabilizado` int(11) NOT NULL,
  `Fecha_Caducidad` date NOT NULL,
  `Lote` varchar(100) NOT NULL,
  `PrecioMaximo` double(50,2) NOT NULL,
  `Precio_Venta` decimal(50,2) DEFAULT NULL,
  `Precio_C` decimal(50,2) DEFAULT NULL,
  `AgregadoPor` varchar(250) NOT NULL,
  `AgregadoEl` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `FechaInventario` date NOT NULL,
  `Estatus` varchar(200) NOT NULL,
  `NumOrden` int(10) UNSIGNED ZEROFILL NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Disparadores `IngresosFarmacias`
--
DELIMITER $$
CREATE TRIGGER `actualizar_existencias` AFTER INSERT ON `IngresosFarmacias` FOR EACH ROW BEGIN
    UPDATE Stock_POS
    SET Existencias_R = Existencias_R + NEW.Contabilizado,
        Lote = NEW.Lote,
        ActualizadoPor = NEW.AgregadoPor,
        Fecha_Caducidad = NEW.Fecha_Caducidad
    WHERE Cod_Barra = NEW.Cod_Barra
      AND Fk_Sucursal = NEW.Fk_Sucursal;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Ingresos_Medicamentos`
--

CREATE TABLE `Ingresos_Medicamentos` (
  `Id_Ingreso` int(11) NOT NULL,
  `Cod_Barra` varchar(50) NOT NULL,
  `Nombre_Prod` varchar(300) NOT NULL,
  `Fk_Sucursal` int(11) NOT NULL,
  `Factura` varchar(200) NOT NULL,
  `CantidadIngresada` int(12) NOT NULL,
  `Fecha_Ingreso` date NOT NULL,
  `Agrego` varchar(200) NOT NULL,
  `Licencia` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Inserciones_Excel_inventarios`
--

CREATE TABLE `Inserciones_Excel_inventarios` (
  `Id_Insert` int(11) NOT NULL,
  `Cod_Barra` varchar(250) NOT NULL,
  `Nombre_prod` varchar(500) NOT NULL,
  `Cantidad` int(200) NOT NULL,
  `Sucursal` int(11) NOT NULL,
  `Tipo_ajuste` varchar(300) NOT NULL,
  `Agrego` varchar(300) NOT NULL,
  `Fecha_registro` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Disparadores `Inserciones_Excel_inventarios`
--
DELIMITER $$
CREATE TRIGGER `AjusteInventarios` BEFORE INSERT ON `Inserciones_Excel_inventarios` FOR EACH ROW UPDATE Stock_POS_PruebasInv
    SET Existencias_R =NEW.Cantidad
    WHERE Cod_Barra = NEW.Cod_Barra AND
    Fk_sucursal= NEW.Sucursal
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `InventariosStocks_Conteos`
--

CREATE TABLE `InventariosStocks_Conteos` (
  `Folio_Prod_Stock` int(10) UNSIGNED ZEROFILL NOT NULL,
  `ID_Prod_POS` int(12) UNSIGNED ZEROFILL NOT NULL,
  `Cod_Barra` varchar(100) NOT NULL,
  `Nombre_Prod` varchar(250) NOT NULL,
  `Fk_sucursal` int(12) NOT NULL,
  `Precio_Venta` decimal(50,2) NOT NULL,
  `Precio_C` decimal(50,2) NOT NULL,
  `Contabilizado` int(12) NOT NULL,
  `StockEnMomento` int(11) NOT NULL,
  `Diferencia` int(11) NOT NULL,
  `Sistema` varchar(200) NOT NULL,
  `AgregadoPor` varchar(250) NOT NULL,
  `AgregadoEl` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `ID_H_O_D` varchar(100) NOT NULL,
  `FechaInventario` date NOT NULL,
  `Tipo_Ajuste` varchar(250) NOT NULL,
  `Anaquel` varchar(100) NOT NULL,
  `Repisa` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Disparadores `InventariosStocks_Conteos`
--
DELIMITER $$
CREATE TRIGGER `trg_actualizar_stock_pos` AFTER INSERT ON `InventariosStocks_Conteos` FOR EACH ROW BEGIN
    -- Variables para manejo de errores
    DECLARE error_msg VARCHAR(255);

    -- Actualización de stock, campos Anaquel, Repisa, UltimoInventarioPor y FechaUltimoInventario
    IF NEW.Cod_Barra IS NOT NULL AND NEW.Cod_Barra != '' THEN
        -- Actualizar usando Cod_Barra
        UPDATE Stock_POS
        SET Existencias_R = IFNULL(Existencias_R, 0) + NEW.Diferencia,
            Anaquel = CASE WHEN NEW.Anaquel IS NOT NULL AND NEW.Anaquel != '' AND NEW.Anaquel != Anaquel THEN NEW.Anaquel ELSE Anaquel END,
            Repisa = CASE WHEN NEW.Repisa IS NOT NULL AND NEW.Repisa != '' AND NEW.Repisa != Repisa THEN NEW.Repisa ELSE Repisa END,
            UltimoInventarioPor = NEW.AgregadoPor,
            FechaUltimoInventario = NEW.FechaInventario
        WHERE Cod_Barra = NEW.Cod_Barra AND Fk_sucursal = NEW.Fk_sucursal;
        
        -- Capturar y registrar errores
        IF ROW_COUNT() = 0 THEN
            SET error_msg = CONCAT('No se encontraron filas afectadas para Cod_Barra: ', NEW.Cod_Barra, ' y Fk_sucursal: ', NEW.Fk_sucursal);
            INSERT INTO registro_errores_Actualizacionanaqueles (mensaje_error) VALUES (error_msg);
        END IF;
    ELSE
        -- Actualizar usando ID_Prod_POS
        UPDATE Stock_POS
        SET Existencias_R = IFNULL(Existencias_R, 0) + NEW.Diferencia,
            Anaquel = CASE WHEN NEW.Anaquel IS NOT NULL AND NEW.Anaquel != '' AND NEW.Anaquel != Anaquel THEN NEW.Anaquel ELSE Anaquel END,
            Repisa = CASE WHEN NEW.Repisa IS NOT NULL AND NEW.Repisa != '' AND NEW.Repisa != Repisa THEN NEW.Repisa ELSE Repisa END,
            UltimoInventarioPor = NEW.AgregadoPor,
            FechaUltimoInventario = NEW.FechaInventario
        WHERE ID_Prod_POS = NEW.ID_Prod_POS AND Fk_sucursal = NEW.Fk_sucursal;

        -- Capturar y registrar errores
        IF ROW_COUNT() = 0 THEN
            SET error_msg = CONCAT('No se encontraron filas afectadas para ID_Prod_POS: ', NEW.ID_Prod_POS, ' y Fk_sucursal: ', NEW.Fk_sucursal);
            INSERT INTO registro_errores_Actualizacionanaqueles (mensaje_error) VALUES (error_msg);
        END IF;
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `InventariosSucursales`
--

CREATE TABLE `InventariosSucursales` (
  `IdProdCedis` int(12) UNSIGNED ZEROFILL NOT NULL,
  `ID_Prod_POS` int(12) UNSIGNED ZEROFILL NOT NULL,
  `Cod_Barra` varchar(250) DEFAULT NULL,
  `Nombre_Prod` varchar(1000) DEFAULT NULL,
  `Contabilizado` int(11) NOT NULL,
  `StockEnMomento` int(11) NOT NULL,
  `ExistenciasAjuste` int(11) NOT NULL,
  `Precio_Venta` decimal(50,2) DEFAULT NULL,
  `Precio_C` decimal(50,2) DEFAULT NULL,
  `AgregadoPor` varchar(250) NOT NULL,
  `AgregadoEl` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `FechaInventario` date NOT NULL,
  `Fk_Sucursal` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Disparadores `InventariosSucursales`
--
DELIMITER $$
CREATE TRIGGER `trg_after_insert_inventarios_sucursales` AFTER INSERT ON `InventariosSucursales` FOR EACH ROW BEGIN
    DECLARE v_existencias INT;

    -- Obtener las existencias actuales de la tabla Stock_POS
    SELECT Existencias_R
    INTO v_existencias
    FROM Stock_POS
    WHERE Cod_Barra = NEW.Cod_Barra AND Fk_Sucursal = NEW.Fk_Sucursal;

    -- Ajustar las existencias
    IF v_existencias IS NOT NULL THEN
        UPDATE Stock_POS
        SET Existencias_R = v_existencias + NEW.Contabilizado
        WHERE Cod_Barra = NEW.Cod_Barra AND Fk_Sucursal = NEW.Fk_Sucursal;
    ELSE
        -- Si no existe el Cod_Barra y Fk_Sucursal en la tabla Stock_POS, insertar un nuevo registro
        INSERT INTO Stock_POS (Cod_Barra, Existencias_R, Fk_Sucursal)
        VALUES (NEW.Cod_Barra, NEW.Contabilizado, NEW.Fk_Sucursal);
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Inventarios_Clinicas`
--

CREATE TABLE `Inventarios_Clinicas` (
  `ID_Inv_Clic` int(10) UNSIGNED ZEROFILL NOT NULL,
  `Cod_Equipo` varchar(200) NOT NULL,
  `Cod_Equipo_Repetido` varchar(200) NOT NULL,
  `FK_Tipo_Mob` int(10) UNSIGNED ZEROFILL NOT NULL,
  `Cantidad_Mobil` int(11) NOT NULL,
  `Nombre_equipo` varchar(250) NOT NULL,
  `Descripcion` varchar(250) NOT NULL,
  `Fecha_Ingreso` date NOT NULL,
  `Estatus` varchar(100) NOT NULL,
  `CodigoEstatus` varchar(150) NOT NULL,
  `Agregado_Por` varchar(200) NOT NULL,
  `AgregadoEl` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `Sistema` varchar(200) NOT NULL,
  `ID_H_O_D` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Disparadores `Inventarios_Clinicas`
--
DELIMITER $$
CREATE TRIGGER `Audita_InventariosClinicas` AFTER INSERT ON `Inventarios_Clinicas` FOR EACH ROW INSERT INTO Inventarios_Clinicas_audita
    (ID_Inv_Clic,Cod_Equipo,Cod_Equipo_Repetido,FK_Tipo_Mob,Cantidad_Mobil,Nombre_equipo,Descripcion,Fecha_Ingreso,Estatus,CodigoEstatus,Agregado_Por,AgregadoEl,Sistema,ID_H_O_D)
    VALUES (NEW.ID_Inv_Clic,NEW.Cod_Equipo,NEW.Cod_Equipo_Repetido,NEW.FK_Tipo_Mob,NEW.Cantidad_Mobil,NEW.Nombre_equipo,NEW.Descripcion,NEW.Fecha_Ingreso,NEW.Estatus,NEW.CodigoEstatus,NEW.Agregado_Por,Now(),NEW.Sistema,NEW.ID_H_O_D)
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `Update_Inventarios_Clinicas` BEFORE UPDATE ON `Inventarios_Clinicas` FOR EACH ROW INSERT INTO Inventarios_Clinicas_audita
    (ID_Inv_Clic,Cod_Equipo,Cod_Equipo_Repetido,FK_Tipo_Mob,Cantidad_Mobil,Nombre_equipo,Descripcion,Fecha_Ingreso,Estatus,CodigoEstatus,Agregado_Por,AgregadoEl,Sistema,ID_H_O_D)
    VALUES (NEW.ID_Inv_Clic,NEW.Cod_Equipo,NEW.Cod_Equipo_Repetido,NEW.FK_Tipo_Mob,NEW.Cantidad_Mobil,NEW.Nombre_equipo,NEW.Descripcion,NEW.Fecha_Ingreso,NEW.Estatus,NEW.CodigoEstatus,NEW.Agregado_Por,Now(),NEW.Sistema,NEW.ID_H_O_D)
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Inventarios_Clinicas_audita`
--

CREATE TABLE `Inventarios_Clinicas_audita` (
  `ID_Inv_Clic_Audita` int(11) NOT NULL,
  `ID_Inv_Clic` int(10) UNSIGNED ZEROFILL NOT NULL,
  `Cod_Equipo` varchar(200) NOT NULL,
  `Cod_Equipo_Repetido` varchar(200) NOT NULL,
  `FK_Tipo_Mob` int(10) UNSIGNED ZEROFILL NOT NULL,
  `Cantidad_Mobil` int(11) NOT NULL,
  `Nombre_equipo` varchar(250) NOT NULL,
  `Descripcion` varchar(250) NOT NULL,
  `Fecha_Ingreso` date NOT NULL,
  `Estatus` varchar(100) NOT NULL,
  `CodigoEstatus` varchar(150) NOT NULL,
  `Agregado_Por` varchar(200) NOT NULL,
  `AgregadoEl` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `Sistema` varchar(200) NOT NULL,
  `ID_H_O_D` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `inventario_inicial_estado`
--

CREATE TABLE `inventario_inicial_estado` (
  `id` int(11) NOT NULL,
  `fkSucursal` int(11) DEFAULT NULL,
  `inventario_inicial_establecido` tinyint(1) DEFAULT 0,
  `fecha_establecido` datetime DEFAULT NULL,
  `EstablecidoPor` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Inventario_lotes_fechas`
--

CREATE TABLE `Inventario_lotes_fechas` (
  `ID` int(11) NOT NULL,
  `cod_barra` varchar(100) NOT NULL,
  `lote` varchar(100) NOT NULL,
  `fecha_caducidad` date NOT NULL,
  `Cantidad` int(12) NOT NULL,
  `fecha_ingreso` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Inventario_Mobiliario`
--

CREATE TABLE `Inventario_Mobiliario` (
  `Id_inventario` int(11) NOT NULL,
  `Codigo` varchar(200) NOT NULL,
  `Articulo` varchar(100) NOT NULL,
  `Descripcion` varchar(500) NOT NULL,
  `Marca` varchar(300) NOT NULL,
  `Departamento` varchar(200) NOT NULL,
  `Responsables` varchar(200) NOT NULL,
  `Categoria` varchar(200) NOT NULL,
  `Sucursal` int(11) NOT NULL,
  `Valor` decimal(50,2) NOT NULL,
  `Antiguedad` varchar(100) NOT NULL,
  `Factura` varchar(200) NOT NULL,
  `NSerie` varchar(200) NOT NULL,
  `Vigencia` varchar(200) NOT NULL,
  `Estado` varchar(200) NOT NULL,
  `AgregadoEl` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `AgregadoPor` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Inventario_Productos_Bloqueados`
--

CREATE TABLE `Inventario_Productos_Bloqueados` (
  `ID_Bloqueo` int(11) NOT NULL,
  `ID_Turno` int(11) NOT NULL,
  `ID_Prod_POS` int(12) UNSIGNED ZEROFILL NOT NULL,
  `Cod_Barra` varchar(250) NOT NULL,
  `Fk_sucursal` int(11) NOT NULL,
  `Usuario_Bloqueo` varchar(250) NOT NULL,
  `Fecha_Bloqueo` timestamp NOT NULL DEFAULT current_timestamp(),
  `Fecha_Liberacion` timestamp NULL DEFAULT NULL,
  `Estado` enum('bloqueado','liberado') NOT NULL DEFAULT 'bloqueado'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Inventario_Turnos`
--

CREATE TABLE `Inventario_Turnos` (
  `ID_Turno` int(11) NOT NULL,
  `Folio_Turno` varchar(50) NOT NULL,
  `Fk_sucursal` int(11) NOT NULL,
  `Usuario_Inicio` varchar(250) NOT NULL,
  `Usuario_Actual` varchar(250) NOT NULL,
  `Fecha_Turno` date NOT NULL,
  `Hora_Inicio` timestamp NOT NULL DEFAULT current_timestamp(),
  `Hora_Pausa` timestamp NULL DEFAULT NULL,
  `Hora_Reanudacion` timestamp NULL DEFAULT NULL,
  `Hora_Finalizacion` timestamp NULL DEFAULT NULL,
  `Estado` enum('activo','pausado','finalizado','cancelado') NOT NULL DEFAULT 'activo',
  `Total_Productos` int(11) NOT NULL DEFAULT 0,
  `Productos_Completados` int(11) NOT NULL DEFAULT 0,
  `Limite_Productos` int(11) NOT NULL DEFAULT 50,
  `Observaciones` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Disparadores `Inventario_Turnos`
--
DELIMITER $$
CREATE TRIGGER `trg_historial_turnos` AFTER UPDATE ON `Inventario_Turnos` FOR EACH ROW BEGIN
    DECLARE v_accion VARCHAR(20);
    DECLARE v_datos_estado TEXT;
    
    -- Determinar la acción según el cambio de estado
    IF NEW.Estado = 'pausado' AND OLD.Estado = 'activo' THEN
        SET v_accion = 'pausa';
        SET v_datos_estado = JSON_OBJECT(
            'productos_completados', NEW.Productos_Completados,
            'total_productos', NEW.Total_Productos,
            'hora_pausa', NEW.Hora_Pausa
        );
    ELSEIF NEW.Estado = 'activo' AND OLD.Estado = 'pausado' THEN
        SET v_accion = 'reanudacion';
        SET v_datos_estado = JSON_OBJECT(
            'productos_completados', NEW.Productos_Completados,
            'total_productos', NEW.Total_Productos,
            'hora_reanudacion', NEW.Hora_Reanudacion
        );
    ELSEIF NEW.Estado = 'finalizado' AND OLD.Estado != 'finalizado' THEN
        SET v_accion = 'finalizacion';
        SET v_datos_estado = JSON_OBJECT(
            'productos_completados', NEW.Productos_Completados,
            'total_productos', NEW.Total_Productos,
            'hora_finalizacion', NEW.Hora_Finalizacion
        );
    ELSEIF NEW.Estado = 'cancelado' AND OLD.Estado != 'cancelado' THEN
        SET v_accion = 'cancelacion';
        SET v_datos_estado = JSON_OBJECT(
            'productos_completados', NEW.Productos_Completados,
            'total_productos', NEW.Total_Productos
        );
    END IF;
    
    -- Insertar en historial si hay una acción registrable
    IF v_accion IS NOT NULL THEN
        INSERT INTO Inventario_Turnos_Historial (
            ID_Turno,
            Folio_Turno,
            Accion,
            Usuario,
            Observaciones,
            Datos_Estado
        ) VALUES (
            NEW.ID_Turno,
            NEW.Folio_Turno,
            v_accion,
            NEW.Usuario_Actual,
            NEW.Observaciones,
            v_datos_estado
        );
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Inventario_Turnos_Historial`
--

CREATE TABLE `Inventario_Turnos_Historial` (
  `ID_Historial` int(11) NOT NULL,
  `ID_Turno` int(11) NOT NULL,
  `Folio_Turno` varchar(50) NOT NULL,
  `Accion` enum('inicio','pausa','reanudacion','finalizacion','cancelacion') NOT NULL,
  `Usuario` varchar(250) NOT NULL,
  `Fecha_Accion` timestamp NOT NULL DEFAULT current_timestamp(),
  `Observaciones` text DEFAULT NULL,
  `Datos_Estado` text DEFAULT NULL COMMENT 'JSON con el estado al momento de la acción'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Inventario_Turnos_Productos`
--

CREATE TABLE `Inventario_Turnos_Productos` (
  `ID_Registro` int(11) NOT NULL,
  `ID_Turno` int(11) NOT NULL,
  `Folio_Turno` varchar(50) NOT NULL,
  `ID_Prod_POS` int(12) UNSIGNED ZEROFILL NOT NULL,
  `Cod_Barra` varchar(250) NOT NULL,
  `Nombre_Producto` varchar(500) NOT NULL,
  `Fk_sucursal` int(11) NOT NULL,
  `Existencias_Sistema` int(11) NOT NULL DEFAULT 0,
  `Existencias_Fisicas` int(11) DEFAULT NULL,
  `Diferencia` int(11) DEFAULT NULL,
  `Usuario_Selecciono` varchar(250) NOT NULL,
  `Fecha_Seleccion` timestamp NOT NULL DEFAULT current_timestamp(),
  `Fecha_Conteo` timestamp NULL DEFAULT NULL,
  `Estado` enum('seleccionado','en_conteo','completado','liberado') NOT NULL DEFAULT 'seleccionado',
  `Observaciones` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Disparadores `Inventario_Turnos_Productos`
--
DELIMITER $$
CREATE TRIGGER `trg_bloquear_producto_inventario` AFTER INSERT ON `Inventario_Turnos_Productos` FOR EACH ROW BEGIN
    -- Insertar bloqueo del producto
    INSERT INTO Inventario_Productos_Bloqueados (
        ID_Turno,
        ID_Prod_POS,
        Cod_Barra,
        Fk_sucursal,
        Usuario_Bloqueo
    ) VALUES (
        NEW.ID_Turno,
        NEW.ID_Prod_POS,
        NEW.Cod_Barra,
        NEW.Fk_sucursal,
        NEW.Usuario_Selecciono
    )
    ON DUPLICATE KEY UPDATE
        Usuario_Bloqueo = NEW.Usuario_Selecciono,
        Fecha_Bloqueo = NOW(),
        Estado = 'bloqueado',
        Fecha_Liberacion = NULL;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_liberar_producto_inventario` AFTER UPDATE ON `Inventario_Turnos_Productos` FOR EACH ROW BEGIN
    -- Si el producto se completa o libera, quitar el bloqueo
    IF (NEW.Estado = 'completado' OR NEW.Estado = 'liberado') AND OLD.Estado != NEW.Estado THEN
        UPDATE Inventario_Productos_Bloqueados
        SET Estado = 'liberado',
            Fecha_Liberacion = NOW()
        WHERE ID_Turno = NEW.ID_Turno
          AND ID_Prod_POS = NEW.ID_Prod_POS
          AND Cod_Barra = NEW.Cod_Barra
          AND Estado = 'bloqueado';
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Licencias`
--

CREATE TABLE `Licencias` (
  `ID_Licencia` int(12) NOT NULL,
  `Nombre_Licencia` varchar(200) NOT NULL,
  `Fecha_Creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `Fecha_Vencimiento` date NOT NULL,
  `CreadoPor` varchar(200) NOT NULL,
  `CreadoEl` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ListadoServicios`
--

CREATE TABLE `ListadoServicios` (
  `Servicio_ID` int(11) UNSIGNED ZEROFILL NOT NULL,
  `Servicio` varchar(200) NOT NULL,
  `Costo` decimal(10,2) DEFAULT 0.00,
  `Comision` decimal(10,2) DEFAULT 0.00,
  `CostoVariable` varchar(1) DEFAULT 'N',
  `Estado` varchar(100) NOT NULL,
  `Agregado_Por` varchar(250) NOT NULL,
  `Agregadoel` timestamp NOT NULL DEFAULT current_timestamp(),
  `Sistema` varchar(250) NOT NULL,
  `Licencia` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Localidades`
--

CREATE TABLE `Localidades` (
  `ID_Localidad` int(11) NOT NULL,
  `Nombre_Localidad` varchar(400) NOT NULL,
  `Fk_Municipio` int(12) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `logsingresosmedicamentos`
--

CREATE TABLE `logsingresosmedicamentos` (
  `log_id` int(11) NOT NULL,
  `error_time` timestamp NULL DEFAULT current_timestamp(),
  `error_description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `logs_checador`
--

CREATE TABLE `logs_checador` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `accion` varchar(100) NOT NULL,
  `detalles` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Lotes_Descuentos_Ventas`
--

CREATE TABLE `Lotes_Descuentos_Ventas` (
  `ID_Descuento` int(11) NOT NULL,
  `ID_Venta` int(11) DEFAULT NULL,
  `Folio_Ticket` varchar(100) DEFAULT NULL,
  `ID_Prod_POS` int(12) UNSIGNED ZEROFILL NOT NULL,
  `Cod_Barra` varchar(250) NOT NULL,
  `Fk_sucursal` int(11) NOT NULL,
  `Lote` varchar(100) NOT NULL,
  `Fecha_Caducidad` date NOT NULL,
  `Cantidad_Descontada` int(11) NOT NULL DEFAULT 0,
  `Existencias_Antes` int(11) NOT NULL DEFAULT 0,
  `Existencias_Despues` int(11) NOT NULL DEFAULT 0,
  `Fecha_Descuento` timestamp NOT NULL DEFAULT current_timestamp(),
  `Usuario_Venta` varchar(250) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Marcas_POS`
--

CREATE TABLE `Marcas_POS` (
  `Marca_ID` int(11) UNSIGNED ZEROFILL NOT NULL,
  `Nom_Marca` varchar(200) NOT NULL,
  `Agregado_Por` varchar(250) NOT NULL,
  `ActualizadoPor` varchar(200) NOT NULL,
  `Agregadoel` timestamp NOT NULL DEFAULT current_timestamp(),
  `Sistema` varchar(250) NOT NULL,
  `Licencia` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Marcas_POS_Updates`
--

CREATE TABLE `Marcas_POS_Updates` (
  `ID_Update_Mar` int(11) NOT NULL,
  `Marca_ID` int(11) UNSIGNED ZEROFILL NOT NULL,
  `Nom_Marca` varchar(200) NOT NULL,
  `Estado` varchar(150) NOT NULL,
  `Cod_Estado` varchar(250) NOT NULL,
  `Agregado_Por` varchar(250) NOT NULL,
  `Agregadoel` timestamp NOT NULL DEFAULT current_timestamp(),
  `Sistema` varchar(250) NOT NULL,
  `ID_H_O_D` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Municipios`
--

CREATE TABLE `Municipios` (
  `ID_Municipio` int(11) NOT NULL,
  `Nombre_Municipio` varchar(200) NOT NULL,
  `Fk_Estado` int(11) NOT NULL,
  `Ananido` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Notificaciones`
--

CREATE TABLE `Notificaciones` (
  `ID_Notificacion` int(11) NOT NULL,
  `Tipo` varchar(50) NOT NULL COMMENT 'Tipo de notificación (por ejemplo, “Inventario Bajo”, “Producto por Vencer”, “Venta Alta”, “Corte de Caja Pendiente”)',
  `Mensaje` text NOT NULL COMMENT 'Mensaje de la notificación (por ejemplo, “El producto X tiene bajo inventario en la sucursal Y.”)',
  `Fecha` datetime NOT NULL DEFAULT current_timestamp() COMMENT 'Fecha y hora de generación de la notificación (por defecto, NOW())',
  `SucursalID` int(11) NOT NULL COMMENT 'ID de la sucursal (o “ID_Sucursal”) a la que pertenece la notificación (debe existir en la tabla “Sucursales”)',
  `Leido` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Indica si la notificación ya fue leída (o atendida) (por defecto, FALSE)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabla para almacenar las notificaciones generadas por el SP “GenerarNotificaciones”';

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Ordenes_Compra_Sugeridas`
--

CREATE TABLE `Ordenes_Compra_Sugeridas` (
  `ID_Orden` int(11) NOT NULL,
  `Folio_Prod_Stock` varchar(255) DEFAULT NULL,
  `Cod_Barra` varchar(255) DEFAULT NULL,
  `Nombre_Prod` varchar(255) DEFAULT NULL,
  `Cantidad_Sugerida` int(11) DEFAULT NULL,
  `Existencias_Actuales` int(11) DEFAULT NULL,
  `Min_Existencia` int(11) DEFAULT NULL,
  `Max_Existencia` int(11) DEFAULT NULL,
  `Fecha_Sugerencia` timestamp NULL DEFAULT current_timestamp(),
  `Estatus` enum('Pendiente','Procesada','Cancelada') DEFAULT 'Pendiente',
  `Fk_Sucursal` int(11) DEFAULT NULL,
  `ID_H_O_D` varchar(255) DEFAULT NULL,
  `Proveedor` varchar(255) DEFAULT NULL,
  `No_Orden_Sucursal` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `PagosServicios`
--

CREATE TABLE `PagosServicios` (
  `id` int(11) NOT NULL,
  `nombre_paciente` varchar(255) NOT NULL,
  `Servicio` varchar(255) NOT NULL,
  `estado` varchar(200) DEFAULT NULL,
  `costo` decimal(10,2) NOT NULL,
  `NumTicket` varchar(120) NOT NULL,
  `Fk_Sucursal` int(11) NOT NULL,
  `Fk_Caja` int(11) NOT NULL,
  `Empleado` varchar(300) NOT NULL,
  `FormaDePago` varchar(300) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Disparadores `PagosServicios`
--
DELIMITER $$
CREATE TRIGGER `tr_pago_servicio_delete` AFTER DELETE ON `PagosServicios` FOR EACH ROW BEGIN
            DECLARE v_comision DECIMAL(10, 2) DEFAULT 0.00;
            DECLARE v_total_a_restar DECIMAL(10, 2) DEFAULT 0.00;
            
            -- Obtener la comisión del servicio
            SELECT IFNULL(Comision, 0.00) INTO v_comision
            FROM ListadoServicios
            WHERE Servicio = OLD.Servicio
            LIMIT 1;
            
            -- Calcular el total a restar: costo + comisión
            SET v_total_a_restar = IFNULL(OLD.costo, 0.00) + IFNULL(v_comision, 0.00);
            
            -- Restar del valor total de la caja si existe
            IF v_total_a_restar > 0 AND OLD.Fk_Caja > 0 THEN
                UPDATE Cajas 
                SET Valor_Total_Caja = Valor_Total_Caja - v_total_a_restar
                WHERE ID_Caja = OLD.Fk_Caja;
            END IF;
        END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `tr_pago_servicio_insert` AFTER INSERT ON `PagosServicios` FOR EACH ROW BEGIN
            DECLARE v_comision DECIMAL(10, 2) DEFAULT 0.00;
            DECLARE v_total_a_sumar DECIMAL(10, 2) DEFAULT 0.00;
            
            -- Obtener la comisión del servicio desde ListadoServicios usando el nombre del servicio
            SELECT IFNULL(Comision, 0.00) INTO v_comision
            FROM ListadoServicios
            WHERE Servicio = NEW.Servicio
            LIMIT 1;
            
            -- Calcular el total a sumar: costo + comisión
            SET v_total_a_sumar = IFNULL(NEW.costo, 0.00) + IFNULL(v_comision, 0.00);
            
            -- Solo actualizar si hay un valor a sumar mayor a 0 y existe la caja
            IF v_total_a_sumar > 0 AND NEW.Fk_Caja > 0 THEN
                UPDATE Cajas 
                SET Valor_Total_Caja = Valor_Total_Caja + v_total_a_sumar
                WHERE ID_Caja = NEW.Fk_Caja;
            END IF;
        END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `tr_pago_servicio_update` AFTER UPDATE ON `PagosServicios` FOR EACH ROW BEGIN
            DECLARE v_comision_old DECIMAL(10, 2) DEFAULT 0.00;
            DECLARE v_comision_new DECIMAL(10, 2) DEFAULT 0.00;
            DECLARE v_total_old DECIMAL(10, 2) DEFAULT 0.00;
            DECLARE v_total_new DECIMAL(10, 2) DEFAULT 0.00;
            
            -- Solo procesar si cambió el costo o el servicio
            IF OLD.costo != NEW.costo OR OLD.Servicio != NEW.Servicio THEN
                -- Obtener comisión del servicio anterior
                SELECT IFNULL(Comision, 0.00) INTO v_comision_old
                FROM ListadoServicios
                WHERE Servicio = OLD.Servicio
                LIMIT 1;
                
                -- Obtener comisión del servicio nuevo
                SELECT IFNULL(Comision, 0.00) INTO v_comision_new
                FROM ListadoServicios
                WHERE Servicio = NEW.Servicio
                LIMIT 1;
                
                -- Calcular totales
                SET v_total_old = IFNULL(OLD.costo, 0.00) + IFNULL(v_comision_old, 0.00);
                SET v_total_new = IFNULL(NEW.costo, 0.00) + IFNULL(v_comision_new, 0.00);
                
                -- Restar el valor anterior si existe
                IF v_total_old > 0 AND OLD.Fk_Caja > 0 THEN
                    UPDATE Cajas 
                    SET Valor_Total_Caja = Valor_Total_Caja - v_total_old
                    WHERE ID_Caja = OLD.Fk_Caja;
                END IF;
                
                -- Sumar el nuevo valor
                IF v_total_new > 0 AND NEW.Fk_Caja > 0 THEN
                    UPDATE Cajas 
                    SET Valor_Total_Caja = Valor_Total_Caja + v_total_new
                    WHERE ID_Caja = NEW.Fk_Caja;
                END IF;
            END IF;
        END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedidos`
--

CREATE TABLE `pedidos` (
  `id` int(11) NOT NULL,
  `folio` varchar(50) NOT NULL,
  `sucursal_id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `estado` enum('pendiente','aprobado','rechazado','en_proceso','completado','cancelado') DEFAULT 'pendiente',
  `prioridad` enum('baja','normal','alta','urgente') DEFAULT 'normal',
  `tipo_origen` enum('admin','farmacia','cedis','sucursal') DEFAULT 'admin',
  `observaciones` text DEFAULT NULL,
  `fecha_creacion` datetime DEFAULT current_timestamp(),
  `fecha_aprobacion` datetime DEFAULT NULL,
  `fecha_completado` datetime DEFAULT NULL,
  `aprobado_por` int(11) DEFAULT NULL,
  `total_estimado` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedido_detalles`
--

CREATE TABLE `pedido_detalles` (
  `id` int(11) NOT NULL,
  `pedido_id` int(11) NOT NULL,
  `producto_id` int(11) NOT NULL,
  `cantidad_solicitada` decimal(10,2) NOT NULL,
  `cantidad_aprobada` decimal(10,2) DEFAULT NULL,
  `cantidad_recibida` decimal(10,2) DEFAULT 0.00,
  `precio_unitario` decimal(10,2) DEFAULT NULL,
  `subtotal` decimal(10,2) DEFAULT 0.00,
  `observaciones` text DEFAULT NULL,
  `estado` enum('pendiente','aprobado','rechazado','recibido') DEFAULT 'pendiente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedido_historial`
--

CREATE TABLE `pedido_historial` (
  `id` int(11) NOT NULL,
  `pedido_id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `estado_anterior` varchar(50) DEFAULT NULL,
  `estado_nuevo` varchar(50) DEFAULT NULL,
  `comentario` text DEFAULT NULL,
  `fecha_cambio` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Presentaciones`
--

CREATE TABLE `Presentaciones` (
  `Presentacion_ID` int(11) UNSIGNED ZEROFILL NOT NULL,
  `Nom_Presentacion` varchar(250) NOT NULL,
  `Estado` varchar(150) NOT NULL,
  `Agregado_Por` varchar(200) NOT NULL,
  `Agregadoel` timestamp NOT NULL DEFAULT current_timestamp(),
  `ActualizadoPor` int(200) NOT NULL,
  `Sistema` varchar(250) NOT NULL,
  `Licencia` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Presentacion_Prod_POS_Updates`
--

CREATE TABLE `Presentacion_Prod_POS_Updates` (
  `ID_Update_Pre` int(11) NOT NULL,
  `Pprod_ID` int(11) UNSIGNED ZEROFILL NOT NULL,
  `Nom_Presentacion` varchar(250) NOT NULL,
  `Siglas` varchar(100) NOT NULL,
  `Estado` varchar(150) NOT NULL,
  `Cod_Estado` varchar(200) NOT NULL,
  `Agregado_Por` varchar(200) NOT NULL,
  `Agregadoel` timestamp NOT NULL DEFAULT current_timestamp(),
  `Sistema` varchar(250) NOT NULL,
  `Licencia` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Productos`
--

CREATE TABLE `Productos` (
  `ID_Producto` int(11) NOT NULL,
  `Folio_Producto` varchar(50) DEFAULT NULL,
  `Codigo_Barra` varchar(50) DEFAULT NULL,
  `Nombre_Producto` varchar(100) NOT NULL,
  `Maximo` decimal(10,2) DEFAULT NULL,
  `Minimo` decimal(10,2) DEFAULT NULL,
  `Fecha_Creacion` datetime DEFAULT current_timestamp(),
  `Fecha_Actualizacion` datetime DEFAULT NULL,
  `Activo` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos_lotes_caducidad`
--

CREATE TABLE `productos_lotes_caducidad` (
  `id_lote` int(11) NOT NULL,
  `folio_stock` int(10) UNSIGNED ZEROFILL NOT NULL,
  `cod_barra` varchar(100) NOT NULL,
  `nombre_producto` varchar(250) NOT NULL,
  `lote` varchar(100) NOT NULL,
  `fecha_caducidad` date NOT NULL,
  `fecha_ingreso` date NOT NULL,
  `cantidad_inicial` int(11) NOT NULL,
  `cantidad_actual` int(11) NOT NULL,
  `sucursal_id` int(11) NOT NULL,
  `proveedor` varchar(255) DEFAULT NULL,
  `precio_compra` decimal(10,2) DEFAULT NULL,
  `precio_venta` decimal(10,2) DEFAULT NULL,
  `estado` enum('activo','agotado','vencido','retirado') DEFAULT 'activo',
  `usuario_registro` int(11) NOT NULL,
  `fecha_registro` timestamp NULL DEFAULT current_timestamp(),
  `fecha_actualizacion` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `observaciones` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Productos_POS`
--

CREATE TABLE `Productos_POS` (
  `ID_Prod_POS` int(12) UNSIGNED ZEROFILL NOT NULL,
  `Cod_Barra` varchar(250) DEFAULT NULL,
  `Clave_adicional` varchar(15) DEFAULT NULL,
  `Clave_Levic` varchar(100) NOT NULL,
  `Nombre_Prod` varchar(1000) DEFAULT NULL,
  `Precio_Venta` decimal(50,2) DEFAULT NULL,
  `Precio_C` decimal(50,2) DEFAULT NULL,
  `Tipo_Servicio` int(10) UNSIGNED ZEROFILL DEFAULT NULL,
  `Componente_Activo` varchar(250) NOT NULL,
  `Tipo` varchar(500) DEFAULT NULL,
  `FkCategoria` varchar(500) DEFAULT NULL,
  `FkMarca` varchar(500) DEFAULT NULL,
  `FkPresentacion` varchar(500) DEFAULT NULL,
  `Proveedor1` varchar(250) DEFAULT NULL,
  `Proveedor2` varchar(200) NOT NULL,
  `RecetaMedica` varchar(100) DEFAULT NULL,
  `AgregadoPor` varchar(250) NOT NULL,
  `AgregadoEl` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `Licencia` varchar(30) NOT NULL,
  `Ivaal16` varchar(100) NOT NULL,
  `ActualizadoPor` varchar(250) NOT NULL,
  `ActualizadoEl` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE current_timestamp(),
  `Contable` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Disparadores `Productos_POS`
--
DELIMITER $$
CREATE TRIGGER `after_delete_productos_pos` AFTER DELETE ON `Productos_POS` FOR EACH ROW BEGIN
    -- Insertar datos en Productos_POS_Eliminados
    INSERT INTO Productos_POS_Eliminados (
        ID_Prod_POS, Cod_Barra, Clave_adicional, Clave_Levic, Nombre_Prod, Precio_Venta, Precio_C, 
        Tipo_Servicio, Componente_Activo, Tipo, FkCategoria, FkMarca, FkPresentacion, Proveedor1, 
        Proveedor2, RecetaMedica, AgregadoPor, AgregadoEl, Licencia, Ivaal16, ActualizadoPor, 
        ActualizadoEl, Contable
    ) VALUES (
        OLD.ID_Prod_POS, OLD.Cod_Barra, OLD.Clave_adicional, OLD.Clave_Levic, OLD.Nombre_Prod, OLD.Precio_Venta, OLD.Precio_C, 
        OLD.Tipo_Servicio, OLD.Componente_Activo, OLD.Tipo, OLD.FkCategoria, OLD.FkMarca, OLD.FkPresentacion, OLD.Proveedor1, 
        OLD.Proveedor2, OLD.RecetaMedica, OLD.AgregadoPor, OLD.AgregadoEl, OLD.Licencia, OLD.Ivaal16, OLD.ActualizadoPor, 
        OLD.ActualizadoEl, OLD.Contable
    );

    -- Eliminar registros relacionados en CEDIS
    DELETE FROM CEDIS
    WHERE ID_Prod_POS = OLD.ID_Prod_POS;

    -- Eliminar registros relacionados en Stock_POS
    DELETE FROM Stock_POS
    WHERE ID_Prod_POS = OLD.ID_Prod_POS;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `after_insert_productos_pos` AFTER INSERT ON `Productos_POS` FOR EACH ROW BEGIN
    INSERT INTO Productos_POS_Auditoria (
        ID_Prod_POS, Cod_Barra, Clave_adicional, Clave_Levic, Nombre_Prod, Precio_Venta, Precio_C, Tipo_Servicio, Componente_Activo, Tipo, FkCategoria, FkMarca, FkPresentacion, Proveedor1, Proveedor2, RecetaMedica, AgregadoPor, AgregadoEl, Licencia, Ivaal16, ActualizadoPor, ActualizadoEl
    )
    VALUES (
        NEW.ID_Prod_POS, NEW.Cod_Barra, NEW.Clave_adicional, NEW.Clave_Levic, NEW.Nombre_Prod, NEW.Precio_Venta, NEW.Precio_C, NEW.Tipo_Servicio, NEW.Componente_Activo, NEW.Tipo, NEW.FkCategoria, NEW.FkMarca, NEW.FkPresentacion, NEW.Proveedor1, NEW.Proveedor2, NEW.RecetaMedica, NEW.AgregadoPor, NEW.AgregadoEl, NEW.Licencia, NEW.Ivaal16, NEW.ActualizadoPor, NEW.ActualizadoEl
    );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `after_update_productos_pos` AFTER UPDATE ON `Productos_POS` FOR EACH ROW BEGIN
    INSERT INTO Productos_POS_Auditoria (
        ID_Prod_POS, Cod_Barra, Clave_adicional, Clave_Levic, Nombre_Prod, Precio_Venta, Precio_C, Tipo_Servicio, Componente_Activo, Tipo, FkCategoria, FkMarca, FkPresentacion, Proveedor1, Proveedor2, RecetaMedica, AgregadoPor, AgregadoEl, Licencia, Ivaal16, ActualizadoPor, ActualizadoEl
    )
    VALUES (
        NEW.ID_Prod_POS, NEW.Cod_Barra, NEW.Clave_adicional, NEW.Clave_Levic, NEW.Nombre_Prod, NEW.Precio_Venta, NEW.Precio_C, NEW.Tipo_Servicio, NEW.Componente_Activo, NEW.Tipo, NEW.FkCategoria, NEW.FkMarca, NEW.FkPresentacion, NEW.Proveedor1, NEW.Proveedor2, NEW.RecetaMedica, NEW.AgregadoPor, NEW.AgregadoEl, NEW.Licencia, NEW.Ivaal16, NEW.ActualizadoPor, NEW.ActualizadoEl
    );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `after_update_productos_pos_CEDIS_Stocks` AFTER UPDATE ON `Productos_POS` FOR EACH ROW BEGIN
    DECLARE exit handler FOR SQLEXCEPTION
    BEGIN
        DECLARE err_msg TEXT;
        GET DIAGNOSTICS CONDITION 1 err_msg = MESSAGE_TEXT;
        INSERT INTO error_log_act_prod (error_message) VALUES (err_msg);
    END;

    -- Actualizar la tabla CEDIS
    UPDATE CEDIS
    SET 
        Cod_Barra = NEW.Cod_Barra, 
        Clave_adicional = NEW.Clave_adicional,
        Clave_Levic = NEW.Clave_Levic,
        Nombre_Prod = NEW.Nombre_Prod,
        Precio_Venta = NEW.Precio_Venta,
        Precio_C = NEW.Precio_C,
        Tipo_Servicio = NEW.Tipo_Servicio,
        Componente_Activo = NEW.Componente_Activo,
        Tipo = NEW.Tipo,
        FkCategoria = NEW.FkCategoria,
        FkMarca = NEW.FkMarca,
        FkPresentacion = NEW.FkPresentacion,
        Proveedor1 = NEW.Proveedor1,
        Proveedor2 = NEW.Proveedor2,
        RecetaMedica = NEW.RecetaMedica
    WHERE ID_Prod_POS = OLD.ID_Prod_POS;

    -- Actualizar la tabla Stock_POS
    UPDATE Stock_POS
    SET 
        Clave_adicional = NEW.Clave_adicional,
        Clave_Levic = NEW.Clave_Levic,
        Cod_Barra = NEW.Cod_Barra,
        Nombre_Prod = NEW.Nombre_Prod,
        Precio_Venta = NEW.Precio_Venta,
        Precio_C = NEW.Precio_C,
        Tipo_Servicio = NEW.Tipo_Servicio,
        Tipo = NEW.Tipo,
        FkCategoria = NEW.FkCategoria,
        FkMarca = NEW.FkMarca,
        FkPresentacion = NEW.FkPresentacion,
        Proveedor1 = NEW.Proveedor1,
        Proveedor2 = NEW.Proveedor2
     WHERE ID_Prod_POS = OLD.ID_Prod_POS;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `agregar_producto_despues_insercion` AFTER INSERT ON `Productos_POS` FOR EACH ROW BEGIN
    INSERT INTO CEDIS (IdProdCedis, ID_Prod_POS, Cod_Barra, Clave_adicional, Clave_Levic, Nombre_Prod, Precio_Venta, Precio_C, Existencias, Tipo_Servicio, Componente_Activo, Tipo, FkCategoria, FkMarca, FkPresentacion, Proveedor1,Proveedor2, RecetaMedica,AgregadoPor, AgregadoEl,Contable,Licencia)
    VALUES (NULL, NEW.ID_Prod_POS, NEW.Cod_Barra, NEW.Clave_adicional, NEW.Clave_Levic, NEW.Nombre_Prod, NEW.Precio_Venta, NEW.Precio_C, 0, NEW.Tipo_Servicio, NEW.Componente_Activo, NEW.Tipo, NEW.FkCategoria, NEW.FkMarca, NEW.FkPresentacion, NEW.Proveedor1,  NEW.Proveedor2, NEW.RecetaMedica,  NEW.AgregadoPor, NEW.AgregadoEl,NEW.Contable,NEW.Licencia);
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Productos_POS_Auditoria`
--

CREATE TABLE `Productos_POS_Auditoria` (
  `Id_Auditoria` int(12) NOT NULL,
  `ID_Prod_POS` int(12) UNSIGNED ZEROFILL NOT NULL,
  `Cod_Barra` varchar(250) DEFAULT NULL,
  `Clave_adicional` varchar(15) DEFAULT NULL,
  `Clave_Levic` varchar(100) NOT NULL,
  `Nombre_Prod` varchar(1000) DEFAULT NULL,
  `Precio_Venta` decimal(50,2) DEFAULT NULL,
  `Precio_C` decimal(50,2) DEFAULT NULL,
  `Tipo_Servicio` int(10) UNSIGNED ZEROFILL DEFAULT NULL,
  `Componente_Activo` varchar(250) NOT NULL,
  `Tipo` varchar(500) DEFAULT NULL,
  `FkCategoria` varchar(500) DEFAULT NULL,
  `FkMarca` varchar(500) DEFAULT NULL,
  `FkPresentacion` varchar(500) DEFAULT NULL,
  `Proveedor1` varchar(250) DEFAULT NULL,
  `Proveedor2` varchar(200) NOT NULL,
  `RecetaMedica` varchar(100) DEFAULT NULL,
  `AgregadoPor` varchar(250) NOT NULL,
  `AgregadoEl` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `Licencia` varchar(30) NOT NULL,
  `Ivaal16` varchar(100) NOT NULL,
  `ActualizadoPor` varchar(250) NOT NULL,
  `ActualizadoEl` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Productos_POS_Eliminados`
--

CREATE TABLE `Productos_POS_Eliminados` (
  `EliminadoIDPOS` int(10) UNSIGNED ZEROFILL NOT NULL,
  `ID_Prod_POS` int(12) UNSIGNED ZEROFILL NOT NULL,
  `Cod_Barra` varchar(250) DEFAULT NULL,
  `Clave_adicional` varchar(15) DEFAULT NULL,
  `Clave_Levic` varchar(100) NOT NULL,
  `Nombre_Prod` varchar(1000) DEFAULT NULL,
  `Precio_Venta` decimal(50,2) DEFAULT NULL,
  `Precio_C` decimal(50,2) DEFAULT NULL,
  `Tipo_Servicio` int(10) UNSIGNED ZEROFILL DEFAULT NULL,
  `Componente_Activo` varchar(250) NOT NULL,
  `Tipo` varchar(500) DEFAULT NULL,
  `FkCategoria` varchar(500) DEFAULT NULL,
  `FkMarca` varchar(500) DEFAULT NULL,
  `FkPresentacion` varchar(500) DEFAULT NULL,
  `Proveedor1` varchar(250) DEFAULT NULL,
  `Proveedor2` varchar(200) NOT NULL,
  `RecetaMedica` varchar(100) DEFAULT NULL,
  `AgregadoPor` varchar(250) NOT NULL,
  `AgregadoEl` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `Licencia` varchar(30) NOT NULL,
  `Ivaal16` varchar(100) NOT NULL,
  `ActualizadoPor` varchar(250) NOT NULL,
  `ActualizadoEl` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE current_timestamp(),
  `Contable` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `producto_proveedor`
--

CREATE TABLE `producto_proveedor` (
  `id` int(11) NOT NULL,
  `producto_id` int(11) NOT NULL,
  `proveedor_id` int(11) NOT NULL,
  `codigo_proveedor` varchar(100) DEFAULT NULL,
  `precio_proveedor` decimal(10,2) DEFAULT NULL,
  `tiempo_entrega_dias` int(11) DEFAULT 7,
  `activo` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Proveedores`
--

CREATE TABLE `Proveedores` (
  `ID_Proveedor` int(11) UNSIGNED ZEROFILL NOT NULL,
  `Nombre_Proveedor` varchar(250) NOT NULL,
  `Clave_Proveedor` varchar(12) NOT NULL,
  `Numero_Contacto` varchar(50) NOT NULL,
  `Correo_Electronico` varchar(150) NOT NULL,
  `AgregadoPor` varchar(250) NOT NULL,
  `AgregadoEl` timestamp NOT NULL DEFAULT current_timestamp(),
  `ActualizadoPor` varchar(200) NOT NULL,
  `Sistema` varchar(250) NOT NULL,
  `Licencia` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proveedores_pedidos`
--

CREATE TABLE `proveedores_pedidos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `contacto` varchar(255) DEFAULT NULL,
  `telefono` varchar(50) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `direccion` text DEFAULT NULL,
  `activo` tinyint(1) DEFAULT 1,
  `fecha_creacion` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `recordatorios_config_whatsapp`
--

CREATE TABLE `recordatorios_config_whatsapp` (
  `id_config` int(11) NOT NULL,
  `api_url` varchar(500) NOT NULL,
  `api_token` text NOT NULL,
  `numero_telefono` varchar(20) NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_actualizacion` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `usuario_configurador` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Configuración de WhatsApp para recordatorios';

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `recordatorios_destinatarios`
--

CREATE TABLE `recordatorios_destinatarios` (
  `id_destinatario` int(11) NOT NULL,
  `recordatorio_id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `telefono_whatsapp` varchar(20) DEFAULT NULL,
  `estado_envio` enum('pendiente','enviado','error','cancelado') NOT NULL DEFAULT 'pendiente',
  `fecha_envio` timestamp NULL DEFAULT NULL,
  `error_envio` text DEFAULT NULL,
  `tipo_envio` enum('whatsapp','notificacion','ambos') NOT NULL DEFAULT 'ambos',
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Destinatarios específicos para recordatorios';

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `recordatorios_grupos`
--

CREATE TABLE `recordatorios_grupos` (
  `id_grupo` int(11) NOT NULL,
  `nombre_grupo` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `sucursal_id` int(11) DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_actualizacion` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `usuario_creador` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Grupos de destinatarios para recordatorios';

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `recordatorios_grupos_miembros`
--

CREATE TABLE `recordatorios_grupos_miembros` (
  `id_miembro` int(11) NOT NULL,
  `grupo_id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `fecha_agregado` timestamp NOT NULL DEFAULT current_timestamp(),
  `activo` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Miembros de grupos de recordatorios';

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `recordatorios_logs`
--

CREATE TABLE `recordatorios_logs` (
  `id_log` int(11) NOT NULL,
  `recordatorio_id` int(11) NOT NULL,
  `destinatario_id` int(11) DEFAULT NULL,
  `tipo_envio` enum('whatsapp','notificacion','ambos') NOT NULL,
  `estado` enum('iniciado','exitoso','error','cancelado') NOT NULL,
  `mensaje` text DEFAULT NULL,
  `detalles_tecnico` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`detalles_tecnico`)),
  `fecha_log` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Logs de envío de recordatorios';

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Recordatorios_Pendientes`
--

CREATE TABLE `Recordatorios_Pendientes` (
  `ID_Notificacion` int(11) NOT NULL,
  `Encabezado` varchar(200) NOT NULL,
  `TipoMensaje` varchar(200) NOT NULL,
  `Mensaje_Recordatorio` varchar(500) NOT NULL,
  `Registrado` varchar(200) NOT NULL,
  `Sistema` varchar(150) NOT NULL,
  `Sucursal` int(11) NOT NULL,
  `Estado` int(11) NOT NULL,
  `Licencia` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `recordatorios_plantillas`
--

CREATE TABLE `recordatorios_plantillas` (
  `id_plantilla` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `tipo` enum('whatsapp','notificacion','ambos') NOT NULL DEFAULT 'ambos',
  `plantilla_whatsapp` text DEFAULT NULL,
  `plantilla_notificacion` text DEFAULT NULL,
  `variables_disponibles` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`variables_disponibles`)),
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_actualizacion` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `usuario_creador` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Plantillas de mensajes para recordatorios';

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `recordatorios_sistema`
--

CREATE TABLE `recordatorios_sistema` (
  `id_recordatorio` int(11) NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `mensaje_whatsapp` text DEFAULT NULL,
  `mensaje_notificacion` text DEFAULT NULL,
  `fecha_programada` datetime NOT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_actualizacion` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `prioridad` enum('baja','media','alta','urgente') NOT NULL DEFAULT 'media',
  `estado` enum('programado','enviando','enviado','cancelado','error') NOT NULL DEFAULT 'programado',
  `tipo_envio` set('whatsapp','notificacion','ambos') NOT NULL DEFAULT 'ambos',
  `destinatarios` enum('todos','sucursal','grupo','individual') NOT NULL DEFAULT 'todos',
  `sucursal_id` int(11) DEFAULT NULL,
  `grupo_id` int(11) DEFAULT NULL,
  `usuario_creador` int(11) NOT NULL,
  `usuario_modificador` int(11) DEFAULT NULL,
  `intentos_envio` int(11) NOT NULL DEFAULT 0,
  `max_intentos` int(11) NOT NULL DEFAULT 3,
  `fecha_ultimo_intento` timestamp NULL DEFAULT NULL,
  `error_ultimo_intento` text DEFAULT NULL,
  `configuracion_adicional` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`configuracion_adicional`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabla principal de recordatorios del sistema';

--
-- Disparadores `recordatorios_sistema`
--
DELIMITER $$
CREATE TRIGGER `tr_recordatorios_log_estado` AFTER UPDATE ON `recordatorios_sistema` FOR EACH ROW BEGIN
    IF OLD.estado != NEW.estado THEN
        INSERT INTO recordatorios_logs (recordatorio_id, tipo_envio, estado, mensaje, detalles_tecnico)
        VALUES (NEW.id_recordatorio, 'ambos', NEW.estado, 
                CONCAT('Estado cambiado de ', OLD.estado, ' a ', NEW.estado),
                JSON_OBJECT('estado_anterior', OLD.estado, 'estado_nuevo', NEW.estado, 'fecha_cambio', NOW()));
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `tr_recordatorios_sistema_update` BEFORE UPDATE ON `recordatorios_sistema` FOR EACH ROW BEGIN
    SET NEW.fecha_actualizacion = CURRENT_TIMESTAMP;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Registros_Energia`
--

CREATE TABLE `Registros_Energia` (
  `Id_Registro` int(11) NOT NULL,
  `Registro_Watts` varchar(100) NOT NULL,
  `Fecha_registro` date NOT NULL,
  `Sucursal` varchar(100) NOT NULL,
  `Comentario` varchar(200) NOT NULL,
  `Registro` varchar(120) NOT NULL,
  `Agregadoel` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `Licencia` varchar(100) NOT NULL,
  `file_name` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `registro_errores_Actualizacionanaqueles`
--

CREATE TABLE `registro_errores_Actualizacionanaqueles` (
  `id` int(11) NOT NULL,
  `mensaje_error` varchar(255) NOT NULL,
  `fecha` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Servicios_POS`
--

CREATE TABLE `Servicios_POS` (
  `Servicio_ID` int(11) UNSIGNED ZEROFILL NOT NULL,
  `Nom_Serv` varchar(200) NOT NULL,
  `Estado` varchar(100) NOT NULL,
  `Agregado_Por` varchar(250) NOT NULL,
  `ActualizadoPor` varchar(100) NOT NULL,
  `Agregadoel` timestamp NOT NULL DEFAULT current_timestamp(),
  `Sistema` varchar(250) NOT NULL,
  `Licencia` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Disparadores `Servicios_POS`
--
DELIMITER $$
CREATE TRIGGER `Audita_Servicios_POS` AFTER INSERT ON `Servicios_POS` FOR EACH ROW INSERT INTO Servicios_POS_Audita
(Servicio_ID,Nom_Serv,Estado,Agregado_Por,Agregadoel,Sistema,Licencia)
    VALUES (NEW.Servicio_ID,NEW.Nom_Serv,NEW.Estado,NEW.Agregado_Por,Now(),NEW.Sistema,NEW.Licencia)
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `Audita_Servicios_POS_Updates` AFTER UPDATE ON `Servicios_POS` FOR EACH ROW INSERT INTO Servicios_POS_Audita
(Servicio_ID,Nom_Serv,Estado,ActualizadoPor,Agregadoel,Sistema,Licencia)
    VALUES (NEW.Servicio_ID,NEW.Nom_Serv,NEW.Estado,NEW.ActualizadoPor,Now(),NEW.Sistema,NEW.Licencia)
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Servicios_POS_Audita`
--

CREATE TABLE `Servicios_POS_Audita` (
  `Audita_Serv_ID` int(11) NOT NULL,
  `Servicio_ID` int(11) UNSIGNED ZEROFILL NOT NULL,
  `Nom_Serv` varchar(200) NOT NULL,
  `Estado` varchar(100) NOT NULL,
  `Agregado_Por` varchar(250) NOT NULL,
  `ActualizadoPor` varchar(200) NOT NULL,
  `Agregadoel` timestamp NOT NULL DEFAULT current_timestamp(),
  `Sistema` varchar(250) NOT NULL,
  `Licencia` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Solicitudes_Ingresos`
--

CREATE TABLE `Solicitudes_Ingresos` (
  `IdProdCedis` int(12) UNSIGNED ZEROFILL NOT NULL,
  `ID_Prod_POS` int(12) UNSIGNED ZEROFILL NOT NULL,
  `NumFactura` varchar(200) NOT NULL,
  `Proveedor` varchar(200) NOT NULL,
  `Cod_Barra` varchar(250) DEFAULT NULL,
  `Nombre_Prod` varchar(1000) DEFAULT NULL,
  `Fk_Sucursal` int(11) NOT NULL,
  `Contabilizado` int(11) NOT NULL,
  `Fecha_Caducidad` date NOT NULL,
  `Lote` varchar(100) NOT NULL,
  `PrecioMaximo` double(50,2) NOT NULL,
  `Precio_Venta` decimal(50,2) DEFAULT NULL,
  `Precio_C` decimal(50,2) DEFAULT NULL,
  `AgregadoPor` varchar(250) NOT NULL,
  `AgregadoEl` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `FechaInventario` date NOT NULL,
  `Estatus` varchar(200) NOT NULL,
  `NumOrden` int(10) UNSIGNED ZEROFILL NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Disparadores `Solicitudes_Ingresos`
--
DELIMITER $$
CREATE TRIGGER `after_delete_solicitudes_ingresos` AFTER DELETE ON `Solicitudes_Ingresos` FOR EACH ROW BEGIN
    INSERT INTO Solicitudes_Ingresos_Eliminados (
        IdProdCedis, 
        ID_Prod_POS, 
        NumFactura, 
        Proveedor, 
        Cod_Barra, 
        Nombre_Prod, 
        Fk_Sucursal, 
        Contabilizado, 
        Fecha_Caducidad, 
        Lote, 
        PrecioMaximo, 
        Precio_Venta, 
        Precio_C, 
        AgregadoPor, 
        AgregadoEl, 
        FechaInventario, 
        Estatus, 
        NumOrden
    ) VALUES (
        OLD.IdProdCedis, 
        OLD.ID_Prod_POS, 
        OLD.NumFactura, 
        OLD.Proveedor, 
        OLD.Cod_Barra, 
        OLD.Nombre_Prod, 
        OLD.Fk_Sucursal, 
        OLD.Contabilizado, 
        OLD.Fecha_Caducidad, 
        OLD.Lote, 
        OLD.PrecioMaximo, 
        OLD.Precio_Venta, 
        OLD.Precio_C, 
        OLD.AgregadoPor, 
        OLD.AgregadoEl, 
        OLD.FechaInventario, 
        OLD.Estatus, 
        OLD.NumOrden
    );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `after_insert_solicitudes_ingresos` AFTER INSERT ON `Solicitudes_Ingresos` FOR EACH ROW BEGIN
    -- Verificar si ya existe una notificación para el NumOrden
    IF NOT EXISTS (
        SELECT 1 
        FROM Recordatorios_Pendientes 
        WHERE Encabezado = CONCAT('Nueva solicitud de ingreso - ', NEW.NumOrden)
    ) THEN
        INSERT INTO Recordatorios_Pendientes (
            Encabezado, 
            TipoMensaje, 
            Mensaje_Recordatorio, 
            Registrado, 
            Sistema, 
            Sucursal, 
            Estado, 
            Licencia
        ) VALUES (
            CONCAT('Nueva solicitud de ingreso - ', NEW.NumOrden),
            'Ingreso de medicamentos',
            CONCAT(
                'Se ha registrado con el NumOrden ', NEW.NumOrden, 
                ' del proveedor ', NEW.Proveedor, 
                ' con el NumFactura ', NEW.NumFactura, 
                ' COn estatus de pendiente'
            ),
            NEW.AgregadoPor,
            'Ventas',  -- Aquí puedes reemplazar 'Sistema' por el valor que corresponda
            NEW.Fk_Sucursal,
            'Pendiente',
            'Doctor Pez'
        );
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Solicitudes_Ingresos_Eliminados`
--

CREATE TABLE `Solicitudes_Ingresos_Eliminados` (
  `Id_Eliminado` int(10) UNSIGNED ZEROFILL NOT NULL,
  `IdProdCedis` int(12) UNSIGNED ZEROFILL NOT NULL,
  `ID_Prod_POS` int(12) UNSIGNED ZEROFILL NOT NULL,
  `NumFactura` varchar(200) NOT NULL,
  `Proveedor` varchar(200) NOT NULL,
  `Cod_Barra` varchar(250) DEFAULT NULL,
  `Nombre_Prod` varchar(1000) DEFAULT NULL,
  `Fk_Sucursal` int(11) NOT NULL,
  `Contabilizado` int(11) NOT NULL,
  `Fecha_Caducidad` date NOT NULL,
  `Lote` varchar(100) NOT NULL,
  `PrecioMaximo` double(50,2) NOT NULL,
  `Precio_Venta` decimal(50,2) DEFAULT NULL,
  `Precio_C` decimal(50,2) DEFAULT NULL,
  `AgregadoPor` varchar(250) NOT NULL,
  `AgregadoEl` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `FechaInventario` date NOT NULL,
  `Estatus` varchar(200) NOT NULL,
  `NumOrden` int(10) UNSIGNED ZEROFILL NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Stock_POS`
--

CREATE TABLE `Stock_POS` (
  `Folio_Prod_Stock` int(10) UNSIGNED ZEROFILL NOT NULL,
  `ID_Prod_POS` int(12) UNSIGNED ZEROFILL NOT NULL,
  `Clave_adicional` varchar(15) DEFAULT NULL,
  `Clave_Levic` varchar(100) NOT NULL,
  `Cod_Barra` varchar(100) NOT NULL,
  `Nombre_Prod` varchar(250) NOT NULL,
  `Fk_sucursal` int(12) NOT NULL,
  `Precio_Venta` decimal(50,2) NOT NULL,
  `Precio_C` decimal(50,2) NOT NULL,
  `Max_Existencia` int(11) NOT NULL,
  `Min_Existencia` int(12) NOT NULL,
  `Existencias_R` int(11) NOT NULL,
  `Lote` varchar(100) NOT NULL,
  `Fecha_Caducidad` date NOT NULL,
  `Fecha_Ingreso` date NOT NULL,
  `Tipo_Servicio` int(10) UNSIGNED ZEROFILL NOT NULL,
  `Tipo` varchar(500) NOT NULL,
  `FkCategoria` varchar(500) NOT NULL,
  `FkMarca` varchar(500) NOT NULL,
  `FkPresentacion` varchar(500) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL,
  `Proveedor1` varchar(250) DEFAULT NULL,
  `Proveedor2` varchar(250) DEFAULT NULL,
  `Estatus` varchar(150) NOT NULL,
  `Sistema` varchar(200) NOT NULL,
  `AgregadoPor` varchar(250) NOT NULL,
  `AgregadoEl` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `ID_H_O_D` varchar(100) NOT NULL,
  `ActualizoFecha` varchar(200) NOT NULL,
  `Cod_Paquete` int(11) NOT NULL,
  `ActualizadoPor` varchar(200) NOT NULL,
  `Contable` varchar(200) NOT NULL,
  `Anaquel` varchar(100) NOT NULL,
  `Repisa` varchar(200) NOT NULL,
  `UltimoInventarioPor` varchar(200) NOT NULL,
  `FechaUltimoInventario` date NOT NULL,
  `JustificacionAjuste` varchar(300) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Disparadores `Stock_POS`
--
DELIMITER $$
CREATE TRIGGER `trg_AfterStockInsert` AFTER INSERT ON `Stock_POS` FOR EACH ROW BEGIN
    DECLARE existe_lote INT;

    -- Verificar si el producto, lote y sucursal ya existen en Historial_Lotes
    SELECT COUNT(*) INTO existe_lote 
    FROM Historial_Lotes 
    WHERE ID_Prod_POS = NEW.ID_Prod_POS 
      AND Lote = NEW.Lote
      AND Fk_sucursal = NEW.Fk_sucursal;

    IF existe_lote = 0 THEN
        -- Insertar nuevo lote en el historial
        INSERT INTO Historial_Lotes (
            ID_Prod_POS, Fk_sucursal, Lote, Fecha_Caducidad, Fecha_Ingreso, Existencias, Usuario_Modifico
        ) VALUES (
            NEW.ID_Prod_POS, NEW.Fk_sucursal, NEW.Lote, NEW.Fecha_Caducidad, NEW.Fecha_Ingreso, NEW.Existencias_R, NEW.AgregadoPor
        );
    ELSE
        -- Si el lote ya existe en la misma sucursal, actualizar existencias
        UPDATE Historial_Lotes
        SET Existencias = NEW.Existencias_R,
            Fecha_Ingreso = NEW.Fecha_Ingreso,
            Usuario_Modifico = NEW.AgregadoPor
        WHERE ID_Prod_POS = NEW.ID_Prod_POS 
          AND Lote = NEW.Lote
          AND Fk_sucursal = NEW.Fk_sucursal;
    END IF;

END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_AfterStockUpdate` AFTER UPDATE ON `Stock_POS` FOR EACH ROW BEGIN
    DECLARE existe_lote INT;

    -- Verificar si el producto, lote y sucursal ya existen en Historial_Lotes
    SELECT COUNT(*) INTO existe_lote 
    FROM Historial_Lotes 
    WHERE ID_Prod_POS = NEW.ID_Prod_POS 
      AND Lote = NEW.Lote
      AND Fk_sucursal = NEW.Fk_sucursal;

    IF existe_lote = 0 THEN
        -- Insertar nuevo lote en el historial
        INSERT INTO Historial_Lotes (
            ID_Prod_POS, Fk_sucursal, Lote, Fecha_Caducidad, Fecha_Ingreso, Existencias, Usuario_Modifico
        ) VALUES (
            NEW.ID_Prod_POS, NEW.Fk_sucursal, NEW.Lote, NEW.Fecha_Caducidad, NEW.Fecha_Ingreso, NEW.Existencias_R, NEW.ActualizadoPor
        );
    ELSE
        -- Si el lote ya existe en la misma sucursal, actualizar existencias
        UPDATE Historial_Lotes
        SET Existencias = NEW.Existencias_R,
            Fecha_Ingreso = NEW.Fecha_Ingreso,
            Usuario_Modifico = NEW.AgregadoPor
        WHERE ID_Prod_POS = NEW.ID_Prod_POS 
          AND Lote = NEW.Lote
          AND Fk_sucursal = NEW.Fk_sucursal;
    END IF;

END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Stock_POS_Log`
--

CREATE TABLE `Stock_POS_Log` (
  `id` int(11) NOT NULL,
  `Cod_Barra` varchar(50) NOT NULL,
  `Fk_sucursal` int(11) NOT NULL,
  `Cantidad` int(11) NOT NULL,
  `TipoDeMov` varchar(50) NOT NULL,
  `Fecha` timestamp NULL DEFAULT current_timestamp(),
  `Mensaje` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Stock_POS_respaldo`
--

CREATE TABLE `Stock_POS_respaldo` (
  `Folio_Prod_Stock` int(10) UNSIGNED ZEROFILL NOT NULL,
  `ID_Prod_POS` int(12) UNSIGNED ZEROFILL NOT NULL,
  `Clave_adicional` varchar(15) DEFAULT NULL,
  `Clave_Levic` varchar(100) NOT NULL,
  `Cod_Barra` varchar(100) NOT NULL,
  `Nombre_Prod` varchar(250) NOT NULL,
  `Fk_sucursal` int(12) NOT NULL,
  `Precio_Venta` decimal(50,2) NOT NULL,
  `Precio_C` decimal(50,2) NOT NULL,
  `Max_Existencia` int(11) NOT NULL,
  `Min_Existencia` int(12) NOT NULL,
  `Existencias_R` int(11) NOT NULL,
  `Lote` varchar(100) NOT NULL,
  `Fecha_Caducidad` date NOT NULL,
  `Fecha_Ingreso` date NOT NULL,
  `Tipo_Servicio` int(10) UNSIGNED ZEROFILL NOT NULL,
  `Tipo` varchar(500) NOT NULL,
  `FkCategoria` varchar(500) NOT NULL,
  `FkMarca` varchar(500) NOT NULL,
  `FkPresentacion` varchar(500) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL,
  `Proveedor1` varchar(250) DEFAULT NULL,
  `Proveedor2` varchar(250) DEFAULT NULL,
  `Estatus` varchar(150) NOT NULL,
  `Sistema` varchar(200) NOT NULL,
  `AgregadoPor` varchar(250) NOT NULL,
  `AgregadoEl` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `ID_H_O_D` varchar(100) NOT NULL,
  `ActualizoFecha` varchar(200) NOT NULL,
  `Cod_Paquete` int(11) NOT NULL,
  `ActualizadoPor` varchar(200) NOT NULL,
  `Contable` varchar(200) NOT NULL,
  `Anaquel` varchar(100) NOT NULL,
  `Repisa` varchar(200) NOT NULL,
  `UltimoInventarioPor` varchar(200) NOT NULL,
  `FechaUltimoInventario` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Stock_POS_Respaldo2611`
--

CREATE TABLE `Stock_POS_Respaldo2611` (
  `Folio_Prod_Stock` int(10) UNSIGNED ZEROFILL NOT NULL,
  `ID_Prod_POS` int(12) UNSIGNED ZEROFILL NOT NULL,
  `Clave_adicional` varchar(15) DEFAULT NULL,
  `Clave_Levic` varchar(100) NOT NULL,
  `Cod_Barra` varchar(100) NOT NULL,
  `Nombre_Prod` varchar(250) NOT NULL,
  `Fk_sucursal` int(12) NOT NULL,
  `Precio_Venta` decimal(50,2) NOT NULL,
  `Precio_C` decimal(50,2) NOT NULL,
  `Max_Existencia` int(11) NOT NULL,
  `Min_Existencia` int(12) NOT NULL,
  `Existencias_R` int(11) NOT NULL,
  `Lote` varchar(100) NOT NULL,
  `Fecha_Caducidad` date NOT NULL,
  `Fecha_Ingreso` date NOT NULL,
  `Tipo_Servicio` int(10) UNSIGNED ZEROFILL NOT NULL,
  `Tipo` varchar(500) NOT NULL,
  `FkCategoria` varchar(500) NOT NULL,
  `FkMarca` varchar(500) NOT NULL,
  `FkPresentacion` varchar(500) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL,
  `Proveedor1` varchar(250) DEFAULT NULL,
  `Proveedor2` varchar(250) DEFAULT NULL,
  `Estatus` varchar(150) NOT NULL,
  `Sistema` varchar(200) NOT NULL,
  `AgregadoPor` varchar(250) NOT NULL,
  `AgregadoEl` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `ID_H_O_D` varchar(100) NOT NULL,
  `ActualizoFecha` varchar(200) NOT NULL,
  `Cod_Paquete` int(11) NOT NULL,
  `ActualizadoPor` varchar(200) NOT NULL,
  `Contable` varchar(200) NOT NULL,
  `Anaquel` varchar(100) NOT NULL,
  `Repisa` varchar(200) NOT NULL,
  `UltimoInventarioPor` varchar(200) NOT NULL,
  `FechaUltimoInventario` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Stock_POS_RespaldoSeptiembre`
--

CREATE TABLE `Stock_POS_RespaldoSeptiembre` (
  `Folio_Prod_Stock` int(10) UNSIGNED ZEROFILL NOT NULL,
  `ID_Prod_POS` int(12) UNSIGNED ZEROFILL NOT NULL,
  `Clave_adicional` varchar(15) DEFAULT NULL,
  `Clave_Levic` varchar(100) NOT NULL,
  `Cod_Barra` varchar(100) NOT NULL,
  `Nombre_Prod` varchar(250) NOT NULL,
  `Fk_sucursal` int(12) NOT NULL,
  `Precio_Venta` decimal(50,2) NOT NULL,
  `Precio_C` decimal(50,2) NOT NULL,
  `Max_Existencia` int(11) NOT NULL,
  `Min_Existencia` int(12) NOT NULL,
  `Existencias_R` int(11) NOT NULL,
  `Lote` varchar(100) NOT NULL,
  `Fecha_Caducidad` date NOT NULL,
  `Fecha_Ingreso` date NOT NULL,
  `Tipo_Servicio` int(10) UNSIGNED ZEROFILL NOT NULL,
  `Tipo` varchar(500) NOT NULL,
  `FkCategoria` varchar(500) NOT NULL,
  `FkMarca` varchar(500) NOT NULL,
  `FkPresentacion` varchar(500) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL,
  `Proveedor1` varchar(250) DEFAULT NULL,
  `Proveedor2` varchar(250) DEFAULT NULL,
  `Estatus` varchar(150) NOT NULL,
  `Sistema` varchar(200) NOT NULL,
  `AgregadoPor` varchar(250) NOT NULL,
  `AgregadoEl` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `ID_H_O_D` varchar(100) NOT NULL,
  `ActualizoFecha` varchar(200) NOT NULL,
  `Cod_Paquete` int(11) NOT NULL,
  `ActualizadoPor` varchar(200) NOT NULL,
  `Contable` varchar(200) NOT NULL,
  `Anaquel` varchar(100) NOT NULL,
  `Repisa` varchar(200) NOT NULL,
  `UltimoInventarioPor` varchar(200) NOT NULL,
  `FechaUltimoInventario` date NOT NULL,
  `JustificacionAjuste` varchar(300) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Stock_registrosNuevos`
--

CREATE TABLE `Stock_registrosNuevos` (
  `Folio_Ingreso` int(10) UNSIGNED ZEROFILL NOT NULL,
  `ID_Prod_POS` int(12) UNSIGNED ZEROFILL NOT NULL,
  `Fk_sucursal` int(12) NOT NULL,
  `Existencias_R` int(11) NOT NULL,
  `ExistenciaPrev` int(11) NOT NULL,
  `Recibido` int(11) NOT NULL,
  `Lote` varchar(100) NOT NULL,
  `Fecha_Caducidad` date NOT NULL,
  `AgregadoPor` varchar(250) NOT NULL,
  `AgregadoEl` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `ID_H_O_D` varchar(100) NOT NULL,
  `Factura` varchar(200) NOT NULL,
  `Precio_compra` decimal(50,2) NOT NULL,
  `Total_Factura` decimal(50,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Sucursales`
--

CREATE TABLE `Sucursales` (
  `ID_Sucursal` int(11) NOT NULL,
  `Nombre_Sucursal` varchar(200) NOT NULL,
  `Direccion` varchar(250) NOT NULL,
  `CP` varchar(150) NOT NULL,
  `RFC` varchar(150) NOT NULL,
  `Licencia` varchar(200) NOT NULL,
  `Identificador` varchar(10) NOT NULL,
  `Telefono` varchar(12) DEFAULT NULL,
  `Correo` varchar(100) NOT NULL,
  `Contra_correo` varchar(250) NOT NULL,
  `Pin_Equipo` varchar(20) NOT NULL,
  `Sucursal_Activa` varchar(100) NOT NULL,
  `Agrego` varchar(250) NOT NULL,
  `AgregadoEl` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `NombreImpresora` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Suscripciones_Push`
--

CREATE TABLE `Suscripciones_Push` (
  `ID_Suscripcion` int(11) NOT NULL,
  `UsuarioID` int(11) NOT NULL,
  `SucursalID` int(11) NOT NULL,
  `Dispositivo` varchar(255) DEFAULT NULL,
  `Datos_Suscripcion` text NOT NULL,
  `Fecha_Creacion` datetime DEFAULT current_timestamp(),
  `Ultima_Actualizacion` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `Activo` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tareas`
--

CREATE TABLE `tareas` (
  `id` int(11) NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `prioridad` enum('Alta','Media','Baja') DEFAULT 'Media',
  `fecha_limite` date DEFAULT NULL,
  `estado` enum('Por hacer','En progreso','Completada','Cancelada') DEFAULT 'Por hacer',
  `asignado_a` int(11) NOT NULL,
  `creado_por` int(11) NOT NULL,
  `fecha_creacion` timestamp NULL DEFAULT current_timestamp(),
  `fecha_actualizacion` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Tareas`
--

CREATE TABLE `Tareas` (
  `id` int(11) NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `prioridad` enum('Alta','Media','Baja') NOT NULL DEFAULT 'Media',
  `fecha_limite` date DEFAULT NULL,
  `estado` enum('Por hacer','En progreso','Completada','Cancelada') NOT NULL DEFAULT 'Por hacer',
  `asignado_a` int(11) NOT NULL,
  `creado_por` int(11) NOT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_actualizacion` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `TareasPorHacer`
--

CREATE TABLE `TareasPorHacer` (
  `ID_Tarea` int(11) NOT NULL,
  `NombreTarea` varchar(200) NOT NULL,
  `Descripcion` varchar(200) NOT NULL,
  `Registrado` varchar(200) NOT NULL,
  `Sistema` varchar(150) NOT NULL,
  `Sucursal` int(11) NOT NULL,
  `Estado` int(11) NOT NULL,
  `Licencia` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `templates_downloads`
--

CREATE TABLE `templates_downloads` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `template_downloaded` tinyint(1) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `TiposDeGastos`
--

CREATE TABLE `TiposDeGastos` (
  `Gasto_ID` int(11) UNSIGNED ZEROFILL NOT NULL,
  `Nom_Gasto` varchar(200) NOT NULL,
  `Estado` varchar(100) NOT NULL,
  `Agregado_Por` varchar(250) NOT NULL,
  `ActualizadoPor` varchar(200) NOT NULL,
  `Agregadoel` timestamp NOT NULL DEFAULT current_timestamp(),
  `Sistema` varchar(250) NOT NULL,
  `Licencia` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Tipos_Devolucion`
--

CREATE TABLE `Tipos_Devolucion` (
  `id` int(11) NOT NULL,
  `codigo` varchar(50) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `color` varchar(20) DEFAULT '#6c757d',
  `requiere_autorizacion` tinyint(1) DEFAULT 0,
  `activo` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Tipos_estudios`
--

CREATE TABLE `Tipos_estudios` (
  `ID_tipo_analisis` int(11) NOT NULL,
  `Nombre_estudio` varchar(250) NOT NULL,
  `Fk_Tipo_analisis` int(11) NOT NULL,
  `ID_H_O_D` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Tipos_Usuarios`
--

CREATE TABLE `Tipos_Usuarios` (
  `ID_User` int(11) NOT NULL,
  `TipoUsuario` varchar(200) NOT NULL,
  `Licencia` varchar(200) NOT NULL,
  `Creadoel` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `Creado` varchar(200) NOT NULL,
  `ActualizadoPor` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `TipProd_POS`
--

CREATE TABLE `TipProd_POS` (
  `Tip_Prod_ID` int(11) UNSIGNED ZEROFILL NOT NULL,
  `Nom_Tipo_Prod` varchar(200) NOT NULL,
  `Estado` varchar(100) NOT NULL,
  `Agregado_Por` varchar(250) NOT NULL,
  `ActualizadoPor` varchar(200) NOT NULL,
  `Agregadoel` timestamp NOT NULL DEFAULT current_timestamp(),
  `Sistema` varchar(250) NOT NULL,
  `Licencia` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `TipProd_POS_Audita`
--

CREATE TABLE `TipProd_POS_Audita` (
  `ID_Audita_TipoProd` int(11) NOT NULL,
  `Tip_Prod_ID` int(11) UNSIGNED ZEROFILL NOT NULL,
  `Nom_Tipo_Prod` varchar(200) NOT NULL,
  `Estado` varchar(100) NOT NULL,
  `Cod_Estado` varchar(200) NOT NULL,
  `Agregado_Por` varchar(250) NOT NULL,
  `Agregadoel` timestamp NOT NULL DEFAULT current_timestamp(),
  `Sistema` varchar(250) NOT NULL,
  `Licencia` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `TraspasosYNotasC`
--

CREATE TABLE `TraspasosYNotasC` (
  `TraspaNotID` int(10) UNSIGNED ZEROFILL NOT NULL,
  `Folio_Ticket` varchar(100) NOT NULL,
  `Cod_Barra` varchar(100) NOT NULL,
  `Nombre_Prod` varchar(250) NOT NULL,
  `Cantidad` int(11) NOT NULL,
  `Fk_sucursal` int(12) NOT NULL,
  `Fk_SucursalDestino` int(11) NOT NULL,
  `Total_VentaG` decimal(50,2) NOT NULL,
  `Pc` decimal(50,2) NOT NULL,
  `TipoDeMov` varchar(200) NOT NULL,
  `Fecha_venta` date NOT NULL,
  `Estatus` varchar(200) NOT NULL,
  `Sistema` varchar(200) NOT NULL,
  `AgregadoPor` varchar(250) NOT NULL,
  `AgregadoEl` timestamp NOT NULL DEFAULT current_timestamp(),
  `ID_H_O_D` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Disparadores `TraspasosYNotasC`
--
DELIMITER $$
CREATE TRIGGER `after_insert_traspasosynotasc` AFTER INSERT ON `TraspasosYNotasC` FOR EACH ROW BEGIN
    DECLARE v_existencias INT;

    -- Verificar si existe el registro en Stock_POS
    SELECT Existencias_R INTO v_existencias
    FROM Stock_POS
    WHERE Cod_Barra = NEW.Cod_Barra AND Fk_sucursal = NEW.Fk_sucursal;

    IF v_existencias IS NOT NULL THEN
        -- Actualizar el stock restando la cantidad y colocando la justificación
        UPDATE Stock_POS
        SET Existencias_R = Existencias_R - NEW.Cantidad,
            JustificacionAjuste = NEW.TipoDeMov
        WHERE Cod_Barra = NEW.Cod_Barra AND Fk_sucursal = NEW.Fk_sucursal;
    ELSE
        -- Registrar el intento fallido en una tabla de logs
        INSERT INTO Stock_POS_Log (Cod_Barra, Fk_sucursal, Cantidad, TipoDeMov, Fecha, Mensaje)
        VALUES (NEW.Cod_Barra, NEW.Fk_sucursal, NEW.Cantidad, NEW.TipoDeMov, NOW(), 'Intento fallido de actualizar stock: registro no encontrado');
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `suma_traspaso_a_la_sucursal` AFTER INSERT ON `TraspasosYNotasC` FOR EACH ROW BEGIN
    -- Verificar si existe el registro en Stock_POS y actualizar en el mismo paso
    IF EXISTS (
        SELECT 1 FROM Stock_POS 
        WHERE Cod_Barra = NEW.Cod_Barra AND Fk_sucursal = NEW.Fk_SucursalDestino
    ) THEN
        UPDATE Stock_POS
        SET Existencias_R = Existencias_R + NEW.Cantidad,
            JustificacionAjuste = NEW.TipoDeMov
        WHERE Cod_Barra = NEW.Cod_Barra AND Fk_sucursal = NEW.Fk_SucursalDestino;
    ELSE
        -- Registrar el intento fallido en una tabla de logs
        INSERT INTO Stock_POS_Log (Cod_Barra, Fk_sucursal, Cantidad, TipoDeMov, Fecha, Mensaje)
        VALUES (NEW.Cod_Barra, NEW.Fk_SucursalDestino, NEW.Cantidad, NEW.TipoDeMov, NOW(), 'Intento fallido de actualizar stock: registro no encontrado');
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Traspasos_generados`
--

CREATE TABLE `Traspasos_generados` (
  `ID_Traspaso_Generado` int(11) NOT NULL,
  `Num_Orden` int(11) UNSIGNED ZEROFILL NOT NULL,
  `Num_Factura` varchar(200) NOT NULL,
  `Cod_Barra` varchar(100) NOT NULL,
  `Nombre_Prod` varchar(250) NOT NULL,
  `Fk_SucDestino` int(11) NOT NULL,
  `Precio_Venta` decimal(50,2) NOT NULL,
  `Precio_Compra` decimal(50,2) NOT NULL,
  `Cantidad_Enviada` int(11) NOT NULL,
  `FechaEntrega` date NOT NULL,
  `TraspasoGeneradoPor` varchar(300) NOT NULL,
  `TraspasoRecibidoPor` varchar(250) DEFAULT NULL,
  `Estatus` varchar(150) NOT NULL,
  `AgregadoPor` varchar(250) NOT NULL,
  `AgregadoEl` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `ID_H_O_D` varchar(100) NOT NULL,
  `TotaldePiezas` int(11) NOT NULL,
  `Fecha_recepcion` varchar(1000) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Disparadores `Traspasos_generados`
--
DELIMITER $$
CREATE TRIGGER `trg_after_update_traspasos_generados` AFTER UPDATE ON `Traspasos_generados` FOR EACH ROW BEGIN
    IF NEW.Estatus = 'Entregado' THEN
        INSERT INTO Traspasos_Recepcionados
        (
            `ID_Traspaso_Generado`, 
            `Num_Orden`, 
            `Num_Factura`, 
            `Cod_Barra`, 
            `Nombre_Prod`, 
            `Fk_SucDestino`, 
            `Precio_Venta`, 
            `Precio_Compra`, 
            `Cantidad_Enviada`, 
            `FechaEntrega`, 
            `TraspasoGeneradoPor`, 
            `TraspasoRecibidoPor`, 
            `Estatus`, 
            `AgregadoPor`, 
            `AgregadoEl`, 
            `ID_H_O_D`, 
            `TotaldePiezas`, 
            `Fecha_recepcion`
        )
        VALUES
        (
            NEW.`ID_Traspaso_Generado`, 
            NEW.`Num_Orden`, 
            NEW.`Num_Factura`, 
            NEW.`Cod_Barra`, 
            NEW.`Nombre_Prod`, 
            NEW.`Fk_SucDestino`, 
            NEW.`Precio_Venta`, 
            NEW.`Precio_Compra`, 
            NEW.`Cantidad_Enviada`, 
            NEW.`FechaEntrega`, 
            NEW.`TraspasoGeneradoPor`, 
            NEW.`TraspasoRecibidoPor`, 
            NEW.`Estatus`, 
            NEW.`AgregadoPor`, 
            NEW.`AgregadoEl`, 
            NEW.`ID_H_O_D`, 
            NEW.`TotaldePiezas`, 
            NEW.`Fecha_recepcion`
        );
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Traspasos_generados_audita`
--

CREATE TABLE `Traspasos_generados_audita` (
  `id_audita_traspaso` int(11) NOT NULL,
  `ID_Traspaso_Generado` int(11) NOT NULL,
  `Folio_Prod_Stock` int(10) UNSIGNED ZEROFILL NOT NULL,
  `ID_Prod_POS` int(12) UNSIGNED ZEROFILL NOT NULL,
  `Cod_Barra` varchar(100) NOT NULL,
  `Nombre_Prod` varchar(250) NOT NULL,
  `Fk_sucursal` int(12) NOT NULL,
  `Fk_Sucursal_Destino` int(11) NOT NULL,
  `Precio_Venta` decimal(50,2) NOT NULL,
  `Precio_Compra` decimal(50,2) NOT NULL,
  `Total_traspaso` decimal(50,2) NOT NULL,
  `TotalVenta` double(50,2) NOT NULL,
  `Existencias_R` int(11) NOT NULL,
  `Cantidad_Enviada` int(11) NOT NULL,
  `Existencias_D_envio` int(11) NOT NULL,
  `FechaEntrega` date NOT NULL,
  `TraspasoGeneradoPor` varchar(300) NOT NULL,
  `TraspasoRecibidoPor` varchar(250) NOT NULL,
  `Tipo_Servicio` int(10) UNSIGNED ZEROFILL NOT NULL,
  `Proveedor1` varchar(250) DEFAULT NULL,
  `Proveedor2` varchar(250) DEFAULT NULL,
  `Estatus` varchar(150) NOT NULL,
  `AgregadoPor` varchar(250) NOT NULL,
  `AgregadoEl` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `ID_H_O_D` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Traspasos_generados_Eliminados`
--

CREATE TABLE `Traspasos_generados_Eliminados` (
  `ID_eliminado` int(11) NOT NULL,
  `ID_Traspaso_Generado` int(11) NOT NULL,
  `Folio_Prod_Stock` int(10) UNSIGNED ZEROFILL NOT NULL,
  `ID_Prod_POS` int(12) UNSIGNED ZEROFILL NOT NULL,
  `Cod_Barra` varchar(100) NOT NULL,
  `Nombre_Prod` varchar(250) NOT NULL,
  `Fk_sucursal` int(12) NOT NULL,
  `Fk_Sucursal_Destino` varchar(100) NOT NULL,
  `Fk_SucDestino` int(11) NOT NULL,
  `Precio_Venta` decimal(50,2) NOT NULL,
  `Precio_Compra` decimal(50,2) NOT NULL,
  `Total_traspaso` decimal(50,2) NOT NULL,
  `TotalVenta` double(50,2) NOT NULL,
  `Existencias_R` int(11) NOT NULL,
  `Cantidad_Enviada` int(11) NOT NULL,
  `Existencias_D_envio` int(11) NOT NULL,
  `FechaEntrega` date NOT NULL,
  `TraspasoGeneradoPor` varchar(300) NOT NULL,
  `TraspasoRecibidoPor` varchar(250) NOT NULL,
  `Tipo_Servicio` int(10) UNSIGNED ZEROFILL NOT NULL,
  `Proveedor1` varchar(250) DEFAULT NULL,
  `Proveedor2` varchar(250) DEFAULT NULL,
  `Estatus` varchar(150) NOT NULL,
  `AgregadoPor` varchar(250) NOT NULL,
  `AgregadoEl` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `ID_H_O_D` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Traspasos_generados_Entre_sucursales`
--

CREATE TABLE `Traspasos_generados_Entre_sucursales` (
  `ID_Traspaso_Generado` int(11) NOT NULL,
  `Num_Orden` int(11) UNSIGNED ZEROFILL NOT NULL,
  `Num_Factura` varchar(200) NOT NULL,
  `Cod_Barra` varchar(100) NOT NULL,
  `Nombre_Prod` varchar(250) NOT NULL,
  `Fk_SucDestino` int(11) NOT NULL,
  `SucursalOrigen` varchar(250) NOT NULL,
  `Precio_Venta` decimal(50,2) NOT NULL,
  `Precio_Compra` decimal(50,2) NOT NULL,
  `Cantidad_Enviada` int(11) NOT NULL,
  `FechaEntrega` date NOT NULL,
  `TraspasoGeneradoPor` varchar(300) NOT NULL,
  `TraspasoRecibidoPor` varchar(250) DEFAULT NULL,
  `Estatus` varchar(150) NOT NULL,
  `AgregadoPor` varchar(250) NOT NULL,
  `AgregadoEl` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `ID_H_O_D` varchar(100) NOT NULL,
  `TotaldePiezas` int(11) NOT NULL,
  `Fecha_recepcion` varchar(1000) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Traspasos_Recepcionados`
--

CREATE TABLE `Traspasos_Recepcionados` (
  `Id_recepcion` int(11) NOT NULL,
  `ID_Traspaso_Generado` int(11) NOT NULL,
  `Num_Orden` int(11) UNSIGNED ZEROFILL NOT NULL,
  `Num_Factura` varchar(200) NOT NULL,
  `Cod_Barra` varchar(100) NOT NULL,
  `Nombre_Prod` varchar(250) NOT NULL,
  `Fk_SucDestino` int(11) NOT NULL,
  `Precio_Venta` decimal(50,2) NOT NULL,
  `Precio_Compra` decimal(50,2) NOT NULL,
  `Cantidad_Enviada` int(11) NOT NULL,
  `FechaEntrega` date NOT NULL,
  `TraspasoGeneradoPor` varchar(300) NOT NULL,
  `TraspasoRecibidoPor` varchar(250) DEFAULT NULL,
  `Estatus` varchar(150) NOT NULL,
  `AgregadoPor` varchar(250) NOT NULL,
  `AgregadoEl` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `ID_H_O_D` varchar(100) NOT NULL,
  `TotaldePiezas` int(11) NOT NULL,
  `Fecha_recepcion` varchar(1000) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Disparadores `Traspasos_Recepcionados`
--
DELIMITER $$
CREATE TRIGGER `after_insert_traspasos_recepcionados` AFTER INSERT ON `Traspasos_Recepcionados` FOR EACH ROW BEGIN
    -- Actualizar la columna Existencias_R en Stock_POS
    UPDATE Stock_POS
    SET Existencias_R = Existencias_R + NEW.Cantidad_Enviada
    WHERE Stock_POS.Cod_Barra = NEW.Cod_Barra
    AND Stock_POS.Fk_sucursal = NEW.Fk_SucDestino;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ubicaciones_trabajo`
--

CREATE TABLE `ubicaciones_trabajo` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `latitud` decimal(10,8) NOT NULL,
  `longitud` decimal(11,8) NOT NULL,
  `radio` int(11) NOT NULL DEFAULT 100 COMMENT 'Radio en metros',
  `direccion` text DEFAULT NULL,
  `estado` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Usuarios_PV`
--

CREATE TABLE `Usuarios_PV` (
  `Id_PvUser` int(11) NOT NULL,
  `Nombre_Apellidos` varchar(250) DEFAULT NULL,
  `Password` varchar(10) DEFAULT NULL,
  `file_name` varchar(300) DEFAULT NULL,
  `Fk_Usuario` int(12) DEFAULT NULL,
  `Fk_Sucursal` int(11) NOT NULL,
  `Fecha_Nacimiento` date DEFAULT NULL,
  `Correo_Electronico` varchar(100) DEFAULT NULL,
  `Telefono` varchar(12) DEFAULT NULL,
  `AgregadoPor` varchar(200) DEFAULT NULL,
  `AgregadoEl` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `Estatus` varchar(150) DEFAULT NULL,
  `Licencia` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Ventas_POS`
--

CREATE TABLE `Ventas_POS` (
  `Venta_POS_ID` int(10) UNSIGNED ZEROFILL NOT NULL,
  `ID_Prod_POS` int(12) UNSIGNED ZEROFILL NOT NULL,
  `Identificador_tipo` int(12) UNSIGNED ZEROFILL NOT NULL,
  `Turno` varchar(250) NOT NULL,
  `FolioSucursal` varchar(100) NOT NULL,
  `Folio_Ticket` varchar(100) NOT NULL,
  `Folio_Ticket_Aleatorio` varchar(200) NOT NULL,
  `Clave_adicional` varchar(15) DEFAULT NULL,
  `Cod_Barra` varchar(100) NOT NULL,
  `Nombre_Prod` varchar(250) NOT NULL,
  `Cantidad_Venta` int(11) NOT NULL,
  `Fk_sucursal` int(12) NOT NULL,
  `Total_Venta` decimal(50,2) NOT NULL,
  `Importe` decimal(50,2) NOT NULL,
  `Total_VentaG` decimal(50,2) NOT NULL,
  `DescuentoAplicado` int(11) DEFAULT NULL,
  `FormaDePago` varchar(200) NOT NULL,
  `CantidadPago` decimal(50,2) NOT NULL,
  `Cambio` decimal(50,2) NOT NULL,
  `Cliente` varchar(200) NOT NULL,
  `Fecha_venta` date NOT NULL,
  `Fk_Caja` int(10) UNSIGNED ZEROFILL NOT NULL,
  `Lote` varchar(100) NOT NULL,
  `Motivo_Cancelacion` varchar(250) NOT NULL,
  `Estatus` varchar(200) NOT NULL,
  `Sistema` varchar(200) NOT NULL,
  `AgregadoPor` varchar(250) NOT NULL,
  `AgregadoEl` timestamp NOT NULL DEFAULT current_timestamp(),
  `ID_H_O_D` varchar(100) NOT NULL,
  `FolioSignoVital` varchar(200) NOT NULL,
  `TicketAnterior` varchar(100) NOT NULL,
  `Pagos_tarjeta` decimal(50,2) NOT NULL,
  `Tipo` varchar(300) NOT NULL,
  `FolioRifa` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Disparadores `Ventas_POS`
--
DELIMITER $$
CREATE TRIGGER `RestarExistenciasDespuesInsert` AFTER INSERT ON `Ventas_POS` FOR EACH ROW BEGIN
    DECLARE v_existencias INT;
    DECLARE v_error VARCHAR(255);

    -- Buscar las existencias del producto en Stock_POS
    SELECT Existencias_R INTO v_existencias
    FROM Stock_POS
    WHERE ID_Prod_POS = NEW.ID_Prod_POS
      AND (Cod_Barra = NEW.Cod_Barra OR NEW.Cod_Barra IS NULL)
      AND Fk_sucursal = NEW.Fk_sucursal
    LIMIT 1;

    -- Verificar si el producto existe en el inventario
    IF v_existencias IS NULL THEN
        SET v_error = 'Producto no encontrado en inventario.';
        INSERT INTO Errores_POS_Ventas (ID_Prod_POS, Cod_Barra, Fk_sucursal, Cantidad_Venta, Mensaje_Error)
        VALUES (NEW.ID_Prod_POS, NEW.Cod_Barra, NEW.Fk_sucursal, NEW.Cantidad_Venta, v_error);
    ELSEIF v_existencias < NEW.Cantidad_Venta THEN
        -- Verificar si hay suficiente stock
        SET v_error = 'No hay suficientes existencias.';
        INSERT INTO Errores_POS_Ventas (ID_Prod_POS, Cod_Barra, Fk_sucursal, Cantidad_Venta, Mensaje_Error)
        VALUES (NEW.ID_Prod_POS, NEW.Cod_Barra, NEW.Fk_sucursal, NEW.Cantidad_Venta, v_error);
    ELSE
        -- Restar la cantidad vendida del inventario
        UPDATE Stock_POS
        SET Existencias_R = Existencias_R - NEW.Cantidad_Venta
        WHERE ID_Prod_POS = NEW.ID_Prod_POS
          AND (Cod_Barra = NEW.Cod_Barra OR NEW.Cod_Barra IS NULL)
          AND Fk_sucursal = NEW.Fk_sucursal;
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `actualizar_valor_caja` AFTER INSERT ON `Ventas_POS` FOR EACH ROW BEGIN
    DECLARE caja_id INT;
    DECLARE importe_nuevo DECIMAL(10, 2);
    SET caja_id = NEW.Fk_Caja;
    
    -- Obtener el importe total actual de la caja
    SELECT Valor_Total_Caja INTO importe_nuevo
    FROM Cajas
    WHERE ID_Caja = caja_id;
    
    -- Sumar el importe de la fila insertada al importe total de la caja
    SET importe_nuevo = importe_nuevo + NEW.Importe;
    
    -- Actualizar el valor total de la caja
    UPDATE Cajas
    SET Valor_Total_Caja = importe_nuevo
    WHERE ID_Caja = caja_id;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Ventas_POSV2`
--

CREATE TABLE `Ventas_POSV2` (
  `Venta_POS_ID` int(10) UNSIGNED ZEROFILL NOT NULL,
  `ID_Prod_POS` int(12) UNSIGNED ZEROFILL NOT NULL,
  `Identificador_tipo` varchar(300) NOT NULL,
  `Turno` varchar(250) NOT NULL,
  `FolioSucursal` varchar(100) NOT NULL,
  `Folio_Ticket` varchar(100) NOT NULL,
  `Folio_Ticket_Aleatorio` varchar(200) NOT NULL,
  `Clave_adicional` varchar(15) DEFAULT NULL,
  `Cod_Barra` varchar(100) NOT NULL,
  `Nombre_Prod` varchar(250) NOT NULL,
  `Cantidad_Venta` int(11) NOT NULL,
  `Fk_sucursal` int(12) NOT NULL,
  `Total_Venta` decimal(50,2) NOT NULL,
  `Importe` decimal(50,2) NOT NULL,
  `Total_VentaG` decimal(50,2) NOT NULL,
  `DescuentoAplicado` int(11) DEFAULT NULL,
  `FormaDePago` varchar(200) NOT NULL,
  `CantidadPago` decimal(50,2) NOT NULL,
  `Cambio` decimal(50,2) NOT NULL,
  `Cliente` varchar(200) NOT NULL,
  `Fecha_venta` date NOT NULL,
  `Fk_Caja` int(10) UNSIGNED ZEROFILL NOT NULL,
  `Lote` varchar(100) NOT NULL,
  `Motivo_Cancelacion` varchar(250) NOT NULL,
  `Estatus` varchar(200) NOT NULL,
  `Sistema` varchar(200) NOT NULL,
  `AgregadoPor` varchar(250) NOT NULL,
  `AgregadoEl` timestamp NOT NULL DEFAULT current_timestamp(),
  `ID_H_O_D` varchar(100) NOT NULL,
  `FolioSignoVital` varchar(200) NOT NULL,
  `TicketAnterior` varchar(100) NOT NULL,
  `Pagos_tarjeta` decimal(50,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Ventas_POS_Audita`
--

CREATE TABLE `Ventas_POS_Audita` (
  `ID_Audita` int(11) NOT NULL,
  `Venta_POS_ID` int(10) UNSIGNED ZEROFILL NOT NULL,
  `ID_Prod_POS` int(12) UNSIGNED ZEROFILL NOT NULL,
  `Identificador_tipo` varchar(300) NOT NULL,
  `Folio_Ticket` varchar(200) NOT NULL,
  `Clave_adicional` varchar(15) DEFAULT NULL,
  `Cod_Barra` varchar(100) NOT NULL,
  `Nombre_Prod` varchar(250) NOT NULL,
  `Cantidad_Venta` int(11) NOT NULL,
  `Fk_sucursal` int(12) NOT NULL,
  `Total_Venta` decimal(50,2) NOT NULL,
  `Importe` decimal(50,2) NOT NULL,
  `Total_VentaG` decimal(50,2) NOT NULL,
  `CantidadPago` decimal(50,2) NOT NULL,
  `Cambio` decimal(50,2) NOT NULL,
  `Fecha_venta` date NOT NULL,
  `Fk_Caja` int(10) UNSIGNED ZEROFILL NOT NULL,
  `Lote` varchar(100) NOT NULL,
  `Motivo_Cancelacion` varchar(250) NOT NULL,
  `Estatus` varchar(200) NOT NULL,
  `Sistema` varchar(200) NOT NULL,
  `AgregadoPor` varchar(250) NOT NULL,
  `AgregadoEl` timestamp NOT NULL DEFAULT current_timestamp(),
  `ID_H_O_D` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Ventas_POS_Cancelaciones`
--

CREATE TABLE `Ventas_POS_Cancelaciones` (
  `Cancelacion_IDVenPOS` int(11) NOT NULL,
  `Venta_POS_ID` int(10) UNSIGNED ZEROFILL NOT NULL,
  `ID_Prod_POS` int(12) UNSIGNED ZEROFILL NOT NULL,
  `Identificador_tipo` varchar(300) NOT NULL,
  `Folio_Ticket` varchar(200) NOT NULL,
  `Clave_adicional` varchar(15) DEFAULT NULL,
  `Cod_Barra` varchar(100) NOT NULL,
  `Nombre_Prod` varchar(250) NOT NULL,
  `Cantidad_Venta` int(11) NOT NULL,
  `Fk_sucursal` int(12) NOT NULL,
  `Total_Venta` decimal(50,2) NOT NULL,
  `Fk_Caja` int(10) UNSIGNED ZEROFILL NOT NULL,
  `Lote` varchar(100) NOT NULL,
  `Motivo_Cancelacion` varchar(250) NOT NULL,
  `Estatus` varchar(200) NOT NULL,
  `Sistema` varchar(200) NOT NULL,
  `AgregadoPor` varchar(250) NOT NULL,
  `AgregadoEl` timestamp NOT NULL DEFAULT current_timestamp(),
  `ID_H_O_D` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Disparadores `Ventas_POS_Cancelaciones`
--
DELIMITER $$
CREATE TRIGGER `Disminuye_Caja_PorCancelacion` AFTER INSERT ON `Ventas_POS_Cancelaciones` FOR EACH ROW Update Cajas_POS
set Cajas_POS.Valor_Total_Caja = Cajas_POS.Valor_Total_Caja - NEW.Total_Venta
where Cajas_POS.ID_Caja = NEW.Fk_Caja
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Ventas_POS_Pruebas`
--

CREATE TABLE `Ventas_POS_Pruebas` (
  `Venta_POS_ID` int(10) UNSIGNED ZEROFILL NOT NULL,
  `ID_Prod_POS` int(12) UNSIGNED ZEROFILL NOT NULL,
  `Identificador_tipo` varchar(300) NOT NULL,
  `Turno` varchar(250) NOT NULL,
  `FolioSucursal` varchar(100) NOT NULL,
  `Folio_Ticket` int(10) UNSIGNED ZEROFILL NOT NULL,
  `Folio_Ticket_Aleatorio` varchar(200) NOT NULL,
  `Clave_adicional` varchar(15) DEFAULT NULL,
  `Cod_Barra` varchar(100) NOT NULL,
  `Nombre_Prod` varchar(250) NOT NULL,
  `Cantidad_Venta` int(11) NOT NULL,
  `Fk_sucursal` int(12) NOT NULL,
  `Total_Venta` decimal(50,2) NOT NULL,
  `Importe` decimal(50,2) NOT NULL,
  `Total_VentaG` decimal(50,2) NOT NULL,
  `DescuentoAplicado` int(11) DEFAULT NULL,
  `FormaDePago` varchar(200) NOT NULL,
  `CantidadPago` decimal(50,2) NOT NULL,
  `Cambio` decimal(50,2) NOT NULL,
  `Cliente` varchar(200) NOT NULL,
  `Fecha_venta` date NOT NULL,
  `Fk_Caja` int(10) UNSIGNED ZEROFILL NOT NULL,
  `Lote` varchar(100) NOT NULL,
  `Motivo_Cancelacion` varchar(250) NOT NULL,
  `Estatus` varchar(200) NOT NULL,
  `Sistema` varchar(200) NOT NULL,
  `AgregadoPor` varchar(250) NOT NULL,
  `AgregadoEl` timestamp NOT NULL DEFAULT current_timestamp(),
  `ID_H_O_D` varchar(100) NOT NULL,
  `FolioSignoVital` varchar(200) NOT NULL,
  `TicketAnterior` varchar(100) NOT NULL,
  `Pagos_tarjeta` decimal(50,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Ventas_POS_respaldo`
--

CREATE TABLE `Ventas_POS_respaldo` (
  `Venta_POS_ID` int(10) UNSIGNED ZEROFILL NOT NULL,
  `ID_Prod_POS` int(12) UNSIGNED ZEROFILL NOT NULL,
  `Identificador_tipo` varchar(300) NOT NULL,
  `Turno` varchar(250) NOT NULL,
  `FolioSucursal` varchar(100) NOT NULL,
  `Folio_Ticket` varchar(100) NOT NULL,
  `Folio_Ticket_Aleatorio` varchar(200) NOT NULL,
  `Clave_adicional` varchar(15) DEFAULT NULL,
  `Cod_Barra` varchar(100) NOT NULL,
  `Nombre_Prod` varchar(250) NOT NULL,
  `Cantidad_Venta` int(11) NOT NULL,
  `Fk_sucursal` int(12) NOT NULL,
  `Total_Venta` decimal(50,2) NOT NULL,
  `Importe` decimal(50,2) NOT NULL,
  `Total_VentaG` decimal(50,2) NOT NULL,
  `DescuentoAplicado` int(11) DEFAULT NULL,
  `FormaDePago` varchar(200) NOT NULL,
  `CantidadPago` decimal(50,2) NOT NULL,
  `Cambio` decimal(50,2) NOT NULL,
  `Cliente` varchar(200) NOT NULL,
  `Fecha_venta` date NOT NULL,
  `Fk_Caja` int(10) UNSIGNED ZEROFILL NOT NULL,
  `Lote` varchar(100) NOT NULL,
  `Motivo_Cancelacion` varchar(250) NOT NULL,
  `Estatus` varchar(200) NOT NULL,
  `Sistema` varchar(200) NOT NULL,
  `AgregadoPor` varchar(250) NOT NULL,
  `AgregadoEl` timestamp NOT NULL DEFAULT current_timestamp(),
  `ID_H_O_D` varchar(100) NOT NULL,
  `FolioSignoVital` varchar(200) NOT NULL,
  `TicketAnterior` varchar(100) NOT NULL,
  `Pagos_tarjeta` decimal(50,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `v_chat_conversaciones_info`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `v_chat_conversaciones_info` (
`id_conversacion` int(11)
,`nombre_conversacion` varchar(255)
,`descripcion` text
,`tipo_conversacion` enum('individual','grupo','sucursal','general','canal')
,`sucursal_id` int(11)
,`Nombre_Sucursal` varchar(200)
,`creado_por` int(11)
,`creado_por_nombre` varchar(250)
,`creado_por_avatar` varchar(300)
,`fecha_creacion` timestamp
,`fecha_actualizacion` timestamp
,`ultimo_mensaje` text
,`ultimo_mensaje_fecha` timestamp
,`ultimo_mensaje_usuario_id` int(11)
,`ultimo_mensaje_usuario_nombre` varchar(250)
,`activo` tinyint(1)
,`privado` tinyint(1)
,`archivado` tinyint(1)
,`total_participantes` bigint(21)
,`participantes_activos` bigint(21)
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `v_chat_mensajes_info`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `v_chat_mensajes_info` (
`id_mensaje` int(11)
,`conversacion_id` int(11)
,`usuario_id` int(11)
,`usuario_nombre` varchar(250)
,`usuario_avatar` varchar(300)
,`usuario_tipo` varchar(200)
,`mensaje` text
,`tipo_mensaje` enum('texto','imagen','video','audio','archivo','sistema','sticker','encuesta')
,`archivo_url` varchar(500)
,`archivo_nombre` varchar(255)
,`archivo_tipo` varchar(100)
,`archivo_tamaño` bigint(20)
,`archivo_hash` varchar(64)
,`fecha_envio` timestamp
,`fecha_edicion` timestamp
,`fecha_eliminacion` timestamp
,`editado` tinyint(1)
,`eliminado` tinyint(1)
,`eliminado_por` int(11)
,`eliminado_por_nombre` varchar(250)
,`mensaje_respuesta_id` int(11)
,`mensaje_original_id` int(11)
,`metadatos` longtext
,`prioridad` enum('baja','normal','alta','urgente')
,`destinatarios_especificos` longtext
,`total_reacciones` bigint(21)
,`total_lecturas` bigint(21)
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `v_chat_participantes_info`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `v_chat_participantes_info` (
`id_participante` int(11)
,`conversacion_id` int(11)
,`usuario_id` int(11)
,`usuario_nombre` varchar(250)
,`usuario_avatar` varchar(300)
,`usuario_tipo` varchar(200)
,`Nombre_Sucursal` varchar(200)
,`rol` enum('admin','moderador','miembro')
,`fecha_union` timestamp
,`fecha_salida` timestamp
,`ultima_lectura` timestamp
,`notificaciones` tinyint(1)
,`silenciado` tinyint(1)
,`activo` tinyint(1)
,`configuracion_participante` longtext
,`estado_usuario` enum('online','offline','ausente','ocupado','invisible')
,`ultima_actividad` timestamp
,`mensajes_no_leidos` bigint(21)
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `v_devoluciones_completas`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `v_devoluciones_completas` (
`id` int(11)
,`folio` varchar(50)
,`fecha` timestamp
,`estatus` enum('pendiente','procesada','cancelada')
,`observaciones_generales` text
,`total_productos` int(11)
,`total_unidades` int(11)
,`valor_total` decimal(15,2)
,`sucursal_nombre` varchar(200)
,`usuario_nombre` varchar(250)
,`usuario_tipo` varchar(200)
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `v_devoluciones_detalle_completo`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `v_devoluciones_detalle_completo` (
`id` int(11)
,`devolucion_id` int(11)
,`folio` varchar(50)
,`producto_id` int(12) unsigned zerofill
,`codigo_barras` varchar(100)
,`nombre_producto` varchar(250)
,`cantidad` int(11)
,`tipo_devolucion` varchar(50)
,`tipo_nombre` varchar(100)
,`tipo_color` varchar(20)
,`observaciones` text
,`lote` varchar(100)
,`fecha_caducidad` date
,`precio_venta` decimal(15,2)
,`precio_costo` decimal(15,2)
,`valor_total` decimal(15,2)
,`accion_tomada` enum('ajuste_inventario','traspaso','destruccion','reembolso','otro')
,`observaciones_accion` text
,`created_at` timestamp
,`fecha_devolucion` timestamp
,`estatus_devolucion` enum('pendiente','procesada','cancelada')
,`sucursal_nombre` varchar(200)
,`usuario_nombre` varchar(250)
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `v_estadisticas_asistencia`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `v_estadisticas_asistencia` (
`usuario_id` int(11)
,`Nombre_Apellidos` varchar(250)
,`fecha` date
,`entradas` bigint(21)
,`salidas` bigint(21)
,`total_registros` bigint(21)
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `v_estadisticas_devoluciones`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `v_estadisticas_devoluciones` (
`fecha` date
,`sucursal_id` int(11)
,`Nombre_Sucursal` varchar(200)
,`total_devoluciones` bigint(21)
,`total_unidades_devueltas` decimal(32,0)
,`valor_total_devuelto` decimal(37,2)
,`pendientes` bigint(21)
,`procesadas` bigint(21)
,`canceladas` bigint(21)
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `v_productos_mas_devueltos`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `v_productos_mas_devueltos` (
`codigo_barras` varchar(100)
,`nombre_producto` varchar(250)
,`total_devoluciones` bigint(21)
,`total_unidades_devueltas` decimal(32,0)
,`valor_total_devuelto` decimal(37,2)
,`tipo_devolucion` varchar(50)
,`tipo_nombre` varchar(100)
,`promedio_cantidad_por_devolucion` decimal(14,4)
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `v_recordatorios_completos`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `v_recordatorios_completos` (
`id_recordatorio` int(11)
,`titulo` varchar(255)
,`descripcion` text
,`mensaje_whatsapp` text
,`mensaje_notificacion` text
,`fecha_programada` datetime
,`fecha_creacion` timestamp
,`prioridad` enum('baja','media','alta','urgente')
,`estado` enum('programado','enviando','enviado','cancelado','error')
,`tipo_envio` set('whatsapp','notificacion','ambos')
,`destinatarios` enum('todos','sucursal','grupo','individual')
,`sucursal_id` int(11)
,`grupo_id` int(11)
,`intentos_envio` int(11)
,`max_intentos` int(11)
,`fecha_ultimo_intento` timestamp
,`error_ultimo_intento` text
,`creador_nombre` varchar(250)
,`modificador_nombre` varchar(250)
,`sucursal_nombre` varchar(200)
,`grupo_nombre` varchar(100)
,`estado_descripcion` varchar(17)
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `v_recordatorios_destinatarios_completos`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `v_recordatorios_destinatarios_completos` (
`id_destinatario` int(11)
,`recordatorio_id` int(11)
,`usuario_id` int(11)
,`telefono_whatsapp` varchar(20)
,`estado_envio` enum('pendiente','enviado','error','cancelado')
,`fecha_envio` timestamp
,`error_envio` text
,`tipo_envio` enum('whatsapp','notificacion','ambos')
,`usuario_nombre` varchar(250)
,`usuario_email` varchar(100)
,`usuario_telefono` varchar(12)
,`sucursal_nombre` varchar(200)
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `v_resumen_mensual`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `v_resumen_mensual` (
`usuario_id` int(11)
,`Nombre_Apellidos` varchar(250)
,`año` int(5)
,`mes` int(3)
,`total_entradas` bigint(21)
,`total_salidas` bigint(21)
,`total_registros` bigint(21)
);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `AbonosCreditosLiquidaciones`
--
ALTER TABLE `AbonosCreditosLiquidaciones`
  ADD PRIMARY KEY (`IdAbono`);

--
-- Indices de la tabla `AbonosCreditosVentas`
--
ALTER TABLE `AbonosCreditosVentas`
  ADD PRIMARY KEY (`IdAbono`);

--
-- Indices de la tabla `ActualizacionesMasivasProductosPOS`
--
ALTER TABLE `ActualizacionesMasivasProductosPOS`
  ADD PRIMARY KEY (`ID_Prod_POS`);

--
-- Indices de la tabla `ActualizacionMasivaProductosGlobales`
--
ALTER TABLE `ActualizacionMasivaProductosGlobales`
  ADD PRIMARY KEY (`IdActualizador`);

--
-- Indices de la tabla `ActualizacionMaxMin`
--
ALTER TABLE `ActualizacionMaxMin`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `Agenda_Laboratorios`
--
ALTER TABLE `Agenda_Laboratorios`
  ADD PRIMARY KEY (`Id_agenda`);

--
-- Indices de la tabla `Agenda_revaloraciones`
--
ALTER TABLE `Agenda_revaloraciones`
  ADD PRIMARY KEY (`Id_agenda`);

--
-- Indices de la tabla `AjustesDeInventarios`
--
ALTER TABLE `AjustesDeInventarios`
  ADD PRIMARY KEY (`Folio_Ingreso`),
  ADD KEY `ID_Prod_POS` (`ID_Prod_POS`);

--
-- Indices de la tabla `Areas_Credit_POS`
--
ALTER TABLE `Areas_Credit_POS`
  ADD PRIMARY KEY (`ID_Area_Cred`);

--
-- Indices de la tabla `Areas_Credit_POS_Audita`
--
ALTER TABLE `Areas_Credit_POS_Audita`
  ADD PRIMARY KEY (`ID_Audita_Ar_Cred`);

--
-- Indices de la tabla `Area_De_Notificaciones`
--
ALTER TABLE `Area_De_Notificaciones`
  ADD PRIMARY KEY (`ID_Notificacion`);

--
-- Indices de la tabla `asistencias`
--
ALTER TABLE `asistencias`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_usuario_id` (`usuario_id`),
  ADD KEY `idx_tipo` (`tipo`),
  ADD KEY `idx_fecha_hora` (`fecha_hora`),
  ADD KEY `idx_usuario_fecha` (`usuario_id`,`fecha_hora`),
  ADD KEY `idx_asistencias_usuario_tipo_fecha` (`usuario_id`,`tipo`,`fecha_hora`);

--
-- Indices de la tabla `Bitacora_Limpieza`
--
ALTER TABLE `Bitacora_Limpieza`
  ADD PRIMARY KEY (`id_bitacora`);

--
-- Indices de la tabla `caducados_configuracion`
--
ALTER TABLE `caducados_configuracion`
  ADD PRIMARY KEY (`id_config`),
  ADD UNIQUE KEY `unique_sucursal` (`sucursal_id`);

--
-- Indices de la tabla `caducados_historial`
--
ALTER TABLE `caducados_historial`
  ADD PRIMARY KEY (`id_historial`),
  ADD KEY `idx_id_lote` (`id_lote`),
  ADD KEY `idx_tipo_movimiento` (`tipo_movimiento`),
  ADD KEY `idx_fecha_movimiento` (`fecha_movimiento`);

--
-- Indices de la tabla `caducados_notificaciones`
--
ALTER TABLE `caducados_notificaciones`
  ADD PRIMARY KEY (`id_notificacion`),
  ADD KEY `idx_fecha_programada` (`fecha_programada`),
  ADD KEY `idx_estado` (`estado`),
  ADD KEY `idx_tipo_alerta` (`tipo_alerta`),
  ADD KEY `fk_notificaciones_lote` (`id_lote`),
  ADD KEY `idx_notificaciones_fecha_estado` (`fecha_programada`,`estado`);

--
-- Indices de la tabla `Cajas`
--
ALTER TABLE `Cajas`
  ADD PRIMARY KEY (`ID_Caja`);

--
-- Indices de la tabla `Cajas_POS_Audita`
--
ALTER TABLE `Cajas_POS_Audita`
  ADD PRIMARY KEY (`ID_Caja_Audita`);

--
-- Indices de la tabla `Categorias_POS`
--
ALTER TABLE `Categorias_POS`
  ADD PRIMARY KEY (`Cat_ID`);

--
-- Indices de la tabla `Categorias_POS_Updates`
--
ALTER TABLE `Categorias_POS_Updates`
  ADD PRIMARY KEY (`ID_Update`);

--
-- Indices de la tabla `CEDIS`
--
ALTER TABLE `CEDIS`
  ADD PRIMARY KEY (`IdProdCedis`);

--
-- Indices de la tabla `CEDIS_Eliminados`
--
ALTER TABLE `CEDIS_Eliminados`
  ADD PRIMARY KEY (`Id_CedisEliminado`);

--
-- Indices de la tabla `Cedis_Inventarios`
--
ALTER TABLE `Cedis_Inventarios`
  ADD PRIMARY KEY (`IdProdCedis`);

--
-- Indices de la tabla `chat_configuraciones`
--
ALTER TABLE `chat_configuraciones`
  ADD PRIMARY KEY (`id_config`),
  ADD UNIQUE KEY `unique_usuario` (`usuario_id`);

--
-- Indices de la tabla `chat_conversaciones`
--
ALTER TABLE `chat_conversaciones`
  ADD PRIMARY KEY (`id_conversacion`),
  ADD KEY `idx_tipo_sucursal` (`tipo_conversacion`,`sucursal_id`),
  ADD KEY `idx_creado_por` (`creado_por`),
  ADD KEY `idx_activo_archivado` (`activo`,`archivado`),
  ADD KEY `idx_ultimo_mensaje_fecha` (`ultimo_mensaje_fecha`),
  ADD KEY `idx_privado` (`privado`),
  ADD KEY `fk_chat_conversaciones_sucursal` (`sucursal_id`),
  ADD KEY `idx_conversacion_activa_ultimo` (`activo`,`archivado`,`ultimo_mensaje_fecha` DESC);
ALTER TABLE `chat_conversaciones` ADD FULLTEXT KEY `idx_conversacion_nombre` (`nombre_conversacion`,`descripcion`);

--
-- Indices de la tabla `chat_estados_usuario`
--
ALTER TABLE `chat_estados_usuario`
  ADD PRIMARY KEY (`id_estado`),
  ADD UNIQUE KEY `unique_usuario` (`usuario_id`),
  ADD KEY `idx_estado` (`estado`),
  ADD KEY `idx_ultima_actividad` (`ultima_actividad`);

--
-- Indices de la tabla `chat_lecturas`
--
ALTER TABLE `chat_lecturas`
  ADD PRIMARY KEY (`id_lectura`),
  ADD UNIQUE KEY `unique_mensaje_usuario` (`mensaje_id`,`usuario_id`),
  ADD KEY `idx_usuario` (`usuario_id`),
  ADD KEY `idx_fecha_lectura` (`fecha_lectura`),
  ADD KEY `idx_mensaje` (`mensaje_id`),
  ADD KEY `idx_lectura_usuario_fecha` (`usuario_id`,`fecha_lectura` DESC);

--
-- Indices de la tabla `chat_mensajes`
--
ALTER TABLE `chat_mensajes`
  ADD PRIMARY KEY (`id_mensaje`),
  ADD KEY `idx_conversacion` (`conversacion_id`),
  ADD KEY `idx_usuario` (`usuario_id`),
  ADD KEY `idx_fecha_envio` (`fecha_envio`),
  ADD KEY `idx_tipo_mensaje` (`tipo_mensaje`),
  ADD KEY `idx_eliminado` (`eliminado`),
  ADD KEY `idx_mensaje_respuesta` (`mensaje_respuesta_id`),
  ADD KEY `idx_prioridad` (`prioridad`),
  ADD KEY `idx_archivo_hash` (`archivo_hash`),
  ADD KEY `idx_conversacion_fecha_eliminado` (`conversacion_id`,`fecha_envio` DESC,`eliminado`),
  ADD KEY `fk_chat_mensajes_eliminado_por` (`eliminado_por`),
  ADD KEY `idx_mensaje_conversacion_fecha` (`conversacion_id`,`fecha_envio` DESC,`eliminado`);
ALTER TABLE `chat_mensajes` ADD FULLTEXT KEY `idx_mensaje_texto` (`mensaje`);

--
-- Indices de la tabla `chat_mensajes_eliminados`
--
ALTER TABLE `chat_mensajes_eliminados`
  ADD PRIMARY KEY (`id_eliminacion`),
  ADD KEY `idx_mensaje_id` (`mensaje_id`),
  ADD KEY `idx_usuario_elimino` (`usuario_elimino`),
  ADD KEY `idx_fecha_eliminacion` (`fecha_eliminacion`);

--
-- Indices de la tabla `chat_participantes`
--
ALTER TABLE `chat_participantes`
  ADD PRIMARY KEY (`id_participante`),
  ADD UNIQUE KEY `unique_conversacion_usuario_activo` (`conversacion_id`,`usuario_id`,`activo`),
  ADD KEY `idx_usuario` (`usuario_id`),
  ADD KEY `idx_conversacion` (`conversacion_id`),
  ADD KEY `idx_activo` (`activo`),
  ADD KEY `idx_rol` (`rol`),
  ADD KEY `idx_ultima_lectura` (`ultima_lectura`),
  ADD KEY `idx_participante_activo_conversacion` (`activo`,`conversacion_id`,`usuario_id`);

--
-- Indices de la tabla `chat_reacciones`
--
ALTER TABLE `chat_reacciones`
  ADD PRIMARY KEY (`id_reaccion`),
  ADD UNIQUE KEY `unique_mensaje_usuario_reaccion` (`mensaje_id`,`usuario_id`,`tipo_reaccion`),
  ADD KEY `idx_mensaje` (`mensaje_id`),
  ADD KEY `idx_usuario` (`usuario_id`),
  ADD KEY `idx_tipo_reaccion` (`tipo_reaccion`),
  ADD KEY `idx_reaccion_mensaje_tipo` (`mensaje_id`,`tipo_reaccion`);

--
-- Indices de la tabla `Componentes`
--
ALTER TABLE `Componentes`
  ADD PRIMARY KEY (`ID`);

--
-- Indices de la tabla `configuracion_checador`
--
ALTER TABLE `configuracion_checador`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_usuario_clave` (`usuario_id`,`clave`);

--
-- Indices de la tabla `ConteosDiarios`
--
ALTER TABLE `ConteosDiarios`
  ADD PRIMARY KEY (`Folio_Ingreso`),
  ADD KEY `ID_Prod_POS` (`Cod_Barra`);

--
-- Indices de la tabla `ConteosDiariosrRESPALDO`
--
ALTER TABLE `ConteosDiariosrRESPALDO`
  ADD PRIMARY KEY (`Folio_Ingreso`),
  ADD KEY `ID_Prod_POS` (`Cod_Barra`);

--
-- Indices de la tabla `ConteosDiarios_Pausados`
--
ALTER TABLE `ConteosDiarios_Pausados`
  ADD PRIMARY KEY (`Folio_Ingreso`),
  ADD KEY `ID_Prod_POS` (`Cod_Barra`);

--
-- Indices de la tabla `Cortes_Cajas_POS`
--
ALTER TABLE `Cortes_Cajas_POS`
  ADD PRIMARY KEY (`ID_Caja`);

--
-- Indices de la tabla `Cotizaciones`
--
ALTER TABLE `Cotizaciones`
  ADD PRIMARY KEY (`IdProdCedis`);

--
-- Indices de la tabla `Creditos_POS`
--
ALTER TABLE `Creditos_POS`
  ADD PRIMARY KEY (`Folio_Credito`);

--
-- Indices de la tabla `Creditos_POS_Audita`
--
ALTER TABLE `Creditos_POS_Audita`
  ADD PRIMARY KEY (`Audita_Credi_POS`);

--
-- Indices de la tabla `Data_Facturacion_POS`
--
ALTER TABLE `Data_Facturacion_POS`
  ADD PRIMARY KEY (`ID_Factura`);

--
-- Indices de la tabla `Data_Pacientes`
--
ALTER TABLE `Data_Pacientes`
  ADD PRIMARY KEY (`ID_Data_Paciente`);

--
-- Indices de la tabla `Data_Pacientes_Updates`
--
ALTER TABLE `Data_Pacientes_Updates`
  ADD PRIMARY KEY (`ID_Update`);

--
-- Indices de la tabla `Detalle_Limpieza`
--
ALTER TABLE `Detalle_Limpieza`
  ADD PRIMARY KEY (`id_detalle`),
  ADD KEY `id_bitacora` (`id_bitacora`);

--
-- Indices de la tabla `Devoluciones`
--
ALTER TABLE `Devoluciones`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `folio` (`folio`),
  ADD KEY `idx_sucursal` (`sucursal_id`),
  ADD KEY `idx_usuario` (`usuario_id`),
  ADD KEY `idx_fecha` (`fecha`),
  ADD KEY `idx_estatus` (`estatus`),
  ADD KEY `idx_devoluciones_fecha_estatus` (`fecha`,`estatus`),
  ADD KEY `idx_devoluciones_sucursal_fecha` (`sucursal_id`,`fecha`);

--
-- Indices de la tabla `Devoluciones_Acciones`
--
ALTER TABLE `Devoluciones_Acciones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_devolucion` (`devolucion_id`),
  ADD KEY `idx_detalle` (`detalle_id`),
  ADD KEY `idx_usuario` (`usuario_ejecuta`);

--
-- Indices de la tabla `Devoluciones_Autorizaciones`
--
ALTER TABLE `Devoluciones_Autorizaciones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_devolucion` (`devolucion_id`),
  ADD KEY `idx_usuario` (`usuario_autoriza`);

--
-- Indices de la tabla `Devoluciones_Detalle`
--
ALTER TABLE `Devoluciones_Detalle`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_devolucion` (`devolucion_id`),
  ADD KEY `idx_producto` (`producto_id`),
  ADD KEY `idx_codigo_barras` (`codigo_barras`),
  ADD KEY `idx_tipo` (`tipo_devolucion`),
  ADD KEY `idx_detalle_tipo_fecha` (`tipo_devolucion`,`created_at`),
  ADD KEY `idx_detalle_producto_cantidad` (`producto_id`,`cantidad`);

--
-- Indices de la tabla `Devoluciones_Reportes`
--
ALTER TABLE `Devoluciones_Reportes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_tipo` (`tipo_reporte`),
  ADD KEY `idx_fecha` (`fecha_generacion`),
  ADD KEY `idx_usuario` (`usuario_genera`);

--
-- Indices de la tabla `Devolucion_POS`
--
ALTER TABLE `Devolucion_POS`
  ADD PRIMARY KEY (`ID_Registro`);

--
-- Indices de la tabla `encargos`
--
ALTER TABLE `encargos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `Errores_POS`
--
ALTER TABLE `Errores_POS`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `Errores_POS_Ventas`
--
ALTER TABLE `Errores_POS_Ventas`
  ADD PRIMARY KEY (`ID_Error`);

--
-- Indices de la tabla `ErrorLog`
--
ALTER TABLE `ErrorLog`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `error_log_act_prod`
--
ALTER TABLE `error_log_act_prod`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `Estados`
--
ALTER TABLE `Estados`
  ADD PRIMARY KEY (`ID_Estado`);

--
-- Indices de la tabla `Fondos_Cajas`
--
ALTER TABLE `Fondos_Cajas`
  ADD PRIMARY KEY (`ID_Fon_Caja`),
  ADD KEY `Fk_Sucursal` (`Fk_Sucursal`);

--
-- Indices de la tabla `Fondos_Cajas_Audita`
--
ALTER TABLE `Fondos_Cajas_Audita`
  ADD PRIMARY KEY (`ID_Audita_FonCaja`),
  ADD KEY `Fk_Sucursal` (`Fk_Sucursal`);

--
-- Indices de la tabla `GastosPOS`
--
ALTER TABLE `GastosPOS`
  ADD PRIMARY KEY (`ID_Gastos`);

--
-- Indices de la tabla `Gestion_Lotes_Movimientos`
--
ALTER TABLE `Gestion_Lotes_Movimientos`
  ADD PRIMARY KEY (`ID_Movimiento`),
  ADD KEY `idx_producto_sucursal` (`ID_Prod_POS`,`Fk_sucursal`),
  ADD KEY `idx_cod_barra` (`Cod_Barra`),
  ADD KEY `idx_fecha` (`Fecha_Modificacion`);

--
-- Indices de la tabla `historial_abonos_encargos`
--
ALTER TABLE `historial_abonos_encargos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_encargo_id` (`encargo_id`),
  ADD KEY `idx_fecha_abono` (`fecha_abono`),
  ADD KEY `idx_sucursal` (`sucursal`);

--
-- Indices de la tabla `Historial_Lotes`
--
ALTER TABLE `Historial_Lotes`
  ADD PRIMARY KEY (`ID_Historial`),
  ADD KEY `idx_caducidad_existencias` (`Fecha_Caducidad`,`Existencias`);

--
-- Indices de la tabla `IngresosAutorizados`
--
ALTER TABLE `IngresosAutorizados`
  ADD PRIMARY KEY (`IDIngreso`);

--
-- Indices de la tabla `IngresosCedis`
--
ALTER TABLE `IngresosCedis`
  ADD PRIMARY KEY (`IDIngreso`);

--
-- Indices de la tabla `IngresosFarmacias`
--
ALTER TABLE `IngresosFarmacias`
  ADD PRIMARY KEY (`IdProdCedis`);

--
-- Indices de la tabla `Inserciones_Excel_inventarios`
--
ALTER TABLE `Inserciones_Excel_inventarios`
  ADD PRIMARY KEY (`Id_Insert`);

--
-- Indices de la tabla `InventariosStocks_Conteos`
--
ALTER TABLE `InventariosStocks_Conteos`
  ADD PRIMARY KEY (`Folio_Prod_Stock`),
  ADD KEY `ID_Prod_POS` (`ID_Prod_POS`);

--
-- Indices de la tabla `InventariosSucursales`
--
ALTER TABLE `InventariosSucursales`
  ADD PRIMARY KEY (`IdProdCedis`);

--
-- Indices de la tabla `Inventarios_Clinicas`
--
ALTER TABLE `Inventarios_Clinicas`
  ADD PRIMARY KEY (`ID_Inv_Clic`);

--
-- Indices de la tabla `Inventarios_Clinicas_audita`
--
ALTER TABLE `Inventarios_Clinicas_audita`
  ADD PRIMARY KEY (`ID_Inv_Clic_Audita`);

--
-- Indices de la tabla `inventario_inicial_estado`
--
ALTER TABLE `inventario_inicial_estado`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `fkSucursal` (`fkSucursal`,`fecha_establecido`);

--
-- Indices de la tabla `Inventario_lotes_fechas`
--
ALTER TABLE `Inventario_lotes_fechas`
  ADD PRIMARY KEY (`ID`);

--
-- Indices de la tabla `Inventario_Mobiliario`
--
ALTER TABLE `Inventario_Mobiliario`
  ADD PRIMARY KEY (`Id_inventario`);

--
-- Indices de la tabla `Inventario_Productos_Bloqueados`
--
ALTER TABLE `Inventario_Productos_Bloqueados`
  ADD PRIMARY KEY (`ID_Bloqueo`),
  ADD UNIQUE KEY `idx_producto_turno` (`ID_Turno`,`ID_Prod_POS`,`Cod_Barra`),
  ADD KEY `idx_usuario` (`Usuario_Bloqueo`),
  ADD KEY `idx_sucursal` (`Fk_sucursal`);

--
-- Indices de la tabla `Inventario_Turnos`
--
ALTER TABLE `Inventario_Turnos`
  ADD PRIMARY KEY (`ID_Turno`),
  ADD UNIQUE KEY `idx_folio` (`Folio_Turno`),
  ADD KEY `idx_sucursal_fecha` (`Fk_sucursal`,`Fecha_Turno`),
  ADD KEY `idx_usuario` (`Usuario_Actual`),
  ADD KEY `idx_estado` (`Estado`);

--
-- Indices de la tabla `Inventario_Turnos_Historial`
--
ALTER TABLE `Inventario_Turnos_Historial`
  ADD PRIMARY KEY (`ID_Historial`),
  ADD KEY `idx_turno` (`ID_Turno`,`Folio_Turno`),
  ADD KEY `idx_fecha` (`Fecha_Accion`);

--
-- Indices de la tabla `Inventario_Turnos_Productos`
--
ALTER TABLE `Inventario_Turnos_Productos`
  ADD PRIMARY KEY (`ID_Registro`),
  ADD KEY `idx_turno` (`ID_Turno`,`Folio_Turno`),
  ADD KEY `idx_producto` (`ID_Prod_POS`,`Cod_Barra`),
  ADD KEY `idx_usuario_estado` (`Usuario_Selecciono`,`Estado`),
  ADD KEY `idx_sucursal` (`Fk_sucursal`);

--
-- Indices de la tabla `Licencias`
--
ALTER TABLE `Licencias`
  ADD PRIMARY KEY (`ID_Licencia`);

--
-- Indices de la tabla `ListadoServicios`
--
ALTER TABLE `ListadoServicios`
  ADD PRIMARY KEY (`Servicio_ID`),
  ADD KEY `idx_servicio_id` (`Servicio_ID`);

--
-- Indices de la tabla `Localidades`
--
ALTER TABLE `Localidades`
  ADD PRIMARY KEY (`ID_Localidad`),
  ADD KEY `Fk_Municipio` (`Fk_Municipio`);

--
-- Indices de la tabla `logsingresosmedicamentos`
--
ALTER TABLE `logsingresosmedicamentos`
  ADD PRIMARY KEY (`log_id`);

--
-- Indices de la tabla `logs_checador`
--
ALTER TABLE `logs_checador`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_usuario_id` (`usuario_id`),
  ADD KEY `idx_accion` (`accion`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indices de la tabla `Lotes_Descuentos_Ventas`
--
ALTER TABLE `Lotes_Descuentos_Ventas`
  ADD PRIMARY KEY (`ID_Descuento`),
  ADD KEY `idx_venta` (`Folio_Ticket`),
  ADD KEY `idx_producto_lote` (`ID_Prod_POS`,`Lote`,`Fk_sucursal`),
  ADD KEY `idx_fecha_caducidad` (`Fecha_Caducidad`);

--
-- Indices de la tabla `Marcas_POS`
--
ALTER TABLE `Marcas_POS`
  ADD PRIMARY KEY (`Marca_ID`);

--
-- Indices de la tabla `Marcas_POS_Updates`
--
ALTER TABLE `Marcas_POS_Updates`
  ADD PRIMARY KEY (`ID_Update_Mar`);

--
-- Indices de la tabla `Municipios`
--
ALTER TABLE `Municipios`
  ADD PRIMARY KEY (`ID_Municipio`),
  ADD KEY `Fk_Estado` (`Fk_Estado`);

--
-- Indices de la tabla `Notificaciones`
--
ALTER TABLE `Notificaciones`
  ADD PRIMARY KEY (`ID_Notificacion`),
  ADD KEY `SucursalID` (`SucursalID`),
  ADD KEY `Tipo` (`Tipo`),
  ADD KEY `Fecha` (`Fecha`);

--
-- Indices de la tabla `Ordenes_Compra_Sugeridas`
--
ALTER TABLE `Ordenes_Compra_Sugeridas`
  ADD PRIMARY KEY (`ID_Orden`),
  ADD KEY `idx_folio_prod_stock` (`Folio_Prod_Stock`);

--
-- Indices de la tabla `PagosServicios`
--
ALTER TABLE `PagosServicios`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `folio` (`folio`),
  ADD KEY `idx_sucursal` (`sucursal_id`),
  ADD KEY `idx_estado` (`estado`),
  ADD KEY `idx_fecha_creacion` (`fecha_creacion`);

--
-- Indices de la tabla `pedido_detalles`
--
ALTER TABLE `pedido_detalles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_pedido` (`pedido_id`),
  ADD KEY `idx_producto` (`producto_id`);

--
-- Indices de la tabla `pedido_historial`
--
ALTER TABLE `pedido_historial`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_pedido_fecha` (`pedido_id`,`fecha_cambio`);

--
-- Indices de la tabla `Presentaciones`
--
ALTER TABLE `Presentaciones`
  ADD PRIMARY KEY (`Presentacion_ID`);

--
-- Indices de la tabla `Presentacion_Prod_POS_Updates`
--
ALTER TABLE `Presentacion_Prod_POS_Updates`
  ADD PRIMARY KEY (`ID_Update_Pre`);

--
-- Indices de la tabla `Productos`
--
ALTER TABLE `Productos`
  ADD PRIMARY KEY (`ID_Producto`);

--
-- Indices de la tabla `productos_lotes_caducidad`
--
ALTER TABLE `productos_lotes_caducidad`
  ADD PRIMARY KEY (`id_lote`),
  ADD KEY `idx_cod_barra` (`cod_barra`),
  ADD KEY `idx_lote` (`lote`),
  ADD KEY `idx_fecha_caducidad` (`fecha_caducidad`),
  ADD KEY `idx_sucursal` (`sucursal_id`),
  ADD KEY `idx_estado` (`estado`),
  ADD KEY `idx_folio_stock` (`folio_stock`),
  ADD KEY `idx_productos_caducidad_fecha` (`fecha_caducidad`,`estado`),
  ADD KEY `idx_productos_caducidad_sucursal_fecha` (`sucursal_id`,`fecha_caducidad`);

--
-- Indices de la tabla `Productos_POS`
--
ALTER TABLE `Productos_POS`
  ADD PRIMARY KEY (`ID_Prod_POS`);

--
-- Indices de la tabla `Productos_POS_Auditoria`
--
ALTER TABLE `Productos_POS_Auditoria`
  ADD PRIMARY KEY (`Id_Auditoria`);

--
-- Indices de la tabla `Productos_POS_Eliminados`
--
ALTER TABLE `Productos_POS_Eliminados`
  ADD PRIMARY KEY (`EliminadoIDPOS`);

--
-- Indices de la tabla `producto_proveedor`
--
ALTER TABLE `producto_proveedor`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_producto_proveedor` (`producto_id`,`proveedor_id`),
  ADD KEY `idx_producto` (`producto_id`),
  ADD KEY `idx_proveedor` (`proveedor_id`);

--
-- Indices de la tabla `Proveedores`
--
ALTER TABLE `Proveedores`
  ADD PRIMARY KEY (`ID_Proveedor`);

--
-- Indices de la tabla `proveedores_pedidos`
--
ALTER TABLE `proveedores_pedidos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_activo` (`activo`);

--
-- Indices de la tabla `recordatorios_config_whatsapp`
--
ALTER TABLE `recordatorios_config_whatsapp`
  ADD PRIMARY KEY (`id_config`),
  ADD KEY `fk_recordatorios_config_whatsapp_usuario` (`usuario_configurador`);

--
-- Indices de la tabla `recordatorios_destinatarios`
--
ALTER TABLE `recordatorios_destinatarios`
  ADD PRIMARY KEY (`id_destinatario`),
  ADD KEY `idx_recordatorio` (`recordatorio_id`),
  ADD KEY `idx_usuario` (`usuario_id`),
  ADD KEY `idx_estado_envio` (`estado_envio`),
  ADD KEY `idx_destinatarios_estado_envio` (`estado_envio`,`fecha_envio`);

--
-- Indices de la tabla `recordatorios_grupos`
--
ALTER TABLE `recordatorios_grupos`
  ADD PRIMARY KEY (`id_grupo`),
  ADD KEY `idx_sucursal` (`sucursal_id`),
  ADD KEY `idx_activo` (`activo`),
  ADD KEY `fk_recordatorios_grupos_usuario_creador` (`usuario_creador`);

--
-- Indices de la tabla `recordatorios_grupos_miembros`
--
ALTER TABLE `recordatorios_grupos_miembros`
  ADD PRIMARY KEY (`id_miembro`),
  ADD UNIQUE KEY `unique_grupo_usuario` (`grupo_id`,`usuario_id`),
  ADD KEY `idx_grupo` (`grupo_id`),
  ADD KEY `idx_usuario` (`usuario_id`);

--
-- Indices de la tabla `recordatorios_logs`
--
ALTER TABLE `recordatorios_logs`
  ADD PRIMARY KEY (`id_log`),
  ADD KEY `idx_recordatorio` (`recordatorio_id`),
  ADD KEY `idx_destinatario` (`destinatario_id`),
  ADD KEY `idx_fecha` (`fecha_log`);

--
-- Indices de la tabla `Recordatorios_Pendientes`
--
ALTER TABLE `Recordatorios_Pendientes`
  ADD PRIMARY KEY (`ID_Notificacion`);

--
-- Indices de la tabla `recordatorios_plantillas`
--
ALTER TABLE `recordatorios_plantillas`
  ADD PRIMARY KEY (`id_plantilla`),
  ADD KEY `idx_tipo` (`tipo`),
  ADD KEY `idx_activo` (`activo`),
  ADD KEY `fk_recordatorios_plantillas_usuario_creador` (`usuario_creador`);

--
-- Indices de la tabla `recordatorios_sistema`
--
ALTER TABLE `recordatorios_sistema`
  ADD PRIMARY KEY (`id_recordatorio`),
  ADD KEY `idx_fecha_programada` (`fecha_programada`),
  ADD KEY `idx_estado` (`estado`),
  ADD KEY `idx_prioridad` (`prioridad`),
  ADD KEY `idx_destinatarios` (`destinatarios`),
  ADD KEY `idx_sucursal` (`sucursal_id`),
  ADD KEY `idx_usuario_creador` (`usuario_creador`),
  ADD KEY `fk_recordatorios_usuario_modificador` (`usuario_modificador`),
  ADD KEY `idx_recordatorios_fecha_estado` (`fecha_programada`,`estado`),
  ADD KEY `idx_recordatorios_prioridad_estado` (`prioridad`,`estado`);

--
-- Indices de la tabla `Registros_Energia`
--
ALTER TABLE `Registros_Energia`
  ADD PRIMARY KEY (`Id_Registro`);

--
-- Indices de la tabla `registro_errores_Actualizacionanaqueles`
--
ALTER TABLE `registro_errores_Actualizacionanaqueles`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `Servicios_POS`
--
ALTER TABLE `Servicios_POS`
  ADD PRIMARY KEY (`Servicio_ID`);

--
-- Indices de la tabla `Servicios_POS_Audita`
--
ALTER TABLE `Servicios_POS_Audita`
  ADD PRIMARY KEY (`Audita_Serv_ID`);

--
-- Indices de la tabla `Solicitudes_Ingresos`
--
ALTER TABLE `Solicitudes_Ingresos`
  ADD PRIMARY KEY (`IdProdCedis`);

--
-- Indices de la tabla `Solicitudes_Ingresos_Eliminados`
--
ALTER TABLE `Solicitudes_Ingresos_Eliminados`
  ADD PRIMARY KEY (`Id_Eliminado`);

--
-- Indices de la tabla `Stock_POS`
--
ALTER TABLE `Stock_POS`
  ADD PRIMARY KEY (`Folio_Prod_Stock`),
  ADD KEY `ID_Prod_POS` (`ID_Prod_POS`);

--
-- Indices de la tabla `Stock_POS_Log`
--
ALTER TABLE `Stock_POS_Log`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `Stock_POS_respaldo`
--
ALTER TABLE `Stock_POS_respaldo`
  ADD PRIMARY KEY (`Folio_Prod_Stock`),
  ADD KEY `ID_Prod_POS` (`ID_Prod_POS`);

--
-- Indices de la tabla `Stock_POS_Respaldo2611`
--
ALTER TABLE `Stock_POS_Respaldo2611`
  ADD PRIMARY KEY (`Folio_Prod_Stock`),
  ADD KEY `ID_Prod_POS` (`ID_Prod_POS`);

--
-- Indices de la tabla `Stock_POS_RespaldoSeptiembre`
--
ALTER TABLE `Stock_POS_RespaldoSeptiembre`
  ADD PRIMARY KEY (`Folio_Prod_Stock`),
  ADD KEY `ID_Prod_POS` (`ID_Prod_POS`);

--
-- Indices de la tabla `Stock_registrosNuevos`
--
ALTER TABLE `Stock_registrosNuevos`
  ADD PRIMARY KEY (`Folio_Ingreso`),
  ADD KEY `ID_Prod_POS` (`ID_Prod_POS`);

--
-- Indices de la tabla `Sucursales`
--
ALTER TABLE `Sucursales`
  ADD PRIMARY KEY (`ID_Sucursal`),
  ADD UNIQUE KEY `Nombre_Sucursal` (`Nombre_Sucursal`,`Licencia`);

--
-- Indices de la tabla `Suscripciones_Push`
--
ALTER TABLE `Suscripciones_Push`
  ADD PRIMARY KEY (`ID_Suscripcion`);

--
-- Indices de la tabla `tareas`
--
ALTER TABLE `tareas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_asignado_a` (`asignado_a`),
  ADD KEY `idx_estado` (`estado`),
  ADD KEY `idx_prioridad` (`prioridad`);

--
-- Indices de la tabla `Tareas`
--
ALTER TABLE `Tareas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_asignado_a` (`asignado_a`),
  ADD KEY `idx_creado_por` (`creado_por`),
  ADD KEY `idx_estado` (`estado`),
  ADD KEY `idx_prioridad` (`prioridad`),
  ADD KEY `idx_fecha_limite` (`fecha_limite`);

--
-- Indices de la tabla `TareasPorHacer`
--
ALTER TABLE `TareasPorHacer`
  ADD PRIMARY KEY (`ID_Tarea`);

--
-- Indices de la tabla `templates_downloads`
--
ALTER TABLE `templates_downloads`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- Indices de la tabla `TiposDeGastos`
--
ALTER TABLE `TiposDeGastos`
  ADD PRIMARY KEY (`Gasto_ID`);

--
-- Indices de la tabla `Tipos_Devolucion`
--
ALTER TABLE `Tipos_Devolucion`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `codigo` (`codigo`);

--
-- Indices de la tabla `Tipos_estudios`
--
ALTER TABLE `Tipos_estudios`
  ADD PRIMARY KEY (`ID_tipo_analisis`),
  ADD KEY `Fk_Tipo_analisis` (`Fk_Tipo_analisis`,`ID_H_O_D`),
  ADD KEY `ID_H_O_D` (`ID_H_O_D`);

--
-- Indices de la tabla `Tipos_Usuarios`
--
ALTER TABLE `Tipos_Usuarios`
  ADD PRIMARY KEY (`ID_User`);

--
-- Indices de la tabla `TipProd_POS`
--
ALTER TABLE `TipProd_POS`
  ADD PRIMARY KEY (`Tip_Prod_ID`);

--
-- Indices de la tabla `TipProd_POS_Audita`
--
ALTER TABLE `TipProd_POS_Audita`
  ADD PRIMARY KEY (`ID_Audita_TipoProd`);

--
-- Indices de la tabla `TraspasosYNotasC`
--
ALTER TABLE `TraspasosYNotasC`
  ADD PRIMARY KEY (`TraspaNotID`);

--
-- Indices de la tabla `Traspasos_generados`
--
ALTER TABLE `Traspasos_generados`
  ADD PRIMARY KEY (`ID_Traspaso_Generado`);

--
-- Indices de la tabla `Traspasos_generados_audita`
--
ALTER TABLE `Traspasos_generados_audita`
  ADD PRIMARY KEY (`id_audita_traspaso`);

--
-- Indices de la tabla `Traspasos_generados_Eliminados`
--
ALTER TABLE `Traspasos_generados_Eliminados`
  ADD PRIMARY KEY (`ID_eliminado`),
  ADD KEY `ID_Prod_POS` (`ID_Prod_POS`);

--
-- Indices de la tabla `Traspasos_generados_Entre_sucursales`
--
ALTER TABLE `Traspasos_generados_Entre_sucursales`
  ADD PRIMARY KEY (`ID_Traspaso_Generado`);

--
-- Indices de la tabla `Traspasos_Recepcionados`
--
ALTER TABLE `Traspasos_Recepcionados`
  ADD PRIMARY KEY (`Id_recepcion`);

--
-- Indices de la tabla `ubicaciones_trabajo`
--
ALTER TABLE `ubicaciones_trabajo`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_usuario_id` (`usuario_id`),
  ADD KEY `idx_estado` (`estado`),
  ADD KEY `idx_ubicaciones_usuario_estado` (`usuario_id`,`estado`);

--
-- Indices de la tabla `Usuarios_PV`
--
ALTER TABLE `Usuarios_PV`
  ADD PRIMARY KEY (`Id_PvUser`),
  ADD KEY `Nombre_Apellidos` (`Nombre_Apellidos`),
  ADD KEY `Fk_Usuario` (`Fk_Usuario`);

--
-- Indices de la tabla `Ventas_POS`
--
ALTER TABLE `Ventas_POS`
  ADD PRIMARY KEY (`Venta_POS_ID`);

--
-- Indices de la tabla `Ventas_POSV2`
--
ALTER TABLE `Ventas_POSV2`
  ADD PRIMARY KEY (`Venta_POS_ID`);

--
-- Indices de la tabla `Ventas_POS_Audita`
--
ALTER TABLE `Ventas_POS_Audita`
  ADD PRIMARY KEY (`ID_Audita`);

--
-- Indices de la tabla `Ventas_POS_Cancelaciones`
--
ALTER TABLE `Ventas_POS_Cancelaciones`
  ADD PRIMARY KEY (`Cancelacion_IDVenPOS`);

--
-- Indices de la tabla `Ventas_POS_Pruebas`
--
ALTER TABLE `Ventas_POS_Pruebas`
  ADD PRIMARY KEY (`Venta_POS_ID`);

--
-- Indices de la tabla `Ventas_POS_respaldo`
--
ALTER TABLE `Ventas_POS_respaldo`
  ADD PRIMARY KEY (`Venta_POS_ID`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `AbonosCreditosLiquidaciones`
--
ALTER TABLE `AbonosCreditosLiquidaciones`
  MODIFY `IdAbono` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `AbonosCreditosVentas`
--
ALTER TABLE `AbonosCreditosVentas`
  MODIFY `IdAbono` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `ActualizacionesMasivasProductosPOS`
--
ALTER TABLE `ActualizacionesMasivasProductosPOS`
  MODIFY `ID_Prod_POS` int(12) UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `ActualizacionMasivaProductosGlobales`
--
ALTER TABLE `ActualizacionMasivaProductosGlobales`
  MODIFY `IdActualizador` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `ActualizacionMaxMin`
--
ALTER TABLE `ActualizacionMaxMin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `Agenda_Laboratorios`
--
ALTER TABLE `Agenda_Laboratorios`
  MODIFY `Id_agenda` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `Agenda_revaloraciones`
--
ALTER TABLE `Agenda_revaloraciones`
  MODIFY `Id_agenda` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `AjustesDeInventarios`
--
ALTER TABLE `AjustesDeInventarios`
  MODIFY `Folio_Ingreso` int(10) UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `Areas_Credit_POS`
--
ALTER TABLE `Areas_Credit_POS`
  MODIFY `ID_Area_Cred` int(10) UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `Areas_Credit_POS_Audita`
--
ALTER TABLE `Areas_Credit_POS_Audita`
  MODIFY `ID_Audita_Ar_Cred` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `Area_De_Notificaciones`
--
ALTER TABLE `Area_De_Notificaciones`
  MODIFY `ID_Notificacion` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `asistencias`
--
ALTER TABLE `asistencias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `Bitacora_Limpieza`
--
ALTER TABLE `Bitacora_Limpieza`
  MODIFY `id_bitacora` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `caducados_configuracion`
--
ALTER TABLE `caducados_configuracion`
  MODIFY `id_config` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `caducados_historial`
--
ALTER TABLE `caducados_historial`
  MODIFY `id_historial` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `caducados_notificaciones`
--
ALTER TABLE `caducados_notificaciones`
  MODIFY `id_notificacion` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `Cajas`
--
ALTER TABLE `Cajas`
  MODIFY `ID_Caja` int(10) UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `Cajas_POS_Audita`
--
ALTER TABLE `Cajas_POS_Audita`
  MODIFY `ID_Caja_Audita` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `Categorias_POS`
--
ALTER TABLE `Categorias_POS`
  MODIFY `Cat_ID` int(11) UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `Categorias_POS_Updates`
--
ALTER TABLE `Categorias_POS_Updates`
  MODIFY `ID_Update` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `CEDIS`
--
ALTER TABLE `CEDIS`
  MODIFY `IdProdCedis` int(12) UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `CEDIS_Eliminados`
--
ALTER TABLE `CEDIS_Eliminados`
  MODIFY `Id_CedisEliminado` int(10) UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `Cedis_Inventarios`
--
ALTER TABLE `Cedis_Inventarios`
  MODIFY `IdProdCedis` int(12) UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `chat_configuraciones`
--
ALTER TABLE `chat_configuraciones`
  MODIFY `id_config` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `chat_conversaciones`
--
ALTER TABLE `chat_conversaciones`
  MODIFY `id_conversacion` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `chat_estados_usuario`
--
ALTER TABLE `chat_estados_usuario`
  MODIFY `id_estado` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `chat_lecturas`
--
ALTER TABLE `chat_lecturas`
  MODIFY `id_lectura` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `chat_mensajes`
--
ALTER TABLE `chat_mensajes`
  MODIFY `id_mensaje` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `chat_mensajes_eliminados`
--
ALTER TABLE `chat_mensajes_eliminados`
  MODIFY `id_eliminacion` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `chat_participantes`
--
ALTER TABLE `chat_participantes`
  MODIFY `id_participante` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `chat_reacciones`
--
ALTER TABLE `chat_reacciones`
  MODIFY `id_reaccion` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `Componentes`
--
ALTER TABLE `Componentes`
  MODIFY `ID` int(11) UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `configuracion_checador`
--
ALTER TABLE `configuracion_checador`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `ConteosDiarios`
--
ALTER TABLE `ConteosDiarios`
  MODIFY `Folio_Ingreso` int(10) UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `ConteosDiariosrRESPALDO`
--
ALTER TABLE `ConteosDiariosrRESPALDO`
  MODIFY `Folio_Ingreso` int(10) UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `ConteosDiarios_Pausados`
--
ALTER TABLE `ConteosDiarios_Pausados`
  MODIFY `Folio_Ingreso` int(10) UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `Cortes_Cajas_POS`
--
ALTER TABLE `Cortes_Cajas_POS`
  MODIFY `ID_Caja` int(10) UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `Cotizaciones`
--
ALTER TABLE `Cotizaciones`
  MODIFY `IdProdCedis` int(12) UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `Creditos_POS`
--
ALTER TABLE `Creditos_POS`
  MODIFY `Folio_Credito` int(10) UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `Creditos_POS_Audita`
--
ALTER TABLE `Creditos_POS_Audita`
  MODIFY `Audita_Credi_POS` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `Data_Facturacion_POS`
--
ALTER TABLE `Data_Facturacion_POS`
  MODIFY `ID_Factura` int(10) UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `Data_Pacientes`
--
ALTER TABLE `Data_Pacientes`
  MODIFY `ID_Data_Paciente` int(12) UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `Data_Pacientes_Updates`
--
ALTER TABLE `Data_Pacientes_Updates`
  MODIFY `ID_Update` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `Detalle_Limpieza`
--
ALTER TABLE `Detalle_Limpieza`
  MODIFY `id_detalle` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `Devoluciones`
--
ALTER TABLE `Devoluciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `Devoluciones_Acciones`
--
ALTER TABLE `Devoluciones_Acciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `Devoluciones_Autorizaciones`
--
ALTER TABLE `Devoluciones_Autorizaciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `Devoluciones_Detalle`
--
ALTER TABLE `Devoluciones_Detalle`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `Devoluciones_Reportes`
--
ALTER TABLE `Devoluciones_Reportes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `Devolucion_POS`
--
ALTER TABLE `Devolucion_POS`
  MODIFY `ID_Registro` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `encargos`
--
ALTER TABLE `encargos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `Errores_POS`
--
ALTER TABLE `Errores_POS`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `Errores_POS_Ventas`
--
ALTER TABLE `Errores_POS_Ventas`
  MODIFY `ID_Error` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `ErrorLog`
--
ALTER TABLE `ErrorLog`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `error_log_act_prod`
--
ALTER TABLE `error_log_act_prod`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `Estados`
--
ALTER TABLE `Estados`
  MODIFY `ID_Estado` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `Fondos_Cajas`
--
ALTER TABLE `Fondos_Cajas`
  MODIFY `ID_Fon_Caja` int(10) UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `Fondos_Cajas_Audita`
--
ALTER TABLE `Fondos_Cajas_Audita`
  MODIFY `ID_Audita_FonCaja` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `GastosPOS`
--
ALTER TABLE `GastosPOS`
  MODIFY `ID_Gastos` int(10) UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `Gestion_Lotes_Movimientos`
--
ALTER TABLE `Gestion_Lotes_Movimientos`
  MODIFY `ID_Movimiento` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `historial_abonos_encargos`
--
ALTER TABLE `historial_abonos_encargos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `Historial_Lotes`
--
ALTER TABLE `Historial_Lotes`
  MODIFY `ID_Historial` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `IngresosAutorizados`
--
ALTER TABLE `IngresosAutorizados`
  MODIFY `IDIngreso` int(12) UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `IngresosCedis`
--
ALTER TABLE `IngresosCedis`
  MODIFY `IDIngreso` int(12) UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `IngresosFarmacias`
--
ALTER TABLE `IngresosFarmacias`
  MODIFY `IdProdCedis` int(12) UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `Inserciones_Excel_inventarios`
--
ALTER TABLE `Inserciones_Excel_inventarios`
  MODIFY `Id_Insert` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `InventariosStocks_Conteos`
--
ALTER TABLE `InventariosStocks_Conteos`
  MODIFY `Folio_Prod_Stock` int(10) UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `InventariosSucursales`
--
ALTER TABLE `InventariosSucursales`
  MODIFY `IdProdCedis` int(12) UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `Inventarios_Clinicas`
--
ALTER TABLE `Inventarios_Clinicas`
  MODIFY `ID_Inv_Clic` int(10) UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `Inventarios_Clinicas_audita`
--
ALTER TABLE `Inventarios_Clinicas_audita`
  MODIFY `ID_Inv_Clic_Audita` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `inventario_inicial_estado`
--
ALTER TABLE `inventario_inicial_estado`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `Inventario_lotes_fechas`
--
ALTER TABLE `Inventario_lotes_fechas`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `Inventario_Mobiliario`
--
ALTER TABLE `Inventario_Mobiliario`
  MODIFY `Id_inventario` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `Inventario_Productos_Bloqueados`
--
ALTER TABLE `Inventario_Productos_Bloqueados`
  MODIFY `ID_Bloqueo` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `Inventario_Turnos`
--
ALTER TABLE `Inventario_Turnos`
  MODIFY `ID_Turno` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `Inventario_Turnos_Historial`
--
ALTER TABLE `Inventario_Turnos_Historial`
  MODIFY `ID_Historial` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `Inventario_Turnos_Productos`
--
ALTER TABLE `Inventario_Turnos_Productos`
  MODIFY `ID_Registro` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `Licencias`
--
ALTER TABLE `Licencias`
  MODIFY `ID_Licencia` int(12) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `ListadoServicios`
--
ALTER TABLE `ListadoServicios`
  MODIFY `Servicio_ID` int(11) UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `Localidades`
--
ALTER TABLE `Localidades`
  MODIFY `ID_Localidad` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `logsingresosmedicamentos`
--
ALTER TABLE `logsingresosmedicamentos`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `logs_checador`
--
ALTER TABLE `logs_checador`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `Lotes_Descuentos_Ventas`
--
ALTER TABLE `Lotes_Descuentos_Ventas`
  MODIFY `ID_Descuento` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `Marcas_POS`
--
ALTER TABLE `Marcas_POS`
  MODIFY `Marca_ID` int(11) UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `Marcas_POS_Updates`
--
ALTER TABLE `Marcas_POS_Updates`
  MODIFY `ID_Update_Mar` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `Municipios`
--
ALTER TABLE `Municipios`
  MODIFY `ID_Municipio` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `Notificaciones`
--
ALTER TABLE `Notificaciones`
  MODIFY `ID_Notificacion` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `Ordenes_Compra_Sugeridas`
--
ALTER TABLE `Ordenes_Compra_Sugeridas`
  MODIFY `ID_Orden` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `PagosServicios`
--
ALTER TABLE `PagosServicios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `pedido_detalles`
--
ALTER TABLE `pedido_detalles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `pedido_historial`
--
ALTER TABLE `pedido_historial`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `Presentaciones`
--
ALTER TABLE `Presentaciones`
  MODIFY `Presentacion_ID` int(11) UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `Presentacion_Prod_POS_Updates`
--
ALTER TABLE `Presentacion_Prod_POS_Updates`
  MODIFY `ID_Update_Pre` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `productos_lotes_caducidad`
--
ALTER TABLE `productos_lotes_caducidad`
  MODIFY `id_lote` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `Productos_POS`
--
ALTER TABLE `Productos_POS`
  MODIFY `ID_Prod_POS` int(12) UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `Productos_POS_Auditoria`
--
ALTER TABLE `Productos_POS_Auditoria`
  MODIFY `Id_Auditoria` int(12) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `Productos_POS_Eliminados`
--
ALTER TABLE `Productos_POS_Eliminados`
  MODIFY `EliminadoIDPOS` int(10) UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `producto_proveedor`
--
ALTER TABLE `producto_proveedor`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `Proveedores`
--
ALTER TABLE `Proveedores`
  MODIFY `ID_Proveedor` int(11) UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `proveedores_pedidos`
--
ALTER TABLE `proveedores_pedidos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `recordatorios_config_whatsapp`
--
ALTER TABLE `recordatorios_config_whatsapp`
  MODIFY `id_config` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `recordatorios_destinatarios`
--
ALTER TABLE `recordatorios_destinatarios`
  MODIFY `id_destinatario` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `recordatorios_grupos`
--
ALTER TABLE `recordatorios_grupos`
  MODIFY `id_grupo` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `recordatorios_grupos_miembros`
--
ALTER TABLE `recordatorios_grupos_miembros`
  MODIFY `id_miembro` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `recordatorios_logs`
--
ALTER TABLE `recordatorios_logs`
  MODIFY `id_log` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `Recordatorios_Pendientes`
--
ALTER TABLE `Recordatorios_Pendientes`
  MODIFY `ID_Notificacion` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `recordatorios_plantillas`
--
ALTER TABLE `recordatorios_plantillas`
  MODIFY `id_plantilla` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `recordatorios_sistema`
--
ALTER TABLE `recordatorios_sistema`
  MODIFY `id_recordatorio` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `Registros_Energia`
--
ALTER TABLE `Registros_Energia`
  MODIFY `Id_Registro` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `registro_errores_Actualizacionanaqueles`
--
ALTER TABLE `registro_errores_Actualizacionanaqueles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `Servicios_POS`
--
ALTER TABLE `Servicios_POS`
  MODIFY `Servicio_ID` int(11) UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `Servicios_POS_Audita`
--
ALTER TABLE `Servicios_POS_Audita`
  MODIFY `Audita_Serv_ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `Solicitudes_Ingresos`
--
ALTER TABLE `Solicitudes_Ingresos`
  MODIFY `IdProdCedis` int(12) UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `Solicitudes_Ingresos_Eliminados`
--
ALTER TABLE `Solicitudes_Ingresos_Eliminados`
  MODIFY `Id_Eliminado` int(10) UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `Stock_POS`
--
ALTER TABLE `Stock_POS`
  MODIFY `Folio_Prod_Stock` int(10) UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `Stock_POS_Log`
--
ALTER TABLE `Stock_POS_Log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `Stock_POS_respaldo`
--
ALTER TABLE `Stock_POS_respaldo`
  MODIFY `Folio_Prod_Stock` int(10) UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `Stock_POS_Respaldo2611`
--
ALTER TABLE `Stock_POS_Respaldo2611`
  MODIFY `Folio_Prod_Stock` int(10) UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `Stock_POS_RespaldoSeptiembre`
--
ALTER TABLE `Stock_POS_RespaldoSeptiembre`
  MODIFY `Folio_Prod_Stock` int(10) UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `Stock_registrosNuevos`
--
ALTER TABLE `Stock_registrosNuevos`
  MODIFY `Folio_Ingreso` int(10) UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `Sucursales`
--
ALTER TABLE `Sucursales`
  MODIFY `ID_Sucursal` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `Suscripciones_Push`
--
ALTER TABLE `Suscripciones_Push`
  MODIFY `ID_Suscripcion` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tareas`
--
ALTER TABLE `tareas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `Tareas`
--
ALTER TABLE `Tareas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `TareasPorHacer`
--
ALTER TABLE `TareasPorHacer`
  MODIFY `ID_Tarea` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `templates_downloads`
--
ALTER TABLE `templates_downloads`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `TiposDeGastos`
--
ALTER TABLE `TiposDeGastos`
  MODIFY `Gasto_ID` int(11) UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `Tipos_Devolucion`
--
ALTER TABLE `Tipos_Devolucion`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `Tipos_estudios`
--
ALTER TABLE `Tipos_estudios`
  MODIFY `ID_tipo_analisis` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `Tipos_Usuarios`
--
ALTER TABLE `Tipos_Usuarios`
  MODIFY `ID_User` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `TipProd_POS`
--
ALTER TABLE `TipProd_POS`
  MODIFY `Tip_Prod_ID` int(11) UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `TipProd_POS_Audita`
--
ALTER TABLE `TipProd_POS_Audita`
  MODIFY `ID_Audita_TipoProd` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `TraspasosYNotasC`
--
ALTER TABLE `TraspasosYNotasC`
  MODIFY `TraspaNotID` int(10) UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `Traspasos_generados`
--
ALTER TABLE `Traspasos_generados`
  MODIFY `ID_Traspaso_Generado` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `Traspasos_generados_audita`
--
ALTER TABLE `Traspasos_generados_audita`
  MODIFY `id_audita_traspaso` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `Traspasos_generados_Eliminados`
--
ALTER TABLE `Traspasos_generados_Eliminados`
  MODIFY `ID_eliminado` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `Traspasos_generados_Entre_sucursales`
--
ALTER TABLE `Traspasos_generados_Entre_sucursales`
  MODIFY `ID_Traspaso_Generado` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `Traspasos_Recepcionados`
--
ALTER TABLE `Traspasos_Recepcionados`
  MODIFY `Id_recepcion` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `ubicaciones_trabajo`
--
ALTER TABLE `ubicaciones_trabajo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `Usuarios_PV`
--
ALTER TABLE `Usuarios_PV`
  MODIFY `Id_PvUser` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `Ventas_POS`
--
ALTER TABLE `Ventas_POS`
  MODIFY `Venta_POS_ID` int(10) UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `Ventas_POSV2`
--
ALTER TABLE `Ventas_POSV2`
  MODIFY `Venta_POS_ID` int(10) UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `Ventas_POS_Audita`
--
ALTER TABLE `Ventas_POS_Audita`
  MODIFY `ID_Audita` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `Ventas_POS_Cancelaciones`
--
ALTER TABLE `Ventas_POS_Cancelaciones`
  MODIFY `Cancelacion_IDVenPOS` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `Ventas_POS_Pruebas`
--
ALTER TABLE `Ventas_POS_Pruebas`
  MODIFY `Venta_POS_ID` int(10) UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `Ventas_POS_respaldo`
--
ALTER TABLE `Ventas_POS_respaldo`
  MODIFY `Venta_POS_ID` int(10) UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT;

-- --------------------------------------------------------

--
-- Estructura para la vista `v_chat_conversaciones_info`
--
DROP TABLE IF EXISTS `v_chat_conversaciones_info`;

CREATE ALGORITHM=UNDEFINED DEFINER=`u858848268_devpezer0`@`127.0.0.1` SQL SECURITY DEFINER VIEW `v_chat_conversaciones_info`  AS SELECT `c`.`id_conversacion` AS `id_conversacion`, `c`.`nombre_conversacion` AS `nombre_conversacion`, `c`.`descripcion` AS `descripcion`, `c`.`tipo_conversacion` AS `tipo_conversacion`, `c`.`sucursal_id` AS `sucursal_id`, `s`.`Nombre_Sucursal` AS `Nombre_Sucursal`, `c`.`creado_por` AS `creado_por`, `u`.`Nombre_Apellidos` AS `creado_por_nombre`, `u`.`file_name` AS `creado_por_avatar`, `c`.`fecha_creacion` AS `fecha_creacion`, `c`.`fecha_actualizacion` AS `fecha_actualizacion`, `c`.`ultimo_mensaje` AS `ultimo_mensaje`, `c`.`ultimo_mensaje_fecha` AS `ultimo_mensaje_fecha`, `c`.`ultimo_mensaje_usuario_id` AS `ultimo_mensaje_usuario_id`, `um`.`Nombre_Apellidos` AS `ultimo_mensaje_usuario_nombre`, `c`.`activo` AS `activo`, `c`.`privado` AS `privado`, `c`.`archivado` AS `archivado`, count(`p`.`id_participante`) AS `total_participantes`, count(case when `p`.`activo` = 1 then 1 end) AS `participantes_activos` FROM ((((`chat_conversaciones` `c` left join `Sucursales` `s` on(`c`.`sucursal_id` = `s`.`ID_Sucursal`)) left join `Usuarios_PV` `u` on(`c`.`creado_por` = `u`.`Id_PvUser`)) left join `Usuarios_PV` `um` on(`c`.`ultimo_mensaje_usuario_id` = `um`.`Id_PvUser`)) left join `chat_participantes` `p` on(`c`.`id_conversacion` = `p`.`conversacion_id`)) WHERE `c`.`activo` = 1 GROUP BY `c`.`id_conversacion` ;

-- --------------------------------------------------------

--
-- Estructura para la vista `v_chat_mensajes_info`
--
DROP TABLE IF EXISTS `v_chat_mensajes_info`;

CREATE ALGORITHM=UNDEFINED DEFINER=`u858848268_devpezer0`@`127.0.0.1` SQL SECURITY DEFINER VIEW `v_chat_mensajes_info`  AS SELECT `m`.`id_mensaje` AS `id_mensaje`, `m`.`conversacion_id` AS `conversacion_id`, `m`.`usuario_id` AS `usuario_id`, `u`.`Nombre_Apellidos` AS `usuario_nombre`, `u`.`file_name` AS `usuario_avatar`, `t`.`TipoUsuario` AS `usuario_tipo`, `m`.`mensaje` AS `mensaje`, `m`.`tipo_mensaje` AS `tipo_mensaje`, `m`.`archivo_url` AS `archivo_url`, `m`.`archivo_nombre` AS `archivo_nombre`, `m`.`archivo_tipo` AS `archivo_tipo`, `m`.`archivo_tamaño` AS `archivo_tamaño`, `m`.`archivo_hash` AS `archivo_hash`, `m`.`fecha_envio` AS `fecha_envio`, `m`.`fecha_edicion` AS `fecha_edicion`, `m`.`fecha_eliminacion` AS `fecha_eliminacion`, `m`.`editado` AS `editado`, `m`.`eliminado` AS `eliminado`, `m`.`eliminado_por` AS `eliminado_por`, `eu`.`Nombre_Apellidos` AS `eliminado_por_nombre`, `m`.`mensaje_respuesta_id` AS `mensaje_respuesta_id`, `m`.`mensaje_original_id` AS `mensaje_original_id`, `m`.`metadatos` AS `metadatos`, `m`.`prioridad` AS `prioridad`, `m`.`destinatarios_especificos` AS `destinatarios_especificos`, (select count(0) from `chat_reacciones` `r` where `r`.`mensaje_id` = `m`.`id_mensaje`) AS `total_reacciones`, (select count(0) from `chat_lecturas` `l` where `l`.`mensaje_id` = `m`.`id_mensaje`) AS `total_lecturas` FROM (((`chat_mensajes` `m` left join `Usuarios_PV` `u` on(`m`.`usuario_id` = `u`.`Id_PvUser`)) left join `Tipos_Usuarios` `t` on(`u`.`Fk_Usuario` = `t`.`ID_User`)) left join `Usuarios_PV` `eu` on(`m`.`eliminado_por` = `eu`.`Id_PvUser`)) WHERE `m`.`eliminado` = 0 ;

-- --------------------------------------------------------

--
-- Estructura para la vista `v_chat_participantes_info`
--
DROP TABLE IF EXISTS `v_chat_participantes_info`;

CREATE ALGORITHM=UNDEFINED DEFINER=`u858848268_devpezer0`@`127.0.0.1` SQL SECURITY DEFINER VIEW `v_chat_participantes_info`  AS SELECT `p`.`id_participante` AS `id_participante`, `p`.`conversacion_id` AS `conversacion_id`, `p`.`usuario_id` AS `usuario_id`, `u`.`Nombre_Apellidos` AS `usuario_nombre`, `u`.`file_name` AS `usuario_avatar`, `t`.`TipoUsuario` AS `usuario_tipo`, `s`.`Nombre_Sucursal` AS `Nombre_Sucursal`, `p`.`rol` AS `rol`, `p`.`fecha_union` AS `fecha_union`, `p`.`fecha_salida` AS `fecha_salida`, `p`.`ultima_lectura` AS `ultima_lectura`, `p`.`notificaciones` AS `notificaciones`, `p`.`silenciado` AS `silenciado`, `p`.`activo` AS `activo`, `p`.`configuracion_participante` AS `configuracion_participante`, `eu`.`estado` AS `estado_usuario`, `eu`.`ultima_actividad` AS `ultima_actividad`, (select count(0) from `chat_mensajes` `m` where `m`.`conversacion_id` = `p`.`conversacion_id` and `m`.`fecha_envio` > coalesce(`p`.`ultima_lectura`,'1900-01-01') and `m`.`usuario_id` <> `p`.`usuario_id` and `m`.`eliminado` = 0) AS `mensajes_no_leidos` FROM ((((`chat_participantes` `p` left join `Usuarios_PV` `u` on(`p`.`usuario_id` = `u`.`Id_PvUser`)) left join `Tipos_Usuarios` `t` on(`u`.`Fk_Usuario` = `t`.`ID_User`)) left join `Sucursales` `s` on(`u`.`Fk_Sucursal` = `s`.`ID_Sucursal`)) left join `chat_estados_usuario` `eu` on(`p`.`usuario_id` = `eu`.`usuario_id`)) WHERE `p`.`activo` = 1 ;

-- --------------------------------------------------------

--
-- Estructura para la vista `v_devoluciones_completas`
--
DROP TABLE IF EXISTS `v_devoluciones_completas`;

CREATE ALGORITHM=UNDEFINED DEFINER=`u858848268_devpezer0`@`127.0.0.1` SQL SECURITY DEFINER VIEW `v_devoluciones_completas`  AS SELECT `d`.`id` AS `id`, `d`.`folio` AS `folio`, `d`.`fecha` AS `fecha`, `d`.`estatus` AS `estatus`, `d`.`observaciones_generales` AS `observaciones_generales`, `d`.`total_productos` AS `total_productos`, `d`.`total_unidades` AS `total_unidades`, `d`.`valor_total` AS `valor_total`, coalesce(`s`.`Nombre_Sucursal`,concat('Sucursal ID: ',`d`.`sucursal_id`)) AS `sucursal_nombre`, coalesce(`u`.`Nombre_Apellidos`,concat('Usuario ID: ',`d`.`usuario_id`)) AS `usuario_nombre`, coalesce(`tu`.`TipoUsuario`,'Usuario') AS `usuario_tipo` FROM (((`Devoluciones` `d` left join `Sucursales` `s` on(`d`.`sucursal_id` = `s`.`ID_Sucursal`)) left join `Usuarios_PV` `u` on(`d`.`usuario_id` = `u`.`Id_PvUser`)) left join `Tipos_Usuarios` `tu` on(`u`.`Fk_Usuario` = `tu`.`ID_User`)) ;

-- --------------------------------------------------------

--
-- Estructura para la vista `v_devoluciones_detalle_completo`
--
DROP TABLE IF EXISTS `v_devoluciones_detalle_completo`;

CREATE ALGORITHM=UNDEFINED DEFINER=`u858848268_devpezer0`@`127.0.0.1` SQL SECURITY DEFINER VIEW `v_devoluciones_detalle_completo`  AS SELECT `dd`.`id` AS `id`, `dd`.`devolucion_id` AS `devolucion_id`, `d`.`folio` AS `folio`, `dd`.`producto_id` AS `producto_id`, `dd`.`codigo_barras` AS `codigo_barras`, `dd`.`nombre_producto` AS `nombre_producto`, `dd`.`cantidad` AS `cantidad`, `dd`.`tipo_devolucion` AS `tipo_devolucion`, coalesce(`td`.`nombre`,`dd`.`tipo_devolucion`) AS `tipo_nombre`, coalesce(`td`.`color`,'#6c757d') AS `tipo_color`, `dd`.`observaciones` AS `observaciones`, `dd`.`lote` AS `lote`, `dd`.`fecha_caducidad` AS `fecha_caducidad`, `dd`.`precio_venta` AS `precio_venta`, `dd`.`precio_costo` AS `precio_costo`, `dd`.`valor_total` AS `valor_total`, `dd`.`accion_tomada` AS `accion_tomada`, `dd`.`observaciones_accion` AS `observaciones_accion`, `dd`.`created_at` AS `created_at`, `d`.`fecha` AS `fecha_devolucion`, `d`.`estatus` AS `estatus_devolucion`, coalesce(`s`.`Nombre_Sucursal`,concat('Sucursal ID: ',`d`.`sucursal_id`)) AS `sucursal_nombre`, coalesce(`u`.`Nombre_Apellidos`,concat('Usuario ID: ',`d`.`usuario_id`)) AS `usuario_nombre` FROM ((((`Devoluciones_Detalle` `dd` left join `Devoluciones` `d` on(`dd`.`devolucion_id` = `d`.`id`)) left join `Tipos_Devolucion` `td` on(`dd`.`tipo_devolucion` = `td`.`codigo`)) left join `Sucursales` `s` on(`d`.`sucursal_id` = `s`.`ID_Sucursal`)) left join `Usuarios_PV` `u` on(`d`.`usuario_id` = `u`.`Id_PvUser`)) ;

-- --------------------------------------------------------

--
-- Estructura para la vista `v_estadisticas_asistencia`
--
DROP TABLE IF EXISTS `v_estadisticas_asistencia`;

CREATE ALGORITHM=UNDEFINED DEFINER=`u858848268_devpezer0`@`127.0.0.1` SQL SECURITY DEFINER VIEW `v_estadisticas_asistencia`  AS SELECT `a`.`usuario_id` AS `usuario_id`, `u`.`Nombre_Apellidos` AS `Nombre_Apellidos`, cast(`a`.`fecha_hora` as date) AS `fecha`, count(case when `a`.`tipo` = 'entrada' then 1 end) AS `entradas`, count(case when `a`.`tipo` = 'salida' then 1 end) AS `salidas`, count(0) AS `total_registros` FROM (`asistencias` `a` join `Usuarios_PV` `u` on(`a`.`usuario_id` = `u`.`Id_PvUser`)) GROUP BY `a`.`usuario_id`, cast(`a`.`fecha_hora` as date) ORDER BY cast(`a`.`fecha_hora` as date) DESC ;

-- --------------------------------------------------------

--
-- Estructura para la vista `v_estadisticas_devoluciones`
--
DROP TABLE IF EXISTS `v_estadisticas_devoluciones`;

CREATE ALGORITHM=UNDEFINED DEFINER=`u858848268_devpezer0`@`127.0.0.1` SQL SECURITY DEFINER VIEW `v_estadisticas_devoluciones`  AS SELECT cast(`d`.`fecha` as date) AS `fecha`, `d`.`sucursal_id` AS `sucursal_id`, `s`.`Nombre_Sucursal` AS `Nombre_Sucursal`, count(0) AS `total_devoluciones`, sum(`d`.`total_unidades`) AS `total_unidades_devueltas`, sum(`d`.`valor_total`) AS `valor_total_devuelto`, count(case when `d`.`estatus` = 'pendiente' then 1 end) AS `pendientes`, count(case when `d`.`estatus` = 'procesada' then 1 end) AS `procesadas`, count(case when `d`.`estatus` = 'cancelada' then 1 end) AS `canceladas` FROM (`Devoluciones` `d` left join `Sucursales` `s` on(`d`.`sucursal_id` = `s`.`ID_Sucursal`)) GROUP BY cast(`d`.`fecha` as date), `d`.`sucursal_id`, `s`.`Nombre_Sucursal` ORDER BY cast(`d`.`fecha` as date) DESC ;

-- --------------------------------------------------------

--
-- Estructura para la vista `v_productos_mas_devueltos`
--
DROP TABLE IF EXISTS `v_productos_mas_devueltos`;

CREATE ALGORITHM=UNDEFINED DEFINER=`u858848268_devpezer0`@`127.0.0.1` SQL SECURITY DEFINER VIEW `v_productos_mas_devueltos`  AS SELECT `dd`.`codigo_barras` AS `codigo_barras`, `dd`.`nombre_producto` AS `nombre_producto`, count(0) AS `total_devoluciones`, sum(`dd`.`cantidad`) AS `total_unidades_devueltas`, sum(`dd`.`valor_total`) AS `valor_total_devuelto`, `dd`.`tipo_devolucion` AS `tipo_devolucion`, `td`.`nombre` AS `tipo_nombre`, avg(`dd`.`cantidad`) AS `promedio_cantidad_por_devolucion` FROM ((`Devoluciones_Detalle` `dd` left join `Tipos_Devolucion` `td` on(`dd`.`tipo_devolucion` = `td`.`codigo`)) left join `Devoluciones` `d` on(`dd`.`devolucion_id` = `d`.`id`)) WHERE `d`.`estatus` = 'procesada' GROUP BY `dd`.`codigo_barras`, `dd`.`nombre_producto`, `dd`.`tipo_devolucion`, `td`.`nombre` ORDER BY count(0) DESC, sum(`dd`.`cantidad`) DESC ;

-- --------------------------------------------------------

--
-- Estructura para la vista `v_recordatorios_completos`
--
DROP TABLE IF EXISTS `v_recordatorios_completos`;

CREATE ALGORITHM=UNDEFINED DEFINER=`u858848268_devpezer0`@`127.0.0.1` SQL SECURITY DEFINER VIEW `v_recordatorios_completos`  AS SELECT `r`.`id_recordatorio` AS `id_recordatorio`, `r`.`titulo` AS `titulo`, `r`.`descripcion` AS `descripcion`, `r`.`mensaje_whatsapp` AS `mensaje_whatsapp`, `r`.`mensaje_notificacion` AS `mensaje_notificacion`, `r`.`fecha_programada` AS `fecha_programada`, `r`.`fecha_creacion` AS `fecha_creacion`, `r`.`prioridad` AS `prioridad`, `r`.`estado` AS `estado`, `r`.`tipo_envio` AS `tipo_envio`, `r`.`destinatarios` AS `destinatarios`, `r`.`sucursal_id` AS `sucursal_id`, `r`.`grupo_id` AS `grupo_id`, `r`.`intentos_envio` AS `intentos_envio`, `r`.`max_intentos` AS `max_intentos`, `r`.`fecha_ultimo_intento` AS `fecha_ultimo_intento`, `r`.`error_ultimo_intento` AS `error_ultimo_intento`, `u_creador`.`Nombre_Apellidos` AS `creador_nombre`, `u_modificador`.`Nombre_Apellidos` AS `modificador_nombre`, `s`.`Nombre_Sucursal` AS `sucursal_nombre`, `g`.`nombre_grupo` AS `grupo_nombre`, CASE WHEN `r`.`estado` = 'programado' AND `r`.`fecha_programada` > current_timestamp() THEN 'Pendiente' WHEN `r`.`estado` = 'programado' AND `r`.`fecha_programada` <= current_timestamp() THEN 'Listo para enviar' WHEN `r`.`estado` = 'enviando' THEN 'Enviando' WHEN `r`.`estado` = 'enviado' THEN 'Enviado' WHEN `r`.`estado` = 'cancelado' THEN 'Cancelado' WHEN `r`.`estado` = 'error' THEN 'Error' ELSE 'Desconocido' END AS `estado_descripcion` FROM ((((`recordatorios_sistema` `r` left join `Usuarios_PV` `u_creador` on(`r`.`usuario_creador` = `u_creador`.`Id_PvUser`)) left join `Usuarios_PV` `u_modificador` on(`r`.`usuario_modificador` = `u_modificador`.`Id_PvUser`)) left join `Sucursales` `s` on(`r`.`sucursal_id` = `s`.`ID_Sucursal`)) left join `recordatorios_grupos` `g` on(`r`.`grupo_id` = `g`.`id_grupo`)) ;

-- --------------------------------------------------------

--
-- Estructura para la vista `v_recordatorios_destinatarios_completos`
--
DROP TABLE IF EXISTS `v_recordatorios_destinatarios_completos`;

CREATE ALGORITHM=UNDEFINED DEFINER=`u858848268_devpezer0`@`127.0.0.1` SQL SECURITY DEFINER VIEW `v_recordatorios_destinatarios_completos`  AS SELECT `rd`.`id_destinatario` AS `id_destinatario`, `rd`.`recordatorio_id` AS `recordatorio_id`, `rd`.`usuario_id` AS `usuario_id`, `rd`.`telefono_whatsapp` AS `telefono_whatsapp`, `rd`.`estado_envio` AS `estado_envio`, `rd`.`fecha_envio` AS `fecha_envio`, `rd`.`error_envio` AS `error_envio`, `rd`.`tipo_envio` AS `tipo_envio`, `u`.`Nombre_Apellidos` AS `usuario_nombre`, `u`.`Correo_Electronico` AS `usuario_email`, `u`.`Telefono` AS `usuario_telefono`, `s`.`Nombre_Sucursal` AS `sucursal_nombre` FROM ((`recordatorios_destinatarios` `rd` left join `Usuarios_PV` `u` on(`rd`.`usuario_id` = `u`.`Id_PvUser`)) left join `Sucursales` `s` on(`u`.`Fk_Sucursal` = `s`.`ID_Sucursal`)) ;

-- --------------------------------------------------------

--
-- Estructura para la vista `v_resumen_mensual`
--
DROP TABLE IF EXISTS `v_resumen_mensual`;

CREATE ALGORITHM=UNDEFINED DEFINER=`u858848268_devpezer0`@`127.0.0.1` SQL SECURITY DEFINER VIEW `v_resumen_mensual`  AS SELECT `a`.`usuario_id` AS `usuario_id`, `u`.`Nombre_Apellidos` AS `Nombre_Apellidos`, year(`a`.`fecha_hora`) AS `año`, month(`a`.`fecha_hora`) AS `mes`, count(case when `a`.`tipo` = 'entrada' then 1 end) AS `total_entradas`, count(case when `a`.`tipo` = 'salida' then 1 end) AS `total_salidas`, count(0) AS `total_registros` FROM (`asistencias` `a` join `Usuarios_PV` `u` on(`a`.`usuario_id` = `u`.`Id_PvUser`)) GROUP BY `a`.`usuario_id`, year(`a`.`fecha_hora`), month(`a`.`fecha_hora`) ORDER BY year(`a`.`fecha_hora`) DESC, month(`a`.`fecha_hora`) DESC ;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `asistencias`
--
ALTER TABLE `asistencias`
  ADD CONSTRAINT `fk_asistencias_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `Usuarios_PV` (`Id_PvUser`) ON DELETE CASCADE;

--
-- Filtros para la tabla `caducados_historial`
--
ALTER TABLE `caducados_historial`
  ADD CONSTRAINT `fk_historial_lote` FOREIGN KEY (`id_lote`) REFERENCES `productos_lotes_caducidad` (`id_lote`) ON DELETE CASCADE;

--
-- Filtros para la tabla `caducados_notificaciones`
--
ALTER TABLE `caducados_notificaciones`
  ADD CONSTRAINT `fk_notificaciones_lote` FOREIGN KEY (`id_lote`) REFERENCES `productos_lotes_caducidad` (`id_lote`) ON DELETE CASCADE;

--
-- Filtros para la tabla `chat_configuraciones`
--
ALTER TABLE `chat_configuraciones`
  ADD CONSTRAINT `fk_chat_configuraciones_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `Usuarios_PV` (`Id_PvUser`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `chat_conversaciones`
--
ALTER TABLE `chat_conversaciones`
  ADD CONSTRAINT `fk_chat_conversaciones_sucursal` FOREIGN KEY (`sucursal_id`) REFERENCES `Sucursales` (`ID_Sucursal`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_chat_conversaciones_usuario` FOREIGN KEY (`creado_por`) REFERENCES `Usuarios_PV` (`Id_PvUser`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `chat_estados_usuario`
--
ALTER TABLE `chat_estados_usuario`
  ADD CONSTRAINT `fk_chat_estados_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `Usuarios_PV` (`Id_PvUser`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `chat_lecturas`
--
ALTER TABLE `chat_lecturas`
  ADD CONSTRAINT `fk_chat_lecturas_mensaje` FOREIGN KEY (`mensaje_id`) REFERENCES `chat_mensajes` (`id_mensaje`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_chat_lecturas_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `Usuarios_PV` (`Id_PvUser`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `chat_mensajes`
--
ALTER TABLE `chat_mensajes`
  ADD CONSTRAINT `fk_chat_mensajes_conversacion` FOREIGN KEY (`conversacion_id`) REFERENCES `chat_conversaciones` (`id_conversacion`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_chat_mensajes_eliminado_por` FOREIGN KEY (`eliminado_por`) REFERENCES `Usuarios_PV` (`Id_PvUser`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_chat_mensajes_respuesta` FOREIGN KEY (`mensaje_respuesta_id`) REFERENCES `chat_mensajes` (`id_mensaje`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_chat_mensajes_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `Usuarios_PV` (`Id_PvUser`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `chat_mensajes_eliminados`
--
ALTER TABLE `chat_mensajes_eliminados`
  ADD CONSTRAINT `fk_chat_eliminados_mensaje` FOREIGN KEY (`mensaje_id`) REFERENCES `chat_mensajes` (`id_mensaje`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_chat_eliminados_usuario` FOREIGN KEY (`usuario_elimino`) REFERENCES `Usuarios_PV` (`Id_PvUser`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `chat_participantes`
--
ALTER TABLE `chat_participantes`
  ADD CONSTRAINT `fk_chat_participantes_conversacion` FOREIGN KEY (`conversacion_id`) REFERENCES `chat_conversaciones` (`id_conversacion`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_chat_participantes_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `Usuarios_PV` (`Id_PvUser`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `chat_reacciones`
--
ALTER TABLE `chat_reacciones`
  ADD CONSTRAINT `fk_chat_reacciones_mensaje` FOREIGN KEY (`mensaje_id`) REFERENCES `chat_mensajes` (`id_mensaje`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_chat_reacciones_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `Usuarios_PV` (`Id_PvUser`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `configuracion_checador`
--
ALTER TABLE `configuracion_checador`
  ADD CONSTRAINT `fk_config_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `Usuarios_PV` (`Id_PvUser`) ON DELETE CASCADE;

--
-- Filtros para la tabla `Detalle_Limpieza`
--
ALTER TABLE `Detalle_Limpieza`
  ADD CONSTRAINT `Detalle_Limpieza_ibfk_1` FOREIGN KEY (`id_bitacora`) REFERENCES `Bitacora_Limpieza` (`id_bitacora`);

--
-- Filtros para la tabla `Devoluciones_Acciones`
--
ALTER TABLE `Devoluciones_Acciones`
  ADD CONSTRAINT `fk_detalle_accion` FOREIGN KEY (`detalle_id`) REFERENCES `Devoluciones_Detalle` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_devolucion_accion` FOREIGN KEY (`devolucion_id`) REFERENCES `Devoluciones` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `Devoluciones_Autorizaciones`
--
ALTER TABLE `Devoluciones_Autorizaciones`
  ADD CONSTRAINT `fk_devolucion_autorizacion` FOREIGN KEY (`devolucion_id`) REFERENCES `Devoluciones` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `Devoluciones_Detalle`
--
ALTER TABLE `Devoluciones_Detalle`
  ADD CONSTRAINT `fk_devolucion_detalle` FOREIGN KEY (`devolucion_id`) REFERENCES `Devoluciones` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `Inventario_Productos_Bloqueados`
--
ALTER TABLE `Inventario_Productos_Bloqueados`
  ADD CONSTRAINT `fk_bloqueo_turno` FOREIGN KEY (`ID_Turno`) REFERENCES `Inventario_Turnos` (`ID_Turno`) ON DELETE CASCADE;

--
-- Filtros para la tabla `Inventario_Turnos_Historial`
--
ALTER TABLE `Inventario_Turnos_Historial`
  ADD CONSTRAINT `fk_turno_historial` FOREIGN KEY (`ID_Turno`) REFERENCES `Inventario_Turnos` (`ID_Turno`) ON DELETE CASCADE;

--
-- Filtros para la tabla `Inventario_Turnos_Productos`
--
ALTER TABLE `Inventario_Turnos_Productos`
  ADD CONSTRAINT `fk_turno_producto` FOREIGN KEY (`ID_Turno`) REFERENCES `Inventario_Turnos` (`ID_Turno`) ON DELETE CASCADE;

--
-- Filtros para la tabla `logs_checador`
--
ALTER TABLE `logs_checador`
  ADD CONSTRAINT `fk_logs_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `Usuarios_PV` (`Id_PvUser`) ON DELETE CASCADE;

--
-- Filtros para la tabla `Municipios`
--
ALTER TABLE `Municipios`
  ADD CONSTRAINT `Municipios_ibfk_1` FOREIGN KEY (`Fk_Estado`) REFERENCES `Estados` (`ID_Estado`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `Notificaciones`
--
ALTER TABLE `Notificaciones`
  ADD CONSTRAINT `Notificaciones_ibfk_1` FOREIGN KEY (`SucursalID`) REFERENCES `Sucursales` (`ID_Sucursal`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `productos_lotes_caducidad`
--
ALTER TABLE `productos_lotes_caducidad`
  ADD CONSTRAINT `fk_lotes_stock` FOREIGN KEY (`folio_stock`) REFERENCES `Stock_POS` (`Folio_Prod_Stock`) ON DELETE CASCADE;

--
-- Filtros para la tabla `recordatorios_config_whatsapp`
--
ALTER TABLE `recordatorios_config_whatsapp`
  ADD CONSTRAINT `fk_recordatorios_config_whatsapp_usuario` FOREIGN KEY (`usuario_configurador`) REFERENCES `Usuarios_PV` (`Id_PvUser`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `recordatorios_destinatarios`
--
ALTER TABLE `recordatorios_destinatarios`
  ADD CONSTRAINT `fk_recordatorios_destinatarios_recordatorio` FOREIGN KEY (`recordatorio_id`) REFERENCES `recordatorios_sistema` (`id_recordatorio`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_recordatorios_destinatarios_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `Usuarios_PV` (`Id_PvUser`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `recordatorios_grupos`
--
ALTER TABLE `recordatorios_grupos`
  ADD CONSTRAINT `fk_recordatorios_grupos_usuario_creador` FOREIGN KEY (`usuario_creador`) REFERENCES `Usuarios_PV` (`Id_PvUser`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `recordatorios_grupos_miembros`
--
ALTER TABLE `recordatorios_grupos_miembros`
  ADD CONSTRAINT `fk_recordatorios_grupos_miembros_grupo` FOREIGN KEY (`grupo_id`) REFERENCES `recordatorios_grupos` (`id_grupo`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_recordatorios_grupos_miembros_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `Usuarios_PV` (`Id_PvUser`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `recordatorios_logs`
--
ALTER TABLE `recordatorios_logs`
  ADD CONSTRAINT `fk_recordatorios_logs_destinatario` FOREIGN KEY (`destinatario_id`) REFERENCES `recordatorios_destinatarios` (`id_destinatario`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_recordatorios_logs_recordatorio` FOREIGN KEY (`recordatorio_id`) REFERENCES `recordatorios_sistema` (`id_recordatorio`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `recordatorios_plantillas`
--
ALTER TABLE `recordatorios_plantillas`
  ADD CONSTRAINT `fk_recordatorios_plantillas_usuario_creador` FOREIGN KEY (`usuario_creador`) REFERENCES `Usuarios_PV` (`Id_PvUser`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `recordatorios_sistema`
--
ALTER TABLE `recordatorios_sistema`
  ADD CONSTRAINT `fk_recordatorios_usuario_creador` FOREIGN KEY (`usuario_creador`) REFERENCES `Usuarios_PV` (`Id_PvUser`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_recordatorios_usuario_modificador` FOREIGN KEY (`usuario_modificador`) REFERENCES `Usuarios_PV` (`Id_PvUser`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `Tareas`
--
ALTER TABLE `Tareas`
  ADD CONSTRAINT `fk_tareas_asignado` FOREIGN KEY (`asignado_a`) REFERENCES `Usuarios_PV` (`Id_PvUser`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_tareas_creador` FOREIGN KEY (`creado_por`) REFERENCES `Usuarios_PV` (`Id_PvUser`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `ubicaciones_trabajo`
--
ALTER TABLE `ubicaciones_trabajo`
  ADD CONSTRAINT `fk_ubicaciones_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `Usuarios_PV` (`Id_PvUser`) ON DELETE CASCADE;

--
-- Filtros para la tabla `Usuarios_PV`
--
ALTER TABLE `Usuarios_PV`
  ADD CONSTRAINT `Usuarios_PV_ibfk_1` FOREIGN KEY (`Fk_Usuario`) REFERENCES `Tipos_Usuarios` (`ID_User`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
