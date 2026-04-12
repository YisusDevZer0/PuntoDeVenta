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
    <title>Checador - Sistema de Control</title>
    
    <!-- Favicon -->
    <link href="img/favicon.ico" rel="icon">

    <!-- Google Web Fonts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Heebo:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Icon Font Stylesheet -->
    <script src="https://kit.fontawesome.com/7c30e0d2f4.js" crossorigin="anonymous"></script>
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
    <!-- Leaflet para mapas y geolocalización -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <!-- IP Geolocation API -->
    <script src="https://cdn.jsdelivr.net/npm/axios@1.6.0/dist/axios.min.js"></script>
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
        
        .header-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        
        .header-section h1 {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 10px;
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
        
        .user-info {
            background: #e3f2fd;
            padding: 20px;
            border-radius: 15px;
            margin-bottom: 20px;
            border-left: 4px solid #2196f3;
            box-shadow: 0 2px 8px rgba(33, 150, 243, 0.1);
        }
        
        .user-info .user-section {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .user-info .user-section > div {
            flex: 1;
        }
        
        .user-info .user-section > div:first-child {
            text-align: left;
        }
        
        .user-info .user-section > div:last-child {
            text-align: right;
        }
        
        .user-info .section-title {
            font-size: 18px;
            font-weight: 700;
            color: #1976d2;
            margin-bottom: 5px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .user-info .section-value {
            font-size: 16px;
            color: #424242;
            font-weight: 500;
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
            .user-info .user-section {
                flex-direction: column;
                text-align: center;
            }
            
            .user-info .user-section > div {
                margin-bottom: 15px;
            }
            
            .user-info .user-section > div:first-child,
            .user-info .user-section > div:last-child {
                text-align: center;
            }
            
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
        // Variables globales para el checador
        let userLocation = null;
        let isInWorkArea = false;
        let locationPermissionDenied = false;
        let locationCheckAttempted = false;
        
        // Habilitar botones al cargar
        window.addEventListener('DOMContentLoaded', function(){
            console.log('Checador iniciando...');
            initializeChecador();
        });
        
        // Función principal de inicialización
        async function initializeChecador() {
            try {
                // Actualizar fecha y hora
                updateDateTime();
                setInterval(updateDateTime, 1000);
                
                // Cargar información del centro de trabajo
                await loadWorkCenterInfo();
                
                // Verificar ubicación
                await checkLocation();
                
                // Habilitar botones por defecto (modo permisivo)
                enableButtons();
                
                console.log('Checador inicializado correctamente');
            } catch (error) {
                console.error('Error inicializando checador:', error);
                // Aún así habilitar botones para permitir uso
                enableButtons();
            }
        }
        
        // Cargar información del centro de trabajo
        async function loadWorkCenterInfo() {
            try {
                const response = await makeRequest('obtener_centros_trabajo');
                
                if (response.success && response.data && response.data.length > 0) {
                    // Mostrar el primer centro de trabajo activo
                    const workCenter = response.data.find(center => center.estado === 'active') || response.data[0];
                    document.getElementById('workCenter').textContent = workCenter.nombre || 'Centro de Trabajo';
                } else {
                    document.getElementById('workCenter').textContent = 'No configurado';
                }
            } catch (error) {
                console.error('Error cargando centro de trabajo:', error);
                document.getElementById('workCenter').textContent = 'Error al cargar';
            }
        }
    </script>
    <div class="checador-container">
        <div class="checador-card">
            <!-- Header Section -->
            <div class="header-section">
                <button class="back-button" onclick="window.location.href='ChecadorIndex.php'">
                    <i class="fas fa-arrow-left"></i> Volver
                </button>
                <h1><i class="fas fa-clock"></i> Registro de Asistencia</h1>
                <p>Registra tu entrada y salida del trabajo</p>
            </div>

            <!-- Barra de Estado -->
            <div class="status-bar">
                <div class="status-indicator" id="statusIndicator">
                    <i class="fas fa-map-marker-alt"></i>
                    <span id="statusText">Estado: Verificando ubicación...</span>
                </div>
                <div>
                    <small>Última verificación: <span id="lastVerification">--</span></small>
                    <br>
                    <div style="margin-top: 5px;">
                        <button class="btn btn-sm btn-outline-primary" onclick="checkLocationManual()" style="margin-right: 5px;">
                            <i class="fas fa-sync-alt"></i> GPS
                    </button>
                        <button class="btn btn-sm btn-outline-success" onclick="getLocationByIP().then(handleLocationSuccess).catch(handleLocationError)" style="margin-right: 5px;">
                            <i class="fas fa-globe"></i> IP
                        </button>
                        <button class="btn btn-sm btn-outline-info" onclick="getLocationWithMap().then(handleLocationSuccess).catch(handleLocationError)" style="margin-right: 5px;">
                            <i class="fas fa-map"></i> Mapa
                        </button>
                        <button class="btn btn-sm btn-outline-warning" onclick="testGeolocation()" style="margin-right: 5px;">
                            <i class="fas fa-test-tube"></i> Test
                        </button>
                        <button class="btn btn-sm btn-outline-secondary" onclick="diagnosticarGeolocalizacion()">
                            <i class="fas fa-bug"></i> Debug
                        </button>
                    </div>
                </div>
            </div>

            <!-- Panel de Información del Usuario -->
            <div class="info-panel">
                <!-- Información del Usuario -->
                <div class="user-info">
                    <div class="user-section">
                        <div>
                            <div class="section-title">
                                <i class="fas fa-user"></i> USUARIO
                            </div>
                            <div class="section-value" id="userName">
                                <?php echo isset($row['Nombre_Apellidos']) ? htmlspecialchars($row['Nombre_Apellidos']) : 'Usuario'; ?>
                            </div>
                        </div>
                        <div>
                            <div class="section-title">
                                <i class="fas fa-building"></i> CENTRO DE TRABAJO
                            </div>
                            <div class="section-value" id="workCenter">
                                Cargando...
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Información de Ubicación -->
                <div class="location-info">
                    <div class="location-left">
                        <div class="location-title">UBICACIÓN ACTUAL</div>
                        <div class="location-value" id="currentLocation">Cargando...</div>
                    </div>
                    <div class="location-right">
                        <div class="clock-display" id="currentTime">--:--</div>
                        <div class="location-title">FECHA</div>
                        <div class="location-value" id="currentDate">--</div>
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

            const currentTime = document.getElementById('currentTime');
            const currentDate = document.getElementById('currentDate');

            if (currentTime) currentTime.textContent = timeString;
            if (currentDate) currentDate.textContent = `${dateString} ${dayString}`;
        }
        
        // Manejo de ubicación exitosa
        async function handleLocationSuccess(position) {
            console.log('🎉 Ubicación obtenida exitosamente:', position);
            
            userLocation = {
                lat: position.coords.latitude,
                lng: position.coords.longitude
            };
            
            const source = position.source || 'Desconocido';
            document.getElementById('currentLocation').textContent = `Ubicación detectada (${source})`;
            updateLastVerification();
            
            // Mostrar información de la ubicación
            Swal.fire({
                title: '¡Ubicación Obtenida!',
                html: `
                    <p><strong>Método:</strong> ${source}</p>
                    <p><strong>Latitud:</strong> ${userLocation.lat.toFixed(6)}</p>
                    <p><strong>Longitud:</strong> ${userLocation.lng.toFixed(6)}</p>
                    <p><strong>Precisión:</strong> ${Math.round(position.coords.accuracy)} metros</p>
                `,
                icon: 'success',
                confirmButtonText: 'Continuar'
            });
            
            // Verificar área de trabajo
            await verifyWorkArea();
        }
        
        // Manejo de error de ubicación
        function handleLocationError(error) {
            console.error('❌ Error obteniendo ubicación:', error);
            
            Swal.fire({
                title: 'Error de Ubicación',
                text: error.message || 'No se pudo obtener la ubicación',
                icon: 'error',
                confirmButtonText: 'Entendido'
            });
            
            document.getElementById('currentLocation').textContent = 'Error obteniendo ubicación';
            updateStatus('outside', 'Error de ubicación');
        }
        
        // Verificar ubicación (método híbrido)
        async function checkLocation() {
            // Si ya se intentó y fue denegado, no volver a intentar
            if (locationPermissionDenied || locationCheckAttempted) {
                console.log('Ubicación ya verificada o denegada, omitiendo...');
                return;
            }
            
            locationCheckAttempted = true;
            
            try {
                console.log('🔄 Iniciando verificación de ubicación híbrida...');
                const position = await getCurrentPosition();
                await handleLocationSuccess(position);
                
            } catch (error) {
                console.log('Error obteniendo ubicación:', error);
                
                // Marcar como denegado si es error de permisos
                if (error.code === 1) {
                    locationPermissionDenied = true;
                    document.getElementById('currentLocation').textContent = 'Permisos de ubicación denegados';
                    updateStatus('outside', 'Ubicación no disponible - Permisos denegados');
                } else {
                    document.getElementById('currentLocation').textContent = 'Ubicación no disponible';
                    updateStatus('outside', 'Ubicación no disponible');
                }
            }
        }
        
        // SISTEMA DE GEOLOCALIZACIÓN MÚLTIPLE
        // =====================================
        
        // 1. Geolocalización por IP (más confiable)
        async function getLocationByIP() {
            console.log('🌐 Obteniendo ubicación por IP...');
            
            try {
                // Usar múltiples APIs como fallback
                const apis = [
                    'https://ipapi.co/json/',
                    'https://ip-api.com/json/',
                    'https://api.ipgeolocation.io/ipgeo?apiKey=free'
                ];
                
                for (const api of apis) {
                    try {
                        console.log(`🔗 Intentando API: ${api}`);
                        const response = await fetch(api);
                        const data = await response.json();
                        
                        if (data.latitude && data.longitude) {
                            console.log('✅ Ubicación obtenida por IP:', data);
                            return {
                                lat: parseFloat(data.latitude),
                                lng: parseFloat(data.longitude),
                                accuracy: 1000, // Aproximación por IP
                                source: 'IP',
                                city: data.city || data.city_name,
                                country: data.country || data.country_name
                            };
                        }
                    } catch (apiError) {
                        console.warn(`⚠️ API falló: ${api}`, apiError);
                        continue;
                    }
                }
                
                throw new Error('Todas las APIs de IP fallaron');
                
            } catch (error) {
                console.error('❌ Error obteniendo ubicación por IP:', error);
                throw error;
            }
        }
        
        // 2. Geolocalización del navegador (GPS)
        function getLocationByGPS() {
            return new Promise((resolve, reject) => {
                console.log('📡 Obteniendo ubicación por GPS...');
                
                if (!navigator.geolocation) {
                    reject(new Error('Geolocalización no soportada'));
                    return;
                }
                
                const options = {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 0
                };
                
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        console.log('✅ GPS exitoso:', position.coords);
                        resolve({
                            lat: position.coords.latitude,
                            lng: position.coords.longitude,
                            accuracy: position.coords.accuracy,
                            source: 'GPS',
                            timestamp: position.timestamp
                        });
                    },
                    (error) => {
                        console.error('❌ GPS falló:', error);
                        reject(error);
                    },
                    options
                );
            });
        }
        
        // 3. Geolocalización híbrida (múltiples métodos)
        async function getCurrentPosition() {
            console.log('🚀 INICIANDO GEOLOCALIZACIÓN HÍBRIDA');
            
            const methods = [
                { name: 'GPS', fn: getLocationByGPS, priority: 1 },
                { name: 'IP', fn: getLocationByIP, priority: 2 }
            ];
            
            // Ordenar por prioridad
            methods.sort((a, b) => a.priority - b.priority);
            
            for (const method of methods) {
                try {
                    console.log(`🔄 Intentando método: ${method.name}`);
                    const location = await method.fn();
                    
                    console.log(`✅ Éxito con ${method.name}:`, location);
                    return {
                        coords: {
                            latitude: location.lat,
                            longitude: location.lng,
                            accuracy: location.accuracy || 1000
                        },
                        timestamp: location.timestamp || Date.now(),
                        source: location.source || method.name
                    };
                    
                } catch (error) {
                    console.warn(`⚠️ Método ${method.name} falló:`, error.message);
                    continue;
                }
            }
            
            throw new Error('Todos los métodos de geolocalización fallaron');
        }
        
        // 4. Geolocalización con mapa interactivo (Leaflet)
        async function getLocationWithMap() {
            return new Promise((resolve, reject) => {
                Swal.fire({
                    title: 'Seleccionar Ubicación',
                    html: `
                        <div id="map" style="height: 400px; width: 100%;"></div>
                        <p class="mt-3">Haz clic en el mapa para seleccionar tu ubicación</p>
                    `,
                    showCancelButton: true,
                    confirmButtonText: 'Usar esta ubicación',
                    cancelButtonText: 'Cancelar',
                    didOpen: () => {
                        // Inicializar mapa
                        const map = L.map('map').setView([19.4326, -99.1332], 13);
                        
                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                            attribution: '© OpenStreetMap contributors'
                        }).addTo(map);
                        
                        let marker = null;
                        
                        // Obtener ubicación actual si es posible
                        if (navigator.geolocation) {
                            navigator.geolocation.getCurrentPosition((pos) => {
                                const lat = pos.coords.latitude;
                                const lng = pos.coords.longitude;
                                map.setView([lat, lng], 15);
                                marker = L.marker([lat, lng]).addTo(map);
                            });
                        }
                        
                        // Click en el mapa
                        map.on('click', (e) => {
                            if (marker) {
                                map.removeLayer(marker);
                            }
                            marker = L.marker([e.latlng.lat, e.latlng.lng]).addTo(map);
                        });
                        
                        // Guardar referencia para usar después
                        window.tempMap = map;
                        window.tempMarker = marker;
                    },
                    preConfirm: () => {
                        if (window.tempMarker) {
                            const latlng = window.tempMarker.getLatLng();
                            return {
                                lat: latlng.lat,
                                lng: latlng.lng,
                                source: 'Mapa'
                            };
                        }
                        return null;
                    }
                }).then((result) => {
                    if (result.isConfirmed && result.value) {
                        resolve({
                            coords: {
                                latitude: result.value.lat,
                                longitude: result.value.lng,
                                accuracy: 10 // Muy preciso por selección manual
                            },
                            timestamp: Date.now(),
                            source: 'Mapa'
                        });
                    } else {
                        reject(new Error('Selección cancelada'));
                    }
                });
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
                        const workCenterName = response.ubicacion_encontrada ? response.ubicacion_encontrada.nombre : 'Centro de Trabajo';
                        updateStatus('inside', `En el área: ${workCenterName}`);
                        enableButtons();
                    } else {
                        updateStatus('outside', 'Fuera del área de trabajo');
                        // Aún habilitar botones para permitir uso
                        enableButtons();
                    }
                } else {
                    // En caso de error, habilitar botones de todas formas
                    updateStatus('outside', 'Error verificando ubicación');
                    enableButtons();
                }
            } catch (error) {
                console.error('Error verificando área:', error);
                // En caso de error, habilitar botones
                updateStatus('outside', 'Error de conexión');
                enableButtons();
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
        
        // Habilitar botones
        function enableButtons() {
            const btnEntry = document.getElementById('btnEntry');
            const btnExit = document.getElementById('btnExit');
            
            if (btnEntry) {
                btnEntry.disabled = false;
                btnEntry.style.opacity = '1';
            }
            if (btnExit) {
                btnExit.disabled = false;
                btnExit.style.opacity = '1';
            }
        }
        
        // Deshabilitar botones
        function disableButtons() {
            const btnEntry = document.getElementById('btnEntry');
            const btnExit = document.getElementById('btnExit');
            
            if (btnEntry) {
                btnEntry.disabled = true;
                btnEntry.style.opacity = '0.5';
            }
            if (btnExit) {
                btnExit.disabled = true;
                btnExit.style.opacity = '0.5';
            }
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
            
            const lastVerification = document.getElementById('lastVerification');
            if (lastVerification) {
                lastVerification.textContent = formattedDate;
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
        
        // Registrar entrada
        async function registrarEntrada() {
            console.log('Registrando entrada...');
            
            // Solo intentar obtener ubicación si no se ha denegado antes
            if (!userLocation && !locationPermissionDenied) {
                try {
                    await checkLocation();
                } catch (error) {
                    console.log('No se pudo obtener ubicación, continuando...');
                }
            }
            
            const confirmed = await showConfirmation(
                'Registrando Entrada',
                '¿Confirmas tu entrada?'
            );

            if (confirmed) {
                await registrarAsistencia('entrada');
            }
        }
        
        // Registrar salida
        async function registrarSalida() {
            console.log('Registrando salida...');
            
            // Solo intentar obtener ubicación si no se ha denegado antes
            if (!userLocation && !locationPermissionDenied) {
                try {
                    await checkLocation();
                } catch (error) {
                    console.log('No se pudo obtener ubicación, continuando...');
                }
            }
            
            const confirmed = await showConfirmation(
                'Registrando Salida',
                '¿Confirmas tu salida?'
            );

            if (confirmed) {
                await registrarAsistencia('salida');
            }
        }
        
        // Registrar asistencia
        async function registrarAsistencia(tipo) {
            try {
                const data = {
                    tipo: tipo,
                    latitud: userLocation ? userLocation.lat : 0,
                    longitud: userLocation ? userLocation.lng : 0,
                    timestamp: new Date().toISOString()
                };

                const response = await makeRequest('registrar_asistencia', data);
                
                if (response.success) {
                    showSuccess(`${tipo.charAt(0).toUpperCase() + tipo.slice(1)} registrada exitosamente`);
                } else {
                    showError(response.message);
                }
            } catch (error) {
                console.error('Error registrando asistencia:', error);
                showError('Error al registrar la asistencia');
            }
        }
        
        // Mostrar confirmación
        function showConfirmation(title, text) {
            return Swal.fire({
                title: title,
                text: text,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Sí, confirmar',
                cancelButtonText: 'Cancelar'
            }).then((result) => result.isConfirmed);
        }
        
        // Mostrar mensaje de éxito
        function showSuccess(message) {
            Swal.fire({
                title: '¡Éxito!',
                text: message,
                icon: 'success',
                timer: 3000
            });
        }
        
        // Mostrar mensaje de error
        function showError(message) {
            Swal.fire({
                title: 'Error',
                text: message,
                icon: 'error'
            });
        }
        
        // Verificar ubicación manualmente
        // Diagnóstico de geolocalización
        function diagnosticarGeolocalizacion() {
            console.log('🔧 DIAGNÓSTICO DE GEOLOCALIZACIÓN');
            console.log('================================');
            
            // 1. Verificar soporte
            console.log('1. Soporte de geolocalización:', !!navigator.geolocation);
            
            // 2. Verificar HTTPS
            console.log('2. Protocolo seguro (HTTPS):', location.protocol === 'https:');
            
            // 3. Verificar permisos (si está disponible)
            if (navigator.permissions) {
                navigator.permissions.query({name: 'geolocation'}).then(result => {
                    console.log('3. Estado de permisos:', result.state);
                }).catch(err => {
                    console.log('3. No se puede verificar permisos:', err);
                });
            } else {
                console.log('3. API de permisos no disponible');
            }
            
            // 4. Verificar configuración del navegador
            console.log('4. User Agent:', navigator.userAgent);
            console.log('5. Plataforma:', navigator.platform);
            
            // 5. Verificar variables globales
            console.log('6. Variables del checador:', {
                userLocation,
                isInWorkArea,
                locationPermissionDenied,
                locationCheckAttempted
            });
            
            console.log('================================');
        }
        
        // Test de geolocalización
        async function testGeolocation() {
            console.log('🧪 INICIANDO TEST DE GEOLOCALIZACIÓN');
            
            try {
                // Test básico
                console.log('1. Test básico de disponibilidad...');
                if (!navigator.geolocation) {
                    throw new Error('Geolocalización no soportada');
                }
                console.log('✅ Geolocalización disponible');
                
                // Test de permisos
                console.log('2. Test de permisos...');
                if (navigator.permissions) {
                    const permission = await navigator.permissions.query({name: 'geolocation'});
                    console.log('📋 Estado de permisos:', permission.state);
                } else {
                    console.log('⚠️ API de permisos no disponible');
                }
                
                // Test de ubicación con diferentes configuraciones
                console.log('3. Test de ubicación (baja precisión)...');
                const position1 = await new Promise((resolve, reject) => {
                    navigator.geolocation.getCurrentPosition(resolve, reject, {
                        enableHighAccuracy: false,
                        timeout: 5000,
                        maximumAge: 60000
                    });
                });
                console.log('✅ Ubicación obtenida (baja precisión):', {
                    lat: position1.coords.latitude,
                    lng: position1.coords.longitude,
                    accuracy: position1.coords.accuracy
                });
                
                // Test de ubicación con alta precisión
                console.log('4. Test de ubicación (alta precisión)...');
                const position2 = await new Promise((resolve, reject) => {
                    navigator.geolocation.getCurrentPosition(resolve, reject, {
                        enableHighAccuracy: true,
                        timeout: 10000,
                        maximumAge: 0
                    });
                });
                console.log('✅ Ubicación obtenida (alta precisión):', {
                    lat: position2.coords.latitude,
                    lng: position2.coords.longitude,
                    accuracy: position2.coords.accuracy
                });
                
                // Mostrar resultado exitoso
                Swal.fire({
                    title: '¡Test Exitoso!',
                    html: `
                        <p><strong>Geolocalización funcionando correctamente</strong></p>
                        <p><strong>Última ubicación:</strong></p>
                        <p>Lat: ${position2.coords.latitude.toFixed(6)}</p>
                        <p>Lng: ${position2.coords.longitude.toFixed(6)}</p>
                        <p>Precisión: ${Math.round(position2.coords.accuracy)} metros</p>
                    `,
                    icon: 'success',
                    confirmButtonText: 'Excelente'
                });
                
            } catch (error) {
                console.error('❌ Test falló:', error);
                
                Swal.fire({
                    title: 'Test Falló',
                    html: `
                        <p><strong>Error:</strong> ${error.message}</p>
                        <p><strong>Recomendaciones:</strong></p>
                        <ul style="text-align: left;">
                            <li>Verifica permisos de ubicación en el navegador</li>
                            <li>Asegúrate de estar en HTTPS</li>
                            <li>Intenta en un dispositivo móvil</li>
                            <li>Revisa la configuración de privacidad</li>
                        </ul>
                    `,
                    icon: 'error',
                    confirmButtonText: 'Entendido'
                });
            }
        }
        
        // Verificación manual de ubicación
        async function checkLocationManual() {
            console.log('🔄 Verificación manual de ubicación...');
            
            // Ejecutar diagnóstico
            diagnosticarGeolocalizacion();
            
            // Resetear flags para permitir reintento
            locationPermissionDenied = false;
            locationCheckAttempted = false;
            
            // Mostrar loading
            Swal.fire({
                title: 'Obteniendo ubicación...',
                text: 'Por favor espera mientras obtenemos tu ubicación',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            try {
                // Intentar obtener ubicación con timeout personalizado
                const position = await Promise.race([
                    getCurrentPosition(),
                    new Promise((_, reject) => 
                        setTimeout(() => reject(new Error('Timeout personalizado')), 20000)
                    )
                ]);
                
                userLocation = {
                    lat: position.coords.latitude,
                    lng: position.coords.longitude
                };
                
                Swal.close();
                
                // Mostrar información de la ubicación
                Swal.fire({
                    title: '¡Ubicación obtenida!',
                    html: `
                        <p><strong>Latitud:</strong> ${userLocation.lat.toFixed(6)}</p>
                        <p><strong>Longitud:</strong> ${userLocation.lng.toFixed(6)}</p>
                        <p><strong>Precisión:</strong> ${Math.round(position.coords.accuracy)} metros</p>
                    `,
                    icon: 'success',
                    confirmButtonText: 'Continuar'
                });
                
                // Actualizar interfaz
                document.getElementById('currentLocation').textContent = 'Ubicación detectada';
                updateLastVerification();
                
                // Verificar área de trabajo
                await verifyWorkArea();
                
            } catch (error) {
                Swal.close();
                console.error('❌ Error en verificación manual:', error);
                
                Swal.fire({
                    title: 'Error obteniendo ubicación',
                    html: `
                        <p><strong>Error:</strong> ${error.message}</p>
                        <p><strong>Posibles soluciones:</strong></p>
                        <ul style="text-align: left;">
                            <li>Verifica que el navegador tenga permisos de ubicación</li>
                            <li>Asegúrate de estar en una conexión HTTPS</li>
                            <li>Intenta en un dispositivo con GPS</li>
                            <li>Revisa la configuración de privacidad del navegador</li>
                        </ul>
                    `,
                    icon: 'error',
                    confirmButtonText: 'Entendido'
                });
            }
        }
        
        // Configurar ubicación
        async function setupLocation() {
            if (!userLocation) {
                try {
                    await checkLocation();
                } catch (error) {
                    showError('No se pudo obtener la ubicación actual');
                    return;
                }
            }
            
            const confirmed = await showConfirmation(
                'Configurar Ubicación',
                '¿Deseas configurar esta ubicación como tu centro de trabajo?'
            );

            if (confirmed) {
                try {
                    const data = {
                        nombre: 'Ubicación configurada',
                        descripcion: 'Ubicación configurada automáticamente',
                        latitud: userLocation.lat,
                        longitud: userLocation.lng,
                        radio: 100,
                        direccion: '',
                        estado: 'active'
                    };

                    const response = await makeRequest('guardar_ubicacion', data);
                    
                    if (response.success) {
                        showSuccess('Ubicación configurada exitosamente');
                        await verifyWorkArea();
                    } else {
                        showError(response.message);
                    }
                } catch (error) {
                    console.error('Error configurando ubicación:', error);
                    showError('Error al configurar la ubicación');
                }
            }
        }
    </script>
</body>
</html> 