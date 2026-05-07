<?php

require_once '../app/models/UserModel.php';

class User extends Controller
{
    // =========================
    // LOGIN
    // =========================
    public function login()
    {
        header('Content-Type: application/json; charset=utf-8');

        $input = json_decode(file_get_contents("php://input"), true);

        $usuario     = $input['usuario']     ?? '';
        $contrasenya = $input['contrasenya'] ?? '';

        $model = new UserModel();
        $user  = $model->login($usuario, $contrasenya);

        echo json_encode($user ? [
            "success" => true,
            "message" => "Sesión iniciada correctamente",
            "usuario" => $user['nombre_usuario'],
            "rol"     => $user['rol']
        ] : [
            "success" => false,
            "message" => "Credenciales incorrectas"
        ]);

        exit;
    }

    // =========================
    // GET USER BY ID
    // =========================
    public function getUser()
    {
        header('Content-Type: application/json; charset=utf-8');

        $id = $_GET['id'] ?? null;

        if (!$id) {
            http_response_code(400);
            echo json_encode([
                "success" => false,
                "message" => "ID requerido"
            ]);
            exit;
        }

        $model   = new UserModel();
        $usuario = $model->getUsuarioById($id);

        if (!$usuario) {
            http_response_code(404);
            echo json_encode([
                "success" => false,
                "message" => "Usuario no encontrado"
            ]);
            exit;
        }

        echo json_encode([
            "success" => true,
            "data"    => $usuario
        ]);

        exit;
    }

    // =========================
    // GET ALL USERS
    // =========================
    public function getUsers()
    {
        header('Content-Type: application/json; charset=utf-8');

        $model    = new UserModel();
        $usuarios = $model->getUsuarios();

        echo json_encode([
            "success" => true,
            "data"    => $usuarios ?: []
        ]);

        exit;
    }

    // =========================
    // DELETE USER
    // =========================
    public function removeUser()
    {
        header('Content-Type: application/json; charset=utf-8');

        $id = $_POST['id'] ?? null;

        if (!$id) {
            echo json_encode([
                "success" => false,
                "message" => "ID requerido"
            ]);
            exit;
        }

        $model = new UserModel();
        $ok    = $model->removeUser($id);

        echo json_encode([
            "success" => (bool) $ok,
            "message" => $ok ? "Usuario eliminado correctamente" : "Error al eliminar el usuario"
        ]);

        exit;
    }

    // =========================
    // ADD USER + MATRICULAS
    // =========================
    public function addUser()
    {
        header('Content-Type: application/json; charset=utf-8');

        if (empty($_POST)) {
            echo json_encode([
                "success" => false,
                "message" => "Datos vacíos"
            ]);
            exit;
        }

        $model = new UserModel();

        // 1. CREATE USER
        $userId = $model->crearUsuario([
            'nombre'          => $_POST['nombre']           ?? '',
            'apellidos'       => $_POST['apellidos']        ?? '',
            'nombre_usuario'  => $_POST['usuario']          ?? '',
            'contraseña'      => $_POST['contraseña']       ?? '',
            'dni'             => $_POST['dni']              ?? '',
            'telefono'        => $_POST['telefono']         ?? '',
            'email'           => $_POST['email']            ?? '',
            'rol'             => $_POST['rol']              ?? '',
            'fecha_nacimiento'=> $_POST['fecha_nacimiento'] ?? ''  // ← ADDED
        ]);

        if (!$userId) {
            echo json_encode([
                "success" => false,
                "message" => "Error al crear el usuario"
            ]);
            exit;
        }

        // 2. ADD MATRICULAS — accepts both matriculas[] array and legacy comma-separated string
        $raw = $_POST['matriculas'] ?? [];

        if (is_string($raw)) {
            $matriculas = array_filter(array_map('trim', explode(',', $raw)));
        } else {
            $matriculas = array_filter(array_map('trim', (array) $raw));
        }

        foreach ($matriculas as $m) {
            $model->addMatricula($userId, $m);
        }

        echo json_encode([
            "success"    => true,
            "message"    => "Usuario creado correctamente",
            "id_usuario" => $userId
        ]);

        exit;
    }

    // =========================
    // EDIT USER
    // =========================
    public function editUser()
    {
        header('Content-Type: application/json; charset=utf-8');

        $raw = $_POST['matriculas'] ?? [];

        if (is_string($raw)) {
            $matriculas = array_filter(array_map('trim', explode(',', $raw)));
        } else {
            $matriculas = array_filter(array_map('trim', (array) $raw));
        }

        $matriculas = array_values($matriculas);

        $datos = [
            'id'              => $_POST['id']              ?? null,
            'nombre'          => $_POST['nombre']          ?? '',
            'apellidos'       => $_POST['apellidos']       ?? '',
            'nombre_usuario'  => $_POST['usuario']         ?? '',
            'dni'             => $_POST['dni']             ?? '',
            'telefono'        => $_POST['telefono']        ?? '',
            'email'           => $_POST['email']           ?? '',
            'rol'             => $_POST['rol']             ?? '',
            'fecha_nacimiento'=> $_POST['fecha_nacimiento']?? '',  // ← ADDED
            'matriculas'      => $matriculas
        ];

        if (!$datos['id']) {
            echo json_encode([
                "success" => false,
                "message" => "ID requerido"
            ]);
            exit;
        }

        $model = new UserModel();

        try {
            $ok = $model->editUser($datos);

            echo json_encode([
                "success" => (bool) $ok,
                "message" => $ok ? "Cambios guardados correctamente" : "Error al guardar los cambios"
            ]);

            

        } catch (Throwable $e) {
            http_response_code(500);

            echo json_encode([
                "success" => false,
                "message" => $e->getMessage()
            ]);
        }

        exit;
    }
}