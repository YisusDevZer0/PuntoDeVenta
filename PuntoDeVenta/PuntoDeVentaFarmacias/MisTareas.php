<?php
include_once "Controladores/ControladorUsuario.php";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Mis Tareas - <?php echo $row['Licencia']?></title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <?php include "header.php"; ?>
</head>
<body>
    <?php include_once "Menu.php"; ?>
    <div class="content">
        <?php include "navbar.php"; ?>
        
        <div class="container-fluid">
            <h1 class="h3 mb-4 text-gray-800">Mis Tareas - <?php echo $row['Nombre_Apellidos']; ?></h1>
            
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5>Mis Tareas Asignadas</h5>
                        </div>
                        <div class="card-body">
                            <div class="text-center py-5">
                                <i class="fas fa-tasks fa-4x text-muted mb-3"></i>
                                <h4 class="text-muted">¡No tienes tareas asignadas!</h4>
                                <p class="text-muted">Cuando te asignen tareas, aparecerán aquí para que puedas gestionarlas.</p>
                                <div class="mt-4">
                                    <a href="crear_tabla_tareas.php" class="btn btn-success">
                                        <i class="fas fa-plus"></i> Crear Tabla de Tareas
                                    </a>
                                    <a href="debug_tareas.php" class="btn btn-info" target="_blank">
                                        <i class="fas fa-bug"></i> Verificar Sistema
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php 
    include "Modales/Modales_Errores.php";
    include "Modales/Modales_Referencias.php";
    include "Footer.php"; 
    ?>
</body>
</html>