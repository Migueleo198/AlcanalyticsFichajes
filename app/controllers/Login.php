<?php

require_once __DIR__ . '/../../../vendor/autoload.php';
require_once __DIR__ . '/../models/UserModel.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Login extends Controller
{
    private $userModel;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $this->userModel = new UserModel();
    }

    /*
    |--------------------------------------------------------------------------
    | LOGIN VIEW
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        $datos = ["title" => "login"];
        $this->load_view('login', $datos);
    }

    /*
    |--------------------------------------------------------------------------
    | LOGIN
    |--------------------------------------------------------------------------
    */

    public function login()
    {
        header('Content-Type: application/json');

        $raw  = file_get_contents("php://input");
        $data = json_decode($raw, true);

        if (!$data) {
            $data = $_POST;
        }

        $usuario     = $data['usuario']     ?? '';
        $contrasenya = $data['contrasenya'] ?? '';

        if (!$usuario || !$contrasenya) {
            echo json_encode(['success' => false, 'message' => 'Datos vacíos']);
            exit;
        }

        $user = $this->userModel->login($usuario, $contrasenya);

        if ($user) {
            $_SESSION['id_usuario'] = $user['id_usuario'];
            $_SESSION['nombre']     = $user['nombre'];
            $_SESSION['rol']        = $user['rol'];

            echo json_encode(['success' => true, 'usuario' => $user['nombre']]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Usuario o contraseña incorrectos']);
        }

        exit;
    }

    /*
    |--------------------------------------------------------------------------
    | VIEW RECUPERACIÓN
    |--------------------------------------------------------------------------
    */

    public function recuperacion()
    {
        $datos = ["title" => "Recuperación de contraseña"];
        $this->load_view('recuperacionContraseña', $datos);
    }

    /*
    |--------------------------------------------------------------------------
    | ENVIAR EMAIL RECUPERACIÓN
    |--------------------------------------------------------------------------
    */

    public function enviarRecuperacion()
    {
        header('Content-Type: application/json');

        // ── FIX: accept both JSON body and classic form POST ──────────────
        $raw  = file_get_contents("php://input");
        $data = json_decode($raw, true);
        if (!$data) {
            $data = $_POST;
        }

        $email = trim($data['usuario_email'] ?? '');

        if (!$email) {
            echo json_encode(['success' => false, 'message' => 'Email vacío']);
            exit;
        }

        $usuario = $this->userModel->buscarPorEmail($email);

        if ($usuario) {

            // Raw token goes in the URL — only the hash is stored in the DB
            $token     = bin2hex(random_bytes(32));
            $tokenHash = password_hash($token, PASSWORD_DEFAULT);
            $expira    = date('Y-m-d H:i:s', strtotime('+15 minutes'));

            $ok = $this->userModel->guardarTokenRecuperacion(
                $usuario['id_usuario'],
                $tokenHash,
                $expira
            );

            if (!$ok) {
                echo json_encode(['success' => false, 'message' => 'Error guardando token']);
                exit;
            }

            $link = RUTA_URL . "/login/resetPassword?token=" . urlencode($token);

            try {
                $this->enviarCorreo($usuario['email'], $link);
            } catch (\Exception $e) {
                // Log real SMTP details server-side; never expose internals to the client
                error_log('[enviarRecuperacion] SMTP error: ' . $e->getMessage());
                echo json_encode(['success' => false, 'message' => 'Error enviando correo. Contacta con soporte.']);
                exit;
            }
        }

        // Generic response: do not reveal whether the email exists in the DB
        echo json_encode([
            'success' => true,
            'message' => 'Si el correo existe, recibirás instrucciones'
        ]);
        exit;
    }

    /*
    |--------------------------------------------------------------------------
    | ENVÍO EMAIL
    |--------------------------------------------------------------------------
    */

    private function enviarCorreo($destinatario, $link)
    {
        $mail = new PHPMailer(true); // true = throw exceptions on error

        // No debug output — would corrupt JSON responses
        $mail->SMTPDebug = 0;

        $mail->isSMTP();
        $mail->Host     = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'miguel.alcanalytics@gmail.com';
        $mail->Password = 'qquarvcvwmykndaw'; // Gmail App Password

        // ── FIX 1: use port 587 + STARTTLS ──────────────────────────────
        // Port 465/SMTPS is blocked on many hosting providers.
        // 587/STARTTLS is the modern standard and is almost never blocked.
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // ── FIX 2: disable SSL peer certificate verification ─────────────
        // Servers with outdated CA bundles fail silently with
        // "SMTP connect() failed" due to TLS handshake errors.
        $mail->SMTPOptions = [
            'ssl' => [
                'verify_peer'       => false,
                'verify_peer_name'  => false,
                'allow_self_signed' => true,
            ]
        ];

        $mail->setFrom('miguel.alcanalytics@gmail.com', 'Soporte');
        $mail->addAddress($destinatario);

        $mail->isHTML(true);
        $mail->CharSet = 'UTF-8';
        $mail->Subject = 'Recuperación de contraseña';
        $mail->Body    = "
            <h2>Recuperación de contraseña</h2>
            <p>Haz click en el siguiente enlace para restablecer tu contraseña:</p>
            <a href='{$link}'>Recuperar contraseña</a>
            <p>Este enlace expira en 15 minutos.</p>
        ";

        // Throws Exception on failure — the caller catches and logs it
        $mail->send();

        return true;
    }

    /*
    |--------------------------------------------------------------------------
    | RESET PASSWORD — validate token, show the new-password form
    |--------------------------------------------------------------------------
    */

    public function resetPassword()
    {
        $token = $_GET['token'] ?? '';

        if (!$token) {
            die('Token inválido');
        }

        $registros = $this->userModel->buscarTokensRecuperacion();
        $valido    = false;
        $usuarioId = null;

        foreach ($registros as $item) {
            if (password_verify($token, $item['token'])) {
                if (strtotime($item['expira_en']) > time() && $item['usado'] == 0) {
                    $valido    = true;
                    $usuarioId = $item['id_usuario'];
                    break;
                }
            }
        }

        if (!$valido) {
            die('Token inválido o expirado');
        }

        $datos = [
            'usuarioId' => $usuarioId,
            'token'     => $token
        ];

        $this->load_view('nuevaPassword', $datos);
    }

    /*
    |--------------------------------------------------------------------------
    | GUARDAR NUEVA PASSWORD — called by the nuevaPassword view form
    |--------------------------------------------------------------------------
    */

    public function guardarNuevaPassword()
    {
        header('Content-Type: application/json');

        $raw  = file_get_contents("php://input");
        $data = json_decode($raw, true);
        if (!$data) {
            $data = $_POST;
        }

        $token        = $data['token']             ?? '';
        $usuarioId    = (int) ($data['usuarioId']  ?? 0);
        $nuevaPass    = $data['nueva_password']    ?? '';
        $confirmaPass = $data['confirma_password'] ?? '';

        if (!$token || !$usuarioId || !$nuevaPass) {
            echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
            exit;
        }

        if ($nuevaPass !== $confirmaPass) {
            echo json_encode(['success' => false, 'message' => 'Las contraseñas no coinciden']);
            exit;
        }

        // Re-validate the token before actually changing the password
        $registros = $this->userModel->buscarTokensRecuperacion();
        $valido    = false;

        foreach ($registros as $item) {
            if (
                (int) $item['id_usuario'] === $usuarioId
                && password_verify($token, $item['token'])
                && strtotime($item['expira_en']) > time()
                && $item['usado'] == 0
            ) {
                $valido = true;
                break;
            }
        }

        if (!$valido) {
            echo json_encode(['success' => false, 'message' => 'Token inválido o expirado']);
            exit;
        }

        $ok = $this->userModel->actualizarPassword($usuarioId, $nuevaPass);

        if (!$ok) {
            echo json_encode(['success' => false, 'message' => 'Error actualizando contraseña']);
            exit;
        }

        // Mark ALL tokens for this user as used so none can be reused
        $this->userModel->marcarTokenUsado($usuarioId);

        echo json_encode(['success' => true, 'message' => 'Contraseña actualizada correctamente']);
        exit;
    }

    /*
    |--------------------------------------------------------------------------
    | LOGOUT
    |--------------------------------------------------------------------------
    */

    public function logout()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        session_unset();
        session_destroy();

        header("Location: /login");
        exit;
    }
}
