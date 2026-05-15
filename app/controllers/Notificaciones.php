<?php

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/NotificacionesModel.php';

class Notificaciones extends Controller
{
    public function getNotificaciones()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        header('Content-Type: application/json; charset=utf-8');

        if (!isset($_SESSION['id_usuario'])) {
            echo json_encode(['success' => false, 'total' => 0, 'items' => []]);
            exit;
        }

        try {
            $db     = new Database();
            $modelo = new NotificacionesModel($db->conectar());
            $esAdmin = isset($_SESSION['rol']) && $_SESSION['rol'] === 'Administrador';

            $items = $esAdmin
                ? $modelo->getParaAdmin()
                : $modelo->getParaTrabajador((int) $_SESSION['id_usuario']);

            $total = array_sum(array_column($items, 'count'));

            echo json_encode([
                'success' => true,
                'total'   => $total,
                'items'   => $items,
            ]);

        } catch (Throwable $e) {
            error_log('Notificaciones error: ' . $e->getMessage());
            echo json_encode(['success' => false, 'total' => 0, 'items' => []]);
        }

        exit;
    }
}
