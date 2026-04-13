<?php
class LoginDTO {

    public static function fromDatabase($usuario, $token): array {
        if (!$usuario) {
            return [];
        }

        return [
            'mensaje' => 'Login exitoso',
            'token' => $token,
            'usuario' => [
                'id_usuario' => $usuario->id_usuario,
                'nombre' => $usuario->nombre,
                'apellido' => $usuario->apellido,
                'email' => $usuario->email,
                'dni' => $usuario->dni,
                'rol' => $usuario->rol,
                'creado' => $usuario->creado
            ]
        ];
    }
}
