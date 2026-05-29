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
        $sql = "SELECT a.*,
                       u.nombre AS usuario,
                       COALESCE(a.motivo_personalizado, m.nombre) AS motivo
                FROM ausencias a
                INNER JOIN Usuario u ON u.id_usuario = a.id_usuario
                LEFT JOIN motivos_ausencia m ON m.id_motivo = a.id_motivo
                ORDER BY a.id DESC";

        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function crear($idUsuario, $motivoTexto, $inicio, $fin)
    {
        $sql = "INSERT INTO ausencias (id_usuario, motivo_personalizado, fecha_inicio, fecha_fin)
                VALUES (?, ?, ?, ?)";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$idUsuario, $motivoTexto, $inicio, $fin]);
    }

    public function getById($id)
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM ausencias WHERE id = ?"
        );
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function editar($id, $idUsuario, $motivoTexto, $inicio, $fin)
    {
        $stmt = $this->db->prepare(
            "UPDATE ausencias
             SET id_usuario = ?, motivo_personalizado = ?, fecha_inicio = ?, fecha_fin = ?
             WHERE id = ?"
        );
        $stmt->execute([$idUsuario, $motivoTexto, $inicio, $fin, $id]);
    }

    public function eliminar($id)
    {
        $stmt = $this->db->prepare("DELETE FROM ausencias WHERE id = ?");
        $stmt->execute([$id]);
    }
}