/**
 * Sistema de notificaciones en tiempo real
 * Maneja notificaciones push, sonidos y actualizaciones automáticas
 */

class NotificacionesManager {
    constructor() {
        this.usuarioId = window.usuarioId;
        this.intervalId = null;
        this.audioContext = null;
        this.notificaciones = [];
        this.init();
    }
    
    init() {
        this.cargarConfiguracion();
        this.inicializarAudio();
        this.iniciarPolling();
        this.configurarEventListeners();
    }
    
    /**
     * Cargar configuración de notificaciones
     */
    async cargarConfiguracion() {
        try {
            const response = await fetch('api/chat_api.php?action=configuracion');
            const data = await response.json();
            
            if (data.success) {
                this.configuracion = data.data;
            } else {
                this.configuracion = {
                    notificaciones_sonido: true,
                    notificaciones_push: true,
                    tema_oscuro: false
                };
            }
        } catch (error) {
            console.error('Error al cargar configuración:', error);
            this.configuracion = {
                notificaciones_sonido: true,
                notificaciones_push: true,
                tema_oscuro: false
            };
        }
    }
    
    /**
     * Inicializar contexto de audio para notificaciones
     */
    inicializarAudio() {
        try {
            this.audioContext = new (window.AudioContext || window.webkitAudioContext)();
        } catch (error) {
            console.warn('No se pudo inicializar el contexto de audio:', error);
        }
    }
    
    /**
     * Iniciar polling de notificaciones
     */
    iniciarPolling() {
        // Verificar notificaciones cada 5 segundos
        this.intervalId = setInterval(() => {
            this.verificarNotificaciones();
        }, 5000);
        
        // Verificar inmediatamente
        this.verificarNotificaciones();
    }
    
    /**
     * Configurar event listeners
     */
    configurarEventListeners() {
        // Mostrar notificación cuando la página se vuelve visible
        document.addEventListener('visibilitychange', () => {
            if (!document.hidden) {
                this.verificarNotificaciones();
            }
        });
        
        // Solicitar permisos de notificación
        if ('Notification' in window && Notification.permission === 'default') {
            Notification.requestPermission();
        }
    }
    
    /**
     * Verificar notificaciones nuevas
     */
    async verificarNotificaciones() {
        try {
            const response = await fetch(`api/chat_api.php?action=notificaciones&usuario_id=${this.usuarioId}`);
            const data = await response.json();
            
            if (data.success && data.data.length > 0) {
                this.procesarNotificaciones(data.data);
            }
        } catch (error) {
            console.error('Error al verificar notificaciones:', error);
        }
    }
    
    /**
     * Procesar notificaciones recibidas
     */
    procesarNotificaciones(notificaciones) {
        notificaciones.forEach(notificacion => {
            // Verificar si ya se mostró esta notificación
            if (!this.notificaciones.includes(notificacion.ID_Notificacion)) {
                this.mostrarNotificacion(notificacion);
                this.notificaciones.push(notificacion.ID_Notificacion);
            }
        });
    }
    
    /**
     * Mostrar notificación
     */
    mostrarNotificacion(notificacion) {
        // Reproducir sonido si está habilitado
        if (this.configuracion.notificaciones_sonido) {
            this.reproducirSonido();
        }
        
        // Mostrar notificación del navegador si está habilitada
        if (this.configuracion.notificaciones_push && 'Notification' in window && Notification.permission === 'granted') {
            this.mostrarNotificacionNavegador(notificacion);
        }
        
        // Mostrar notificación en la UI
        this.mostrarNotificacionUI(notificacion);
        
        // Actualizar contador de notificaciones
        this.actualizarContadorNotificaciones();
    }
    
    /**
     * Reproducir sonido de notificación
     */
    reproducirSonido() {
        if (this.audioContext) {
            try {
                const oscillator = this.audioContext.createOscillator();
                const gainNode = this.audioContext.createGain();
                
                oscillator.connect(gainNode);
                gainNode.connect(this.audioContext.destination);
                
                oscillator.frequency.setValueAtTime(800, this.audioContext.currentTime);
                oscillator.frequency.setValueAtTime(600, this.audioContext.currentTime + 0.1);
                
                gainNode.gain.setValueAtTime(0.3, this.audioContext.currentTime);
                gainNode.gain.exponentialRampToValueAtTime(0.01, this.audioContext.currentTime + 0.3);
                
                oscillator.start(this.audioContext.currentTime);
                oscillator.stop(this.audioContext.currentTime + 0.3);
            } catch (error) {
                console.warn('Error al reproducir sonido:', error);
            }
        }
    }
    
