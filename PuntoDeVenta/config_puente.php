<?php
/**
 * Configuración del puente POS ↔ Sistema nuevo.
 * Editar aquí las URLs y la API key; el POS no usa .env.
 *
 * IMPORTANTE: LoginFromApp.php e IrAlSistemaNuevo.php hacen CURL desde ESTE servidor.
 * - Desarrollo local: si POS y API corren en la misma máquina, usar localhost.
 * - Producción (ej. POS en doctorpez.mx): usar URLs PÚBLICAS que este servidor pueda alcanzar.
 *   No usar localhost en producción: el servidor POS no puede abrir localhost de tu PC.
 *
 * Producción (ejemplo): ver config_puente.example.php
 */
// URL base del API de auth. Producción: https://api.farmaciasdeldoctorpez.com/api/v1/auth
$FDP_AUTH_API_URL = 'http://localhost:8000/api/v1/auth';
// API key: debe coincidir con POS_BRIDGE_API_KEY en .env del backend (FarmacitasCore)
$FDP_POS_BRIDGE_API_KEY = 'pos-bridge-dev-key';
// URL de la app (donde está /auth/pos-callback). Producción: https://app.farmaciasdeldoctorpez.com
$FDP_APP_URL = 'http://localhost:3000';
