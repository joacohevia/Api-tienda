<?php

require_once 'app/models/CategoriaModel.php';
require_once 'app/view/Api.View.php';
require_once 'helpers/JWTAuth.helper.php';

class CategoriaController {

    private $model;
    private $view;
    private $data;

    function __construct() {
        $this->model = new CategoriaModel();
        $this->view = new ApiView();
        $this->data = file_get_contents('php://input');//lee lo que ingrese el usuario
            //esto manda un arr
    }
    function getData() {//hace un json con los ingreso el usuario
            return json_decode($this->data);
    }

    function listar($params = []){

        $categorias = $this->model->listarCategorias();

        if($categorias){
            $this->view->response($categorias,200);
        }else{
            $this->view->response(
            'No hay categorías disponibles',404);
        }
    }
    function obtener($params = []){
        if(!isset($params[':id']) || empty($params[':id'])){
            $this->view->response('El ID es requerido', 400);
            return;
        }

        $id = $params[':id'];
        $usuario = $this->model->obtenerCategoria($id);
        
        if(!$usuario){
            $this->view->response('Categoria no encontrado', 404);
            return;
        }

        $this->view->response($usuario, 200);
    }

    function crear($params = []){
        $userPayload = JWTAuth::getAuthUser();
        if(!$userPayload || $userPayload->rol !== 'admin'){
            $this->view->response('Acceso denegado. Se requiere rol de administrador.', 403);
            return;
        }

        $data = $this->getData();
        
        if(!isset($data->nombre) || empty($data->nombre)){
            $this->view->response('El nombre es requerido', 400);
            return;
        }

        $descripcion = isset($data->descripcion) ? $data->descripcion : '';
        
        if($this->model->insertarCategoria($data->nombre, $descripcion)){
            $this->view->response('Categoría creada exitosamente', 201);
        }else{
            $this->view->response('Error al crear la categoría', 500);
        }
    }

    function actualizar($params = []){
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
        $categoria = $this->model->obtenerCategoria($id);
        
        if(!$categoria){
            $this->view->response('Categoría no encontrada', 404);
            return;
        }

        $data = $this->getData();
        
        if(!isset($data->nombre) || empty($data->nombre)){
            $this->view->response('El nombre es requerido', 400);
            return;
        }

        $descripcion = isset($data->descripcion) ? $data->descripcion : '';
        
        if($this->model->actualizarCategoria($id, $data->nombre, $descripcion)){
            $this->view->response('Categoría actualizada exitosamente', 200);
        }else{
            $this->view->response('Error al actualizar la categoría', 500);
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
        $categoria = $this->model->obtenerCategoria($id);
        
        if(!$categoria){
            $this->view->response('Categoría no encontrada', 404);
            return;
        }

        if($this->model->eliminarCategoria($id)){
            $this->view->response('Categoría eliminada exitosamente', 200);
        }else{
            $this->view->response('Error al eliminar la categoría', 500);
        }
    }
}