<?php
// index.php - Entry point mínimo para Railway
// Si este archivo responde, el problema está en router-api.php o Router.php

// Headers CORS básicos
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// Leer el recurso desde query param
$resource = $_GET['resource'] ?? '';

// ✅ Ruta de health SIN depender del Router
if ($resource === 'health') {
    http_response_code(200);
    echo json_encode([
        'status' => 'ok',
        'php_version' => phpversion(),
        'timestamp' => time(),
        'debug' => [
            'resource' => $resource,
            'get' => $_GET,
            'server_port' => $_SERVER['SERVER_PORT'] ?? 'unknown'
        ]
    ]);
    exit;
}

// Para cualquier otra ruta, delegar al router original
require_once __DIR__ . '/router-api.php';