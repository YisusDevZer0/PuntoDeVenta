<?php
/**
 * Configuración del puente POS ↔ Sistema nuevo.
 * Editar aquí las URLs y la API key; el POS no usa .env.
 */
// URL base del API de auth del sistema nuevo (local: puerto 8000; prod: https://api.farmaciasdeldoctorpez.com/api/v1/auth)
$FDP_AUTH_API_URL = 'http://localhost:8000/api/v1/auth';
// API key: debe ser la misma que en el backend (variable POS_BRIDGE_API_KEY en .env de FarmacitasCore)
$FDP_POS_BRIDGE_API_KEY = 'pos-bridge-dev-key';
// URL de la app del sistema nuevo (donde está /auth/pos-callback)
$FDP_APP_URL = 'http://localhost:3000';
