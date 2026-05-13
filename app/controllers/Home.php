<?php

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/FichajeModel.php';

class Home extends Controller
{
    public function index()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['id_usuario'])) {
            header("Location: " . RUTA_URL . "/login");
            exit;
        }

        $db = new Database();
        $conexion = $db->conectar();

        $modelo = new FichajeModelo($conexion);

        $esAdmin = isset($_SESSION['rol']) && $_SESSION['rol'] === 'Administrador';

        $tiempoDescanso = 0;
        $horasSemana    = 0;
        $fichajeActivo  = null;

        if (!$esAdmin) {
            $id = $_SESSION['id_usuario'];
            $tiempoDescanso = $modelo->getTiempoDescansoHoy($id);
            $horasSemana    = $modelo->getHorasSemanalesUsuario($id);
            $fichajeActivo  = $modelo->obtenerFichajeActivo($id);
        }

        $datos = [
            "empleadosActivos" => $modelo->getEmpleadosActivos(),
            "fichajesHoy"      => $modelo->getFichajesHoy(),
            "horasHoy"         => $modelo->getHorasHoy(),
            "horasSemana"      => $horasSemana,
            "retrasosHoy"      => $modelo->getRetrasosHoy(),
            "tiempoDescanso"   => $tiempoDescanso,
            "fichajeActivo"    => $fichajeActivo,
            "title"            => "Inicio"
        ];

        $this->load_view('inicio', $datos);
    }
}