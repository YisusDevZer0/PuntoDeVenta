<?php
include_once "dbconect.php";

// Verificar si el usuario es administrador
if (!isset($row) || $row['TipoUsuario'] != 'Administrador') {
    die("Acceso denegado. Solo administradores pueden ejecutar esta instalación.");
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instalación del Módulo de Caducados</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0"><i class="fa fa-database me-2"></i>Instalación del Módulo de Caducados</h4>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <h6><i class="fa fa-info-circle me-2"></i>Información</h6>
                            <p>Este script creará las tablas necesarias para el módulo de control de caducados.</p>
                        </div>
                        
                        <div id="resultado-instalacion">
                            <!-- Los resultados se mostrarán aquí -->
                        </div>
                        
                        <div class="text-center mt-4">
                            <button class="btn btn-primary btn-lg" onclick="instalarModulo()">
                                <i class="fa fa-play me-2"></i>Instalar Módulo
                            </button>
                            <a href="Caducados.php" class="btn btn-secondary btn-lg ms-2">
                                <i class="fa fa-arrow-left me-2"></i>Volver al Módulo
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    function instalarModulo() {
        Swal.fire({
            title: 'Instalando módulo...',
            text: 'Creando tablas de base de datos',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        $.post('api/instalar_caducados.php', {}, function(data) {
            Swal.close();
            
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Instalación exitosa',
                    text: data.message,
                    confirmButtonText: 'Continuar'
                }).then(() => {
                    window.location.href = 'Caducados.php';
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error en la instalación',
                    text: data.error,
                    confirmButtonText: 'Reintentar'
                });
            }
        }).fail(function() {
            Swal.close();
            Swal.fire({
                icon: 'error',
                title: 'Error de conexión',
                text: 'No se pudo conectar con el servidor',
                confirmButtonText: 'Reintentar'
            });
        });
    }
    </script>
</body>
</html>
