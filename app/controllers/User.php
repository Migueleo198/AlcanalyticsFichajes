<?php

require_once '../app/models/UserModel.php';

class User extends Controller
{
    private function checkAuth()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['id_usuario'])) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'No autorizado']);
            exit;
        }
    }

    private function checkAdmin()
    {
        $this->checkAuth();
        if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'Administrador') {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Acceso denegado']);
            exit;
        }
    }

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
        $this->checkAuth();
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
        $this->checkAuth();
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
        $this->checkAdmin();
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
        $this->checkAdmin();

        $nombre   = trim($_POST['nombre']    ?? '');
        $usuario  = trim($_POST['usuario']   ?? '');
        $password = $_POST['contraseña']     ?? '';
        $email    = trim($_POST['email']     ?? '');

        if (!$nombre || !$usuario || !$password || !$email) {
            echo json_encode([
                "success" => false,
                "message" => "Los campos Nombre, Usuario, Contraseña y Email son obligatorios"
            ]);
            exit;
        }

        $model = new UserModel();

        try {
            $userId = $model->crearUsuario([
                'nombre'           => $nombre,
                'apellidos'        => trim($_POST['apellidos']        ?? ''),
                'nombre_usuario'   => $usuario,
                'contraseña'       => $password,
                'dni'              => trim($_POST['dni']              ?? ''),
                'telefono'         => trim($_POST['telefono']         ?? ''),
                'email'            => $email,
                'rol'              => $_POST['rol']                   ?? 'Trabajador',
                'fecha_nacimiento' => $_POST['fecha_nacimiento']      ?? ''
            ]);

            if (!$userId) {
                echo json_encode(["success" => false, "message" => "No se pudo crear el usuario"]);
                exit;
            }

            // Matrículas (comma-separated string o array)
            $raw = $_POST['matriculas'] ?? [];
            $matriculas = is_string($raw)
                ? array_filter(array_map('trim', explode(',', $raw)))
                : array_filter(array_map('trim', (array) $raw));

            foreach ($matriculas as $m) {
                $model->addMatricula($userId, $m);
            }

            echo json_encode([
                "success"    => true,
                "message"    => "Usuario creado correctamente",
                "id_usuario" => $userId
            ]);

        } catch (Throwable $e) {
            // Detectar violación de constraint UNIQUE (código SQLSTATE 23000)
            $msg = "Error al crear el usuario";
            if (str_contains($e->getMessage(), 'nombre_usuario') || str_contains($e->getMessage(), 'Duplicate entry')) {
                if (str_contains($e->getMessage(), 'nombre_usuario'))  $msg = "El nombre de usuario ya existe";
                elseif (str_contains($e->getMessage(), 'dni'))         $msg = "El DNI ya está registrado";
                elseif (str_contains($e->getMessage(), 'email'))       $msg = "El email ya está registrado";
                else                                                    $msg = "Ya existe un usuario con esos datos";
            }
            echo json_encode(["success" => false, "message" => $msg]);
        }

        exit;
    }

    // =========================
    // EDIT USER
    // =========================
    public function editUser()
    {
        $this->checkAuth();
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