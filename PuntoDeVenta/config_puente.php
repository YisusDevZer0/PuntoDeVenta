<?php
require_once __DIR__ . '/config/app.php';

/**
 * Configuración del puente POS ↔ Sistema nuevo.
 *
 * En doctorpez.mx (Hostinger) no hay .env: los valores del puente viven SOLO en este archivo.
 * Tras editar, sube este PHP al servidor; el API (Vercel/Hetzner/etc.) sigue usando su .env (POS_BRIDGE_API_KEY).
 *
 * IMPORTANTE: LoginFromApp.php e IrAlSistemaNuevo.php hacen CURL desde ESTE servidor (Hostinger).
 * Debe apuntar al MISMO API y front que usa el usuario en producción (mismo JWT / misma BD).
 *
 * Develop online (referencia): API https://api.farmacitasdeldoctorpez.online | Front https://farmacitasdeldoctorpez.online
 */
// URL base del auth del API de producción (sin barra final antes de /pos-login-token).
$FDP_AUTH_API_URL = 'https://api.farmacita.com/api/v1/auth';
// API key: EXACTAMENTE la misma que POS_BRIDGE_API_KEY en el .env del deploy de ese API.
$FDP_POS_BRIDGE_API_KEY = 'SBemUapHVTbyA8Yf66E1+rHJ/FsxOdBErrswNSy9xdE=';
// Front donde está /auth/pos-callback (misma URL que NEXT_PUBLIC_* en prod).
$FDP_APP_URL = 'https://farmacita.com';
