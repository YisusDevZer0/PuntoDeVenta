<?php
include_once "Controladores/ControladorUsuario.php";

// Verificar sesión usando las variables correctas del sistema PuntoDeVentaFarmacias
if(!isset($_SESSION['VentasPos'])){
    header("Location: Expiro.php");
    exit();
}

// Asegurar que $row esté disponible
if (!isset($row)) {
    // Si $row no está disponible, incluir nuevamente el controlador
    include_once "Controladores/ControladorUsuario.php";
}
// Determinar ID de usuario para JS
$userId = $_SESSION['VentasPos'];
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

    <!-- Checador JavaScript -->
    <script src="js/checador.js"></script>

    <style>
        .checador-container {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        
        .checador-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            overflow: hidden;
            max-width: 800px;
            margin: 0 auto;
        }
        
        .status-bar {
            background: #f8f9fa;
            padding: 15px 20px;
            border-bottom: 1px solid #e9ecef;
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
        
        .info-panel {
            padding: 30px;
            text-align: center;
        }
        
        .location-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 15px;
        }
        
        .location-left {
            text-align: left;
        }
        
        .location-right {
            text-align: right;
        }
        
        .location-title {
            font-size: 18px;
            font-weight: 700;
            color: #495057;
            margin-bottom: 5px;
        }
        
        .location-value {
            font-size: 16px;
            color: #6c757d;
        }
        
        .clock-display {
            font-size: 48px;
            font-weight: 700;
            color: #495057;
            margin: 20px 0;
            font-family: 'Courier New', monospace;
        }
        
        .weather-panel {
            background: #e9ecef;
            padding: 20px;
            border-radius: 15px;
            margin-bottom: 30px;
        }
        
        .weather-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            gap: 15px;
            margin-top: 15px;
        }
        
        .weather-item {
            text-align: center;
            padding: 10px;
            background: white;
            border-radius: 10px;
        }
        
        .weather-label {
            font-size: 12px;
            color: #6c757d;
            margin-bottom: 5px;
        }
        
        .weather-value {
            font-size: 16px;
            font-weight: 600;
            color: #495057;
        }
        
        .attendance-section {
            padding: 30px;
            background: #f8f9fa;
        }
        
        .attendance-title {
            text-align: center;
            font-size: 24px;
            font-weight: 700;
            color: #495057;
            margin-bottom: 10px;
        }
        
        .attendance-subtitle {
            text-align: center;
            font-size: 16px;
            color: #6c757d;
            margin-bottom: 30px;
        }
        
        .attendance-buttons {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        
        .attendance-button {
            padding: 30px 20px;
            border-radius: 15px;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 120px;
        }
        
        .attendance-button:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        
        .attendance-button.entry {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
        }
        
        .attendance-button.exit {
            background: linear-gradient(135deg, #dc3545, #fd7e14);
            color: white;
        }
        
        .attendance-button:hover:not(:disabled) {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        }
        
        .attendance-icon {
            font-size: 32px;
            margin-bottom: 10px;
        }
        
        .attendance-text {
            font-size: 16px;
            font-weight: 600;
        }
        
        .attendance-description {
            font-size: 12px;
            opacity: 0.9;
            margin-top: 5px;
        }
        
        .location-setup {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            text-align: center;
        }
        
        .location-setup h5 {
            color: #856404;
            margin-bottom: 15px;
        }
        
        .btn-setup-location {
            background: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 25px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .btn-setup-location:hover {
            background: #0056b3;
            transform: translateY(-2px);
        }
        
        @media (max-width: 768px) {
            .location-info {
                flex-direction: column;
                text-align: center;
            }
            
            .location-left, .location-right {
                text-align: center;
                margin-bottom: 10px;
            }
            
            .clock-display {
                font-size: 36px;
            }
            
            .attendance-buttons {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <script>
        window.CHECK_USER_ID = <?php echo intval($userId); ?>;
        // Habilitar botones al cargar
        window.addEventListener('DOMContentLoaded', function(){
            const be = document.getElementById('btnEntry');
            const bs = document.getElementById('btnExit');
            if (be) be.disabled = false;
            if (bs) bs.disabled = false;
        });
    </script>
    <div class="checador-container">
        <div class="checador-card">
            <!-- Barra de Estado -->
            <div class="status-bar">
                <div class="status-indicator" id="statusIndicator">
                    <i class="fas fa-map-marker-alt"></i>
                    <span id="statusText">Estado: Verificando ubicación...</span>
                </div>
                <div>
                    <small>Última verificación: <span id="lastVerification">--</span></small><br>
                    <small>Centro de trabajo: <span id="workCenter">--</span></small>
                    <br>
                    <button class="btn btn-sm btn-outline-primary" onclick="checkLocationManual()" style="margin-top: 5px;">
                        <i class="fas fa-sync-alt"></i> Verificar Ubicación
                    </button>
                    <br>
                    <a href="test_checador.php" class="btn btn-sm btn-outline-info" style="margin-top: 5px;">
                        <i class="fas fa-bug"></i> Probar Sistema
                    </a>
                    <br>
                    <a href="test_checador_connection.php" class="btn btn-sm btn-outline-warning" style="margin-top: 5px;">
                        <i class="fas fa-database"></i> Probar Conexión
                    </a>
                    <br>
                    <a href="test_controller_direct.php" class="btn btn-sm btn-outline-danger" style="margin-top: 5px;">
                        <i class="fas fa-code"></i> Probar Controlador
                    </a>
                </div>
            </div>

            <!-- Panel de Información -->
            <div class="info-panel">
                <!-- Información de Ubicación -->
                <div class="location-info">
                    <div class="location-left">
                        <div class="location-title">UBICACIÓN</div>
                        <div class="location-value" id="currentLocation">Cargando...</div>
                    </div>
                    <div class="location-right">
                        <div class="clock-display" id="currentTime">--:--</div>
                        <div class="location-title">FECHA</div>
                        <div class="location-value" id="currentDate">--</div>
                    </div>
                </div>

                <!-- Panel del Clima -->
                <div class="weather-panel">
                    <div class="weather-grid">
                        <div class="weather-item">
                            <div class="weather-label">Temperatura</div>
                            <div class="weather-value" id="temperature">--°C</div>
                        </div>
                        <div class="weather-item">
                            <div class="weather-label">Condiciones</div>
                            <div class="weather-value" id="conditions">--</div>
                        </div>
                        <div class="weather-item">
                            <div class="weather-label">Humedad</div>
                            <div class="weather-value" id="humidity">--%</div>
                        </div>
                        <div class="weather-item">
                            <div class="weather-label">Viento</div>
                            <div class="weather-value" id="wind">-- km/h</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Configuración de Ubicación -->
            <div class="location-setup" id="locationSetup" style="display: none;">
                <h5><i class="fas fa-exclamation-triangle"></i> Configuración Requerida</h5>
                <p>Necesitas configurar tu ubicación de trabajo para poder registrar asistencia.</p>
                <button class="btn-setup-location" onclick="setupLocation()">
                    <i class="fas fa-map-marker-alt"></i> Configurar Ubicación
                </button>
            </div>

            <!-- Sección de Registro de Asistencia -->
            <div class="attendance-section">
                <div class="attendance-title">Registro de Asistencia</div>
                <div class="attendance-subtitle">¿Qué deseas registrar?</div>
                
                <div class="attendance-buttons">
                    <button class="attendance-button entry" id="btnEntry" onclick="registrarEntrada()" disabled>
                        <div class="attendance-icon">
                            <i class="fas fa-sign-in-alt"></i>
                        </div>
                        <div class="attendance-text">Entrada</div>
                        <div class="attendance-description">Registro de entrada</div>
                    </button>
                    
                    <button class="attendance-button exit" id="btnExit" onclick="registrarSalida()" disabled>
                        <div class="attendance-icon">
                            <i class="fas fa-sign-out-alt"></i>
                        </div>
                        <div class="attendance-text">Salida</div>
                        <div class="attendance-description">Registro de salida</div>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Actualizar clima (simulado)
        function updateWeather() {
            document.getElementById('temperature').textContent = '28°C';
            document.getElementById('conditions').textContent = 'Algo De Nubes';
            document.getElementById('humidity').textContent = '74%';
            document.getElementById('wind').textContent = '11 km/h';
        }

        // Actualizar clima cada 30 minutos
        updateWeather();
        setInterval(updateWeather, 1800000);
    </script>
</body>
</html>
