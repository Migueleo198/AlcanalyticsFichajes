<?php

require_once __DIR__ . '/../models/JornadasModel.php';
require_once __DIR__ . '/../config/Database.php';

class AsignarJornadas extends Controller
{
    private function model()
    {
        $db = new Database();
        $conn = $db->conectar();
        return new JornadasModel($conn);
    }

    // =========================
    // AUTH GUARD
    // =========================
    private function checkAdmin()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['id_usuario'])) {
            header("Location: " . RUTA_URL . "/login");
            exit;
        }
        if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'Administrador') {
            header("Location: " . RUTA_URL . "/home/index");
            exit;
        }
    }

    // =========================
    // INDEX VIEW
    // =========================
    public function index()
    {
        $this->checkAdmin();
        $model = $this->model();

        $datos = [
            'title'    => 'Asignar Jornadas',
            'jornadas' => $model->getJornadas(),
            'usuarios' => $model->getUsuarios(),
        ];

        $this->load_view('asignar_jornadas', $datos);
    }

    // =========================
    // GET ALL JORNADAS
    // =========================
    public function getJornadas()
    {
        header('Content-Type: application/json; charset=utf-8');

        try {
            $model = $this->model();

            echo json_encode([
                "success" => true,
                "data" => $model->getJornadas()
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

    // =========================
    // GET JORNADA BY ID
    // =========================
    public function getJornada()
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

        try {
            $model = $this->model();
            $jornada = $model->getJornadaById($id);

            if (!$jornada) {
                http_response_code(404);
                echo json_encode([
                    "success" => false,
                    "message" => "Jornada no encontrada"
                ]);
                exit;
            }

            echo json_encode([
                "success" => true,
                "data" => $jornada
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

    // =========================
    // CREATE JORNADA
    // =========================
    public function addJornada()
    {
        header('Content-Type: application/json; charset=utf-8');

        try {
            $model = $this->model();

            $id = $model->addJornada([
                'id_usuario'   => $_POST['id_usuario'] ?? null,
                'horas_dia'    => $_POST['horas_dia'] ?? 0,
                'horas_semana' => $_POST['horas_semana'] ?? 0,
                'fecha_inicio' => $_POST['fecha_inicio'] ?? null,
                'fecha_fin'    => $_POST['fecha_fin'] ?? null
            ]);

            echo json_encode([
                "success" => (bool)$id,
                "id_jornada" => $id
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

    // =========================
    // EDIT JORNADA
    // =========================
    public function editJornada()
    {
        header('Content-Type: application/json; charset=utf-8');

        $datos = [
            'id'           => $_POST['id'] ?? null,
            'id_usuario'   => $_POST['id_usuario'] ?? null,
            'horas_dia'    => $_POST['horas_dia'] ?? 0,
            'horas_semana' => $_POST['horas_semana'] ?? 0,
            'fecha_inicio' => $_POST['fecha_inicio'] ?? null,
            'fecha_fin'    => $_POST['fecha_fin'] ?? null
        ];

        if (!$datos['id']) {
            echo json_encode([
                "success" => false,
                "message" => "ID requerido"
            ]);
            exit;
        }

        try {
            $model = $this->model();

            $ok = $model->editJornada($datos);

            echo json_encode([
                "success" => (bool)$ok
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

    // =========================
    // DELETE JORNADA
    // =========================
    public function removeJornada()
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

        try {
            $model = $this->model();

            echo json_encode([
                "success" => (bool)$model->removeJornada($id)
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