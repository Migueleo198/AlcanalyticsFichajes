<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/FichajeModel.php';

class Fichaje extends Controller {

    private $modelo;

    public function __construct() {

        // Conexión BD
        $db = new Database();
        $conexion = $db->conectar();

        // Seguridad sesión
        if (!isset($_SESSION['id_usuario'])) {
            header("Location: " . RUTA_URL . "/login");
            exit;
        }

        $this->modelo = new FichajeModelo($conexion);
    }

    // ========================
    // VISTA PRINCIPAL
    // ========================
    public function index() {

        $id_usuario = $_SESSION['id_usuario'];

        $fichaje = $this->modelo->obtenerFichajeActivo($id_usuario);

        $descansos = [];
        $enDescanso = false;

        if ($fichaje && isset($fichaje['id_fichaje'])) {

            $id_fichaje = $fichaje['id_fichaje'];

            $descansos = $this->modelo->obtenerDescansos($id_fichaje);

            $descanso = $this->modelo->estaEnDescanso($id_fichaje);

            $enDescanso = !empty($descanso);
        }

        $datos = [
            "title" => "Fichajes",
            "fichaje" => $fichaje,
            "descansos" => $descansos,
            "enDescanso" => $enDescanso
        ];

        $this->load_view('fichajes', $datos);
    }

    // ========================
    // DETALLE GLOBAL DE FICHAJES
    // ========================
    public function detalle() {

        $id_usuario = $_SESSION['id_usuario'] ?? null;
        $rol = trim($_SESSION['rol'] ?? '');

        $fichajes = [];

        // Normalización segura del rol
        if ($rol === 'Administrador') {

            $fichajes = $this->modelo->getFichajesAdminDetalle();

        } else {

            $fichajes = $this->modelo->getFichajesUsuarioDetalle($id_usuario);
        }

        // Seguridad: siempre array
        if (!is_array($fichajes)) {
            $fichajes = [];
        }

        $datos = [
            "title" => "Detalle de fichajes",
            "fichajes" => $fichajes
        ];

        $this->load_view("fichaje_detalle", $datos);
    }

    // ========================
    // LISTADO JSON
    // ========================
    public function listFichajes() {

        header('Content-Type: application/json');

        $id_usuario = $_SESSION['id_usuario'];
        $rol = $_SESSION['rol'];

        $fichajes = $this->modelo->getFichajes($id_usuario, $rol);

        echo json_encode([
            "status" => (!empty($fichajes) ? "success" : "empty"),
            "data" => $fichajes ?: []
        ]);
    }

    // ========================
    // CHECK SESSION
    // ========================
    private function checkSession() {

        if (!isset($_SESSION['id_usuario'])) {
            echo json_encode([
                'ok' => false,
                'error' => 'no_session'
            ]);
            exit;
        }
    }

    // ========================
    // INICIAR FICHAJE
    // ========================
    public function iniciar() {

        header('Content-Type: application/json');
        $this->checkSession();

        $this->modelo->iniciarFichaje($_SESSION['id_usuario']);

        echo json_encode(['ok' => true]);
    }

    // ========================
    // PAUSAR
    // ========================
    public function pausar() {

        header('Content-Type: application/json');
        $this->checkSession();

        $fichaje = $this->modelo->obtenerFichajeActivo($_SESSION['id_usuario']);

        if ($fichaje && isset($fichaje['id_fichaje'])) {

            $motivo = $_POST['motivo'] ?? $_GET['motivo'] ?? null;
            $motivo = $motivo ? trim($motivo) : null;

            $this->modelo->iniciarDescanso(
                $fichaje['id_fichaje'],
                $motivo
            );
        }

        echo json_encode(['ok' => true]);
    }

    // ========================
    // REANUDAR
    // ========================
    public function reanudar() {

        header('Content-Type: application/json');
        $this->checkSession();

        $fichaje = $this->modelo->obtenerFichajeActivo($_SESSION['id_usuario']);

        if ($fichaje && isset($fichaje['id_fichaje'])) {
            $this->modelo->finalizarDescanso($fichaje['id_fichaje']);
        }

        echo json_encode(['ok' => true]);
    }

    // ========================
    // FINALIZAR
    // ========================
    public function finalizar() {

        header('Content-Type: application/json');
        $this->checkSession();

        $fichaje = $this->modelo->obtenerFichajeActivo($_SESSION['id_usuario']);

        if ($fichaje && isset($fichaje['id_fichaje'])) {
            $this->modelo->finalizarFichaje($fichaje['id_fichaje']);
        }

        echo json_encode(['ok' => true]);
    }

    public function horasTotales() {

    header('Content-Type: application/json');
    $this->checkSession();

    $id_usuario = $_SESSION['id_usuario'];

    $segundos = $this->modelo->getHorasTotalesUsuario($id_usuario);

    // Convertir a formato H:i:s
    $horas = floor($segundos / 3600);
    $minutos = floor(($segundos % 3600) / 60);
    $segundos_restantes = $segundos % 60;

    $tiempo_formateado = sprintf('%02d:%02d:%02d', $horas, $minutos, $segundos_restantes);

    echo json_encode([
        'ok' => true,
        'segundos' => $segundos,
        'tiempo' => $tiempo_formateado
    ]);
    }

    // ========================
    // GUARDAR HORAS EXTRA MANUALMENTE (solo admin)
    // ========================
    public function setHorasExtra()
    {
        header('Content-Type: application/json');
        $this->checkSession();

        if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'Administrador') {
            echo json_encode(['ok' => false, 'error' => 'No autorizado']);
            exit;
        }

        $id_fichaje  = (int)($_POST['id_fichaje']  ?? 0);
        $horas_extra = isset($_POST['horas_extra']) ? (float)$_POST['horas_extra'] : null;

        if (!$id_fichaje) {
            echo json_encode(['ok' => false, 'error' => 'ID de fichaje inválido']);
            exit;
        }

        $ok = ($horas_extra === null || $_POST['horas_extra'] === '')
            ? $this->modelo->resetHorasExtra($id_fichaje)
            : $this->modelo->setHorasExtra($id_fichaje, $horas_extra);

        echo json_encode(['ok' => (bool)$ok]);
        exit;
    }
}