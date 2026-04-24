<?php

require_once __DIR__ . 'app/models/UsuarioModel.php';
require_once __DIR__ . 'app/view/Api.View.php';
require_once __DIR__ . 'helpers/JWTAuth.helper.php';

class UsuarioController {

    private $model;
    private $view;
    private $data;

    function __construct() {
        $this->model = new UsuarioModel();
        $this->view = new ApiView();
        $this->data = file_get_contents('php://input');
    }

    function getData() {
        return json_decode($this->data);
    }

    function listar($params = []){
        $userPayload = JWTAuth::getAuthUser();
        if(!$userPayload || $userPayload->rol !== 'admin'){
            $this->view->response('Acceso denegado. Se requiere rol de administrador.', 403);
            return;
        }

        $usuarios = $this->model->listarUsuarios();

        if($usuarios){
            $this->view->response($usuarios, 200);
        }else{
            $this->view->response('No hay usuarios disponibles', 404);
        }
    }

    function cambiarRol($params = []){
        $userPayload = JWTAuth::getAuthUser();
        if(!$userPayload || $userPayload->rol !== 'admin'){
            $this->view->response('Acceso denegado. Se requiere rol de administrador.', 403);
            return;
        }

        if(!isset($params[':id']) || empty($params[':id'])){
            $this->view->response('El ID es requerido', 400);
            return;
        }

        $id = $params[':id'];
        $usuario = $this->model->obtenerUsuario($id);
        
        if(!$usuario){
            $this->view->response('Usuario no encontrado', 404);
            return;
        }

        $data = $this->getData();

        if(!isset($data->rol) || empty($data->rol)){
            $this->view->response('El rol es requerido', 400);
            return;
        }

        $rolesValidos = ['admin', 'cliente'];
        if(!in_array($data->rol, $rolesValidos)){
            $this->view->response('El rol no es válido. Roles permitidos: admin, cliente', 400);
            return;
        }

        if($usuario->rol === $data->rol){
            $this->view->response('El usuario ya tiene el rol de ' . $data->rol, 400);
            return;
        }

        if($this->model->actualizarUsuario($id, $usuario->nombre, $usuario->apellido, $usuario->dni, $usuario->email, $data->rol)){
            $this->view->response('Rol actualizado exitosamente a ' . $data->rol, 200);
        }else{
            $this->view->response('Error al actualizar el rol', 500);
        }
    }

    function obtener($params = []){
        $userPayload = JWTAuth::getAuthUser();
        if(!$userPayload || $userPayload->rol !== 'admin'){
            $this->view->response('Acceso denegado. Se requiere rol de administrador.', 403);
            return;
        }

        if(!isset($params[':id']) || empty($params[':id'])){
            $this->view->response('El ID es requerido', 400);
            return;
        }

        $id = $params[':id'];
        $usuario = $this->model->obtenerUsuario($id);
        
        if(!$usuario){
            $this->view->response('Usuario no encontrado', 404);
            return;
        }

        $this->view->response($usuario, 200);
    }


    //aqui cualquiera puede cambiar su propia password
    function cambiarPassword($params = []){
        $userPayload = JWTAuth::getAuthUser();
        if(!$userPayload){
            $this->view->response('Acceso denegado.', 403);
            return;
        }

        $id = $params[':id'] ?? null;

        // Un cliente solo puede cambiar su propia contraseña, un admin puede cambiar la de cualquiera
        if($userPayload->rol !== 'admin' && $userPayload->id_usuario != $id){
            $this->view->response('Acceso denegado. Solo puedes cambiar tu propia contraseña.', 403);
            return;
        }

        if(!isset($params[':id']) || empty($params[':id'])){
            $this->view->response('El ID es requerido', 400);
            return;
        }

        $id = $params[':id'];
        $usuario = $this->model->obtenerUsuario($id);
        
        if(!$usuario){
            $this->view->response('Usuario no encontrado', 404);
            return;
        }

        $data = $this->getData();

        if(!isset($data->passwordActual) || empty($data->passwordActual)){
            $this->view->response('La contraseña actual es requerida', 400);
            return;
        }

        if(!isset($data->passwordNueva) || empty($data->passwordNueva)){
            $this->view->response('La contraseña nueva es requerida', 400);
            return;
        }

        // Verificar contraseña actual
        if(!$this->model->verificarPassword($id, $data->passwordActual)){
            $this->view->response('La contraseña actual es incorrecta', 401);
            return;
        }

        if($this->model->actualizarPassword($id, $data->passwordNueva)){
            $this->view->response('Contraseña actualizada exitosamente', 200);
        }else{
            $this->view->response('Error al actualizar la contraseña', 500);
        }
    }

    function eliminar($params = []){
        $userPayload = JWTAuth::getAuthUser();
        if(!$userPayload || $userPayload->rol !== 'admin'){
            $this->view->response('Acceso denegado. Se requiere rol de administrador.', 403);
            return;
        }

        if(!isset($params[':id']) || empty($params[':id'])){
            $this->view->response('El ID es requerido', 400);
            return;
        }

        $id = $params[':id'];
        $usuario = $this->model->obtenerUsuario($id);
        
        if(!$usuario){
            $this->view->response('Usuario no encontrado', 404);
            return;
        }

        if($this->model->eliminarUsuario($id)){
            $this->view->response('Usuario eliminado exitosamente', 200);
        }else{
            $this->view->response('Error al eliminar el usuario', 500);
        }
    }
}
