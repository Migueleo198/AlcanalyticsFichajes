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
        $sql = "SELECT id_motivo, nombre, dias, tipo, limite_horas_anual, nota_limite FROM MotivoAlta ORDER BY nombre ASC";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getHorasUsadasAnio($idUsuario, $idMotivo, $anio)
    {
        $stmt = $this->conexion->prepare(
            "SELECT COALESCE(SUM(horas), 0) AS total
             FROM Bajas
             WHERE id_usuario = ? AND id_motivo = ?
               AND YEAR(fecha_inicio) = ?
               AND estado != 'rechazada'"
        );
        $stmt->execute([$idUsuario, $idMotivo, $anio]);
        return (float) $stmt->fetchColumn();
    }

    public function insertarBaja($data)
    {
        $sql = "INSERT INTO Bajas
                (id_usuario, id_motivo, fecha_inicio, fecha_fin, descripcion, horas, es_remunerada)
                VALUES (:id_usuario, :id_motivo, :fecha_inicio, :fecha_fin, :descripcion, :horas, :es_remunerada)";

        $stmt = $this->conexion->prepare($sql);

        return $stmt->execute([
            ':id_usuario'    => $data['id_usuario'],
            ':id_motivo'     => $data['id_motivo'],
            ':fecha_inicio'  => $data['fecha_inicio'],
            ':fecha_fin'     => $data['fecha_fin'],
            ':descripcion'   => $data['descripcion']   ?? null,
            ':horas'         => $data['horas']         ?? null,
            ':es_remunerada' => $data['es_remunerada'] ?? 1,
        ]);
    }

    public function obtenerBajas()
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

    public function obtenerBajasPorUsuario($id_usuario)
{
    $sql = "SELECT b.*, m.nombre AS motivo, b.horas, b.es_remunerada
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
    $sql = "SELECT b.*, u.nombre AS usuario, m.nombre AS motivo, b.horas, b.es_remunerada
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

public function eliminarBaja($id)
{
    $stmt = $this->conexion->prepare("DELETE FROM Bajas WHERE id = ?");
    return $stmt->execute([$id]);
}

public function editarBaja($id, $idMotivo, $fechaInicio, $fechaFin, $descripcion, $estado = null)
{
    $sql = $estado !== null
        ? "UPDATE Bajas SET id_motivo=:m, fecha_inicio=:fi, fecha_fin=:ff, descripcion=:d, estado=:e WHERE id=:id"
        : "UPDATE Bajas SET id_motivo=:m, fecha_inicio=:fi, fecha_fin=:ff, descripcion=:d WHERE id=:id";

    $params = [
        ':m'  => $idMotivo,
        ':fi' => $fechaInicio,
        ':ff' => !empty($fechaFin) ? $fechaFin : null,
        ':d'  => $descripcion,
        ':id' => $id,
    ];

    if ($estado !== null) {
        $params[':e'] = $estado;
    }

    return $this->conexion->prepare($sql)->execute($params);
}
}