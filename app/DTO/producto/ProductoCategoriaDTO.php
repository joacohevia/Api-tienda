<?php
class ProductoCategoriaDTO {

    public static function fromDatabase($product): array {
        if (!$product) {
            return [];
        }

        return [
            'id_producto' => $product->id_producto,
            'id_categoria' => $product->id_categoria,
            'nombre' => $product->nombre,
            'descripcion' => $product->descripcion,
            'precio_base' => $product->precio_base,
            'marca' => $product->marca,
            'img' => $product->img,
            'activo' => $product->activo,
        ];
    }

    public static function fromDatabaseList($products): array {
        if (!$products || empty($products)) {
            return [];
        }

        return array_map(fn($p) => self::fromDatabase($p), $products);
    }
}
