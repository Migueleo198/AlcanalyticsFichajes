<?php

require_once __DIR__ . '/../models/UserModel.php';

class Login extends Controller
{
    
    private $userModel;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $this->userModel = new UserModel();
    }

    public function index()
    {
        $datos = [
            "title" => "login"
        ];

        $this->load_view('login', $datos);
    }

    public function login()
    {
        header('Content-Type: application/json');

        $raw = file_get_contents("php://input");
        $data = json_decode($raw, true);

        if (!$data) {
            $data = $_POST;
        }

        $usuario = $data['usuario'] ?? '';
        $contrasenya = $data['contrasenya'] ?? '';

        if (!$usuario || !$contrasenya) {
            echo json_encode([
                'success' => false,
                'message' => 'Datos vacíos'
            ]);
            exit;
        }

        $user = $this->userModel->login($usuario, $contrasenya);

        if ($user) {
            // 🔐 GUARDAR SOLO LO NECESARIO
            $_SESSION['id_usuario'] = $user['id_usuario'];
            $_SESSION['nombre'] = $user['nombre'];
            $_SESSION['rol'] = $user['rol'];

            echo json_encode([
                'success' => true,
                'usuario' => $user['nombre']
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Usuario o contraseña incorrectos'
            ]);
        }

        exit;
    }

    // 🔓 LOGOUT
    public function logout()
    {
        session_start();
        session_unset();
        session_destroy();

        header("Location: /login");
        exit;
    }
}