<?php
/**
 * Ejemplo de configuración del puente para PRODUCCIÓN.
 * Copiar a config_puente.php y ajustar si aplica.
 * En el servidor POS (ej. doctorpez.mx) las URLs deben ser alcanzables por CURL desde ese servidor.
 */
$FDP_AUTH_API_URL = 'https://api.farmaciasdeldoctorpez.com/api/v1/auth';
$FDP_POS_BRIDGE_API_KEY = 'pos-bridge-dev-key'; // Usar clave segura en prod; misma que POS_BRIDGE_API_KEY en backend
$FDP_APP_URL = 'https://app.farmaciasdeldoctorpez.com';
