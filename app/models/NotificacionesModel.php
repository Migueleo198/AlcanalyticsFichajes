<?php

class NotificacionesModel
{
    private $db;

    public function __construct($conexion)
    {
        $this->db = $conexion;
    }

    // Notificaciones para Administrador
    public function getParaAdmin(): array
    {
        $items = [];

        // Incidencias pendientes de resolver
        $n = (int) $this->db->query(
            "SELECT COUNT(*) FROM Incidencia WHERE estado = 'pendiente'"
        )->fetchColumn();
        if ($n > 0) {
            $items[] = [
                'tipo'    => 'danger',
                'icono'   => 'bi-exclamation-triangle-fill',
                'mensaje' => $n . ' incidencia' . ($n !== 1 ? 's' : '') . ' pendiente' . ($n !== 1 ? 's' : ''),
                'url'     => '/incidencias/index',
                'count'   => $n,
            ];
        }

        // Vacaciones pendientes de aprobar
        $n = (int) $this->db->query(
            "SELECT COUNT(*) FROM Vacaciones WHERE estado = 'pendiente'"
        )->fetchColumn();
        if ($n > 0) {
            $items[] = [
                'tipo'    => 'warning',
                'icono'   => 'bi-airplane-fill',
                'mensaje' => $n . ' solicitud' . ($n !== 1 ? 'es' : '') . ' de vacaciones',
                'url'     => '/Vacaciones/index',
                'count'   => $n,
            ];
        }

        // Bajas pendientes de aprobar
        $n = (int) $this->db->query(
            "SELECT COUNT(*) FROM Bajas WHERE estado = 'pendiente'"
        )->fetchColumn();
        if ($n > 0) {
            $items[] = [
                'tipo'    => 'warning',
                'icono'   => 'bi-person-dash-fill',
                'mensaje' => $n . ' baja' . ($n !== 1 ? 's' : '') . ' pendiente' . ($n !== 1 ? 's' : ''),
                'url'     => '/Bajas/gestionar',
                'count'   => $n,
            ];
        }

        // Fichajes abiertos desde hace más de 12 horas (posibles olvidos)
        $n = (int) $this->db->query(
            "SELECT COUNT(*) FROM Fichaje
             WHERE estado = 'abierto'
             AND (fecha < CURDATE()
                  OR (fecha = CURDATE() AND hora_entrada < SUBTIME(CURTIME(), '12:00:00')))"
        )->fetchColumn();
        if ($n > 0) {
            $items[] = [
                'tipo'    => 'danger',
                'icono'   => 'bi-clock-history',
                'mensaje' => $n . ' fichaje' . ($n !== 1 ? 's' : '') . ' sin cerrar (+12 h)',
                'url'     => '/Fichaje/detalle',
                'count'   => $n,
            ];
        }

        return $items;
    }

    // Notificaciones para Trabajador
    public function getParaTrabajador(int $id_usuario): array
    {
        $items = [];

        // Fichaje activo en curso
        $stmt = $this->db->prepare(
            "SELECT id_fichaje FROM Fichaje
             WHERE id_usuario = ? AND estado = 'abierto'
             ORDER BY id_fichaje DESC LIMIT 1"
        );
        $stmt->execute([$id_usuario]);
        if ($stmt->fetch()) {
            $items[] = [
                'tipo'    => 'primary',
                'icono'   => 'bi-clock-fill',
                'mensaje' => 'Tienes un fichaje activo abierto',
                'url'     => '/Fichaje/index',
                'count'   => 1,
            ];
        }

        // Vacaciones aprobadas/rechazadas en los últimos 7 días
        $stmt = $this->db->prepare(
            "SELECT estado, COUNT(*) AS c
             FROM Vacaciones
             WHERE id_usuario = ?
               AND estado IN ('aprobada','rechazada')
               AND fecha_solicitud >= DATE_SUB(NOW(), INTERVAL 7 DAY)
             GROUP BY estado"
        );
        $stmt->execute([$id_usuario]);
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $n = (int) $row['c'];
            $items[] = [
                'tipo'    => $row['estado'] === 'aprobada' ? 'success' : 'danger',
                'icono'   => $row['estado'] === 'aprobada' ? 'bi-check-circle-fill' : 'bi-x-circle-fill',
                'mensaje' => $n . ' vacacion' . ($n !== 1 ? 'es' : '') . ' ' . $row['estado'] . ($n !== 1 ? 's' : ''),
                'url'     => '/Vacaciones/index',
                'count'   => $n,
            ];
        }

        // Incidencias resueltas en sus fichajes (últimos 7 días)
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM Incidencia i
             JOIN Fichaje f ON f.id_fichaje = i.id_fichaje
             WHERE f.id_usuario = ?
               AND i.estado = 'resuelta'
               AND i.fecha >= DATE_SUB(NOW(), INTERVAL 7 DAY)"
        );
        $stmt->execute([$id_usuario]);
        $n = (int) $stmt->fetchColumn();
        if ($n > 0) {
            $items[] = [
                'tipo'    => 'success',
                'icono'   => 'bi-check-circle-fill',
                'mensaje' => $n . ' incidencia' . ($n !== 1 ? 's' : '') . ' resuelta' . ($n !== 1 ? 's' : ''),
                'url'     => '/incidencias/index',
                'count'   => $n,
            ];
        }

        return $items;
    }
}
