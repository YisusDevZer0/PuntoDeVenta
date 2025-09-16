<?php
include_once "Controladores/ControladorUsuario.php";
include_once "Controladores/BitacoraLimpiezaAdminController.php";

// Verificar sesión
if(!isset($_SESSION['ControlMaestro']) && !isset($_SESSION['AdministradorRH']) && !isset($_SESSION['Marketing'])){
    header("Location: Expiro.php");
    exit();
}

// Asegurar que $row esté disponible
if (!isset($row)) {
    // Si $row no está disponible, incluir nuevamente el controlador
    include_once "Controladores/ControladorUsuario.php";
}

// Definir variable para atributos disabled (por ahora vacía para habilitar todo)
$disabledAttr = '';

// Variable para identificar la página actual
$currentPage = 'bitacora_limpieza';

// Variable específica para el dashboard (no depende de permisos)
$showDashboard = true;

// Obtener el tipo de usuario actual
$tipoUsuario = isset($row['TipoUsuario']) ? $row['TipoUsuario'] : 'Usuario';

// Verificar si el usuario tiene permisos de administrador
$isAdmin = ($tipoUsuario == 'Administrador' || $tipoUsuario == 'MKT');

// Verificar si es desarrollo humano (RH)
$isRH = ($tipoUsuario == 'Desarrollo Humano' || $tipoUsuario == 'RH');

// Inicializar controlador
$controller = new BitacoraLimpiezaAdminController($conn);

// Obtener filtros
$filtros = [
    'sucursal' => $_GET['sucursal'] ?? '',
    'area' => $_GET['area'] ?? '',
    'fecha_inicio' => $_GET['fecha_inicio'] ?? '',
    'fecha_fin' => $_GET['fecha_fin'] ?? '',
    'estado' => $_GET['estado'] ?? '',
    'limit' => 50
];

