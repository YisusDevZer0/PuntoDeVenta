<?php
// Habilitar visualización de errores para debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// No hacer caché
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prueba de Notificaciones Push</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
    <style>
        body { padding: 20px; }
        .container { max-width: 800px; }
        pre { background: #f4f4f4; padding: 10px; border-radius: 5px; overflow: auto; }
        .status-box { padding: 15px; margin: 15px 0; border-radius: 5px; }
        .status-success { background-color: #d4edda; color: #155724; }
        .status-error { background-color: #f8d7da; color: #721c24; }
        .status-warning { background-color: #fff3cd; color: #856404; }
        .status-info { background-color: #d1ecf1; color: #0c5460; }
        .log-container { max-height: 300px; overflow-y: auto; }
        .notification-badge {
            position: relative;
            display: inline-block;
            width: 40px;
            height: 40px;
            background-color: #f8f9fa;
            border-radius: 50%;
            margin-right: 10px;
            text-align: center;
            line-height: 40px;
        }
        .notification-counter {
            position: absolute;
            top: -5px;
            right: -5px;
            background-color: #dc3545;
            color: white;
            border-radius: 50%;
            padding: 0 6px;
            font-size: 12px;
            min-width: 20px;
            height: 20px;
            line-height: 20px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="mb-4">
            <i class="fas fa-bell mr-2"></i>
            Prueba de Notificaciones Push
        </h1>
        
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Estado del Sistema</h5>
                    </div>
                    <div class="card-body" id="status-container">
                        <p>Cargando estado del sistema...</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">Acciones</h5>
                    </div>
                    <div class="card-body">
                        <button id="btn-check-support" class="btn btn-info mb-2 w-100">
                            <i class="fas fa-search mr-2"></i>Verificar Soporte
                        </button>
                        <button id="btn-register-sw" class="btn btn-primary mb-2 w-100">
                            <i class="fas fa-cog mr-2"></i>Registrar Service Worker
                        </button>
                        <button id="btn-request-permission" class="btn btn-warning mb-2 w-100">
                            <i class="fas fa-unlock-alt mr-2"></i>Solicitar Permiso
                        </button>
                        <button id="btn-subscribe" class="btn btn-success mb-2 w-100">
                            <i class="fas fa-bell mr-2"></i>Suscribir
                        </button>
                        <button id="btn-get-subscription" class="btn btn-dark mb-2 w-100">
                            <i class="fas fa-info-circle mr-2"></i>Ver Suscripción
                        </button>
                        <button id="btn-test-notification" class="btn btn-danger mb-2 w-100">
                            <i class="fas fa-paper-plane mr-2"></i>Enviar Notificación
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card mb-4">
            <div class="card-header bg-secondary text-white">
                <h5 class="mb-0">Registro de Actividad</h5>
            </div>
            <div class="card-body p-0">
                <div id="log-container" class="log-container">
                    <pre id="log-output">// Registro de actividad
// ------------------
</pre>
                </div>
            </div>
            <div class="card-footer">
                <button id="btn-clear-log" class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-trash mr-1"></i>Limpiar Registro
                </button>
            </div>
        </div>
        
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">Enviar Notificación Personalizada</h5>
                    </div>
                    <div class="card-body">
                        <form id="form-send-notification">
                            <div class="form-group">
                                <label for="notification-title">Título:</label>
                                <input type="text" id="notification-title" class="form-control" value="Prueba de Notificación">
                            </div>
                            <div class="form-group">
                                <label for="notification-body">Mensaje:</label>
                                <textarea id="notification-body" class="form-control" rows="2">Esta es una notificación de prueba desde la aplicación.</textarea>
                            </div>
                            <div class="form-group">
                                <label for="notification-type">Tipo:</label>
                                <select id="notification-type" class="form-control">
                                    <option value="sistema">Sistema</option>
                                    <option value="inventario">Inventario</option>
                                    <option value="caducidad">Caducidad</option>
                                    <option value="caja">Caja</option>
                                    <option value="venta">Venta</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-paper-plane mr-2"></i>Enviar
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-dark text-white">
                        <h5 class="mb-0">Diagnóstico VAPID</h5>
                    </div>
                    <div class="card-body">
                        <div id="vapid-status" class="mb-3 status-info status-box">
                            Verificando estado de claves VAPID...
                        </div>
                        <button id="btn-check-vapid" class="btn btn-info mb-2 w-100">
                            <i class="fas fa-key mr-2"></i>Verificar Claves VAPID
                        </button>
                        <button id="btn-generate-vapid" class="btn btn-warning mb-2 w-100">
                            <i class="fas fa-sync mr-2"></i>Regenerar Claves VAPID
                        </button>
                        <div class="mt-3">
                            <a href="verificar-vapid.php" target="_blank" class="btn btn-outline-secondary w-100">
                                <i class="fas fa-external-link-alt mr-2"></i>Herramienta de Diagnóstico
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="text-center text-muted small mt-4">
            <p>Esta herramienta es solo para fines de prueba y diagnóstico.</p>
        </div>
    </div>
    
    <!-- Script para evitar que sea cargado como módulo -->
    <script>
        // Función de registro
        const log = (message, type = 'info') => {
            const logOutput = document.getElementById('log-output');
            const timestamp = new Date().toLocaleTimeString();
            let prefix = '';
            
            switch(type) {
                case 'error':
                    prefix = '❌ ERROR: ';
                    break;
                case 'success':
                    prefix = '✅ ÉXITO: ';
                    break;
                case 'warning':
                    prefix = '⚠️ AVISO: ';
                    break;
                default:
                    prefix = 'ℹ️ INFO: ';
            }
            
            logOutput.innerHTML += `[${timestamp}] ${prefix}${message}\n`;
            logOutput.scrollTop = logOutput.scrollHeight;
        };
        
        // Función para actualizar el estado del sistema
        const updateStatus = () => {
            const statusContainer = document.getElementById('status-container');
            
            // Verificar soporte básico
            const hasServiceWorker = 'serviceWorker' in navigator;
            const hasPushManager = 'PushManager' in window;
            const hasNotification = 'Notification' in window;
            
            let html = '';
            
            // Soporte de API
            html += `<div class="mb-3">
                <h6>Soporte de API:</h6>
                <ul class="list-unstyled">
                    <li>
                        <i class="fas ${hasServiceWorker ? 'fa-check-circle text-success' : 'fa-times-circle text-danger'}"></i>
                        Service Worker API
                    </li>
                    <li>
                        <i class="fas ${hasPushManager ? 'fa-check-circle text-success' : 'fa-times-circle text-danger'}"></i>
                        Push API
                    </li>
                    <li>
                        <i class="fas ${hasNotification ? 'fa-check-circle text-success' : 'fa-times-circle text-danger'}"></i>
                        Notifications API
                    </li>
                </ul>
            </div>`;
            
            // Estado actual
            const permissionStatus = Notification.permission;
            let permissionClass = 'text-warning';
            let permissionIcon = 'fa-question-circle';
            
            if (permissionStatus === 'granted') {
                permissionClass = 'text-success';
                permissionIcon = 'fa-check-circle';
            } else if (permissionStatus === 'denied') {
                permissionClass = 'text-danger';
                permissionIcon = 'fa-times-circle';
            }
            
            html += `<div>
                <h6>Estado actual:</h6>
                <ul class="list-unstyled">
                    <li>
                        <i class="fas ${permissionIcon} ${permissionClass}"></i>
                        Permiso de notificaciones: <strong>${permissionStatus}</strong>
                    </li>
                    <li id="sw-status">
                        <i class="fas fa-spinner fa-spin"></i>
                        Service Worker: verificando...
                    </li>
                    <li id="subscription-status">
                        <i class="fas fa-spinner fa-spin"></i>
                        Suscripción: verificando...
                    </li>
                </ul>
            </div>`;
            
            statusContainer.innerHTML = html;
            
            // Verificar Service Worker
            if (hasServiceWorker) {
                navigator.serviceWorker.getRegistration().then(registration => {
                    const swStatus = document.getElementById('sw-status');
                    if (registration) {
                        swStatus.innerHTML = `
                            <i class="fas fa-check-circle text-success"></i>
                            Service Worker: registrado
                        `;
                        
                        // Verificar suscripción
                        registration.pushManager.getSubscription().then(subscription => {
                            const subStatus = document.getElementById('subscription-status');
                            if (subscription) {
                                subStatus.innerHTML = `
                                    <i class="fas fa-check-circle text-success"></i>
                                    Suscripción: activa
                                `;
                            } else {
                                subStatus.innerHTML = `
                                    <i class="fas fa-times-circle text-warning"></i>
                                    Suscripción: no suscrito
                                `;
                            }
                        });
                    } else {
                        swStatus.innerHTML = `
                            <i class="fas fa-times-circle text-warning"></i>
                            Service Worker: no registrado
                        `;
                        
                        const subStatus = document.getElementById('subscription-status');
                        subStatus.innerHTML = `
                            <i class="fas fa-times-circle text-warning"></i>
                            Suscripción: no disponible sin SW
                        `;
                    }
                });
            }
        };
        
        // Verificar estado de claves VAPID
        const checkVapidKeys = async () => {
            const vapidStatus = document.getElementById('vapid-status');
            
            try {
                const response = await fetch('api/verificar_vapid_keys.php');
                const data = await response.json();
                
                if (data.success) {
                    vapidStatus.className = 'mb-3 status-success status-box';
                    vapidStatus.innerHTML = `
                        <h6><i class="fas fa-check-circle mr-2"></i>Claves VAPID verificadas</h6>
                        <div>${data.diagnostics.join('<br>')}</div>
                        ${data.actions_taken.length > 0 ? 
                            `<div class="mt-2"><strong>Acciones realizadas:</strong><br>${data.actions_taken.join('<br>')}</div>` : 
                            ''}
                    `;
                    log('Claves VAPID verificadas correctamente', 'success');
                } else {
                    vapidStatus.className = 'mb-3 status-error status-box';
                    vapidStatus.innerHTML = `
                        <h6><i class="fas fa-exclamation-triangle mr-2"></i>Problema con claves VAPID</h6>
                        <div>${data.diagnostics.join('<br>')}</div>
                        ${data.actions_taken.length > 0 ? 
                            `<div class="mt-2"><strong>Acciones realizadas:</strong><br>${data.actions_taken.join('<br>')}</div>` : 
                            ''}
                    `;
                    log('Problema con las claves VAPID: ' + data.diagnostics.join(', '), 'error');
                }
            } catch (error) {
                vapidStatus.className = 'mb-3 status-error status-box';
                vapidStatus.innerHTML = `
                    <h6><i class="fas fa-exclamation-circle mr-2"></i>Error al verificar claves VAPID</h6>
                    <div>${error.message}</div>
                `;
                log('Error al verificar claves VAPID: ' + error.message, 'error');
            }
        };
        
        // Detectar navegadores problemáticos
        const detectBrowserIssues = () => {
            const ua = navigator.userAgent;
            
            if (/iPad|iPhone|iPod/.test(ua) && !/Chrome/.test(ua)) {
                log('Estás usando Safari en iOS, que no soporta notificaciones push web', 'warning');
                return true;
            }
            
            if (/Macintosh/.test(ua) && /Safari/.test(ua) && !/Chrome/.test(ua)) {
                log('Estás usando Safari en macOS, que tiene soporte limitado para push', 'warning');
                return true;
            }
            
            return false;
        };
        
        // Inicializar
        document.addEventListener('DOMContentLoaded', () => {
            // Actualizar estado
            updateStatus();
            
            // Verificar claves VAPID
            checkVapidKeys();
            
            // Detectar problemas de navegador
            detectBrowserIssues();
            
            // Manejadores de eventos para botones
            document.getElementById('btn-check-support').addEventListener('click', () => {
                log('Verificando soporte de API...');
                
                const hasServiceWorker = 'serviceWorker' in navigator;
                const hasPushManager = 'PushManager' in window;
                const hasNotification = 'Notification' in window;
                
                log(`Service Worker API: ${hasServiceWorker ? 'Soportada ✓' : 'No soportada ✗'}`, 
                    hasServiceWorker ? 'success' : 'error');
                    
                log(`Push API: ${hasPushManager ? 'Soportada ✓' : 'No soportada ✗'}`, 
                    hasPushManager ? 'success' : 'error');
                    
                log(`Notifications API: ${hasNotification ? 'Soportada ✓' : 'No soportada ✗'}`, 
                    hasNotification ? 'success' : 'error');
                
                updateStatus();
            });
            
            document.getElementById('btn-register-sw').addEventListener('click', async () => {
                try {
                    log('Registrando Service Worker...');
                    const registration = await navigator.serviceWorker.register('service-worker.js');
                    log('Service Worker registrado correctamente', 'success');
                    updateStatus();
                } catch (error) {
                    log('Error al registrar Service Worker: ' + error.message, 'error');
                }
            });
            
            document.getElementById('btn-request-permission').addEventListener('click', async () => {
                try {
                    log('Solicitando permiso para notificaciones...');
                    const permission = await Notification.requestPermission();
                    log(`Permiso de notificaciones: ${permission}`, 
                        permission === 'granted' ? 'success' : 'warning');
                    updateStatus();
                } catch (error) {
                    log('Error al solicitar permiso: ' + error.message, 'error');
                }
            });
            
            document.getElementById('btn-subscribe').addEventListener('click', async () => {
                try {
                    log('Iniciando proceso de suscripción...');
                    
                    // Verificar Service Worker
                    const registration = await navigator.serviceWorker.getRegistration();
                    if (!registration) {
                        throw new Error('No hay Service Worker registrado');
                    }
                    
                    // Obtener clave pública
                    log('Obteniendo clave pública VAPID...');
                    const response = await fetch('api/get_vapid_public_key.php');
                    const data = await response.json();
                    
                    if (!data.success || !data.publicKey) {
                        throw new Error('No se pudo obtener la clave pública: ' + 
                            (data.message || 'Error desconocido'));
                    }
                    
                    log('Clave pública VAPID obtenida', 'success');
                    
                    // Convertir clave pública
                    function urlBase64ToUint8Array(base64String) {
                        const padding = '='.repeat((4 - base64String.length % 4) % 4);
                        const base64 = (base64String + padding)
                            .replace(/-/g, '+')
                            .replace(/_/g, '/');
                        
                        const rawData = window.atob(base64);
                        const outputArray = new Uint8Array(rawData.length);
                        
                        for (let i = 0; i < rawData.length; ++i) {
                            outputArray[i] = rawData.charCodeAt(i);
                        }
                        return outputArray;
                    }
                    
                    // Suscribir
                    log('Suscribiendo al usuario...');
                    const subscription = await registration.pushManager.subscribe({
                        userVisibleOnly: true,
                        applicationServerKey: urlBase64ToUint8Array(data.publicKey)
                    });
                    
                    // Guardar en servidor
                    log('Enviando datos de suscripción al servidor...');
                    const saveResponse = await fetch('api/guardar_suscripcion.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            subscription: subscription
                        })
                    });
                    
                    const saveData = await saveResponse.json();
                    
                    if (saveData.success) {
                        log('Suscripción guardada en el servidor', 'success');
                    } else {
                        log('Error al guardar suscripción: ' + saveData.message, 'warning');
                    }
                    
                    updateStatus();
                } catch (error) {
                    log('Error en el proceso de suscripción: ' + error.message, 'error');
                }
            });
            
            document.getElementById('btn-get-subscription').addEventListener('click', async () => {
                try {
                    log('Verificando suscripción actual...');
                    
                    const registration = await navigator.serviceWorker.getRegistration();
                    if (!registration) {
                        throw new Error('No hay Service Worker registrado');
                    }
                    
                    const subscription = await registration.pushManager.getSubscription();
                    
                    if (subscription) {
                        const subscriptionJSON = JSON.stringify(subscription, null, 2);
                        log('Suscripción actual encontrada:', 'success');
                        log(`Endpoint: ${subscription.endpoint.substring(0, 50)}...`);
                        console.log('Suscripción completa:', subscription);
                    } else {
                        log('No hay suscripción activa', 'warning');
                    }
                } catch (error) {
                    log('Error al verificar suscripción: ' + error.message, 'error');
                }
            });
            
            document.getElementById('btn-test-notification').addEventListener('click', async () => {
                try {
                    log('Enviando notificación de prueba...');
                    
                    const response = await fetch('api/enviar_notificacion_push.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            tipo: 'sistema',
                            mensaje: 'Esta es una notificación de prueba',
                            url: window.location.href
                        })
                    });
                    
                    const data = await response.json();
                    
                    if (data.success) {
                        log('Notificación enviada correctamente', 'success');
                        log(`Enviada a ${data.estadisticas.enviadas} dispositivos, ` +
                          `fallida en ${data.estadisticas.fallidas} dispositivos`);
                    } else {
                        log('Error al enviar notificación: ' + data.message, 'error');
                    }
                } catch (error) {
                    log('Error al enviar notificación: ' + error.message, 'error');
                }
            });
            
            document.getElementById('btn-clear-log').addEventListener('click', () => {
                document.getElementById('log-output').innerHTML = '// Registro de actividad\n// ------------------\n';
            });
            
            document.getElementById('btn-check-vapid').addEventListener('click', () => {
                checkVapidKeys();
            });
            
            document.getElementById('btn-generate-vapid').addEventListener('click', async () => {
                try {
                    log('Regenerando claves VAPID...');
                    
                    const response = await fetch('api/generar_vapid_keys.php?force=1');
                    const data = await response.json();
                    
                    if (data.success) {
                        log('Claves VAPID regeneradas correctamente', 'success');
                        checkVapidKeys();
                    } else {
                        log('Error al regenerar claves VAPID: ' + data.message, 'error');
                    }
                } catch (error) {
                    log('Error al regenerar claves VAPID: ' + error.message, 'error');
                }
            });
            
            // Formulario de envío de notificación personalizada
            document.getElementById('form-send-notification').addEventListener('submit', async (e) => {
                e.preventDefault();
                
                const title = document.getElementById('notification-title').value;
                const body = document.getElementById('notification-body').value;
                const type = document.getElementById('notification-type').value;
                
                if (!title || !body) {
                    log('Por favor complete todos los campos', 'warning');
                    return;
                }
                
                try {
                    log(`Enviando notificación personalizada de tipo "${type}"...`);
                    
                    const response = await fetch('api/enviar_notificacion_push.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            tipo: type,
                            mensaje: body,
                            titulo: title,
                            url: window.location.href
                        })
                    });
                    
                    const data = await response.json();
                    
                    if (data.success) {
                        log('Notificación personalizada enviada correctamente', 'success');
                        log(`Enviada a ${data.estadisticas.enviadas} dispositivos, ` +
                          `fallida en ${data.estadisticas.fallidas} dispositivos`);
                    } else {
                        log('Error al enviar notificación: ' + data.message, 'error');
                    }
                } catch (error) {
                    log('Error al enviar notificación: ' + error.message, 'error');
                }
            });
        });
    </script>
</body>
</html> 