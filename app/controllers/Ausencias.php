<?php

require_once __DIR__ . '/../models/AusenciaModel.php';
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../config/Database.php';

class Ausencias extends Controller
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

        $modelo = new AusenciaModel($conexion);
        $modeloUser = new UserModel($conexion);


        $datos = [
             "ausencias" => $modelo->getAll(),
             "motivos" => $modelo->getMotivos(),
             "usuarios" => $modeloUser->getUsuarios(),
             "title" => "Gestión de Ausencias"
            ];

        $this->load_view('ausencia', $datos);
    }

    public function crear()
    {
        session_start();

        $db = new Database();
        $conexion = $db->conectar();

        $modelo = new AusenciaModel($conexion);

        $idUsuario = $_POST['id_usuario'];
        $idMotivo = $_POST['id_motivo'];
        $fechaInicio = $_POST['fecha_inicio'];
        $fechaFin = $_POST['fecha_fin'];

        $modelo->crear($idUsuario, $idMotivo, $fechaInicio, $fechaFin);

        echo json_encode(["ok" => true]);
    }

    public function eliminar()
    {
        $db = new Database();
        $conexion = $db->conectar();

        $modelo = new AusenciaModel($conexion);

        $id = $_POST['id'];

        $modelo->eliminar($id);

        echo json_encode(["ok" => true]);
    }
}