<?php

class VacacionesModel
{
    private $conexion;

    public function __construct($db)
    {
        $this->conexion = $db;
    }

    
    public function insertarVacacion($data)
    {
        $sql = "INSERT INTO Vacaciones 
                (id_usuario, fecha_inicio, fecha_fin, dias_solicitados, estado, comentario)
                VALUES 
                (:id_usuario, :fecha_inicio, :fecha_fin, :dias, 'pendiente', :comentario)";

        $stmt = $this->conexion->prepare($sql);

        $dias = $this->calcularDias($data['fecha_inicio'], $data['fecha_fin']);

        return $stmt->execute([
            ':id_usuario' => $data['id_usuario'],
            ':fecha_inicio' => $data['fecha_inicio'],
            ':fecha_fin' => $data['fecha_fin'],
            ':dias' => $dias,
            ':comentario' => $data['comentario']
        ]);
    }

    
    public function obtenerVacacionesPorUsuario($id_usuario)
    {
        $sql = "SELECT *
                FROM Vacaciones
                WHERE id_usuario = :id_usuario
                ORDER BY fecha_inicio DESC";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([':id_usuario' => $id_usuario]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    
    public function obtenerTodasVacaciones()
    {
        $sql = "SELECT v.*, u.nombre, u.apellidos
                FROM Vacaciones v
                JOIN Usuario u ON v.id_usuario = u.id_usuario
                ORDER BY v.fecha_inicio DESC";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    
    public function actualizarEstado($id, $estado)
    {
        $sql = "UPDATE Vacaciones 
                SET estado = :estado 
                WHERE id_vacacion = :id";

        $stmt = $this->conexion->prepare($sql);

        return $stmt->execute([
            ':estado' => $estado,
            ':id' => $id
        ]);
    }

    
    public function eliminarVacacion($id)
    {
        $sql = "DELETE FROM Vacaciones 
                WHERE id_vacacion = :id";

        $stmt = $this->conexion->prepare($sql);

        return $stmt->execute([
            ':id' => $id
        ]);
    }

    public function getAll()
    {
    return $this->obtenerTodasVacaciones();
    }

    
    public function haySolapamiento($id_usuario, $inicio, $fin)
    {
        $sql = "SELECT *
                FROM Vacaciones
                WHERE id_usuario = :id_usuario
                AND estado IN ('pendiente','aprobada')
                AND (
                    fecha_inicio <= :fin
                    AND fecha_fin >= :inicio
                )";

        $stmt = $this->conexion->prepare($sql);

        $stmt->execute([
            ':id_usuario' => $id_usuario,
            ':inicio' => $inicio,
            ':fin' => $fin
        ]);

        return $stmt->rowCount() > 0;
    }

    
    private function calcularDias($inicio, $fin)
    {
        $fecha1 = new DateTime($inicio);
        $fecha2 = new DateTime($fin);

        return $fecha1->diff($fecha2)->days + 1;
    }
}