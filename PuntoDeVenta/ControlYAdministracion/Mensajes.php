<?php
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

// Definir variable para atributos disabled
$disabledAttr = '';

// Variable para identificar la página actual
$currentPage = 'mensajes';

// Variable específica para el dashboard
$showDashboard = false;

// Obtener el tipo de usuario actual
$tipoUsuario = isset($row['TipoUsuario']) ? $row['TipoUsuario'] : 'Usuario';

// Verificar si el usuario tiene permisos de administrador
$isAdmin = ($tipoUsuario == 'Administrador' || $tipoUsuario == 'MKT');

// Verificar si es desarrollo humano (RH)
$isRH = ($tipoUsuario == 'Desarrollo Humano' || $tipoUsuario == 'RH');

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Sistema de Mensajes - <?php echo $row['Licencia']?></title>
    <meta content="" name="keywords">
    <meta content="" name="description">
    
    <?php include "header.php";?>
    
    <!-- Estilos específicos del chat -->
    <link href="css/chat.css" rel="stylesheet">
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
        <nav class="navbar navbar-expand bg-light navbar-light sticky-top px-4 py-0">
            <a href="index" class="navbar-brand d-flex d-lg-none me-4">
                <h2 class="text-primary mb-0"><i class="fa fa-user-edit"></i></h2>
            </a>
            <a href="#" class="sidebar-toggler flex-shrink-0">
                <i class="fa fa-bars"></i>
            </a>
            <form class="d-none d-md-flex ms-4">
                <input class="form-control border-0" type="search" placeholder="Buscar conversaciones...">
            </form>
            <div class="navbar-nav align-items-center ms-auto">
                <div class="nav-item dropdown">
                    <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                        <i class="fa fa-cog me-lg-2"></i>
                        <span class="d-none d-lg-inline-flex">Configuración</span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end bg-light border-0 rounded-0 rounded-bottom m-0">
                        <a href="#" class="dropdown-item" onclick="abrirConfiguracion()">Configuración del Chat</a>
                        <a href="#" class="dropdown-item" onclick="crearNuevaConversacion()">Nueva Conversación</a>
                        <a href="#" class="dropdown-item" onclick="exportarConversaciones()">Exportar Conversaciones</a>
                    </div>
                </div>
            </div>
        </nav>
        <!-- Navbar End -->

        <!-- Chat Container Start -->
        <div class="container-fluid pt-4 px-4">
            <div class="row g-4">
                <!-- Lista de Conversaciones -->
                <div class="col-lg-4">
                    <div class="bg-light rounded h-100 p-4">
                        <div class="d-flex align-items-center justify-content-between mb-4">
                            <h6 class="mb-0">Conversaciones</h6>
                            <button class="btn btn-primary btn-sm" onclick="crearNuevaConversacion()">
                                <i class="fa fa-plus"></i> Nueva
                            </button>
                        </div>
                        
                        <!-- Filtros -->
                        <div class="mb-3">
                            <div class="btn-group w-100" role="group">
                                <input type="radio" class="btn-check" name="filtroTipo" id="todos" value="todos" checked>
                                <label class="btn btn-outline-primary btn-sm" for="todos">Todos</label>
                                
                                <input type="radio" class="btn-check" name="filtroTipo" id="individual" value="individual">
                                <label class="btn btn-outline-primary btn-sm" for="individual">Individual</label>
                                
                                <input type="radio" class="btn-check" name="filtroTipo" id="grupo" value="grupo">
                                <label class="btn btn-outline-primary btn-sm" for="grupo">Grupo</label>
                                
                                <input type="radio" class="btn-check" name="filtroTipo" id="sucursal" value="sucursal">
                                <label class="btn btn-outline-primary btn-sm" for="sucursal">Sucursal</label>
                            </div>
                        </div>
                        
                        <!-- Lista de conversaciones -->
                        <div id="lista-conversaciones" class="conversaciones-lista">
                            <!-- Las conversaciones se cargarán aquí dinámicamente -->
                        </div>
                    </div>
                </div>
                
                <!-- Área de Chat -->
                <div class="col-lg-8">
                    <div class="bg-light rounded h-100 d-flex flex-column">
                        <!-- Header del chat -->
                        <div id="chat-header" class="chat-header p-3 border-bottom" style="display: none;">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <img id="chat-avatar" src="" alt="" class="rounded-circle" style="width: 40px; height: 40px;">
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 id="chat-nombre" class="mb-0"></h6>
                                    <small id="chat-info" class="text-muted"></small>
                                </div>
                                <div class="flex-shrink-0">
                                    <div class="dropdown">
                                        <button class="btn btn-link btn-sm" data-bs-toggle="dropdown">
                                            <i class="fa fa-ellipsis-v"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="#" onclick="verInfoConversacion()">Información</a></li>
                                            <li><a class="dropdown-item" href="#" onclick="agregarParticipantes()">Agregar personas</a></li>
                                            <li><a class="dropdown-item" href="#" onclick="exportarConversacion()">Exportar chat</a></li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li><a class="dropdown-item text-danger" href="#" onclick="abandonarConversacion()">Abandonar conversación</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Mensajes -->
                        <div id="chat-mensajes" class="chat-mensajes flex-grow-1 p-3" style="display: none;">
                            <!-- Los mensajes se cargarán aquí dinámicamente -->
                        </div>
                        
                        <!-- Input de mensaje -->
                        <div id="chat-input" class="chat-input p-3 border-top" style="display: none;">
                            <form id="form-mensaje" class="d-flex align-items-center">
                                <div class="flex-grow-1 me-2">
                                    <div class="input-group">
                                        <button type="button" class="btn btn-outline-secondary" onclick="abrirSelectorArchivos()">
                                            <i class="fa fa-paperclip"></i>
                                        </button>
                                        <input type="file" id="input-archivo" class="d-none" accept="image/*,video/*,audio/*,.pdf,.doc,.docx,.xls,.xlsx,.txt">
                                        <input type="text" id="input-mensaje" class="form-control" placeholder="Escribe un mensaje..." autocomplete="off">
                                        <button type="button" class="btn btn-outline-secondary" onclick="mostrarEmojis()">
                                            <i class="fa fa-smile"></i>
                                        </button>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-paper-plane"></i>
                                </button>
                            </form>
                        </div>
                        
                        <!-- Estado vacío -->
                        <div id="chat-vacio" class="d-flex align-items-center justify-content-center h-100">
                            <div class="text-center">
                                <i class="fa fa-comments fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">Selecciona una conversación</h5>
                                <p class="text-muted">Elige una conversación de la lista para comenzar a chatear</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Chat Container End -->
    </div>
    <!-- Content End -->

    <!-- Modal para nueva conversación -->
    <div class="modal fade" id="modalNuevaConversacion" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Nueva Conversación</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="formNuevaConversacion">
                        <div class="mb-3">
                            <label for="nombreConversacion" class="form-label">Nombre de la conversación</label>
                            <input type="text" class="form-control" id="nombreConversacion" required>
                        </div>
                        <div class="mb-3">
                            <label for="tipoConversacion" class="form-label">Tipo</label>
                            <select class="form-select" id="tipoConversacion">
                                <option value="individual">Individual</option>
                                <option value="grupo">Grupo</option>
                                <option value="sucursal">Sucursal</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="participantes" class="form-label">Participantes</label>
                            <select class="form-select" id="participantes" multiple>
                                <!-- Se cargarán dinámicamente -->
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="crearConversacion()">Crear</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para configuración -->
    <div class="modal fade" id="modalConfiguracion" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Configuración del Chat</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="formConfiguracion">
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="notificacionesSonido">
                                <label class="form-check-label" for="notificacionesSonido">
                                    Notificaciones de sonido
                                </label>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="notificacionesPush">
                                <label class="form-check-label" for="notificacionesPush">
                                    Notificaciones push
                                </label>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="temaOscuro">
                                <label class="form-check-label" for="temaOscuro">
                                    Tema oscuro
                                </label>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="mensajesPorPagina" class="form-label">Mensajes por página</label>
                            <select class="form-select" id="mensajesPorPagina">
                                <option value="25">25</option>
                                <option value="50" selected>50</option>
                                <option value="100">100</option>
                                <option value="200">200</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="guardarConfiguracion()">Guardar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Back to Top -->
    <a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top"><i class="bi bi-arrow-up"></i></a>

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="lib/chart/chart.min.js"></script>
    <script src="lib/easing/easing.min.js"></script>
    <script src="lib/waypoints/waypoints.min.js"></script>
    <script src="lib/owlcarousel/owl.carousel.min.js"></script>
    <script src="lib/tempusdominus/js/moment.min.js"></script>
    <script src="lib/tempusdominus/js/moment-timezone.min.js"></script>
    <script src="lib/tempusdominus/js/tempusdominus-bootstrap-4.min.js"></script>

    <!-- Template Javascript -->
    <script src="js/main.js"></script>
    
    <!-- Chat JavaScript -->
    <script src="js/chat.js"></script>
</body>
</html>
