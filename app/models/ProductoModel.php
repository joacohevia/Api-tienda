<?php
require_once 'app/models/model.php';

class ProductoModel extends Model{

    // ==================== MÉTODOS DE PRODUCTOS ====================
    
    function listarProductos(){
        $query = $this->db->prepare("
            SELECT 
                p.id_producto,
                p.id_categoria,
                c.nombre AS categoria_nombre,
                p.nombre,
                p.descripcion,
                p.precio_base,
                p.marca,
                p.img,
                p.activo
            FROM productos p
            INNER JOIN categorias c ON p.id_categoria = c.id_categoria
            WHERE p.activo = 1
            ORDER BY p.nombre
        ");
        $query->execute();
        return $query->fetchAll(PDO::FETCH_OBJ);
    }

    function listarProductosPorCategoria($id_categoria){
        $query = $this->db->prepare("
            SELECT 
                p.id_producto,
                p.id_categoria,
                p.nombre,
                p.descripcion,
                p.precio_base,
                p.marca,
                p.img,
                p.activo
            FROM productos p
            WHERE p.id_categoria = ? AND p.activo = 1
            ORDER BY p.nombre
        ");
        $query->execute([$id_categoria]);
        return $query->fetchAll(PDO::FETCH_OBJ);
    }

    function obtenerProducto($id){
        $query = $this->db->prepare("
            SELECT 
                p.id_producto,
                p.id_categoria,
                c.nombre AS categoria_nombre,
                p.nombre,
                p.descripcion,
                p.precio_base,
                p.marca,
                p.img,
                p.activo
            FROM productos p
            INNER JOIN categorias c ON p.id_categoria = c.id_categoria
            WHERE p.id_producto = ?
        ");
        $query->execute([$id]);
        return $query->fetch(PDO::FETCH_OBJ);
    }

    function obtenerProductoConVariantes($id){
        $producto = $this->obtenerProducto($id);
        
        if(!$producto){
            return null;
        }

        // Obtener variantes del producto
        $variantes = $this->obtenerVariantesProducto($id);
        $producto->variantes = $variantes;
        
        return $producto;
    }

    function insertarProducto($id_categoria, $nombre, $descripcion, $precio_base, $marca, $img, $activo = 1){
        $query = $this->db->prepare("
            INSERT INTO productos (id_categoria, nombre, descripcion, precio_base, marca, img, activo)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        if($query->execute([$id_categoria, $nombre, $descripcion, $precio_base, $marca, $img, $activo])){
            return $this->db->lastInsertId();
        }
        return false;
    }

    function actualizarProducto($id, $id_categoria, $nombre, $descripcion, $precio_base, $marca, $img, $activo){
        $query = $this->db->prepare("
            UPDATE productos 
            SET id_categoria = ?, nombre = ?, descripcion = ?, precio_base = ?, marca = ?, img = ?, activo = ?
            WHERE id_producto = ?
        ");
        return $query->execute([$id_categoria, $nombre, $descripcion, $precio_base, $marca, $img, $activo, $id]);
    }

    function desactivarProducto($id){
        $query = $this->db->prepare("UPDATE productos SET activo = 0 WHERE id_producto = ?");
        return $query->execute([$id]);
    }

    function eliminarProducto($id){
        // Primero eliminar variantes
        $query = $this->db->prepare("DELETE FROM variante_productos WHERE id_producto = ?");
        $query->execute([$id]);

        // Luego eliminar el producto
        $query = $this->db->prepare("DELETE FROM productos WHERE id_producto = ?");
        return $query->execute([$id]);
    }

    // ==================== MÉTODOS DE VARIANTES ====================

    function obtenerVariantesProducto($id_producto){
        $query = $this->db->prepare("
            SELECT 
                id_variante,
                id_producto,
                talle,
                color,
                precio,
                stock
            FROM variante_productos
            WHERE id_producto = ?
            ORDER BY talle, color
        ");
        $query->execute([$id_producto]);
        return $query->fetchAll(PDO::FETCH_OBJ);
    }

    function obtenerVariante($id_variante){
        $query = $this->db->prepare("
            SELECT 
                id_variante,
                id_producto,
                talle,
                color,
                precio,
                stock
            FROM variante_productos
            WHERE id_variante = ?
        ");
        $query->execute([$id_variante]);
        return $query->fetch(PDO::FETCH_OBJ);
    }

    function insertarVariante($id_producto, $talle, $color, $precio, $stock){
        $query = $this->db->prepare("
            INSERT INTO variante_productos (id_producto, talle, color, precio, stock)
            VALUES (?, ?, ?, ?, ?)
        ");
        if($query->execute([$id_producto, $talle, $color, $precio, $stock])){
            return $this->db->lastInsertId();
        }
        return false;
    }

    function actualizarVariante($id_variante, $talle, $color, $precio, $stock){
        $query = $this->db->prepare("
            UPDATE variante_productos 
            SET talle = ?, color = ?, precio = ?, stock = ?
            WHERE id_variante = ?
        ");
        return $query->execute([$talle, $color, $precio, $stock, $id_variante]);
    }

    function actualizarStockVariante($id_variante, $cantidad){
        $query = $this->db->prepare("
            UPDATE variante_productos 
            SET stock = ? 
            WHERE id_variante = ?
        ");
        return $query->execute([$cantidad, $id_variante]);
    }

    function eliminarVariante($id_variante){
        $query = $this->db->prepare("DELETE FROM variante_productos WHERE id_variante = ?");
        return $query->execute([$id_variante]);
    }

    // ==================== VALIDACIONES ====================

    function verificarProductoExiste($id){
        $query = $this->db->prepare("SELECT id_producto FROM productos WHERE id_producto = ?");
        $query->execute([$id]);
        return $query->fetch(PDO::FETCH_OBJ) !== false;
    }

    function verificarVarianteExiste($id_variante){
        $query = $this->db->prepare("SELECT id_variante FROM variante_productos WHERE id_variante = ?");
        $query->execute([$id_variante]);
        return $query->fetch(PDO::FETCH_OBJ) !== false;
    }

    function verificarCategoriaExiste($id_categoria){
        $query = $this->db->prepare("SELECT id_categoria FROM categorias WHERE id_categoria = ?");
        $query->execute([$id_categoria]);
        return $query->fetch(PDO::FETCH_OBJ) !== false;
    }

    function obtenerStockVariante($id_variante){
        $query = $this->db->prepare("SELECT stock FROM variante_productos WHERE id_variante = ?");
        $query->execute([$id_variante]);
        $variante = $query->fetch(PDO::FETCH_OBJ);
        return $variante ? $variante->stock : 0;
    }
}
