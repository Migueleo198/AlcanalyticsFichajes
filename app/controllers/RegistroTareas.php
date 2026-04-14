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

        // ✅ CARGAR TIPOS DESDE EL MODELO
        $modeloTareas = new RegistroTareasModelo($conexion);
        $tipos = $modeloTareas->getTipos();

        $datos = [
            "title" => "Tareas",
            "fichajeActivo" => $fichaje,
            "tipo" => $tipos // ✅ CLAVE
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

        $db = new Database();
        $conexion = $db->conectar();

        $modeloFichaje = new FichajeModelo($conexion);
        $fichaje = $modeloFichaje->obtenerFichajeActivo($_SESSION['id_usuario']);

        if (!$fichaje) {
            header("Location: " . RUTA_URL . "/RegistroTareas?error=no_fichaje");
            exit;
        }

        $data = $_POST;

        // 🔥 FORZAR DATOS REALES DEL SISTEMA
        $data['id_usuario'] = $_SESSION['id_usuario'];
        $data['id_fichaje'] = $fichaje['id_fichaje'];

        $modelo = new RegistroTareasModelo($conexion);
        $modelo->createTask($data);

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

        // =========================
        // ✅ FIX: NORMALIZE id_tipo
        // =========================
        if (!isset($data['id_tipo']) || $data['id_tipo'] === '' || $data['id_tipo'] === '0') {
            $data['id_tipo'] = null;
        }

        // optional: force integer if exists
        if ($data['id_tipo'] !== null) {
            $data['id_tipo'] = (int)$data['id_tipo'];
        }

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

        // =========================
        // ✅ VALIDATE ID
        // =========================
        if ($id === null || $id === '' || !is_numeric($id)) {
            header("Location: " . RUTA_URL . "/RegistroTareas");
            exit;
        }

        $id = (int)$id;

        $db = new Database();
        $conexion = $db->conectar();

        $modelo = new RegistroTareasModelo($conexion);

        // =========================
        // (OPTIONAL BUT STRONGLY RECOMMENDED)
        // CHECK OWNERSHIP / PERMISSION
        // =========================
        $usuarioId = $_SESSION['id_usuario'];

        $stmt = $conexion->prepare("
            SELECT id_usuario 
            FROM Tarea 
            WHERE id_tarea = ?
        ");
        $stmt->execute([$id]);
        $tarea = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$tarea) {
            header("Location: " . RUTA_URL . "/RegistroTareas");
            exit;
        }

        // If NOT admin, only owner can delete
        if ($_SESSION['rol'] !== 'Administrador' && $tarea['id_usuario'] != $usuarioId) {
            header("Location: " . RUTA_URL . "/RegistroTareas");
            exit;
        }

        // =========================
        // DELETE
        // =========================
        $modelo->deleteTask($id);

        header("Location: " . RUTA_URL . "/RegistroTareas");
        exit;
    }
}
}