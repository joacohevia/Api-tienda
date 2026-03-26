<?php

class JWTAuth {

    /**
     * Codifica datos en Base64 URL-safe (sin padding)
     */
    private static function base64UrlEncode($data) {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    /**
     * Decodifica Base64 URL-safe
     */
    private static function base64UrlDecode($data) {
        return base64_decode(strtr($data, '-_', '+/'));
    }

    /**
     * Crea un token JWT
     * @param array $payload Datos a incluir en el token
     * @return string Token JWT
     */
    public static function createToken($payload) {
        $header = json_encode(['alg' => 'HS256', 'typ' => 'JWT']);

        // Agregar tiempos de emisión y expiración
        $payload['iat'] = time();
        $payload['exp'] = time() + JWT_EXPIRATION;

        $headerEncoded = self::base64UrlEncode($header);
        $payloadEncoded = self::base64UrlEncode(json_encode($payload));

        $signature = hash_hmac('sha256', "$headerEncoded.$payloadEncoded", JWT_SECRET, true);
        $signatureEncoded = self::base64UrlEncode($signature);

        return "$headerEncoded.$payloadEncoded.$signatureEncoded";
    }

    /**
     * Verifica y decodifica un token JWT
     * @param string $token Token JWT a verificar
     * @return object|false Payload decodificado o false si es inválido
     */
    public static function verifyToken($token) {
        $parts = explode('.', $token);

        if (count($parts) !== 3) {
            return false;
        }

        [$headerEncoded, $payloadEncoded, $signatureEncoded] = $parts;

        // Verificar firma
        $expectedSignature = self::base64UrlEncode(
            hash_hmac('sha256', "$headerEncoded.$payloadEncoded", JWT_SECRET, true)
        );

        if (!hash_equals($expectedSignature, $signatureEncoded)) {
            return false;
        }

        $payload = json_decode(self::base64UrlDecode($payloadEncoded));

        if (!$payload) {
            return false;
        }

        // Verificar expiración
        if (isset($payload->exp) && $payload->exp < time()) {
            return false;
        }

        return $payload;
    }

    /**
     * Obtiene el token del header Authorization
     * @return string|null Token o null si no existe
     */
    public static function getTokenFromHeader() {
        $headers = '';

        if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
            $headers = $_SERVER['HTTP_AUTHORIZATION'];
        } elseif (isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
            $headers = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
        } elseif (function_exists('apache_request_headers')) {
            $requestHeaders = apache_request_headers();
            if (isset($requestHeaders['Authorization'])) {
                $headers = $requestHeaders['Authorization'];
            }
        }

        if (!empty($headers) && preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
            return $matches[1];
        }

        return null;
    }

    /**
     * Obtiene el payload del usuario autenticado desde el header
     * @return object|false Payload del usuario o false
     */
    public static function getAuthUser() {
        $token = self::getTokenFromHeader();

        if (!$token) {
            return false;
        }

        return self::verifyToken($token);
    }
}
