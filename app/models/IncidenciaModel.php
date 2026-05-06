<?php

class IncidenciaModel {

    private $db;

    public function __construct($conexion) {
        $this->db = $conexion;
    }

    // =========================
    // GET TODAS LAS INCIDENCIAS
    // =========================
    public function getIncidencias() {
    $sql = "SELECT 
                i.id_incidencia,
                i.id_fichaje,
                i.mensaje,
                i.respuesta,
                i.estado,
                i.fecha,
                u.nombre AS nombre_usuario
            FROM Incidencia i
            INNER JOIN Fichaje f 
                ON i.id_fichaje = f.id_fichaje
            INNER JOIN Usuario u 
                ON f.id_usuario = u.id_usuario
            ORDER BY i.fecha ASC";

    $stmt = $this->db->prepare($sql);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


    // =========================
    // GET POR FICHAJE
    // =========================
    public function getByFichaje($id_fichaje) {

        $sql = "SELECT * 
                FROM Incidencia 
                WHERE id_fichaje = ?";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id_fichaje]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    // =========================
    // GET UNA INCIDENCIA
    // =========================
    public function getIncidencia($id) {

        $sql = "SELECT * 
                FROM Incidencia 
                WHERE id_incidencia = :id";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    // =========================
    // INSERT
    // =========================
    public function addIncidencia($data) {

        $sql = "INSERT INTO Incidencia (
                    id_fichaje,
                    id_usuario,
                    mensaje,
                    respuesta,
                    estado,
                    fecha
                )
                VALUES (
                    :id_fichaje,
                    :id_usuario,
                    :mensaje,
                    :respuesta,
                    :estado,
                    :fecha
                )";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            'id_fichaje' => $data['id_fichaje'],
            'id_usuario' => $data['id_usuario'],
            'mensaje' => $data['mensaje'],
            'respuesta' => $data['respuesta'] ?? null,
            'estado' => $data['estado'],
            'fecha' => $data['fecha']
        ]);
    }


    // =========================
    // UPDATE
    // =========================
    public function updateIncidencia($id, $data) {

        $sql = "UPDATE Incidencia 
                SET id_fichaje = :id_fichaje,
                    id_usuario = :id_usuario,
                    mensaje = :mensaje,
                    respuesta = :respuesta,
                    estado = :estado,
                    fecha = :fecha
                WHERE id_incidencia = :id";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            'id' => $id,
            'id_fichaje' => $data['id_fichaje'],
            'id_usuario' => $data['id_usuario'],
            'mensaje' => $data['mensaje'],
            'respuesta' => $data['respuesta'] ?? null,
            'estado' => $data['estado'],
            'fecha' => $data['fecha']
        ]);
    }


    // =========================
    // DELETE
    // =========================
    public function removeIncidencia($id) {

        $sql = "DELETE FROM Incidencia 
                WHERE id_incidencia = :id";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute(['id' => $id]);
    }
}