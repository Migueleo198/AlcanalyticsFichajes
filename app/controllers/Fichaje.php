<?php

session_start();
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/FichajeModel.php';

class Fichaje extends Controller {

    private $modelo;

    public function __construct() {
        $db = new Database();
        $conexion = $db->conectar();

        if (!isset($_SESSION['id_usuario'])) {
            header("Location: " . RUTA_URL . "/login");
            exit;
        }

        $this->modelo = new FichajeModelo($conexion);
    }

    // ========================
    // VISTA
    // ========================
    public function index() {

    $id_usuario = $_SESSION['id_usuario'];

    $fichaje = $this->modelo->obtenerFichajeActivo($id_usuario);

    $enDescanso = false;
    $descansos = [];

    if ($fichaje) {

        $id_fichaje = $fichaje['id_fichaje'];

       
        $descansos = $this->modelo->obtenerDescansos($id_fichaje);

       
        $descanso = $this->modelo->estaEnDescanso($id_fichaje);

        $enDescanso = $descanso ? true : false;
    }

    $datos = [
        "title" => "Fichajes",
        "fichaje" => $fichaje,
        "descansos" => $descansos,   
        "enDescanso" => $enDescanso
    ];

    $this->load_view('fichajes', $datos);
    }

    public function listFichajes(){

    header('Content-Type: application/json');

    $fichajes = $this->modelo->getFichajes();

    if($fichajes && count($fichajes) > 0){
        echo json_encode([
            "status" => "success",
            "data" => $fichajes
        ]);
    } else {
        echo json_encode([
            "status" => "empty",
            "data" => []
        ]);
    }
    }

   

    private function checkSession() {
        if (!isset($_SESSION['id_usuario'])) {
            echo json_encode([
                'ok' => false,
                'error' => 'no_session'
            ]);
            exit;
        }
    }

    public function iniciar() {
        header('Content-Type: application/json');
        $this->checkSession();

        $this->modelo->iniciarFichaje($_SESSION['id_usuario']);

        echo json_encode(['ok' => true]);
    }

    public function pausar() {
        header('Content-Type: application/json');
        $this->checkSession();

        $fichaje = $this->modelo->obtenerFichajeActivo($_SESSION['id_usuario']);

        if ($fichaje) {
            $this->modelo->iniciarDescanso($fichaje['id_fichaje']);
        }

        echo json_encode(['ok' => true]);
    }

    public function reanudar() {
        header('Content-Type: application/json');
        $this->checkSession();

        $fichaje = $this->modelo->obtenerFichajeActivo($_SESSION['id_usuario']);

        if ($fichaje) {
            $this->modelo->finalizarDescanso($fichaje['id_fichaje']);
        }

        echo json_encode(['ok' => true]);
    }

    public function finalizar() {
        header('Content-Type: application/json');
        $this->checkSession();

        $fichaje = $this->modelo->obtenerFichajeActivo($_SESSION['id_usuario']);

        if ($fichaje) {
            $this->modelo->finalizarFichaje($fichaje['id_fichaje']);
        }

        echo json_encode(['ok' => true]);
    }
}