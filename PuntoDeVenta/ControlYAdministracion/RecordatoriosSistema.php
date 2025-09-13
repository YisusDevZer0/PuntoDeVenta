<?php
/**
 * Sistema de Recordatorios - Doctor Pez
 * Interfaz administrativa para gestionar recordatorios
 * con envío por WhatsApp y notificaciones internas
 */

include_once "Controladores/ControladorUsuario.php";

// Verificar sesión
if(!isset($_SESSION['ControlMaestro']) && !isset($_SESSION['AdministradorRH']) && !isset($_SESSION['Marketing'])){
    header("Location: Expiro.php");
    exit();
}

// Asegurar que $row esté disponible
if (!isset($row)) {
    include_once "Controladores/ControladorUsuario.php";
}

// Obtener ID del usuario actual
$usuario_id = isset($_SESSION['ControlMaestro']) ? $_SESSION['ControlMaestro'] : 
            (isset($_SESSION['AdministradorRH']) ? $_SESSION['AdministradorRH'] : $_SESSION['Marketing']);

$sucursal_id = $row['Fk_Sucursal'] ?? 1;

// Incluir controlador de recordatorios
include_once "Controladores/RecordatoriosSistemaController.php";

// Definir variable para atributos disabled
$disabledAttr = '';

// Variable para identificar la página actual
$currentPage = 'recordatorios';

// Variable específica para el dashboard
$showDashboard = false;

// Obtener el tipo de usuario actual
$tipoUsuario = isset($row['TipoUsuario']) ? $row['TipoUsuario'] : 'Usuario';

// Verificar si el usuario tiene permisos de administrador
$isAdmin = ($tipoUsuario == 'Administrador' || $tipoUsuario == 'MKT');

// Inicializar controlador
$recordatoriosController = new RecordatoriosSistemaController($con, $usuario_id);

// Obtener datos para la interfaz
$filtros = [
    'limit' => 20,
    'estado' => $_GET['estado'] ?? null,
    'prioridad' => $_GET['prioridad'] ?? null,
    'fecha_desde' => $_GET['fecha_desde'] ?? null,
    'fecha_hasta' => $_GET['fecha_hasta'] ?? null,
    'sucursal_id' => $sucursal_id
];

$recordatorios = $recordatoriosController->obtenerRecordatorios($filtros);

// Obtener sucursales para filtros
$sucursales_query = "SELECT ID_Sucursal, Nombre_Sucursal FROM Sucursales WHERE Activo = 1 ORDER BY Nombre_Sucursal";
$sucursales_result = $con->query($sucursales_query);
$sucursales = [];
while ($row = $sucursales_result->fetch_assoc()) {
    $sucursales[] = $row;
}

