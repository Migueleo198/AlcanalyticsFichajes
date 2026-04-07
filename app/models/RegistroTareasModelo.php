<?php


class RegistroTareasModelo{

private $db;

    public function __construct($conexion) {
        $this->db = $conexion;
    }

    public function getTasks(){
    $sql = "SELECT 
            t.id_tarea,
            t.id_fichaje,
            t.id_usuario,
            u.nombre_usuario,
            t.titulo,
            t.descripcion,
            t.hora_inicio,
            t.hora_fin,
            t.tiempo_total,
            t.estado,
            t.fecha,
            t.id_tipo,
            tt.nombre FROM Tarea t
            LEFT JOIN Usuario u on t.id_usuario = u.id_usuario
            LEFT JOIN TipoTarea tt on t.id_tipo = tt.id_tipo"
            ;

    $stmt = $this->db->query($sql);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


}