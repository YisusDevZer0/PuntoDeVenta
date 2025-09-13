/**
 * Sistema de Chat Completo - Doctor Pez
 * Interfaz moderna y funcional para mensajería
 */

class ChatSystem {
    constructor() {
        this.conversaciones = [];
        this.mensajes = [];
        this.conversacionActual = null;
        this.configuracion = {
            notificaciones_sonido: true,
            notificaciones_push: true,
            tema_oscuro: false,
            mensajes_por_pagina: 50
        };
        this.usuarioId = window.usuarioId;
        this.nombreUsuario = window.nombreUsuario;
        this.sucursalId = window.sucursalId;
    }

    /**
     * Inicializar el sistema de chat
     */
    async init() {
        console.log('Inicializando sistema de chat...');
        
        try {
            // Cargar configuración
            await this.cargarConfiguracion();
            
            // Cargar conversaciones
            await this.cargarConversaciones();
            
            // Configurar event listeners
            this.configurarEventListeners();
            
            // Iniciar polling
            this.iniciarPolling();
            
            console.log('Sistema de chat inicializado correctamente');
        } catch (error) {
            console.error('Error al inicializar el chat:', error);
            this.mostrarError('Error al inicializar el sistema de chat');
        }
    }

    /**
     * Configurar event listeners
     */
    configurarEventListeners() {
        // Envío de mensajes con Enter
        document.getElementById('input-mensaje').addEventListener('keypress', (e) => {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                this.enviarMensaje();
            }
        });

        // Búsqueda de conversaciones
        document.getElementById('buscar-conversaciones').addEventListener('input', (e) => {
            this.filtrarConversaciones(e.target.value);
        });

        // Subida de archivos
        document.getElementById('input-archivo').addEventListener('change', (e) => {
            if (e.target.files.length > 0) {
                this.subirArchivo(e.target.files[0]);
            }
        });

        // Scroll infinito en mensajes
        const mensajesContainer = document.getElementById('mensajes-container');
        mensajesContainer.addEventListener('scroll', () => {
            if (mensajesContainer.scrollTop === 0) {
                this.cargarMasMensajes();
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
                this.configuracion = { ...this.configuracion, ...data.data };
                this.aplicarConfiguracion();
            }
        } catch (error) {
            console.error('Error al cargar configuración:', error);
        }
    }

