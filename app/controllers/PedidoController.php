<?php

require_once 'app/models/PedidoModel.php';
require_once 'app/view/Api.View.php';
require_once 'helpers/JWTAuth.helper.php';
require_once 'app/models/UsuarioModel.php';
require_once 'app/DTO/pedido/PedidoDTO.php';
require_once 'app/DTO/pedido/PedidoDetalleDTO.php';
require_once 'app/DTO/pedido/PedidoUsuarioDTO.php';
require_once 'app/DTO/pedido/PedidoProductoDTO.php';

class PedidoController {

    private $model;
    private $view;
    private $data;
    private $usuarioModel;

    function __construct() {
        $this->model = new PedidoModel();
        $this->usuarioModel = new UsuarioModel();
        $this->view = new ApiView();
        $this->data = file_get_contents('php://input');
    }

    function getData() {
        return json_decode($this->data);
    }

    // ==================== GESTIÓN DE PEDIDOS ====================

    function listar($params = []){
        $userPayload = JWTAuth::getAuthUser();
        if(!$userPayload || $userPayload->rol !== 'admin'){
            $this->view->response('Acceso denegado. Se requiere rol de administrador.', 403);
            return;
        }

        $pedidos = $this->model->listarPedidos();

        if($pedidos){
            $this->view->response($pedidos, 200);
        }else{
            $this->view->response('No hay pedidos disponibles', 404);
        }
    }

    function listarPorUsuario($params = []){
        if(!isset($params[':id']) || empty($params[':id'])){
            $this->view->response('El ID de usuario es requerido', 400);
            return;
        }

        $id_usuario = $params[':id'];
        $pedidos = $this->model->listarPedidosPorUsuario($id_usuario);

        if($pedidos !== false){
            $pedidosDTO = PedidoUsuarioDTO::fromDatabaseList($pedidos);
            $this->view->response($pedidosDTO, 200);
        }else{
            $this->view->response('Error al obtener pedidos', 500);
        }
    }

    function obtener($params = []){
        if(!isset($params[':id']) || empty($params[':id'])){
            $this->view->response('El ID del pedido es requerido', 400);
            return;
        }

        $id = $params[':id'];
        $pedido = $this->model->obtenerDetallesPedido($id);
        
        if(!$pedido){
            $this->view->response('Pedido no encontrado', 404);
            return;
        }

        $pedidoDTO = PedidoDetalleDTO::fromDatabase($pedido);
        $this->view->response($pedidoDTO, 200);
    }

    function crear($params = []){
        $data = $this->getData();

        if(!isset($data->id_usuario) || empty($data->id_usuario)){
            $this->view->response('El ID de usuario es requerido', 400);
            return;
        }

        // Verificar que el usuario existe
        $usuario = $this->usuarioModel->obtenerUsuario($data->id_usuario);
        if(!$usuario){
            $this->view->response('El usuario no existe', 404);
            return;
        }

        $estado = isset($data->estado) ? $data->estado : 'carrito';

        // Validar que el estado sea válido
        $estadosValidos = ['carrito', 'pendiente', 'pagado', 'enviado', 'cancelado'];
        if(!in_array($estado, $estadosValidos)){
            $this->view->response('El estado no es válido', 400);
            return;
        }

        $id_pedido = $this->model->crearPedido($data->id_usuario, $estado);
        
        if($id_pedido){
            $respuesta = PedidoProductoDTO::fromCrear($id_pedido);
            $this->view->response($respuesta, 201);
        }else{
            $this->view->response('Error al crear el pedido', 500);
        }
    }

    function actualizarEstado($params = []){
        $userPayload = JWTAuth::getAuthUser();
        if(!$userPayload || $userPayload->rol !== 'admin'){
            $this->view->response('Acceso denegado. Se requiere rol de administrador.', 403);
            return;
        }

        if(!isset($params[':id']) || empty($params[':id'])){
            $this->view->response('El ID del pedido es requerido', 400);
            return;
        }

        $id = $params[':id'];
        $pedido = $this->model->obtenerPedido($id);
        
        if(!$pedido){
            $this->view->response('Pedido no encontrado', 404);
            return;
        }

        $data = $this->getData();

        if(!isset($data->estado) || empty($data->estado)){
            $this->view->response('El estado es requerido', 400);
            return;
        }

        // Validar que el estado sea válido
        $estadosValidos = ['carrito', 'pendiente', 'pagado', 'enviado', 'cancelado'];
        if(!in_array($data->estado, $estadosValidos)){
            $this->view->response('El estado no es válido', 400);
            return;
        }

        if($this->model->actualizarEstadoPedido($id, $data->estado)){
            $this->view->response('Estado del pedido actualizado exitosamente', 200);
        }else{
            $this->view->response('Error al actualizar el pedido', 500);
        }
    }

    function eliminar($params = []){
        

        if(!isset($params[':id']) || empty($params[':id'])){
            $this->view->response('El ID del pedido es requerido', 400);
            return;
        }

        $id = $params[':id'];
        $pedido = $this->model->obtenerPedido($id);
        
        if(!$pedido){
            $this->view->response('Pedido no encontrado', 404);
            return;
        }

        if($this->model->eliminarPedido($id)){
            $this->view->response('Pedido eliminado exitosamente', 200);
        }else{
            $this->view->response('Error al eliminar el pedido', 500);
        }
    }

