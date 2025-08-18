/**
 * Sistema de Checador - JavaScript
 * Maneja todas las operaciones del checador de entrada y salida
 */

class ChecadorManager {
    constructor() {
        this.userLocation = null;
        this.workLocations = [];
        this.isInWorkArea = false;
        this.currentPosition = null;
        this.verificationInterval = null;
        this.controllerUrl = 'Controladores/ChecadorController.php';
        this.permissionErrorShown = false;
        this.isCheckingLocation = false; // Evitar verificaciones simultáneas
    }

    /**
     * Inicializar el sistema de checador
     */
    async init() {
        try {
            console.log('Inicializando sistema de checador...');
            await this.loadWorkLocations();
            
            // Solo iniciar verificación automática si no hay ubicaciones configuradas
            if (this.workLocations.length === 0) {
                console.log('No hay ubicaciones configuradas, iniciando verificación automática...');
                this.startLocationVerification();
            } else {
                console.log('Ubicaciones configuradas encontradas, sistema listo para uso manual');
            }
            
            this.setupEventListeners();
        } catch (error) {
            console.error('Error inicializando checador:', error);
            this.showError('Error al inicializar el sistema de checador');
        }
    }

    /**
     * Cargar ubicaciones de trabajo del usuario
     */
    async loadWorkLocations() {
        try {
            const response = await this.makeRequest('obtener_ubicaciones', {});
            if (response.success) {
                this.workLocations = response.data;
                this.updateWorkCenterDisplay();
                
                // Si hay ubicaciones configuradas, configurar el estado inicial
                if (this.workLocations.length > 0) {
                    console.log('Ubicaciones cargadas:', this.workLocations.length);
                    document.getElementById('currentLocation').textContent = 'Ubicación configurada disponible';
                    this.updateStatus('outside', 'Fuera del área');
                    this.disableButtons();
                    this.hideLocationSetup();
                }
            }
        } catch (error) {
            console.error('Error cargando ubicaciones:', error);
        }
    }

    /**
     * Iniciar verificación de ubicación
     */
    startLocationVerification() {
        // Solo verificar ubicación si no hay ubicaciones configuradas
        if (this.workLocations.length === 0) {
            this.checkLocation();
            // Verificar ubicación cada 60 segundos solo si no hay ubicaciones configuradas
            this.verificationInterval = setInterval(() => {
                this.checkLocation();
            }, 60000);
        } else {
            console.log('Ubicaciones configuradas disponibles, no iniciando verificación automática');
            // Si hay ubicaciones configuradas, solo verificar una vez al inicio
            this.checkLocationOnce();
        }
    }

    /**
     * Verificar ubicación del usuario
     */
    async checkLocation() {
        // Evitar verificaciones simultáneas
        if (this.isCheckingLocation) {
            console.log('Verificación de ubicación ya en progreso, saltando...');
            return;
        }

        // Si ya tenemos una ubicación válida y reciente, no verificar de nuevo
        if (this.userLocation && this.currentPosition && 
            (Date.now() - this.currentPosition.timestamp) < 300000) { // 5 minutos
            console.log('Ubicación reciente disponible, saltando verificación...');
            return;
        }

        // Si no hay geolocalización disponible, mostrar error solo si no hay ubicaciones configuradas
        if (!navigator.geolocation) {
            if (this.workLocations.length === 0) {
                this.updateStatus('error', 'Geolocalización no soportada');
            }
            return;
        }

        this.isCheckingLocation = true;

        try {
            const position = await this.getCurrentPosition();
            this.currentPosition = position;
            this.userLocation = {
                lat: position.coords.latitude,
                lng: position.coords.longitude
            };

            // Resetear la bandera de error de permiso si se obtiene ubicación exitosamente
            this.permissionErrorShown = false;

            document.getElementById('currentLocation').textContent = 'Ubicación detectada';
            await this.verifyWorkArea();
            this.updateLastVerification();
        } catch (error) {
            console.error('Error obteniendo ubicación:', error);
            
            // Si hay ubicaciones configuradas, no mostrar errores de geolocalización
            if (this.workLocations.length > 0) {
                console.log('Ubicaciones configuradas disponibles, ignorando error de geolocalización');
                document.getElementById('currentLocation').textContent = 'Ubicación configurada disponible';
                this.updateStatus('outside', 'Fuera del área');
                return;
            }
            
            let errorMessage = 'No se pudo obtener la ubicación';
            let statusMessage = 'Error de ubicación';
            
            // Manejar diferentes tipos de errores de geolocalización
            if (error.code === 1) {
                errorMessage = 'Permiso de ubicación denegado';
                statusMessage = 'Permiso denegado';
                // Solo mostrar el modal una vez si no hay ubicaciones configuradas
                if (!this.permissionErrorShown && this.workLocations.length === 0) {
                    this.showLocationPermissionError();
                    this.permissionErrorShown = true;
                }
            } else if (error.code === 2) {
                errorMessage = 'Ubicación no disponible';
                statusMessage = 'Ubicación no disponible';
            } else if (error.code === 3) {
                errorMessage = 'Tiempo de espera agotado';
                statusMessage = 'Tiempo agotado';
            }
            
            document.getElementById('currentLocation').textContent = errorMessage;
            this.updateStatus('error', statusMessage);
            
            // Solo mostrar configuración si no hay ubicaciones
            if (this.workLocations.length === 0 && !this.permissionErrorShown) {
                this.showLocationSetup();
            }
        } finally {
            this.isCheckingLocation = false;
        }
    }

