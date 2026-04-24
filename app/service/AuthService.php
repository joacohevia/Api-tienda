<?php
require_once __DIR__ . 'app/models/UsuarioModel.php';
require_once __DIR__ . 'helpers/JWTAuth.helper.php';

class AuthService extends UsuarioModel{

    private $usuarioModel;

    public function __construct(){
        $this->usuarioModel = new UsuarioModel();
    }

    /**
     * Verifica las credenciales del usuario
     * @param string $email Email del usuario
     * @param string $password Contraseña sin encriptar
     * @return object|false Usuario si es válido, false si no
     */
    public function login($email, $password){
        
        if(empty($email) || empty($password)){
            return false;
        }

        $usuario = $this->usuarioModel->obtenerPorEmail($email);

        if(!$usuario){
            return false;
        }

        // Obtener password hasheada de la BD
        if(!$this->usuarioModel->verificarPassword($usuario->id_usuario, $password)){
            return false;
        }

        return $usuario;
    }

    /**
     * Genera un token JWT para el usuario autenticado
     * @param object $usuario Usuario autenticado
     * @return string Token JWT
     */
    public function generarToken($usuario){
        $payload = [
            'id_usuario' => $usuario->id_usuario,
            'email' => $usuario->email,
            'rol' => $usuario->rol
        ];
        return JWTAuth::createToken($payload);
    }

    /**
     * Verifica un token JWT y retorna el payload
     * @param string $token Token JWT a verificar
     * @return object|false Payload decodificado o false
     */
    public function verificarToken($token){
        return JWTAuth::verifyToken($token);
    }

    /**
     * Obtiene el usuario actual desde el token JWT en el header
     * @return object|null Datos del usuario o null
     */
    public function obtenerUsuarioActual(){
        $payload = JWTAuth::getAuthUser();
        
        if($payload && isset($payload->id_usuario)){
            return $this->usuarioModel->obtenerUsuario($payload->id_usuario);
        }
        return null;
    }
}
