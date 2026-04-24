<?php
class HealthController {
    public function check() {
        // Esta ruta NO usa DB, solo verifica que PHP esté vivo
        http_response_code(200);
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'ok',
            'php_version' => phpversion(),
            'timestamp' => time()
        ]);
        exit; // Importante: terminar aquí para que no siga ejecutando código
    }
}