    /**
     * Mostrar notificación del navegador
     */
    mostrarNotificacionNavegador(notificacion) {
        const opciones = {
            body: notificacion.Mensaje,
            icon: 'favicon.ico',
            badge: 'favicon.ico',
            tag: 'chat-notification',
            requireInteraction: false,
            silent: false
        };
        
        const notification = new Notification('Nuevo mensaje en el chat', opciones);
        
        // Cerrar notificación después de 5 segundos
        setTimeout(() => {
            notification.close();
        }, 5000);
        
        // Abrir chat al hacer clic en la notificación
        notification.onclick = () => {
            window.focus();
            window.location.href = 'Mensajes.php';
            notification.close();
        };
    }
    
    /**
     * Mostrar notificación en la UI
     */
    mostrarNotificacionUI(notificacion) {
        // Crear elemento de notificación
        const notificacionElement = document.createElement('div');
        notificacionElement.className = 'notificacion-toast';
        notificacionElement.innerHTML = `
            <div class="notificacion-contenido">
                <div class="notificacion-icono">
                    <i class="zmdi zmdi-comment-text"></i>
                </div>
                <div class="notificacion-texto">
                    <div class="notificacion-titulo">Nuevo mensaje</div>
                    <div class="notificacion-mensaje">${notificacion.Mensaje}</div>
                </div>
                <button class="notificacion-cerrar" onclick="this.parentElement.parentElement.remove()">
                    <i class="zmdi zmdi-close"></i>
                </button>
            </div>
        `;
        
        // Agregar al contenedor de notificaciones
        let contenedor = document.getElementById('notificaciones-contenedor');
        if (!contenedor) {
            contenedor = document.createElement('div');
            contenedor.id = 'notificaciones-contenedor';
            contenedor.className = 'notificaciones-contenedor';
            document.body.appendChild(contenedor);
        }
        
        contenedor.appendChild(notificacionElement);
        
        // Animar entrada
        setTimeout(() => {
            notificacionElement.classList.add('mostrar');
        }, 100);
        
        // Remover después de 5 segundos
        setTimeout(() => {
            notificacionElement.classList.add('ocultar');
            setTimeout(() => {
                if (notificacionElement.parentElement) {
                    notificacionElement.remove();
                }
            }, 300);
        }, 5000);
    }
    
    /**
     * Actualizar contador de notificaciones
     */
    async actualizarContadorNotificaciones() {
        try {
            const response = await fetch(`api/chat_api.php?action=contador_notificaciones&usuario_id=${this.usuarioId}`);
            const data = await response.json();
            
            if (data.success) {
                const contador = document.getElementById('contador-notificaciones');
                if (contador) {
                    contador.textContent = data.count;
                    contador.style.display = data.count > 0 ? 'block' : 'none';
                }
            }
        } catch (error) {
            console.error('Error al actualizar contador:', error);
        }
    }
    
    /**
     * Marcar notificación como leída
     */
    async marcarComoLeida(notificacionId) {
        try {
            const response = await fetch('api/chat_api.php?action=marcar_notificacion_leida', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    notificacion_id: notificacionId
                })
            });
            
            if (response.ok) {
                this.actualizarContadorNotificaciones();
            }
        } catch (error) {
            console.error('Error al marcar notificación como leída:', error);
        }
    }
    
    /**
     * Detener polling
     */
    detener() {
        if (this.intervalId) {
            clearInterval(this.intervalId);
            this.intervalId = null;
        }
    }
}

// Inicializar sistema de notificaciones cuando se carga la página
document.addEventListener('DOMContentLoaded', () => {
    if (window.usuarioId) {
        window.notificacionesManager = new NotificacionesManager();
    }
});

// Limpiar al salir de la página
window.addEventListener('beforeunload', () => {
    if (window.notificacionesManager) {
        window.notificacionesManager.detener();
    }
});
