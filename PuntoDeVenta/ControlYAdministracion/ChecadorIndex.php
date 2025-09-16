<?php
include_once "Controladores/ControladorUsuario.php";

// Verificar sesión usando las variables correctas del sistema
if(!isset($_SESSION['ControlMaestro']) && !isset($_SESSION['AdministradorRH']) && !isset($_SESSION['Marketing'])){
    header("Location: Expiro.php");
    exit();
}

// Asegurar que $row esté disponible
if (!isset($row)) {
    include_once "Controladores/ControladorUsuario.php";
}

// Determinar ID de usuario para JS
$userId = isset($_SESSION['ControlMaestro']) ? $_SESSION['ControlMaestro'] : (isset($_SESSION['AdministradorRH']) ? $_SESSION['AdministradorRH'] : $_SESSION['Marketing']);

// Verificar si es administrador
$isAdmin = ($row['TipoUsuario'] == 'Administrador' || $row['TipoUsuario'] == 'MKT');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Checador - Sistema de Control</title>
    
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

    <style>
        .checador-index-container {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        
        .checador-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            overflow: hidden;
            max-width: 1000px;
            margin: 0 auto;
        }
        
        .header-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px;
            text-align: center;
        }
        
        .header-section h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 10px;
        }
        
        .header-section p {
            font-size: 1.1rem;
            opacity: 0.9;
        }
        
        .user-info {
            background: rgba(255,255,255,0.1);
            border-radius: 15px;
            padding: 20px;
            margin-top: 20px;
            backdrop-filter: blur(10px);
        }
        
        .user-avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            border: 3px solid white;
            margin: 0 auto 15px;
            display: block;
        }
        
        .user-name {
            font-size: 1.3rem;
            font-weight: 600;
            margin-bottom: 5px;
        }
        
        .user-role {
            font-size: 1rem;
            opacity: 0.8;
        }
        
        .options-section {
            padding: 40px;
        }
        
        .options-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            margin-bottom: 30px;
        }
        
        .option-card {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 30px;
            text-align: center;
            transition: all 0.3s ease;
            border: 2px solid transparent;
            cursor: pointer;
        }
        
        .option-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            border-color: #667eea;
        }
        
        .option-icon {
            font-size: 3rem;
            margin-bottom: 20px;
            color: #667eea;
        }
        
        .option-title {
            font-size: 1.4rem;
            font-weight: 600;
            color: #495057;
            margin-bottom: 10px;
        }
        
        .option-description {
            color: #6c757d;
            font-size: 1rem;
            line-height: 1.5;
        }
        
        .admin-section {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 15px;
            padding: 20px;
            margin-top: 30px;
        }
        
        .admin-section h3 {
            color: #856404;
            margin-bottom: 15px;
        }
        
        .admin-options {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
        }
        
        .admin-option {
            background: white;
            border: 1px solid #ffeaa7;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        
        .admin-option:hover {
            background: #f8f9fa;
            transform: translateY(-2px);
        }
        
        .admin-option i {
            font-size: 2rem;
            color: #856404;
            margin-bottom: 10px;
        }
        
        .status-bar {
            background: #e9ecef;
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }
        
        .status-indicator {
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 14px;
        }
        
        .status-outside {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }
        
        .status-inside {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .back-button {
            position: absolute;
            top: 20px;
            left: 20px;
            background: rgba(255,255,255,0.2);
            border: none;
            color: white;
            padding: 10px 15px;
            border-radius: 25px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .back-button:hover {
            background: rgba(255,255,255,0.3);
            transform: translateY(-2px);
        }
        
        @media (max-width: 768px) {
            .options-grid {
                grid-template-columns: 1fr;
            }
            
            .header-section h1 {
                font-size: 2rem;
            }
            
            .admin-options {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <div class="checador-index-container">
        <div class="checador-card">
            <!-- Header Section -->
            <div class="header-section">
                <?php if ($isAdmin): ?>
                <button class="back-button" onclick="window.location.href='index.php'">
                    <i class="fas fa-arrow-left"></i> Volver al POS
                </button>
                <?php endif; ?>
                
                <h1><i class="fas fa-clock"></i> Sistema de Checador</h1>
                <p>Control de asistencia y ubicaciones de trabajo</p>
                
                <div class="user-info">
                    <img src="https://doctorpez.mx/PuntoDeVenta/PerfilesImg/<?php echo isset($row['file_name']) ? $row['file_name'] : 'user.jpg'; ?>" 
                         alt="Usuario" class="user-avatar" 
                         onerror="this.src='img/user.jpg'; this.onerror=null;">
                    <div class="user-name"><?php echo isset($row['Nombre_Apellidos']) ? $row['Nombre_Apellidos'] : 'Usuario'; ?></div>
                    <div class="user-role"><?php echo isset($row['TipoUsuario']) ? $row['TipoUsuario'] : 'Usuario'; ?></div>
                </div>
            </div>

            <!-- Status Bar -->
            <div class="status-bar">
                <div class="status-indicator" id="statusIndicator">
                    <i class="fas fa-map-marker-alt"></i>
                    <span id="statusText">Estado: Verificando ubicación...</span>
                </div>
                <div>
                    <small>Última verificación: <span id="lastVerification">--</span></small>
                </div>
            </div>

            <!-- Options Section -->
            <div class="options-section">
                <div class="options-grid">
                    <div class="option-card" onclick="window.location.href='Checador.php'">
                        <div class="option-icon">
                            <i class="fas fa-sign-in-alt"></i>
                        </div>
                        <div class="option-title">Registrar Asistencia</div>
                        <div class="option-description">
                            Registra tu entrada y salida del trabajo con verificación de ubicación
                        </div>
                    </div>
                    
                    <div class="option-card" onclick="window.location.href='ChecadorDiario.php'">
                        <div class="option-icon">
                            <i class="fas fa-calendar-day"></i>
                        </div>
                        <div class="option-title">Reporte Diario</div>
                        <div class="option-description">
                            Consulta tus registros de asistencia del día actual
                        </div>
                    </div>
                    
                    <div class="option-card" onclick="window.location.href='ChecadorGeneral.php'">
                        <div class="option-icon">
                            <i class="fas fa-calendar"></i>
                        </div>
                        <div class="option-title">Reporte General</div>
                        <div class="option-description">
                            Consulta tu historial completo de asistencias
                        </div>
                    </div>
                    
                    <div class="option-card" onclick="window.location.href='ConfiguracionUbicaciones.php'">
                        <div class="option-icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div class="option-title">Configurar Ubicaciones</div>
                        <div class="option-description">
                            Configura tus ubicaciones de trabajo para el checador
                        </div>
                    </div>
                </div>

                <?php if ($isAdmin): ?>
                <!-- Admin Section -->
                <div class="admin-section">
                    <h3><i class="fas fa-cog"></i> Administración del Checador</h3>
                    <div class="admin-options">
                        <div class="admin-option" onclick="window.location.href='ChecadorAdmin.php'">
                            <i class="fas fa-users-cog"></i>
                            <div>Gestionar Usuarios</div>
                        </div>
                        <div class="admin-option" onclick="window.location.href='ChecadorCentrosTrabajo.php'">
                            <i class="fas fa-building"></i>
                            <div>Centros de Trabajo</div>
                        </div>
                        <div class="admin-option" onclick="window.location.href='ChecadorReportes.php'">
                            <i class="fas fa-chart-bar"></i>
                            <div>Reportes Avanzados</div>
                        </div>
                        <div class="admin-option" onclick="window.location.href='ChecadorConfiguracion.php'">
                            <i class="fas fa-sliders-h"></i>
                            <div>Configuración</div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        // Variables globales
        let userLocation = null;
        let isInWorkArea = false;
        
        // Inicializar al cargar
        document.addEventListener('DOMContentLoaded', function() {
            updateDateTime();
            setInterval(updateDateTime, 1000);
            checkLocation();
        });
        
        // Actualizar fecha y hora
        function updateDateTime() {
            const now = new Date();
            const formattedDate = now.toLocaleDateString('es-MX') + ', ' + 
                                now.toLocaleTimeString('es-MX', { 
                                    hour: '2-digit', 
                                    minute: '2-digit',
                                    second: '2-digit',
                                    hour12: true 
                                });
            
            const lastVerification = document.getElementById('lastVerification');
            if (lastVerification) {
                lastVerification.textContent = formattedDate;
            }
        }
        
        // Verificar ubicación
        async function checkLocation() {
            try {
                if (!navigator.geolocation) {
                    updateStatus('outside', 'Geolocalización no disponible');
                    return;
                }

                const position = await getCurrentPosition();
                userLocation = {
                    lat: position.coords.latitude,
                    lng: position.coords.longitude
                };
                
                // Verificar si está en área de trabajo
                await verifyWorkArea();
                
            } catch (error) {
                console.log('Error obteniendo ubicación:', error);
                updateStatus('outside', 'Ubicación no disponible');
            }
        }
        
        // Obtener posición actual
        function getCurrentPosition() {
            return new Promise((resolve, reject) => {
                navigator.geolocation.getCurrentPosition(
                    resolve,
                    reject,
                    {
                        enableHighAccuracy: false,
                        timeout: 10000,
                        maximumAge: 300000 // 5 minutos de cache
                    }
                );
            });
        }
        
        // Verificar área de trabajo
        async function verifyWorkArea() {
            if (!userLocation) {
                isInWorkArea = false;
                updateStatus('outside', 'Sin ubicación');
                return;
            }
            
            try {
                const response = await makeRequest('verificar_ubicacion', {
                    latitud: userLocation.lat,
                    longitud: userLocation.lng
                });
                
                if (response.success) {
                    isInWorkArea = response.en_area;
                    
                    if (isInWorkArea) {
                        updateStatus('inside', 'En el área de trabajo');
                    } else {
                        updateStatus('outside', 'Fuera del área');
                    }
                } else {
                    updateStatus('outside', 'Error verificando ubicación');
                }
            } catch (error) {
                console.error('Error verificando área:', error);
                updateStatus('outside', 'Error de conexión');
            }
        }
        
        // Actualizar estado visual
        function updateStatus(type, message) {
            const indicator = document.getElementById('statusIndicator');
            const statusText = document.getElementById('statusText');
            
            indicator.className = 'status-indicator';
            if (type === 'inside') {
                indicator.classList.add('status-inside');
            } else {
                indicator.classList.add('status-outside');
            }
            
            if (statusText) {
                statusText.textContent = `Estado: ${message}`;
            }
        }
        
        // Realizar petición al servidor
        async function makeRequest(action, data = {}) {
            const formData = new FormData();
            formData.append('action', action);
            
            for (const [key, value] of Object.entries(data)) {
                formData.append(key, value);
            }

            const response = await fetch('Controladores/ChecadorController.php', {
                method: 'POST',
                body: formData
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            return await response.json();
        }
    </script>
</body>
</html>
