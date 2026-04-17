<?php

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/FichajeModel.php';

class Home extends Controller
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

        // 🔥 TIEMPO DE DESCANSO
        $tiempoDescanso = 0;

        if ($_SESSION['rol'] !== 'Administrador') {
            $tiempoDescanso = $modelo->getTiempoDescansoHoy($_SESSION['id_usuario']);
        }

        // 🔥 DATOS PRINCIPALES
        $datos = [
            "empleadosActivos" => $modelo->getEmpleadosActivos(),
            "fichajesHoy"      => $modelo->getFichajesHoy(),
            "horasHoy"         => $modelo->getHorasHoy(),
            "retrasosHoy"      => $modelo->getRetrasosHoy(),
            "tiempoDescanso"   => $tiempoDescanso,
            "fichajeActivo"    => null,
            "title"            => "Inicio"
        ];

        $this->load_view('inicio', $datos);
    }
}