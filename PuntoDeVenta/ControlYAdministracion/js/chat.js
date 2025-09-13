/**
 * Sistema de Chat - JavaScript
 * Maneja toda la funcionalidad del frontend del chat
 */

class ChatSystem {
    constructor() {
        this.conversacionActual = null;
        this.mensajes = [];
        this.conversaciones = [];
        this.usuarios = [];
        this.configuracion = {};
        this.ultimoMensajeId = 0;
        this.cargandoMensajes = false;
        this.intervaloActualizacion = null;
        this.intervaloReconexion = null;
        this.conectado = true;
        
        this.init();
    }
    
    init() {
        this.cargarConfiguracion();
        this.cargarConversaciones();
        this.cargarUsuarios();
        this.setupEventListeners();
        this.iniciarActualizacionAutomatica();
        this.setupNotificaciones();
    }
    
    /**
     * Configurar event listeners
     */
    setupEventListeners() {
        // Formulario de mensaje
        document.getElementById('form-mensaje').addEventListener('submit', (e) => {
            e.preventDefault();
            this.enviarMensaje();
        });
        
        // Input de mensaje
        document.getElementById('input-mensaje').addEventListener('keypress', (e) => {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                this.enviarMensaje();
            }
        });
        
        // Input de archivo
        document.getElementById('input-archivo').addEventListener('change', (e) => {
            this.subirArchivo(e.target.files[0]);
        });
        
        // Filtros de conversaciones
        document.querySelectorAll('input[name="filtroTipo"]').forEach(radio => {
            radio.addEventListener('change', () => {
                this.filtrarConversaciones(radio.value);
            });
        });
        
        // Buscador de conversaciones
        document.querySelector('input[placeholder="Buscar conversaciones..."]').addEventListener('input', (e) => {
            this.buscarConversaciones(e.target.value);
        });
        
        // Detectar cuando el usuario está escribiendo
        let timeoutEscritura;
        document.getElementById('input-mensaje').addEventListener('input', () => {
            this.mostrarIndicadorEscritura();
            clearTimeout(timeoutEscritura);
            timeoutEscritura = setTimeout(() => {
                this.ocultarIndicadorEscritura();
            }, 1000);
        });
        
