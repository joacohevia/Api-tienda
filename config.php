<?php
// config.php - Compatible con Docker local + Railway

// Helper para leer variables de entorno (prioriza getenv, luego $_ENV, luego $_SERVER)
function env($key, $default = null) {
    $value = getenv($key);
    if ($value === false) {
        $value = $_ENV[$key] ?? $_SERVER[$key] ?? $default;
    }
    return $value;
}

// Configuración de MySQL
define('MYSQL_USER', env('MYSQL_USER', 'root'));
define('MYSQL_PASS', env('MYSQL_PASSWORD', 'root')); // 👈 Nota: 'MYSQL_PASSWORD', no 'MYSQL_PASS'
define('MYSQL_DB',   env('MYSQL_DATABASE', 'tiendaropa'));
define('MYSQL_HOST', env('MYSQL_HOST', '127.0.0.1')); // 👈 Default para Railway
define('MYSQL_PORT', env('MYSQL_PORT', '3306'));

// JWT Configuration
define('JWT_SECRET', env('JWT_SECRET', 'cAmBiAr_3st4_cLaV3_S3cr3tA_en_pr0ducc10n'));
define('JWT_EXPIRATION', (int) env('JWT_EXPIRATION', 3600));