<?php

require_once 'app/models/UsuarioModel.php';
require_once 'app/service/AuthService.php';
require_once 'app/view/Api.View.php';
require_once 'helpers/JWTAuth.helper.php';
require_once 'app/DTO/auth/LoginDTO.php';
require_once 'app/DTO/auth/RegistroDTO.php';

class AuthController {

    private $authService;
    private $usuarioModel;
    private $view;
    private $data;

    function __construct() {
        $this->authService = new AuthService();
        $this->usuarioModel = new UsuarioModel();
        $this->view = new ApiView();
        $this->data = file_get_contents('php://input');
    }

    function getData() {
        return json_decode($this->data);
    }

    /**
     * Autentica un usuario con email y contraseña, retorna JWT
     */
    function login($params = []){
        $data = $this->getData();

        // Validaciones
        if(!isset($data->email) || empty($data->email)){
            $this->view->response('El email es requerido', 400);
            return;
        }

        if(!isset($data->password) || empty($data->password)){
            $this->view->response('La contraseña es requerida', 400);
            return;
        }

        // Validar formato de email
        if(!filter_var($data->email, FILTER_VALIDATE_EMAIL)){
            $this->view->response('El formato del email no es válido', 400);
            return;
        }

        // Intentar autenticar
        $usuario = $this->authService->login($data->email, $data->password);

        if(!$usuario){
            $this->view->response('Email o contraseña incorrectos', 401);
            return;
        }

        // Generar token JWT
        $token = $this->authService->generarToken($usuario);

        // Responder con el token y datos del usuario
        $respuesta = LoginDTO::fromDatabase($usuario, $token);
        $this->view->response($respuesta, 200);
    }

    /**
     * Registra un nuevo usuario
     */
    function registro($params = []){
        $data = $this->getData();

        // Validaciones básicas
        if(!isset($data->nombre) || empty($data->nombre)){
            $this->view->response('El nombre es requerido', 400);
            return;
        }

        if(!isset($data->apellido) || empty($data->apellido)){
            $this->view->response('El apellido es requerido', 400);
            return;
        }

        if(!isset($data->dni) || empty($data->dni)){
            $this->view->response('El DNI es requerido', 400);
            return;
        }

        if(!isset($data->email) || empty($data->email)){
            $this->view->response('El email es requerido', 400);
            return;
        }

        if(!isset($data->password) || empty($data->password)){
            $this->view->response('La contraseña es requerida', 400);
            return;
        }

        // Validar DNI
        if(!is_numeric($data->dni) || strlen((string)$data->dni) > 10){
            $this->view->response('El DNI no es válido', 400);
            return;
        }

        // Validar email
        if(!filter_var($data->email, FILTER_VALIDATE_EMAIL)){
            $this->view->response('El formato del email no es válido', 400);
            return;
        }

        // Validar contraseña mínimo 6 caracteres
        if(strlen($data->password) < 6){
            $this->view->response('La contraseña debe tener mínimo 6 caracteres', 400);
            return;
        }

        // Verificar que DNI no exista
        $usuarioDni = $this->usuarioModel->obtenerPorDni($data->dni);
        if($usuarioDni){
            $this->view->response('El DNI ya está registrado', 409);
            return;
        }

        // Verificar que email no exista
        $usuarioEmail = $this->usuarioModel->obtenerPorEmail($data->email);
        if($usuarioEmail){
            $this->view->response('El email ya está registrado', 409);
            return;
        }

        // Crear el usuario (siempre como cliente)
        if($this->usuarioModel->insertarUsuario(
            $data->nombre, 
            $data->apellido, 
            $data->dni, 
            $data->email, 
            $data->password, 
            'cliente'
        )){
            $usuario = $this->usuarioModel->obtenerPorEmail($data->email);
            $respuesta = RegistroDTO::fromDatabase($usuario);
            $this->view->response($respuesta, 201);
        }else{
            $this->view->response('Error al registrar el usuario', 500);
        }
    }

    /**
     * Obtiene los datos del usuario actual autenticado (requiere JWT)
     */
    function perfil($params = []){
        $userPayload = JWTAuth::getAuthUser();
        if(!$userPayload){
            $this->view->response('Token inválido o expirado', 401);
            return;
        }

        $usuario = $this->usuarioModel->obtenerUsuario($userPayload->id_usuario);

        if(!$usuario){
            $this->view->response('Usuario no encontrado', 404);
            return;
        }

        $respuesta = [
            'id_usuario' => $usuario->id_usuario,
            'nombre' => $usuario->nombre,
            'apellido' => $usuario->apellido,
            'email' => $usuario->email,
            'dni' => $usuario->dni,
            'rol' => $usuario->rol,
            'creado' => $usuario->creado
        ];

        $this->view->response($respuesta, 200);
    }

    /**
     * Actualiza el perfil del usuario autenticado (requiere JWT)
     */
    function actualizarPerfil($params = []){
        $userPayload = JWTAuth::getAuthUser();
        if(!$userPayload){
            $this->view->response('Token inválido o expirado', 401);
            return;
        }

        $id_usuario = $userPayload->id_usuario;
        $usuario = $this->usuarioModel->obtenerUsuario($id_usuario);

        if(!$usuario){
            $this->view->response('Usuario no encontrado', 404);
            return;
        }

        $data = $this->getData();

        // Validaciones
        if(!isset($data->nombre) || empty($data->nombre)){
            $this->view->response('El nombre es requerido', 400);
            return;
        }

        if(!isset($data->apellido) || empty($data->apellido)){
            $this->view->response('El apellido es requerido', 400);
            return;
        }
        if(!isset($data->dni) || empty($data->dni)){
            $this->view->response('El dni es requerido', 400);
            return;
        }

        if(!isset($data->email) || empty($data->email)){
            $this->view->response('El email es requerido', 400);
            return;
        }

        // Validar formato de email
        if(!filter_var($data->email, FILTER_VALIDATE_EMAIL)){
            $this->view->response('El formato del email no es válido', 400);
            return;
        }

        // Validar que el email no esté en uso por otro usuario
        if($data->email != $usuario->email){
            $usuarioEmail = $this->usuarioModel->obtenerPorEmail($data->email);
            if($usuarioEmail){
                $this->view->response('El email ya está en uso', 409);
                return;
            }
        }

        $rol = $usuario->rol; // El rol no puede ser cambiado por el usuario

        if($this->usuarioModel->actualizarUsuario($id_usuario, $data->nombre, $data->apellido, $data->dni, $data->email, $rol)){
            $this->view->response('Perfil actualizado exitosamente', 200);
        }else{
            $this->view->response('Error al actualizar el perfil', 500);
        }
    }

    /**
     * Verifica el estado de autenticación actual (requiere JWT)
     */
    function verificar($params = []){
        $userPayload = JWTAuth::getAuthUser();
        if($userPayload){
            $usuario = [
                'id_usuario' => $userPayload->id_usuario,
                'email' => $userPayload->email,
                'rol' => $userPayload->rol
            ];
            $this->view->response(['autenticado' => true, 'usuario' => $usuario], 200);
        }else{
            $this->view->response(['autenticado' => false], 200);
        }
    }

    /**
     * Cierra la sesión del usuario (el cliente debe eliminar el token)
     */
    function logout($params = []){
        $this->view->response(['mensaje' => 'Sesión cerrada exitosamente. Elimine el token del lado del cliente.'], 200);
    }
}
