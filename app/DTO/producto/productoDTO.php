<?php
class ProductDTO {
    
    public static function fromDatabase($product): array {
        if (!$product) {
            return [];
        }

        return [
            // ✅ Cambiar ['key'] por ->key
            'id' => (int) $product->id_producto,
            'nombre' => $product->nombre,
            'descripcion' => $product->descripcion ?? '',
            'precio' => (float) $product->precio_base,
            'marca' => $product->marca ?? '',
            'imagen' => $product->img ?? '',
            'activo' => (bool) $product->activo,
            'categoria' => [
                'id' => (int) $product->id_categoria,
                'nombre' => $product->categoria_nombre ?? ''
            ],
        ];
    }

    public static function fromDatabaseList($products): array {
        if (!$products || empty($products)) {
            return [];
        }

        return array_map(fn($p) => self::fromDatabase($p), $products);
    }
}
?>