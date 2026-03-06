<?php
/**
 * Muestra un mensaje cuando el puente POS ↔ Sistema nuevo falla.
 * Recibe ?code= validate_failed | token_failed | invalid_response | no_token | no_session
 * Sin datos sensibles; diseño cuidado.
 */
$baseUrl = 'https://doctorpez.mx/PuntoDeVenta/';
$code = isset($_GET['code']) ? trim($_GET['code']) : '';

$messages = [
    'validate_failed' => [
        'title' => 'No se pudo validar el enlace',
        'detail' => 'No fue posible conectar con el sistema nuevo. Contacta al administrador para revisar la configuración del puente.',
    ],
    'token_failed' => [
        'title' => 'No se pudo obtener el enlace',
        'detail' => 'La conexión con el sistema nuevo no se completó. El administrador debe verificar la configuración del puente en el servidor.',
    ],
    'invalid_response' => [
        'title' => 'Respuesta inesperada',
        'detail' => 'El sistema nuevo respondió de forma no válida. Contacta al administrador.',
    ],
    'no_token' => [
        'title' => 'Enlace incompleto',
        'detail' => 'Este enlace no incluye los datos necesarios. Usa la opción "Volver al POS" desde el sistema nuevo.',
    ],
    'no_session' => [
        'title' => 'Sin sesión activa',
        'detail' => 'Inicia sesión en el Punto de Venta y luego usa "Ir al sistema nuevo".',
    ],
];

$info = isset($messages[$code]) ? $messages[$code] : ['title' => 'Error del puente', 'detail' => 'Algo no salió bien. Intenta de nuevo o contacta al administrador.'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error del puente - Punto de Venta</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,500;0,9..40,600;1,9..40,400&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: 'DM Sans', system-ui, sans-serif;
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
            background: linear-gradient(160deg, #0f172a 0%, #1e293b 45%, #0f172a 100%);
            color: #e2e8f0;
        }
        .card {
            width: 100%;
            max-width: 420px;
            background: rgba(30, 41, 59, 0.85);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(148, 163, 184, 0.12);
            border-radius: 16px;
            padding: 2rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.4);
        }
        .icon {
            width: 56px;
            height: 56px;
            margin: 0 auto 1.25rem;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(248, 113, 113, 0.15);
            border-radius: 14px;
            color: #f87171;
        }
        .icon svg {
            width: 28px;
            height: 28px;
        }
        h1 {
            margin: 0 0 0.75rem;
            font-size: 1.25rem;
            font-weight: 600;
            letter-spacing: -0.02em;
            color: #f8fafc;
        }
        .detail {
            margin: 0 0 1.75rem;
            font-size: 0.9375rem;
            line-height: 1.6;
            color: #94a3b8;
        }
        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.25rem;
            font-family: inherit;
            font-size: 0.9375rem;
            font-weight: 500;
            color: #f1f5f9;
            background: rgba(148, 163, 184, 0.2);
            border: 1px solid rgba(148, 163, 184, 0.25);
            border-radius: 10px;
            cursor: pointer;
            text-decoration: none;
            transition: background 0.2s, border-color 0.2s;
        }
        .btn-back:hover {
            background: rgba(148, 163, 184, 0.3);
            border-color: rgba(148, 163, 184, 0.4);
        }
        .btn-back svg {
            width: 18px;
            height: 18px;
            flex-shrink: 0;
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="icon" aria-hidden="true">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
        </div>
        <h1><?php echo htmlspecialchars($info['title']); ?></h1>
        <p class="detail"><?php echo htmlspecialchars($info['detail']); ?></p>
        <a href="#" class="btn-back" id="btn-back" title="Volver a la página anterior">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Volver a la página anterior
        </a>
    </div>
    <script>
        (function() {
            var btn = document.getElementById('btn-back');
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                if (window.history.length > 1) {
                    window.history.back();
                } else {
                    window.location.href = <?php echo json_encode($baseUrl); ?>;
                }
            });
        })();
    </script>
</body>
</html>
