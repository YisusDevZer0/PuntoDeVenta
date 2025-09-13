<?php
/**
 * Sistema de Recordatorios - Doctor Pez
 * Interfaz administrativa para gestionar recordatorios
 * con envío por WhatsApp y notificaciones internas
 */

session_start();
include_once "Consultas/db_connect.php";
include_once "Controladores/RecordatoriosSistemaController.php";

// Verificar sesión
if (!isset($_SESSION["Id_PvUser"])) {
    header("Location: ../index.php");
    exit();
}

$usuario_id = $_SESSION["Id_PvUser"];
$sucursal_id = $_SESSION["ID_Sucursal"] ?? 1;

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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Recordatorios - Doctor Pez</title>
    
    <!-- CSS -->
    <link rel="stylesheet" href="../css/material.min.css">
    <link rel="stylesheet" href="../css/main.css">
    <link rel="stylesheet" href="css/recordatorios.css">
    
    <!-- Material Design Icons -->
    <link rel="stylesheet" href="../css/material-design-iconic-font.min.css">
    
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="../css/sweetalert2.css">
</head>
<body>
    <!-- Header -->
    <div class="mdl-layout mdl-js-layout mdl-layout--fixed-header">
        <header class="mdl-layout__header">
            <div class="mdl-layout__header-row">
                <span class="mdl-layout-title">
                    <i class="zmdi zmdi-notifications-active"></i>
                    Sistema de Recordatorios
                </span>
                <div class="mdl-layout-spacer"></div>
                <button class="mdl-button mdl-js-button mdl-button--fab mdl-button--mini-fab mdl-button--colored" 
                        id="btn-nuevo-recordatorio">
                    <i class="zmdi zmdi-plus"></i>
                </button>
            </div>
        </header>

        <!-- Navigation -->
        <div class="mdl-layout__drawer">
            <span class="mdl-layout-title">Menú</span>
            <nav class="mdl-navigation">
                <a class="mdl-navigation__link" href="index.php">
                    <i class="zmdi zmdi-home"></i> Inicio
                </a>
                <a class="mdl-navigation__link" href="RecordatoriosSistema.php">
                    <i class="zmdi zmdi-notifications-active"></i> Recordatorios
                </a>
                <a class="mdl-navigation__link" href="RecordatoriosSistema.php?action=grupos">
                    <i class="zmdi zmdi-accounts"></i> Grupos
                </a>
                <a class="mdl-navigation__link" href="RecordatoriosSistema.php?action=plantillas">
                    <i class="zmdi zmdi-file-text"></i> Plantillas
                </a>
                <a class="mdl-navigation__link" href="RecordatoriosSistema.php?action=estadisticas">
                    <i class="zmdi zmdi-chart"></i> Estadísticas
                </a>
            </nav>
        </div>

        <!-- Main Content -->
        <main class="mdl-layout__content">
            <div class="page-content">
                
                <!-- Filtros -->
                <div class="mdl-card mdl-shadow--2dp filtros-card">
                    <div class="mdl-card__title">
                        <h2 class="mdl-card__title-text">
                            <i class="zmdi zmdi-filter-list"></i>
                            Filtros
                        </h2>
                    </div>
                    <div class="mdl-card__supporting-text">
                        <form method="GET" class="filtros-form">
                            <div class="mdl-grid">
                                <div class="mdl-cell mdl-cell--3-col">
                                    <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                                        <input class="mdl-textfield__input" type="text" id="fecha_desde" name="fecha_desde" 
                                               value="<?= $_GET['fecha_desde'] ?? '' ?>">
                                        <label class="mdl-textfield__label" for="fecha_desde">Fecha Desde</label>
                                    </div>
                                </div>
                                <div class="mdl-cell mdl-cell--3-col">
                                    <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                                        <input class="mdl-textfield__input" type="text" id="fecha_hasta" name="fecha_hasta" 
                                               value="<?= $_GET['fecha_hasta'] ?? '' ?>">
                                        <label class="mdl-textfield__label" for="fecha_hasta">Fecha Hasta</label>
                                    </div>
                                </div>
                                <div class="mdl-cell mdl-cell--2-col">
                                    <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                                        <select class="mdl-textfield__input" id="estado" name="estado">
                                            <option value="">Todos los estados</option>
                                            <option value="programado" <?= ($_GET['estado'] ?? '') === 'programado' ? 'selected' : '' ?>>Programado</option>
                                            <option value="enviando" <?= ($_GET['estado'] ?? '') === 'enviando' ? 'selected' : '' ?>>Enviando</option>
                                            <option value="enviado" <?= ($_GET['estado'] ?? '') === 'enviado' ? 'selected' : '' ?>>Enviado</option>
                                            <option value="cancelado" <?= ($_GET['estado'] ?? '') === 'cancelado' ? 'selected' : '' ?>>Cancelado</option>
                                            <option value="error" <?= ($_GET['estado'] ?? '') === 'error' ? 'selected' : '' ?>>Error</option>
                                        </select>
                                        <label class="mdl-textfield__label" for="estado">Estado</label>
                                    </div>
                                </div>
                                <div class="mdl-cell mdl-cell--2-col">
                                    <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                                        <select class="mdl-textfield__input" id="prioridad" name="prioridad">
                                            <option value="">Todas las prioridades</option>
                                            <option value="urgente" <?= ($_GET['prioridad'] ?? '') === 'urgente' ? 'selected' : '' ?>>Urgente</option>
                                            <option value="alta" <?= ($_GET['prioridad'] ?? '') === 'alta' ? 'selected' : '' ?>>Alta</option>
                                            <option value="media" <?= ($_GET['prioridad'] ?? '') === 'media' ? 'selected' : '' ?>>Media</option>
                                            <option value="baja" <?= ($_GET['prioridad'] ?? '') === 'baja' ? 'selected' : '' ?>>Baja</option>
                                        </select>
                                        <label class="mdl-textfield__label" for="prioridad">Prioridad</label>
                                    </div>
                                </div>
                                <div class="mdl-cell mdl-cell--2-col">
                                    <button type="submit" class="mdl-button mdl-js-button mdl-button--raised mdl-button--colored">
                                        <i class="zmdi zmdi-search"></i> Filtrar
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Lista de Recordatorios -->
                <div class="mdl-card mdl-shadow--2dp recordatorios-card">
                    <div class="mdl-card__title">
                        <h2 class="mdl-card__title-text">
                            <i class="zmdi zmdi-notifications"></i>
                            Recordatorios del Sistema
                        </h2>
                        <div class="mdl-layout-spacer"></div>
                        <button class="mdl-button mdl-js-button mdl-button--icon" id="btn-refresh">
                            <i class="zmdi zmdi-refresh"></i>
                        </button>
                    </div>
                    <div class="mdl-card__supporting-text">
                        <?php if ($recordatorios['success'] && !empty($recordatorios['recordatorios'])): ?>
                            <div class="recordatorios-table-container">
                                <table class="mdl-data-table mdl-js-data-table mdl-shadow--2dp recordatorios-table">
                                    <thead>
                                        <tr>
                                            <th class="mdl-data-table__cell--non-numeric">Título</th>
                                            <th class="mdl-data-table__cell--non-numeric">Prioridad</th>
                                            <th class="mdl-data-table__cell--non-numeric">Estado</th>
                                            <th class="mdl-data-table__cell--non-numeric">Fecha Programada</th>
                                            <th class="mdl-data-table__cell--non-numeric">Destinatarios</th>
                                            <th class="mdl-data-table__cell--non-numeric">Tipo Envío</th>
                                            <th class="mdl-data-table__cell--non-numeric">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($recordatorios['recordatorios'] as $recordatorio): ?>
                                            <tr data-recordatorio-id="<?= $recordatorio['id_recordatorio'] ?>">
                                                <td class="mdl-data-table__cell--non-numeric">
                                                    <strong><?= htmlspecialchars($recordatorio['titulo']) ?></strong>
                                                    <?php if (!empty($recordatorio['descripcion'])): ?>
                                                        <br><small class="text-muted"><?= htmlspecialchars(substr($recordatorio['descripcion'], 0, 100)) ?>...</small>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="mdl-data-table__cell--non-numeric">
                                                    <span class="prioridad-badge prioridad-<?= $recordatorio['prioridad'] ?>">
                                                        <?= ucfirst($recordatorio['prioridad']) ?>
                                                    </span>
                                                </td>
                                                <td class="mdl-data-table__cell--non-numeric">
                                                    <span class="estado-badge estado-<?= $recordatorio['estado'] ?>">
                                                        <?= $recordatorio['estado_descripcion'] ?>
                                                    </span>
                                                </td>
                                                <td class="mdl-data-table__cell--non-numeric">
                                                    <?= date('d/m/Y H:i', strtotime($recordatorio['fecha_programada'])) ?>
                                                </td>
                                                <td class="mdl-data-table__cell--non-numeric">
                                                    <?= ucfirst($recordatorio['destinatarios']) ?>
                                                    <?php if ($recordatorio['sucursal_nombre']): ?>
                                                        <br><small class="text-muted"><?= $recordatorio['sucursal_nombre'] ?></small>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="mdl-data-table__cell--non-numeric">
                                                    <?php
                                                    $tipos = explode(',', $recordatorio['tipo_envio']);
                                                    foreach ($tipos as $tipo): ?>
                                                        <span class="tipo-envio-badge tipo-<?= trim($tipo) ?>">
                                                            <?= ucfirst(trim($tipo)) ?>
                                                        </span>
                                                    <?php endforeach; ?>
                                                </td>
                                                <td class="mdl-data-table__cell--non-numeric">
                                                    <button class="mdl-button mdl-js-button mdl-button--icon mdl-button--colored btn-ver" 
                                                            data-recordatorio-id="<?= $recordatorio['id_recordatorio'] ?>">
                                                        <i class="zmdi zmdi-eye"></i>
                                                    </button>
                                                    <?php if ($recordatorio['estado'] === 'programado'): ?>
                                                        <button class="mdl-button mdl-js-button mdl-button--icon mdl-button--colored btn-editar" 
                                                                data-recordatorio-id="<?= $recordatorio['id_recordatorio'] ?>">
                                                            <i class="zmdi zmdi-edit"></i>
                                                        </button>
                                                        <button class="mdl-button mdl-js-button mdl-button--icon mdl-button--accent btn-enviar" 
                                                                data-recordatorio-id="<?= $recordatorio['id_recordatorio'] ?>">
                                                            <i class="zmdi zmdi-send"></i>
                                                        </button>
                                                    <?php endif; ?>
                                                    <button class="mdl-button mdl-js-button mdl-button--icon btn-eliminar" 
                                                            data-recordatorio-id="<?= $recordatorio['id_recordatorio'] ?>">
                                                        <i class="zmdi zmdi-delete"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="no-data">
                                <i class="zmdi zmdi-notifications-off"></i>
                                <p>No hay recordatorios que mostrar</p>
                                <button class="mdl-button mdl-js-button mdl-button--raised mdl-button--colored" 
                                        id="btn-crear-primer-recordatorio">
                                    <i class="zmdi zmdi-plus"></i>
                                    Crear Primer Recordatorio
                                </button>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Modal para crear/editar recordatorio -->
    <div id="modal-recordatorio" class="mdl-dialog">
        <div class="mdl-dialog__content">
            <h4 id="modal-titulo">Nuevo Recordatorio</h4>
            <form id="form-recordatorio">
                <input type="hidden" id="recordatorio-id" name="recordatorio_id">
                
                <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                    <input class="mdl-textfield__input" type="text" id="titulo" name="titulo" required>
                    <label class="mdl-textfield__label" for="titulo">Título *</label>
                </div>
                
                <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                    <textarea class="mdl-textfield__input" type="text" id="descripcion" name="descripcion" rows="3"></textarea>
                    <label class="mdl-textfield__label" for="descripcion">Descripción</label>
                </div>
                
                <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                    <input class="mdl-textfield__input" type="datetime-local" id="fecha_programada" name="fecha_programada" required>
                    <label class="mdl-textfield__label" for="fecha_programada">Fecha y Hora Programada *</label>
                </div>
                
                <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                    <select class="mdl-textfield__input" id="prioridad" name="prioridad">
                        <option value="baja">Baja</option>
                        <option value="media" selected>Media</option>
                        <option value="alta">Alta</option>
                        <option value="urgente">Urgente</option>
                    </select>
                    <label class="mdl-textfield__label" for="prioridad">Prioridad</label>
                </div>
                
                <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                    <select class="mdl-textfield__input" id="destinatarios" name="destinatarios">
                        <option value="todos">Todos los usuarios</option>
                        <option value="sucursal">Sucursal específica</option>
                        <option value="grupo">Grupo específico</option>
                        <option value="individual">Usuarios individuales</option>
                    </select>
                    <label class="mdl-textfield__label" for="destinatarios">Destinatarios</label>
                </div>
                
                <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label" id="sucursal-container" style="display: none;">
                    <select class="mdl-textfield__input" id="sucursal_id" name="sucursal_id">
                        <?php foreach ($sucursales as $sucursal): ?>
                            <option value="<?= $sucursal['ID_Sucursal'] ?>"><?= $sucursal['Nombre_Sucursal'] ?></option>
                        <?php endforeach; ?>
                    </select>
                    <label class="mdl-textfield__label" for="sucursal_id">Sucursal</label>
                </div>
                
                <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label" id="grupo-container" style="display: none;">
                    <select class="mdl-textfield__input" id="grupo_id" name="grupo_id">
                        <?php foreach ($grupos as $grupo): ?>
                            <option value="<?= $grupo['id_grupo'] ?>"><?= $grupo['nombre_grupo'] ?></option>
                        <?php endforeach; ?>
                    </select>
                    <label class="mdl-textfield__label" for="grupo_id">Grupo</label>
                </div>
                
                <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                    <select class="mdl-textfield__input" id="tipo_envio" name="tipo_envio">
                        <option value="ambos">WhatsApp y Notificación</option>
                        <option value="whatsapp">Solo WhatsApp</option>
                        <option value="notificacion">Solo Notificación</option>
                    </select>
                    <label class="mdl-textfield__label" for="tipo_envio">Tipo de Envío</label>
                </div>
                
                <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                    <textarea class="mdl-textfield__input" type="text" id="mensaje_whatsapp" name="mensaje_whatsapp" rows="3"></textarea>
                    <label class="mdl-textfield__label" for="mensaje_whatsapp">Mensaje para WhatsApp</label>
                </div>
                
                <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                    <textarea class="mdl-textfield__input" type="text" id="mensaje_notificacion" name="mensaje_notificacion" rows="3"></textarea>
                    <label class="mdl-textfield__label" for="mensaje_notificacion">Mensaje para Notificación</label>
                </div>
            </form>
        </div>
        <div class="mdl-dialog__actions">
            <button type="button" class="mdl-button mdl-js-button mdl-button--raised" id="btn-cancelar">
                Cancelar
            </button>
            <button type="button" class="mdl-button mdl-js-button mdl-button--raised mdl-button--colored" id="btn-guardar">
                Guardar
            </button>
        </div>
    </div>

    <!-- Scripts -->
    <script src="../js/jquery-1.11.2.min.js"></script>
    <script src="../js/material.min.js"></script>
    <script src="../js/sweetalert2.min.js"></script>
    <script src="js/recordatorios.js"></script>
</body>
</html>
