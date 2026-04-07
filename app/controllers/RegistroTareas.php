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

    $modelo = new FichajeModelo($conexion);
    $fichaje = $modelo->obtenerFichajeActivo($_SESSION['id_usuario']);

    $datos = [
        "title" => "Tareas",
        "fichajeActivo" => $fichaje
    ];

    $this->load_view('registroTareas', $datos);
}

   public function getTasks(){
    session_start();

    header('Content-Type: application/json');

    $db = new Database();
    $conexion = $db->conectar();

    $modelo = new RegistroTareasModelo($conexion);
    $tareas = $modelo->getTasks();

    if ($tareas) {
        echo json_encode([
            "success" => true,
            "data" => $tareas
        ]);
    } else {
        echo json_encode([
            "success" => false,
            "data" => []
        ]);
    }

    exit;
}
}