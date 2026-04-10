<?php
require_once '../app/models/UserModel.php';

class User
{
    // =========================
    // LOGIN
    // =========================
    public function login()
    {
        header('Content-Type: application/json');

        $input = json_decode(file_get_contents("php://input"), true);

        $usuario = $input['usuario'] ?? '';
        $contrasenya = $input['contrasenya'] ?? '';

        $model = new UserModel();
        $user = $model->login($usuario, $contrasenya);

        if ($user) {
            echo json_encode([
                "success" => true,
                "usuario" => $user['nombre_usuario'],
                "rol" => $user['rol']
            ]);
        } else {
            echo json_encode([
                "success" => false,
                "message" => "Credenciales incorrectas"
            ]);
        }
    }

    // =========================
    // GET USER BY ID
    // =========================
    public function getUser()
    {
        header('Content-Type: application/json');

        $id = $_GET['id'] ?? null;

        if (!$id) {
            echo json_encode(["success" => false, "message" => "ID requerido"]);
            return;
        }

        $model = new UserModel();
        $usuario = $model->getUsuarioById($id);

        // 👇 aquí añadimos matrículas
        $matriculas = $model->getMatriculasByUsuario($id);

        echo json_encode([
            "success" => true,
            "data" => $usuario,
            "matriculas" => $matriculas
        ]);
    }

    // =========================
    // GET ALL USERS
    // =========================
    public function getUsers()
    {
        header('Content-Type: application/json');

        $model = new UserModel();
        $usuarios = $model->getUsuarios();

        echo json_encode([
            "success" => true,
            "data" => $usuarios
        ]);
    }

    // =========================
    // DELETE USER
    // =========================
    public function removeUser()
    {
        header('Content-Type: application/json');

        if (empty($_POST)) {
            echo json_encode(["success" => false, "message" => "Datos vacíos"]);
            return;
        }

        $model = new UserModel();
        $ok = $model->removeUser($_POST['id']);

        if ($ok) {
            header("Location: /empleado/index");
            exit;
        } else {
            echo json_encode(["success" => false, "message" => "Error al eliminar usuario"]);
        }
    }

    // =========================
    // ADD USER (con matrícula opcional)
    // =========================
    public function addUser()
    {
        header('Content-Type: application/json');

        if (empty($_POST)) {
            echo json_encode(["success" => false, "message" => "Datos vacíos"]);
            return;
        }

        $model = new UserModel();

        $ok = $model->crearUsuario([
            'nombre' => $_POST['nombre'] ?? '',
            'apellidos' => $_POST['apellidos'] ?? '',
            'nombre_usuario' => $_POST['usuario'] ?? '',
            'contraseña' => $_POST['contraseña'] ?? '',
            'dni' => $_POST['dni'] ?? '',
            'telefono' => $_POST['telefono'] ?? '',
            'email' => $_POST['email'] ?? '',
            'rol' => $_POST['rol'] ?? ''
        ]);

        // 👇 si envías matrícula al crear usuario
        if ($ok && !empty($_POST['matricula'])) {
            $model->addMatricula(
                $ok, // id usuario (ajústalo según tu model)
                $_POST['matricula']
            );
        }

        header("Location: /empleado/index");
        exit;
    }

    // =========================
    // EDIT USER
    // =========================
    public function editUser()
    {
        $datos = [
            'id' => $_POST['id'] ?? null,
            'nombre' => $_POST['nombre'] ?? '',
            'apellidos' => $_POST['apellidos'] ?? '',
            'nombre_usuario' => $_POST['usuario'] ?? '',
            'dni' => $_POST['dni'] ?? '',
            'telefono' => $_POST['telefono'] ?? '',
            'email' => $_POST['email'] ?? '',
            'rol' => $_POST['rol'] ?? ''
        ];

        if (!$datos['id']) {
            echo "ID requerido";
            return;
        }

        $model = new UserModel();
        $ok = $model->editUser($datos);

        if ($ok) {
            header("Location: /empleado/index");
            exit;
        } else {
            echo "Error al actualizar";
        }
    }
}