<?php

class IncidenciaModel {

    private $db;

    public function __construct($conexion) {
        $this->db = $conexion;
    }

    public function getIncidencias() {
        $sql = "SELECT id_incidencia, id_fichaje, mensaje, respuesta, estado, fecha 
                FROM Incidencia
                ORDER BY fecha ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getByFichaje($id_fichaje) {

    $sql = "SELECT * FROM Incidencia WHERE id_fichaje = ?";
    $stmt = $this->db->prepare($sql);
    $stmt->execute([$id_fichaje]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function getIncidencia($id) {
        $sql = "SELECT * FROM Incidencia WHERE id_incidencia = :id";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function addIncidencia($data) {
        $sql = "INSERT INTO Incidencia (id_fichaje, mensaje, respuesta, estado, fecha)
                VALUES (:id_fichaje, :mensaje, :respuesta, :estado, :fecha)";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            'id_fichaje' => $data['id_fichaje'],
            'mensaje' => $data['mensaje'],
            'respuesta' => $data['respuesta'],
            'estado' => $data['estado'],
            'fecha' => $data['fecha']
        ]);
    }

    
    public function updateIncidencia($id, $data) {
        $sql = "UPDATE Incidencia 
                SET id_fichaje = :id_fichaje,
                    mensaje = :mensaje,
                    respuesta = :respuesta,
                    estado = :estado,
                    fecha = :fecha
                WHERE id_incidencia = :id";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            'id' => $id,
            'id_fichaje' => $data['id_fichaje'],
            'mensaje' => $data['mensaje'],
            'respuesta' => $data['respuesta'],
            'estado' => $data['estado'],
            'fecha' => $data['fecha']
        ]);
    }

    public function removeIncidencia($id) {
        $sql = "DELETE FROM Incidencia WHERE id_incidencia = :id";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }
}