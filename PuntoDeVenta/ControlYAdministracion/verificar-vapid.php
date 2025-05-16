<?php
// Habilitar visualización de errores para debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// No hacer caché
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificación de Claves VAPID</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; line-height: 1.6; }
        .container { max-width: 800px; margin: 0 auto; }
        .result { padding: 15px; margin: 10px 0; border-radius: 5px; }
        .success { background-color: #d4edda; color: #155724; }
        .error { background-color: #f8d7da; color: #721c24; }
        .info { background-color: #d1ecf1; color: #0c5460; }
        pre { background: #f4f4f4; padding: 10px; overflow: auto; }
        .btn { padding: 8px 16px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; }
        .btn:hover { background: #0069d9; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Verificación de Claves VAPID</h1>
        
        <div class="info result">
            <p>Esta herramienta verifica que las claves VAPID para notificaciones push estén correctamente configuradas.</p>
        </div>
        
        <h2>1. Verificar archivo de claves</h2>
        <?php
        $configFile = './config/vapid_keys.json';
        
        if (file_exists($configFile)) {
            echo '<div class="success result">';
            echo '<p>✅ El archivo de claves VAPID existe en: ' . $configFile . '</p>';
            
            // Verificar permisos
            $perms = fileperms($configFile);
            $permsOctal = substr(sprintf('%o', $perms), -4);
            echo '<p>Permisos del archivo: ' . $permsOctal . '</p>';
            
            // Intentar leer archivo
            $content = @file_get_contents($configFile);
            if ($content === false) {
                echo '<p>❌ No se puede leer el archivo. Verifique los permisos.</p>';
            } else {
                echo '<p>✅ Se puede leer el archivo correctamente.</p>';
                
                // Verificar validez JSON
                $keyData = json_decode($content, true);
                if ($keyData === null) {
                    echo '<p>❌ El archivo no contiene JSON válido. Error: ' . json_last_error_msg() . '</p>';
                } else {
                    echo '<p>✅ El archivo contiene JSON válido.</p>';
                    
                    // Verificar claves necesarias
                    if (!isset($keyData['publicKey'])) {
                        echo '<p>❌ No se encontró la clave pública.</p>';
                    } else {
                        echo '<p>✅ Clave pública encontrada.</p>';
                        echo '<p>Longitud de la clave pública: ' . strlen($keyData['publicKey']) . ' caracteres</p>';
                    }
                    
                    if (!isset($keyData['privateKey'])) {
                        echo '<p>❌ No se encontró la clave privada.</p>';
                    } else {
                        echo '<p>✅ Clave privada encontrada.</p>';
                        echo '<p>Longitud de la clave privada: ' . strlen($keyData['privateKey']) . ' caracteres</p>';
                    }
                    
                    if (!isset($keyData['subject'])) {
                        echo '<p>❌ No se encontró el subject VAPID.</p>';
                    } else {
                        echo '<p>✅ Subject VAPID encontrado: ' . htmlspecialchars($keyData['subject']) . '</p>';
                    }
                }
            }
            echo '</div>';
        } else {
            echo '<div class="error result">';
            echo '<p>❌ El archivo de claves VAPID NO existe en: ' . $configFile . '</p>';
            echo '<p>Debe generar las claves VAPID primero.</p>';
            echo '</div>';
        }
        ?>
        
        <h2>2. Opciones</h2>
        <p><a href="api/generar_vapid_keys.php" class="btn">Generar/Regenerar Claves VAPID</a></p>
        <p><a href="api/get_vapid_public_key.php" class="btn">Verificar API de Clave Pública</a></p>
        <p><a href="test-push.php" class="btn">Ir a Prueba de Notificaciones</a></p>
        
        <h2>3. Información del Servidor</h2>
        <div class="info result">
            <p><strong>PHP Version:</strong> <?php echo phpversion(); ?></p>
            <p><strong>OpenSSL Instalado:</strong> <?php echo extension_loaded('openssl') ? 'Sí ✅' : 'No ❌'; ?></p>
            <p><strong>cURL Instalado:</strong> <?php echo extension_loaded('curl') ? 'Sí ✅' : 'No ❌'; ?></p>
            <p><strong>JSON Instalado:</strong> <?php echo extension_loaded('json') ? 'Sí ✅' : 'No ❌'; ?></p>
            <p><strong>Servidor Web:</strong> <?php echo $_SERVER['SERVER_SOFTWARE'] ?? 'Desconocido'; ?></p>
        </div>
    </div>
</body>
</html> 