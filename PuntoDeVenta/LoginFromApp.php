<?php

require_once __DIR__ . '/config/fragment_init.php';
/**
 * Entrada al POS desde el sistema nuevo (puente de sesión).
 * Recibe ?token=xxx, valida el token con el API del sistema nuevo,
 * setea la variable de sesión correspondiente al tipo de usuario
 * y redirige a PuntoDeVentaFarmacias, ControlYAdministracion, etc.
 * No modifica el login tradicional (ValidadorUsuario + ControlPOS).
 */
session_start();
require_once __DIR__ . '/config_puente.php';

$errorUrl = fdp_url('bridge_error.php?code=');
$token = isset($_GET['token']) ? trim($_GET['token']) : '';
if ($token === '') {
    header('Location: ' . $errorUrl . 'no_token');
    exit;
}

$apiBase = isset($FDP_AUTH_API_URL) ? $FDP_AUTH_API_URL : 'http://localhost:8000/api/v1/auth';
$validateUrl = rtrim($apiBase, '/') . '/pos-bridge/validate?token=' . urlencode($token);

$ch = curl_init($validateUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
// Misma clave que pos-login-token; obligatoria si el API tiene POS_BRIDGE_API_KEY configurada
if (isset($FDP_POS_BRIDGE_API_KEY) && $FDP_POS_BRIDGE_API_KEY !== '') {
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['X-API-Key: ' . $FDP_POS_BRIDGE_API_KEY]);
}
$response = curl_exec($ch);
$httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode !== 200 || !$response) {
    header('Location: ' . $errorUrl . 'validate_failed');
    exit;
}

$data = json_decode($response, true);
if (!is_array($data) || empty($data['legacy_user_id']) || empty($data['pos_session_key']) || empty($data['pos_redirect_path'])) {
    header('Location: ' . $errorUrl . 'invalid_response');
    exit;
}

$legacy_user_id = $data['legacy_user_id'];
$pos_session_key = $data['pos_session_key'];
$pos_redirect_path = preg_replace('/[^a-zA-Z0-9_\-]/', '', $data['pos_redirect_path']);
if ($pos_redirect_path === '') {
    $pos_redirect_path = 'PuntoDeVentaFarmacias';
}

$_SESSION[$pos_session_key] = $legacy_user_id;

header('Location: ' . fdp_url($pos_redirect_path . '/'));
exit;
