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
    // GET USERS + MATRICULAS
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
        }

        return $usuarios;
    }

    // =========================
    // GET USER BY ID
    // =========================
    public function getUsuarioById($id) {

        $stmt = $this->db->prepare("
            SELECT * FROM Usuario
            WHERE id_usuario = :id
        ");

        $stmt->execute([':id' => $id]);
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
    // CREATE USER
    // =========================
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
            ':password' => password_hash($datos['contraseña'], PASSWORD_DEFAULT),
            ':dni' => $datos['dni'],
            ':telefono' => $datos['telefono'],
            ':email' => $datos['email'],
            ':rol' => $datos['rol']
        ]);
    }

    // =========================
    // EDIT USER + SYNC MATRICULAS
    // =========================
    public function editUser($datos) {

        try {
            $this->db->beginTransaction();

            // 1. UPDATE USER
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

            $stmt->execute([
                ':id' => $datos['id'],
                ':nombre' => $datos['nombre'],
                ':apellidos' => $datos['apellidos'],
                ':usuario' => $datos['nombre_usuario'],
                ':dni' => $datos['dni'],
                ':telefono' => $datos['telefono'],
                ':email' => $datos['email'],
                ':rol' => $datos['rol']
            ]);

            // 2. GET CURRENT MATRICULAS
            $stmtGet = $this->db->prepare("
                SELECT id_matricula
                FROM matriculas
                WHERE id_usuario = :id
            ");

            $stmtGet->execute([':id' => $datos['id']]);
            $current = $stmtGet->fetchAll(PDO::FETCH_COLUMN);

            $new = $datos['matriculas'] ?? [];

            // 3. DELETE REMOVED MATRICULAS
            foreach ($current as $id_matricula) {

                $stmtCheck = $this->db->prepare("
                    SELECT matricula 
                    FROM matriculas 
                    WHERE id_matricula = :id
                ");

                $stmtCheck->execute([':id' => $id_matricula]);
                $mat = $stmtCheck->fetchColumn();

                if (!in_array($mat, $new)) {
                    $stmtDel = $this->db->prepare("
                        DELETE FROM matriculas
                        WHERE id_matricula = :id
                    ");

                    $stmtDel->execute([':id' => $id_matricula]);
                }
            }

            // 4. ADD NEW MATRICULAS
            foreach ($new as $matricula) {

                $stmtExists = $this->db->prepare("
                    SELECT COUNT(*) 
                    FROM matriculas 
                    WHERE id_usuario = :id AND matricula = :matricula
                ");

                $stmtExists->execute([
                    ':id' => $datos['id'],
                    ':matricula' => $matricula
                ]);

                if ($stmtExists->fetchColumn() == 0) {

                    $stmtIns = $this->db->prepare("
                        INSERT INTO matriculas (id_usuario, matricula)
                        VALUES (:id_usuario, :matricula)
                    ");

                    $stmtIns->execute([
                        ':id_usuario' => $datos['id'],
                        ':matricula' => $matricula
                    ]);
                }
            }

            $this->db->commit();
            return true;

        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    // =========================
    // DELETE USER
    // =========================
    public function removeUser($id) {

        $stmt = $this->db->prepare("
            DELETE FROM Usuario
            WHERE id_usuario = :id
        ");

        return $stmt->execute([':id' => $id]);
    }

    // =========================
    // MATRICULAS HELPERS
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

        return $stmt->execute([':id' => $id_matricula]);
    }

    public function matriculaExiste($matricula) {

        $stmt = $this->db->prepare("
            SELECT COUNT(*) as total
            FROM matriculas
            WHERE matricula = :matricula
        ");

        $stmt->execute([':matricula' => $matricula]);

        return $stmt->fetch(PDO::FETCH_ASSOC)['total'] > 0;
    }
}