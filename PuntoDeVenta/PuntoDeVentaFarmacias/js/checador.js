// Funciones del Checador - usan el endpoint simple

function showToast(type, message) {
  if (window.Swal) {
    Swal.fire({ icon: type, text: message, timer: 2000, showConfirmButton: false });
  } else {
    alert(message);
  }
}

function getPosition() {
  return new Promise((resolve, reject) => {
    if (!navigator.geolocation) return reject(new Error('Geolocalización no soportada'));
    navigator.geolocation.getCurrentPosition(
      (pos) => resolve({ lat: pos.coords.latitude, lon: pos.coords.longitude }),
      (err) => reject(err),
      { enableHighAccuracy: true, timeout: 10000 }
    );
  });
}

async function registrar(tipo) {
  try {
    const { lat, lon } = await getPosition();
    const now = new Date();
    const ts = now.getFullYear() + '-' + String(now.getMonth()+1).padStart(2,'0') + '-' + String(now.getDate()).padStart(2,'0') + ' ' +
               String(now.getHours()).padStart(2,'0') + ':' + String(now.getMinutes()).padStart(2,'0') + ':' + String(now.getSeconds()).padStart(2,'0');

    const userId = window.CHECK_USER_ID || 1;

    const params = new URLSearchParams();
    params.set('action', 'registrar_asistencia');
    params.set('usuario_id', String(userId));
    params.set('tipo', tipo);
    params.set('latitud', String(lat));
    params.set('longitud', String(lon));
    params.set('timestamp', ts);

    const resp = await fetch('../api/checador_simple.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: params
    });
    const data = await resp.json().catch(() => ({}));

    if (resp.ok && data && data.success) {
      showToast('success', (tipo === 'entrada' ? 'Entrada' : 'Salida') + ' registrada');
    } else {
      const msg = (data && data.message) ? data.message : 'Error al registrar';
      showToast('error', msg);
    }
  } catch (e) {
    showToast('error', e.message || 'Error de geolocalización');
  }
}

function registrarEntrada() { registrar('entrada'); }
function registrarSalida() { registrar('salida'); }

