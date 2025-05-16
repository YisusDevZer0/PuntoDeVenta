<?php
// Habilitar visualización de errores para debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// No hacer caché
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');

// Definir rutas
$configDir = './config';
$configFile = $configDir . '/vapid_keys.json';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Diagnóstico de Claves VAPID</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
    <style>
        body { padding: 20px; }
        .container { max-width: 900px; }
        pre { background: #f8f9fa; padding: 10px; border-radius: 4px; }
        .key-block { word-break: break-all; font-family: monospace; }
        .success { color: #28a745; }
        .error { color: #dc3545; }
        .warning { color: #ffc107; }
        .info { color: #17a2b8; }
        .panel { border: 1px solid #dee2e6; border-radius: 4px; margin-bottom: 20px; }
        .panel-heading { background-color: #f8f9fa; padding: 10px 15px; border-bottom: 1px solid #dee2e6; }
        .panel-body { padding: 15px; }
        .status-icon { margin-right: 5px; }
        .bytes { font-family: monospace; background-color: #f8f9fa; padding: 1px 4px; border-radius: 2px; }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="mb-4">
            <i class="fas fa-key mr-2"></i>
            Diagnóstico de Claves VAPID
        </h1>
        
        <div class="alert alert-info">
            <i class="fas fa-info-circle mr-2"></i>
            Esta herramienta analiza las claves VAPID, verifica su formato y realiza pruebas de diagnóstico.
        </div>
        
        <div class="panel">
            <div class="panel-heading">
                <h4 class="mb-0"><i class="fas fa-file-alt mr-2"></i>Estado del archivo de claves</h4>
            </div>
            <div class="panel-body">
                <?php
                // Verificar si el archivo existe
                if (file_exists($configFile)) {
                    echo '<p><i class="fas fa-check-circle status-icon success"></i>El archivo de claves VAPID existe en: <code>' . $configFile . '</code></p>';
                    
                    // Verificar permisos
                    $perms = fileperms($configFile);
                    $permsOctal = substr(sprintf('%o', $perms), -4);
                    echo '<p>Permisos del archivo: <code>' . $permsOctal . '</code></p>';
                    
                    // Intentar leer archivo
                    $content = @file_get_contents($configFile);
                    if ($content === false) {
                        echo '<p><i class="fas fa-times-circle status-icon error"></i>No se puede leer el archivo. Verifique los permisos.</p>';
                    } else {
                        echo '<p><i class="fas fa-check-circle status-icon success"></i>El archivo se puede leer correctamente.</p>';
                        
                        // Verificar validez JSON
                        $keyData = json_decode($content, true);
                        if ($keyData === null) {
                            echo '<p><i class="fas fa-times-circle status-icon error"></i>El archivo no contiene JSON válido. Error: ' . json_last_error_msg() . '</p>';
                        } else {
                            echo '<p><i class="fas fa-check-circle status-icon success"></i>El archivo contiene JSON válido.</p>';
                            
                            // Información general
                            echo '<h5 class="mt-3">Información general de las claves:</h5>';
                            echo '<ul>';
                            
                            // Fecha de creación
                            if (isset($keyData['created'])) {
                                echo '<li>Fecha de creación: ' . $keyData['created'] . '</li>';
                            }
                            
                            // Formato
                            if (isset($keyData['format'])) {
                                echo '<li>Formato declarado: ' . $keyData['format'] . '</li>';
                            }
                            
                            // Subject
                            if (isset($keyData['subject'])) {
                                echo '<li>Subject (para autenticación): ' . htmlspecialchars($keyData['subject']) . '</li>';
                            } else {
                                echo '<li><span class="error">⚠ El subject VAPID no está definido</span></li>';
                            }
                            
                            // Claves simuladas o reales
                            if (isset($keyData['simulated']) && $keyData['simulated']) {
                                echo '<li><span class="warning">⚠ Estas son claves simuladas (no criptográficamente seguras)</span></li>';
                            }
                            
                            echo '</ul>';
                        }
                    }
                } else {
                    echo '<p><i class="fas fa-times-circle status-icon error"></i>El archivo de claves VAPID NO existe en: <code>' . $configFile . '</code></p>';
                    echo '<p>Debe generar las claves VAPID primero.</p>';
                    
                    // Verificar si existe el directorio config
                    if (!file_exists($configDir)) {
                        echo '<p><i class="fas fa-times-circle status-icon error"></i>El directorio <code>' . $configDir . '</code> no existe.</p>';
                    } else {
                        echo '<p><i class="fas fa-check-circle status-icon success"></i>El directorio <code>' . $configDir . '</code> existe.</p>';
                        
                        // Verificar permisos de escritura
                        if (is_writable($configDir)) {
                            echo '<p><i class="fas fa-check-circle status-icon success"></i>El directorio tiene permisos de escritura.</p>';
                        } else {
                            echo '<p><i class="fas fa-times-circle status-icon error"></i>El directorio NO tiene permisos de escritura.</p>';
                        }
                    }
                }
                ?>
            </div>
        </div>
        
        <?php if (file_exists($configFile) && isset($keyData) && is_array($keyData)): ?>
        <div class="panel">
            <div class="panel-heading">
                <h4 class="mb-0"><i class="fas fa-key mr-2"></i>Análisis de Claves</h4>
            </div>
            <div class="panel-body">
                <?php
                // Verificar claves necesarias
                $requiredKeys = ['publicKey', 'privateKey', 'subject'];
                $missingKeys = [];
                
                foreach ($requiredKeys as $key) {
                    if (!isset($keyData[$key]) || empty($keyData[$key])) {
                        $missingKeys[] = $key;
                    }
                }
                
                if (count($missingKeys) > 0) {
                    echo '<p><i class="fas fa-times-circle status-icon error"></i>Faltan claves requeridas: <strong>' . implode(', ', $missingKeys) . '</strong></p>';
                } else {
                    echo '<p><i class="fas fa-check-circle status-icon success"></i>Todas las claves requeridas están presentes.</p>';
                    
                    // Analizar la clave pública
                    $publicKey = $keyData['publicKey'];
                    $privateKey = $keyData['privateKey'];
                    
                    echo '<h5 class="mt-3">Clave Pública:</h5>';
                    echo '<div class="key-block mb-2">' . htmlspecialchars($publicKey) . '</div>';
                    echo '<ul>';
                    echo '<li>Longitud: ' . strlen($publicKey) . ' caracteres</li>';
                    
                    // Verificar formato base64url
                    if (preg_match('/^[A-Za-z0-9\-_]+$/', $publicKey)) {
                        echo '<li><i class="fas fa-check-circle status-icon success"></i>Formato base64url válido</li>';
                    } else {
                        echo '<li><i class="fas fa-times-circle status-icon error"></i>Formato base64url inválido</li>';
                    }
                    
                    // Verificar padding
                    if (substr($publicKey, -1) === '=') {
                        echo '<li><i class="fas fa-times-circle status-icon error"></i>Contiene padding (=) al final, debería eliminarse</li>';
                    } else {
                        echo '<li><i class="fas fa-check-circle status-icon success"></i>No contiene padding (=)</li>';
                    }
                    
                    // Intentar decodificar
                    $paddedPublicKey = $publicKey . str_repeat('=', (4 - (strlen($publicKey) % 4)) % 4);
                    $decodedPublicKey = base64_decode(str_replace(['-', '_'], ['+', '/'], $paddedPublicKey));
                    
                    if ($decodedPublicKey === false) {
                        echo '<li><i class="fas fa-times-circle status-icon error"></i>No se puede decodificar la clave pública</li>';
                    } else {
                        echo '<li><i class="fas fa-check-circle status-icon success"></i>La clave pública se puede decodificar</li>';
                        echo '<li>Longitud decodificada: ' . strlen($decodedPublicKey) . ' bytes</li>';
                        
                        // Verificar si el primer byte es 0x04 (formato sin comprimir)
                        if (strlen($decodedPublicKey) > 0) {
                            $firstByte = ord($decodedPublicKey[0]);
                            if ($firstByte === 4) {
                                echo '<li><i class="fas fa-check-circle status-icon success"></i>El primer byte es <span class="bytes">0x04</span> (formato correcto sin comprimir)</li>';
                            } else {
                                echo '<li><i class="fas fa-exclamation-triangle status-icon warning"></i>El primer byte es <span class="bytes">0x' . dechex($firstByte) . '</span> (debería ser <span class="bytes">0x04</span>)</li>';
                            }
                        }
                    }
                    echo '</ul>';
                    
                    echo '<h5 class="mt-3">Clave Privada:</h5>';
                    echo '<div class="key-block mb-2">' . htmlspecialchars($privateKey) . '</div>';
                    echo '<ul>';
                    echo '<li>Longitud: ' . strlen($privateKey) . ' caracteres</li>';
                    
                    // Verificar formato base64url
                    if (preg_match('/^[A-Za-z0-9\-_]+$/', $privateKey)) {
                        echo '<li><i class="fas fa-check-circle status-icon success"></i>Formato base64url válido</li>';
                    } else {
                        echo '<li><i class="fas fa-times-circle status-icon error"></i>Formato base64url inválido</li>';
                    }
                    
                    // Verificar padding
                    if (substr($privateKey, -1) === '=') {
                        echo '<li><i class="fas fa-times-circle status-icon error"></i>Contiene padding (=) al final, debería eliminarse</li>';
                    } else {
                        echo '<li><i class="fas fa-check-circle status-icon success"></i>No contiene padding (=)</li>';
                    }
                    
                    // Intentar decodificar
                    $paddedPrivateKey = $privateKey . str_repeat('=', (4 - (strlen($privateKey) % 4)) % 4);
                    $decodedPrivateKey = base64_decode(str_replace(['-', '_'], ['+', '/'], $paddedPrivateKey));
                    
                    if ($decodedPrivateKey === false) {
                        echo '<li><i class="fas fa-times-circle status-icon error"></i>No se puede decodificar la clave privada</li>';
                    } else {
                        echo '<li><i class="fas fa-check-circle status-icon success"></i>La clave privada se puede decodificar</li>';
                        echo '<li>Longitud decodificada: ' . strlen($decodedPrivateKey) . ' bytes</li>';
                    }
                    echo '</ul>';
                }
                ?>
            </div>
        </div>
        
        <div class="panel">
            <div class="panel-heading">
                <h4 class="mb-0"><i class="fas fa-code mr-2"></i>Código de Prueba JavaScript</h4>
            </div>
            <div class="panel-body">
                <p>El siguiente código muestra cómo convertir correctamente la clave pública VAPID a Uint8Array:</p>
                <pre>function urlBase64ToUint8Array(base64String) {
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

// Su clave pública VAPID
const vapidPublicKey = "<?php echo htmlspecialchars($publicKey); ?>";

// Convertir la clave
const applicationServerKey = urlBase64ToUint8Array(vapidPublicKey);
console.log('Clave convertida:', applicationServerKey);

// Verificar el primer byte (debería ser 4)
console.log('Primer byte:', applicationServerKey[0]);
</pre>
                <button id="btn-test-conversion" class="btn btn-primary">Probar Conversión</button>
                <div id="conversion-result" class="mt-3"></div>
            </div>
        </div>
        <?php endif; ?>
        
        <div class="panel">
            <div class="panel-heading">
                <h4 class="mb-0"><i class="fas fa-tools mr-2"></i>Acciones</h4>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="card bg-light mb-3">
                            <div class="card-body">
                                <h5 class="card-title">Regenerar Claves VAPID</h5>
                                <p class="card-text">Genera nuevas claves VAPID reemplazando las existentes.</p>
                                <a href="api/crear_vapid_keys.php" class="btn btn-primary" target="_blank">Generar Nuevas Claves</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card bg-light mb-3">
                            <div class="card-body">
                                <h5 class="card-title">Ver Clave Pública</h5>
                                <p class="card-text">Obtiene la clave pública desde la API.</p>
                                <a href="api/get_vapid_public_key.php" class="btn btn-info" target="_blank">Ver Clave Pública</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="card bg-light mb-3">
                            <div class="card-body">
                                <h5 class="card-title">Probar Notificaciones Push</h5>
                                <p class="card-text">Ir a la página de prueba de notificaciones push.</p>
                                <a href="test-push.php" class="btn btn-success">Ir a Página de Prueba</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card bg-light mb-3">
                            <div class="card-body">
                                <h5 class="card-title">Verificar Diagnóstico API</h5>
                                <p class="card-text">Ejecuta el diagnóstico de la API para verificar el estado.</p>
                                <a href="api/verificar_vapid_keys.php" class="btn btn-warning" target="_blank">Ejecutar Diagnóstico</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const btnTestConversion = document.getElementById('btn-test-conversion');
        const conversionResult = document.getElementById('conversion-result');
        
        if (btnTestConversion) {
            btnTestConversion.addEventListener('click', function() {
                try {
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
                    
                    // Su clave pública VAPID
                    const vapidPublicKey = "<?php echo isset($publicKey) ? addslashes($publicKey) : ''; ?>";
                    
                    // Convertir la clave
                    const applicationServerKey = urlBase64ToUint8Array(vapidPublicKey);
                    
                    // Crear visualización del resultado
                    let resultHTML = '<div class="alert alert-success">';
                    resultHTML += '<h5><i class="fas fa-check-circle mr-2"></i>Conversión exitosa</h5>';
                    resultHTML += '<p>La clave se convirtió correctamente a Uint8Array.</p>';
                    resultHTML += '<ul>';
                    resultHTML += '<li>Longitud del array: ' + applicationServerKey.length + ' bytes</li>';
                    resultHTML += '<li>Primer byte: 0x' + applicationServerKey[0].toString(16) + 
                                  (applicationServerKey[0] === 4 ? ' (correcto)' : ' (debería ser 0x04)') + '</li>';
                    
                    // Mostrar primeros 10 bytes
                    const firstBytes = Array.from(applicationServerKey.slice(0, 10))
                        .map(b => '0x' + b.toString(16).padStart(2, '0'))
                        .join(', ');
                    
                    resultHTML += '<li>Primeros 10 bytes: ' + firstBytes + '</li>';
                    resultHTML += '</ul>';
                    resultHTML += '</div>';
                    
                    conversionResult.innerHTML = resultHTML;
                } catch (error) {
                    conversionResult.innerHTML = '<div class="alert alert-danger">' +
                        '<h5><i class="fas fa-times-circle mr-2"></i>Error en la conversión</h5>' +
                        '<p>' + error.message + '</p>' +
                        '</div>';
                }
            });
        }
    });
    </script>
</body>
</html> 