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
        $datos = [
            "title" => "login"
        ];

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

        try {

            $raw  = file_get_contents("php://input");
            $data = json_decode($raw, true);

            if (!$data) {
                $data = $_POST;
            }

            $usuario     = trim($data['usuario'] ?? '');
            $contrasenya = $data['contrasenya'] ?? '';

            if (!$usuario || !$contrasenya) {

                echo json_encode([
                    'success' => false,
                    'message' => 'Datos vacíos'
                ]);

                exit;
            }

            $user = $this->userModel->login(
                $usuario,
                $contrasenya
            );

            if (!$user) {

                echo json_encode([
                    'success' => false,
                    'message' => 'Usuario o contraseña incorrectos'
                ]);

                exit;
            }

            // Regenerate session ID for security
            session_regenerate_id(true);

            $_SESSION['id_usuario'] = $user['id_usuario'];
            $_SESSION['nombre']     = $user['nombre'];
            $_SESSION['rol']        = $user['rol'];

            echo json_encode([
                'success' => true,
                'usuario' => $user['nombre']
            ]);

            exit;

        } catch (Exception $e) {

            error_log($e->getMessage());

            echo json_encode([
                'success' => false,
                'message' => 'Error interno'
            ]);

            exit;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | RECOVERY VIEW
    |--------------------------------------------------------------------------
    */

    public function recuperacion()
    {
        $datos = [
            "title" => "Recuperación de contraseña"
        ];

        $this->load_view(
            'recuperacionContraseña',
            $datos
        );
    }

    /*
    |--------------------------------------------------------------------------
    | SEND RECOVERY EMAIL
    |--------------------------------------------------------------------------
    */

    public function enviarRecuperacion()
    {
        header('Content-Type: application/json');

        try {

            $email = trim(
                $_POST['usuario_email'] ?? ''
            );

            if (!$email) {

                echo json_encode([
                    'success' => false,
                    'message' => 'Email vacío'
                ]);

                exit;
            }

            $usuario = $this->userModel->buscarPorEmail($email);

            // Always return same response
            if (!$usuario) {

                echo json_encode([
                    'success' => true,
                    'message' => 'Si el correo existe, recibirás instrucciones'
                ]);

                exit;
            }

            // Generate raw token
            $token = bin2hex(random_bytes(32));

            // Hash for DB
            $tokenHash = password_hash(
                $token,
                PASSWORD_DEFAULT
            );

            $expira = date(
                'Y-m-d H:i:s',
                strtotime('+15 minutes')
            );

            // Save token
            $ok = $this->userModel->guardarTokenRecuperacion(
                $usuario['id_usuario'],
                $tokenHash,
                $expira
            );

            if (!$ok) {
                throw new Exception('Error guardando token');
            }

            // URL with RAW token
            $link = RUTA_URL .
                "/login/resetPassword?token=" .
                urlencode($token);

            // Send email
            $this->enviarCorreo(
                $usuario['email'],
                $link
            );

        } catch (Exception $e) {

            error_log($e->getMessage());

            // Never expose errors
        }

        echo json_encode([
            'success' => true,
            'message' => 'Si el correo existe, recibirás instrucciones'
        ]);

        exit;
    }

    /*
    |--------------------------------------------------------------------------
    | SEND EMAIL
    |--------------------------------------------------------------------------
    */

    private function enviarCorreo($destinatario, $link)
    {
        $mail = new PHPMailer(true);

        try {

            $mail->SMTPDebug = 0;

            $mail->isSMTP();

            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'miguel.alcanalytics@gmail.com';
            $mail->Password   = 'qquarvcvwmykndaw';

            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            $mail->SMTPOptions = [
                'ssl' => [
                    'verify_peer'       => false,
                    'verify_peer_name'  => false,
                    'allow_self_signed' => true,
                ]
            ];

            $mail->setFrom(
                'miguel.alcanalytics@gmail.com',
                'Soporte'
            );

            $mail->addAddress($destinatario);

            $mail->isHTML(true);
            $mail->CharSet = 'UTF-8';

            $mail->Subject = 'Recuperación de contraseña';

            $mail->Body = "
                <h2>Recuperación de contraseña</h2>

                <p>
                    Haz click en el siguiente enlace para
                    restablecer tu contraseña:
                </p>

                <p>
                    <a href='{$link}'>
                        Recuperar contraseña
                    </a>
                </p>

                <p>
                    Este enlace expira en 15 minutos.
                </p>
            ";

            return $mail->send();

        } catch (Exception $e) {

            error_log($e->getMessage());

            return false;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | RESET PASSWORD VIEW
    |--------------------------------------------------------------------------
    */

    public function resetPassword()
    {
        $token = trim($_GET['token'] ?? '');

        if (!$token) {
            header("Location: " . RUTA_URL . "/login?error=token");
            exit;
        }

        // Search valid token
        $tokens = $this->userModel->buscarTokensRecuperacion();

        $registroValido = null;

        foreach ($tokens as $registro) {

            if (
                password_verify($token, $registro['token'])
                && strtotime($registro['expira_en']) > time()
                && (int)$registro['usado'] === 0
            ) {
                $registroValido = $registro;
                break;
            }
        }

        if (!$registroValido) {
            header("Location: " . RUTA_URL . "/login?error=token_expirado");
            exit;
        }

        $datos = [
            'usuarioId' => $registroValido['id_usuario'],
            'token'     => $token
        ];

        $this->load_view(
            'resetPassword',
            $datos
        );
    }

    /*
    |--------------------------------------------------------------------------
    | SAVE NEW PASSWORD
    |--------------------------------------------------------------------------
    */

    public function guardarNuevaPassword()
    {
        header('Content-Type: application/json');

        try {

            $data = json_decode(
                file_get_contents("php://input"),
                true
            ) ?? $_POST;

            $token        = trim($data['token'] ?? '');
            $usuarioId    = (int)($data['usuarioId'] ?? 0);
            $nuevaPass    = $data['nueva_password'] ?? '';
            $confirmaPass = $data['confirma_password'] ?? '';

            if (
                !$token ||
                !$usuarioId ||
                !$nuevaPass ||
                !$confirmaPass
            ) {

                echo json_encode([
                    'success' => false,
                    'message' => 'Datos incompletos'
                ]);

                exit;
            }

            if ($nuevaPass !== $confirmaPass) {

                echo json_encode([
                    'success' => false,
                    'message' => 'Las contraseñas no coinciden'
                ]);

                exit;
            }

            // Basic password validation
            if (strlen($nuevaPass) < 4) {

                echo json_encode([
                    'success' => false,
                    'message' => 'La contraseña es demasiado corta'
                ]);

                exit;
            }

            // Search latest token
            $registro = $this->userModel->getTokenByUser(
                $usuarioId
            );

            if (
                !$registro ||
                !password_verify($token, $registro['token']) ||
                strtotime($registro['expira_en']) < time() ||
                (int)$registro['usado'] === 1
            ) {

                echo json_encode([
                    'success' => false,
                    'message' => 'Token inválido'
                ]);

                exit;
            }

            // Update password
            $ok = $this->userModel->actualizarPassword(
                $usuarioId,
                $nuevaPass
            );

            if (!$ok) {

                echo json_encode([
                    'success' => false,
                    'message' => 'Error actualizando contraseña'
                ]);

                exit;
            }

            // Mark token as used
            $this->userModel->marcarTokenUsado(
                $registro['token']
            );

            echo json_encode([
                'success' => true,
                'message' => 'Contraseña actualizada'
            ]);

            exit;

        } catch (Exception $e) {

            error_log($e->getMessage());

            echo json_encode([
                'success' => false,
                'message' => 'Error interno'
            ]);

            exit;
        }
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

        $_SESSION = [];

        // Delete session cookie
        if (ini_get("session.use_cookies")) {

            $params = session_get_cookie_params();

            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }

        session_destroy();

        header("Location: /login");
        exit;
    }
}