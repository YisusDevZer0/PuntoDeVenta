<?php
/**
 * Redirige al usuario al sistema nuevo con un token de un solo uso.
 * Detecta la sesión POS activa (VentasPos, ControlMaestro, etc.),
 * pide un token al API del sistema nuevo y redirige a /auth/pos-callback?token=xxx.
 * No cierra la sesión del POS.
 */
session_start();
require_once __DIR__ . '/config_puente.php';

$sessionKeys = [
    'VentasPos',
    'ControlMaestro',
    'AdministradorGeneral',
    'ResponsableDeSupervision',
    'Supervision',
    'AdministradorRH',
    'ResponsableDelCedis',
    'Inventarios',
    'Enfermeria',
    'Marketing',
];

$legacy_user_id = null;
foreach ($sessionKeys as $key) {
    if (!empty($_SESSION[$key])) {
        $legacy_user_id = $_SESSION[$key];
        break;
    }
}

if ($legacy_user_id === null || $legacy_user_id === '') {
    header('Location: https://doctorpez.mx/PuntoDeVenta/');
    exit;
}

$apiUrl = isset($FDP_AUTH_API_URL) ? $FDP_AUTH_API_URL : 'https://api.farmaciasdeldoctorpez.com/api/v1/auth';
$apiKey = isset($FDP_POS_BRIDGE_API_KEY) ? $FDP_POS_BRIDGE_API_KEY : '';
$appUrl = isset($FDP_APP_URL) ? $FDP_APP_URL : 'https://app.farmaciasdeldoctorpez.com';

$tokenUrl = rtrim($apiUrl, '/') . '/pos-login-token';

$ch = curl_init($tokenUrl);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['legacy_user_id' => (string) $legacy_user_id]));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'X-API-Key: ' . $apiKey,
]);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
$response = curl_exec($ch);
$httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode !== 200 || !$response) {
    header('Location: https://doctorpez.mx/PuntoDeVenta/');
    exit;
}

$data = json_decode($response, true);
$token = is_array($data) && !empty($data['token']) ? $data['token'] : '';
if ($token === '') {
    header('Location: https://doctorpez.mx/PuntoDeVenta/');
    exit;
}

$callbackUrl = rtrim($appUrl, '/') . '/auth/pos-callback?token=' . urlencode($token);
header('Location: ' . $callbackUrl);
exit;
