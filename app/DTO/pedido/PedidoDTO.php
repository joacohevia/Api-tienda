<?php
class PedidoDTO {

    public static function fromDatabase($pedido): array {
        if (!$pedido) {
            return [];
        }

        return [
            'id_pedido' => $pedido->id_pedido,
            'id_usuario' => $pedido->id_usuario,
            'fecha' => $pedido->fecha ?? null,
            'estado' => $pedido->estado,
            'total' => $pedido->total,
        ];
    }

    public static function fromDatabaseList($pedidos): array {
        if (!$pedidos || empty($pedidos)) {
            return [];
        }

        return array_map(fn($p) => self::fromDatabase($p), $pedidos);
    }
}
