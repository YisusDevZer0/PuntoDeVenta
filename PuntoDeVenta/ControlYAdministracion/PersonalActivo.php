
<?php
include_once "Controladores/ControladorUsuario.php";

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <title>Personal Vigente <?php echo $row['Licencia']?></title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
   
    <?php include "header.php";?>
    
    <style>
        /* Estilos para que la tabla tenga los mismos colores que las demás */
        #tablaPersonal {
            width: 100%;
            border-collapse: collapse;
        }
        #tablaPersonal thead th {
            background-color: #ef7980 !important;
            color: white !important;
            font-weight: bold;
            padding: 12px 8px;
            text-align: center;
            border: 1px solid #ddd;
        }
        #tablaPersonal tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        #tablaPersonal tbody tr:hover {
            background-color: #ffe6e7 !important;
        }
        #tablaPersonal tbody td {
            padding: 8px;
            border: 1px solid #ddd;
            text-align: center;
        }
        /* Estilos para los botones de paginación */
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            background: #ef7980 !important;
            color: white !important;
            border: 1px solid #ef7980 !important;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: #d65a62 !important;
            color: white !important;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background: #d65a62 !important;
            color: white !important;
        }
        /* Estilos para el loading */
        #loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.7);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            display: none;
        }
        .loader {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #ef7980;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        /* Estilos para las estadísticas con colores por importancia */
        .stats-card-primary {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            color: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            text-align: center;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .stats-card-success {
            background: linear-gradient(135deg, #28a745 0%, #1e7e34 100%);
            color: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            text-align: center;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .stats-card-info {
            background: linear-gradient(135deg, #17a2b8 0%, #117a8b 100%);
            color: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            text-align: center;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .stats-card-warning {
            background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%);
            color: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            text-align: center;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .stats-number {
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .stats-label {
            font-size: 0.9rem;
            opacity: 0.9;
        }
        .stats-icon {
            font-size: 2.5rem;
            margin-bottom: 10px;
            opacity: 0.8;
        }
        /* Estilos para las imágenes de perfil */
        .profile-img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #ef7980;
        }
    </style>
</head>

<body>
    <div id="loading-overlay">
        <div class="loader"></div>
        <div id="loading-text" style="color: white; margin-top: 10px; font-size: 18px;"></div>
    </div>
    
    <?php include_once "Menu.php" ?>

    <!-- Content Start -->
    <div class="content">
        <!-- Navbar Start -->
        <?php include "navbar.php";?>
        <!-- Navbar End -->

        <!-- Table Start -->
        <div class="container-fluid pt-4 px-4">
            <div class="col-12">
                <div class="bg-light rounded h-100 p-4">
                    <h6 class="mb-4" style="color:#0172b6;">
                        <i class="fas fa-users me-2"></i>
                        Personal Activo <?php echo $row['Licencia']?>
                    </h6>
                    
                    <!-- Filtros -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <label for="tipo_usuario" class="form-label">
                                <i class="fas fa-user-tag me-1"></i> Tipo de Usuario
                            </label>
                            <select id="tipo_usuario" class="form-control">
                                <option value="">Todos los tipos</option>
                                <?php
                                include_once "db_connect.php";
                                $sql_tipos = "SELECT DISTINCT TipoUsuario FROM Tipos_Usuarios ORDER BY TipoUsuario";
                                $result_tipos = $conn->query($sql_tipos);
                                if ($result_tipos && $result_tipos->num_rows > 0) {
                                    while ($tipo = $result_tipos->fetch_assoc()) {
                                        echo "<option value='" . htmlspecialchars($tipo['TipoUsuario']) . "'>" . htmlspecialchars($tipo['TipoUsuario']) . "</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="sucursal" class="form-label">
                                <i class="fas fa-store me-1"></i> Sucursal
                            </label>
                            <select id="sucursal" class="form-control">
                                <option value="">Todas las sucursales</option>
                                <?php
                                $sql_sucursales = "SELECT ID_Sucursal, Nombre_Sucursal FROM Sucursales WHERE Sucursal_Activa = 'Si' ORDER BY Nombre_Sucursal";
                                $result_sucursales = $conn->query($sql_sucursales);
                                if ($result_sucursales && $result_sucursales->num_rows > 0) {
                                    while ($sucursal = $result_sucursales->fetch_assoc()) {
                                        echo "<option value='" . $sucursal['ID_Sucursal'] . "'>" . htmlspecialchars($sucursal['Nombre_Sucursal']) . "</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="estatus" class="form-label">
                                <i class="fas fa-toggle-on me-1"></i> Estatus
                            </label>
                            <select id="estatus" class="form-control">
                                <option value="">Todos los estatus</option>
                                <option value="Activo">Activo</option>
                                <option value="Inactivo">Inactivo</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-primary" onclick="filtrarDatos()">
                                    <i class="fas fa-search me-1"></i> Filtrar
                                </button>
                                <button type="button" class="btn btn-success" onclick="exportarExcel()">
                                    <i class="fas fa-file-excel me-1"></i> Exportar a Excel
                                </button>
                                <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#myModal">
                                    <i class="fas fa-plus me-1"></i> Agregar Personal
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Cards de estadísticas -->
                    <div class="row mb-4" id="statsRow">
                        <div class="col-md-3">
                            <div class="stats-card-primary">
                                <div class="stats-icon">
                                    <i class="fas fa-users"></i>
                                </div>
                                <div class="stats-number" id="totalPersonal">0</div>
                                <div class="stats-label">Total Personal</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stats-card-success">
                                <div class="stats-icon">
                                    <i class="fas fa-user-check"></i>
                                </div>
                                <div class="stats-number" id="personalActivo">0</div>
                                <div class="stats-label">Personal Activo</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stats-card-info">
                                <div class="stats-icon">
                                    <i class="fas fa-store"></i>
                                </div>
                                <div class="stats-number" id="sucursalesActivas">0</div>
                                <div class="stats-label">Sucursales Activas</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stats-card-warning">
                                <div class="stats-icon">
                                    <i class="fas fa-user-tag"></i>
                                </div>
                                <div class="stats-number" id="tiposUsuario">0</div>
                                <div class="stats-label">Tipos de Usuario</div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Tabla de datos -->
                    <div class="table-responsive">
                        <table id="tablaPersonal" class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>ID Empleado</th>
                                    <th>Fotografía</th>
                                    <th>Nombre Completo</th>
                                    <th>Tipo de Usuario</th>
                                    <th>Sucursal</th>
                                    <th>Correo Electrónico</th>
                                    <th>Teléfono</th>
                                    <th>Fecha de Nacimiento</th>
                                    <th>Fecha de Creación</th>
                                    <th>Estatus</th>
                                    <th>Creado Por</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Los datos se cargarán dinámicamente -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="js/ControlDeUsuarios.js"></script>
    <script src="js/AgregarUsuarioss.js"></script>
    <script src="js/PersonalActivo.js"></script>

    <!-- Footer Start -->
    <?php 
    include "Modales/AltaNuevoUsuario.php";
    include "Modales/Modales_Referencias.php";
    include "Footer.php";?>

    <script>
        $(document).ready(function() {
            // Delegación de eventos para el botón "btn-edita" dentro de .dropdown-menu
            $(document).on("click", ".btn-edita", function() {
                console.log("Botón de editar clickeado para el ID:", id);
                var id = $(this).data("id");
                $.post("https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/Modales/EditaDatosDeUsuario.php", { id: id }, function(data) {
                    $("#FormCajas").html(data);
                    $("#TitulosCajas").html("Editar datos del usuario");
                    // Cambiar tamaño del modal agregando la clase 'modal-xl'
                    $("#ModalEdDele .modal-dialog").removeClass("modal-sm modal-lg modal-xl").addClass("modal-lg");
                });
                $('#ModalEdDele').modal('show');
            });

            // Delegación de eventos para el botón "btn-elimina" dentro de .dropdown-menu
            $(document).on("click", ".btn-elimina", function() {
                var id = $(this).data("id");
                $.post("https://doctorpez.mx/PuntoDeVenta/ControlYAdministracion/Modales/EliminarDatosDeUsuario.php", { id: id }, function(data) {
                    $("#FormCajas").html(data);
                    $("#TitulosCajas").html("Eliminar usuario");
                });
                $('#ModalEdDele').modal('show');
            });
        });
    </script>

    <div class="modal fade" id="ModalEdDele" tabindex="-1" role="dialog" style="overflow-y: scroll;" aria-labelledby="ModalEdDeleLabel" aria-hidden="true">
        <div id="CajasDi" class="modal-dialog modal-notify modal-success">
            <div class="text-center">
                <div class="modal-content">
                    <div class="modal-header" style="background-color: #ef7980 !important;">
                        <p class="heading lead" id="TitulosCajas" style="color:white;"></p>
                    </div>
                    <div class="modal-body">
                        <div class="text-center">
                            <div id="FormCajas"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>