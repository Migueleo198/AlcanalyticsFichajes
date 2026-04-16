<?php

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/JornadasModel.php';

class Jornadas extends Controller
{
    private $modelo;

    public function __construct()
    {
        session_start();

        if (!isset($_SESSION['id_usuario'])) {
            header("Location: " . RUTA_URL . "/login");
            exit;
        }

        $db = new Database();
        $conexion = $db->conectar();

        $this->modelo = new JornadasModel($conexion);
    }

    public function index()
    {
        $rol = $_SESSION['rol'];
        $userId = $_SESSION['id_usuario'];

        if ($rol === "Administrador") {
            $jornadas = $this->modelo->getJornadas();
        } else {
            $jornadas = $this->modelo->getJornadasByUser($userId);
        }

        $datos = [
            'title' => 'Jornadas',
            'jornadas' => $jornadas
        ];

        $this->load_view('jornadas', $datos);
    }

    public function getJornadas()
    {
        header('Content-Type: application/json');

        try {

            $rol = $_SESSION['rol'];
            $userId = $_SESSION['id_usuario'];

            if ($rol === "Administrador") {
                $jornadas = $this->modelo->getJornadas();
            } else {
                $jornadas = $this->modelo->getJornadasByUser($userId);
            }

            echo json_encode([
                "success" => true,
                "data" => $jornadas
            ]);

        } catch (Exception $e) {

            echo json_encode([
                "success" => false,
                "error" => $e->getMessage()
            ]);
        }
    }
}