    /**
     * Aplicar configuración
     */
    aplicarConfiguracion() {
        // Aplicar tema oscuro
        if (this.configuracion.tema_oscuro) {
            document.documentElement.setAttribute('data-theme', 'dark');
        } else {
            document.documentElement.removeAttribute('data-theme');
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
            } else {
                console.error('Error al cargar conversaciones:', data.error);
                this.mostrarError('Error al cargar conversaciones');
            }
        } catch (error) {
            console.error('Error al cargar conversaciones:', error);
            this.mostrarError('Error de conexión al cargar conversaciones');
        }
    }

    /**
     * Renderizar lista de conversaciones
     */
    renderizarConversaciones() {
        const container = document.getElementById('conversaciones-lista');
        
        if (this.conversaciones.length === 0) {
            container.innerHTML = `
                <div class="text-center p-4">
                    <i class="fas fa-comments fa-2x text-muted mb-3"></i>
                    <p class="text-muted">No hay conversaciones</p>
                    <button class="btn btn-primary btn-sm" onclick="crearNuevaConversacion()">
                        <i class="fas fa-plus"></i> Nueva conversación
                    </button>
                </div>
            `;
            return;
        }

        container.innerHTML = this.conversaciones.map(conversacion => `
            <div class="conversacion-item" data-conversacion-id="${conversacion.id_conversacion}" onclick="chatSystem.seleccionarConversacion(${conversacion.id_conversacion})">
                <img src="PerfilesImg/${conversacion.ultimo_mensaje_usuario_avatar || 'user.jpg'}" alt="Avatar" class="conversacion-avatar">
                <div class="conversacion-info">
                    <h6 class="conversacion-nombre">${conversacion.nombre_conversacion}</h6>
                    <p class="conversacion-ultimo-mensaje">${conversacion.ultimo_mensaje || 'Sin mensajes'}</p>
                </div>
                <div class="conversacion-meta">
                    <span class="conversacion-hora">${this.formatearHora(conversacion.ultimo_mensaje_fecha)}</span>
                    ${conversacion.mensajes_no_leidos > 0 ? `<span class="conversacion-badge">${conversacion.mensajes_no_leidos}</span>` : ''}
                </div>
                <div class="conversacion-tipo">${conversacion.tipo_conversacion}</div>
            </div>
        `).join('');
    }

    /**
     * Seleccionar conversación
     */
    async seleccionarConversacion(conversacionId) {
        this.conversacionActual = conversacionId;
        
        // Actualizar UI
        document.querySelectorAll('.conversacion-item').forEach(item => {
            item.classList.remove('active');
        });
        document.querySelector(`[data-conversacion-id="${conversacionId}"]`).classList.add('active');
        
        // Mostrar área de chat
        this.mostrarAreaChat();
        
        // Cargar mensajes
        await this.cargarMensajes(conversacionId);
        
        // Cargar participantes
        await this.cargarParticipantes(conversacionId);
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
            document.getElementById('chat-descripcion').textContent = `${conversacion.total_participantes} participantes • ${conversacion.tipo_conversacion}`;
            document.getElementById('chat-avatar').src = `PerfilesImg/${conversacion.ultimo_mensaje_usuario_avatar || 'user.jpg'}`;
        }
    }

    /**
     * Cargar mensajes de una conversación
     */
    async cargarMensajes(conversacionId) {
        try {
            const response = await fetch(`api/chat_api.php?action=mensajes&conversacion_id=${conversacionId}&limite=${this.configuracion.mensajes_por_pagina}`);
            const data = await response.json();
            
            if (data.success) {
                this.mensajes = Array.isArray(data.data) ? data.data : [];
                this.renderizarMensajes();
                this.scrollToBottom();
            } else {
                console.error('Error al cargar mensajes:', data.error);
                this.mensajes = [];
                this.renderizarMensajes();
            }
        } catch (error) {
            console.error('Error al cargar mensajes:', error);
            this.mensajes = [];
            this.renderizarMensajes();
        }
    }

    /**
     * Renderizar mensajes
     */
    renderizarMensajes() {
        const container = document.getElementById('mensajes-container');
        
        if (!Array.isArray(this.mensajes)) {
            console.error('this.mensajes no es un array:', this.mensajes);
            this.mensajes = [];
        }
        
        if (this.mensajes.length === 0) {
            container.innerHTML = `
                <div class="text-center p-4">
                    <i class="fas fa-comment-slash fa-2x text-muted mb-3"></i>
                    <p class="text-muted">No hay mensajes en esta conversación</p>
                    <p class="text-muted small">Sé el primero en enviar un mensaje</p>
                </div>
            `;
            return;
        }

        container.innerHTML = this.mensajes.map(mensaje => this.crearElementoMensaje(mensaje)).join('');
    }

    /**
     * Crear elemento de mensaje
     */
    crearElementoMensaje(mensaje) {
        const esPropio = mensaje.usuario_id == this.usuarioId;
        const hora = this.formatearHora(mensaje.fecha_envio);
        
        return `
            <div class="mensaje ${esPropio ? 'propio' : 'otro'}">
                <div class="mensaje-burbuja">
                    <p class="mensaje-contenido">${this.escapeHtml(mensaje.mensaje)}</p>
                    <div class="mensaje-meta">
                        <span class="mensaje-hora">${hora}</span>
                        ${mensaje.editado ? '<span class="mensaje-estado">editado</span>' : ''}
                        ${mensaje.tipo_mensaje !== 'texto' ? `<span class="mensaje-tipo">${mensaje.tipo_mensaje}</span>` : ''}
                    </div>
                </div>
            </div>
        `;
    }

    /**
     * Cargar participantes de una conversación
     */
    async cargarParticipantes(conversacionId) {
        try {
            const response = await fetch(`api/chat_api.php?action=participantes&conversacion_id=${conversacionId}`);
            const data = await response.json();
            
            if (data.success) {
                console.log('Participantes cargados:', data.data);
                // Aquí puedes actualizar la UI con los participantes si es necesario
            }
        } catch (error) {
            console.error('Error al cargar participantes:', error);
        }
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
            conversacion_id: this.conversacionActual,
            usuario_id: this.usuarioId,
            usuario_nombre: this.nombreUsuario,
            mensaje: mensaje,
            tipo_mensaje: 'texto',
            fecha_envio: new Date().toISOString(),
            editado: false,
            eliminado: false,
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
        const container = document.getElementById('mensajes-container');
        const elemento = document.createElement('div');
        elemento.className = 'mensaje propio';
        elemento.id = `mensaje-${mensaje.id_mensaje}`;
        elemento.innerHTML = this.crearElementoMensaje(mensaje);
        
        container.appendChild(elemento);
        this.scrollToBottom();
    }

    /**
     * Reemplazar mensaje temporal
     */
    reemplazarMensajeTemporal(tempId, realId) {
        const elemento = document.getElementById(`mensaje-${tempId}`);
        if (elemento) {
            elemento.id = `mensaje-${realId}`;
            elemento.classList.remove('temporal');
        }
    }

    /**
     * Mostrar error en mensaje
     */
    mostrarErrorMensaje(tempId, error) {
        const elemento = document.getElementById(`mensaje-${tempId}`);
        if (elemento) {
            elemento.classList.add('error');
            const contenido = elemento.querySelector('.mensaje-contenido');
            contenido.innerHTML = `❌ Error: ${error}`;
        }
    }

    /**
     * Subir archivo
     */
    async subirArchivo(archivo) {
        if (!this.conversacionActual) {
            this.mostrarError('Selecciona una conversación primero');
            return;
        }
        
        // Validar tamaño (10MB máximo)
        const maxSize = 10 * 1024 * 1024;
        if (archivo.size > maxSize) {
            this.mostrarError('El archivo es demasiado grande. Máximo 10MB');
            return;
        }
        
        // Validar tipo
        const allowedTypes = ['image/', 'video/', 'audio/', 'application/pdf', 'text/', 'application/msword', 'application/vnd.openxmlformats-officedocument'];
        const isValidType = allowedTypes.some(type => archivo.type.startsWith(type));
        
        if (!isValidType) {
            this.mostrarError('Tipo de archivo no permitido');
            return;
        }
        
        try {
            const formData = new FormData();
            formData.append('conversacion_id', this.conversacionActual);
            formData.append('mensaje', '');
            formData.append('archivo', archivo);
            
            const response = await fetch('api/chat_api.php?action=subir_archivo', {
                method: 'POST',
                body: formData
            });
            
            const data = await response.json();
            
            if (data.success) {
                await this.cargarMensajes(this.conversacionActual);
                this.mostrarExito('Archivo enviado exitosamente');
            } else {
                this.mostrarError('Error al subir archivo: ' + (data.error || 'Error desconocido'));
            }
        } catch (error) {
            console.error('Error al subir archivo:', error);
            this.mostrarError('Error de conexión al subir archivo');
        }
    }

    /**
     * Filtrar conversaciones
     */
    filtrarConversaciones(termino) {
        const items = document.querySelectorAll('.conversacion-item');
        const terminoLower = termino.toLowerCase();
        
        items.forEach(item => {
            const nombre = item.querySelector('.conversacion-nombre').textContent.toLowerCase();
            const mensaje = item.querySelector('.conversacion-ultimo-mensaje').textContent.toLowerCase();
            
            if (nombre.includes(terminoLower) || mensaje.includes(terminoLower)) {
                item.style.display = 'flex';
            } else {
                item.style.display = 'none';
            }
        });
    }

    /**
     * Scroll al final
     */
    scrollToBottom() {
        const container = document.getElementById('mensajes-container');
        container.scrollTop = container.scrollHeight;
    }

    /**
     * Cargar más mensajes (scroll infinito)
     */
    async cargarMasMensajes() {
        // Implementar scroll infinito si es necesario
    }

    /**
     * Iniciar polling
     */
    iniciarPolling() {
        // Actualizar conversaciones cada 30 segundos
        setInterval(() => {
            this.cargarConversaciones();
        }, 30000);
        
        // Actualizar mensajes cada 5 segundos si hay conversación activa
        setInterval(() => {
            if (this.conversacionActual) {
                this.cargarMensajes(this.conversacionActual);
            }
        }, 5000);
    }

    /**
     * Formatear hora
     */
    formatearHora(fecha) {
        if (!fecha) return '';
        const d = new Date(fecha);
        const ahora = new Date();
        const diff = ahora - d;
        
        if (diff < 60000) { // Menos de 1 minuto
            return 'Ahora';
        } else if (diff < 3600000) { // Menos de 1 hora
            return Math.floor(diff / 60000) + 'm';
        } else if (diff < 86400000) { // Menos de 1 día
            return Math.floor(diff / 3600000) + 'h';
        } else {
            return d.toLocaleDateString();
        }
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
     * Mostrar error
     */
    mostrarError(mensaje) {
        // Usar SweetAlert2 si está disponible, sino alert
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
     * Mostrar éxito
     */
    mostrarExito(mensaje) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'success',
                title: 'Éxito',
                text: mensaje,
                timer: 2000
            });
        } else {
            alert('Éxito: ' + mensaje);
        }
    }
}

