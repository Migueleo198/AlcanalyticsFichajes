<?php

class FichajeModelo {

    private $db;

    public function __construct($conexion) {
        $this->db = $conexion;
    }

    public function getFichajes(){
    $sql = "SELECT 
                f.id_fichaje,
                f.fecha,
                f.hora_entrada,
                f.hora_salida,
                f.tiempo_total,
                f.estado,
                f.horas_ordinarias,
                f.horas_extra,
                u.nombre,
                u.apellidos,
                m.nombre AS motivo
            FROM Fichaje f
            INNER JOIN Usuario u ON f.id_usuario = u.id_usuario
            LEFT JOIN MotivoAlta m ON f.id_motivo = m.id_motivo
            ORDER BY f.fecha DESC, f.hora_entrada DESC";

    $stmt = $this->db->query($sql);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

    public function getFichaje($id_usuario){

    }

    // 🔍 FICHAJE ACTIVO
    public function obtenerFichajeActivo($id_usuario) {
        $sql = "SELECT * FROM Fichaje 
                WHERE id_usuario = ? 
                AND estado = 'abierto'
                ORDER BY id_fichaje DESC 
                LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id_usuario]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // ▶️ INICIAR (PROTEGIDO)
    public function iniciarFichaje($id_usuario) {

        // evitar duplicados
        $check = $this->obtenerFichajeActivo($id_usuario);
        if ($check) return false;

        $sql = "INSERT INTO Fichaje 
                (id_usuario, hora_entrada, fecha, estado, hora_salida)
                VALUES (?, CURTIME(), CURDATE(), 'abierto', NULL)";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id_usuario]);
    }

    // ⏸️ INICIAR DESCANSO
    public function iniciarDescanso($id_fichaje) {
        $sql = "INSERT INTO Descanso (id_fichaje, hora_inicio)
                VALUES (?, CURTIME())";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id_fichaje]);
    }

    // 🔄 FINALIZAR DESCANSO
    public function finalizarDescanso($id_fichaje) {
        $sql = "UPDATE Descanso 
                SET hora_fin = CURTIME()
                WHERE id_fichaje = ? 
                AND hora_fin IS NULL";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id_fichaje]);
    }

    // ⛔ FINALIZAR FICHAJE
    public function finalizarFichaje($id_fichaje) {
        $sql = "UPDATE Fichaje 
                SET hora_salida = CURTIME(), estado = 'cerrado'
                WHERE id_fichaje = ?";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id_fichaje]);
    }

    // 🔍 DESCANSO ACTIVO
    public function estaEnDescanso($id_fichaje) {
        $sql = "SELECT * FROM Descanso 
                WHERE id_fichaje = ? 
                AND hora_fin IS NULL";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id_fichaje]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // 🔍 TODOS LOS DESCANSOS
    public function obtenerDescansos($id_fichaje) {
        $sql = "SELECT * FROM Descanso WHERE id_fichaje = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id_fichaje]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getEmpleadosActivos() {
    $sql = "SELECT COUNT(*) as total FROM Usuario WHERE activo = 1";
    return $this->db->query($sql)->fetch()['total'];
}

public function getFichajesHoy() {
    $sql = "SELECT COUNT(*) as total FROM Fichaje WHERE fecha = CURDATE()";
    return $this->db->query($sql)->fetch()['total'];
}

public function getHorasHoy() {
    $sql = "SELECT SUM(horas_ordinarias + horas_extra) as total 
            FROM Fichaje 
            WHERE fecha = CURDATE()";

    return $this->db->query($sql)->fetch()['total'] ?? 0;
}

public function getRetrasosHoy() {
    $sql = "SELECT COUNT(*) as total 
            FROM Fichaje 
            WHERE fecha = CURDATE() 
            AND estado = 'retraso'";

    return $this->db->query($sql)->fetch()['total'];
}


    public function getByUserAndRange($userId, $desde, $hasta) {

        $stmt = $this->db->prepare("
            SELECT f.*, u.nombre, u.apellidos, u.dni
            FROM Fichaje f
            JOIN Usuario u ON u.id_usuario = f.id_usuario
            WHERE f.id_usuario = ?
            AND f.fecha BETWEEN ? AND ?
            ORDER BY f.fecha ASC
        ");

        $stmt->execute([$userId, $desde, $hasta]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}