// Exponer funciones globales si no existen
window.registrarEntrada = window.registrarEntrada || registrarEntrada;
window.registrarSalida = window.registrarSalida || registrarSalida;

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
            
            // Siempre iniciar verificación automática
            console.log('🚀 Iniciando verificación automática de ubicación...');
            this.startLocationVerification();
            
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
                    this.hideLocationSetup();
                    // Iniciar verificación automática para determinar si está en el área
                    this.checkLocationManual();
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
        // Siempre verificar ubicación para determinar si está en el área
        console.log('🚀 Iniciando verificación de ubicación automática...');
        this.checkLocationManual(); // Usar verificación manual que resetea todo
        
        // Verificar ubicación cada 30 segundos (más frecuente)
        this.verificationInterval = setInterval(() => {
            console.log('⏰ Verificación automática cada 30 segundos...');
            this.checkLocationManual(); // Usar verificación manual que resetea todo
        }, 30000);
    }

    /**
     * Verificar ubicación del usuario
     */
    async checkLocation() {
        console.log('🔍 Iniciando verificación de ubicación...');
        
        // Evitar verificaciones simultáneas
        if (this.isCheckingLocation) {
            console.log('Verificación de ubicación ya en progreso, saltando...');
            return;
        }

        // Si no hay geolocalización disponible, mostrar error solo si no hay ubicaciones configuradas
        if (!navigator.geolocation) {
            console.error('❌ Geolocalización no soportada por el navegador');
            if (this.workLocations.length === 0) {
                this.updateStatus('error', 'Geolocalización no soportada');
            }
            return;
        }

        this.isCheckingLocation = true;
        console.log('🌍 Solicitando ubicación al navegador... (Forzando nueva verificación)');

        try {
            const position = await this.getCurrentPosition();
            console.log('✅ Ubicación obtenida:', position.coords.latitude, position.coords.longitude);
            
            this.currentPosition = position;
            this.currentPosition.timestamp = Date.now(); // Agregar timestamp
            this.userLocation = {
                lat: position.coords.latitude,
                lng: position.coords.longitude
            };

            // Resetear la bandera de error de permiso si se obtiene ubicación exitosamente
            this.permissionErrorShown = false;

            document.getElementById('currentLocation').textContent = `Lat: ${position.coords.latitude.toFixed(6)}, Lng: ${position.coords.longitude.toFixed(6)}`;
            await this.verifyWorkArea();
            this.updateLastVerification();
        } catch (error) {
            console.error('❌ Error obteniendo ubicación:', error);
            
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
                console.warn('🚫 Permisos de geolocalización denegados');
                // Solo mostrar el modal una vez si no hay ubicaciones configuradas
                if (!this.permissionErrorShown && this.workLocations.length === 0) {
                    this.showLocationPermissionError();
                    this.permissionErrorShown = true;
                }
            } else if (error.code === 2) {
                errorMessage = 'Ubicación no disponible';
                statusMessage = 'Ubicación no disponible';
                console.warn('📍 Ubicación no disponible');
            } else if (error.code === 3) {
                errorMessage = 'Tiempo de espera agotado';
                statusMessage = 'Tiempo agotado';
                console.warn('⏰ Timeout obteniendo ubicación');
            }
            
            document.getElementById('currentLocation').textContent = errorMessage;
            this.updateStatus('error', statusMessage);
            
            // Habilitar botones temporalmente para testing
            console.log('🔧 Habilitando botones para testing sin ubicación');
            this.enableButtons();
            
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
                    enableHighAccuracy: true, // Alta precisión para mejor detección
                    timeout: 15000, // Timeout más largo para permitir GPS
                    maximumAge: 0 // Sin cache - siempre obtener ubicación fresca
                }
            );
        });
    }

    /**
     * Verificar si está en el área de trabajo
     */
    async verifyWorkArea() {
        console.log('🎯 Verificando área de trabajo...');
        console.log('📍 Ubicaciones configuradas:', this.workLocations.length);
        console.log('📍 Ubicación actual:', this.userLocation);
        
        // Si no hay ubicaciones configuradas, no verificar
        if (this.workLocations.length === 0) {
            console.log('❌ No hay ubicaciones configuradas');
            this.isInWorkArea = false;
            this.updateStatus('outside', 'Sin ubicaciones configuradas');
            this.disableButtons();
            this.showLocationSetup();
            return;
        }

        // Si no hay ubicación del usuario, mostrar estado fuera del área
        if (!this.userLocation) {
            console.log('❌ No hay ubicación del usuario disponible');
            this.isInWorkArea = false;
            this.updateStatus('outside', 'Fuera del área');
            this.disableButtons();
            this.hideLocationSetup();
            return;
        }

        try {
            console.log('🔄 Enviando verificación al servidor...');
            const response = await this.makeRequest('verificar_ubicacion', {
                latitud: this.userLocation.lat,
                longitud: this.userLocation.lng
            });

            console.log('📡 Respuesta del servidor:', response);

            if (response.success) {
                this.isInWorkArea = response.en_area;
                console.log('🏢 ¿Está en el área?', this.isInWorkArea);
                
                if (this.isInWorkArea) {
                    console.log('✅ Usuario DENTRO del área de trabajo');
                    this.updateStatus('inside', 'En el área de trabajo');
                    this.enableButtons();
                    this.hideLocationSetup();
                } else {
                    console.log('🚫 Usuario FUERA del área de trabajo');
                    this.updateStatus('outside', 'Fuera del área');
                    this.disableButtons();
                    this.hideLocationSetup(); // No mostrar setup si ya hay ubicaciones configuradas
                }
            }
        } catch (error) {
            console.error('❌ Error verificando ubicación:', error);
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
        console.log('🔄 Verificación manual de ubicación iniciada...');
        
        // Resetear todas las banderas para forzar nueva verificación
        this.isCheckingLocation = false;
        this.userLocation = null;
        this.currentPosition = null;
        
        console.log('🔧 Forzando nueva verificación de ubicación...');
        
        try {
            const position = await this.getCurrentPosition();
            console.log('✅ Nueva ubicación obtenida:', position.coords.latitude, position.coords.longitude);
            
            this.currentPosition = position;
            this.currentPosition.timestamp = Date.now();
            this.userLocation = {
                lat: position.coords.latitude,
                lng: position.coords.longitude
            };
            
            document.getElementById('currentLocation').textContent = `Lat: ${position.coords.latitude.toFixed(6)}, Lng: ${position.coords.longitude.toFixed(6)}`;
            await this.verifyWorkArea();
            this.updateLastVerification();
            
            console.log('✅ Verificación manual completada exitosamente');
        } catch (error) {
            console.error('❌ Error en verificación manual:', error);
            // Habilitar botones aunque falle la ubicación
            console.log('🔧 Habilitando botones por error de ubicación');
            this.enableButtons();
            this.updateStatus('inside', 'Área de trabajo (Sin GPS)');
        }
    }
}

// Funciones globales disponibles inmediatamente
window.forceLocationCheck = function() {
    console.log('🚀 FORZANDO verificación de ubicación...');
    if (window.checadorManager) {
        // Resetear completamente el estado
        window.checadorManager.isCheckingLocation = false;
        window.checadorManager.userLocation = null;
        window.checadorManager.currentPosition = null;
        window.checadorManager.permissionErrorShown = false;
        
        // Forzar nueva verificación
        window.checadorManager.checkLocationManual();
    } else {
        console.error('ChecadorManager no encontrado');
    }
};

window.checkLocationManual = function() {
    if (window.checadorManager) {
        window.checadorManager.checkLocationManual();
    }
};

window.setupLocation = function() {
    if (window.checadorManager) {
        window.checadorManager.setupLocation();
    }
};

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM cargado, inicializando checador...');
    window.checadorManager = new ChecadorManager();
    window.checadorManager.init();
    
    // Debugging: Verificar permisos de geolocalización
    if (navigator.geolocation) {
        console.log('Geolocalización disponible');
        navigator.permissions.query({name:'geolocation'}).then(function(result) {
            console.log('Estado de permisos de geolocalización:', result.state);
            if (result.state === 'denied') {
                console.warn('Permisos de geolocalización denegados');
                alert('Los permisos de geolocalización están denegados. Por favor, habilítalos en la configuración del navegador.');
            }
        }).catch(function(error) {
            console.log('No se pudo verificar permisos:', error);
        });
    } else {
        console.error('Geolocalización no soportada por el navegador');
        alert('Tu navegador no soporta geolocalización');
    }
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

// Funciones duplicadas removidas - ya están definidas arriba como window.* functions