        // Detectar cambios de visibilidad de la página
        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                this.pausarActualizacion();
            } else {
                this.reanudarActualizacion();
            }
        });
    }
    
    /**
     * Cargar configuración del usuario
     */
    async cargarConfiguracion() {
        try {
            const response = await fetch('api/chat_api.php?action=configuracion');
            const data = await response.json();
            
            if (data.success) {
                this.configuracion = data.data;
                this.aplicarConfiguracion();
            }
        } catch (error) {
            console.error('Error al cargar configuración:', error);
        }
    }
    
    /**
     * Aplicar configuración cargada
     */
    aplicarConfiguracion() {
        // Aplicar tema oscuro
        if (this.configuracion.tema_oscuro) {
            document.documentElement.setAttribute('data-theme', 'dark');
        }
        
        // Configurar notificaciones
        if (this.configuracion.notificaciones_sonido) {
            this.habilitarNotificacionesSonido();
        }
    }
    
    /**
     * Cargar conversaciones
     */
    async cargarConversaciones() {
        try {
            const response = await fetch('api/chat_api.php?action=conversaciones');
            const data = await response.json();
            
            if (data.success) {
                this.conversaciones = data.data;
                this.renderizarConversaciones();
            }
        } catch (error) {
            console.error('Error al cargar conversaciones:', error);
            this.mostrarError('Error al cargar conversaciones');
        }
    }
    
    /**
     * Renderizar lista de conversaciones
     */
    renderizarConversaciones() {
        const container = document.getElementById('lista-conversaciones');
        container.innerHTML = '';
        
        if (this.conversaciones.length === 0) {
            container.innerHTML = `
                <div class="text-center text-muted py-4">
                    <i class="fa fa-comments fa-2x mb-2"></i>
                    <p>No hay conversaciones</p>
                </div>
            `;
            return;
        }
        
        this.conversaciones.forEach(conversacion => {
            const item = this.crearElementoConversacion(conversacion);
            container.appendChild(item);
        });
    }
    
    /**
     * Crear elemento de conversación
     */
    crearElementoConversacion(conversacion) {
        const div = document.createElement('div');
        div.className = 'conversacion-item';
        div.dataset.conversacionId = conversacion.id_conversacion;
        
        const tiempo = this.formatearTiempo(conversacion.ultimo_mensaje_fecha);
        const mensajesNoLeidos = conversacion.mensajes_no_leidos || 0;
        
        div.innerHTML = `
            <div class="d-flex align-items-center">
                <div class="flex-shrink-0 me-3">
                    <img src="PerfilesImg/${conversacion.usuario_avatar || 'user.jpg'}" 
                         alt="" class="conversacion-avatar" 
                         onerror="this.src='img/user.jpg'">
                </div>
                <div class="conversacion-info flex-grow-1">
                    <div class="d-flex justify-content-between align-items-start">
                        <h6 class="conversacion-nombre mb-0">${conversacion.nombre_conversacion}</h6>
                        <small class="conversacion-tiempo">${tiempo}</small>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <p class="conversacion-ultimo-mensaje mb-0 text-truncate">${conversacion.ultimo_mensaje || 'Sin mensajes'}</p>
                        ${mensajesNoLeidos > 0 ? `<span class="conversacion-no-leidos">${mensajesNoLeidos}</span>` : ''}
                    </div>
                </div>
            </div>
        `;
        
        div.addEventListener('click', () => {
            this.seleccionarConversacion(conversacion.id_conversacion);
        });
        
        return div;
    }
    
    /**
     * Seleccionar conversación
     */
    async seleccionarConversacion(conversacionId) {
        // Actualizar UI
        document.querySelectorAll('.conversacion-item').forEach(item => {
            item.classList.remove('active');
        });
        document.querySelector(`[data-conversacion-id="${conversacionId}"]`).classList.add('active');
        
        // Mostrar área de chat
        this.conversacionActual = conversacionId;
        this.mostrarAreaChat();
        
        // Cargar mensajes
        await this.cargarMensajes(conversacionId);
    }
    
    /**
     * Mostrar área de chat
     */
    mostrarAreaChat() {
        document.getElementById('chat-vacio').style.display = 'none';
        document.getElementById('chat-header').style.display = 'block';
        document.getElementById('chat-mensajes').style.display = 'block';
        document.getElementById('chat-input').style.display = 'block';
        
        // Actualizar header
        const conversacion = this.conversaciones.find(c => c.id_conversacion == this.conversacionActual);
        if (conversacion) {
            document.getElementById('chat-nombre').textContent = conversacion.nombre_conversacion;
            document.getElementById('chat-info').textContent = `${conversacion.total_participantes} participantes`;
            document.getElementById('chat-avatar').src = `PerfilesImg/${conversacion.usuario_avatar || 'user.jpg'}`;
        }
    }
    
    /**
     * Cargar mensajes de una conversación
     */
    async cargarMensajes(conversacionId, offset = 0) {
        if (this.cargandoMensajes) return;
        
        this.cargandoMensajes = true;
        
        try {
            const response = await fetch(`api/chat_api.php?action=mensajes&conversacion_id=${conversacionId}&offset=${offset}&limite=${this.configuracion.mensajes_por_pagina || 50}`);
            const data = await response.json();
            
            console.log('Respuesta de la API:', data); // Debug
            
            if (data.success) {
                // Asegurar que data.data sea un array
                const mensajesData = Array.isArray(data.data) ? data.data : [];
                
                if (offset === 0) {
                    this.mensajes = mensajesData;
                } else {
                    this.mensajes = [...mensajesData, ...this.mensajes];
                }
                
                console.log('Mensajes cargados:', this.mensajes); // Debug
                this.renderizarMensajes();
                this.scrollToBottom();
            } else {
                console.error('Error en la API:', data.error);
                this.mostrarError(data.error || 'Error al cargar mensajes');
                this.mensajes = []; // Asegurar que sea un array vacío
            }
        } catch (error) {
            console.error('Error al cargar mensajes:', error);
            this.mostrarError('Error al cargar mensajes');
            this.mensajes = []; // Asegurar que sea un array vacío
        } finally {
            this.cargandoMensajes = false;
        }
    }
    
    /**
     * Renderizar mensajes
     */
    renderizarMensajes() {
        const container = document.getElementById('chat-mensajes');
        container.innerHTML = '';
        
        // Verificar que this.mensajes sea un array
        if (!Array.isArray(this.mensajes)) {
            console.error('this.mensajes no es un array:', this.mensajes);
            this.mensajes = [];
            return;
        }
        
        this.mensajes.forEach(mensaje => {
            const elemento = this.crearElementoMensaje(mensaje);
            container.appendChild(elemento);
        });
    }
    
    /**
     * Crear elemento de mensaje
     */
    crearElementoMensaje(mensaje) {
        const div = document.createElement('div');
        div.className = `mensaje-container ${mensaje.usuario_id == this.getUsuarioActual() ? 'propio' : 'otro'}`;
        div.dataset.mensajeId = mensaje.id_mensaje;
        
        const tiempo = this.formatearTiempo(mensaje.fecha_envio);
        const editado = mensaje.editado ? '<small class="mensaje-editado">(editado)</small>' : '';
        
        let contenido = '';
        
        // Mensaje de respuesta
        if (mensaje.mensaje_respuesta_id) {
            contenido += `
                <div class="mensaje-respuesta">
                    <div class="mensaje-respuesta-usuario">${mensaje.mensaje_respuesta_usuario_nombre}</div>
                    <div class="mensaje-respuesta-texto">${mensaje.mensaje_respuesta}</div>
                </div>
            `;
        }
        
        // Contenido del mensaje
        if (mensaje.tipo_mensaje === 'texto') {
            contenido += `<p class="mensaje-texto">${this.escapeHtml(mensaje.mensaje)}</p>`;
        } else {
            contenido += this.crearContenidoArchivo(mensaje);
        }
        
        div.innerHTML = `
            <div class="mensaje-burbuja">
                ${mensaje.usuario_id != this.getUsuarioActual() ? `<div class="mensaje-usuario">${mensaje.usuario_nombre}</div>` : ''}
                ${contenido}
                <div class="mensaje-tiempo">${tiempo} ${editado}</div>
            </div>
        `;
        
        // Agregar event listeners para acciones del mensaje
        this.agregarEventListenersMensaje(div, mensaje);
        
        return div;
    }
    
    /**
     * Crear contenido de archivo
     */
    crearContenidoArchivo(mensaje) {
        const iconos = {
            'imagen': 'fa-image',
            'video': 'fa-video',
            'audio': 'fa-volume-up',
            'archivo': 'fa-file'
        };
        
        const icono = iconos[mensaje.tipo_mensaje] || 'fa-file';
        const tamaño = this.formatearTamaño(mensaje.archivo_tamaño);
        
        return `
            <div class="mensaje-archivo">
                <i class="fa ${icono} mensaje-archivo-icono"></i>
                <div class="mensaje-archivo-info">
                    <div class="mensaje-archivo-nombre">${mensaje.archivo_nombre}</div>
                    <div class="mensaje-archivo-tamaño">${tamaño}</div>
                </div>
                <a href="${mensaje.archivo_url}" target="_blank" class="btn btn-sm btn-outline-primary">
                    <i class="fa fa-download"></i>
                </a>
            </div>
        `;
    }
    
    /**
     * Agregar event listeners a un mensaje
     */
    agregarEventListenersMensaje(elemento, mensaje) {
        // Click derecho para menú contextual
        elemento.addEventListener('contextmenu', (e) => {
            e.preventDefault();
            this.mostrarMenuContextual(e, mensaje);
        });
        
        // Doble click para responder
        elemento.addEventListener('dblclick', () => {
            this.responderMensaje(mensaje);
        });
    }
    
    /**
     * Enviar mensaje
     */
    async enviarMensaje() {
        const input = document.getElementById('input-mensaje');
        const mensaje = input.value.trim();
        
        if (!mensaje || !this.conversacionActual) return;
        
        // Limpiar input
        input.value = '';
        
        // Crear mensaje temporal
        const mensajeTemporal = {
            id_mensaje: 'temp_' + Date.now(),
            usuario_id: this.getUsuarioActual(),
            usuario_nombre: this.getNombreUsuario(),
            mensaje: mensaje,
            tipo_mensaje: 'texto',
            fecha_envio: new Date().toISOString(),
            editado: false,
            temporal: true
        };
        
        // Agregar a la UI inmediatamente
        this.agregarMensajeTemporal(mensajeTemporal);
        
        try {
            const response = await fetch('api/chat_api.php?action=enviar_mensaje', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    conversacion_id: this.conversacionActual,
                    mensaje: mensaje,
                    tipo_mensaje: 'texto'
                })
            });
            
            const data = await response.json();
            
            if (data.success) {
                // Reemplazar mensaje temporal con el real
                this.reemplazarMensajeTemporal(mensajeTemporal.id_mensaje, data.mensaje_id);
            } else {
                // Mostrar error
                this.mostrarErrorMensaje(mensajeTemporal.id_mensaje, data.error);
            }
        } catch (error) {
            console.error('Error al enviar mensaje:', error);
            this.mostrarErrorMensaje(mensajeTemporal.id_mensaje, 'Error de conexión');
        }
    }
    
    /**
     * Agregar mensaje temporal
     */
    agregarMensajeTemporal(mensaje) {
        const elemento = this.crearElementoMensaje(mensaje);
        elemento.classList.add('mensaje-enviando');
        elemento.dataset.mensajeId = mensaje.id_mensaje;
        
        document.getElementById('chat-mensajes').appendChild(elemento);
        this.scrollToBottom();
    }
    
    /**
     * Reemplazar mensaje temporal
     */
    reemplazarMensajeTemporal(temporalId, mensajeId) {
        const elemento = document.querySelector(`[data-mensaje-id="${temporalId}"]`);
        if (elemento) {
            elemento.classList.remove('mensaje-enviando');
            elemento.dataset.mensajeId = mensajeId;
        }
    }
    
    /**
     * Mostrar error en mensaje
     */
    mostrarErrorMensaje(temporalId, error) {
        const elemento = document.querySelector(`[data-mensaje-id="${temporalId}"]`);
        if (elemento) {
            elemento.classList.remove('mensaje-enviando');
            elemento.classList.add('mensaje-error');
            elemento.querySelector('.mensaje-texto').textContent = `Error: ${error}`;
        }
    }
    
    /**
     * Subir archivo
     */
    async subirArchivo(archivo) {
        if (!archivo || !this.conversacionActual) return;
        
        const formData = new FormData();
        formData.append('conversacion_id', this.conversacionActual);
        formData.append('mensaje', '');
        formData.append('archivo', archivo);
        
        try {
            const response = await fetch('api/chat_api.php?action=subir_archivo', {
                method: 'POST',
                body: formData
            });
            
            const data = await response.json();
            
            if (data.success) {
                // Recargar mensajes para mostrar el archivo
                await this.cargarMensajes(this.conversacionActual);
            } else {
                this.mostrarError(data.error || 'Error al subir archivo');
            }
        } catch (error) {
            console.error('Error al subir archivo:', error);
            this.mostrarError('Error al subir archivo');
        }
    }
    
    /**
     * Cargar usuarios disponibles
     */
    async cargarUsuarios() {
        try {
            const response = await fetch('api/chat_api.php?action=usuarios');
            const data = await response.json();
            
            if (data.success) {
                this.usuarios = data.data;
            }
        } catch (error) {
            console.error('Error al cargar usuarios:', error);
        }
    }
    
    /**
     * Iniciar actualización automática
     */
    iniciarActualizacionAutomatica() {
        this.intervaloActualizacion = setInterval(() => {
            this.actualizarConversaciones();
            if (this.conversacionActual) {
                this.actualizarMensajes();
            }
        }, 3000); // Actualizar cada 3 segundos
    }
    
    /**
     * Actualizar conversaciones
     */
    async actualizarConversaciones() {
        try {
            const response = await fetch('api/chat_api.php?action=conversaciones');
            const data = await response.json();
            
            if (data.success) {
                const conversacionActual = this.conversacionActual;
                this.conversaciones = data.data;
                this.renderizarConversaciones();
                
                // Restaurar selección
                if (conversacionActual) {
                    const elemento = document.querySelector(`[data-conversacion-id="${conversacionActual}"]`);
                    if (elemento) {
                        elemento.classList.add('active');
                    }
                }
            }
        } catch (error) {
            console.error('Error al actualizar conversaciones:', error);
        }
    }
    
    /**
     * Actualizar mensajes
     */
    async actualizarMensajes() {
        if (!this.conversacionActual || this.cargandoMensajes) return;
        
        try {
            const response = await fetch(`api/chat_api.php?action=mensajes&conversacion_id=${this.conversacionActual}&offset=0&limite=10`);
            const data = await response.json();
            
            if (data.success && data.data.length > 0) {
                const ultimoMensaje = data.data[data.data.length - 1];
                if (ultimoMensaje.id_mensaje > this.ultimoMensajeId) {
                    this.cargarMensajes(this.conversacionActual);
                    this.ultimoMensajeId = ultimoMensaje.id_mensaje;
                }
            }
        } catch (error) {
            console.error('Error al actualizar mensajes:', error);
        }
    }
    
    /**
     * Scroll al final de los mensajes
     */
    scrollToBottom() {
        const container = document.getElementById('chat-mensajes');
        container.scrollTop = container.scrollHeight;
    }
    
    /**
     * Formatear tiempo
     */
    formatearTiempo(fecha) {
        const ahora = new Date();
        const mensajeFecha = new Date(fecha);
        const diff = ahora - mensajeFecha;
        
        if (diff < 60000) { // Menos de 1 minuto
            return 'Ahora';
        } else if (diff < 3600000) { // Menos de 1 hora
            return Math.floor(diff / 60000) + 'm';
        } else if (diff < 86400000) { // Menos de 1 día
            return Math.floor(diff / 3600000) + 'h';
        } else {
            return mensajeFecha.toLocaleDateString();
        }
    }
    
    /**
     * Formatear tamaño de archivo
     */
    formatearTamaño(bytes) {
        if (bytes === 0) return '0 Bytes';
        
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }
    
    /**
     * Escape HTML
     */
    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    /**
     * Obtener ID del usuario actual
     */
    getUsuarioActual() {
        return window.usuarioId || 1;
    }
    
    /**
     * Obtener nombre del usuario actual
     */
    getNombreUsuario() {
        return window.nombreUsuario || 'Usuario';
    }
    
    /**
     * Mostrar error
     */
    mostrarError(mensaje) {
        // Usar SweetAlert2 si está disponible
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: mensaje
            });
        } else {
            alert('Error: ' + mensaje);
        }
    }
    
    /**
     * Configurar notificaciones
     */
    setupNotificaciones() {
        if ('Notification' in window && this.configuracion.notificaciones_push) {
            Notification.requestPermission();
        }
    }
    
    /**
     * Habilitar notificaciones de sonido
     */
    habilitarNotificacionesSonido() {
        // Implementar notificaciones de sonido
    }
    
    /**
     * Pausar actualización
     */
    pausarActualizacion() {
        if (this.intervaloActualizacion) {
            clearInterval(this.intervaloActualizacion);
        }
    }
    
    /**
     * Reanudar actualización
     */
    reanudarActualizacion() {
        this.iniciarActualizacionAutomatica();
    }
}

// Funciones globales para los modales
function crearNuevaConversacion() {
    const modal = new bootstrap.Modal(document.getElementById('modalNuevaConversacion'));
    modal.show();
}

function abrirConfiguracion() {
    const modal = new bootstrap.Modal(document.getElementById('modalConfiguracion'));
    modal.show();
}

function abrirSelectorArchivos() {
    document.getElementById('input-archivo').click();
}

function mostrarEmojis() {
    // Implementar selector de emojis
    console.log('Mostrar emojis');
}

// Inicializar el sistema de chat cuando se carga la página
document.addEventListener('DOMContentLoaded', () => {
    window.chatSystem = new ChatSystem();
});
