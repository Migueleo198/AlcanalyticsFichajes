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

        // ✅ Nombre correcto del modelo
        $this->modelo = new JornadasModel($conexion);
    }

    // =========================
    // VISTA NORMAL (SIN JS)
    // =========================
    public function index()
    {
        // ✅ Obtener jornadas
        $jornadas = $this->modelo->getJornadas();

        $datos = [
            'title' => 'Jornadas',
            'jornadas' => $jornadas
        ];

        $this->load_view('jornadas', $datos);
    }

    // =========================
    // API (PARA JS)
    // =========================
    public function getJornadas()
    {
        header('Content-Type: application/json');

        try {

            $jornadas = $this->modelo->getJornadas();

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