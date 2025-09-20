<?php
include_once "Controladores/ControladorUsuario.php";
include_once "Controladores/TareasController.php";

$tareasController = new TareasController($conn, $row['Id_PvUser'], $row['Fk_Sucursal']);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <title>Mis Tareas - <?php echo $row['Licencia']?> - <?php echo $row['Nombre_Sucursal']?></title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
   
    <style>
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
        
        /* Estilos para las tarjetas de estadísticas */
        .stats-card {
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            text-align: center;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            color: white;
        }
        .stats-card-primary {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
        }
        .stats-card-success {
            background: linear-gradient(135deg, #28a745 0%, #1e7e34 100%);
        }
        .stats-card-warning {
            background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%);
        }
        .stats-card-danger {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
        }
        .stats-card-info {
            background: linear-gradient(135deg, #17a2b8 0%, #117a8b 100%);
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
        
        /* Estilos para las etiquetas de prioridad */
        .badge-prioridad {
            font-size: 0.8rem;
            padding: 4px 8px;
        }
        .badge-alta {
            background-color: #dc3545;
            color: white;
        }
        .badge-media {
            background-color: #ffc107;
            color: #212529;
        }
        .badge-baja {
            background-color: #28a745;
            color: white;
        }
        
        /* Estilos para las etiquetas de estado */
        .badge-estado {
            font-size: 0.8rem;
            padding: 4px 8px;
        }
        .badge-por-hacer {
            background-color: #6c757d;
            color: white;
        }
        .badge-en-progreso {
            background-color: #17a2b8;
            color: white;
        }
        .badge-completada {
            background-color: #28a745;
            color: white;
        }
        .badge-cancelada {
            background-color: #dc3545;
            color: white;
        }
        
        /* Estilos para la tabla */
        #tablaTareas th {
            font-size: 14px;
            background-color: #ef7980 !important;
            color: white;
            padding: 8px;
        }
        #tablaTareas td {
            font-size: 13px;
            padding: 6px;
            color: #000;
        }
        .dataTables_wrapper .dataTables_paginate {
            text-align: center !important;
            margin-top: 10px !important;
        }
        .dataTables_paginate .paginate_button {
            padding: 5px 10px !important;
            border: 1px solid #ef7980 !important;
            margin: 2px !important;
            cursor: pointer !important;
            font-size: 16px !important;
            color: #ef7980 !important;
            background-color: #fff !important;
        }
        .dataTables_paginate .paginate_button.current {
            background-color: #ef7980 !important;
            color: #fff !important;
            border-color: #ef7980 !important;
        }
        .dataTables_paginate .paginate_button:hover {
            background-color: #C80096 !important;
            color: #fff !important;
            border-color: #C80096 !important;
        }
        
        /* Estilos para los filtros */
        .filtros-container {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        /* Estilos para los botones de acción */
        .btn-accion {
            margin: 2px;
            font-size: 0.8rem;
        }
        
        /* Estilos para tareas vencidas */
        .tarea-vencida {
            background-color: #fff3cd !important;
        }
        
        .tarea-vencida td {
            color: #856404 !important;
        }
    </style>

    <div id="loading-overlay">
        <div class="loader"></div>
        <div id="loading-text" style="color: white; margin-top: 10px; font-size: 18px;"></div>
    </div>
    <?php include "header.php"; ?>
</head>
<body>
    <!-- Spinner End -->
    <?php include_once "Menu.php" ?>

    <!-- Content Start -->
    <div class="content">
        <!-- Navbar Start -->
        <?php include "navbar.php"; ?>
        <!-- Navbar End -->
        
        <div class="container-fluid">
            <h1 class="h3 mb-4 text-gray-800">Mis Tareas - <?php echo $row['Nombre_Apellidos']; ?></h1>
            
            <!-- Estadísticas -->
            <div class="row mb-4">
                <?php
                $estadisticas = $tareasController->getEstadisticas();
                $stats = [];
                while ($row_stats = $estadisticas->fetch_assoc()) {
                    $stats[$row_stats['estado']] = $row_stats['cantidad'];
                }
                ?>
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="stats-card stats-card-primary">
                        <div class="stats-icon">
                            <i class="fas fa-tasks"></i>
                        </div>
                        <div class="stats-number"><?php echo isset($stats['Por hacer']) ? $stats['Por hacer'] : 0; ?></div>
                        <div class="stats-label">Por Hacer</div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="stats-card stats-card-warning">
                        <div class="stats-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="stats-number"><?php echo isset($stats['En progreso']) ? $stats['En progreso'] : 0; ?></div>
                        <div class="stats-label">En Progreso</div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="stats-card stats-card-success">
                        <div class="stats-icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="stats-number"><?php echo isset($stats['Completada']) ? $stats['Completada'] : 0; ?></div>
                        <div class="stats-label">Completadas</div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="stats-card stats-card-info">
                        <div class="stats-icon">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <div class="stats-number"><?php echo $tareasController->getTareasProximasVencer()->num_rows; ?></div>
                        <div class="stats-label">Próximas a Vencer</div>
                    </div>
                </div>
            </div>
            
            <!-- Filtros -->
            <div class="filtros-container">
                <div class="row">
                    <div class="col-md-3">
                        <label for="filtroEstado">Estado:</label>
                        <select id="filtroEstado" class="form-control">
                            <option value="">Todos</option>
                            <option value="Por hacer">Por hacer</option>
                            <option value="En progreso">En progreso</option>
                            <option value="Completada">Completada</option>
                            <option value="Cancelada">Cancelada</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="filtroPrioridad">Prioridad:</label>
                        <select id="filtroPrioridad" class="form-control">
                            <option value="">Todas</option>
                            <option value="Alta">Alta</option>
                            <option value="Media">Media</option>
                            <option value="Baja">Baja</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label>&nbsp;</label>
                        <div>
                            <button type="button" class="btn btn-primary" onclick="aplicarFiltros()">
                                <i class="fas fa-filter"></i> Aplicar Filtros
                            </button>
                            <button type="button" class="btn btn-secondary" onclick="limpiarFiltros()">
                                <i class="fas fa-times"></i> Limpiar
                            </button>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label>&nbsp;</label>
                        <div>
                            <button type="button" class="btn btn-info" onclick="exportarTareas()">
                                <i class="fas fa-download"></i> Exportar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Tabla de tareas -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Mis Tareas Asignadas</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="tablaTareas" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Título</th>
                                    <th>Descripción</th>
                                    <th>Prioridad</th>
                                    <th>Fecha Límite</th>
                                    <th>Estado</th>
                                    <th>Creado por</th>
                                    <th>Fecha Creación</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
         
    <!-- Footer Start -->
    <?php 
    include "Modales/Modales_Errores.php";
    include "Modales/Modales_Referencias.php";
    include "Footer.php"; ?>
    
    <div id="loading-overlay">
        <div class="loader"></div>
        <div id="loading-text" style="color: white; margin-top: 10px; font-size: 18px;"></div>
    </div>
    
    <script>
        var tablaTareas;
        var filtrosActuales = {};
        
        $(document).ready(function() {
            inicializarTabla();
            cargarTareas();
        });
        
        function inicializarTabla() {
            tablaTareas = $('#tablaTareas').DataTable({
                "processing": true,
                "serverSide": false,
                "ajax": {
                    "url": "Controladores/ArrayTareas.php",
                    "type": "POST",
                    "data": function(d) {
                        return $.extend(d, filtrosActuales);
                    },
                    "dataSrc": "data"
                },
                "columns": [
                    {"data": "id"},
                    {"data": "titulo"},
                    {"data": "descripcion"},
                    {
                        "data": "prioridad",
                        "render": function(data) {
                            var clase = data.toLowerCase();
                            return '<span class="badge badge-prioridad badge-' + clase + '">' + data + '</span>';
                        }
                    },
                    {"data": "fecha_limite"},
                    {
                        "data": "estado",
                        "render": function(data) {
                            var clase = data.toLowerCase().replace(' ', '-');
                            return '<span class="badge badge-estado badge-' + clase + '">' + data + '</span>';
                        }
                    },
                    {"data": "creador_nombre"},
                    {"data": "fecha_creacion"},
                    {
                        "data": null,
                        "render": function(data, type, row) {
                            var botones = '';
                            
                            // Botón para ver detalles
                            botones += '<button class="btn btn-sm btn-info btn-accion" onclick="verDetalles(' + row.id + ')" title="Ver detalles"><i class="fas fa-eye"></i></button>';
                            
                            // Botones según el estado
                            if (row.estado === 'Por hacer') {
                                botones += '<button class="btn btn-sm btn-warning btn-accion" onclick="cambiarEstado(' + row.id + ', \'En progreso\')" title="Marcar en progreso"><i class="fas fa-play"></i></button>';
                                botones += '<button class="btn btn-sm btn-success btn-accion" onclick="cambiarEstado(' + row.id + ', \'Completada\')" title="Marcar como completada"><i class="fas fa-check"></i></button>';
                            } else if (row.estado === 'En progreso') {
                                botones += '<button class="btn btn-sm btn-success btn-accion" onclick="cambiarEstado(' + row.id + ', \'Completada\')" title="Marcar como completada"><i class="fas fa-check"></i></button>';
                                botones += '<button class="btn btn-sm btn-secondary btn-accion" onclick="cambiarEstado(' + row.id + ', \'Por hacer\')" title="Volver a por hacer"><i class="fas fa-undo"></i></button>';
                            } else if (row.estado === 'Completada') {
                                botones += '<button class="btn btn-sm btn-warning btn-accion" onclick="cambiarEstado(' + row.id + ', \'En progreso\')" title="Marcar en progreso"><i class="fas fa-play"></i></button>';
                            }
                            
                            return botones;
                        }
                    }
                ],
                "order": [[3, "asc"], [4, "asc"]],
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json"
                },
                "createdRow": function(row, data, dataIndex) {
                    // Marcar tareas vencidas
                    if (data.fecha_limite && new Date(data.fecha_limite) < new Date()) {
                        $(row).addClass('tarea-vencida');
                    }
                }
            });
        }
        
        function cargarTareas() {
            mostrarLoading("Cargando tareas...");
            tablaTareas.ajax.reload(function() {
                ocultarLoading();
            });
        }
        
        function aplicarFiltros() {
            filtrosActuales = {
                estado: $('#filtroEstado').val(),
                prioridad: $('#filtroPrioridad').val()
            };
            cargarTareas();
        }
        
        function limpiarFiltros() {
            $('#filtroEstado').val('');
            $('#filtroPrioridad').val('');
            filtrosActuales = {};
            cargarTareas();
        }
        
        function verDetalles(id) {
            mostrarLoading("Cargando detalles de la tarea...");
            $.ajax({
                url: 'Controladores/ArrayTareas.php',
                type: 'POST',
                data: {accion: 'obtener', id: id},
                success: function(response) {
                    ocultarLoading();
                    if (response.success) {
                        var tarea = response.data;
                        var detalles = `
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">Detalles de la Tarea #${tarea.id}</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <strong>Título:</strong><br>
                                            ${tarea.titulo}
                                        </div>
                                        <div class="col-md-6">
                                            <strong>Prioridad:</strong><br>
                                            <span class="badge badge-prioridad badge-${tarea.prioridad.toLowerCase()}">${tarea.prioridad}</span>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <strong>Estado:</strong><br>
                                            <span class="badge badge-estado badge-${tarea.estado.toLowerCase().replace(' ', '-')}">${tarea.estado}</span>
                                        </div>
                                        <div class="col-md-6">
                                            <strong>Fecha Límite:</strong><br>
                                            ${tarea.fecha_limite || 'Sin fecha límite'}
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-12">
                                            <strong>Descripción:</strong><br>
                                            ${tarea.descripcion || 'Sin descripción'}
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <strong>Creado por:</strong><br>
                                            ${tarea.creador_nombre}
                                        </div>
                                        <div class="col-md-6">
                                            <strong>Fecha de creación:</strong><br>
                                            ${tarea.fecha_creacion}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                        
                        Swal.fire({
                            title: 'Detalles de la Tarea',
                            html: detalles,
                            width: '800px',
                            showCloseButton: true,
                            showConfirmButton: false
                        });
                    } else {
                        Swal.fire('Error', response.message, 'error');
                    }
                },
                error: function() {
                    ocultarLoading();
                    Swal.fire('Error', 'Error al cargar los detalles de la tarea', 'error');
                }
            });
        }
        
        function cambiarEstado(id, nuevoEstado) {
            var mensaje = '';
            switch(nuevoEstado) {
                case 'En progreso':
                    mensaje = '¿Deseas marcar esta tarea como "En progreso"?';
                    break;
                case 'Completada':
                    mensaje = '¿Deseas marcar esta tarea como "Completada"?';
                    break;
                case 'Por hacer':
                    mensaje = '¿Deseas volver a marcar esta tarea como "Por hacer"?';
                    break;
                default:
                    mensaje = '¿Deseas cambiar el estado de esta tarea?';
            }
            
            Swal.fire({
                title: '¿Confirmar cambio?',
                text: mensaje,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Sí, cambiar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    mostrarLoading("Cambiando estado...");
                    $.ajax({
                        url: 'Controladores/ArrayTareas.php',
                        type: 'POST',
                        data: {
                            accion: 'cambiar_estado',
                            id: id,
                            estado: nuevoEstado
                        },
                        success: function(response) {
                            ocultarLoading();
                            if (response.success) {
                                Swal.fire('Éxito', response.message, 'success');
                                cargarTareas();
                                // Recargar la página para actualizar las estadísticas
                                setTimeout(function() {
                                    location.reload();
                                }, 1500);
                            } else {
                                Swal.fire('Error', response.message, 'error');
                            }
                        },
                        error: function() {
                            ocultarLoading();
                            Swal.fire('Error', 'Error al cambiar el estado', 'error');
                        }
                    });
                }
            });
        }
        
        function exportarTareas() {
            mostrarLoading("Exportando tareas...");
            var filtros = JSON.stringify(filtrosActuales);
            window.location.href = 'Controladores/exportar_tareas.php?filtros=' + encodeURIComponent(filtros);
            setTimeout(ocultarLoading, 2000);
        }
        
        function mostrarLoading(mensaje) {
            $('#loading-text').text(mensaje);
            $('#loading-overlay').show();
        }
        
        function ocultarLoading() {
            $('#loading-overlay').hide();
        }
    </script>
</body>

</html>