    // ==================== GESTIÓN DE PRODUCTOS EN PEDIDOS ====================

    function agregarProducto($params = []){
        if(!isset($params[':id']) || empty($params[':id'])){
            $this->view->response('El ID del pedido es requerido', 400);
            return;
        }

        $id_pedido = $params[':id'];
        $pedido = $this->model->obtenerPedido($id_pedido);
        
        if(!$pedido){
            $this->view->response('Pedido no encontrado', 404);
            return;
        }

        $data = $this->getData();

        if(!isset($data->id_variante) || empty($data->id_variante)){
            $this->view->response('El ID de variante es requerido', 400);
            return;
        }

        if(!isset($data->cantidad) || $data->cantidad <= 0){
            $this->view->response('La cantidad debe ser mayor a 0', 400);
            return;
        }

        if(!isset($data->precio_unitario) || $data->precio_unitario <= 0){
            $this->view->response('El precio unitario debe ser mayor a 0', 400);
            return;
        }

        // Validar que la variante exista
        if(!$this->model->verificarVarianteExiste($data->id_variante)){
            $this->view->response('La variante no existe', 404);
            return;
        }

        // Validar stock disponible
        if(!$this->model->verificarStockDisponible($data->id_variante, $data->cantidad)){
            $this->view->response('Stock insuficiente para esta variante', 400);
            return;
        }

        if($this->model->agregarProductoAlPedido($id_pedido, $data->id_variante, $data->cantidad, $data->precio_unitario)){
            // Recalcular total
            $total = $this->model->calcularTotalPedido($id_pedido);
            $this->model->actualizarTotalPedido($id_pedido, $total);
            
            $respuesta = PedidoProductoDTO::fromAgregarProducto($total);
            $this->view->response($respuesta, 201);
        }else{
            $this->view->response('Error al agregar el producto', 500);
        }
    }

    function actualizarProducto($params = []){
        if(!isset($params[':id_pedido_producto']) || empty($params[':id_pedido_producto'])){
            $this->view->response('El ID del producto en el pedido es requerido', 400);
            return;
        }

        $id_pedido_producto = $params[':id_pedido_producto'];
        $producto = $this->model->obtenerProductoDelPedido($id_pedido_producto);
        
        if(!$producto){
            $this->view->response('Producto del pedido no encontrado', 404);
            return;
        }

        $data = $this->getData();

        if(!isset($data->cantidad) || $data->cantidad < 0){
            $this->view->response('La cantidad debe ser mayor o igual a 0', 400);
            return;
        }

        if($this->model->actualizarProductoEnPedido($id_pedido_producto, $data->cantidad)){
            // Recalcular total del pedido
            $total = $this->model->calcularTotalPedido($producto->id_pedido);
            $this->model->actualizarTotalPedido($producto->id_pedido, $total);
            
            $this->view->response(['mensaje' => 'Producto del pedido actualizado exitosamente', 'nuevo_total' => $total], 200);
        }else{
            $this->view->response('Error al actualizar el producto', 500);
        }
    }

    function eliminarProducto($params = []){
        if(!isset($params[':id_pedido_producto']) || empty($params[':id_pedido_producto'])){
            $this->view->response('El ID del producto en el pedido es requerido', 400);
            return;
        }

        $id_pedido_producto = $params[':id_pedido_producto'];
        $producto = $this->model->obtenerProductoDelPedido($id_pedido_producto);
        
        if(!$producto){
            $this->view->response('Producto del pedido no encontrado', 404);
            return;
        }

        if($this->model->eliminarProductoDelPedido($id_pedido_producto)){
            // Recalcular total del pedido
            $total = $this->model->calcularTotalPedido($producto->id_pedido);
            $this->model->actualizarTotalPedido($producto->id_pedido, $total);
            
            $this->view->response(['mensaje' => 'Producto eliminado del pedido exitosamente', 'nuevo_total' => $total], 200);
        }else{
            $this->view->response('Error al eliminar el producto', 500);
        }
    }
    //DESCONTAR STOCK
    function actualizarStock($params = []){
        if(!isset($params[':id_variante']) || empty($params[':id_variante'])){
            $this->view->response('El ID de la variante es requerido', 400);
            return;
        }

        $id_variante = $params[':id_variante'];
        $data = $this->getData();

        if(!isset($data->cantidad) || $data->cantidad <= 0){
            $this->view->response('La cantidad a descontar debe ser mayor a 0', 400);
            return;
        }

        // Obtener stock actual
        $variante = $this->model->obtenerVariante($id_variante);
        if(!$variante){
            $this->view->response('Variante no encontrada', 404);
            return;
        }

        // Verificar que hay stock suficiente
        if($variante->stock < $data->cantidad){
            $this->view->response('Stock insuficiente. Stock disponible: ' . $variante->stock, 400);
            return;
        }

        // Descontar stock (restar)
        if($this->model->descontarStockVariante($id_variante, $data->cantidad)){
            $this->view->response(['mensaje' => 'Stock descontado exitosamente', 'stock_restante' => $variante->stock - $data->cantidad], 200);
        }else{
            $this->view->response('Error al descontar stock', 500);
        }
    }
}
