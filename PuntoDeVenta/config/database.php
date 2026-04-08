<?php

/**
 * Conexión MySQL compartida (PuntoDeVenta).
 *
 * Prioridad: LEGACY_DB_* → DB_* (DB_PASS / DB_PASSWORD) → valores por defecto develop.
 */

require_once __DIR__ . '/app.php';

/**
 * @param bool $dieOnError Si es false, devuelve null ante fallo (p. ej. respuestas JSON).
 */
function fdp_db_connect(bool $dieOnError = true): ?mysqli
{
    $host = getenv('LEGACY_DB_HOST') ?: getenv('DB_HOST') ?: 'srv1264.hstgr.io';
    $port = (int) (getenv('LEGACY_DB_PORT') ?: getenv('DB_PORT') ?: '3306');
    $user = getenv('LEGACY_DB_USER') ?: getenv('DB_USER') ?: 'u858848268_DevelopPez';
    $password = getenv('LEGACY_DB_PASSWORD')
        ?: getenv('DB_PASS')
        ?: getenv('DB_PASSWORD')
        ?: 'DevelopFDP2602';
    $dbname = getenv('LEGACY_DB_NAME') ?: getenv('DB_NAME') ?: 'u858848268_Develop';
    $sslMode = strtolower((string) (getenv('LEGACY_DB_SSL_MODE') ?: getenv('DB_SSL_MODE') ?: 'prefer'));

    $applySession = static function (mysqli $conn): void {
        mysqli_query($conn, "SET time_zone = '-6:00'");
    };

    $fail = static function (bool $exitOnError, string $msg): ?mysqli {
        if ($exitOnError) {
            exit($msg);
        }
        return null;
    };

    $connectPlain = function () use ($host, $user, $password, $dbname, $port, $applySession, $dieOnError, $fail): ?mysqli {
        $conn = @mysqli_connect($host, $user, $password, $dbname, $port);
        if (!$conn) {
            return $fail($dieOnError, 'No podemos conectar a la base de datos: ' . mysqli_connect_error());
        }
        $applySession($conn);
        return $conn;
    };

    $sslFlags = 0;
    if (in_array($sslMode, ['require', 'verify-full', 'verify-ca', 'prefer'], true)) {
        $sslFlags = MYSQLI_CLIENT_SSL_DONT_VERIFY_SERVER_CERT;
    }

    if ($sslFlags !== 0) {
        $mysqli = mysqli_init();
        if ($mysqli === false) {
            return $fail($dieOnError, 'No podemos inicializar MySQLi.');
        }
        $ok = @mysqli_real_connect($mysqli, $host, $user, $password, $dbname, $port, null, $sslFlags);
        if ($ok) {
            $applySession($mysqli);
            return $mysqli;
        }
        if ($sslMode === 'prefer') {
            return $connectPlain();
        }
        $err = $mysqli->connect_error ?: mysqli_connect_error();
        return $fail($dieOnError, 'No podemos conectar a la base de datos (SSL): ' . $err);
    }

    return $connectPlain();
}
