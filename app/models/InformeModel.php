<?php

class InformeModel {

    private $db;

    public function __construct($pdo) {
        $this->db = $pdo;
    }

    public function obtenerInforme($desde, $hasta, $usuario = null) {

        $sql = "SELECT 
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
                    COALESCE(SUM(TIMESTAMPDIFF(MINUTE, d.hora_inicio, d.hora_fin)) / 60, 0) AS horas_descanso,
                    i.mensaje AS incidencia,
                    i.estado AS estado_incidencia

                FROM Fichaje f
                JOIN Usuario u ON u.id_usuario = f.id_usuario
                LEFT JOIN Descanso d ON d.id_fichaje = f.id_fichaje
                LEFT JOIN Incidencia i ON i.id_fichaje = f.id_fichaje

                WHERE f.fecha BETWEEN :desde AND :hasta";

        if ($usuario) {
            $sql .= " AND u.id_usuario = :usuario";
        }

        $sql .= " GROUP BY f.id_fichaje ORDER BY f.fecha ASC";

        $stmt = $this->db->prepare($sql);

        $stmt->bindParam(':desde', $desde);
        $stmt->bindParam(':hasta', $hasta);

        if ($usuario) {
            $stmt->bindParam(':usuario', $usuario);
        }

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerUsuarios() {
        $stmt = $this->db->query("SELECT id_usuario, nombre, apellidos FROM Usuario");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}