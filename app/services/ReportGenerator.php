<?php

use Dompdf\Dompdf;

class ReportGenerator {

    public static function generarPDF($filas, $desde, $hasta) {

        // Pasar variables a la vista
        ob_start();

        // IMPORTANTE: variables disponibles en la vista
        $filas = $filas;
        $desde = $desde;
        $hasta = $hasta;

        require __DIR__ . '/../views/informe_email.php';

        $html = ob_get_clean();

        // Crear PDF
        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);

        // Horizontal queda mejor para tablas
        $dompdf->setPaper('A4', 'landscape');

        $dompdf->render();

        return $dompdf->output(); // devuelve el PDF en binario
    }
}