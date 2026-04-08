<?php

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/RegistroTareasModelo.php';
require_once __DIR__ . '/../models/FichajeModel.php';

class RegistroTareas extends Controller
{
    
    public function index()
    {
        session_start();

        if (!isset($_SESSION['id_usuario'])) {
            header("Location: " . RUTA_URL . "/login");
            exit;
        }

        $db = new Database();
        $conexion = $db->conectar();

        $modeloFichaje = new FichajeModelo($conexion);
        $fichaje = $modeloFichaje->obtenerFichajeActivo($_SESSION['id_usuario']);

        $datos = [
            "title" => "Tareas",
            "fichajeActivo" => $fichaje
        ];

        $this->load_view('registroTareas', $datos);
    }

    // =========================
    // GET ALL (JSON para JS)
    // =========================
    public function getTasks()
    {
        session_start();

        if (!isset($_SESSION['id_usuario'])) {
            header("Location: " . RUTA_URL . "/login");
            exit;
        }

        header('Content-Type: application/json');

        $db = new Database();
        $conexion = $db->conectar();

        $modelo = new RegistroTareasModelo($conexion);
        $tareas = $modelo->getTasks();

        echo json_encode([
            "success" => true,
            "data" => $tareas
        ]);
        exit;
    }

    // =========================
    // CREATE
    // =========================
    public function create()
    {
        session_start();

        if (!isset($_SESSION['id_usuario'])) {
            header("Location: " . RUTA_URL . "/login");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $data = $_POST;

            // ✅ añadir usuario desde sesión
            $data['id_usuario'] = $_SESSION['id_usuario'];

            $db = new Database();
            $conexion = $db->conectar();

            $modelo = new RegistroTareasModelo($conexion);
            $modelo->createTask($data);

            // ✅ redirección a la vista (no JSON)
            header("Location: " . RUTA_URL . "/RegistroTareas");
            exit;
        }
    }

    // =========================
    // UPDATE
    // =========================
    public function update()
    {
        session_start();

        if (!isset($_SESSION['id_usuario'])) {
            header("Location: " . RUTA_URL . "/login");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $data = $_POST;

            $db = new Database();
            $conexion = $db->conectar();

            $modelo = new RegistroTareasModelo($conexion);
            $modelo->updateTask($data);

            header("Location: " . RUTA_URL . "/RegistroTareas");
            exit;
        }
    }

    // =========================
    // DELETE
    // =========================
    public function delete()
    {
        session_start();

        if (!isset($_SESSION['id_usuario'])) {
            header("Location: " . RUTA_URL . "/login");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $id = $_POST['id'] ?? null;

            if ($id) {
                $db = new Database();
                $conexion = $db->conectar();

                $modelo = new RegistroTareasModelo($conexion);
                $modelo->deleteTask($id);
            }

            header("Location: " . RUTA_URL . "/RegistroTareas");
            exit;
        }
    }
}