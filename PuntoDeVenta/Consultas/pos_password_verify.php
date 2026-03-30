<?php
/**
 * Verificación alineada con FarmacitasCore (app/core/security.py verify_password).
 *
 * - Valores bcrypt ($2a$/$2b$/$2y$): siempre se validan (SHA-256 binario + bcrypt, luego fallback plano vs bcrypt).
 * - Valores no bcrypt: solo si POS_PASSWORD_LEGACY_PLAINTEXT es true (migración en curso).
 *
 * Cuando todos los usuarios tengan Password hasheado, cambia la constante de abajo a false.
 */
define('POS_PASSWORD_LEGACY_PLAINTEXT', true);

function pos_password_matches(string $plain_password, string $stored): bool
{
    if ($stored === '') {
        return false;
    }
    if (preg_match('/^\$2[ayb]\$/', $stored)) {
        $prehashed = hash('sha256', $plain_password, true);
        if (password_verify($prehashed, $stored)) {
            return true;
        }
        return password_verify($plain_password, $stored);
    }
    if (!POS_PASSWORD_LEGACY_PLAINTEXT) {
        return false;
    }
    return hash_equals($stored, $plain_password);
}
