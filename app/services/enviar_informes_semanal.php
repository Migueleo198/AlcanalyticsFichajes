<?php

require_once __DIR__ . '/../../vendor/autoload.php';

require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../models/InformeModel.php';
require_once __DIR__ . '/../services/ReportGenerator.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Conexión BD
$host   = 'localhost';
$dbname = 'Fichajes2';
$user   = 'admin_fichajes2';
$pass   = '6%DfdlBrud5$jVg8';

try {
    $conexion = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8",
        $user,
        $pass
    );
    $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error conexión BD: " . $e->getMessage());
}

$userModel    = new UserModel($conexion);
$informeModel = new InformeModel($conexion);

// Semana anterior: lunes a domingo
$desde = date('Y-m-d', strtotime('monday last week'));
$hasta = date('Y-m-d', strtotime('sunday last week'));

$usuarios = $userModel->getUsuarios();

foreach ($usuarios as $usuario) {

    $userId = $usuario['id_usuario'];
    $email  = $usuario['email'];
    $nombre = $usuario['nombre'];

    $filas = $informeModel->obtenerInforme($desde, $hasta, [$userId]);

    if (empty($filas)) {
        echo "Sin datos para $nombre ($email)\n";
        continue;
    }

    // Generar PDF en memoria (sin fichero temporal)
    $pdf = ReportGenerator::generarPDF($filas, $desde, $hasta);

    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'miguel.alcanalytics@gmail.com';
        $mail->Password   = 'qqua rvcv wmyk ndaw';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        $mail->CharSet    = 'UTF-8';

        $mail->setFrom('miguel.alcanalytics@gmail.com', 'Sistema de Fichajes');
        $mail->addAddress($email, $nombre);

        $mail->isHTML(true);
        $mail->Subject = 'Informe semanal de fichajes';
        $mail->Body    = "
            <p>Hola <b>$nombre</b>,</p>
            <p>Adjuntamos tu informe semanal de fichajes correspondiente al periodo:</p>
            <p><b>$desde</b> al <b>$hasta</b></p>
            <p>Saludos.</p>
        ";

        $filename = "informe_semanal_{$userId}_{$desde}_{$hasta}.pdf";
        $mail->addStringAttachment($pdf, $filename);

        $mail->send();

        echo "Enviado a $email\n";

    } catch (Exception $e) {
        echo "Error enviando a $email: {$mail->ErrorInfo}\n";
    }
}

echo "Proceso finalizado\n";
