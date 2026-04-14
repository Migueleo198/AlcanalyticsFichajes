<?php

class InformeModel {

    private $db;

    public function __construct($pdo) {
        $this->db = $pdo;
    }

    public function obtenerInforme($desde, $hasta, $usuario = null) {

    $sql = "
        SELECT 
            u.nombre,
            u.apellidos,
            u.dni,
            f.fecha,
            f.hora_entrada,
            f.hora_salida,
            f.tiempo_total,
            f.horas_ordinarias,
            f.horas_extra,
            f.estado,

            COALESCE((
                SELECT SUM(TIMESTAMPDIFF(MINUTE, d.hora_inicio, d.hora_fin)) / 60
                FROM Descanso d
                WHERE d.id_fichaje = f.id_fichaje
            ), 0) AS horas_descanso,

            i.mensaje AS incidencia,
            i.estado AS estado_incidencia

        FROM Fichaje f
        JOIN Usuario u ON u.id_usuario = f.id_usuario
        LEFT JOIN Incidencia i ON i.id_fichaje = f.id_fichaje

        WHERE f.fecha BETWEEN :desde AND :hasta
    ";

    if ($usuario) {
        $sql .= " AND f.id_usuario = :usuario";
    }

    $sql .= " ORDER BY f.fecha ASC";

    $stmt = $this->db->prepare($sql);

    $stmt->bindValue(':desde', $desde);
    $stmt->bindValue(':hasta', $hasta);

    if ($usuario) {
        $stmt->bindValue(':usuario', $usuario);
    }

    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

    public function obtenerUsuarios() {
        $stmt = $this->db->query("SELECT id_usuario, nombre, apellidos FROM Usuario");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function getByUserAndRange($userId, $desde, $hasta) {

    $stmt = $this->db->prepare("
        SELECT f.*, u.nombre, u.apellidos
        FROM fichajes f
        JOIN Usuario u ON u.id_usuario = f.user_id
        WHERE f.user_id = ?
        AND f.fecha BETWEEN ? AND ?
        ORDER BY f.fecha ASC
    ");

    $stmt->execute([$userId, $desde, $hasta]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
}