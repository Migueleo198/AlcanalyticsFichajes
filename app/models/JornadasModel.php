<?php

class JornadasModel
{
    private $db;

    public function __construct()
    {
        require_once __DIR__ . '/../config/Database.php';

        $database = new Database();
        $this->db = $database->conectar();
    }

    // =========================
    // GET ALL JORNADAS (ADMIN)
    // =========================
    public function getJornadas()
    {
        $sql = "
            SELECT 
                j.*,
                u.nombre AS nombre_usuario,
                u.apellidos
            FROM Jornada j
            INNER JOIN Usuario u ON j.id_usuario = u.id_usuario
            ORDER BY j.id_jornada DESC
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // =========================
    // GET JORNADAS BY USER (NORMAL USER)
    // =========================
    public function getJornadasByUser($userId)
    {
        $stmt = $this->db->prepare("
            SELECT 
                j.*,
                u.nombre AS nombre_usuario,
                u.apellidos
            FROM Jornada j
            INNER JOIN Usuario u ON j.id_usuario = u.id_usuario
            WHERE j.id_usuario = :user_id
            ORDER BY j.id_jornada DESC
        ");

        $stmt->execute([
            ':user_id' => $userId
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getJornadaById($id)
    {
        $stmt = $this->db->prepare("
            SELECT * FROM Jornada
            WHERE id_jornada = :id
        ");

        $stmt->execute([':id' => $id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function addJornada($data)
    {
        $stmt = $this->db->prepare("
            INSERT INTO Jornada
            (id_usuario, horas_dia, horas_semana, fecha_inicio, fecha_fin)
            VALUES
            (:id_usuario, :horas_dia, :horas_semana, :fecha_inicio, :fecha_fin)
        ");

        $ok = $stmt->execute([
            ':id_usuario'   => $data['id_usuario'],
            ':horas_dia'    => $data['horas_dia'],
            ':horas_semana' => $data['horas_semana'],
            ':fecha_inicio' => $data['fecha_inicio'],
            ':fecha_fin'    => $data['fecha_fin']
        ]);

        return $ok ? $this->db->lastInsertId() : false;
    }

    public function editJornada($data)
    {
        $stmt = $this->db->prepare("
            UPDATE Jornada SET
                id_usuario = :id_usuario,
                horas_dia = :horas_dia,
                horas_semana = :horas_semana,
                fecha_inicio = :fecha_inicio,
                fecha_fin = :fecha_fin
            WHERE id_jornada = :id
        ");

        return $stmt->execute([
            ':id'           => $data['id'],
            ':id_usuario'   => $data['id_usuario'],
            ':horas_dia'    => $data['horas_dia'],
            ':horas_semana' => $data['horas_semana'],
            ':fecha_inicio' => $data['fecha_inicio'],
            ':fecha_fin'    => $data['fecha_fin']
        ]);
    }

    public function removeJornada($id)
    {
        $stmt = $this->db->prepare("
            DELETE FROM Jornada
            WHERE id_jornada = :id
        ");

        return $stmt->execute([':id' => $id]);
    }

    public function getUsuarios()
    {
        $stmt = $this->db->prepare("
            SELECT id_usuario, nombre, apellidos
            FROM Usuario
            ORDER BY nombre ASC
        ");

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}