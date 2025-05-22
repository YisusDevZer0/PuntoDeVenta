<?php
// Cargar configuración
$config = require_once 'api/onesignal_config.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test OneSignal</title>
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
    </style>
</head>
<body>
    <div class="container py-5">
        <h1 class="mb-4">Test OneSignal</h1>
        
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">Estado del Sistema</h5>
                <div id="status" class="status-container">
                    <div class="text-center">
                        <i class="fas fa-spinner fa-spin"></i> Verificando estado...
                    </div>
                </div>
                
                <div class="d-flex justify-content-center gap-3 mt-4">
                    <button id="btn-subscribe" class="btn btn-primary btn-lg">
                        <i class="fas fa-bell"></i> Suscribirse a Notificaciones
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

    <!-- OneSignal -->
    <script src="https://cdn.onesignal.com/sdks/OneSignalSDK.js" async=""></script>
    <script>
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

        // Inicializar OneSignal
        window.OneSignal = window.OneSignal || [];
        OneSignal.push(function() {
            OneSignal.init({
                appId: "<?php echo $config['app_id']; ?>",
                safari_web_id: "<?php echo $config['safari_web_id'] ?? ''; ?>",
                notifyButton: {
                    enable: false // Deshabilitamos el botón flotante de OneSignal
                },
                allowLocalhostAsSecureOrigin: true,
                promptOptions: {
                    slidedown: {
                        prompts: [
                            {
                                type: "push",
                                autoPrompt: false,
                                text: {
                                    actionMessage: "¿Quieres recibir notificaciones?",
                                    acceptButton: "Sí, permitir",
                                    cancelButton: "No, gracias"
                                }
                            }
                        ]
                    }
                }
            });

            // Verificar estado de suscripción
            OneSignal.isPushNotificationsEnabled().then(function(isEnabled) {
                updateStatus(isEnabled);
            });
        });

        // Función para actualizar el estado
        function updateStatus(isSubscribed) {
            const statusContainer = document.getElementById('status');
            const btnSubscribe = document.getElementById('btn-subscribe');
            const btnTest = document.getElementById('btn-test');

            let html = '<ul class="list-unstyled">';
            
            // Verificar soporte de notificaciones
            if ('Notification' in window) {
                html += '<li class="text-success">✓ Notificaciones soportadas</li>';
            } else {
                html += '<li class="text-danger">✗ Notificaciones no soportadas</li>';
            }

            // Estado de la suscripción
            if (isSubscribed) {
                html += '<li class="text-success">✓ Suscrito a notificaciones</li>';
                btnSubscribe.style.display = 'none';
                btnTest.style.display = 'inline-block';
            } else {
                html += '<li class="text-warning">⚠️ No suscrito a notificaciones</li>';
                btnSubscribe.style.display = 'inline-block';
                btnTest.style.display = 'none';
            }

            html += '</ul>';
            statusContainer.innerHTML = html;
        }

        // Función para suscribirse
        async function subscribe() {
            try {
                log('Solicitando suscripción...', 'info');
                
                // Solicitar permisos usando la API correcta
                const isSubscribed = await OneSignal.isPushNotificationsEnabled();
                
                if (!isSubscribed) {
                    await OneSignal.showSlidedownPrompt();
                    const newSubscriptionState = await OneSignal.isPushNotificationsEnabled();
                    
                    if (newSubscriptionState) {
                        log('Permisos concedidos', 'success');
                        updateStatus(true);
                    } else {
                        throw new Error('Permiso denegado para notificaciones');
                    }
                } else {
                    log('Ya estás suscrito a las notificaciones', 'info');
                    updateStatus(true);
                }
            } catch (error) {
                log(`Error en suscripción: ${error.message}`, 'error');
            }
        }

        // Función para enviar notificación de prueba
        async function sendTestNotification() {
            try {
                log('Enviando notificación de prueba...', 'info');
                
                const response = await fetch('api/enviar_notificacion_onesignal.php', {
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

        // Inicializar
        document.addEventListener('DOMContentLoaded', function() {
            // Registrar eventos de botones
            document.getElementById('btn-subscribe').addEventListener('click', subscribe);
            document.getElementById('btn-test').addEventListener('click', sendTestNotification);
        });
    </script>
</body>
</html> 