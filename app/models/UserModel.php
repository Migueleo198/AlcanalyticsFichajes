<?php

class UserModel {

    private $db;

    public function __construct() {

        $host   = 'localhost';
        $port   = '3306';
        $dbname = 'Fichajes2';
        $user   = 'admin_fichajes2';
        $pass   = '6%DfdlBrud5$jVg8';

        try {
            $this->db = new PDO(
                "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8",
                $user,
                $pass
            );

            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        } catch (PDOException $e) {
            die(json_encode([
                "success" => false,
                "error"   => "DB connection error"
            ]));
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

        $stmt->execute([':usuario' => $usuario]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) return false;

        $storedPassword = $user['contraseña'];

        if (password_verify($contrasenya, $storedPassword)) {
            return $user;
        }

        // Plain-text legacy password — upgrade hash on the fly
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
            ':id'       => $id
        ]);
    }

    // =========================
    // GET USERS (API SAFE)
    // =========================
    public function getUsuarios() {

        $stmt = $this->db->prepare("
            SELECT
                id_usuario,
                nombre,
                apellidos,
                nombre_usuario,
                dni,
                telefono,
                email,
                fecha_nacimiento,
                rol
            FROM Usuario
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

            $stmtMat->execute([':id' => $usuario['id_usuario']]);
            $usuario['matriculas'] = $stmtMat->fetchAll(PDO::FETCH_ASSOC) ?: [];
        }

        return $usuarios;
    }

    // =========================
    // GET USER BY ID
    // =========================
    public function getUsuarioById($id) {

        $stmt = $this->db->prepare("
            SELECT
                id_usuario,
                nombre,
                apellidos,
                nombre_usuario,
                dni,
                telefono,
                email,
                fecha_nacimiento,
                rol
            FROM Usuario
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
        $usuario['matriculas'] = $stmtMat->fetchAll(PDO::FETCH_ASSOC) ?: [];

        return $usuario;
    }

    // =========================
    // CREATE USER (SAFE)
    // =========================
    public function crearUsuario($datos) {

        $stmt = $this->db->prepare("
            INSERT INTO Usuario
            (nombre, apellidos, nombre_usuario, contraseña, dni, telefono, email, rol, fecha_nacimiento)
            VALUES (:nombre, :apellidos, :usuario, :password, :dni, :telefono, :email, :rol, :fecha_nacimiento)
        ");

        $stmt->execute([
            ':nombre'          => $datos['nombre']          ?? '',
            ':apellidos'       => $datos['apellidos']       ?? '',
            ':usuario'         => $datos['usuario']         ?? $datos['nombre_usuario'] ?? '',
            ':password'        => password_hash($datos['contraseña'] ?? '', PASSWORD_DEFAULT),
            ':dni'             => $datos['dni']             ?? '',
            ':telefono'        => $datos['telefono']        ?? '',
            ':email'           => $datos['email']           ?? '',
            ':rol'             => $datos['rol']             ?? '',
            ':fecha_nacimiento' => !empty($datos['fecha_nacimiento']) ? $datos['fecha_nacimiento'] : null
        ]);

        $id_usuario = $this->db->lastInsertId();

        if (!empty($datos['matriculas'])) {

            $matriculas = is_array($datos['matriculas'])
                ? $datos['matriculas']
                : explode(',', $datos['matriculas']);

            foreach ($matriculas as $matricula) {
                $matricula = trim($matricula);
                if ($matricula === '') continue;
                $this->addMatricula($id_usuario, $matricula);
            }
        }

        return $id_usuario;
    }

    // =========================
    // EDIT USER (SAFE)
    // =========================
    public function editUser($datos) {

        try {
            $this->db->beginTransaction();

            $stmt = $this->db->prepare("
                UPDATE Usuario SET
                    nombre           = :nombre,
                    apellidos        = :apellidos,
                    nombre_usuario   = :usuario,
                    dni              = :dni,
                    telefono         = :telefono,
                    email            = :email,
                    rol              = :rol,
                    fecha_nacimiento = :fecha_nacimiento
                WHERE id_usuario = :id
            ");

            $stmt->execute([
                ':id'              => $datos['id']       ?? 0,
                ':nombre'          => $datos['nombre']   ?? '',
                ':apellidos'       => $datos['apellidos'] ?? '',
                ':usuario'         => $datos['usuario']  ?? $datos['nombre_usuario'] ?? '',
                ':dni'             => $datos['dni']      ?? '',
                ':telefono'        => $datos['telefono'] ?? '',
                ':email'           => $datos['email']    ?? '',
                ':rol'             => $datos['rol']      ?? '',
                ':fecha_nacimiento' => !empty($datos['fecha_nacimiento']) ? $datos['fecha_nacimiento'] : null
            ]);

            $this->db->prepare("
                DELETE FROM matriculas WHERE id_usuario = :id
            ")->execute([':id' => $datos['id']]);

            if (!empty($datos['matriculas'])) {

                $matriculas = is_array($datos['matriculas'])
                    ? $datos['matriculas']
                    : explode(',', $datos['matriculas']);

                $stmtMat = $this->db->prepare("
                    INSERT INTO matriculas (id_usuario, matricula)
                    VALUES (:id_usuario, :matricula)
                ");

                foreach ($matriculas as $matricula) {
                    $matricula = trim($matricula);
                    if ($matricula === '') continue;
                    $stmtMat->execute([
                        ':id_usuario' => $datos['id'],
                        ':matricula'  => $matricula
                    ]);
                }
            }

            $this->db->commit();
            return ["success" => true];

        } catch (Exception $e) {
            $this->db->rollBack();
            return ["success" => false, "error" => $e->getMessage()];
        }
    }

    // =========================
    // DELETE USER (SAFE)
    // =========================
    public function removeUser($id) {

        try {
            $this->db->prepare("
                DELETE FROM matriculas WHERE id_usuario = :id
            ")->execute([':id' => $id]);

            $this->db->prepare("
                DELETE FROM Usuario WHERE id_usuario = :id
            ")->execute([':id' => $id]);

            return ["success" => true];

        } catch (Exception $e) {
            return ["success" => false, "error" => $e->getMessage()];
        }
    }

    // =========================
    // MATRICULAS
    // =========================
    public function addMatricula($id_usuario, $matricula) {

        $stmt = $this->db->prepare("
            INSERT INTO matriculas (id_usuario, matricula)
            VALUES (:id_usuario, :matricula)
        ");

        return $stmt->execute([
            ':id_usuario' => $id_usuario,
            ':matricula'  => $matricula
        ]);
    }

    public function deleteMatricula($id_matricula) {

        $stmt = $this->db->prepare("
            DELETE FROM matriculas WHERE id_matricula = :id
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

    public function buscarTokensRecuperacion()
{
    $stmt = $this->db->prepare("
        SELECT *
        FROM recuperacion_password
        WHERE usado = 0
    ");

    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

    public function getTokenByHash($token)
{
    $stmt = $this->db->prepare("
        SELECT * FROM recuperacion_password
        WHERE token = :token
        LIMIT 1
    ");

    $stmt->execute([':token' => $token]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

public function getTokenByUser($id)
{
    $stmt = $this->db->prepare("
        SELECT * FROM recuperacion_password
        WHERE id_usuario = :id
        ORDER BY id DESC
        LIMIT 1
    ");

    $stmt->execute([':id' => $id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}


    // =========================
    // PASSWORD RECOVERY
    // =========================
    public function buscarPorEmail($email)
    {
        $stmt = $this->db->prepare("
            SELECT * FROM Usuario
            WHERE email = :email
            LIMIT 1
        ");

        $stmt->bindParam(':email', $email);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function guardarTokenRecuperacion($idUsuario, $token, $expira)
{
    try {
        $stmt = $this->db->prepare("
            INSERT INTO recuperacion_password
            (id_usuario, token, expira_en)
            VALUES (:id_usuario, :token, :expira_en)
        ");

        $ok = $stmt->execute([
            ':id_usuario' => $idUsuario,
            ':token' => $token,
            ':expira_en' => $expira
        ]);

        if (!$ok) {
            error_log(print_r($stmt->errorInfo(), true));
        }

        return $ok;

    } catch (PDOException $e) {
        error_log($e->getMessage());
        return false;
    }
}

   public function marcarTokenUsado($token)
{
    $stmt = $this->db->prepare("
        UPDATE recuperacion_password
        SET usado = 1
        WHERE token = :token
    ");

    return $stmt->execute([
        ':token' => $token
    ]);
}

    // =========================
    // ACTUALIZAR PERFIL PROPIO (solo campos seguros)
    // =========================
    public function actualizarPerfil($idUsuario, $datos)
    {
        $stmt = $this->db->prepare("
            UPDATE Usuario SET
                nombre           = :nombre,
                apellidos        = :apellidos,
                email            = :email,
                telefono         = :telefono,
                fecha_nacimiento = :fecha_nacimiento
            WHERE id_usuario = :id
        ");

        return $stmt->execute([
            ':nombre'           => $datos['nombre']           ?? '',
            ':apellidos'        => $datos['apellidos']        ?? '',
            ':email'            => $datos['email']            ?? '',
            ':telefono'         => $datos['telefono']         ?? '',
            ':fecha_nacimiento' => !empty($datos['fecha_nacimiento']) ? $datos['fecha_nacimiento'] : null,
            ':id'               => $idUsuario,
        ]);
    }

    // Verifica si la contraseña actual es correcta
    public function verificarPassword($idUsuario, $password)
    {
        $stmt = $this->db->prepare("
            SELECT contraseña FROM Usuario WHERE id_usuario = :id LIMIT 1
        ");
        $stmt->execute([':id' => $idUsuario]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) return false;
        return password_verify($password, $row['contraseña']) || $password === $row['contraseña'];
    }

    // ── FIX: was missing — saves the new hashed password ─────────────────
    public function actualizarPassword($idUsuario, $nuevaPassword)
    {
        $stmt = $this->db->prepare("
            UPDATE Usuario
            SET contraseña = :password
            WHERE id_usuario = :id
        ");

        return $stmt->execute([
            ':password' => password_hash($nuevaPassword, PASSWORD_DEFAULT),
            ':id'       => $idUsuario
        ]);
    }
}
