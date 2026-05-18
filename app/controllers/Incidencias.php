<?php

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/IncidenciaModel.php';

class Incidencias extends Controller
{
    private $model;

    public function __construct()
    {
        $db = new Database();
        $conexion = $db->conectar();
        $this->model = new IncidenciaModel($conexion);
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

        $datos = [
            "title" => "Incidencias",
        ];

        $this->load_view('registroIncidencias', $datos);
    }

    public function getIncidencias()
    {
        header('Content-Type: application/json');
        $this->checkAuth();

        try {
            $incidencias = $this->model->getIncidencias();

            echo json_encode([
                "success" => true,
                "data" => $incidencias
            ]);
        } catch (Throwable $e) {
            echo json_encode([
                "success" => false,
                "message" => $e->getMessage()
            ]);
        }
    }

    public function getIncidencia($id)
    {
        header('Content-Type: application/json');
        $this->checkAuth();

        try {
            $incidencia = $this->model->getIncidencia($id);

            echo json_encode([
                "success" => true,
                "data" => $incidencia
            ]);
        } catch (Throwable $e) {
            echo json_encode([
                "success" => false,
                "message" => $e->getMessage()
            ]);
        }
    }

    public function getByFichaje()
    {
        header('Content-Type: application/json');
        $this->checkAuth();

        $id = $_GET['id'] ?? null;

        if (!$id) {
            echo json_encode([
                "success" => false,
                "data" => []
            ]);
            return;
        }

        try {
            $incidencias = $this->model->getByFichaje($id);

            echo json_encode([
                "success" => true,
                "data" => $incidencias
            ]);
        } catch (Throwable $e) {
            echo json_encode([
                "success" => false,
                "message" => $e->getMessage()
            ]);
        }
    }

    public function updateIncidencia()
    {
        $this->checkAuth();

        if (empty($_POST)) {
            echo json_encode([
                "success" => false,
                "message" => "Datos vacíos"
            ]);
            return;
        }

        $required = ['id', 'id_fichaje', 'mensaje', 'respuesta', 'estado', 'fecha'];

        foreach ($required as $field) {
            if (!isset($_POST[$field])) {
                echo json_encode([
                    "success" => false,
                    "message" => "Falta el campo: $field"
                ]);
                return;
            }
        }

        try {
            $ok = $this->model->updateIncidencia($_POST['id'], [
                'id_fichaje' => $_POST['id_fichaje'],
                'mensaje'    => $_POST['mensaje'],
                'respuesta'  => $_POST['respuesta'],
                'estado'     => $_POST['estado'],
                'fecha'      => $_POST['fecha']
            ]);

            if ($ok) {
                header("Location: " . RUTA_URL . "/incidencias/index");
                exit;
            }

            echo json_encode([
                "success" => false,
                "message" => "Error al actualizar incidencia"
            ]);
        } catch (Throwable $e) {
            echo json_encode([
                "success" => false,
                "message" => $e->getMessage()
            ]);
        }
    }

    public function addIncidencia()
    {
        $this->checkAuth();

        if (empty($_POST)) {
            echo json_encode([
                "success" => false,
                "message" => "Datos vacíos"
            ]);
            return;
        }

        $required = ['id_fichaje', 'mensaje', 'respuesta', 'estado', 'fecha'];

        foreach ($required as $field) {
            if (!isset($_POST[$field])) {
                echo json_encode([
                    "success" => false,
                    "message" => "Falta el campo: $field"
                ]);
                return;
            }
        }

        try {
            $ok = $this->model->addIncidencia([
                'id_fichaje' => $_POST['id_fichaje'],
                'mensaje'    => $_POST['mensaje'],
                'respuesta'  => $_POST['respuesta'],
                'estado'     => $_POST['estado'],
                'fecha'      => $_POST['fecha']
            ]);

            if ($ok) {
                header("Location: " . RUTA_URL . "/home/index");
                exit;
            }

            echo json_encode([
                "success" => false,
                "message" => "Error al crear incidencia"
            ]);
        } catch (Throwable $e) {
            echo json_encode([
                "success" => false,
                "message" => $e->getMessage()
            ]);
        }
    }

    public function removeIncidencia()
    {
        $this->checkAuth();

        header('Content-Type: application/json');

        $id = $_POST['id'] ?? null;

        if (!$id) {
            echo json_encode([
                "success" => false,
                "message" => "ID no proporcionado"
            ]);
            return;
        }

        try {
            $ok = $this->model->removeIncidencia($id);

            if ($ok) {
                header("Location: " . RUTA_URL . "/incidencias/index");
                exit;
            }

            echo json_encode([
                "success" => false,
                "message" => "Error al eliminar incidencia"
            ]);
        } catch (Throwable $e) {
            echo json_encode([
                "success" => false,
                "message" => $e->getMessage()
            ]);
        }
    }
}