<?php
/**
 * Muestra un mensaje claro cuando el puente POS ↔ Sistema nuevo falla.
 * Recibe ?code= validate_failed | token_failed | invalid_response | no_token | no_session
 */
$baseUrl = 'https://doctorpez.mx/PuntoDeVenta/';
$code = isset($_GET['code']) ? trim($_GET['code']) : '';

$messages = [
    'validate_failed' => [
        'title' => 'No se pudo validar el enlace con el sistema nuevo',
        'detail' => 'El servidor no pudo conectar con el API. En este servidor, revisa <strong>config_puente.php</strong>: debe usar la <strong>URL pública del API</strong> (ej. https://api.farmaciasdeldoctorpez.com/api/v1/auth), no localhost.',
    ],
    'token_failed' => [
        'title' => 'No se pudo obtener el enlace al sistema nuevo',
        'detail' => 'El servidor no pudo conectar con el API o la API key no coincide. Revisa <strong>config_puente.php</strong>: <code>FDP_AUTH_API_URL</code> debe ser la URL pública del API y <code>FDP_POS_BRIDGE_API_KEY</code> debe coincidir con la del backend.',
    ],
    'invalid_response' => [
        'title' => 'Respuesta inválida del sistema nuevo',
        'detail' => 'El API respondió pero con un formato inesperado. Revisa que la URL en config_puente.php apunte al API correcto.',
    ],
    'no_token' => [
        'title' => 'Falta el token de acceso',
        'detail' => 'Se abrió el enlace al POS sin token. Usa "Volver al POS" desde el sistema nuevo.',
    ],
    'no_session' => [
        'title' => 'Sin sesión en el Punto de Venta',
        'detail' => 'Inicia sesión primero en el POS y luego usa "Ir al sistema nuevo".',
    ],
];

$info = isset($messages[$code]) ? $messages[$code] : ['title' => 'Error del puente', 'detail' => 'Código: ' . htmlspecialchars($code)];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error del puente - Punto de Venta</title>
    <style>
        body { font-family: sans-serif; max-width: 520px; margin: 2rem auto; padding: 0 1rem; }
        .box { border: 1px solid #c00; background: #fff5f5; border-radius: 8px; padding: 1.25rem; }
        h2 { margin: 0 0 0.75rem; color: #c00; font-size: 1.1rem; }
        p { margin: 0 0 1rem; line-height: 1.5; color: #333; }
        p:last-child { margin-bottom: 0; }
        a { color: #06c; }
        code { background: #eee; padding: 0.1em 0.4em; border-radius: 3px; font-size: 0.9em; }
    </style>
</head>
<body>
    <div class="box">
        <h2><?php echo htmlspecialchars($info['title']); ?></h2>
        <p><?php echo $info['detail']; ?></p>
        <p><a href="<?php echo htmlspecialchars($baseUrl); ?>">Volver al inicio de sesión</a></p>
    </div>
</body>
</html>
