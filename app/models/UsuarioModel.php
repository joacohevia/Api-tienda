<?php
require_once __DIR__ . '/model.php';

class UsuarioModel extends Model{

    function listarUsuarios(){
        $query = $this->db->prepare("SELECT id_usuario, nombre, apellido, dni, email, creado, rol FROM usuarios");
        $query->execute();
        return $query->fetchAll(PDO::FETCH_OBJ);
    }

    function obtenerUsuario($id){
        $query = $this->db->prepare("SELECT id_usuario, nombre, apellido, dni, email, creado, rol FROM usuarios WHERE id_usuario = ?");
        $query->execute([$id]);
        return $query->fetch(PDO::FETCH_OBJ);
    }

    function obtenerPorEmail($email){
        $query = $this->db->prepare("SELECT id_usuario, nombre, apellido, dni, email, creado, rol FROM usuarios WHERE email = ?");
        $query->execute([$email]);
        return $query->fetch(PDO::FETCH_OBJ);
    }

    function obtenerPorDni($dni){
        $query = $this->db->prepare("SELECT id_usuario, nombre, apellido, dni, email, creado, rol FROM usuarios WHERE dni = ?");
        $query->execute([$dni]);
        return $query->fetch(PDO::FETCH_OBJ);
    }

    function insertarUsuario($nombre, $apellido, $dni, $celular, $email, $password, $rol = 'cliente'){
        $passwordHash = password_hash($password, PASSWORD_BCRYPT);
        $query = $this->db->prepare("INSERT INTO usuarios (nombre, apellido, dni, celular, email, password, rol) VALUES (?, ?, ?, ?, ?, ?, ?)");
        return $query->execute([$nombre, $apellido, $dni, $celular, $email, $passwordHash, $rol]);
    }

    function actualizarUsuario($id, $nombre, $apellido, $dni, $email, $rol){
        $query = $this->db->prepare("UPDATE usuarios SET nombre = ?, apellido = ?, dni = ?, email = ?, rol = ? WHERE id_usuario = ?");
        return $query->execute([$nombre, $apellido, $dni, $email, $rol, $id]);
    }

    function actualizarPassword($id, $password){
        $passwordHash = password_hash($password, PASSWORD_BCRYPT);
        $query = $this->db->prepare("UPDATE usuarios SET password = ? WHERE id_usuario = ?");
        return $query->execute([$passwordHash, $id]);
    }

    function eliminarUsuario($id){
        $query = $this->db->prepare("DELETE FROM usuarios WHERE id_usuario = ?");
        return $query->execute([$id]);
    }

    function verificarPassword($id, $password){
        $query = $this->db->prepare("SELECT password FROM usuarios WHERE id_usuario = ?");
        $query->execute([$id]);
        $usuario = $query->fetch(PDO::FETCH_OBJ);
        
        if($usuario){
            return password_verify($password, $usuario->password);
        }
        return false;
    }
}

  
