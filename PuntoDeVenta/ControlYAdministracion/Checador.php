<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit();
}
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
        let userLocation = null;
        let workLocation = null;
        let isInWorkArea = false;
        let currentPosition = null;

        // Inicializar la aplicación
        document.addEventListener('DOMContentLoaded', function() {
            updateDateTime();
            setInterval(updateDateTime, 1000);
            checkLocation();
            loadWorkLocation();
        });

        // Actualizar fecha y hora
        function updateDateTime() {
            const now = new Date();
            const timeString = now.toLocaleTimeString('es-MX', { 
                hour: '2-digit', 
                minute: '2-digit',
                second: '2-digit',
                hour12: true 
            });
            const dateString = now.toLocaleDateString('es-MX', { 
                day: '2-digit',
                month: '2-digit',
                year: '2-digit'
            });
            const dayString = now.toLocaleDateString('es-MX', { weekday: 'short' }).toUpperCase();

            document.getElementById('currentTime').textContent = timeString;
            document.getElementById('currentDate').textContent = `${dateString} ${dayString}`;
        }

        // Verificar ubicación del usuario
        function checkLocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        currentPosition = position;
                        userLocation = {
                            lat: position.coords.latitude,
                            lng: position.coords.longitude
                        };
                        
                        document.getElementById('currentLocation').textContent = 'Ubicación detectada';
                        checkIfInWorkArea();
                        updateLastVerification();
                    },
                    function(error) {
                        console.error('Error obteniendo ubicación:', error);
                        document.getElementById('currentLocation').textContent = 'Error obteniendo ubicación';
                        updateStatus('error', 'No se pudo obtener la ubicación');
                    },
                    {
                        enableHighAccuracy: true,
                        timeout: 10000,
                        maximumAge: 60000
                    }
                );
            } else {
                document.getElementById('currentLocation').textContent = 'Geolocalización no soportada';
                updateStatus('error', 'Geolocalización no disponible');
            }
        }

        // Cargar ubicación de trabajo
        function loadWorkLocation() {
            // Aquí cargarías la ubicación de trabajo desde la base de datos
            // Por ahora usamos una ubicación de ejemplo
            workLocation = {
                lat: 20.9674, // Mérida, Yucatán
                lng: -89.5926,
                name: 'Casa Eduardo Mutul'
            };
            
            document.getElementById('workCenter').textContent = workLocation.name;
        }

        // Verificar si está en el área de trabajo
        function checkIfInWorkArea() {
            if (!userLocation || !workLocation) {
                isInWorkArea = false;
                updateStatus('outside', 'Fuera del área');
                return;
            }

            const distance = calculateDistance(
                userLocation.lat, userLocation.lng,
                workLocation.lat, workLocation.lng
            );

            // Radio de 100 metros para considerar que está en el área de trabajo
            isInWorkArea = distance <= 0.1; // 100 metros

            if (isInWorkArea) {
                updateStatus('inside', 'En el área de trabajo');
                enableButtons();
                hideLocationSetup();
            } else {
                updateStatus('outside', 'Fuera del área');
                disableButtons();
                showLocationSetup();
            }
        }

        // Calcular distancia entre dos puntos
        function calculateDistance(lat1, lon1, lat2, lon2) {
            const R = 6371; // Radio de la Tierra en km
            const dLat = (lat2 - lat1) * Math.PI / 180;
            const dLon = (lon2 - lon1) * Math.PI / 180;
            const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                      Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                      Math.sin(dLon/2) * Math.sin(dLon/2);
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
            return R * c; // Distancia en km
        }

        // Actualizar estado
        function updateStatus(type, message) {
            const indicator = document.getElementById('statusIndicator');
            const statusText = document.getElementById('statusText');
            
            indicator.className = 'status-indicator';
            if (type === 'inside') {
                indicator.classList.add('status-inside');
            } else if (type === 'outside') {
                indicator.classList.add('status-outside');
            } else {
                indicator.classList.add('status-outside');
            }
            
            statusText.textContent = `Estado: ${message}`;
        }

        // Habilitar botones
        function enableButtons() {
            document.getElementById('btnEntry').disabled = false;
            document.getElementById('btnExit').disabled = false;
        }

        // Deshabilitar botones
        function disableButtons() {
            document.getElementById('btnEntry').disabled = true;
            document.getElementById('btnExit').disabled = true;
        }

        // Mostrar configuración de ubicación
        function showLocationSetup() {
            document.getElementById('locationSetup').style.display = 'block';
        }

        // Ocultar configuración de ubicación
        function hideLocationSetup() {
            document.getElementById('locationSetup').style.display = 'none';
        }

        // Configurar ubicación
        function setupLocation() {
            Swal.fire({
                title: 'Configurar Ubicación',
                text: '¿Deseas configurar esta ubicación como tu centro de trabajo?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Sí, configurar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed && currentPosition) {
                    // Aquí guardarías la ubicación en la base de datos
                    workLocation = {
                        lat: currentPosition.coords.latitude,
                        lng: currentPosition.coords.longitude,
                        name: 'Ubicación configurada'
                    };
                    
                    document.getElementById('workCenter').textContent = workLocation.name;
                    checkIfInWorkArea();
                    
                    Swal.fire(
                        '¡Configurado!',
                        'Tu ubicación de trabajo ha sido configurada.',
                        'success'
                    );
                }
            });
        }

        // Actualizar última verificación
        function updateLastVerification() {
            const now = new Date();
            const formattedDate = now.toLocaleDateString('es-MX') + ', ' + 
                                now.toLocaleTimeString('es-MX', { 
                                    hour: '2-digit', 
                                    minute: '2-digit',
                                    second: '2-digit',
                                    hour12: true 
                                });
            document.getElementById('lastVerification').textContent = formattedDate;
        }

        // Registrar entrada
        function registrarEntrada() {
            if (!isInWorkArea) {
                Swal.fire('Error', 'Debes estar en el área de trabajo para registrar entrada.', 'error');
                return;
            }

            Swal.fire({
                title: 'Registrando Entrada',
                text: '¿Confirmas tu entrada?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Sí, registrar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Aquí enviarías los datos al servidor
                    const data = {
                        tipo: 'entrada',
                        latitud: userLocation.lat,
                        longitud: userLocation.lng,
                        timestamp: new Date().toISOString()
                    };
                    
                    console.log('Registrando entrada:', data);
                    
                    Swal.fire(
                        '¡Entrada Registrada!',
                        'Tu entrada ha sido registrada exitosamente.',
                        'success'
                    );
                }
            });
        }

        // Registrar salida
        function registrarSalida() {
            if (!isInWorkArea) {
                Swal.fire('Error', 'Debes estar en el área de trabajo para registrar salida.', 'error');
                return;
            }

            Swal.fire({
                title: 'Registrando Salida',
                text: '¿Confirmas tu salida?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Sí, registrar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Aquí enviarías los datos al servidor
                    const data = {
                        tipo: 'salida',
                        latitud: userLocation.lat,
                        longitud: userLocation.lng,
                        timestamp: new Date().toISOString()
                    };
                    
                    console.log('Registrando salida:', data);
                    
                    Swal.fire(
                        '¡Salida Registrada!',
                        'Tu salida ha sido registrada exitosamente.',
                        'success'
                    );
                }
            });
        }

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