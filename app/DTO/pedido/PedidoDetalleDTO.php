<?php
class PedidoDetalleDTO {

    public static function fromDatabase($pedido): array {
        if (!$pedido) {
            return [];
        }

        return [
            'id_pedido' => $pedido->id_pedido,
            'id_usuario' => $pedido->id_usuario,
            'nombre' => $pedido->nombre,
            'apellido' => $pedido->apellido,
            'email' => $pedido->email,
            'fecha' => $pedido->fecha,
            'estado' => $pedido->estado,
            'total' => $pedido->total,
            'productos' => self::transformarProductos($pedido->productos ?? []),
        ];
    }

    private static function transformarProductos($productos): array {
        if (!$productos || empty($productos)) {
            return [];
        }

        return array_map(function($p) {
            return [
                'id_pedido_producto' => $p->id_pedido_producto,
                'id_variante' => $p->id_variante,
                'cantidad' => $p->cantidad,
                'precio_unitario' => $p->precio_unitario,
                'id_producto' => $p->id_producto,
                'producto_nombre' => $p->producto_nombre,
                'producto_descripcion' => $p->producto_descripcion,
                'talle' => $p->talle,
                'color' => $p->color,
                'precio' => $p->precio,
            ];
        }, $productos);
    }
}
