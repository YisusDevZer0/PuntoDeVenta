<?php
include_once "Controladores/ControladorUsuario.php";

// Verificar sesión usando las variables correctas del sistema
if(!isset($_SESSION['ControlMaestro']) && !isset($_SESSION['AdministradorRH']) && !isset($_SESSION['Marketing'])){
    header("Location: Expiro.php");
    exit();
}

// Asegurar que $row esté disponible
if (!isset($row)) {
    // Si $row no está disponible, incluir nuevamente el controlador
    include_once "Controladores/ControladorUsuario.php";
}

// Determinar ID de usuario para JS
$userId = isset($_SESSION['ControlMaestro']) ? $_SESSION['ControlMaestro'] : (isset($_SESSION['AdministradorRH']) ? $_SESSION['AdministradorRH'] : $_SESSION['Marketing']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Sistema de Mensajes - Doctor Pez</title>
    
    <!-- Favicon -->
    <link href="img/favicon.ico" rel="icon">

    <!-- Google Web Fonts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Heebo:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Icon Font Stylesheet -->
    <script src="https://kit.fontawesome.com/a337b4cc32.js" crossorigin="anonymous"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Libraries Stylesheet -->
    <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">
    <link href="lib/tempusdominus/css/tempusdominus-bootstrap-4.min.css" rel="stylesheet" />

    <!-- Customized Bootstrap Stylesheet -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="css/style.css" rel="stylesheet">

    <!-- Notifications Stylesheet -->
    <link href="css/notifications.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.20/dist/sweetalert2.min.css">

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.20/dist/sweetalert2.all.min.js"></script>
    <script type="text/javascript" src="js/validation.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Chat Stylesheet -->
    <link href="css/chat.css" rel="stylesheet">

    <style>
        .chat-container {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        
        .chat-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            overflow: hidden;
            height: calc(100vh - 40px);
            display: flex;
        }
        
        .conversaciones-sidebar {
            width: 350px;
            background: #f8f9fa;
            border-right: 1px solid #e9ecef;
            display: flex;
            flex-direction: column;
        }
        
        .chat-main {
            flex: 1;
            display: flex;
            flex-direction: column;
            background: white;
        }
        
        .sidebar-header {
            background: linear-gradient(135deg, #009CFF, #007bff);
            color: white;
            padding: 20px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        
        .user-info {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }
        
        .user-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid rgba(255,255,255,0.3);
            margin-right: 12px;
        }
        
        .user-details h6 {
            margin: 0;
            font-weight: 600;
            font-size: 16px;
        }
        
        .user-role {
            opacity: 0.8;
            font-size: 12px;
        }
        
        .sidebar-actions {
            display: flex;
            gap: 8px;
        }
        
        .sidebar-actions .btn {
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid rgba(255,255,255,0.3);
            background: rgba(255,255,255,0.1);
            color: white;
            transition: all 0.3s ease;
        }
        
        .sidebar-actions .btn:hover {
            background: rgba(255,255,255,0.2);
            transform: translateY(-2px);
        }
        
        .search-container {
            padding: 15px 20px;
            background: #f8f9fa;
            border-bottom: 1px solid #e9ecef;
        }
        
        .conversaciones-lista {
            flex: 1;
            overflow-y: auto;
            padding: 0;
        }
        
        .conversacion-item {
            padding: 15px 20px;
            border-bottom: 1px solid #e9ecef;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            position: relative;
        }
        
        .conversacion-item:hover {
            background: #e9ecef;
        }
        
        .conversacion-item.active {
            background: linear-gradient(90deg, rgba(0,156,255,0.1), transparent);
            border-left: 4px solid #009CFF;
        }
        
        .conversacion-avatar {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 12px;
            border: 2px solid #e9ecef;
        }
        
        .conversacion-info {
            flex: 1;
            min-width: 0;
        }
        
        .conversacion-nombre {
            font-weight: 600;
            font-size: 14px;
            color: #495057;
            margin: 0 0 4px 0;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .conversacion-ultimo-mensaje {
            font-size: 12px;
            color: #6c757d;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            margin: 0;
        }
        
        .conversacion-meta {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 4px;
        }
        
        .conversacion-hora {
            font-size: 11px;
            color: #adb5bd;
        }
        
        .conversacion-badge {
            background: #009CFF;
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            font-weight: bold;
        }
        
        .conversacion-tipo {
            position: absolute;
            top: 10px;
            right: 10px;
            font-size: 10px;
            padding: 2px 6px;
            border-radius: 10px;
            background: #e9ecef;
            color: #6c757d;
        }
        
        .chat-vacio {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        }
        
        .chat-header {
            padding: 15px 20px;
            background: white;
            border-bottom: 1px solid #e9ecef;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .chat-header-info {
            display: flex;
            align-items: center;
        }
        
        .chat-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 12px;
            border: 2px solid #e9ecef;
        }
        
        .chat-info h6 {
            margin: 0;
            font-weight: 600;
            color: #495057;
        }
        
        .chat-descripcion {
            color: #6c757d;
            font-size: 12px;
        }
        
        .chat-actions {
            display: flex;
            gap: 8px;
        }
        
        .chat-actions .btn {
            border-radius: 50%;
            width: 35px;
            height: 35px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #dee2e6;
            background: white;
            color: #6c757d;
            transition: all 0.3s ease;
        }
        
        .chat-actions .btn:hover {
            background: #009CFF;
            color: white;
            border-color: #009CFF;
        }
        
        .chat-mensajes {
            flex: 1;
            overflow-y: auto;
            background: linear-gradient(180deg, #f8f9fa 0%, #ffffff 100%);
            position: relative;
        }
        
        .mensajes-container {
            padding: 20px;
            min-height: 100%;
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        
        .mensaje {
            display: flex;
            margin-bottom: 15px;
            animation: fadeInUp 0.3s ease;
        }
        
        .mensaje.propio {
            justify-content: flex-end;
        }
        
        .mensaje.otro {
            justify-content: flex-start;
        }
        
        .mensaje-burbuja {
            max-width: 70%;
            padding: 12px 16px;
            border-radius: 18px;
            position: relative;
            word-wrap: break-word;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        
        .mensaje.propio .mensaje-burbuja {
            background: linear-gradient(135deg, #009CFF, #007bff);
            color: white;
            border-bottom-right-radius: 4px;
        }
        
        .mensaje.otro .mensaje-burbuja {
            background: white;
            color: #495057;
            border: 1px solid #e9ecef;
            border-bottom-left-radius: 4px;
        }
        
        .mensaje-contenido {
            margin: 0;
            line-height: 1.4;
        }
        
        .mensaje-meta {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-top: 4px;
            font-size: 11px;
            opacity: 0.7;
        }
        
        .mensaje-hora {
            font-size: 10px;
        }
        
        .mensaje-estado {
            font-size: 10px;
        }
        
        .mensaje-tipo {
            font-size: 10px;
            padding: 2px 6px;
            border-radius: 10px;
            background: rgba(255,255,255,0.2);
        }
        
        .chat-input-area {
            padding: 20px;
            background: white;
            border-top: 1px solid #e9ecef;
            box-shadow: 0 -2px 4px rgba(0,0,0,0.1);
        }
        
        .chat-input-area .input-group {
            border-radius: 25px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .chat-input-area .btn {
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: none;
            transition: all 0.3s ease;
        }
        
        .chat-input-area .btn-outline-secondary {
            background: #f8f9fa;
            color: #6c757d;
            border: 1px solid #dee2e6;
        }
        
        .chat-input-area .btn-outline-secondary:hover {
            background: #e9ecef;
            color: #495057;
        }
        
        .chat-input-area .btn-primary {
            background: #009CFF;
            color: white;
        }
        
        .chat-input-area .btn-primary:hover {
            background: #007bff;
            transform: translateY(-2px);
        }
        
        .chat-input-area .form-control {
            border: none;
            padding: 12px 16px;
            font-size: 14px;
            background: white;
        }
        
        .chat-input-area .form-control:focus {
            box-shadow: none;
            border: none;
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @media (max-width: 768px) {
            .chat-card {
                height: calc(100vh - 20px);
                flex-direction: column;
            }
            
            .conversaciones-sidebar {
                width: 100%;
                height: 200px;
                border-right: none;
                border-bottom: 1px solid #e9ecef;
            }
            
            .conversaciones-lista {
                height: 120px;
            }
            
            .chat-mensajes {
                height: calc(100vh - 300px);
            }
        }
    </style>
</head>
<body>
    <div class="chat-container">
        <div class="chat-card">
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
                        <button class="btn btn-sm" onclick="crearNuevaConversacion()" title="Nueva conversación">
                            <i class="fas fa-plus"></i>
                        </button>
                        <button class="btn btn-sm" onclick="abrirConfiguracion()" title="Configuración">
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
                        <button class="btn btn-sm" onclick="verInfoConversacion()" title="Información">
                            <i class="fas fa-info-circle"></i>
                        </button>
                        <button class="btn btn-sm" onclick="abrirConfiguracion()" title="Configuración">
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
                        <button class="btn btn-outline-secondary" onclick="abrirSelectorArchivos()" title="Adjuntar archivo">
                            <i class="fas fa-paperclip"></i>
                        </button>
                        <button class="btn btn-outline-secondary" onclick="mostrarEmojis()" title="Emojis">
                            <i class="fas fa-smile"></i>
                        </button>
                        <input type="text" class="form-control" placeholder="Escribe un mensaje..." id="input-mensaje">
                        <button class="btn btn-primary" onclick="enviarMensaje()" title="Enviar">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </div>
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
    <script>
        // Variables globales
        window.usuarioId = <?php echo $userId; ?>;
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