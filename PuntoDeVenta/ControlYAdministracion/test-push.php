<?php
include "header.php";
include "navbar.php";
?>

<div class="container-fluid pt-4 px-4">
    <div class="row g-4">
        <div class="col-12">
            <div class="bg-light rounded h-100 p-4">
                <h5 class="mb-4">Prueba de Notificaciones Push</h5>
                
                <div class="alert alert-info" role="alert">
                    <strong>Información:</strong> Esta página permite probar el sistema de notificaciones push.
                    Primero debes dar permiso para recibir notificaciones y luego puedes enviar una prueba.
                </div>
                
                <div class="mb-4">
                    <h6>Paso 1: Permitir notificaciones</h6>
                    <button id="solicitar-permiso" class="btn btn-primary">
                        <i class="fa fa-bell me-2"></i>Solicitar permiso
                    </button>
                    <div id="status-permiso" class="mt-2"></div>
                </div>
                
                <div class="mb-4">
                    <h6>Paso 2: Suscribirse a notificaciones</h6>
                    <button id="suscribir-push" class="btn btn-success" disabled>
                        <i class="fa fa-plus-circle me-2"></i>Suscribirse
                    </button>
                    <div id="status-suscripcion" class="mt-2"></div>
                </div>
                
                <div class="mb-4">
                    <h6>Paso 3: Enviar notificación de prueba</h6>
                    <form id="form-notificacion">
                        <div class="mb-3">
                            <label for="mensaje" class="form-label">Mensaje</label>
                            <input type="text" class="form-control" id="mensaje" name="mensaje" value="Esta es una notificación de prueba">
                        </div>
                        
                        <div class="mb-3">
                            <label for="tipo" class="form-label">Tipo</label>
                            <select class="form-select" id="tipo" name="tipo">
                                <option value="sistema">Sistema</option>
                                <option value="inventario">Inventario</option>
                                <option value="caducidad">Caducidad</option>
                                <option value="caja">Caja</option>
                                <option value="venta">Venta</option>
                            </select>
                        </div>
                        
                        <button type="submit" class="btn btn-warning" id="enviar-notificacion" disabled>
                            <i class="fa fa-paper-plane me-2"></i>Enviar notificación
                        </button>
                    </form>
                    <div id="resultado-envio" class="mt-2"></div>
                </div>
            </div>
        </div>
        
        <div class="col-12">
            <div class="bg-light rounded h-100 p-4">
                <h5 class="mb-4">Estado del sistema de notificaciones</h5>
                
                <div id="info-sistema" class="mb-3">Cargando información del sistema...</div>
                
                <div id="info-dispositivo" class="mb-3"></div>
                
                <div class="alert alert-warning" role="alert">
                    <strong>Nota:</strong> Safari en iOS y algunos navegadores antiguos no soportan las notificaciones push.
                    En estos casos, considera usar una aplicación nativa o PWA.
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const solicitarPermisoBtn = document.getElementById('solicitar-permiso');
    const suscribirPushBtn = document.getElementById('suscribir-push');
    const enviarNotificacionBtn = document.getElementById('enviar-notificacion');
    const formNotificacion = document.getElementById('form-notificacion');
    
    const statusPermiso = document.getElementById('status-permiso');
    const statusSuscripcion = document.getElementById('status-suscripcion');
    const resultadoEnvio = document.getElementById('resultado-envio');
    const infoSistema = document.getElementById('info-sistema');
    const infoDispositivo = document.getElementById('info-dispositivo');
    
    // Verificar soporte
    if ('serviceWorker' in navigator && 'PushManager' in window) {
        infoSistema.innerHTML = '<span class="text-success">✓ Tu navegador soporta notificaciones push</span>';
    } else {
        infoSistema.innerHTML = '<span class="text-danger">✗ Tu navegador NO soporta notificaciones push</span>';
        solicitarPermisoBtn.disabled = true;
    }
    
    // Mostrar información del dispositivo
    const userAgent = navigator.userAgent;
    let dispositivo = 'Desconocido';
    
    if (/Android/i.test(userAgent)) {
        dispositivo = 'Android';
    } else if (/iPhone|iPad|iPod/i.test(userAgent)) {
        dispositivo = 'iOS';
    } else if (/Windows/i.test(userAgent)) {
        dispositivo = 'Windows';
    } else if (/Macintosh/i.test(userAgent)) {
        dispositivo = 'Mac';
    } else if (/Linux/i.test(userAgent)) {
        dispositivo = 'Linux';
    }
    
    let navegador = 'Desconocido';
    if (/Chrome/i.test(userAgent)) {
        navegador = 'Chrome';
    } else if (/Firefox/i.test(userAgent)) {
        navegador = 'Firefox';
    } else if (/Safari/i.test(userAgent) && !/Chrome/i.test(userAgent)) {
        navegador = 'Safari';
    } else if (/Edge/i.test(userAgent)) {
        navegador = 'Edge';
    } else if (/MSIE|Trident/i.test(userAgent)) {
        navegador = 'Internet Explorer';
    }
    
    infoDispositivo.innerHTML = `
        <strong>Dispositivo:</strong> ${dispositivo}<br>
        <strong>Navegador:</strong> ${navegador}<br>
        <strong>User Agent:</strong> ${userAgent}
    `;
    
    // Verificar permiso actual
    if (Notification.permission === 'granted') {
        statusPermiso.innerHTML = '<span class="text-success">✓ Permiso concedido</span>';
        solicitarPermisoBtn.disabled = true;
        suscribirPushBtn.disabled = false;
    } else if (Notification.permission === 'denied') {
        statusPermiso.innerHTML = '<span class="text-danger">✗ Permiso denegado. Debes cambiar la configuración de tu navegador.</span>';
        solicitarPermisoBtn.disabled = true;
    }
    
    // Solicitar permiso
    solicitarPermisoBtn.addEventListener('click', async function() {
        try {
            const permission = await Notification.requestPermission();
            
            if (permission === 'granted') {
                statusPermiso.innerHTML = '<span class="text-success">✓ Permiso concedido</span>';
                solicitarPermisoBtn.disabled = true;
                suscribirPushBtn.disabled = false;
            } else {
                statusPermiso.innerHTML = '<span class="text-danger">✗ Permiso denegado</span>';
            }
        } catch (error) {
            statusPermiso.innerHTML = `<span class="text-danger">✗ Error: ${error.message}</span>`;
        }
    });
    
    // Suscribirse a notificaciones
    suscribirPushBtn.addEventListener('click', async function() {
        statusSuscripcion.innerHTML = '<span class="text-info">Suscribiendo...</span>';
        
        try {
            const result = await window.inicializarNotificacionesPush();
            
            if (result) {
                statusSuscripcion.innerHTML = '<span class="text-success">✓ Suscripción exitosa</span>';
                suscribirPushBtn.disabled = true;
                enviarNotificacionBtn.disabled = false;
            } else {
                statusSuscripcion.innerHTML = '<span class="text-danger">✗ Error en la suscripción</span>';
            }
        } catch (error) {
            statusSuscripcion.innerHTML = `<span class="text-danger">✗ Error: ${error.message}</span>`;
        }
    });
    
    // Enviar notificación
    formNotificacion.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const mensaje = document.getElementById('mensaje').value;
        const tipo = document.getElementById('tipo').value;
        
        resultadoEnvio.innerHTML = '<span class="text-info">Enviando...</span>';
        
        try {
            const response = await fetch('./api/enviar_notificacion_push.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    mensaje: mensaje,
                    tipo: tipo,
                    url: window.location.href
                })
            });
            
            const data = await response.json();
            
            if (data.success) {
                resultadoEnvio.innerHTML = `<span class="text-success">✓ Notificación enviada (${data.estadisticas.enviadas} de ${data.estadisticas.total})</span>`;
            } else {
                resultadoEnvio.innerHTML = `<span class="text-danger">✗ Error: ${data.message}</span>`;
            }
        } catch (error) {
            resultadoEnvio.innerHTML = `<span class="text-danger">✗ Error: ${error.message}</span>`;
        }
    });
});
</script>

<?php include "Footer.php"; ?> 