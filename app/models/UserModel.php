<?php

class UserModel {
    private $db;

    public function __construct() {
        $host = 'localhost';
        $port = '3306';
        $dbname = 'Fichajes2';
        $user = 'admin_fichajes2';       
        $pass = '6%DfdlBrud5$jVg8';        

        try {
            $this->db = new PDO(
                "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8",
                $user,
                $pass
            );
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
            die("Error de conexión: " . $e->getMessage());
        }
    }

   
    public function login($usuario, $contrasenya) {
        $stmt = $this->db->prepare("
            SELECT * FROM Usuario 
            WHERE nombre_usuario = :usuario 
            LIMIT 1
        ");
        
        $stmt->bindParam(':usuario', $usuario);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // SIN HASH (temporal)
        if ($user && $contrasenya === $user['contraseña']) {
            return $user;
        }

        // CON HASH (recomendado)
        // if ($user && password_verify($contrasenya, $user['contraseña'])) {
        //     return $user;
        // }

        return false;
    }

    public function getUser($usuario) {
        $stmt = $this->db->prepare("
            SELECT id_usuario, nombre_usuario 
            FROM Usuario 
            WHERE nombre_usuario = :usuario 
            LIMIT 1
        ");
        
        $stmt->bindParam(':usuario', $usuario);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

   
    public function getUsuarios() {
        $stmt = $this->db->prepare("
            SELECT * FROM Usuario ORDER BY id_usuario ASC
        ");
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

   
    public function getUsuarioById($id) {
        $stmt = $this->db->prepare("
            SELECT * FROM Usuario WHERE id_usuario = :id
        ");
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

  
    public function crearUsuario($datos) {
        $stmt = $this->db->prepare("
            INSERT INTO Usuario 
            (nombre, apellidos, nombre_usuario, contraseña, dni, telefono, email, rol)
            VALUES (:nombre, :apellidos, :usuario, :password, :dni, :telefono, :email, :rol)
        ");

        return $stmt->execute([
            ':nombre' => $datos['nombre'],
            ':apellidos' => $datos['apellidos'],
            ':usuario' => $datos['nombre_usuario'],
            ':password' => $datos['contraseña'], // 👉 puedes meter password_hash aquí
            ':dni' => $datos['dni'],
            ':telefono' => $datos['telefono'],
            ':email' => $datos['email'],
            ':rol' => $datos['rol']
        ]);
    }

 
    

    public function editUser($datos) {

    $stmt = $this->db->prepare("
        UPDATE Usuario SET
            nombre = :nombre,
            apellidos = :apellidos,
            nombre_usuario = :usuario,
            dni = :dni,
            telefono = :telefono,
            email = :email,
            rol = :rol
        WHERE id_usuario = :id
    ");

    return $stmt->execute([
        ':id' => $datos['id'],
        ':nombre' => $datos['nombre'],
        ':apellidos' => $datos['apellidos'],
        ':usuario' => $datos['nombre_usuario'],
        ':dni' => $datos['dni'],
        ':telefono' => $datos['telefono'],
        ':email' => $datos['email'],
        ':rol' => $datos['rol']
    ]);
}


 
    public function removeUser($id) {
        $stmt = $this->db->prepare("
            DELETE FROM Usuario WHERE id_usuario = :id
        ");

        return $stmt->execute([':id' => $id]);
    }
}