// Funciones globales para los modales
async function crearNuevaConversacion() {
    const modal = new bootstrap.Modal(document.getElementById('modalNuevaConversacion'));
    modal.show();
    
    // Cargar usuarios disponibles
    await cargarUsuariosDisponibles();
}

async function cargarUsuariosDisponibles() {
    try {
        const response = await fetch('api/chat_api.php?action=usuarios');
        const data = await response.json();
        
        if (data.success) {
            const select = document.getElementById('participantes');
            select.innerHTML = '';
            
            data.data.forEach(usuario => {
                const option = document.createElement('option');
                option.value = usuario.Id_PvUser;
                option.textContent = `${usuario.Nombre_Apellidos} (${usuario.TipoUsuario})`;
                select.appendChild(option);
            });
        }
    } catch (error) {
        console.error('Error al cargar usuarios:', error);
    }
}

async function abrirConfiguracion() {
    const modal = new bootstrap.Modal(document.getElementById('modalConfiguracion'));
    modal.show();
    
    // Cargar configuración actual
    await cargarConfiguracionActual();
}

async function cargarConfiguracionActual() {
    try {
        const response = await fetch('api/chat_api.php?action=configuracion');
        const data = await response.json();
        
        if (data.success) {
            const config = data.data;
            document.getElementById('notificacionesSonido').checked = config.notificaciones_sonido || false;
            document.getElementById('notificacionesPush').checked = config.notificaciones_push || false;
            document.getElementById('temaOscuro').checked = config.tema_oscuro || false;
            document.getElementById('mensajesPorPagina').value = config.mensajes_por_pagina || 50;
        }
    } catch (error) {
        console.error('Error al cargar configuración:', error);
    }
}

