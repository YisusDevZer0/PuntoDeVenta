<?php
// Habilitar visualización de errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Notificaciones Push</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .status-container {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .log-container {
            max-height: 300px;
            overflow-y: auto;
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-top: 20px;
        }
        .log-entry {
            margin-bottom: 5px;
            padding: 5px;
            border-radius: 3px;
        }
        .log-info { color: #0d6efd; }
        .log-success { color: #198754; }
        .log-warning { color: #ffc107; }
        .log-error { color: #dc3545; }
        .action-buttons {
            display: flex;
            gap: 10px;
            margin: 20px 0;
        }
        .subscription-status {
            font-size: 1.2em;
            margin: 20px 0;
            padding: 15px;
            border-radius: 5px;
            background-color: #e9ecef;
        }
    </style>
</head>
<body>
    <div class="container py-5">
        <h1 class="mb-4">Test Notificaciones Push</h1>
        
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">Estado del Sistema</h5>
                <div id="status" class="status-container">
                    <div class="subscription-status text-center">
                        <i class="fas fa-spinner fa-spin"></i> Verificando estado...
                    </div>
                </div>
                
                <div class="action-buttons justify-content-center">
                    <button id="btn-subscribe" class="btn btn-primary btn-lg">
                        <i class="fas fa-bell"></i> Suscribirse a Notificaciones
                    </button>
                    <button id="btn-reset" class="btn btn-warning btn-lg">
                        <i class="fas fa-sync"></i> Resetear Suscripción
                    </button>
                    <button id="btn-test" class="btn btn-success btn-lg">
                        <i class="fas fa-paper-plane"></i> Enviar Notificación de Prueba
                    </button>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Registro de Eventos</h5>
                <div id="log" class="log-container"></div>
            </div>
        </div>
    </div>

    <script>
        let swRegistration = null;

        // Función para registrar mensajes en el log
        function log(message, type = 'info') {
            const logContainer = document.getElementById('log');
            const entry = document.createElement('div');
            entry.className = `log-entry log-${type}`;
            entry.innerHTML = `<i class="fas fa-${getIcon(type)}"></i> ${message}`;
            logContainer.appendChild(entry);
            logContainer.scrollTop = logContainer.scrollHeight;
        }

        // Función para obtener el icono según el tipo de mensaje
        function getIcon(type) {
            switch(type) {
                case 'success': return 'check-circle';
                case 'warning': return 'exclamation-triangle';
                case 'error': return 'times-circle';
                default: return 'info-circle';
            }
        }

        // Función para actualizar el estado
        async function updateStatus() {
            const statusContainer = document.getElementById('status');
            let html = '<ul class="list-unstyled">';
            
            // Verificar soporte de Service Worker
            if ('serviceWorker' in navigator) {
                html += '<li class="text-success">✓ Service Worker soportado</li>';
            } else {
                html += '<li class="text-danger">✗ Service Worker no soportado</li>';
            }

            // Verificar Service Worker
            try {
                const registration = await navigator.serviceWorker.getRegistration();
                if (registration) {
                    swRegistration = registration;
                    html += `
                        <li class="text-success">✓ Service Worker registrado</li>
                        <li class="text-info">Scope: ${registration.scope}</li>
                        <li class="text-info">Estado: ${registration.active ? 'Activo' : 'Inactivo'}</li>
                    `;
                    
                    // Verificar suscripción
                    const subscription = await registration.pushManager.getSubscription();
                    if (subscription) {
                        const endpoint = subscription.endpoint;
                        const isFirebase = endpoint.includes('fcm.googleapis.com');
                        html += `
                            <li class="${isFirebase ? 'text-warning' : 'text-success'}">
                                ${isFirebase ? '⚠️' : '✓'} Suscripción activa (${isFirebase ? 'Firebase' : 'Web Push'})
                            </li>
                            <li class="text-info">Endpoint: ${endpoint.substring(0, 50)}...</li>
                            <li class="text-info">Claves: ${subscription.keys ? 'Presentes' : 'No disponibles'}</li>
                        `;
                        
                        // Mostrar detalles de las claves si están disponibles
                        if (subscription.keys) {
                            html += `
                                <li class="text-info">p256dh: ${subscription.keys.p256dh.substring(0, 20)}...</li>
                                <li class="text-info">auth: ${subscription.keys.auth.substring(0, 20)}...</li>
                            `;
                        }
                    } else {
                        html += '<li class="text-warning">⚠️ No hay suscripción activa</li>';
                    }
                } else {
                    html += '<li class="text-danger">✗ Service Worker no registrado</li>';
                }
            } catch (error) {
                html += `<li class="text-danger">✗ Error: ${error.message}</li>`;
                console.error('Error detallado:', error);
            }

            // Verificar permisos
            if ('Notification' in window) {
                const permission = Notification.permission;
                html += `
                    <li class="${permission === 'granted' ? 'text-success' : 'text-warning'}">
                        ${permission === 'granted' ? '✓' : '⚠️'} Permisos: ${permission}
                    </li>
                `;
            } else {
                html += '<li class="text-danger">✗ Notificaciones no soportadas</li>';
            }

            // Verificar soporte de Push API
            if ('PushManager' in window) {
                html += '<li class="text-success">✓ Push API soportada</li>';
            } else {
                html += '<li class="text-danger">✗ Push API no soportada</li>';
            }

            html += '</ul>';
            statusContainer.innerHTML = html;

            // Actualizar estado de los botones
            const btnSubscribe = document.getElementById('btn-subscribe');
            const btnReset = document.getElementById('btn-reset');
            const btnTest = document.getElementById('btn-test');

            if (Notification.permission === 'granted') {
                btnSubscribe.style.display = 'none';
                btnReset.style.display = 'inline-block';
                btnTest.style.display = 'inline-block';
            } else {
                btnSubscribe.style.display = 'inline-block';
                btnReset.style.display = 'none';
                btnTest.style.display = 'none';
            }
        }

        // Función para resetear la suscripción
        async function resetSubscription() {
            try {
                log('Iniciando reset de suscripción...', 'info');
                
                // Cancelar suscripción actual
                const registration = await navigator.serviceWorker.getRegistration();
                if (registration) {
                    const subscription = await registration.pushManager.getSubscription();
                    if (subscription) {
                        await subscription.unsubscribe();
                        log('Suscripción anterior cancelada', 'success');
                    }
                }

                // Eliminar suscripción de la base de datos
                const response = await fetch('api/eliminar_suscripcion.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        user_id: '1' // ID de usuario demo
                    })
                });

                const result = await response.json();
                if (result.success) {
                    log('Suscripción eliminada de la base de datos', 'success');
                } else {
                    log(`Error al eliminar suscripción: ${result.message}`, 'error');
                }

                // Actualizar estado
                await updateStatus();
                log('Reset completado', 'success');
            } catch (error) {
                log(`Error en reset: ${error.message}`, 'error');
            }
        }

        // Función para crear nueva suscripción
        async function createNewSubscription() {
            try {
                log('Iniciando nueva suscripción...', 'info');

                // Verificar Service Worker
                if (!swRegistration) {
                    log('Registrando Service Worker...', 'info');
                    swRegistration = await navigator.serviceWorker.register('service-worker.js');
                    log('Service Worker registrado', 'success');
                }

                // Verificar permisos
                if (Notification.permission !== 'granted') {
                    log('Solicitando permisos...', 'info');
                    const permission = await Notification.requestPermission();
                    if (permission !== 'granted') {
                        throw new Error('Permiso denegado para notificaciones');
                    }
                    log('Permisos concedidos', 'success');
                }

                // Obtener clave pública
                log('Obteniendo clave pública VAPID...', 'info');
                const response = await fetch('api/get_vapid_public_key.php');
                const data = await response.json();
                
                if (!data.success || !data.publicKey) {
                    throw new Error('No se pudo obtener la clave pública');
                }

                // Convertir clave pública
                const applicationServerKey = urlBase64ToUint8Array(data.publicKey);
                
                // Crear suscripción
                log('Creando nueva suscripción...', 'info');
                const subscription = await swRegistration.pushManager.subscribe({
                    userVisibleOnly: true,
                    applicationServerKey: applicationServerKey
                });

                // Guardar suscripción
                const saveResponse = await fetch('api/guardar_suscripcion.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        subscription: subscription,
                        user_id: '1' // ID de usuario demo
                    })
                });

                const saveResult = await saveResponse.json();
                if (saveResult.success) {
                    log('Nueva suscripción guardada', 'success');
                } else {
                    throw new Error(`Error al guardar: ${saveResult.message}`);
                }

                // Actualizar estado
                await updateStatus();
                log('Suscripción completada', 'success');
            } catch (error) {
                log(`Error en suscripción: ${error.message}`, 'error');
            }
        }

        // Función para enviar notificación de prueba
        async function sendTestNotification() {
            try {
                log('Enviando notificación de prueba...', 'info');
                
                const response = await fetch('api/enviar_notificacion.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        titulo: 'Notificación de Prueba',
                        mensaje: 'Esta es una notificación de prueba enviada el ' + new Date().toLocaleString(),
                        tipo: 'sistema'
                    })
                });

                const result = await response.json();
                if (result.success) {
                    log(`Notificación enviada: ${result.message}`, 'success');
                } else {
                    throw new Error(result.message);
                }
            } catch (error) {
                log(`Error al enviar notificación: ${error.message}`, 'error');
            }
        }

        // Función auxiliar para convertir clave base64 a Uint8Array
        function urlBase64ToUint8Array(base64String) {
            const padding = '='.repeat((4 - base64String.length % 4) % 4);
            const base64 = (base64String + padding)
                .replace(/\-/g, '+')
                .replace(/_/g, '/');

            const rawData = window.atob(base64);
            const outputArray = new Uint8Array(rawData.length);

            for (let i = 0; i < rawData.length; ++i) {
                outputArray[i] = rawData.charCodeAt(i);
            }
            return outputArray;
        }

        // Inicializar
        document.addEventListener('DOMContentLoaded', async () => {
            // Registrar eventos de botones
            document.getElementById('btn-subscribe').addEventListener('click', createNewSubscription);
            document.getElementById('btn-reset').addEventListener('click', resetSubscription);
            document.getElementById('btn-test').addEventListener('click', sendTestNotification);

            // Actualizar estado inicial
            await updateStatus();
        });
    </script>
</body>
</html> 