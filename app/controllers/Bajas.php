<?php

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/BajasModel.php';

class Bajas extends Controller
{
    private function checkSession()
    {
        session_start();

        if (!isset($_SESSION['id_usuario'])) {
            header("Location: " . RUTA_URL . "/login");
            exit;
        }
    }

    private function checkAdmin()
    {
    if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'Administrador') {
        header("Location: " . RUTA_URL . "/home");
        exit;
    }
    }

    
    public function index()
    {
        $this->checkSession();

        $db = new Database();
        $conexion = $db->conectar();

        $bajasModel = new BajasModel($conexion);
        $motivos = $bajasModel->obtenerMotivosBaja();

        $datos = [
            'title' => 'Solicitar Baja',
            'motivos' => $motivos
        ];

        $this->load_view('bajas', $datos);
    }

    
    public function solicitar()
    {
        $this->checkSession();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $db = new Database();
            $conexion = $db->conectar();

            $bajasModel = new BajasModel($conexion);

            $data = [
                'id_usuario' => $_SESSION['id_usuario'],
                'id_motivo' => $_POST['id_motivo'],
                'fecha_inicio' => $_POST['fecha_inicio'],
                'fecha_fin' => !empty($_POST['fecha_fin']) ? $_POST['fecha_fin'] : null
            ];

            $bajasModel->insertarBaja($data);

            header("Location: " . RUTA_URL . "/Bajas?ok=1");
            exit;
        }
    }

   
    public function visualizar()
{
    $this->checkSession();

    
    if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'Administrador') {
        header("Location: " . RUTA_URL . "/Bajas/gestionar");
        exit;
    }

    $db = new Database();
    $conexion = $db->conectar();

    $bajasModel = new BajasModel($conexion);
    $bajas = $bajasModel->obtenerBajasPorUsuario($_SESSION['id_usuario']);

    $datos = [
        'title' => 'Mis bajas',
        'bajas' => $bajas
    ];

    $this->load_view('bajas_visualizar', $datos);
}

    
    public function gestionar()
    {
        $this->checkSession();
        $this->checkAdmin(); 

        $db = new Database();
        $conexion = $db->conectar();

        $bajasModel = new BajasModel($conexion);
        $bajas = $bajasModel->obtenerTodasBajas();

        $datos = [
            'title' => 'Gestión de bajas',
            'bajas' => $bajas
        ];

        $this->load_view('bajas_admin', $datos);
    }

    
    public function cambiarEstado($id, $estado)
    {
        $this->checkSession();
        $this->checkAdmin(); 

        
        $estadosValidos = ['aprobada', 'rechazada'];

        if (!in_array($estado, $estadosValidos)) {
            header("Location: " . RUTA_URL . "/Bajas/gestionar");
            exit;
        }

        $db = new Database();
        $conexion = $db->conectar();

        $bajasModel = new BajasModel($conexion);
        $bajasModel->actualizarEstado($id, $estado);

        header("Location: " . RUTA_URL . "/Bajas/gestionar");
        exit;
    }
}