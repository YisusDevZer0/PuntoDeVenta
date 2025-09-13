/**
 * Sistema de Recordatorios - JavaScript
 * Maneja la funcionalidad del frontend para el sistema de recordatorios
 */

class RecordatoriosSistema {
    constructor() {
        this.modal = document.getElementById('modal-recordatorio');
        this.form = document.getElementById('form-recordatorio');
        this.init();
    }

    init() {
        this.bindEvents();
        this.initializeDateInputs();
        this.setupAutoRefresh();
    }

    bindEvents() {
        // Botón nuevo recordatorio
        document.getElementById('btn-nuevo-recordatorio').addEventListener('click', () => {
            this.abrirModal();
        });

        // Botón crear primer recordatorio
        const btnCrearPrimero = document.getElementById('btn-crear-primer-recordatorio');
        if (btnCrearPrimero) {
            btnCrearPrimero.addEventListener('click', () => {
                this.abrirModal();
            });
        }

        // Botones de la tabla
        document.addEventListener('click', (e) => {
            if (e.target.closest('.btn-ver')) {
                this.verRecordatorio(e.target.closest('[data-recordatorio-id]').dataset.recordatorioId);
            } else if (e.target.closest('.btn-editar')) {
                this.editarRecordatorio(e.target.closest('[data-recordatorio-id]').dataset.recordatorioId);
            } else if (e.target.closest('.btn-enviar')) {
                this.enviarRecordatorio(e.target.closest('[data-recordatorio-id]').dataset.recordatorioId);
            } else if (e.target.closest('.btn-eliminar')) {
                this.eliminarRecordatorio(e.target.closest('[data-recordatorio-id]').dataset.recordatorioId);
            }
        });

        // Modal
        document.getElementById('btn-cancelar').addEventListener('click', () => {
            this.cerrarModal();
        });

        document.getElementById('btn-guardar').addEventListener('click', () => {
            this.guardarRecordatorio();
        });

        // Cambio en tipo de destinatarios
        document.getElementById('destinatarios').addEventListener('change', (e) => {
            this.toggleDestinatarios(e.target.value);
        });

        // Botón refresh
        document.getElementById('btn-refresh').addEventListener('click', () => {
            this.refrescarDatos();
        });

        // Cerrar modal con ESC
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && this.modal.classList.contains('is-visible')) {
                this.cerrarModal();
            }
        });
    }

    initializeDateInputs() {
        // Establecer fecha mínima como hoy
        const fechaInput = document.getElementById('fecha_programada');
        const hoy = new Date();
        const fechaMinima = hoy.toISOString().slice(0, 16);
        fechaInput.min = fechaMinima;

        // Establecer fecha por defecto como 1 hora en el futuro
        const fechaDefecto = new Date(hoy.getTime() + 60 * 60 * 1000);
        fechaInput.value = fechaDefecto.toISOString().slice(0, 16);
    }

    setupAutoRefresh() {
        // Refrescar datos cada 30 segundos
        setInterval(() => {
            this.refrescarDatos();
        }, 30000);
    }

    abrirModal(recordatorioId = null) {
        this.limpiarFormulario();
        
        if (recordatorioId) {
            this.cargarRecordatorio(recordatorioId);
            document.getElementById('modal-titulo').textContent = 'Editar Recordatorio';
        } else {
            document.getElementById('modal-titulo').textContent = 'Nuevo Recordatorio';
        }

        this.modal.classList.add('is-visible');
        this.modal.showModal();
    }

    cerrarModal() {
        this.modal.classList.remove('is-visible');
        this.modal.close();
    }

    limpiarFormulario() {
        this.form.reset();
        this.initializeDateInputs();
        this.toggleDestinatarios('todos');
    }

    toggleDestinatarios(tipo) {
        const sucursalContainer = document.getElementById('sucursal-container');
        const grupoContainer = document.getElementById('grupo-container');

        // Ocultar todos los contenedores
        sucursalContainer.style.display = 'none';
        grupoContainer.style.display = 'none';

        // Mostrar el contenedor correspondiente
        switch (tipo) {
            case 'sucursal':
                sucursalContainer.style.display = 'block';
                break;
            case 'grupo':
                grupoContainer.style.display = 'block';
                break;
        }
    }

    async cargarRecordatorio(id) {
        try {
            const response = await fetch(`api/recordatorios_api.php?action=obtener&id=${id}`);
            const data = await response.json();

            if (data.success) {
                const recordatorio = data.recordatorio;
                
                document.getElementById('recordatorio-id').value = recordatorio.id_recordatorio;
                document.getElementById('titulo').value = recordatorio.titulo;
                document.getElementById('descripcion').value = recordatorio.descripcion || '';
                document.getElementById('fecha_programada').value = recordatorio.fecha_programada.slice(0, 16);
                document.getElementById('prioridad').value = recordatorio.prioridad;
                document.getElementById('destinatarios').value = recordatorio.destinatarios;
                document.getElementById('tipo_envio').value = recordatorio.tipo_envio;
                document.getElementById('mensaje_whatsapp').value = recordatorio.mensaje_whatsapp || '';
                document.getElementById('mensaje_notificacion').value = recordatorio.mensaje_notificacion || '';

                this.toggleDestinatarios(recordatorio.destinatarios);
            } else {
                this.mostrarError('Error al cargar el recordatorio: ' + data.message);
            }
        } catch (error) {
            this.mostrarError('Error de conexión: ' + error.message);
        }
    }

    async guardarRecordatorio() {
        if (!this.validarFormulario()) {
            return;
        }

        const formData = new FormData(this.form);
        const data = Object.fromEntries(formData.entries());

        // Limpiar campos vacíos
        Object.keys(data).forEach(key => {
            if (data[key] === '') {
                delete data[key];
            }
        });

        try {
            const action = data.recordatorio_id ? 'actualizar' : 'crear';
            const response = await fetch(`api/recordatorios_api.php?action=${action}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(data)
            });

            const result = await response.json();

            if (result.success) {
                this.mostrarExito(result.message);
                this.cerrarModal();
                this.refrescarDatos();
            } else {
                this.mostrarError(result.message);
            }
        } catch (error) {
            this.mostrarError('Error de conexión: ' + error.message);
        }
    }

    validarFormulario() {
        const titulo = document.getElementById('titulo').value.trim();
        const fecha = document.getElementById('fecha_programada').value;
        const destinatarios = document.getElementById('destinatarios').value;

        if (!titulo) {
            this.mostrarError('El título es requerido');
            return false;
        }

        if (!fecha) {
            this.mostrarError('La fecha programada es requerida');
            return false;
        }

        // Validar fecha futura
        const fechaProgramada = new Date(fecha);
        const ahora = new Date();
        if (fechaProgramada <= ahora) {
            this.mostrarError('La fecha programada debe ser futura');
            return false;
        }

        // Validar destinatarios específicos
        if (destinatarios === 'sucursal' && !document.getElementById('sucursal_id').value) {
            this.mostrarError('Debe seleccionar una sucursal');
            return false;
        }

        if (destinatarios === 'grupo' && !document.getElementById('grupo_id').value) {
            this.mostrarError('Debe seleccionar un grupo');
            return false;
        }

        return true;
    }

    async verRecordatorio(id) {
        try {
            const response = await fetch(`api/recordatorios_api.php?action=obtener&id=${id}`);
            const data = await response.json();

            if (data.success) {
                const recordatorio = data.recordatorio;
                this.mostrarDetalles(recordatorio);
            } else {
                this.mostrarError('Error al cargar el recordatorio: ' + data.message);
            }
        } catch (error) {
            this.mostrarError('Error de conexión: ' + error.message);
        }
    }

    mostrarDetalles(recordatorio) {
        const contenido = `
            <div class="recordatorio-detalles">
                <h3>${recordatorio.titulo}</h3>
                <p><strong>Descripción:</strong> ${recordatorio.descripcion || 'Sin descripción'}</p>
                <p><strong>Fecha Programada:</strong> ${this.formatearFecha(recordatorio.fecha_programada)}</p>
                <p><strong>Prioridad:</strong> <span class="prioridad-badge prioridad-${recordatorio.prioridad}">${recordatorio.prioridad.toUpperCase()}</span></p>
                <p><strong>Estado:</strong> <span class="estado-badge estado-${recordatorio.estado}">${recordatorio.estado_descripcion}</span></p>
                <p><strong>Destinatarios:</strong> ${recordatorio.destinatarios.toUpperCase()}</p>
                <p><strong>Tipo de Envío:</strong> ${recordatorio.tipo_envio.toUpperCase()}</p>
                ${recordatorio.mensaje_whatsapp ? `<p><strong>Mensaje WhatsApp:</strong><br>${recordatorio.mensaje_whatsapp}</p>` : ''}
                ${recordatorio.mensaje_notificacion ? `<p><strong>Mensaje Notificación:</strong><br>${recordatorio.mensaje_notificacion}</p>` : ''}
                <p><strong>Creado por:</strong> ${recordatorio.creador_nombre}</p>
                <p><strong>Fecha de Creación:</strong> ${this.formatearFecha(recordatorio.fecha_creacion)}</p>
            </div>
        `;

        Swal.fire({
            title: 'Detalles del Recordatorio',
            html: contenido,
            width: '600px',
            showCloseButton: true,
            showConfirmButton: false
        });
    }

    async editarRecordatorio(id) {
        this.abrirModal(id);
    }

    async enviarRecordatorio(id) {
        const result = await Swal.fire({
            title: '¿Enviar Recordatorio?',
            text: '¿Está seguro de que desea enviar este recordatorio ahora?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sí, Enviar',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#4CAF50'
        });

        if (result.isConfirmed) {
            try {
                const response = await fetch(`api/recordatorios_api.php?action=enviar`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ id: id })
                });

                const data = await response.json();

                if (data.success) {
                    this.mostrarExito(data.message);
                    this.refrescarDatos();
                } else {
                    this.mostrarError(data.message);
                }
            } catch (error) {
                this.mostrarError('Error de conexión: ' + error.message);
            }
        }
    }

    async eliminarRecordatorio(id) {
        const result = await Swal.fire({
            title: '¿Eliminar Recordatorio?',
            text: '¿Está seguro de que desea eliminar este recordatorio? Esta acción no se puede deshacer.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, Eliminar',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#F44336'
        });

        if (result.isConfirmed) {
            try {
                const response = await fetch(`api/recordatorios_api.php?action=eliminar`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ id: id })
                });

                const data = await response.json();

                if (data.success) {
                    this.mostrarExito(data.message);
                    this.refrescarDatos();
                } else {
                    this.mostrarError(data.message);
                }
            } catch (error) {
                this.mostrarError('Error de conexión: ' + error.message);
            }
        }
    }

    async refrescarDatos() {
        const tabla = document.querySelector('.recordatorios-table tbody');
        if (tabla) {
            tabla.classList.add('loading');
        }

        try {
            // Recargar la página para obtener datos actualizados
            window.location.reload();
        } catch (error) {
            console.error('Error al refrescar datos:', error);
        }
    }

    formatearFecha(fecha) {
        const fechaObj = new Date(fecha);
        return fechaObj.toLocaleString('es-ES', {
            year: 'numeric',
            month: '2-digit',
            day: '2-digit',
            hour: '2-digit',
            minute: '2-digit'
        });
    }

    mostrarExito(mensaje) {
        Swal.fire({
            title: 'Éxito',
            text: mensaje,
            icon: 'success',
            timer: 3000,
            showConfirmButton: false
        });
    }

    mostrarError(mensaje) {
        Swal.fire({
            title: 'Error',
            text: mensaje,
            icon: 'error',
            confirmButtonText: 'Aceptar'
        });
    }

    mostrarInfo(mensaje) {
        Swal.fire({
            title: 'Información',
            text: mensaje,
            icon: 'info',
            confirmButtonText: 'Aceptar'
        });
    }
}

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', () => {
    new RecordatoriosSistema();
});

// Funciones globales para compatibilidad
function abrirModalRecordatorio() {
    if (window.recordatoriosSistema) {
        window.recordatoriosSistema.abrirModal();
    }
}

function cerrarModalRecordatorio() {
    if (window.recordatoriosSistema) {
        window.recordatoriosSistema.cerrarModal();
    }
}

// Exportar para uso global
window.RecordatoriosSistema = RecordatoriosSistema;
