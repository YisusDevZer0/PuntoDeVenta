<?php
session_start();

// Incluir conexión a la base de datos
include_once "Controladores/db_connect.php";

// Obtener datos del usuario actual
$usuarioId = isset($_SESSION['ControlMaestro']) ? $_SESSION['ControlMaestro'] : 
            (isset($_SESSION['AdministradorRH']) ? $_SESSION['AdministradorRH'] : $_SESSION['Marketing']);

// Obtener información del usuario
$sql = "SELECT u.*, s.Nombre_Sucursal, t.TipoUsuario 
        FROM Usuarios_PV u 
        LEFT JOIN Sucursales s ON u.Fk_Sucursal = s.ID_Sucursal 
        LEFT JOIN Tipos_Usuarios t ON u.Fk_Usuario = t.ID_User 
        WHERE u.Id_PvUser = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $usuarioId);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Mensajes - Doctor Pez</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="css/chat.css" rel="stylesheet">
</head>
<body>
    <div class="chat-container">
        <!-- Sidebar de conversaciones -->
        <div class="conversaciones-sidebar">
            <!-- Header del sidebar -->
            <div class="sidebar-header">
                <div class="user-info">
                    <img src="PerfilesImg/<?php echo $row['file_name'] ?? 'user.jpg'; ?>" alt="Avatar" class="user-avatar">
                    <div class="user-details">
                        <h6 class="user-name"><?php echo $row['Nombre_Apellidos'] ?? 'Usuario'; ?></h6>
                        <small class="user-role"><?php echo $row['TipoUsuario'] ?? 'Usuario'; ?></small>
                    </div>
                </div>
                <div class="sidebar-actions">
                    <button class="btn btn-sm btn-primary" onclick="crearNuevaConversacion()">
                        <i class="fas fa-plus"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-secondary" onclick="abrirConfiguracion()">
                        <i class="fas fa-cog"></i>
                    </button>
                </div>
            </div>

            <!-- Búsqueda -->
            <div class="search-container">
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="Buscar conversaciones..." id="buscar-conversaciones">
                    <button class="btn btn-outline-secondary" type="button">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>

            <!-- Lista de conversaciones -->
            <div class="conversaciones-lista" id="conversaciones-lista">
                <div class="text-center p-3">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Área principal del chat -->
        <div class="chat-main">
            <!-- Estado vacío -->
            <div class="chat-vacio" id="chat-vacio">
                <div class="text-center">
                    <i class="fas fa-comments fa-3x text-muted mb-3"></i>
                    <h4>Selecciona una conversación</h4>
                    <p class="text-muted">Elige una conversación del panel izquierdo para comenzar a chatear</p>
                </div>
            </div>

            <!-- Header del chat -->
            <div class="chat-header" id="chat-header" style="display: none;">
                <div class="chat-header-info">
                    <img src="" alt="Avatar" class="chat-avatar" id="chat-avatar">
                    <div class="chat-info">
                        <h6 class="chat-nombre" id="chat-nombre"></h6>
                        <small class="chat-descripcion" id="chat-descripcion"></small>
                    </div>
                </div>
                <div class="chat-actions">
                    <button class="btn btn-sm btn-outline-secondary" onclick="verInfoConversacion()">
                        <i class="fas fa-info-circle"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-secondary" onclick="abrirConfiguracion()">
                        <i class="fas fa-cog"></i>
                    </button>
                </div>
            </div>

            <!-- Área de mensajes -->
            <div class="chat-mensajes" id="chat-mensajes" style="display: none;">
                <div class="mensajes-container" id="mensajes-container">
                    <!-- Los mensajes se cargarán aquí -->
                </div>
            </div>

            <!-- Área de entrada -->
            <div class="chat-input-area" id="chat-input" style="display: none;">
                <div class="input-group">
                    <button class="btn btn-outline-secondary" onclick="abrirSelectorArchivos()">
                        <i class="fas fa-paperclip"></i>
                    </button>
                    <button class="btn btn-outline-secondary" onclick="mostrarEmojis()">
                        <i class="fas fa-smile"></i>
                    </button>
                    <input type="text" class="form-control" placeholder="Escribe un mensaje..." id="input-mensaje">
                    <button class="btn btn-primary" onclick="enviarMensaje()">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

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
                            <label class="form-label">Nombre de la conversación</label>
                            <input type="text" class="form-control" id="nombreConversacion" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tipo de conversación</label>
                            <select class="form-select" id="tipoConversacion" required>
                                <option value="individual">Individual</option>
                                <option value="grupo">Grupo</option>
                                <option value="sucursal">Sucursal</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Participantes</label>
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

    <!-- Modal de configuración -->
    <div class="modal fade" id="modalConfiguracion" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Configuración del Chat</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
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
                        <label class="form-label">Mensajes por página</label>
                        <select class="form-select" id="mensajesPorPagina">
                            <option value="25">25 mensajes</option>
                            <option value="50" selected>50 mensajes</option>
                            <option value="100">100 mensajes</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="guardarConfiguracion()">Guardar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Input oculto para archivos -->
    <input type="file" id="input-archivo" style="display: none;" multiple>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Variables globales
        window.usuarioId = <?php echo $usuarioId; ?>;
        window.nombreUsuario = '<?php echo addslashes($row['Nombre_Apellidos']); ?>';
        window.sucursalId = <?php echo $row['Fk_Sucursal']; ?>;
        
        // Inicializar sistema de chat
        document.addEventListener('DOMContentLoaded', function() {
            window.chatSystem = new ChatSystem();
            window.chatSystem.init();
        });
    </script>
    <script src="js/chat.js"></script>
    <script src="js/notificaciones.js"></script>
</body>
</html>