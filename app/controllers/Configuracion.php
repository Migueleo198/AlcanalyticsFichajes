<?php

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/ConfiguracionModel.php';
require_once __DIR__ . '/../models/UserModel.php';

class Configuracion extends Controller
{
    private function checkAuth()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['id_usuario'])) {
            header("Location: " . RUTA_URL . "/login");
            exit;
        }
    }

    private function checkAdmin()
    {
        $this->checkAuth();
        if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'Administrador') {
            header("Location: " . RUTA_URL . "/home/index");
            exit;
        }
    }

    private function getModelo(): ConfiguracionModel
    {
        $db = new Database();
        return new ConfiguracionModel($db->conectar());
    }

    // ============================================================
    // VISTA PRINCIPAL
    // ============================================================
    public function index()
    {
        $this->checkAuth();

        $modelo    = $this->getModelo();
        $userModel = new UserModel();
        $esAdmin   = isset($_SESSION['rol']) && $_SESSION['rol'] === 'Administrador';

        $datos = [
            'title'       => 'Configuración',
            'perfil'      => $userModel->getUsuarioById($_SESSION['id_usuario']),
            'config'      => $esAdmin ? $modelo->getAll() : [],
            'tipos'       => $esAdmin ? $modelo->getTiposTarea() : [],
            'esAdmin'     => $esAdmin,
            'tab'         => $_GET['tab'] ?? 'cuenta',
            'ok'          => $_GET['ok']  ?? null,
            'error'       => $_GET['err'] ?? null,
        ];

        $this->load_view('configuracion', $datos);
    }

    // ============================================================
    // ACTUALIZAR PERFIL
    // ============================================================
    public function actualizarPerfil()
    {
        $this->checkAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: " . RUTA_URL . "/Configuracion/index?tab=cuenta");
            exit;
        }

        $userModel = new UserModel();
        $id        = (int) $_SESSION['id_usuario'];

        $ok = $userModel->actualizarPerfil($id, [
            'nombre'           => trim($_POST['nombre']           ?? ''),
            'apellidos'        => trim($_POST['apellidos']        ?? ''),
            'email'            => trim($_POST['email']            ?? ''),
            'telefono'         => trim($_POST['telefono']         ?? ''),
            'fecha_nacimiento' => $_POST['fecha_nacimiento']      ?? '',
        ]);

        // Actualizar nombre en sesión
        if ($ok) {
            $_SESSION['nombre'] = trim($_POST['nombre'] ?? $_SESSION['nombre']);
        }

        $destino = $ok ? 'ok=perfil' : 'err=perfil';
        header("Location: " . RUTA_URL . "/Configuracion/index?tab=cuenta&{$destino}");
        exit;
    }

    // ============================================================
    // CAMBIAR CONTRASEÑA
    // ============================================================
    public function cambiarPassword()
    {
        $this->checkAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: " . RUTA_URL . "/Configuracion/index?tab=cuenta");
            exit;
        }

        $userModel    = new UserModel();
        $id           = (int) $_SESSION['id_usuario'];
        $actual       = $_POST['password_actual']   ?? '';
        $nueva        = $_POST['password_nueva']    ?? '';
        $confirma     = $_POST['password_confirma'] ?? '';

        if (!$actual || !$nueva || !$confirma) {
            header("Location: " . RUTA_URL . "/Configuracion/index?tab=cuenta&err=campos_vacios");
            exit;
        }

        if ($nueva !== $confirma) {
            header("Location: " . RUTA_URL . "/Configuracion/index?tab=cuenta&err=no_coinciden");
            exit;
        }

        if (strlen($nueva) < 6) {
            header("Location: " . RUTA_URL . "/Configuracion/index?tab=cuenta&err=muy_corta");
            exit;
        }

        if (!$userModel->verificarPassword($id, $actual)) {
            header("Location: " . RUTA_URL . "/Configuracion/index?tab=cuenta&err=pass_incorrecta");
            exit;
        }

        $ok = $userModel->actualizarPassword($id, $nueva);
        $destino = $ok ? 'ok=password' : 'err=password';
        header("Location: " . RUTA_URL . "/Configuracion/index?tab=cuenta&{$destino}");
        exit;
    }

    // ============================================================
    // GUARDAR CONFIGURACIÓN DEL SISTEMA
    // ============================================================
    public function guardarSistema()
    {
        $this->checkAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: " . RUTA_URL . "/Configuracion/index?tab=sistema");
            exit;
        }

        $modelo = $this->getModelo();

        $modelo->setMultiple([
            'nombre_empresa'         => trim($_POST['nombre_empresa']         ?? ''),
            'hora_inicio_jornada'    => trim($_POST['hora_inicio_jornada']    ?? '09:00'),
            'umbral_retraso_minutos' => (int) ($_POST['umbral_retraso_minutos'] ?? 30),
            'horas_jornada_defecto'  => (float) ($_POST['horas_jornada_defecto']  ?? 7.5),
            'horas_semana_defecto'   => (float) ($_POST['horas_semana_defecto']   ?? 37.5),
        ]);

        header("Location: " . RUTA_URL . "/Configuracion/index?tab=sistema&ok=sistema");
        exit;
    }

    // ============================================================
    // TIPOS DE TAREA
    // ============================================================
    public function addTipo()
    {
        $this->checkAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: " . RUTA_URL . "/Configuracion/index?tab=tipos");
            exit;
        }

        $nombre = trim($_POST['nombre'] ?? '');

        if ($nombre === '') {
            header("Location: " . RUTA_URL . "/Configuracion/index?tab=tipos&err=nombre_vacio");
            exit;
        }

        $this->getModelo()->addTipoTarea($nombre);
        header("Location: " . RUTA_URL . "/Configuracion/index?tab=tipos&ok=tipo_add");
        exit;
    }

    public function editTipo()
    {
        $this->checkAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: " . RUTA_URL . "/Configuracion/index?tab=tipos");
            exit;
        }

        $id     = (int) ($_POST['id']     ?? 0);
        $nombre = trim($_POST['nombre']   ?? '');

        if (!$id || $nombre === '') {
            header("Location: " . RUTA_URL . "/Configuracion/index?tab=tipos&err=datos_invalidos");
            exit;
        }

        $this->getModelo()->editTipoTarea($id, $nombre);
        header("Location: " . RUTA_URL . "/Configuracion/index?tab=tipos&ok=tipo_edit");
        exit;
    }

    public function deleteTipo($id)
    {
        $this->checkAdmin();
        $this->getModelo()->deleteTipoTarea((int) $id);
        header("Location: " . RUTA_URL . "/Configuracion/index?tab=tipos&ok=tipo_del");
        exit;
    }
}
