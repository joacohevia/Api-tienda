<?php
require_once __DIR__ . 'app/models/model.php';

class CategoriaModel extends Model{

    
    function listarCategorias(){

        $query = $this->db->prepare("SELECT * FROM categorias");
        $query->execute();

        return $query->fetchAll(PDO::FETCH_OBJ);
    }

    function insertarCategoria($nombre, $descripcion){
        $query = $this->db->prepare("INSERT INTO categorias (nombre, descripcion) VALUES (?, ?)");
        return $query->execute([$nombre, $descripcion]);
    }

    function actualizarCategoria($id, $nombre, $descripcion){
        $query = $this->db->prepare("UPDATE categorias SET nombre = ?, descripcion = ? WHERE id_categoria = ?");
        return $query->execute([$nombre, $descripcion, $id]);
    }

    function eliminarCategoria($id){
        $query = $this->db->prepare("DELETE FROM categorias WHERE id_categoria = ?");
        return $query->execute([$id]);
    }

    function obtenerCategoria($id){
        $query = $this->db->prepare("SELECT * FROM categorias WHERE id_categoria = ?");
        $query->execute([$id]);
        return $query->fetch(PDO::FETCH_OBJ);
    }
}