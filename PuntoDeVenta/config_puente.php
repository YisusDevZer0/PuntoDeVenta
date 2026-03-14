<?php
/**
 * Configuración del puente POS ↔ Sistema nuevo.
 * Editar aquí las URLs y la API key; el POS no usa .env.
 *
 * IMPORTANTE: LoginFromApp.php e IrAlSistemaNuevo.php hacen CURL desde ESTE servidor.
 * Ambiente develop online: API https://api.farmacitasdeldoctorpez.online | Front https://farmacitasdeldoctorpez.online
 */
// URL base del API de auth (develop online). El POS concatena ej. /pos-login-token a esta URL.
$FDP_AUTH_API_URL = 'https://api.farmacitasdeldoctorpez.online/api/v1/auth';
// API key: debe ser EXACTAMENTE la misma que POS_BRIDGE_API_KEY en el .env del deploy de la API.
$FDP_POS_BRIDGE_API_KEY = 'SBemUapHVTbyA8Yf66E1+rHJ/FsxOdBErrswNSy9xdE=';
// URL de la app (donde está /auth/pos-callback). Develop online.
$FDP_APP_URL = 'https://farmacitasdeldoctorpez.online';
