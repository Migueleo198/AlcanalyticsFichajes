<?php

class InformeModel {

    private $db;

    public function __construct($pdo) {
        $this->db = $pdo;
    }

    public function obtenerInforme($desde, $hasta, $usuarios = []) {

        // =========================
        // 🔥 NORMALIZACIÓN SEGURA
        // =========================
        if (!is_array($usuarios)) {
            $usuarios = [$usuarios];
        }

        // limpiar basura
        $usuarios = array_values(array_filter($usuarios, function($u) {
            return $u !== null && $u !== '' && is_numeric($u);
        }));

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

        // =========================
        // FILTRO USUARIOS
        // =========================
        if (!empty($usuarios)) {

            $placeholders = [];

            foreach ($usuarios as $index => $u) {
                $placeholders[] = ":usuario$index";
            }

            $sql .= " AND f.id_usuario IN (" . implode(',', $placeholders) . ")";
        }

        $sql .= " ORDER BY f.fecha ASC";

        $stmt = $this->db->prepare($sql);

        $stmt->bindValue(':desde', $desde);
        $stmt->bindValue(':hasta', $hasta);

        // bind dinámico seguro
        foreach ($usuarios as $index => $u) {
            $stmt->bindValue(":usuario$index", (int)$u, PDO::PARAM_INT);
        }

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerUsuarios() {
        $stmt = $this->db->query("
            SELECT id_usuario, nombre, apellidos 
            FROM Usuario
            ORDER BY nombre ASC
        ");

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