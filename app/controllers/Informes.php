<?php

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/InformeModel.php';
require_once __DIR__ . '/../../../vendor/autoload.php';

class Informes extends Controller {

    public function index() {

        session_start();

        if (!isset($_SESSION['id_usuario'])) {
            header("Location: " . RUTA_URL . "/login");
            exit;
        }

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

        session_start();

        if (!isset($_SESSION['id_usuario'])) {
            header("Location: " . RUTA_URL . "/login");
            exit;
        }

        $desde = $_GET['desde'] ?? null;
        $hasta = $_GET['hasta'] ?? null;
        $usuario = $_GET['usuario'] ?? null;

        if (!$desde || !$hasta) {
            die("Faltan fechas");
        }

        $db = new Database();
        $conexion = $db->conectar();

        $modelo = new InformeModel($conexion);
        $datos = $modelo->obtenerInforme($desde, $hasta, $usuario);

        $this->load_view('informe_resultado', [
            "datos" => $datos,
            "desde" => $desde,
            "hasta" => $hasta,
            "title" => "Informe Generado"
        ]);
    }


   public function generarPDF() {

    session_start();

    if (!isset($_SESSION['id_usuario'])) {
        header("Location: " . RUTA_URL . "/login");
        exit;
    }

    $desde = $_GET['desde'] ?? null;
    $hasta = $_GET['hasta'] ?? null;
    $usuario = $_GET['usuario'] ?? null;

    if (!$desde || !$hasta) {
        die("Faltan fechas");
    }

    $db = new Database();
    $conexion = $db->conectar();

    $modelo = new InformeModel($conexion);
    $filas = $modelo->obtenerInforme($desde, $hasta, $usuario);

    // 👉 AQUÍ VA 👇
    require_once __DIR__ . '/../../../vendor/autoload.php';

    // DomPDF
    $dompdf = new Dompdf\Dompdf();

    ob_start();
    require __DIR__ . '/../views/pages/informe_pdf.php';
    $html = ob_get_clean();

    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'landscape');
    $dompdf->render();

    $dompdf->stream("informe_fichajes.pdf", ["Attachment" => true]);
}
}