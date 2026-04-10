<?php

class AusenciaModel
{
    private $db;

    public function __construct($conexion)
    {
        $this->db = $conexion;
    }

    public function getAll()
    {
        $sql = "SELECT a.*, u.nombre AS usuario, m.nombre AS motivo
                FROM ausencias a
                INNER JOIN Usuario u ON u.id_usuario = a.id_usuario
                INNER JOIN motivos_ausencia m ON m.id_motivo = a.id_motivo
                ORDER BY a.id DESC";

        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getMotivos()
    {
        return $this->db->query("SELECT * FROM motivos_ausencia")->fetchAll(PDO::FETCH_ASSOC);
    }

    public function crear($idUsuario, $idMotivo, $inicio, $fin)
    {
        $sql = "INSERT INTO ausencias (id_usuario, id_motivo, fecha_inicio, fecha_fin)
                VALUES (?, ?, ?, ?)";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$idUsuario, $idMotivo, $inicio, $fin]);
    }

    public function eliminar($id)
    {
        $stmt = $this->db->prepare("DELETE FROM ausencias WHERE id = ?");
        $stmt->execute([$id]);
    }
}