    /**
     * Verificar ubicación una sola vez (sin geolocalización si hay ubicaciones configuradas)
     */
    async checkLocationOnce() {
        // Si hay ubicaciones configuradas, no intentar obtener geolocalización
        if (this.workLocations.length > 0) {
            console.log('Ubicaciones configuradas disponibles, configurando estado inicial');
            document.getElementById('currentLocation').textContent = 'Ubicación configurada disponible';
            this.updateStatus('outside', 'Fuera del área');
            this.disableButtons();
            this.hideLocationSetup();
            return;
        }

        // Solo verificar geolocalización si no hay ubicaciones configuradas
        await this.checkLocation();
    }

    /**
     * Obtener posición actual
     */
    getCurrentPosition() {
        return new Promise((resolve, reject) => {
            navigator.geolocation.getCurrentPosition(
                resolve,
                reject,
                {
                    enableHighAccuracy: false, // Menos estricto para evitar ciclos
                    timeout: 10000, // Reducir timeout para evitar bloqueos largos
                    maximumAge: 600000 // 10 minutos de cache para reducir verificaciones
                }
            );
        });
    }

    /**
     * Verificar si está en el área de trabajo
     */
    async verifyWorkArea() {
        // Si no hay ubicaciones configuradas, no verificar
        if (this.workLocations.length === 0) {
            this.isInWorkArea = false;
            this.updateStatus('outside', 'Sin ubicaciones configuradas');
            this.disableButtons();
            this.showLocationSetup();
            return;
        }

        // Si no hay ubicación del usuario, mostrar estado fuera del área
        if (!this.userLocation) {
            this.isInWorkArea = false;
            this.updateStatus('outside', 'Fuera del área');
            this.disableButtons();
            this.hideLocationSetup();
            return;
        }

        try {
            const response = await this.makeRequest('verificar_ubicacion', {
                latitud: this.userLocation.lat,
                longitud: this.userLocation.lng
            });

            if (response.success) {
                this.isInWorkArea = response.en_area;
                
                if (this.isInWorkArea) {
                    this.updateStatus('inside', 'En el área de trabajo');
                    this.enableButtons();
                    this.hideLocationSetup();
                } else {
                    this.updateStatus('outside', 'Fuera del área');
                    this.disableButtons();
                    this.hideLocationSetup(); // No mostrar setup si ya hay ubicaciones configuradas
                }
            }
        } catch (error) {
            console.error('Error verificando ubicación:', error);
            // En caso de error, asumir que está fuera del área
            this.isInWorkArea = false;
            this.updateStatus('outside', 'Fuera del área');
            this.disableButtons();
        }
    }

