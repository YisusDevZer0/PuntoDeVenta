<?php
// Verificación básica de sesión
session_start();

// Verificar sesión de manera simple
if(!isset($_SESSION['ControlMaestro']) && !isset($_SESSION['AdministradorRH']) && !isset($_SESSION['Marketing'])){
    header("Location: Expiro.php");
    exit();
}

// Variables básicas
$usuario_id = isset($_SESSION['ControlMaestro']) ? $_SESSION['ControlMaestro'] : 
            (isset($_SESSION['AdministradorRH']) ? $_SESSION['AdministradorRH'] : $_SESSION['Marketing']);

// Incluir controlador de usuario
include_once "Controladores/ControladorUsuario.php";

$sucursal_id = isset($row['Fk_Sucursal']) ? $row['Fk_Sucursal'] : 1;
$disabledAttr = '';
$currentPage = 'recordatorios';
$showDashboard = false;
$tipoUsuario = isset($row['TipoUsuario']) ? $row['TipoUsuario'] : 'Usuario';
$isAdmin = ($tipoUsuario == 'Administrador' || $tipoUsuario == 'MKT');

// Verificar tablas de manera más robusta
$tablas_existen = false;
$tablas_creadas = [];

try {
    if (isset($con) && $con) {
        // Verificar cada tabla individualmente
        $tablas_requeridas = [
            'recordatorios_sistema',
            'recordatorios_destinatarios', 
            'recordatorios_grupos',
            'recordatorios_logs'
        ];
        
        foreach ($tablas_requeridas as $tabla) {
            $resultado = $con->query("SHOW TABLES LIKE '$tabla'");
            if ($resultado && $resultado->num_rows > 0) {
                $tablas_creadas[] = $tabla;
            }
        }
        
        // Si al menos la tabla principal existe, consideramos que está instalado
        $tablas_existen = in_array('recordatorios_sistema', $tablas_creadas);
        
        // Debug: mostrar información adicional
        if (!$tablas_existen) {
            // Intentar verificar con información del esquema
            $schema_check = $con->query("SELECT COUNT(*) as count FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = 'recordatorios_sistema'");
            if ($schema_check) {
                $schema_result = $schema_check->fetch_assoc();
                if ($schema_result['count'] > 0) {
                    $tablas_existen = true;
                    $tablas_creadas[] = 'recordatorios_sistema (detectado por schema)';
                }
            }
        }
    }
} catch (Exception $e) {
    $tablas_existen = false;
    error_log("Error verificando tablas: " . $e->getMessage());
}

// Obtener sucursales
$sucursales = [];
try {
    if (isset($con) && $con) {
        $sucursales_query = "SELECT ID_Sucursal, Nombre_Sucursal FROM Sucursales WHERE Activo = 1 ORDER BY Nombre_Sucursal";
        $sucursales_result = $con->query($sucursales_query);
        if ($sucursales_result) {
            while ($row_sucursal = $sucursales_result->fetch_assoc()) {
                $sucursales[] = $row_sucursal;
            }
        }
    }
} catch (Exception $e) {
    // Error al obtener sucursales
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Sistema de Recordatorios - <?php echo isset($row['Licencia']) ? $row['Licencia'] : 'Doctor Pez'; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <?php include "header.php";?>
</head>
<body>
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
                                <i class="fa-solid fa-bell me-2"></i>
                                Sistema de Recordatorios
                            </h6>
                        </div>
                        
                        <div class="text-center py-5">
                            <?php if (!$tablas_existen): ?>
                                <i class="fa-solid fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                                <h4 class="text-warning">Instalación Requerida</h4>
                                <p class="text-muted">Las tablas de la base de datos para el sistema de recordatorios no están instaladas.</p>
                                <div class="mt-4">
                                    <a href="instalar_recordatorios.php" class="btn btn-primary btn-lg">
                                        <i class="fa-solid fa-download me-2"></i>
                                        Instalar Sistema
                                    </a>
                                </div>
                                <div class="mt-3">
                                    <small class="text-muted">
                                        Tablas encontradas: <?= implode(', ', $tablas_creadas) ?: 'Ninguna' ?>
                                    </small>
                                </div>
                            <?php else: ?>
                                <i class="fa-solid fa-check-circle fa-3x text-success mb-3"></i>
                                <h4 class="text-success">¡Sistema Instalado!</h4>
                                <p class="text-muted">El sistema de recordatorios está listo para usar.</p>
                                <div class="mt-3">
                                    <small class="text-muted">
                                        Tablas instaladas: <?= implode(', ', $tablas_creadas) ?>
                                    </small>
                                </div>
                                <div class="mt-4">
                                    <button class="btn btn-primary btn-lg" onclick="alert('Funcionalidad en desarrollo')">
                                        <i class="fa-solid fa-plus me-2"></i>
                                        Crear Recordatorio
                                    </button>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Content End -->

    <!-- Footer Start -->
    <?php include "footer.php";?>
    <!-- Footer End -->

    <!-- JavaScript Libraries -->
    <?php include "scripts.php";?>
    
    <script>
        // Ocultar spinner si existe
        if (document.getElementById('spinner')) {
            document.getElementById('spinner').style.display = 'none';
        }
    </script>
</body>
</html>