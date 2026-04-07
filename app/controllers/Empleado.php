<?php

session_start();
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/FichajeModel.php';

class Empleado extends Controller {

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

    
    public function index() {

        if (!isset($_SESSION['id_usuario'])) {
            header("Location: /login");
            exit;
        }

        $datos = [
            "title" => "Empleados"
        ];

        $this->load_view('empleados', $datos);

    }
}