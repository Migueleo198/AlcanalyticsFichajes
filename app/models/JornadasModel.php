<?php

class JornadasModel {

    private $db;

    public function __construct($conexion) {
        $this->db = $conexion;
    }

    public function getJornadas() {

        $sql = "
            SELECT 
                j.*,
                u.nombre_usuario
            FROM Jornada j
            LEFT JOIN Usuario u 
                ON j.id_usuario = u.id_usuario
            ORDER BY j.fecha_inicio DESC
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}