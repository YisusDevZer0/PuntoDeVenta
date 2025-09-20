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
        .table th {
            font-size: 14px;
            background-color: #ef7980 !important;
            color: white;
            padding: 8px;
        }
        .table td {
            font-size: 13px;
            padding: 6px;
            color: #000;
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
        
        /* Estilos para el mensaje de sin tareas */
        .sin-tareas {
            text-align: center;
            padding: 40px;
            background-color: #f8f9fa;
            border-radius: 8px;
            margin: 20px 0;
        }
        
        .sin-tareas i {
            font-size: 4rem;
            color: #6c757d;
            margin-bottom: 20px;
        }
    </style>

    <?php include "header.php"; ?>
</head>
<body>
    <?php include_once "Menu.php"; ?>
    <div class="content">
        <?php include "navbar.php"; ?>
        
        <div class="container-fluid">
            <h1 class="h3 mb-4 text-gray-800">Mis Tareas - <?php echo $row['Nombre_Apellidos']; ?></h1>
            
            <?php
            // Obtener estadísticas
            $estadisticas = $tareasController->getEstadisticas();
            $stats = [];
            while ($row_stats = $estadisticas->fetch_assoc()) {
                $stats[$row_stats['estado']] = $row_stats['cantidad'];
            }
            
            // Obtener tareas del usuario
            $tareas = $tareasController->getTareasAsignadas();
            $tareas_array = [];
            while ($tarea = $tareas->fetch_assoc()) {
                $tareas_array[] = $tarea;
            }
            ?>
            
            <!-- Estadísticas -->
            <div class="row mb-4">
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
                        <select id="filtroEstado" class="form-control" onchange="filtrarTareas()">
                            <option value="">Todos</option>
                            <option value="Por hacer">Por hacer</option>
                            <option value="En progreso">En progreso</option>
                            <option value="Completada">Completada</option>
                            <option value="Cancelada">Cancelada</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="filtroPrioridad">Prioridad:</label>
                        <select id="filtroPrioridad" class="form-control" onchange="filtrarTareas()">
                            <option value="">Todas</option>
                            <option value="Alta">Alta</option>
                            <option value="Media">Media</option>
                            <option value="Baja">Baja</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label>&nbsp;</label>
                        <div>
                            <button type="button" class="btn btn-secondary" onclick="limpiarFiltros()">
                                <i class="fas fa-times"></i> Limpiar Filtros
                            </button>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label>&nbsp;</label>
                        <div>
                            <button type="button" class="btn btn-primary" onclick="location.reload()">
                                <i class="fas fa-refresh"></i> Actualizar
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
                    <?php if (empty($tareas_array)): ?>
                        <!-- Mensaje cuando no hay tareas -->
                        <div class="sin-tareas">
                            <i class="fas fa-tasks"></i>
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
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-bordered" id="tablaTareas">
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
                                    <?php foreach ($tareas_array as $tarea): ?>
                                        <tr class="tarea-row" 
                                            data-estado="<?php echo $tarea['estado']; ?>" 
                                            data-prioridad="<?php echo $tarea['prioridad']; ?>"
                                            <?php if ($tarea['fecha_limite'] && strtotime($tarea['fecha_limite']) < time()): ?>class="tarea-vencida"<?php endif; ?>>
                                            <td><?php echo $tarea['id']; ?></td>
                                            <td><?php echo htmlspecialchars($tarea['titulo']); ?></td>
                                            <td><?php echo htmlspecialchars($tarea['descripcion']); ?></td>
                                            <td>
                                                <span class="badge badge-prioridad badge-<?php echo strtolower($tarea['prioridad']); ?>">
                                                    <?php echo $tarea['prioridad']; ?>
                                                </span>
                                            </td>
                                            <td><?php echo $tarea['fecha_limite'] ?: 'Sin fecha límite'; ?></td>
                                            <td>
                                                <span class="badge badge-estado badge-<?php echo strtolower(str_replace(' ', '-', $tarea['estado'])); ?>">
                                                    <?php echo $tarea['estado']; ?>
                                                </span>
                                            </td>
                                            <td><?php echo htmlspecialchars($tarea['creador_nombre']); ?></td>
                                            <td><?php echo date('d/m/Y H:i', strtotime($tarea['fecha_creacion'])); ?></td>
                                            <td>
                                                <button class="btn btn-sm btn-info btn-accion" onclick="verDetalles(<?php echo $tarea['id']; ?>)" title="Ver detalles">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                
                                                <?php if ($tarea['estado'] === 'Por hacer'): ?>
                                                    <button class="btn btn-sm btn-warning btn-accion" onclick="cambiarEstado(<?php echo $tarea['id']; ?>, 'En progreso')" title="Marcar en progreso">
                                                        <i class="fas fa-play"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-success btn-accion" onclick="cambiarEstado(<?php echo $tarea['id']; ?>, 'Completada')" title="Marcar como completada">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                <?php elseif ($tarea['estado'] === 'En progreso'): ?>
                                                    <button class="btn btn-sm btn-success btn-accion" onclick="cambiarEstado(<?php echo $tarea['id']; ?>, 'Completada')" title="Marcar como completada">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-secondary btn-accion" onclick="cambiarEstado(<?php echo $tarea['id']; ?>, 'Por hacer')" title="Volver a por hacer">
                                                        <i class="fas fa-undo"></i>
                                                    </button>
                                                <?php elseif ($tarea['estado'] === 'Completada'): ?>
                                                    <button class="btn btn-sm btn-warning btn-accion" onclick="cambiarEstado(<?php echo $tarea['id']; ?>, 'En progreso')" title="Marcar en progreso">
                                                        <i class="fas fa-play"></i>
                                                    </button>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
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
        function filtrarTareas() {
            const estado = document.getElementById('filtroEstado').value;
            const prioridad = document.getElementById('filtroPrioridad').value;
            const filas = document.querySelectorAll('.tarea-row');
            
            filas.forEach(fila => {
                const filaEstado = fila.getAttribute('data-estado');
                const filaPrioridad = fila.getAttribute('data-prioridad');
                
                let mostrar = true;
                
                if (estado && filaEstado !== estado) {
                    mostrar = false;
                }
                
                if (prioridad && filaPrioridad !== prioridad) {
                    mostrar = false;
                }
                
                fila.style.display = mostrar ? '' : 'none';
            });
        }
        
        function limpiarFiltros() {
            document.getElementById('filtroEstado').value = '';
            document.getElementById('filtroPrioridad').value = '';
            filtrarTareas();
        }
        
        function verDetalles(id) {
            mostrarLoading("Cargando detalles...");
            
            fetch('Controladores/ArrayTareas.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'accion=obtener&id=' + id
            })
            .then(response => response.json())
            .then(data => {
                ocultarLoading();
                if (data.success) {
                    const tarea = data.data;
                    const detalles = `
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
                    Swal.fire('Error', data.message, 'error');
                }
            })
            .catch(error => {
                ocultarLoading();
                console.error('Error:', error);
                Swal.fire('Error', 'Error al cargar los detalles de la tarea', 'error');
            });
        }
        
        function cambiarEstado(id, nuevoEstado) {
            const mensajes = {
                'En progreso': '¿Deseas marcar esta tarea como "En progreso"?',
                'Completada': '¿Deseas marcar esta tarea como "Completada"?',
                'Por hacer': '¿Deseas volver a marcar esta tarea como "Por hacer"?'
            };
            
            Swal.fire({
                title: '¿Confirmar cambio?',
                text: mensajes[nuevoEstado] || '¿Deseas cambiar el estado de esta tarea?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Sí, cambiar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    mostrarLoading("Cambiando estado...");
                    
                    fetch('Controladores/ArrayTareas.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `accion=cambiar_estado&id=${id}&estado=${nuevoEstado}`
                    })
                    .then(response => response.json())
                    .then(data => {
                        ocultarLoading();
                        if (data.success) {
                            Swal.fire('Éxito', data.message, 'success');
                            setTimeout(() => {
                                location.reload();
                            }, 1500);
                        } else {
                            Swal.fire('Error', data.message, 'error');
                        }
                    })
                    .catch(error => {
                        ocultarLoading();
                        console.error('Error:', error);
                        Swal.fire('Error', 'Error al cambiar el estado', 'error');
                    });
                }
            });
        }
        
        function mostrarLoading(mensaje) {
            document.getElementById('loading-text').textContent = mensaje;
            document.getElementById('loading-overlay').style.display = 'flex';
        }
        
        function ocultarLoading() {
            document.getElementById('loading-overlay').style.display = 'none';
        }
    </script>
</body>
</html>
