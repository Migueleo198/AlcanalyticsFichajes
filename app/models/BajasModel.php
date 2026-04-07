<?php

class BajasModel
{
    private $conexion;

    public function __construct($db)
    {
        $this->conexion = $db;
    }

    public function obtenerMotivosBaja()
    {
        $sql = "SELECT id_motivo, nombre FROM MotivoAlta ORDER BY nombre ASC";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function insertarBaja($data)
    {
    $sql = "INSERT INTO Bajas 
            (id_usuario, id_motivo, fecha_inicio, fecha_fin) 
            VALUES (:id_usuario, :id_motivo, :fecha_inicio, :fecha_fin)";

    $stmt = $this->conexion->prepare($sql);

    return $stmt->execute([
        ':id_usuario' => $data['id_usuario'],
        ':id_motivo' => $data['id_motivo'],
        ':fecha_inicio' => $data['fecha_inicio'],
        ':fecha_fin' => $data['fecha_fin']
    ]);
    }

    public function obtenerBajas(){
        $sql = "SELECT * FROM Bajas";

        $stmt = $this -> conexion -> prepare($sql);
    }

    public function obtenerBajasPorUsuario($id_usuario)
{
    $sql = "SELECT b.*, m.nombre AS motivo
            FROM Bajas b
            JOIN MotivoAlta m ON b.id_motivo = m.id_motivo
            WHERE b.id_usuario = :id_usuario
            ORDER BY b.fecha_inicio DESC";

    $stmt = $this->conexion->prepare($sql);
    $stmt->execute([':id_usuario' => $id_usuario]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


    // Obtener TODAS las bajas (para admin)
public function obtenerTodasBajas()
{
    $sql = "SELECT b.*, u.nombre AS usuario, m.nombre AS motivo
            FROM Bajas b
            JOIN Usuario u ON b.id_usuario = u.id_usuario
            JOIN MotivoAlta m ON b.id_motivo = m.id_motivo
            ORDER BY b.fecha_solicitud DESC";

    $stmt = $this->conexion->prepare($sql);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Cambiar estado
public function actualizarEstado($id, $estado)
{
    $sql = "UPDATE Bajas SET estado = :estado WHERE id = :id";

    $stmt = $this->conexion->prepare($sql);

    return $stmt->execute([
        ':estado' => $estado,
        ':id' => $id
    ]);
}
}