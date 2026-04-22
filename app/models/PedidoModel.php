<?php
require_once 'app/models/model.php';

class PedidoModel extends Model{

    // ==================== MÉTODOS DE PEDIDOS ====================
    
    function listarPedidos(){
        $query = $this->db->prepare("
            SELECT 
                p.id_pedido,
                p.id_usuario,
                u.nombre,
                u.apellido,
                u.email,
                p.fecha,
                p.estado,
                p.total
            FROM pedidos p
            INNER JOIN usuarios u ON p.id_usuario = u.id_usuario
            ORDER BY p.fecha DESC
        ");
        $query->execute();
        return $query->fetchAll(PDO::FETCH_OBJ);
    }

    function listarPedidosPorUsuario($id_usuario){
        $query = $this->db->prepare("
            SELECT 
                p.id_pedido,
                p.id_usuario,
                p.fecha,
                p.estado,
                p.total
            FROM pedidos p
            WHERE p.id_usuario = ?
            ORDER BY p.fecha DESC
        ");
        $query->execute([$id_usuario]);
        return $query->fetchAll(PDO::FETCH_OBJ);
    }

    function obtenerPedido($id){
        $query = $this->db->prepare("
            SELECT 
                p.id_pedido,
                p.id_usuario,
                u.nombre,
                u.apellido,
                u.email,
                p.fecha,
                p.estado,
                p.total
            FROM pedidos p
            INNER JOIN usuarios u ON p.id_usuario = u.id_usuario
            WHERE p.id_pedido = ?
        ");
        $query->execute([$id]);
        return $query->fetch(PDO::FETCH_OBJ);
    }

    function obtenerDetallesPedido($id){
        $pedido = $this->obtenerPedido($id);
        
        if(!$pedido){
            return null;
        }

        // Obtener productos del pedido
        $query = $this->db->prepare("
            SELECT 
                pp.id_pedido_producto,
                pp.id_variante,
                pp.cantidad,
                pp.precio_unitario,
                pr.id_producto,
                pr.nombre AS producto_nombre,
                pr.descripcion AS producto_descripcion,
                vp.talle,
                vp.color,
                vp.precio
            FROM pedido_productos pp
            INNER JOIN variante_productos vp ON pp.id_variante = vp.id_variante
            INNER JOIN productos pr ON vp.id_producto = pr.id_producto
            WHERE pp.id_pedido = ?
        ");
        $query->execute([$id]);
        $productos = $query->fetchAll(PDO::FETCH_OBJ);

        $pedido->productos = $productos;
        return $pedido;
    }

    function crearPedido($id_usuario, $estado = 'carrito'){
        $query = $this->db->prepare("
            INSERT INTO pedidos (id_usuario, estado, total) 
            VALUES (?, ?, 0)
        ");
        if($query->execute([$id_usuario, $estado])){
            return $this->db->lastInsertId();
        }
        return false;
    }

    function actualizarEstadoPedido($id, $estado){
        $query = $this->db->prepare("
            UPDATE pedidos 
            SET estado = ? 
            WHERE id_pedido = ?
        ");
        return $query->execute([$estado, $id]);
    }

    function actualizarTotalPedido($id, $total){
        $query = $this->db->prepare("
            UPDATE pedidos 
            SET total = ? 
            WHERE id_pedido = ?
        ");
        return $query->execute([$total, $id]);
    }

    function calcularTotalPedido($id){
        $query = $this->db->prepare("
            SELECT SUM(pp.cantidad * pp.precio_unitario) as total
            FROM pedido_productos pp
            WHERE pp.id_pedido = ?
        ");
        $query->execute([$id]);
        $result = $query->fetch(PDO::FETCH_OBJ);
        return $result->total ?? 0;
    }

    function eliminarPedido($id){
        // Primero eliminar los productos del pedido
        $query = $this->db->prepare("DELETE FROM pedido_productos WHERE id_pedido = ?");
        $query->execute([$id]);

        // Luego eliminar el pedido
        $query = $this->db->prepare("DELETE FROM pedidos WHERE id_pedido = ?");
        return $query->execute([$id]);
    }

    // ==================== MÉTODOS DE DETALLE PEDIDOS (PRODUCTOS) ====================

    function agregarProductoAlPedido($id_pedido, $id_variante, $cantidad, $precio_unitario){
        // Verificar si el producto ya existe en el pedido
        $query = $this->db->prepare("
            SELECT id_pedido_producto, cantidad 
            FROM pedido_productos 
            WHERE id_pedido = ? AND id_variante = ?
        ");
        $query->execute([$id_pedido, $id_variante]);
        $existente = $query->fetch(PDO::FETCH_OBJ);

        if($existente){
            // Actualizar cantidad
            $nuevaCantidad = $existente->cantidad + $cantidad;
            return $this->actualizarProductoEnPedido($existente->id_pedido_producto, $nuevaCantidad);
        }else{
            // Insertar nuevo
            $query = $this->db->prepare("
                INSERT INTO pedido_productos (id_pedido, id_variante, cantidad, precio_unitario)
                VALUES (?, ?, ?, ?)
            ");
            return $query->execute([$id_pedido, $id_variante, $cantidad, $precio_unitario]);
        }
    }

    function actualizarProductoEnPedido($id_pedido_producto, $cantidad){
        if($cantidad <= 0){
            return $this->eliminarProductoDelPedido($id_pedido_producto);
        }

        $query = $this->db->prepare("
            UPDATE pedido_productos 
            SET cantidad = ? 
            WHERE id_pedido_producto = ?
        ");
        return $query->execute([$cantidad, $id_pedido_producto]);
    }

    function eliminarProductoDelPedido($id_pedido_producto){
        $query = $this->db->prepare("
            DELETE FROM pedido_productos 
            WHERE id_pedido_producto = ?
        ");
        return $query->execute([$id_pedido_producto]);
    }

    function obtenerProductoDelPedido($id_pedido_producto){
        $query = $this->db->prepare("
            SELECT * FROM pedido_productos 
            WHERE id_pedido_producto = ?
        ");
        $query->execute([$id_pedido_producto]);
        return $query->fetch(PDO::FETCH_OBJ);
    }

    function obtenerProductosPedido($id_pedido){
        $query = $this->db->prepare("
            SELECT 
                pp.id_pedido_producto,
                pp.id_variante,
                pp.cantidad,
                pp.precio_unitario,
                pr.id_producto,
                pr.nombre AS producto_nombre,
                vp.talle,
                vp.color,
                vp.precio
            FROM pedido_productos pp
            INNER JOIN variante_productos vp ON pp.id_variante = vp.id_variante
            INNER JOIN productos pr ON vp.id_producto = pr.id_producto
            WHERE pp.id_pedido = ?
        ");
        $query->execute([$id_pedido]);
        return $query->fetchAll(PDO::FETCH_OBJ);
    }
    function descontarStockVariante($id_variante, $cantidad){
        $query = $this->db->prepare("UPDATE variante_productos SET stock = stock - ? WHERE id_variante = ?");
        return $query->execute([$cantidad, $id_variante]);
    }

    function obtenerVariante($id_variante){
        $query = $this->db->prepare("SELECT * FROM variante_productos WHERE id_variante = ?");
        $query->execute([$id_variante]);
        return $query->fetch(PDO::FETCH_OBJ);
    }
    // ==================== VALIDACIONES ====================

    function verificarPedidoExiste($id){
        $query = $this->db->prepare("SELECT id_pedido FROM pedidos WHERE id_pedido = ?");
        $query->execute([$id]);
        return $query->fetch(PDO::FETCH_OBJ) !== false;
    }

    function verificarVarianteExiste($id_variante){
        $query = $this->db->prepare("SELECT id_variante FROM variante_productos WHERE id_variante = ?");
        $query->execute([$id_variante]);
        return $query->fetch(PDO::FETCH_OBJ) !== false;
    }

    function verificarStockDisponible($id_variante, $cantidad){
        $query = $this->db->prepare("SELECT stock FROM variante_productos WHERE id_variante = ?");
        $query->execute([$id_variante]);
        $variante = $query->fetch(PDO::FETCH_OBJ);
        
        if($variante && $variante->stock >= $cantidad){
            return true;
        }
        return false;
    }
}
