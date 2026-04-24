<?php

require_once __DIR__ . '/../models/ProductoModel.php';
require_once __DIR__ . '/../view/Api.View.php';
require_once __DIR__ . '/../../helpers/JWTAuth.helper.php';
require_once __DIR__ . '/../DTO/producto/productoDTO.php';
require_once __DIR__ . '/../DTO/producto/productoVarianteDTO.php';
require_once __DIR__ . '/../DTO/producto/ProductoCategoriaDTO.php';

class ProductoController {

    private $model;
    private $view;
    private $data;

    function __construct() {
        $this->model = new ProductoModel();
        $this->view = new ApiView();
        $this->data = file_get_contents('php://input');
    }

    function getData() {
        return json_decode($this->data);
    }

    // ==================== GESTIÓN DE PRODUCTOS ====================

    function listar($params = []){
        
        $productos = $this->model->listarProductos();
        
        if($productos){
            $productosDTO = ProductDTO::fromDatabaseList($productos);
            $this->view->response($productosDTO, 200);
        }else{
            $this->view->response('No hay productos disponibles', 404);
        }
    }
    function listarPorNomCategoria($params = []){
        
    }
    function listarPorCategoria($params = []){
        if(!isset($params[':id']) || empty($params[':id'])){
            $this->view->response('El ID de categoría es requerido', 400);
            return;
        }

        $id_categoria = $params[':id'];
        
        if(!$this->model->verificarCategoriaExiste($id_categoria)){
            $this->view->response('La categoría no existe', 404);
            return;
        }

        $productos = $this->model->listarProductosPorCategoria($id_categoria);

        if($productos !== false){
            $productosDTO = ProductoCategoriaDTO::fromDatabaseList($productos);
            $this->view->response($productosDTO, 200);
        }else{
            $this->view->response('Error al obtener productos', 500);
        }
    }

    function obtener($params = []){
        if(!isset($params[':id']) || empty($params[':id'])){
            $this->view->response('El ID del producto es requerido', 400);
            return;
        }

        $id = $params[':id'];
        $producto = $this->model->obtenerProductoConVariantes($id);
        
        if(!$producto){
            $this->view->response('Producto no encontrado', 404);
            return;
        }
        //fromDatabase sin list porque devuelvo un solo producto
        $productosDTO = ProductoVarianteDTO::fromDatabase($producto);
        $this->view->response($productosDTO, 200);
    }

    function crear($params = []){
        $userPayload = JWTAuth::getAuthUser();
        if(!$userPayload || $userPayload->rol !== 'admin'){
            $this->view->response('Acceso denegado. Se requiere rol de administrador.', 403);
            return;
        }

        $data = $this->getData();

        // Validaciones
        if(!isset($data->id_categoria) || empty($data->id_categoria)){
            $this->view->response('El ID de categoría es requerido', 400);
            return;
        }

        if(!isset($data->nombre) || empty($data->nombre)){
            $this->view->response('El nombre es requerido', 400);
            return;
        }

        if(!isset($data->precio_base) || $data->precio_base <= 0){
            $this->view->response('El precio base debe ser mayor a 0', 400);
            return;
        }

        // Validar que la categoría exista
        if(!$this->model->verificarCategoriaExiste($data->id_categoria)){
            $this->view->response('La categoría no existe', 404);
            return;
        }

        $descripcion = isset($data->descripcion) ? $data->descripcion : '';
        $marca = isset($data->marca) ? $data->marca : '';
        $img = isset($data->img) ? $data->img : '';
        $activo = isset($data->activo) ? $data->activo : 1;

        $id_producto = $this->model->insertarProducto(
            $data->id_categoria, 
            $data->nombre, 
            $descripcion, 
            $data->precio_base, 
            $marca, 
            $img, 
            $activo
        );

        if($id_producto){
            $this->view->response(['id_producto' => $id_producto, 'mensaje' => 'Producto creado exitosamente'], 201);
        }else{
            $this->view->response('Error al crear el producto', 500);
        }
    }

