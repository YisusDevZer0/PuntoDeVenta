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
                    <button class="btn btn-sm btn-outline-primary" onclick="checkLocationManual()" style="margin-top: 5px;">
                        <i class="fas fa-sync-alt"></i> Verificar Ubicación
                    </button>
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
        
        // Verificar ubicación
        async function checkLocation() {
            try {
                if (!navigator.geolocation) {
                    console.log('Geolocalización no soportada');
                    document.getElementById('currentLocation').textContent = 'Geolocalización no disponible';
                    updateStatus('outside', 'Geolocalización no disponible');
                    return;
                }

                const position = await getCurrentPosition();
                userLocation = {
                    lat: position.coords.latitude,
                    lng: position.coords.longitude
                };
                
                document.getElementById('currentLocation').textContent = 'Ubicación detectada';
                updateLastVerification();
                
                // Verificar si está en área de trabajo
                await verifyWorkArea();
                
            } catch (error) {
                console.log('Error obteniendo ubicación:', error);
                document.getElementById('currentLocation').textContent = 'Ubicación no disponible';
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
            
            if (!userLocation) {
                // Intentar obtener ubicación si no la tenemos
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
            
            if (!userLocation) {
                // Intentar obtener ubicación si no la tenemos
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
        async function checkLocationManual() {
            console.log('Verificación manual de ubicación...');
            await checkLocation();
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