try {
    // Obtener datos usando el controlador
    $bitacoras = $controller->obtenerBitacorasConFiltros($filtros);
    $sucursales = $controller->obtenerSucursales();
    $areas = $controller->obtenerAreas();
    $estadisticas = $controller->obtenerEstadisticasGenerales();
    
    $total_bitacoras = $estadisticas['total_bitacoras'];
    $total_sucursales = $estadisticas['total_sucursales'];
    $total_areas = $estadisticas['total_areas'];
    $bitacoras_activas = $estadisticas['bitacoras_activas'];
    
} catch (Exception $e) {
    // En caso de error, usar datos básicos
    $bitacoras = [];
    $sucursales = [];
    $areas = [];
    $total_bitacoras = 0;
    $total_sucursales = 0;
    $total_areas = 0;
    $bitacoras_activas = 0;
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Control de Bitácoras de Limpieza - <?php echo $row['Licencia']?></title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
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

    <!-- Sidebar Start -->
    <?php include_once "Menu.php" ?>
    <!-- Sidebar End -->

    <!-- Content Start -->
    <div class="content">
        <!-- Navbar Start -->
        <?php include "navbar.php";?>
        <!-- Navbar End -->

        <!-- Container Start -->
        <div class="container-fluid pt-4 px-4">
            <div class="row g-4">
                <!-- Título y controles -->
                <div class="col-12">
                    <div class="bg-light rounded p-4">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h4 class="mb-0 text-primary">
                                <i class="fa-solid fa-broom me-2"></i>
                                Control de Bitácoras de Limpieza
                            </h4>
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#ModalNuevaBitacoraAdmin">
                                    <i class="fa fa-plus me-2"></i>Nueva Bitácora
                                </button>
                                <button type="button" class="btn btn-success" onclick="exportarCSV()">
                                    <i class="fa fa-download me-2"></i>Exportar CSV
                                </button>
                                <button type="button" class="btn btn-info" onclick="aplicarFiltros()">
                                    <i class="fa fa-refresh me-2"></i>Actualizar
                                </button>
                            </div>
                        </div>

                        <!-- Filtros -->
                        <div class="row g-3 mb-4">
                            <div class="col-md-3">
                                <label for="filtroSucursal" class="form-label">Sucursal:</label>
                                <select class="form-select" id="filtroSucursal">
                                    <option value="">Todas las sucursales</option>
                                    <?php foreach($sucursales as $sucursal): ?>
                                        <option value="<?php echo $sucursal['ID_Sucursal']; ?>">
                                            <?php echo htmlspecialchars($sucursal['Nombre_Sucursal']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="filtroArea" class="form-label">Área:</label>
                                <select class="form-select" id="filtroArea">
                                    <option value="">Todas las áreas</option>
                                    <?php foreach($areas as $area): ?>
                                        <option value="<?php echo htmlspecialchars($area); ?>">
                                            <?php echo htmlspecialchars($area); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="filtroFechaInicio" class="form-label">Fecha Inicio:</label>
                                <input type="date" class="form-control" id="filtroFechaInicio">
                            </div>
                            <div class="col-md-3">
                                <label for="filtroFechaFin" class="form-label">Fecha Fin:</label>
                                <input type="date" class="form-control" id="filtroFechaFin">
                            </div>
                        </div>

                        <!-- Estadísticas -->
                        <div class="row g-3 mb-4">
                            <div class="col-md-3">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-body text-center">
                                        <i class="fa fa-clipboard-list fa-2x text-primary mb-2"></i>
                                        <h5 class="card-title"><?php echo $total_bitacoras; ?></h5>
                                        <p class="card-text text-muted">Total Bitácoras</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-body text-center">
                                        <i class="fa fa-check-circle fa-2x text-success mb-2"></i>
                                        <h5 class="card-title"><?php echo $bitacoras_activas; ?></h5>
                                        <p class="card-text text-muted">Bitácoras Activas</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-body text-center">
                                        <i class="fa fa-building fa-2x text-info mb-2"></i>
                                        <h5 class="card-title"><?php echo $total_sucursales; ?></h5>
                                        <p class="card-text text-muted">Sucursales</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-body text-center">
                                        <i class="fa fa-map-marker-alt fa-2x text-warning mb-2"></i>
                                        <h5 class="card-title"><?php echo $total_areas; ?></h5>
                                        <p class="card-text text-muted">Áreas</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabla de bitácoras -->
                <div class="col-12">
                    <div class="bg-light rounded p-4">
                        <h5 class="mb-3">
                            <i class="fa fa-table me-2"></i>
                            Bitácoras de Limpieza
                        </h5>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" id="tablaBitacoras" style="width:100%">
                                <thead class="table-primary">
                                    <tr>
                                        <th>ID</th>
                                        <th>Sucursal</th>
                                        <th>Área</th>
                                        <th>Semana</th>
                                        <th>Fecha Inicio</th>
                                        <th>Fecha Fin</th>
                                        <th>Responsable</th>
                                        <th>Supervisor</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($bitacoras as $bitacora): ?>
                                    <tr>
                                        <td><?php echo $bitacora['id_bitacora']; ?></td>
                                        <td><?php echo $bitacora['Nombre_Sucursal'] ?? 'N/A'; ?></td>
                                        <td><?php echo htmlspecialchars($bitacora['area']); ?></td>
                                        <td><?php echo htmlspecialchars($bitacora['semana']); ?></td>
                                        <td><?php echo $bitacora['fecha_inicio']; ?></td>
                                        <td><?php echo $bitacora['fecha_fin']; ?></td>
                                        <td><?php echo htmlspecialchars($bitacora['responsable']); ?></td>
                                        <td><?php echo htmlspecialchars($bitacora['supervisor']); ?></td>
                                        <td>
                                            <span class="badge bg-<?php echo $bitacora['estado'] == 'Activa' ? 'success' : ($bitacora['estado'] == 'Completada' ? 'primary' : 'warning'); ?>">
                                                <?php echo htmlspecialchars($bitacora['estado']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <button class="btn btn-sm btn-primary" onclick="verDetallesBitacora(<?php echo $bitacora['id_bitacora']; ?>)" title="Ver Detalles">
                                                    <i class="fa fa-eye"></i>
                                                </button>
                                                <button class="btn btn-sm btn-success" onclick="verElementosLimpieza(<?php echo $bitacora['id_bitacora']; ?>)" title="Ver Elementos">
                                                    <i class="fa fa-list"></i>
                                                </button>
                                                <button class="btn btn-sm btn-danger" onclick="eliminarBitacora(<?php echo $bitacora['id_bitacora']; ?>)" title="Eliminar">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Container End -->

        <!-- Footer -->
        <?php include "Footer.php"; ?>
    </div>
    <!-- Content End -->

    <!-- Modales -->
    <?php include "Modales/NuevaBitacoraAdmin.php"; ?>
    <?php include "Modales/VerDetallesBitacora.php"; ?>
    <?php include "Modales/VerElementosLimpieza.php"; ?>

    <script>
    // Variables globales
    let tablaBitacoras;
    
    // Inicialización cuando el DOM esté listo
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Página cargada correctamente');
        
        // Ocultar spinner
        const spinner = document.getElementById('spinner');
        if (spinner) {
            spinner.style.display = 'none';
        }
        
        // Inicializar DataTable
        inicializarDataTable();
        
        // Configurar formulario de nueva bitácora
        configurarFormularioNuevaBitacora();
    });
    
    // Inicializar DataTable
    function inicializarDataTable() {
        if (typeof $.fn.DataTable !== 'undefined') {
            tablaBitacoras = $('#tablaBitacoras').DataTable({
                "paging": true,
                "searching": true,
                "info": true,
                "responsive": true,
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json"
                },
                "order": [[0, "desc"]],
                "pageLength": 25,
                "columnDefs": [
                    { "orderable": false, "targets": 9 } // Columna de acciones
                ]
            });
        }
    }
    
    // Configurar formulario de nueva bitácora
    function configurarFormularioNuevaBitacora() {
        $('#formNuevaBitacoraAdmin').on('submit', function(e) {
            e.preventDefault();
            crearBitacora();
        });
    }
    
    // Aplicar filtros
    function aplicarFiltros() {
        const sucursal = document.getElementById('filtroSucursal').value;
        const area = document.getElementById('filtroArea').value;
        const fechaInicio = document.getElementById('filtroFechaInicio').value;
        const fechaFin = document.getElementById('filtroFechaFin').value;
        
        const params = new URLSearchParams();
        if (sucursal) params.append('sucursal', sucursal);
        if (area) params.append('area', area);
        if (fechaInicio) params.append('fecha_inicio', fechaInicio);
        if (fechaFin) params.append('fecha_fin', fechaFin);
        
        window.location.href = 'BitacoraLimpieza.php?' + params.toString();
    }
    
    // Exportar CSV
    function exportarCSV() {
        const sucursal = document.getElementById('filtroSucursal').value;
        const area = document.getElementById('filtroArea').value;
        const fechaInicio = document.getElementById('filtroFechaInicio').value;
        const fechaFin = document.getElementById('filtroFechaFin').value;
        
        const params = new URLSearchParams();
        if (sucursal) params.append('sucursal', sucursal);
        if (area) params.append('area', area);
        if (fechaInicio) params.append('fecha_inicio', fechaInicio);
        if (fechaFin) params.append('fecha_fin', fechaFin);
        
        window.open('api/exportar_bitacoras_csv.php?' + params.toString(), '_blank');
    }
    
    // Crear nueva bitácora
    function crearBitacora() {
        const formData = new FormData(document.getElementById('formNuevaBitacoraAdmin'));
        
        fetch('api/crear_bitacora_admin.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Éxito',
                    text: data.message
                }).then(() => {
                    location.reload();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: data.message
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Error de conexión'
            });
        });
    }
    
    // Ver detalles de bitácora
    function verDetallesBitacora(id) {
        fetch(`api/obtener_detalles_bitacora.php?id=${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                mostrarDetallesBitacora(data.data);
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: data.message
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Error de conexión'
            });
        });
    }
    
    // Mostrar detalles en modal
    function mostrarDetallesBitacora(bitacora) {
        const contenido = `
            <div class="row g-3">
                <div class="col-md-6">
                    <strong>ID Bitácora:</strong> ${bitacora.id_bitacora}
                </div>
                <div class="col-md-6">
                    <strong>Sucursal:</strong> ${bitacora.Nombre_Sucursal || 'N/A'}
                </div>
                <div class="col-md-6">
                    <strong>Área:</strong> ${bitacora.area}
                </div>
                <div class="col-md-6">
                    <strong>Semana:</strong> ${bitacora.semana}
                </div>
                <div class="col-md-6">
                    <strong>Fecha Inicio:</strong> ${bitacora.fecha_inicio}
                </div>
                <div class="col-md-6">
                    <strong>Fecha Fin:</strong> ${bitacora.fecha_fin}
                </div>
                <div class="col-md-6">
                    <strong>Responsable:</strong> ${bitacora.responsable}
                </div>
                <div class="col-md-6">
                    <strong>Supervisor:</strong> ${bitacora.supervisor}
                </div>
                <div class="col-md-6">
                    <strong>Auxiliar:</strong> ${bitacora.aux_res || 'N/A'}
                </div>
                <div class="col-md-6">
                    <strong>Estado:</strong> 
                    <span class="badge bg-${bitacora.estado == 'Activa' ? 'success' : (bitacora.estado == 'Completada' ? 'primary' : 'warning')}">
                        ${bitacora.estado}
                    </span>
                </div>
                <div class="col-12">
                    <strong>Observaciones:</strong><br>
                    ${bitacora.observaciones || 'Sin observaciones'}
                </div>
            </div>
        `;
        
        document.getElementById('contenidoDetallesBitacora').innerHTML = contenido;
        $('#ModalVerDetallesBitacora').modal('show');
    }
    
    // Ver elementos de limpieza
    function verElementosLimpieza(id) {
        fetch(`api/obtener_elementos_limpieza.php?id=${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                mostrarElementosLimpieza(data.data);
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: data.message
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Error de conexión'
            });
        });
    }
    
    // Mostrar elementos en modal
    function mostrarElementosLimpieza(elementos) {
        let contenido = '<div class="table-responsive"><table class="table table-striped"><thead><tr><th>Elemento</th><th>Estado</th><th>Fecha</th><th>Hora</th><th>Observaciones</th></tr></thead><tbody>';
        
        if (elementos.length > 0) {
            elementos.forEach(elemento => {
                contenido += `
                    <tr>
                        <td>${elemento.elemento_limpieza}</td>
                        <td><span class="badge bg-${elemento.estado == 'Completado' ? 'success' : 'warning'}">${elemento.estado}</span></td>
                        <td>${elemento.fecha_realizacion || 'N/A'}</td>
                        <td>${elemento.hora_realizacion || 'N/A'}</td>
                        <td>${elemento.observaciones || 'N/A'}</td>
                    </tr>
                `;
            });
        } else {
            contenido += '<tr><td colspan="5" class="text-center">No hay elementos de limpieza registrados</td></tr>';
        }
        
        contenido += '</tbody></table></div>';
        
        document.getElementById('contenidoElementosLimpieza').innerHTML = contenido;
        $('#ModalVerElementosLimpieza').modal('show');
    }
    
    // Eliminar bitácora
    function eliminarBitacora(id) {
        Swal.fire({
            title: '¿Estás seguro?',
            text: "Esta acción no se puede deshacer",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                const formData = new FormData();
                formData.append('id', id);
                
                fetch('api/eliminar_bitacora_admin.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Eliminada',
                            text: data.message
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.message
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Error de conexión'
                    });
                });
            }
        });
    }
    </script>

</body>
</html>