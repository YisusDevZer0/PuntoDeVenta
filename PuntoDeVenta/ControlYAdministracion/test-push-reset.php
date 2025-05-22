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
    <title>Resetear Suscripción Push</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .log-container {
            max-height: 300px;
            overflow-y: auto;
            background: #f8f9fa;
            padding: 10px;
            border-radius: 5px;
            margin-top: 20px;
        }
        .log-entry {
            margin: 5px 0;
            padding: 5px;
            border-bottom: 1px solid #dee2e6;
        }
        .log-success { color: #198754; }
        .log-error { color: #dc3545; }
        .log-info { color: #0dcaf0; }
        .log-warning { color: #ffc107; }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h1>Resetear Suscripción Push</h1>
        <p class="lead">Esta página te ayudará a resetear tu suscripción actual y crear una nueva usando Web Push nativo.</p>

        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Estado Actual</h5>
                        <div id="status-container">
                            <p><i class="fas fa-spinner fa-spin"></i> Verificando estado...</p>
                        </div>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-body">
                        <h5 class="card-title">Acciones</h5>
                        <button id="btn-check" class="btn btn-info mb-2">Verificar Estado</button>
                        <button id="btn-reset" class="btn btn-warning mb-2">Resetear Suscripción</button>
                        <button id="btn-subscribe" class="btn btn-primary mb-2">Crear Nueva Suscripción</button>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Registro de Eventos</h5>
                        <div id="log-container" class="log-container"></div>
                        <button id="btn-clear-log" class="btn btn-secondary mt-2">Limpiar Registro</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let swRegistration = null;

        // Función para registrar en el log
        function log(message, type = 'info') {
            const logContainer = document.getElementById('log-container');
            const entry = document.createElement('div');
            entry.className = `log-entry log-${type}`;
            entry.textContent = `[${new Date().toLocaleTimeString()}] ${message}`;
            logContainer.appendChild(entry);
            logContainer.scrollTop = logContainer.scrollHeight;
        }

        // Función para actualizar el estado
        async function updateStatus() {
            const statusContainer = document.getElementById('status-container');
            let html = '<ul class="list-unstyled">';

            // Verificar Service Worker
            try {
                const registration = await navigator.serviceWorker.getRegistration();
                if (registration) {
                    swRegistration = registration;
                    html += '<li class="text-success">✓ Service Worker registrado</li>';
                    
                    // Verificar suscripción
                    const subscription = await registration.pushManager.getSubscription();
                    if (subscription) {
                        const endpoint = subscription.endpoint;
                        const isFirebase = endpoint.includes('fcm.googleapis.com');
                        html += `<li class="${isFirebase ? 'text-warning' : 'text-success'}">
                            ${isFirebase ? '⚠️' : '✓'} Suscripción activa (${isFirebase ? 'Firebase' : 'Web Push'})
                        </li>`;
                    } else {
                        html += '<li class="text-warning">⚠️ No hay suscripción activa</li>';
                    }
                } else {
                    html += '<li class="text-danger">✗ Service Worker no registrado</li>';
                }
            } catch (error) {
                html += `<li class="text-danger">✗ Error: ${error.message}</li>`;
            }

            // Verificar permisos
            if ('Notification' in window) {
                const permission = Notification.permission;
                html += `<li class="${permission === 'granted' ? 'text-success' : 'text-warning'}">
                    ${permission === 'granted' ? '✓' : '⚠️'} Permisos: ${permission}
                </li>`;
            } else {
                html += '<li class="text-danger">✗ Notificaciones no soportadas</li>';
            }

            html += '</ul>';
            statusContainer.innerHTML = html;
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
        document.addEventListener('DOMContentLoaded', () => {
            // Botones
            document.getElementById('btn-check').addEventListener('click', updateStatus);
            document.getElementById('btn-reset').addEventListener('click', resetSubscription);
            document.getElementById('btn-subscribe').addEventListener('click', createNewSubscription);
            document.getElementById('btn-clear-log').addEventListener('click', () => {
                document.getElementById('log-container').innerHTML = '';
            });

            // Estado inicial
            updateStatus();
        });
    </script>
</body>
</html> 