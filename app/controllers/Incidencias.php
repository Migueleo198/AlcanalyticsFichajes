<?php

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/IncidenciaModel.php';

class Incidencias extends Controller
{
    public function index()
    {
        session_start();

        if (!isset($_SESSION['id_usuario'])) {
            header("Location: " . RUTA_URL . "/login");
            exit;
        }

        $datos = [
            "title" => "Incidencias",
        ];

        $this->load_view('registroIncidencias', $datos);
    }

    public function getIncidencias()
    {
        header('Content-Type: application/json');

        try {
            $db = new Database();
            $conexion = $db->conectar();

            $model = new IncidenciaModel($conexion);
            $incidencias = $model->getIncidencias();

            echo json_encode([
                "success" => true,
                "data" => $incidencias
            ]);

        } catch (Throwable $e) {
            echo json_encode([
                "success" => false,
                "message" => $e->getMessage()
            ]);
        }
    }

    public function getIncidencia($id)
    {
        header('Content-Type: application/json');

        try {
            $db = new Database();
            $conexion = $db->conectar();

            $model = new IncidenciaModel($conexion);
            $incidencia = $model->getIncidencia($id);

            echo json_encode([
                "success" => true,
                "data" => $incidencia
            ]);

        } catch (Throwable $e) {
            echo json_encode([
                "success" => false,
                "message" => $e->getMessage()
            ]);
        }
    }

    public function getByFichaje() {

    $id = $_GET['id'] ?? null;

    if (!$id) {
        echo json_encode(["success" => false, "data" => []]);
        return;
    }

    $db = new Database();
    $conexion = $db->conectar();

    $modelo = new IncidenciaModel($conexion);
    $incidencias = $modelo->getByFichaje($id);

    echo json_encode([
        "success" => true,
        "data" => $incidencias
    ]);
}


    public function updateIncidencia()
{
    header('Content-Type: application/json');

    if (empty($_POST)) {
        echo json_encode([
            "success" => false,
            "message" => "Datos vacíos"
        ]);
        return;
    }

    // Validación básica
    $required = ['id', 'id_fichaje', 'mensaje', 'respuesta', 'estado', 'fecha'];
    foreach ($required as $field) {
        if (!isset($_POST[$field])) {
            echo json_encode([
                "success" => false,
                "message" => "Falta el campo: $field"
            ]);
            return;
        }
    }

    try {
        $db = new Database();
        $conexion = $db->conectar();

        $model = new IncidenciaModel($conexion);

        $ok = $model->updateIncidencia($_POST['id'], [
            'id_fichaje' => $_POST['id_fichaje'],
            'mensaje'    => $_POST['mensaje'],
            'respuesta'  => $_POST['respuesta'],
            'estado'     => $_POST['estado'],
            'fecha'      => $_POST['fecha']
        ]);

        if ($ok) {
            
            header("Location: /incidencias/index");
            exit;
        } else {
            echo json_encode([
                "success" => false,
                "message" => "Error al actualizar incidencia"
            ]);
        }

    } catch (Throwable $e) {
        echo json_encode([
            "success" => false,
            "message" => $e->getMessage()
        ]);
    }
}

    public function addIncidencia()
    {
        header('Content-Type: application/json');

        if (empty($_POST)) {
            echo json_encode([
                "success" => false,
                "message" => "Datos vacíos"
            ]);
            return;
        }

        // Validación básica
        $required = ['id_fichaje', 'mensaje', 'respuesta', 'estado', 'fecha'];
        foreach ($required as $field) {
            if (!isset($_POST[$field])) {
                echo json_encode([
                    "success" => false,
                    "message" => "Falta el campo: $field"
                ]);
                return;
            }
        }

        try {
            $db = new Database();
            $conexion = $db->conectar();

            $model = new IncidenciaModel($conexion);

            $ok = $model->addIncidencia([
                'id_fichaje' => $_POST['id_fichaje'],
                'mensaje'    => $_POST['mensaje'],
                'respuesta'  => $_POST['respuesta'],
                'estado'     => $_POST['estado'],
                'fecha'      => $_POST['fecha']
            ]);

            if ($ok) {
                // Redirección en caso de éxito
                header("Location: " . RUTA_URL . "/home/index");
                exit;
            } else {
                echo json_encode([
                    "success" => false,
                    "message" => "Error al crear incidencia"
                ]);
            }

        } catch (Throwable $e) {
            echo json_encode([
                "success" => false,
                "message" => $e->getMessage()
            ]);
        }
    }

    public function removeIncidencia(){

    header('Content-Type: application/json');

    if (empty($_POST)) {
        echo json_encode(["success" => false, "message" => "Datos vacíos"]);
        return;
    }

        $db = new Database();
        $conexion = $db->conectar();
        $model = new IncidenciaModel($conexion);

         $ok = $model->removeIncidencia($_POST['id']);

          if ($ok) {
        header("Location: /incidencias/index"); // redirect after success
        exit;
        } else {
            echo json_encode(["success" => false, "message" => "Error al eliminar usuario"]);
        }
    }
}