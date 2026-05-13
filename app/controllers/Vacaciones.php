<?php

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/VacacionesModel.php';

class Vacaciones extends Controller
{
    private $vacacionesModel;

    public function __construct()
    {
        $db = new Database();
        $conexion = $db->conectar();
        $this->vacacionesModel = new VacacionesModel($conexion);
    }

    private function checkAuth()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['id_usuario'])) {
            header("Location: " . RUTA_URL . "/login");
            exit;
        }
    }

    public function index()
    {
        $this->checkAuth();

        $esAdmin  = isset($_SESSION['rol']) && $_SESSION['rol'] === 'Administrador';
        $id       = $_SESSION['id_usuario'];

        $vacaciones = $esAdmin
            ? $this->vacacionesModel->getAll()
            : $this->vacacionesModel->getByUsuario($id);

        $resumen = $this->vacacionesModel->getResumen($esAdmin ? null : $id);

        $datos = [
            'title'      => 'Vacaciones',
            'vacaciones' => $vacaciones,
            'resumen'    => $resumen,
            'esAdmin'    => $esAdmin,
        ];

        $this->load_view('vacaciones', $datos);
    }

    public function getVacaciones()
    {
        $this->checkAuth();

        $vacaciones = $this->vacacionesModel->getAll();
        $eventos    = [];

        foreach ($vacaciones as $v) {
            $eventos[] = [
                'title' => $v['nombre'] . ' ' . $v['apellidos'],
                'start' => $v['fecha_inicio'],
                'end'   => date('Y-m-d', strtotime($v['fecha_fin'] . ' +1 day')),
                'estado' => $v['estado'],
            ];
        }

        header('Content-Type: application/json');
        echo json_encode($eventos);
    }

    public function add()
    {
        $this->checkAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: " . RUTA_URL . "/Vacaciones/index");
            exit;
        }

        $id_usuario = $_SESSION['id_usuario'];
        $inicio     = $_POST['fecha_inicio'] ?? '';
        $fin        = $_POST['fecha_fin']    ?? '';
        $comentario = $_POST['comentario']   ?? '';

        if (!$inicio || !$fin || $inicio > $fin) {
            header("Location: " . RUTA_URL . "/Vacaciones/index?error=fechas");
            exit;
        }

        if ($this->vacacionesModel->haySolapamiento($id_usuario, $inicio, $fin)) {
            header("Location: " . RUTA_URL . "/Vacaciones/index?error=solapamiento");
            exit;
        }

        $this->vacacionesModel->add([
            'id_usuario'   => $id_usuario,
            'fecha_inicio' => $inicio,
            'fecha_fin'    => $fin,
            'comentario'   => $comentario,
        ]);

        header("Location: " . RUTA_URL . "/Vacaciones/index?ok=1");
    }

    public function updateEstado()
    {
        $this->checkAuth();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id     = $_POST['id_vacacion'] ?? null;
            $estado = $_POST['estado']      ?? null;

            if ($id && in_array($estado, ['aprobada', 'rechazada', 'pendiente'])) {
                $this->vacacionesModel->updateEstado($id, $estado);
            }
        }

        header("Location: " . RUTA_URL . "/Vacaciones/index");
    }

    public function delete($id)
    {
        $this->checkAuth();
        $this->vacacionesModel->delete($id);
        header("Location: " . RUTA_URL . "/Vacaciones/index");
    }
}
