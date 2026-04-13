<?php
class RegistroDTO {

    public static function fromDatabase($usuario): array {
        if (!$usuario) {
            return [];
        }

        return [
            'mensaje' => 'Usuario registrado exitosamente. Por favor inicia sesión.',
            'usuario' => [
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