// Obtener grupos para filtros
$grupos_query = "SELECT id_grupo, nombre_grupo FROM recordatorios_grupos WHERE activo = 1 ORDER BY nombre_grupo";
$grupos_result = $con->query($grupos_query);
$grupos = [];
while ($row = $grupos_result->fetch_assoc()) {
    $grupos[] = $row;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Sistema de Recordatorios - <?php echo $row['Licencia']?></title>
    <meta content="" name="keywords">
    <meta content="" name="description">
    
    <?php include "header.php";?>
    
    <!-- Estilos específicos de recordatorios -->
    <link href="css/recordatorios.css" rel="stylesheet">
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
        <!-- Navbar Start -->
        <div class="container-fluid pt-4 px-4">
            <div class="row g-4">
                <div class="col-sm-12 col-xl-12">
                    <div class="bg-light rounded h-100 p-4">
                        <div class="d-flex align-items-center justify-content-between mb-4">
                            <h6 class="mb-0">
                                <i class="fa-solid fa-bell me-2"></i>
                                Sistema de Recordatorios
                            </h6>
                            <button class="btn btn-primary" id="btn-nuevo-recordatorio">
                                <i class="fa-solid fa-plus me-2"></i>
                                Nuevo Recordatorio
                            </button>
                        </div>
                
                        <!-- Filtros -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fa-solid fa-filter me-2"></i>
                                    Filtros
                                </h5>
                            </div>
                            <div class="card-body">
                                <form method="GET" class="row g-3">
                                    <div class="col-md-3">
                                        <label for="fecha_desde" class="form-label">Fecha Desde</label>
                                        <input type="date" class="form-control" id="fecha_desde" name="fecha_desde" 
                                               value="<?= $_GET['fecha_desde'] ?? '' ?>">
                                    </div>
                                    <div class="col-md-3">
                                        <label for="fecha_hasta" class="form-label">Fecha Hasta</label>
                                        <input type="date" class="form-control" id="fecha_hasta" name="fecha_hasta" 
                                               value="<?= $_GET['fecha_hasta'] ?? '' ?>">
                                    </div>
                                    <div class="col-md-2">
                                        <label for="estado" class="form-label">Estado</label>
                                        <select class="form-select" id="estado" name="estado">
                                            <option value="">Todos los estados</option>
                                            <option value="programado" <?= ($_GET['estado'] ?? '') === 'programado' ? 'selected' : '' ?>>Programado</option>
                                            <option value="enviando" <?= ($_GET['estado'] ?? '') === 'enviando' ? 'selected' : '' ?>>Enviando</option>
                                            <option value="enviado" <?= ($_GET['estado'] ?? '') === 'enviado' ? 'selected' : '' ?>>Enviado</option>
                                            <option value="cancelado" <?= ($_GET['estado'] ?? '') === 'cancelado' ? 'selected' : '' ?>>Cancelado</option>
                                            <option value="error" <?= ($_GET['estado'] ?? '') === 'error' ? 'selected' : '' ?>>Error</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label for="prioridad" class="form-label">Prioridad</label>
                                        <select class="form-select" id="prioridad" name="prioridad">
                                            <option value="">Todas las prioridades</option>
                                            <option value="urgente" <?= ($_GET['prioridad'] ?? '') === 'urgente' ? 'selected' : '' ?>>Urgente</option>
                                            <option value="alta" <?= ($_GET['prioridad'] ?? '') === 'alta' ? 'selected' : '' ?>>Alta</option>
                                            <option value="media" <?= ($_GET['prioridad'] ?? '') === 'media' ? 'selected' : '' ?>>Media</option>
                                            <option value="baja" <?= ($_GET['prioridad'] ?? '') === 'baja' ? 'selected' : '' ?>>Baja</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">&nbsp;</label>
                                        <button type="submit" class="btn btn-primary d-block w-100">
                                            <i class="fa-solid fa-search me-1"></i> Filtrar
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Lista de Recordatorios -->
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">
                                    <i class="fa-solid fa-bell me-2"></i>
                                    Recordatorios del Sistema
                                </h5>
                                <button class="btn btn-outline-primary btn-sm" id="btn-refresh">
                                    <i class="fa-solid fa-refresh me-1"></i>
                                    Actualizar
                                </button>
                            </div>
                            <div class="card-body">
                                <?php if ($recordatorios['success'] && !empty($recordatorios['recordatorios'])): ?>
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Título</th>
                                                    <th>Prioridad</th>
                                                    <th>Estado</th>
                                                    <th>Fecha Programada</th>
                                                    <th>Destinatarios</th>
                                                    <th>Tipo Envío</th>
                                                    <th>Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($recordatorios['recordatorios'] as $recordatorio): ?>
                                                    <tr data-recordatorio-id="<?= $recordatorio['id_recordatorio'] ?>">
                                                        <td>
                                                            <strong><?= htmlspecialchars($recordatorio['titulo']) ?></strong>
                                                            <?php if (!empty($recordatorio['descripcion'])): ?>
                                                                <br><small class="text-muted"><?= htmlspecialchars(substr($recordatorio['descripcion'], 0, 100)) ?>...</small>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td>
                                                            <span class="badge bg-<?= $recordatorio['prioridad'] === 'urgente' ? 'danger' : ($recordatorio['prioridad'] === 'alta' ? 'warning' : ($recordatorio['prioridad'] === 'media' ? 'info' : 'secondary')) ?>">
                                                                <?= ucfirst($recordatorio['prioridad']) ?>
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <span class="badge bg-<?= $recordatorio['estado'] === 'enviado' ? 'success' : ($recordatorio['estado'] === 'error' ? 'danger' : ($recordatorio['estado'] === 'enviando' ? 'warning' : 'primary')) ?>">
                                                                <?= $recordatorio['estado_descripcion'] ?>
                                                            </span>
                                                        </td>
                                                        <td><?= date('d/m/Y H:i', strtotime($recordatorio['fecha_programada'])) ?></td>
                                                        <td>
                                                            <?= ucfirst($recordatorio['destinatarios']) ?>
                                                            <?php if ($recordatorio['sucursal_nombre']): ?>
                                                                <br><small class="text-muted"><?= $recordatorio['sucursal_nombre'] ?></small>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td>
                                                            <?php
                                                            $tipos = explode(',', $recordatorio['tipo_envio']);
                                                            foreach ($tipos as $tipo): ?>
                                                                <span class="badge bg-light text-dark me-1">
                                                                    <?= ucfirst(trim($tipo)) ?>
                                                                </span>
                                                            <?php endforeach; ?>
                                                        </td>
                                                        <td>
                                                            <div class="btn-group" role="group">
                                                                <button class="btn btn-sm btn-outline-info btn-ver" 
                                                                        data-recordatorio-id="<?= $recordatorio['id_recordatorio'] ?>"
                                                                        title="Ver detalles">
                                                                    <i class="fa-solid fa-eye"></i>
                                                                </button>
                                                                <?php if ($recordatorio['estado'] === 'programado'): ?>
                                                                    <button class="btn btn-sm btn-outline-warning btn-editar" 
                                                                            data-recordatorio-id="<?= $recordatorio['id_recordatorio'] ?>"
                                                                            title="Editar">
                                                                        <i class="fa-solid fa-edit"></i>
                                                                    </button>
                                                                    <button class="btn btn-sm btn-outline-success btn-enviar" 
                                                                            data-recordatorio-id="<?= $recordatorio['id_recordatorio'] ?>"
                                                                            title="Enviar ahora">
                                                                        <i class="fa-solid fa-paper-plane"></i>
                                                                    </button>
                                                                <?php endif; ?>
                                                                <button class="btn btn-sm btn-outline-danger btn-eliminar" 
                                                                        data-recordatorio-id="<?= $recordatorio['id_recordatorio'] ?>"
                                                                        title="Eliminar">
                                                                    <i class="fa-solid fa-trash"></i>
                                                                </button>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php else: ?>
                                    <div class="text-center py-5">
                                        <i class="fa-solid fa-bell-slash fa-3x text-muted mb-3"></i>
                                        <h5 class="text-muted">No hay recordatorios que mostrar</h5>
                                        <p class="text-muted">Crea tu primer recordatorio para comenzar</p>
                                        <button class="btn btn-primary" id="btn-crear-primer-recordatorio">
                                            <i class="fa-solid fa-plus me-2"></i>
                                            Crear Primer Recordatorio
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

    <!-- Modal para crear/editar recordatorio -->
    <div class="modal fade" id="modal-recordatorio" tabindex="-1" aria-labelledby="modal-recordatorio-label" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal-recordatorio-label">Nuevo Recordatorio</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="form-recordatorio">
                        <input type="hidden" id="recordatorio-id" name="recordatorio_id">
                        
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label for="titulo" class="form-label">Título *</label>
                                <input type="text" class="form-control" id="titulo" name="titulo" required>
                            </div>
                            
                            <div class="col-md-12">
                                <label for="descripcion" class="form-label">Descripción</label>
                                <textarea class="form-control" id="descripcion" name="descripcion" rows="3"></textarea>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="fecha_programada" class="form-label">Fecha y Hora Programada *</label>
                                <input type="datetime-local" class="form-control" id="fecha_programada" name="fecha_programada" required>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="prioridad" class="form-label">Prioridad</label>
                                <select class="form-select" id="prioridad" name="prioridad">
                                    <option value="baja">Baja</option>
                                    <option value="media" selected>Media</option>
                                    <option value="alta">Alta</option>
                                    <option value="urgente">Urgente</option>
                                </select>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="destinatarios" class="form-label">Destinatarios</label>
                                <select class="form-select" id="destinatarios" name="destinatarios">
                                    <option value="todos">Todos los usuarios</option>
                                    <option value="sucursal">Sucursal específica</option>
                                    <option value="grupo">Grupo específico</option>
                                    <option value="individual">Usuarios individuales</option>
                                </select>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="tipo_envio" class="form-label">Tipo de Envío</label>
                                <select class="form-select" id="tipo_envio" name="tipo_envio">
                                    <option value="ambos">WhatsApp y Notificación</option>
                                    <option value="whatsapp">Solo WhatsApp</option>
                                    <option value="notificacion">Solo Notificación</option>
                                </select>
                            </div>
                            
                            <div class="col-md-6" id="sucursal-container" style="display: none;">
                                <label for="sucursal_id" class="form-label">Sucursal</label>
                                <select class="form-select" id="sucursal_id" name="sucursal_id">
                                    <?php foreach ($sucursales as $sucursal): ?>
                                        <option value="<?= $sucursal['ID_Sucursal'] ?>"><?= $sucursal['Nombre_Sucursal'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="col-md-6" id="grupo-container" style="display: none;">
                                <label for="grupo_id" class="form-label">Grupo</label>
                                <select class="form-select" id="grupo_id" name="grupo_id">
                                    <?php foreach ($grupos as $grupo): ?>
                                        <option value="<?= $grupo['id_grupo'] ?>"><?= $grupo['nombre_grupo'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="col-md-12">
                                <label for="mensaje_whatsapp" class="form-label">Mensaje para WhatsApp</label>
                                <textarea class="form-control" id="mensaje_whatsapp" name="mensaje_whatsapp" rows="3"></textarea>
                            </div>
                            
                            <div class="col-md-12">
                                <label for="mensaje_notificacion" class="form-label">Mensaje para Notificación</label>
                                <textarea class="form-control" id="mensaje_notificacion" name="mensaje_notificacion" rows="3"></textarea>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="btn-guardar">Guardar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer Start -->
    <?php include "footer.php";?>
    <!-- Footer End -->

    <!-- Back to Top -->
    <a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top"><i class="bi bi-arrow-up"></i></a>
    </div>

    <!-- JavaScript Libraries -->
    <?php include "scripts.php";?>
    
    <!-- Scripts específicos de recordatorios -->
    <script src="js/recordatorios.js"></script>
</body>
</html>
