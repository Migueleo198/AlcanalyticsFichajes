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
                f.id_fichaje,
                u.nombre,
                u.apellidos,
                u.dni,
                f.fecha,
                f.hora_entrada,
                f.hora_salida,
                f.estado,
                f.horas_extra AS horas_extra_guardada,

                CASE WHEN f.hora_salida IS NOT NULL
                    THEN ROUND(TIMESTAMPDIFF(MINUTE, f.hora_entrada, f.hora_salida) / 60.0, 2)
                    ELSE 0 END AS horas_brutas,

                COALESCE((
                    SELECT ROUND(SUM(TIMESTAMPDIFF(MINUTE, d.hora_inicio, d.hora_fin)) / 60.0, 2)
                    FROM Descanso d
                    WHERE d.id_fichaje = f.id_fichaje
                    AND d.hora_fin IS NOT NULL
                ), 0) AS horas_descanso,

                i.mensaje AS incidencia,
                i.estado  AS estado_incidencia

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

        $filas = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($filas as &$f) {
            $brutas   = (float)($f['horas_brutas']   ?? 0);
            $descanso = (float)($f['horas_descanso'] ?? 0);
            $netas    = max(0, round($brutas - $descanso, 2));

            // Si hay un valor manual guardado en BD, usarlo; si no, calcular automáticamente
            $extraGuardada = $f['horas_extra_guardada'];
            $f['horas_extra_manual'] = ($extraGuardada !== null);

            $f['horas_netas']      = $netas;
            $f['horas_extra']      = $f['horas_extra_manual']
                                        ? round((float)$extraGuardada, 2)
                                        : round(max(0, $netas - 7.5), 2);
            $f['horas_ordinarias'] = round(max(0, $netas - $f['horas_extra']), 2);
        }
        unset($f);

        return $filas;
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