function abrirSelectorArchivos() {
    document.getElementById('input-archivo').click();
}

function mostrarEmojis() {
    // Implementar selector de emojis
    console.log('Mostrar emojis');
}

async function crearConversacion() {
    const nombre = document.getElementById('nombreConversacion').value;
    const tipo = document.getElementById('tipoConversacion').value;
    const participantes = Array.from(document.getElementById('participantes').selectedOptions).map(option => option.value);
    
    if (!nombre.trim()) {
        alert('Por favor ingresa un nombre para la conversación');
        return;
    }
    
    try {
        const response = await fetch('api/chat_api.php?action=crear_conversacion', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                nombre: nombre,
                tipo_conversacion: tipo,
                participantes: participantes
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            // Cerrar modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('modalNuevaConversacion'));
            modal.hide();
            
            // Limpiar formulario
            document.getElementById('formNuevaConversacion').reset();
            
            // Recargar conversaciones
            if (window.chatSystem) {
                await window.chatSystem.cargarConversaciones();
            }
            
            alert('Conversación creada exitosamente');
        } else {
            alert('Error al crear conversación: ' + (data.error || 'Error desconocido'));
        }
    } catch (error) {
        console.error('Error al crear conversación:', error);
        alert('Error de conexión al crear conversación');
    }
}

async function guardarConfiguracion() {
    const config = {
        notificaciones_sonido: document.getElementById('notificacionesSonido').checked,
        notificaciones_push: document.getElementById('notificacionesPush').checked,
        tema_oscuro: document.getElementById('temaOscuro').checked,
        mensajes_por_pagina: parseInt(document.getElementById('mensajesPorPagina').value)
    };
    
    try {
        const response = await fetch('api/chat_api.php?action=actualizar_configuracion', {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                configuracion: config
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            // Cerrar modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('modalConfiguracion'));
            modal.hide();
            
            // Aplicar configuración
            if (window.chatSystem) {
                window.chatSystem.configuracion = { ...window.chatSystem.configuracion, ...config };
                window.chatSystem.aplicarConfiguracion();
            }
            
            alert('Configuración guardada exitosamente');
        } else {
            alert('Error al guardar configuración: ' + (data.error || 'Error desconocido'));
        }
    } catch (error) {
        console.error('Error al guardar configuración:', error);
        alert('Error de conexión al guardar configuración');
    }
}

function verInfoConversacion() {
    alert('Información de la conversación');
}

function enviarMensaje() {
    if (window.chatSystem) {
        window.chatSystem.enviarMensaje();
    }
}