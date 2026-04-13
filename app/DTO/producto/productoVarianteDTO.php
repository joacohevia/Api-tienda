<?php
class ProductoVarianteDTO {
    
    /**
     * Transforma un producto con sus variantes (endpoint detalle)
     * Asume que el modelo ya hizo el JOIN y trae los datos completos
     */
    public static function fromDatabase($product): array {
        if (!$product) {
            return [];
        }

        return [
            // ✅ Datos del producto (sintaxis de objeto ->)
            'id' => (int) $product->id_producto,
            'nombre' => $product->nombre,
            'descripcion' => $product->descripcion ?? '',
            'precio' => (float) $product->precio_base,
            'marca' => $product->marca ?? '',
            'imagen' => $product->img ?? '',
            'activo' => (bool) $product->activo,
            
            // ✅ Categoría
            'categoria' => [
                'id' => (int) $product->id_categoria,
                'nombre' => $product->categoria_nombre ?? ''
            ],
            
            // ✅ Variantes (ARRAY, no objeto único)
            'variantes' => self::transformarVariantes($product->variantes ?? []),
        ];
    }

    /**
     * Helper para transformar las variantes (si vienen como array anidado)
     */
    private static function transformarVariantes($variantes): array {
        if (!$variantes || empty($variantes)) {
            return [];
        }

        return array_map(function($v) {
            return [
                'id' => (int) $v->id_variante,
                'talle' => $v->talle ?? '',
                'color' => $v->color ?? '',
                'precio' => (float) $v->precio,
                'stock' => (int) $v->stock,
                'disponible' => (int) $v->stock > 0,
            ];
        }, $variantes);
    }

    /**
     * Transforma un array de productos con variantes
     */
    public static function fromDatabaseList($products): array {
        if (!$products || empty($products)) {
            return [];
        }

        return array_map(fn($p) => self::fromDatabase($p), $products);
    }
}
?>