<?php

require_once __DIR__ . '/../config/Database.php';

class Empleado extends Controller
{
    public function __construct()
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
        $datos = ["title" => "Empleados"];
        $this->load_view('empleados', $datos);
    }
}
