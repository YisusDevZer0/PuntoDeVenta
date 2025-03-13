<?php
/**
 * ControlPOS.php - Controlador de redirección basado en roles
 * 
 * Este script maneja la redirección de usuarios según su rol
 * después de la autenticación exitosa.
 */

// Prevenir acceso directo al archivo
if (!defined('BASEPATH')) {
    define('BASEPATH', true);
}

// Iniciar o reanudar la sesión
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Configuración de headers de seguridad
header("X-Frame-Options: DENY");
header("X-XSS-Protection: 1; mode=block");
header("X-Content-Type-Options: nosniff");

// Incluir el validador de usuario
require_once("Consultas/ValidadorUsuario.php");

// Configuración
const BASE_URL = 'https://doctorpez.mx/PuntoDeVenta/';

// Definición de rutas por rol con sus respectivos permisos
$rutasPorRol = [
    'ControlMaestro' => [
        'ruta' => 'ControlYAdministracion',
        'requiereAutenticacion' => true
    ],
    'VentasPos' => [
        'ruta' => 'PuntoDeVentaFarmacias',
        'requiereAutenticacion' => true
    ],
    'AdministradorGeneral' => [
        'ruta' => 'POSAdministracion',
        'requiereAutenticacion' => true
    ],
    'ResponsableDeSupervision' => [
        'ruta' => 'Supervision',
        'requiereAutenticacion' => true
    ],
    'Inventarios' => [
        'ruta' => 'Inventarios',
        'requiereAutenticacion' => true
    ],
    'AdministradorRH' => [
        'ruta' => 'RecursosHumanos',
        'requiereAutenticacion' => true
    ],
    'ResponsableDelCedis' => [
        'ruta' => 'CEDIS',
        'requiereAutenticacion' => true
    ],
    'Enfermeria' => [
        'ruta' => 'Enfermeria',
        'requiereAutenticacion' => true
    ]
];

/**
 * Función para realizar redirección segura
 * @param string $url URL a la que se redirigirá
 */
function redirigirSeguro($url) {
    if (!headers_sent()) {
        header("Location: " . filter_var($url, FILTER_SANITIZE_URL));
        exit();
    }
}

/**
 * Verifica si la sesión es válida
 * @return bool
 */
function sesionValida() {
    return isset($_SESSION['ultima_actividad']) && 
           (time() - $_SESSION['ultima_actividad'] < 3600); // 1 hora de timeout
}

// Actualizar timestamp de última actividad
$_SESSION['ultima_actividad'] = time();

// Verificar si hay una sesión activa y válida
$sesionEncontrada = false;

foreach ($rutasPorRol as $rol => $config) {
    if (isset($_SESSION[$rol]) && $_SESSION[$rol] && $config['requiereAutenticacion']) {
        $sesionEncontrada = true;
        redirigirSeguro(BASE_URL . $config['ruta'] . '/');
        break;
    }
}

// Si no se encontró una sesión válida, redirigir al login
if (!$sesionEncontrada) {
    redirigirSeguro(BASE_URL);
}

