<?php

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/InformeModel.php';
require_once __DIR__ . '/../../../vendor/autoload.php';

class Informes extends Controller {

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

    public function index() {

        $this->checkAuth();

        $db = new Database();
        $conexion = $db->conectar();

        $modelo = new InformeModel($conexion);
        $usuarios = $modelo->obtenerUsuarios();

        $datos = [
            "usuarios" => $usuarios,
            "title" => "Informes"
        ];

        $this->load_view('informes', $datos);
    }

    public function generar() {

        $this->checkAuth();

        $desde = $_GET['desde'] ?? null;
        $hasta = $_GET['hasta'] ?? null;

        $usuarios = $_GET['usuarios'] ?? [];

        if (!is_array($usuarios)) {
            $usuarios = [$usuarios];
        }

        $usuarios = array_filter($usuarios, 'is_numeric');

        if (!$desde || !$hasta) {
            header("Location: " . RUTA_URL . "/Informes/index?error=fechas");
            exit;
        }

        $db = new Database();
        $conexion = $db->conectar();

        $modelo = new InformeModel($conexion);
        $datos = $modelo->obtenerInforme($desde, $hasta, $usuarios);

        $this->load_view('informe_resultado', [
            "datos" => $datos,
            "desde" => $desde,
            "hasta" => $hasta,
            "title" => "Informe Generado"
        ]);
    }


    public function generarPDF() {

        $this->checkAuth();

        $desde = $_GET['desde'] ?? '';
        $hasta = $_GET['hasta'] ?? '';

        $usuarios = $_GET['usuarios'] ?? [];

        if (!is_array($usuarios)) {
            $usuarios = [$usuarios];
        }

        $usuarios = array_filter($usuarios, 'is_numeric');

        if (empty($desde) || empty($hasta)) {
            header("Location: " . RUTA_URL . "/Informes/index?error=fechas");
            exit;
        }

        $db = new Database();
        $conexion = $db->conectar();

        $modelo = new InformeModel($conexion);
        $filas = $modelo->obtenerInforme($desde, $hasta, $usuarios);

        $dompdf = new Dompdf\Dompdf();

        ob_start();
        require __DIR__ . '/../views/pages/informe_pdf.php';
        $html = ob_get_clean();

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        $dompdf->stream("informe_fichajes.pdf", ["Attachment" => true]);
    }


    public function informeSemanalCron() {

        $db = new Database();
        $conexion = $db->conectar();
        $modelo = new InformeModel($conexion);

        $desde = date("Y-m-d", strtotime("monday this week"));
        $hasta = date("Y-m-d", strtotime("sunday this week"));

        $usuarios = $modelo->obtenerUsuarios();

        foreach ($usuarios as $user) {

            // ✅ ahora se pasa como array
            $datos = $modelo->obtenerInforme($desde, $hasta, [$user['id_usuario']]);

            echo "Informe semanal generado para usuario ID: " . $user['id_usuario'] . "\n";
        }
    }
}