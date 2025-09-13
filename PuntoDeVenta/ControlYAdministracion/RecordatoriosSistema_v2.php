<?php
// Versión ultra simple sin dependencias complejas
session_start();

// Verificar sesión
if(!isset($_SESSION['ControlMaestro']) && !isset($_SESSION['AdministradorRH']) && !isset($_SESSION['Marketing'])){
    echo "Error: No hay sesión válida";
    exit();
}

$usuario_id = isset($_SESSION['ControlMaestro']) ? $_SESSION['ControlMaestro'] : 
            (isset($_SESSION['AdministradorRH']) ? $_SESSION['AdministradorRH'] : $_SESSION['Marketing']);

// Incluir solo lo esencial
include_once "Consultas/db_connect.php";

// Verificar si la tabla existe
$tablas_existen = false;
if (isset($con) && $con) {
    $resultado = $con->query("SHOW TABLES LIKE 'recordatorios_sistema'");
    if ($resultado) {
        $tablas_existen = ($resultado->num_rows > 0);
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Sistema de Recordatorios</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fa-solid fa-bell me-2"></i>
                            Sistema de Recordatorios
                        </h5>
                    </div>
                    <div class="card-body text-center py-5">
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
                        <?php else: ?>
                            <i class="fa-solid fa-check-circle fa-3x text-success mb-3"></i>
                            <h4 class="text-success">¡Sistema Instalado!</h4>
                            <p class="text-muted">El sistema de recordatorios está listo para usar.</p>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
