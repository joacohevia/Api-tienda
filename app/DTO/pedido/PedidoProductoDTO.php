<?php
class PedidoProductoDTO {

    public static function fromCrear($id_pedido): array {
        return [
            'id_pedido' => $id_pedido,
            'mensaje' => 'Pedido creado exitosamente',
        ];
    }

    public static function fromAgregarProducto($total): array {
        return [
            'mensaje' => 'Producto agregado al pedido exitosamente',
            'nuevo_total' => $total,
        ];
    }
}