    /**
     * Actualizar estado visual
     */
    updateStatus(type, message) {
        const indicator = document.getElementById('statusIndicator');
        const statusText = document.getElementById('statusText');
        
        // Si hay ubicaciones configuradas y hay un error de ubicación, no mostrar el error
        if (type === 'error' && this.workLocations.length > 0) {
            // En lugar de mostrar error, mostrar que está fuera del área
            type = 'outside';
            message = 'Fuera del área';
        }
        
        // No actualizar el estado si es el mismo que ya está mostrado
        if (statusText && statusText.textContent === `Estado: ${message}`) {
            return;
        }
        
        // Si hay ubicaciones configuradas, nunca mostrar errores de geolocalización
        if (type === 'error' && this.workLocations.length > 0) {
            type = 'outside';
            message = 'Fuera del área';
        }
        
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

    /**
     * Habilitar botones de registro
     */
    enableButtons() {
        const btnEntry = document.getElementById('btnEntry');
        const btnExit = document.getElementById('btnExit');
        
        if (btnEntry) btnEntry.disabled = false;
        if (btnExit) btnExit.disabled = false;
    }

    /**
     * Deshabilitar botones de registro
     */
    disableButtons() {
        const btnEntry = document.getElementById('btnEntry');
        const btnExit = document.getElementById('btnExit');
        
        if (btnEntry) btnEntry.disabled = true;
        if (btnExit) btnExit.disabled = true;
    }

    /**
     * Mostrar configuración de ubicación
     */
    showLocationSetup() {
        // No mostrar setup si ya hay ubicaciones configuradas
        if (this.workLocations.length > 0) {
            return;
        }
        
        const setup = document.getElementById('locationSetup');
        if (setup) setup.style.display = 'block';
    }

    /**
     * Mostrar error de permiso de ubicación
     */
    showLocationPermissionError() {
        // Solo mostrar si no se ha mostrado antes y no hay ubicaciones configuradas
        if (this.permissionErrorShown || this.workLocations.length > 0) {
            return;
        }

        Swal.fire({
            title: 'Permiso de Ubicación Requerido',
            html: `
                <div class="text-left">
                    <p><strong>El sistema necesita acceso a tu ubicación para funcionar correctamente.</strong></p>
                    <p>Para habilitar la geolocalización:</p>
                    <ol class="text-left">
                        <li>Haz clic en el ícono de ubicación en la barra de direcciones</li>
                        <li>Selecciona "Permitir" o "Allow"</li>
                        <li>Recarga la página</li>
                    </ol>
                    <p><strong>Alternativa:</strong> Puedes configurar una ubicación manualmente.</p>
                </div>
            `,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Configurar Manualmente',
            cancelButtonText: 'Recargar Página',
            reverseButtons: true,
            allowOutsideClick: false,
            timer: null,
            timerProgressBar: false
        }).then((result) => {
            if (result.isConfirmed) {
                // Redirigir a la página de configuración de ubicaciones
                window.location.href = 'ConfiguracionUbicaciones.php';
            } else {
                // Recargar la página
                window.location.reload();
            }
        });
    }

    /**
     * Ocultar configuración de ubicación
     */
    hideLocationSetup() {
        const setup = document.getElementById('locationSetup');
        if (setup) setup.style.display = 'none';
    }

    /**
     * Actualizar última verificación
     */
    updateLastVerification() {
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

    /**
     * Actualizar centro de trabajo en pantalla
     */
    updateWorkCenterDisplay() {
        const workCenter = document.getElementById('workCenter');
        if (workCenter && this.workLocations.length > 0) {
            workCenter.textContent = this.workLocations[0].nombre;
        }
    }

    /**
     * Registrar entrada
     */
    async registrarEntrada() {
        console.log('Función registrarEntrada llamada');
        console.log('Estado isInWorkArea:', this.isInWorkArea);
        console.log('Ubicación del usuario:', this.userLocation);
        
        if (!this.isInWorkArea) {
            this.showError('Debes estar en el área de trabajo para registrar entrada.');
            return;
        }

        const confirmed = await this.showConfirmation(
            'Registrando Entrada',
            '¿Confirmas tu entrada?'
        );

        if (confirmed) {
            console.log('Confirmación aceptada, registrando entrada...');
            await this.registrarAsistencia('entrada');
        } else {
            console.log('Confirmación cancelada');
        }
    }

    /**
     * Registrar salida
     */
    async registrarSalida() {
        console.log('Función registrarSalida llamada');
        console.log('Estado isInWorkArea:', this.isInWorkArea);
        console.log('Ubicación del usuario:', this.userLocation);
        
        if (!this.isInWorkArea) {
            this.showError('Debes estar en el área de trabajo para registrar salida.');
            return;
        }

        const confirmed = await this.showConfirmation(
            'Registrando Salida',
            '¿Confirmas tu salida?'
        );

        if (confirmed) {
            console.log('Confirmación aceptada, registrando salida...');
            await this.registrarAsistencia('salida');
        } else {
            console.log('Confirmación cancelada');
        }
    }

    /**
     * Registrar asistencia en el servidor
     */
    async registrarAsistencia(tipo) {
        try {
            console.log('Iniciando registro de asistencia:', tipo);
            console.log('Datos a enviar:', {
                tipo: tipo,
                latitud: this.userLocation.lat,
                longitud: this.userLocation.lng,
                timestamp: new Date().toISOString()
            });

            const data = {
                tipo: tipo,
                latitud: this.userLocation.lat,
                longitud: this.userLocation.lng,
                timestamp: new Date().toISOString()
            };

            console.log('Enviando petición al servidor...');
            const response = await this.makeRequest('registrar_asistencia', data);
            console.log('Respuesta del servidor:', response);
            
            if (response.success) {
                console.log('Registro exitoso');
                this.showSuccess(`${tipo.charAt(0).toUpperCase() + tipo.slice(1)} registrada exitosamente`);
                this.logActivity(`registro_${tipo}`, `Registro de ${tipo} exitoso`);
            } else {
                console.log('Error en respuesta del servidor:', response.message);
                this.showError(response.message);
            }
        } catch (error) {
            console.error('Error registrando asistencia:', error);
            this.showError('Error al registrar la asistencia');
        }
    }

    /**
     * Configurar ubicación de trabajo
     */
    async setupLocation() {
        if (!this.currentPosition) {
            this.showError('No se pudo obtener la ubicación actual');
            return;
        }

        const confirmed = await this.showConfirmation(
            'Configurar Ubicación',
            '¿Deseas configurar esta ubicación como tu centro de trabajo?'
        );

        if (confirmed) {
            try {
                const data = {
                    nombre: 'Ubicación configurada',
                    descripcion: 'Ubicación configurada automáticamente',
                    latitud: this.currentPosition.coords.latitude,
                    longitud: this.currentPosition.coords.longitude,
                    radio: 100,
                    direccion: '',
                    estado: 'active'
                };

                const response = await this.makeRequest('guardar_ubicacion', data);
                
                if (response.success) {
                    this.showSuccess('Ubicación configurada exitosamente');
                    await this.loadWorkLocations();
                    await this.verifyWorkArea();
                } else {
                    this.showError(response.message);
                }
            } catch (error) {
                console.error('Error configurando ubicación:', error);
                this.showError('Error al configurar la ubicación');
            }
        }
    }

    /**
     * Realizar petición al servidor
     */
    async makeRequest(action, data = {}) {
        console.log('makeRequest - Acción:', action);
        console.log('makeRequest - Datos:', data);
        console.log('makeRequest - URL:', this.controllerUrl);
        
        const formData = new FormData();
        formData.append('action', action);
        
        for (const [key, value] of Object.entries(data)) {
            formData.append(key, value);
        }

        console.log('makeRequest - Enviando petición...');
        const response = await fetch(this.controllerUrl, {
            method: 'POST',
            body: formData
        });

        console.log('makeRequest - Status:', response.status);
        console.log('makeRequest - OK:', response.ok);

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const result = await response.json();
        console.log('makeRequest - Resultado:', result);
        return result;
    }

    /**
     * Mostrar confirmación
     */
    showConfirmation(title, text) {
        return Swal.fire({
            title: title,
            text: text,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sí, confirmar',
            cancelButtonText: 'Cancelar'
        }).then((result) => result.isConfirmed);
    }

    /**
     * Mostrar mensaje de éxito
     */
    showSuccess(message) {
        Swal.fire({
            title: '¡Éxito!',
            text: message,
            icon: 'success',
            timer: 3000
        });
    }

    /**
     * Mostrar mensaje de error
     */
    showError(message) {
        // No mostrar errores de geolocalización si ya hay ubicaciones configuradas
        if (this.workLocations.length > 0 && 
            (message.includes('ubicación') || message.includes('geolocalización'))) {
            console.warn('Error de geolocalización ignorado porque hay ubicaciones configuradas:', message);
            return;
        }
        
        Swal.fire({
            title: 'Error',
            text: message,
            icon: 'error'
        });
    }

    /**
     * Registrar actividad en logs
     */
    logActivity(accion, detalles) {
        // Aquí podrías enviar logs al servidor si es necesario
        console.log(`Actividad: ${accion} - ${detalles}`);
    }

    /**
     * Configurar event listeners
     */
    setupEventListeners() {
        // Solo configurar event listeners para fecha y hora
        // Los botones usan onclick en el HTML para evitar conflictos
        
        // Event listener para actualizar fecha y hora
        this.updateDateTime();
        setInterval(() => this.updateDateTime(), 1000);
    }

    /**
     * Actualizar fecha y hora
     */
    updateDateTime() {
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

    /**
     * Limpiar recursos al destruir
     */
    destroy() {
        if (this.verificationInterval) {
            clearInterval(this.verificationInterval);
            this.verificationInterval = null;
        }
        this.isCheckingLocation = false;
    }

    /**
     * Pausar verificación de ubicación
     */
    pauseLocationVerification() {
        if (this.verificationInterval) {
            clearInterval(this.verificationInterval);
            this.verificationInterval = null;
        }
    }

    /**
     * Reanudar verificación de ubicación
     */
    resumeLocationVerification() {
        if (!this.verificationInterval) {
            this.startLocationVerification();
        }
    }

    /**
     * Verificar ubicación manualmente (para botones de refresh)
     */
    async checkLocationManual() {
        console.log('Verificación manual de ubicación iniciada...');
        
        // Si hay ubicaciones configuradas, intentar obtener geolocalización una vez
        if (this.workLocations.length > 0) {
            console.log('Intentando obtener ubicación para verificar proximidad...');
            this.isCheckingLocation = false; // Resetear bandera para permitir verificación manual
            
            try {
                const position = await this.getCurrentPosition();
                this.currentPosition = position;
                this.userLocation = {
                    lat: position.coords.latitude,
                    lng: position.coords.longitude
                };
                
                document.getElementById('currentLocation').textContent = 'Ubicación detectada';
                await this.verifyWorkArea();
                this.updateLastVerification();
                
                console.log('Verificación manual completada exitosamente');
            } catch (error) {
                console.log('No se pudo obtener ubicación en verificación manual, manteniendo estado actual');
                // No mostrar errores en verificación manual si hay ubicaciones configuradas
            }
        } else {
            // Si no hay ubicaciones configuradas, usar el método normal
            this.isCheckingLocation = false;
            await this.checkLocation();
        }
    }
}

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    window.checadorManager = new ChecadorManager();
    window.checadorManager.init();
});

// Funciones globales para compatibilidad con onclick
function registrarEntrada() {
    console.log('Función global registrarEntrada llamada');
    if (window.checadorManager) {
        console.log('ChecadorManager encontrado, llamando registrarEntrada...');
        window.checadorManager.registrarEntrada();
    } else {
        console.error('ChecadorManager no encontrado');
    }
}

function registrarSalida() {
    console.log('Función global registrarSalida llamada');
    if (window.checadorManager) {
        console.log('ChecadorManager encontrado, llamando registrarSalida...');
        window.checadorManager.registrarSalida();
    } else {
        console.error('ChecadorManager no encontrado');
    }
}

function setupLocation() {
    if (window.checadorManager) {
        window.checadorManager.setupLocation();
    }
}

function checkLocationManual() {
    if (window.checadorManager) {
        window.checadorManager.checkLocationManual();
    }
} 