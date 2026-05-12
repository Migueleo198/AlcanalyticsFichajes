<?php

class EstadisticasModel {

    private $db;

    public function __construct($conexion) {
        $this->db = $conexion;
    }

    // ============================================================
    // RESUMEN GENERAL
    // ============================================================

    public function getResumenGeneral() {
        $sql = "SELECT
                    (SELECT COUNT(*) FROM Usuario WHERE activo = 1) AS empleados_activos,
                    (SELECT COUNT(*) FROM Fichaje WHERE fecha = CURDATE()) AS fichajes_hoy,
                    (SELECT COALESCE(SUM(horas_ordinarias + horas_extra), 0) FROM Fichaje WHERE fecha = CURDATE()) AS horas_hoy,
                    (SELECT COUNT(*) FROM Fichaje WHERE fecha = CURDATE() AND estado = 'retraso') AS retrasos_hoy,
                    (SELECT COUNT(*) FROM Fichaje WHERE MONTH(fecha) = MONTH(CURDATE()) AND YEAR(fecha) = YEAR(CURDATE())) AS fichajes_mes,
                    (SELECT COALESCE(SUM(horas_ordinarias + horas_extra), 0) FROM Fichaje WHERE MONTH(fecha) = MONTH(CURDATE()) AND YEAR(fecha) = YEAR(CURDATE())) AS horas_mes,
                    (SELECT COUNT(*) FROM Fichaje WHERE MONTH(fecha) = MONTH(CURDATE()) AND YEAR(fecha) = YEAR(CURDATE()) AND estado = 'retraso') AS retrasos_mes,
                    (SELECT COALESCE(SUM(horas_extra), 0) FROM Fichaje WHERE MONTH(fecha) = MONTH(CURDATE()) AND YEAR(fecha) = YEAR(CURDATE())) AS horas_extra_mes";
        return $this->db->query($sql)->fetch(PDO::FETCH_ASSOC);
    }

