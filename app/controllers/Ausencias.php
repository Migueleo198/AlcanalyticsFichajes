<?php

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/BajasModel.php';
require_once __DIR__ . '/../models/AusenciaModel.php';
require_once __DIR__ . '/../models/UserModel.php';

class Ausencias extends Controller
{
    private $conexion;

    public function __construct()
    {
        $this->initSession();
        $this->conexion = $this->getConexion();
    }

    /* =========================
       SESSION / SEGURIDAD
    ========================= */
    private function initSession()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['id_usuario'])) {
            header("Location: " . RUTA_URL . "/login");
            exit;
        }
    }

    private function checkAdmin()
    {
        if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'Administrador') {
            header("Location: " . RUTA_URL . "/home");
            exit;
        }
    }

    private function getConexion()
    {
        $db = new Database();
        return $db->conectar();
    }


    /* =========================
       VISTA PRINCIPAL
    ========================= */
    public function index()
    {
        $bajasModel = new BajasModel($this->conexion);
        $ausenciasModel = new AusenciaModel($this->conexion);
        $userModel = new UserModel($this->conexion);

        $datos = [
            // 🔴 REMUNERADAS
            'motivos_remunerados' => $bajasModel->obtenerMotivosBaja(),

            // 🔵 NO REMUNERADAS
            'ausencias' => $ausenciasModel->getAll(),
            'motivos_no_remunerados' => $ausenciasModel->getMotivos(),

            // 👥 COMUNES
            'usuarios' => $userModel->getUsuarios(),
            'title' => 'Gestión de ausencias'
        ];

        $this->load_view('ausencia', $datos);
    }


    /* =========================
       🔴 AUSENCIAS REMUNERADAS
    ========================= */

    public function solicitarRemunerada()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;

        $bajasModel = new BajasModel($this->conexion);

        $data = [
            'id_usuario' => $_SESSION['id_usuario'],
            'id_motivo' => $_POST['id_motivo'] ?? null,
            'fecha_inicio' => $_POST['fecha_inicio'] ?? null,
            'fecha_fin' => !empty($_POST['fecha_fin']) ? $_POST['fecha_fin'] : null
        ];

        $bajasModel->insertarBaja($data);

        header("Location: " . RUTA_URL . "/Ausencias?ok=1");
        exit;
    }


    public function gestionarRemuneradas()
    {
        $this->checkAdmin();

        $bajasModel = new BajasModel($this->conexion);

        $datos = [
            'title' => 'Ausencias remuneradas',
            'bajas' => $bajasModel->obtenerTodasBajas()
        ];

        $this->load_view('bajas_admin', $datos);
    }


    public function cambiarEstado($id, $estado)
    {
        $this->checkAdmin();

        $validos = ['aprobada', 'rechazada'];

        if (!in_array($estado, $validos)) {
            header("Location: " . RUTA_URL . "/Ausencias/gestionarRemuneradas");
            exit;
        }

        $bajasModel = new BajasModel($this->conexion);
        $bajasModel->actualizarEstado($id, $estado);

        header("Location: " . RUTA_URL . "/Ausencias/gestionarRemuneradas");
        exit;
    }


    /* =========================
       🔵 AUSENCIAS NO REMUNERADAS
    ========================= */

    public function crearNoRemunerada()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;

        $modelo = new AusenciaModel($this->conexion);

        $modelo->crear(
            $_POST['id_usuario'] ?? null,
            $_POST['id_motivo'] ?? null,
            $_POST['fecha_inicio'] ?? null,
            $_POST['fecha_fin'] ?? null
        );

        echo json_encode(["ok" => true]);
    }


    public function eliminarNoRemunerada()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;

        $modelo = new AusenciaModel($this->conexion);
        $modelo->eliminar($_POST['id']);

        echo json_encode(["ok" => true]);
    }
}