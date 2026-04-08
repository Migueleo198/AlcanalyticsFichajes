<?php

class RegistroTareasModelo
{
    private $db;

    public function __construct($conexion)
    {
        $this->db = $conexion;
    }

    // =========================
    // READ (LISTAR)
    // =========================
    public function getTasks()
    {
        $sql = "
    SELECT 
        t.*, 
        u.nombre AS nombre_usuario,
        tt.nombre AS nombre_tipo
    FROM Tarea t
    INNER JOIN Usuario u ON t.id_usuario = u.id_usuario
    LEFT JOIN TipoTarea tt ON t.id_tipo = tt.id_tipo
    ORDER BY t.fecha DESC
    ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // =========================
    // CREATE (INSERTAR)
    // =========================
    public function createTask($data)
    {
        $sql = "
            INSERT INTO Tarea 
            (id_fichaje, id_usuario, titulo, descripcion, hora_inicio, hora_fin, tiempo_total, estado, fecha)
            VALUES 
            (:id_fichaje, :id_usuario, :titulo, :descripcion, :hora_inicio, :hora_fin, :tiempo_total, :estado, :fecha)
        ";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':id_fichaje' => $data['id_fichaje'],
            ':id_usuario' => $data['id_usuario'],
            ':titulo' => $data['titulo'],
            ':descripcion' => $data['descripcion'],
            ':hora_inicio' => $data['hora_inicio'],
            ':hora_fin' => $data['hora_fin'],
            ':tiempo_total' => $data['tiempo_total'],
            ':estado' => $data['estado'],
            ':fecha' => $data['fecha']
        ]);
    }

    // =========================
    // UPDATE (EDITAR)
    // =========================
    public function updateTask($data)
    {
        $sql = "
            UPDATE Tarea SET
                titulo = :titulo,
                descripcion = :descripcion,
                hora_inicio = :hora_inicio,
                hora_fin = :hora_fin,
                tiempo_total = :tiempo_total,
                estado = :estado,
                fecha = :fecha
            WHERE id_tarea = :id
        ";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':id' => $data['id'],
            ':titulo' => $data['titulo'],
            ':descripcion' => $data['descripcion'],
            ':hora_inicio' => $data['hora_inicio'],
            ':hora_fin' => $data['hora_fin'],
            ':tiempo_total' => $data['tiempo_total'],
            ':estado' => $data['estado'],
            ':fecha' => $data['fecha']
        ]);
    }

    // =========================
    // DELETE (ELIMINAR)
    // =========================
    public function deleteTask($id)
    {
        $sql = "DELETE FROM Tarea WHERE id_tarea = :id";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':id' => $id
        ]);
    }
}