    function actualizar($params = []){
        $userPayload = JWTAuth::getAuthUser();
        if(!$userPayload || $userPayload->rol !== 'admin'){
            $this->view->response('Acceso denegado. Se requiere rol de administrador.', 403);
            return;
        }

        if(!isset($params[':id']) || empty($params[':id'])){
            $this->view->response('El ID del producto es requerido', 400);
            return;
        }

        $id = $params[':id'];
        $producto = $this->model->obtenerProducto($id);
        
        if(!$producto){
            $this->view->response('Producto no encontrado', 404);
            return;
        }

        $data = $this->getData();

        // Validaciones
        if(!isset($data->id_categoria) || empty($data->id_categoria)){
            $this->view->response('El ID de categoría es requerido', 400);
            return;
        }

        if(!isset($data->nombre) || empty($data->nombre)){
            $this->view->response('El nombre es requerido', 400);
            return;
        }

        if(!isset($data->precio_base) || $data->precio_base <= 0){
            $this->view->response('El precio base debe ser mayor a 0', 400);
            return;
        }

        // Validar que la categoría exista
        if(!$this->model->verificarCategoriaExiste($data->id_categoria)){
            $this->view->response('La categoría no existe', 404);
            return;
        }

        $descripcion = isset($data->descripcion) ? $data->descripcion : '';
        $marca = isset($data->marca) ? $data->marca : '';
        $img = isset($data->img) ? $data->img : '';
        $activo = isset($data->activo) ? $data->activo : 1;

        if($this->model->actualizarProducto($id, $data->id_categoria, $data->nombre, $descripcion, $data->precio_base, $marca, $img, $activo)){
            $this->view->response('Producto actualizado exitosamente', 200);
        }else{
            $this->view->response('Error al actualizar el producto', 500);
        }
    }

    function desactivar($params = []){
        $userPayload = JWTAuth::getAuthUser();
        if(!$userPayload || $userPayload->rol !== 'admin'){
            $this->view->response('Acceso denegado. Se requiere rol de administrador.', 403);
            return;
        }

        if(!isset($params[':id']) || empty($params[':id'])){
            $this->view->response('El ID del producto es requerido', 400);
            return;
        }

        $id = $params[':id'];
        $producto = $this->model->obtenerProducto($id);
        
        if(!$producto){
            $this->view->response('Producto no encontrado', 404);
            return;
        }

        if($this->model->desactivarProducto($id)){
            $this->view->response('Producto desactivado exitosamente', 200);
        }else{
            $this->view->response('Error al desactivar el producto', 500);
        }
    }

    function eliminar($params = []){
        $userPayload = JWTAuth::getAuthUser();
        if(!$userPayload || $userPayload->rol !== 'admin'){
            $this->view->response('Acceso denegado. Se requiere rol de administrador.', 403);
            return;
        }

        if(!isset($params[':id']) || empty($params[':id'])){
            $this->view->response('El ID del producto es requerido', 400);
            return;
        }

        $id = $params[':id'];
        $producto = $this->model->obtenerProducto($id);
        
        if(!$producto){
            $this->view->response('Producto no encontrado', 404);
            return;
        }

        if($this->model->eliminarProducto($id)){
            $this->view->response('Producto eliminado exitosamente', 200);
        }else{
            $this->view->response('Error al eliminar el producto', 500);
        }
    }

    // ==================== GESTIÓN DE VARIANTES ====================

    function crearVariante($params = []){
        $userPayload = JWTAuth::getAuthUser();
        if(!$userPayload || $userPayload->rol !== 'admin'){
            $this->view->response('Acceso denegado. Se requiere rol de administrador.', 403);
            return;
        }

        if(!isset($params[':id']) || empty($params[':id'])){
            $this->view->response('El ID del producto es requerido', 400);
            return;
        }

        $id_producto = $params[':id'];
        
        if(!$this->model->verificarProductoExiste($id_producto)){
            $this->view->response('El producto no existe', 404);
            return;
        }

        $data = $this->getData();

        if(!isset($data->precio) || $data->precio <= 0){
            $this->view->response('El precio debe ser mayor a 0', 400);
            return;
        }

        if(!isset($data->stock) || $data->stock < 0){
            $this->view->response('El stock no puede ser negativo', 400);
            return;
        }

        $talle = isset($data->talle) ? $data->talle : NULL;
        $color = isset($data->color) ? $data->color : NULL;

        $id_variante = $this->model->insertarVariante($id_producto, $talle, $color, $data->precio, $data->stock);

        if($id_variante){
            $this->view->response(['id_variante' => $id_variante, 'mensaje' => 'Variante creada exitosamente'], 201);
        }else{
            $this->view->response('Error al crear la variante', 500);
        }
    }

