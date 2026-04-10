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

    // =========================
    // LOGIN
    // =========================
    public function login($usuario, $contrasenya) {

        $stmt = $this->db->prepare("
            SELECT * FROM Usuario 
            WHERE nombre_usuario = :usuario 
            LIMIT 1
        ");

        $stmt->bindParam(':usuario', $usuario);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) return false;

        $storedPassword = $user['contraseña'];

        if (password_verify($contrasenya, $storedPassword)) {
            return $user;
        }

        // compatibilidad passwords antiguas
        if ($contrasenya === $storedPassword) {
            $this->upgradePasswordHash($user['id_usuario'], $contrasenya);
            return $user;
        }

        return false;
    }

    private function upgradePasswordHash($id, $password) {
        $stmt = $this->db->prepare("
            UPDATE Usuario 
            SET contraseña = :password 
            WHERE id_usuario = :id
        ");

        $stmt->execute([
            ':password' => password_hash($password, PASSWORD_DEFAULT),
            ':id' => $id
        ]);
    }

    // =========================
    // USUARIO SIMPLE
    // =========================
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

    // =========================
    // LISTADO USUARIOS + MATRÍCULAS
    // =========================
    public function getUsuarios() {

        $stmt = $this->db->prepare("
            SELECT * FROM Usuario
            ORDER BY id_usuario ASC
        ");

        $stmt->execute();
        $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($usuarios as &$usuario) {

            $stmtMat = $this->db->prepare("
                SELECT id_matricula, matricula
                FROM matriculas
                WHERE id_usuario = :id
            ");

            $stmtMat->execute([
                ':id' => $usuario['id_usuario']
            ]);

            $usuario['matriculas'] = $stmtMat->fetchAll(PDO::FETCH_ASSOC);
            $usuario['matriculas_count'] = count($usuario['matriculas']);
        }

        return $usuarios;
    }

    // =========================
    // USUARIO POR ID + MATRÍCULAS
    // =========================
    public function getUsuarioById($id) {

        $stmt = $this->db->prepare("
            SELECT * FROM Usuario
            WHERE id_usuario = :id
        ");

        $stmt->bindParam(':id', $id);
        $stmt->execute();

        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$usuario) return false;

        $stmtMat = $this->db->prepare("
            SELECT id_matricula, matricula
            FROM matriculas
            WHERE id_usuario = :id
        ");

        $stmtMat->execute([':id' => $id]);

        $usuario['matriculas'] = $stmtMat->fetchAll(PDO::FETCH_ASSOC);

        return $usuario;
    }

    // =========================
    // CREAR USUARIO
    // =========================
    public function crearUsuario($datos) {

        $stmt = $this->db->prepare("
            INSERT INTO Usuario 
            (nombre, apellidos, nombre_usuario, contraseña, dni, telefono, email, rol)
            VALUES (:nombre, :apellidos, :usuario, :password, :dni, :telefono, :email, :rol)
        ");

        $hashedPassword = password_hash($datos['contraseña'], PASSWORD_DEFAULT);

        return $stmt->execute([
            ':nombre' => $datos['nombre'],
            ':apellidos' => $datos['apellidos'],
            ':usuario' => $datos['nombre_usuario'],
            ':password' => $hashedPassword,
            ':dni' => $datos['dni'],
            ':telefono' => $datos['telefono'],
            ':email' => $datos['email'],
            ':rol' => $datos['rol']
        ]);
    }

    // =========================
    // EDITAR USUARIO
    // =========================
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

    // =========================
    // ELIMINAR USUARIO
    // =========================
    public function removeUser($id) {

        $stmt = $this->db->prepare("
            DELETE FROM Usuario
            WHERE id_usuario = :id
        ");

        return $stmt->execute([
            ':id' => $id
        ]);
    }

    // =========================
    // MATRÍCULAS
    // =========================

    public function addMatricula($id_usuario, $matricula) {

        $stmt = $this->db->prepare("
            INSERT INTO matriculas (id_usuario, matricula)
            VALUES (:id_usuario, :matricula)
        ");

        return $stmt->execute([
            ':id_usuario' => $id_usuario,
            ':matricula' => $matricula
        ]);
    }

    public function deleteMatricula($id_matricula) {

        $stmt = $this->db->prepare("
            DELETE FROM matriculas
            WHERE id_matricula = :id
        ");

        return $stmt->execute([
            ':id' => $id_matricula
        ]);
    }

    public function matriculaExiste($matricula) {

        $stmt = $this->db->prepare("
            SELECT COUNT(*) as total
            FROM matriculas
            WHERE matricula = :matricula
        ");

        $stmt->execute([
            ':matricula' => $matricula
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC)['total'] > 0;
    }
}