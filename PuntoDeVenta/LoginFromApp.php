<?php
/**
 * Entrada al POS desde el sistema nuevo (puente de sesión).
 * Recibe ?token=xxx, valida el token con el API del sistema nuevo,
 * setea la variable de sesión correspondiente al tipo de usuario
 * y redirige a PuntoDeVentaFarmacias, ControlYAdministracion, etc.
 * No modifica el login tradicional (ValidadorUsuario + ControlPOS).
 */
session_start();
require_once __DIR__ . '/config_puente.php';

$token = isset($_GET['token']) ? trim($_GET['token']) : '';
if ($token === '') {
    header('Location: https://doctorpez.mx/PuntoDeVenta/');
    exit;
}

$apiBase = isset($FDP_AUTH_API_URL) ? $FDP_AUTH_API_URL : 'https://api.farmaciasdeldoctorpez.com/api/v1/auth';
$validateUrl = rtrim($apiBase, '/') . '/pos-bridge/validate?token=' . urlencode($token);

$ch = curl_init($validateUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
$response = curl_exec($ch);
$httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode !== 200 || !$response) {
    header('Location: https://doctorpez.mx/PuntoDeVenta/');
    exit;
}

$data = json_decode($response, true);
if (!is_array($data) || empty($data['legacy_user_id']) || empty($data['pos_session_key']) || empty($data['pos_redirect_path'])) {
    header('Location: https://doctorpez.mx/PuntoDeVenta/');
    exit;
}

$legacy_user_id = $data['legacy_user_id'];
$pos_session_key = $data['pos_session_key'];
$pos_redirect_path = preg_replace('/[^a-zA-Z0-9_\-]/', '', $data['pos_redirect_path']);
if ($pos_redirect_path === '') {
    $pos_redirect_path = 'PuntoDeVentaFarmacias';
}

$_SESSION[$pos_session_key] = $legacy_user_id;

$baseUrl = 'https://doctorpez.mx/PuntoDeVenta/';
header('Location: ' . $baseUrl . $pos_redirect_path . '/');
exit;
