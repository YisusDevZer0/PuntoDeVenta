<?php
// Verificación básica de sesión
session_start();

// Verificar sesión de manera simple
if(!isset($_SESSION['ControlMaestro']) && !isset($_SESSION['AdministradorRH']) && !isset($_SESSION['Marketing'])){
    header("Location: Expiro.php");
    exit();
}

// Incluir controlador de usuario
include_once "Controladores/ControladorUsuario.php";

// Variables básicas
$usuario_id = isset($_SESSION['ControlMaestro']) ? $_SESSION['ControlMaestro'] : 
            (isset($_SESSION['AdministradorRH']) ? $_SESSION['AdministradorRH'] : $_SESSION['Marketing']);

$sucursal_id = $row['Fk_Sucursal'] ?? 1;
$disabledAttr = '';
$currentPage = 'recordatorios';
$showDashboard = false;
$tipoUsuario = isset($row['TipoUsuario']) ? $row['TipoUsuario'] : 'Usuario';
$isAdmin = ($tipoUsuario == 'Administrador' || $tipoUsuario == 'MKT');

$mensaje = '';
$tipo_mensaje = '';

// Procesar instalación
if ($_POST && isset($_POST['instalar'])) {
    try {
        // SQL básico para crear la tabla principal
        $sql = "CREATE TABLE IF NOT EXISTS `recordatorios_sistema` (
            `id_recordatorio` int(11) NOT NULL AUTO_INCREMENT,
            `titulo` varchar(255) NOT NULL,
            `descripcion` text,
            `fecha_programada` datetime NOT NULL,
            `prioridad` enum('baja','media','alta','urgente') DEFAULT 'media',
            `estado` enum('programado','enviando','enviado','cancelado','error') DEFAULT 'programado',
            `tipo_envio` enum('whatsapp','notificacion','ambos') DEFAULT 'ambos',
            `mensaje_whatsapp` text,
            `mensaje_notificacion` text,
            `destinatarios` enum('todos','sucursal','grupo','individual') DEFAULT 'todos',
            `sucursal_id` int(11) DEFAULT NULL,
            `grupo_id` int(11) DEFAULT NULL,
            `usuario_creador` int(11) NOT NULL,
            `fecha_creacion` timestamp DEFAULT CURRENT_TIMESTAMP,
            `fecha_actualizacion` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            `activo` tinyint(1) DEFAULT 1,
            PRIMARY KEY (`id_recordatorio`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
        
        if ($con->query($sql)) {
            $mensaje = "Instalación completada exitosamente. Tabla 'recordatorios_sistema' creada.";
            $tipo_mensaje = 'success';
        } else {
            $mensaje = "Error al crear la tabla: " . $con->error;
            $tipo_mensaje = 'danger';
        }
        
    } catch (Exception $e) {
        $mensaje = "Error durante la instalación: " . $e->getMessage();
        $tipo_mensaje = 'danger';
    }
}

// Verificar si la tabla existe
$tabla_existe = false;
try {
    if (isset($con) && $con) {
        $resultado = $con->query("SHOW TABLES LIKE 'recordatorios_sistema'");
        $tabla_existe = ($resultado && $resultado->num_rows > 0);
    }
} catch (Exception $e) {
    $tabla_existe = false;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Instalador de Recordatorios - <?php echo isset($row['Licencia']) ? $row['Licencia'] : 'Doctor Pez'; ?></title>
    <meta content="" name="keywords">
    <meta content="" name="description">
    
    <?php include "header.php";?>
</head>
<body>
    <!-- Spinner Start -->
    <div id="spinner" class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
        <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
            <span class="sr-only">Loading...</span>
        </div>
    </div>
    <!-- Spinner End -->

    <!-- Navbar Start -->
    <?php include "navbar.php";?>
    <!-- Navbar End -->

    <!-- Sidebar Start -->
    <?php include "Menu.php";?>
    <!-- Sidebar End -->

    <!-- Content Start -->
    <div class="content">
        <div class="container-fluid pt-4 px-4">
            <div class="row g-4">
                <div class="col-sm-12 col-xl-12">
                    <div class="bg-light rounded h-100 p-4">
                        <div class="d-flex align-items-center justify-content-between mb-4">
                            <h6 class="mb-0">
                                <i class="fa-solid fa-download me-2"></i>
                                Instalador del Sistema de Recordatorios
                            </h6>
                        </div>
                        
                        <?php if ($mensaje): ?>
                            <div class="alert alert-<?= $tipo_mensaje ?> alert-dismissible fade show" role="alert">
                                <i class="fa-solid fa-<?= $tipo_mensaje === 'success' ? 'check-circle' : 'times-circle' ?> me-2"></i>
                                <?= htmlspecialchars($mensaje) ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($tabla_existe): ?>
                            <div class="alert alert-success">
                                <i class="fa-solid fa-check-circle me-2"></i>
                                <strong>¡Sistema ya instalado!</strong> La tabla de recordatorios ya existe en la base de datos.
                                <div class="mt-3">
                                    <a href="RecordatoriosSistema.php" class="btn btn-success">
                                        <i class="fa-solid fa-arrow-right me-2"></i>
                                        Ir al Sistema de Recordatorios
                                    </a>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">
                                        <i class="fa-solid fa-database me-2"></i>
                                        Instalación de Base de Datos
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <p>Este instalador creará la tabla principal para el sistema de recordatorios.</p>
                                    
                                    <div class="alert alert-info">
                                        <i class="fa-solid fa-info-circle me-2"></i>
                                        <strong>Importante:</strong> Asegúrate de tener una copia de seguridad de tu base de datos antes de continuar.
                                    </div>
                                    
                                    <form method="POST" class="mt-4">
                                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                            <a href="RecordatoriosSistema.php" class="btn btn-secondary me-md-2">
                                                <i class="fa-solid fa-arrow-left me-2"></i>
                                                Cancelar
                                            </a>
                                            <button type="submit" name="instalar" class="btn btn-primary">
                                                <i class="fa-solid fa-download me-2"></i>
                                                Instalar Sistema
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Content End -->

    <!-- Footer Start -->
    <?php include "footer.php";?>
    <!-- Footer End -->

    <!-- Back to Top -->
    <a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top"><i class="bi bi-arrow-up"></i></a>
    </div>

    <!-- JavaScript Libraries -->
    <?php include "scripts.php";?>
    
    <script>
        $(document).ready(function() {
            $('#spinner').hide();
        });
    </script>
</body>
</html>