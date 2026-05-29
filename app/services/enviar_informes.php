<?php

// 🔌 Autoload Composer (ruta absoluta recomendada)
require_once __DIR__ . '/../../vendor/autoload.php';

// 📦 Modelos
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../models/FichajeModel.php';
require_once __DIR__ . '/../services/ReportGenerator.php';

// 📧 PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

//APP PASSWORD PARA AlcanalyticsFichajes:  qqua rvcv wmyk ndaw

// 🔐 Conexión BD
$host = 'localhost';
$dbname = 'Fichajes2';
$user = 'admin_fichajes2';
$pass = '6%DfdlBrud5$jVg8';

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

// 🧠 Modelos (IMPORTANTE: pasar conexión)
$userModel = new UserModel($conexion);
$fichajeModel = new FichajeModelo($conexion);

// 📅 Rango: mes anterior
$desde = date('Y-m-01', strtotime('first day of last month'));
$hasta = date('Y-m-t', strtotime('last day of last month'));

// 👥 Usuarios
$usuarios = $userModel->getUsuarios();

foreach ($usuarios as $usuario) {

    $userId = $usuario['id_usuario'];
    $email  = $usuario['email'];
    $nombre = $usuario['nombre'];

    // 📊 Fichajes del usuario
    $filas = $fichajeModel->getByUserAndRange($userId, $desde, $hasta);

    if (empty($filas)) {
        continue;
    }

    // 📄 Generar PDF
    $pdf = ReportGenerator::generarPDF($filas, $desde, $hasta);

    $mail = new PHPMailer(true);

    try {

        // ⚙️ SMTP Gmail
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;

        $mail->Username = 'miguel.alcanalytics@gmail.com';
        $mail->Password = 'qqua rvcv wmyk ndaw'; // ⚠️ usar App Password en producción

        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->CharSet = 'UTF-8';

        // 📤 Remitente
        $mail->setFrom('miguel.alcanalytics@gmail.com', 'Sistema de Fichajes');

        // 📥 Destinatario
        $mail->addAddress($email, $nombre);

        // 📝 Email
        $mail->isHTML(true);
        $mail->Subject = 'Informe mensual de fichajes';

        $mail->Body = "
            <p>Hola <b>$nombre</b>,</p>
            <p>Adjuntamos tu informe mensual de fichajes correspondiente al periodo:</p>
            <p><b>$desde</b> al <b>$hasta</b></p>
            <p>Saludos.</p>
        ";

        // 📎 Adjuntar PDF
        $filename = "informe_{$userId}_{$desde}_{$hasta}.pdf";
        $mail->addStringAttachment($pdf, $filename);

        $mail->send();

        echo "Enviado a $email\n";

    } catch (Exception $e) {
        echo "Error enviando a $email: {$mail->ErrorInfo}\n";
    }
}

echo "Proceso finalizado\n";