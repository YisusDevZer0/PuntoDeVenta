-- =============================================
-- ACTUALIZAR TABLA DE NOTIFICACIONES PARA CHAT
-- =============================================

-- Agregar campos necesarios para el chat
ALTER TABLE `Notificaciones` 
ADD COLUMN IF NOT EXISTS `UsuarioID` int(11) DEFAULT NULL AFTER `SucursalID`,
ADD COLUMN IF NOT EXISTS `ConversacionID` int(11) DEFAULT NULL AFTER `UsuarioID`,
ADD COLUMN IF NOT EXISTS `TipoNotificacion` varchar(50) DEFAULT NULL AFTER `ConversacionID`;

-- Agregar índices para mejor rendimiento
CREATE INDEX IF NOT EXISTS `idx_notificaciones_usuario` ON `Notificaciones` (`UsuarioID`, `Leido`);
CREATE INDEX IF NOT EXISTS `idx_notificaciones_conversacion` ON `Notificaciones` (`ConversacionID`);
CREATE INDEX IF NOT EXISTS `idx_notificaciones_tipo` ON `Notificaciones` (`Tipo`);

-- Actualizar notificaciones existentes si no tienen UsuarioID
UPDATE `Notificaciones` 
SET `UsuarioID` = (
    SELECT u.Id_PvUser 
    FROM Usuarios_PV u 
    WHERE u.Fk_Sucursal = Notificaciones.SucursalID 
    LIMIT 1
) 
WHERE `UsuarioID` IS NULL;
