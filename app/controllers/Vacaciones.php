<?php

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/VacacionesModel.php';

class Vacaciones extends Controller
{
    private $vacacionesModel;

    public function __construct() {
    $db = new Database();
    $conexion = $db->conectar();

    $this->vacacionesModel = new VacacionesModel($conexion);
    }

    // 🔹 Vista principal
    public function index()
    {
        session_start();

        if (!isset($_SESSION['id_usuario'])) {
            header("Location: " . RUTA_URL . "/login");
            exit;
        }

        $datos = [
            'title' => 'Vacaciones'
        ];

        $this->load_view('vacaciones', $datos);
    }

    
    public function getVacaciones() {
        session_start();

        $vacaciones = $this->vacacionesModel->getAll();

        $eventos = [];

        foreach ($vacaciones as $v) {
            $eventos[] = [
                'title' => $v['nombre'] . ' ' . $v['apellidos'],
                'start' => $v['fecha_inicio'],
                'end' => date('Y-m-d', strtotime($v['fecha_fin'] . ' +1 day')),
                'estado' => $v['estado']
            ];
        }

        header('Content-Type: application/json');
        echo json_encode($eventos);
    }

   
    public function add() {
        session_start();

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $id_usuario = $_SESSION['id_usuario'];
            $inicio = $_POST['fecha_inicio'];
            $fin = $_POST['fecha_fin'];
            $comentario = $_POST['comentario'] ?? '';

            if ($inicio > $fin) {
                die("La fecha inicio no puede ser mayor que la fecha fin");
            }

            if ($this->vacacionesModel->haySolapamiento($id_usuario, $inicio, $fin)) {
                die("Ya tienes vacaciones en esas fechas");
            }

            $this->vacacionesModel->add([
                'id_usuario' => $id_usuario,
                'fecha_inicio' => $inicio,
                'fecha_fin' => $fin,
                'comentario' => $comentario
            ]);

            header("Location: " . RUTA_URL . "/vacaciones");
        }
    }

   
    public function updateEstado() {
        session_start();

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $id = $_POST['id_vacacion'];
            $estado = $_POST['estado'];

            $this->vacacionesModel->updateEstado($id, $estado);

            header("Location: " . RUTA_URL . "/vacaciones");
        }
    }

    
    public function delete($id) {
        session_start();

        $this->vacacionesModel->delete($id);

        header("Location: " . RUTA_URL . "/vacaciones");
    }
}