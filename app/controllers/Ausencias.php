<?php

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/BajasModel.php';
require_once __DIR__ . '/../models/AusenciaModel.php';
require_once __DIR__ . '/../models/UserModel.php';

class Ausencias extends Controller
{
    private $conexion;

    public function __construct()
    {
        $this->initSession();
        $this->conexion = $this->getConexion();
    }

    /* =========================
       SESSION / SEGURIDAD
    ========================= */
    private function initSession()
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
        if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'Administrador') {
            header("Location: " . RUTA_URL . "/home");
            exit;
        }
    }

    private function getConexion()
    {
        $db = new Database();
        return $db->conectar();
    }


    /* =========================
       VISTA PRINCIPAL
    ========================= */
    public function index()
    {
        $bajasModel = new BajasModel($this->conexion);
        $ausenciasModel = new AusenciaModel($this->conexion);
        $userModel = new UserModel($this->conexion);

        $datos = [
            // 🔴 REMUNERADAS
            'motivos_remunerados' => $bajasModel->obtenerMotivosBaja(),

            // 🔵 NO REMUNERADAS
            'ausencias' => $ausenciasModel->getAll(),

            // 👥 COMUNES
            'usuarios' => $userModel->getUsuarios(),
            'title' => 'Gestión de ausencias'
        ];

        $this->load_view('ausencia', $datos);
    }


    /* =========================
       🔴 AUSENCIAS REMUNERADAS
    ========================= */

    public function checkLimiteHoras()
    {
        header('Content-Type: application/json; charset=utf-8');

        $idMotivo  = (int)($_GET['id_motivo'] ?? 0);
        $idUsuario = $_SESSION['id_usuario'];
        $anio      = (int)date('Y');

        $bajasModel  = new BajasModel($this->conexion);
        $motivos     = $bajasModel->obtenerMotivosBaja();
        $motivo      = null;

        foreach ($motivos as $m) {
            if ((int)$m['id_motivo'] === $idMotivo) { $motivo = $m; break; }
        }

        if (!$motivo || empty($motivo['limite_horas_anual'])) {
            echo json_encode(['tiene_limite' => false]);
            exit;
        }

        $limite  = (float)$motivo['limite_horas_anual'];
        $usadas  = $bajasModel->getHorasUsadasAnio($idUsuario, $idMotivo, $anio);
        $restantes = max(0, $limite - $usadas);

        echo json_encode([
            'tiene_limite' => true,
            'limite'       => $limite,
            'usadas'       => round($usadas, 2),
            'restantes'    => round($restantes, 2),
        ]);
        exit;
    }

    public function solicitarRemunerada()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;

        $bajasModel = new BajasModel($this->conexion);
        $idMotivo   = (int)($_POST['id_motivo'] ?? 0);
        $horas      = isset($_POST['horas']) && $_POST['horas'] !== '' ? (float)$_POST['horas'] : null;

        // Verificar límite anual si aplica
        $motivos = $bajasModel->obtenerMotivosBaja();
        $motivo  = null;
        foreach ($motivos as $m) {
            if ((int)$m['id_motivo'] === $idMotivo) { $motivo = $m; break; }
        }

        $esRemunerada = 1;

        if ($motivo && !empty($motivo['limite_horas_anual']) && $horas !== null) {
            $limite    = (float)$motivo['limite_horas_anual'];
            $usadas    = $bajasModel->getHorasUsadasAnio($_SESSION['id_usuario'], $idMotivo, (int)date('Y'));
            $restantes = max(0, $limite - $usadas);

            if ($restantes <= 0) {
                // Sin horas disponibles → no remunerada
                $esRemunerada = 0;
            } elseif ($horas > $restantes) {
                // Solicita más de lo disponible → bloquear
                header('Location: ' . RUTA_URL . '/Ausencias?error=limite&motivo=' . urlencode($motivo['nombre'])
                    . '&restantes=' . $restantes . '&limite=' . $limite);
                exit;
            }
        }

        $data = [
            'id_usuario'    => $_SESSION['id_usuario'],
            'id_motivo'     => $idMotivo,
            'fecha_inicio'  => $_POST['fecha_inicio'] ?? null,
            'fecha_fin'     => !empty($_POST['fecha_fin']) ? $_POST['fecha_fin'] : null,
            'descripcion'   => trim($_POST['descripcion'] ?? ''),
            'horas'         => $horas,
            'es_remunerada' => $esRemunerada,
        ];

        $bajasModel->insertarBaja($data);

        header("Location: " . RUTA_URL . "/Ausencias?ok=1");
        exit;
    }


    public function gestionarRemuneradas()
    {
        $this->checkAdmin();

        $bajasModel = new BajasModel($this->conexion);

        $datos = [
            'title' => 'Ausencias remuneradas',
            'bajas' => $bajasModel->obtenerTodasBajas()
        ];

        $this->load_view('bajas_admin', $datos);
    }


    public function cambiarEstado($id, $estado)
    {
        $this->checkAdmin();

        $validos = ['aprobada', 'rechazada'];

        if (!in_array($estado, $validos)) {
            header("Location: " . RUTA_URL . "/Ausencias/gestionarRemuneradas");
            exit;
        }

        $bajasModel = new BajasModel($this->conexion);
        $bajasModel->actualizarEstado($id, $estado);

        header("Location: " . RUTA_URL . "/Ausencias/gestionarRemuneradas");
        exit;
    }


    /* =========================
       🔵 AUSENCIAS NO REMUNERADAS
    ========================= */

    public function crearNoRemunerada()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;

        $modelo = new AusenciaModel($this->conexion);

        $modelo->crear(
            $_POST['id_usuario']          ?? null,
            trim($_POST['motivo_texto']   ?? ''),
            $_POST['fecha_inicio']        ?? null,
            $_POST['fecha_fin']           ?? null
        );

        echo json_encode(["ok" => true]);
    }


    public function editarNoRemunerada()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;

        $modelo = new AusenciaModel($this->conexion);
        $modelo->editar(
            $_POST['id']          ?? null,
            $_POST['id_usuario']  ?? null,
            trim($_POST['motivo_texto'] ?? ''),
            $_POST['fecha_inicio'] ?? null,
            $_POST['fecha_fin']    ?? null
        );

        echo json_encode(["ok" => true]);
    }

    public function eliminarNoRemunerada()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;

        $modelo = new AusenciaModel($this->conexion);
        $modelo->eliminar($_POST['id']);

        echo json_encode(["ok" => true]);
    }
}