    public function getFichajesPorDiaSemana() {
        $sql = "SELECT
                    DAYOFWEEK(fecha) AS dia_num,
                    CASE DAYOFWEEK(fecha)
                        WHEN 2 THEN 'Lunes'
                        WHEN 3 THEN 'Martes'
                        WHEN 4 THEN 'Miércoles'
                        WHEN 5 THEN 'Jueves'
                        WHEN 6 THEN 'Viernes'
                        WHEN 7 THEN 'Sábado'
                        WHEN 1 THEN 'Domingo'
                    END AS dia,
                    COUNT(*) AS total
                FROM Fichaje
                WHERE fecha >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
                GROUP BY DAYOFWEEK(fecha)
                ORDER BY DAYOFWEEK(fecha)";
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getFichajesUltimosMeses($meses = 6) {
        $sql = "SELECT
                    DATE_FORMAT(fecha, '%Y-%m') AS mes,
                    DATE_FORMAT(fecha, '%b %Y') AS mes_label,
                    COUNT(*) AS total_fichajes,
                    COALESCE(SUM(horas_ordinarias + horas_extra), 0) AS total_horas,
                    COUNT(CASE WHEN estado = 'retraso' THEN 1 END) AS total_retrasos
                FROM Fichaje
                WHERE fecha >= DATE_SUB(CURDATE(), INTERVAL :meses MONTH)
                GROUP BY DATE_FORMAT(fecha, '%Y-%m')
                ORDER BY mes ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':meses' => $meses]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTopEmpleadosHoras($limite = 5) {
        $sql = "SELECT
                    u.nombre,
                    u.apellidos,
                    COALESCE(SUM(f.horas_ordinarias), 0) AS horas_ordinarias,
                    COALESCE(SUM(f.horas_extra), 0) AS horas_extra,
                    COALESCE(SUM(f.horas_ordinarias + f.horas_extra), 0) AS total_horas
                FROM Usuario u
                LEFT JOIN Fichaje f ON f.id_usuario = u.id_usuario
                    AND MONTH(f.fecha) = MONTH(CURDATE())
                    AND YEAR(f.fecha) = YEAR(CURDATE())
                WHERE u.activo = 1
                GROUP BY u.id_usuario
                ORDER BY total_horas DESC
                LIMIT :limite";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limite', (int)$limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ============================================================
    // ESTADÍSTICAS DE FICHAJES
    // ============================================================

    public function getFichajesPorEstado($desde = null, $hasta = null) {
        $desde = $desde ?? date('Y-m-01');
        $hasta = $hasta ?? date('Y-m-d');
        $sql = "SELECT
                    estado,
                    COUNT(*) AS total
                FROM Fichaje
                WHERE fecha BETWEEN :desde AND :hasta
                GROUP BY estado";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':desde' => $desde, ':hasta' => $hasta]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getFichajesDiarios($desde = null, $hasta = null) {
        $desde = $desde ?? date('Y-m-01');
        $hasta = $hasta ?? date('Y-m-d');
        $sql = "SELECT
                    fecha,
                    COUNT(*) AS total_fichajes,
                    COUNT(CASE WHEN estado = 'retraso' THEN 1 END) AS retrasos,
                    COUNT(CASE WHEN estado = 'cerrado' OR estado = 'completado' THEN 1 END) AS completados
                FROM Fichaje
                WHERE fecha BETWEEN :desde AND :hasta
                GROUP BY fecha
                ORDER BY fecha ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':desde' => $desde, ':hasta' => $hasta]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getFichajesPorEmpleado($desde = null, $hasta = null) {
        $desde = $desde ?? date('Y-m-01');
        $hasta = $hasta ?? date('Y-m-d');
        $sql = "SELECT
                    u.nombre,
                    u.apellidos,
                    COUNT(f.id_fichaje) AS total_fichajes,
                    COUNT(CASE WHEN f.estado = 'retraso' THEN 1 END) AS retrasos,
                    COALESCE(SUM(f.horas_ordinarias + f.horas_extra), 0) AS total_horas
                FROM Usuario u
                LEFT JOIN Fichaje f ON f.id_usuario = u.id_usuario
                    AND f.fecha BETWEEN :desde AND :hasta
                WHERE u.activo = 1
                GROUP BY u.id_usuario
                ORDER BY total_fichajes DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':desde' => $desde, ':hasta' => $hasta]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getResumenFichajes($desde = null, $hasta = null) {
        $desde = $desde ?? date('Y-m-01');
        $hasta = $hasta ?? date('Y-m-d');
        $sql = "SELECT
                    COUNT(*) AS total,
                    COUNT(CASE WHEN estado = 'retraso' THEN 1 END) AS retrasos,
                    COUNT(CASE WHEN estado = 'cerrado' OR estado = 'completado' OR estado = 'abierto' THEN 1 END) AS normales,
                    COUNT(CASE WHEN estado = 'abierto' THEN 1 END) AS abiertos,
                    AVG(TIMESTAMPDIFF(MINUTE, hora_entrada, hora_salida)) / 60 AS duracion_media_horas
                FROM Fichaje
                WHERE fecha BETWEEN :desde AND :hasta
                AND hora_salida IS NOT NULL";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':desde' => $desde, ':hasta' => $hasta]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // ============================================================
    // ESTADÍSTICAS DE HORAS
    // ============================================================

    public function getHorasPorDia($desde = null, $hasta = null) {
        $desde = $desde ?? date('Y-m-01');
        $hasta = $hasta ?? date('Y-m-d');
        $sql = "SELECT
                    fecha,
                    COALESCE(SUM(horas_ordinarias), 0) AS ordinarias,
                    COALESCE(SUM(horas_extra), 0) AS extra,
                    COALESCE(SUM(horas_ordinarias + horas_extra), 0) AS total
                FROM Fichaje
                WHERE fecha BETWEEN :desde AND :hasta
                GROUP BY fecha
                ORDER BY fecha ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':desde' => $desde, ':hasta' => $hasta]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getHorasPorEmpleado($desde = null, $hasta = null) {
        $desde = $desde ?? date('Y-m-01');
        $hasta = $hasta ?? date('Y-m-d');
        $sql = "SELECT
                    u.nombre,
                    u.apellidos,
                    COALESCE(SUM(f.horas_ordinarias), 0) AS ordinarias,
                    COALESCE(SUM(f.horas_extra), 0) AS extra,
                    COALESCE(SUM(f.horas_ordinarias + f.horas_extra), 0) AS total,
                    COUNT(f.id_fichaje) AS dias_trabajados
                FROM Usuario u
                LEFT JOIN Fichaje f ON f.id_usuario = u.id_usuario
                    AND f.fecha BETWEEN :desde AND :hasta
                WHERE u.activo = 1
                GROUP BY u.id_usuario
                ORDER BY total DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':desde' => $desde, ':hasta' => $hasta]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getResumenHoras($desde = null, $hasta = null) {
        $desde = $desde ?? date('Y-m-01');
        $hasta = $hasta ?? date('Y-m-d');
        $sql = "SELECT
                    COALESCE(SUM(horas_ordinarias), 0) AS total_ordinarias,
                    COALESCE(SUM(horas_extra), 0) AS total_extra,
                    COALESCE(SUM(horas_ordinarias + horas_extra), 0) AS total_horas,
                    COALESCE(AVG(horas_ordinarias + horas_extra), 0) AS media_diaria
                FROM Fichaje
                WHERE fecha BETWEEN :desde AND :hasta";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':desde' => $desde, ':hasta' => $hasta]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // ============================================================
    // ESTADÍSTICAS DE RETRASOS
    // ============================================================

    public function getRetrasosPorDia($desde = null, $hasta = null) {
        $desde = $desde ?? date('Y-m-01');
        $hasta = $hasta ?? date('Y-m-d');
        $sql = "SELECT
                    fecha,
                    COUNT(CASE WHEN estado = 'retraso' THEN 1 END) AS retrasos,
                    COUNT(*) AS total_fichajes
                FROM Fichaje
                WHERE fecha BETWEEN :desde AND :hasta
                GROUP BY fecha
                ORDER BY fecha ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':desde' => $desde, ':hasta' => $hasta]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getRetrasosPorEmpleado($desde = null, $hasta = null) {
        $desde = $desde ?? date('Y-m-01');
        $hasta = $hasta ?? date('Y-m-d');
        $sql = "SELECT
                    u.nombre,
                    u.apellidos,
                    COUNT(f.id_fichaje) AS total_fichajes,
                    COUNT(CASE WHEN f.estado = 'retraso' THEN 1 END) AS retrasos,
                    ROUND(COUNT(CASE WHEN f.estado = 'retraso' THEN 1 END) * 100.0 / NULLIF(COUNT(f.id_fichaje), 0), 1) AS pct_retraso
                FROM Usuario u
                LEFT JOIN Fichaje f ON f.id_usuario = u.id_usuario
                    AND f.fecha BETWEEN :desde AND :hasta
                WHERE u.activo = 1
                GROUP BY u.id_usuario
                ORDER BY retrasos DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':desde' => $desde, ':hasta' => $hasta]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getHoraEntradaMedia($desde = null, $hasta = null) {
        $desde = $desde ?? date('Y-m-01');
        $hasta = $hasta ?? date('Y-m-d');
        $sql = "SELECT
                    u.nombre,
                    u.apellidos,
                    TIME_FORMAT(SEC_TO_TIME(AVG(TIME_TO_SEC(f.hora_entrada))), '%H:%i') AS hora_media_entrada,
                    COUNT(CASE WHEN f.estado = 'retraso' THEN 1 END) AS retrasos
                FROM Usuario u
                INNER JOIN Fichaje f ON f.id_usuario = u.id_usuario
                    AND f.fecha BETWEEN :desde AND :hasta
                WHERE u.activo = 1
                GROUP BY u.id_usuario
                ORDER BY hora_media_entrada ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':desde' => $desde, ':hasta' => $hasta]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getResumenRetrasos($desde = null, $hasta = null) {
        $desde = $desde ?? date('Y-m-01');
        $hasta = $hasta ?? date('Y-m-d');
        $sql = "SELECT
                    COUNT(*) AS total_fichajes,
                    COUNT(CASE WHEN estado = 'retraso' THEN 1 END) AS total_retrasos,
                    ROUND(COUNT(CASE WHEN estado = 'retraso' THEN 1 END) * 100.0 / NULLIF(COUNT(*), 0), 1) AS pct_retraso,
                    (SELECT nombre FROM Usuario u INNER JOIN Fichaje f2 ON f2.id_usuario = u.id_usuario
                     WHERE f2.estado = 'retraso' AND f2.fecha BETWEEN :desde2 AND :hasta2
                     GROUP BY f2.id_usuario ORDER BY COUNT(*) DESC LIMIT 1) AS empleado_mas_retrasos
                FROM Fichaje
                WHERE fecha BETWEEN :desde AND :hasta";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':desde' => $desde, ':hasta' => $hasta, ':desde2' => $desde, ':hasta2' => $hasta]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // ============================================================
    // ESTADÍSTICAS DE ACTIVIDAD
    // ============================================================

    public function getActividadPorHora($desde = null, $hasta = null) {
        $desde = $desde ?? date('Y-m-01');
        $hasta = $hasta ?? date('Y-m-d');
        $sql = "SELECT
                    HOUR(hora_entrada) AS hora,
                    COUNT(*) AS entradas
                FROM Fichaje
                WHERE fecha BETWEEN :desde AND :hasta
                AND hora_entrada IS NOT NULL
                GROUP BY HOUR(hora_entrada)
                ORDER BY hora ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':desde' => $desde, ':hasta' => $hasta]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getActividadDiaria($desde = null, $hasta = null) {
        $desde = $desde ?? date('Y-m-01');
        $hasta = $hasta ?? date('Y-m-d');
        $sql = "SELECT
                    fecha,
                    COUNT(DISTINCT id_usuario) AS empleados_activos,
                    COUNT(*) AS total_fichajes,
                    COALESCE(SUM(horas_ordinarias + horas_extra), 0) AS horas_totales
                FROM Fichaje
                WHERE fecha BETWEEN :desde AND :hasta
                GROUP BY fecha
                ORDER BY fecha ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':desde' => $desde, ':hasta' => $hasta]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAusenciasYVacaciones($desde = null, $hasta = null) {
        $desde = $desde ?? date('Y-m-01');
        $hasta = $hasta ?? date('Y-m-d');
        $sql = "SELECT
                    (SELECT COUNT(*) FROM ausencias WHERE fecha_inicio BETWEEN :desde AND :hasta) AS total_ausencias,
                    (SELECT COUNT(*) FROM Vacaciones WHERE fecha_inicio BETWEEN :desde2 AND :hasta2 AND estado = 'aprobada') AS vacaciones_aprobadas,
                    (SELECT COUNT(*) FROM Vacaciones WHERE fecha_inicio BETWEEN :desde3 AND :hasta3 AND estado = 'pendiente') AS vacaciones_pendientes";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':desde' => $desde, ':hasta' => $hasta,
            ':desde2' => $desde, ':hasta2' => $hasta,
            ':desde3' => $desde, ':hasta3' => $hasta,
        ]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getPresenciaPorEmpleado($desde = null, $hasta = null) {
        $desde = $desde ?? date('Y-m-01');
        $hasta = $hasta ?? date('Y-m-d');
        $sql = "SELECT
                    u.nombre,
                    u.apellidos,
                    COUNT(f.id_fichaje) AS dias_presentes,
                    COALESCE(SUM(f.horas_ordinarias + f.horas_extra), 0) AS horas_totales,
                    COUNT(CASE WHEN f.estado = 'retraso' THEN 1 END) AS retrasos
                FROM Usuario u
                LEFT JOIN Fichaje f ON f.id_usuario = u.id_usuario
                    AND f.fecha BETWEEN :desde AND :hasta
                WHERE u.activo = 1
                GROUP BY u.id_usuario
                ORDER BY dias_presentes DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':desde' => $desde, ':hasta' => $hasta]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getResumenActividad($desde = null, $hasta = null) {
        $desde = $desde ?? date('Y-m-01');
        $hasta = $hasta ?? date('Y-m-d');
        $sql = "SELECT
                    COUNT(DISTINCT id_usuario) AS empleados_registrados,
                    COUNT(DISTINCT fecha) AS dias_con_actividad,
                    COUNT(*) AS total_fichajes,
                    COALESCE(SUM(horas_ordinarias + horas_extra), 0) AS total_horas
                FROM Fichaje
                WHERE fecha BETWEEN :desde AND :hasta";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':desde' => $desde, ':hasta' => $hasta]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // ============================================================
    // HELPERS
    // ============================================================

    public function obtenerUsuarios() {
        $sql = "SELECT id_usuario, nombre, apellidos FROM Usuario WHERE activo = 1 ORDER BY nombre";
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }
}
