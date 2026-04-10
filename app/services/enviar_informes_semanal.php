<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

public function informeSemanalCron() {

    require_once __DIR__ . '/../../../vendor/autoload.php';

    $db = new Database();
    $conexion = $db->conectar();
    $modelo = new InformeModel($conexion);

    // 📅 SEMANA ANTERIOR (más correcto que la actual)
    $desde = date("Y-m-d", strtotime("monday last week"));
    $hasta = date("Y-m-d", strtotime("sunday last week"));

    $usuarios = $modelo->obtenerUsuarios();

    foreach ($usuarios as $user) {

        $datos = $modelo->obtenerInforme($desde, $hasta, $user['id_usuario']);

        // 🚫 No enviar si no hay datos
        if (empty($datos)) {
            echo "Sin datos para usuario ID: " . $user['id_usuario'] . "\n";
            continue;
        }

        // 🧾 GENERAR PDF
        $dompdf = new Dompdf\Dompdf();

        ob_start();
        $nombreUsuario = $user['nombre'];
        require __DIR__ . '/../views/pages/informe_pdf.php';
        $html = ob_get_clean();

        $dompdf->loadHtml($html);
        $dompdf->render();

        // 📁 Guardar temporalmente
        $rutaPDF = __DIR__ . "/tmp/informe_semanal_" . $user['id_usuario'] . ".pdf";
        file_put_contents($rutaPDF, $dompdf->output());

        // 📧 ENVIAR EMAIL
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'miguel.alcanalytics@gmail.com';
            $mail->Password = 'qqua rvcv wmyk ndaw'; // ⚠️ usar App Password en producción
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            $mail->setFrom('miguel.alcanalytics@gmail.com', 'Sistema de Informes');
            $mail->addAddress($user['email'], $user['nombre']);

            $mail->isHTML(true);
            $mail->Subject = 'Informe semanal';
            $mail->Body = "
                <p>Hola {$user['nombre']},</p>
                <p>Adjunto tienes tu informe semanal 
                ({$desde} a {$hasta}).</p>
            ";

            $mail->addAttachment($rutaPDF);

            $mail->send();

            echo "✅ Informe semanal enviado a: " . $user['email'] . "\n";

        } catch (Exception $e) {
            echo "❌ Error enviando a {$user['email']}: {$mail->ErrorInfo}\n";
        }

        // 🧹 limpiar
        unlink($rutaPDF);
    }
}