    function obtenerVariante($params = []){
        if(!isset($params[':id_variante']) || empty($params[':id_variante'])){
            $this->view->response('El ID de la variante es requerido', 400);
            return;
        }

        $id_variante = $params[':id_variante'];
        $variante = $this->model->obtenerVariante($id_variante);
        
        if(!$variante){
            $this->view->response('Variante no encontrada', 404);
            return;
        }

        $this->view->response($variante, 200);
    }

    function actualizarVariante($params = []){
        $userPayload = JWTAuth::getAuthUser();
        if(!$userPayload || $userPayload->rol !== 'admin'){
            $this->view->response('Acceso denegado. Se requiere rol de administrador.', 403);
            return;
        }

        if(!isset($params[':id_variante']) || empty($params[':id_variante'])){
            $this->view->response('El ID de la variante es requerido', 400);
            return;
        }

        $id_variante = $params[':id_variante'];
        $variante = $this->model->obtenerVariante($id_variante);
        
        if(!$variante){
            $this->view->response('Variante no encontrada', 404);
            return;
        }

        $data = $this->getData();

        if(!isset($data->precio) || $data->precio <= 0){
            $this->view->response('El precio debe ser mayor a 0', 400);
            return;
        }

        if(!isset($data->stock) || $data->stock < 0){
            $this->view->response('El stock no puede ser negativo', 400);
            return;
        }

        $talle = isset($data->talle) ? $data->talle : $variante->talle;
        $color = isset($data->color) ? $data->color : $variante->color;

        if($this->model->actualizarVariante($id_variante, $talle, $color, $data->precio, $data->stock)){
            $this->view->response('Variante actualizada exitosamente', 200);
        }else{
            $this->view->response('Error al actualizar la variante', 500);
        }
    }

    function actualizarStockVariante($params = []){
        $userPayload = JWTAuth::getAuthUser();
        if(!$userPayload || $userPayload->rol !== 'admin'){
            $this->view->response('Acceso denegado. Se requiere rol de administrador.', 403);
            return;
        }

        if(!isset($params[':id_variante']) || empty($params[':id_variante'])){
            $this->view->response('El ID de la variante es requerido', 400);
            return;
        }

        $id_variante = $params[':id_variante'];
        
        if(!$this->model->verificarVarianteExiste($id_variante)){
            $this->view->response('Variante no encontrada', 404);
            return;
        }

        $data = $this->getData();

        if(!isset($data->stock) || $data->stock < 0){
            $this->view->response('El stock no puede ser negativo', 400);
            return;
        }

        if($this->model->actualizarStockVariante($id_variante, $data->stock)){
            $this->view->response('Stock actualizado exitosamente', 200);
        }else{
            $this->view->response('Error al actualizar el stock', 500);
        }
    }

    function eliminarVariante($params = []){
        $userPayload = JWTAuth::getAuthUser();
        if(!$userPayload || $userPayload->rol !== 'admin'){
            $this->view->response('Acceso denegado. Se requiere rol de administrador.', 403);
            return;
        }

        if(!isset($params[':id_variante']) || empty($params[':id_variante'])){
            $this->view->response('El ID de la variante es requerido', 400);
            return;
        }

        $id_variante = $params[':id_variante'];
        
        if(!$this->model->verificarVarianteExiste($id_variante)){
            $this->view->response('Variante no encontrada', 404);
            return;
        }

        if($this->model->eliminarVariante($id_variante)){
            $this->view->response('Variante eliminada exitosamente', 200);
        }else{
            $this->view->response('Error al eliminar la variante', 500);
        }
    }
}
