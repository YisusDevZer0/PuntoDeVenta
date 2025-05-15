<?php
// Habilitar visualización de errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Cargar dependencias desde Composer
require '../../vendor/autoload.php';

use Minishlink\WebPush\VAPID;

header('Content-Type: application/json');

// Verificar si ya existen claves
$configFile = '../config/vapid_keys.json';
$configDir = dirname($configFile);

// Crear directorio si no existe
if (!file_exists($configDir)) {
    if (!mkdir($configDir, 0755, true)) {
        echo json_encode([
            'success' => false,
            'message' => 'No se pudo crear el directorio de configuración'
        ]);
        exit;
    }
}

// Generar claves VAPID si no existen
if (!file_exists($configFile)) {
    try {
        $vapidKeys = VAPID::createVapidKeys();
        
        // Guardar claves en archivo de configuración
        $keyData = [
            'publicKey' => $vapidKeys['publicKey'],
            'privateKey' => $vapidKeys['privateKey'],
            'subject' => 'mailto:admin@puntodeventa.com' // Cambiar por tu email
        ];
        
        file_put_contents($configFile, json_encode($keyData, JSON_PRETTY_PRINT));
        
        echo json_encode([
            'success' => true,
            'message' => 'Claves VAPID generadas correctamente',
            'keys' => [
                'publicKey' => $vapidKeys['publicKey']
            ]
        ]);
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Error al generar claves VAPID: ' . $e->getMessage()
        ]);
    }
} else {
    // Las claves ya existen
    $keyData = json_decode(file_get_contents($configFile), true);
    echo json_encode([
        'success' => true,
        'message' => 'Las claves VAPID ya existen',
        'keys' => [
            'publicKey' => $keyData['publicKey']
        ]
    ]);
}
?> 