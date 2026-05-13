<?php

class ConfiguracionModel
{
    private $db;

    public function __construct($conexion)
    {
        $this->db = $conexion;
        $this->initTabla();
    }

    // Crea la tabla y valores por defecto si no existen
    private function initTabla()
    {
        $this->db->exec("
            CREATE TABLE IF NOT EXISTS `configuracion` (
                `id`          int(11)      NOT NULL AUTO_INCREMENT,
                `clave`       varchar(100) NOT NULL,
                `valor`       text         DEFAULT NULL,
                `descripcion` varchar(255) DEFAULT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `uk_clave` (`clave`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3
        ");

        $defaults = [
            ['nombre_empresa',          'Alcanalytics',  'Nombre de la empresa'],
            ['hora_inicio_jornada',     '09:00',         'Hora oficial de inicio de la jornada laboral'],
            ['umbral_retraso_minutos',  '30',            'Minutos de margen antes de marcar un fichaje como retraso'],
            ['horas_jornada_defecto',   '7.5',           'Horas de trabajo diarias por defecto al asignar jornadas'],
            ['horas_semana_defecto',    '37.5',          'Horas de trabajo semanales por defecto al asignar jornadas'],
        ];

        $stmt = $this->db->prepare(
            "INSERT IGNORE INTO configuracion (clave, valor, descripcion) VALUES (?, ?, ?)"
        );
        foreach ($defaults as $d) {
            $stmt->execute($d);
        }
    }

    // Devuelve todas las claves como array asociativo [clave => valor]
    public function getAll(): array
    {
        $rows = $this->db->query(
            "SELECT clave, valor FROM configuracion"
        )->fetchAll(PDO::FETCH_ASSOC);

        $result = [];
        foreach ($rows as $r) {
            $result[$r['clave']] = $r['valor'];
        }
        return $result;
    }

    public function get(string $clave, $default = null)
    {
        $stmt = $this->db->prepare(
            "SELECT valor FROM configuracion WHERE clave = ?"
        );
        $stmt->execute([$clave]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $row['valor'] : $default;
    }

    public function setMultiple(array $data): bool
    {
        $stmt = $this->db->prepare(
            "INSERT INTO configuracion (clave, valor)
             VALUES (?, ?)
             ON DUPLICATE KEY UPDATE valor = VALUES(valor)"
        );
        foreach ($data as $clave => $valor) {
            $stmt->execute([$clave, $valor]);
        }
        return true;
    }

    // ============================================================
    // TIPOS DE TAREA
    // ============================================================

    public function getTiposTarea(): array
    {
        return $this->db->query(
            "SELECT id_tipo, nombre FROM TipoTarea ORDER BY nombre ASC"
        )->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addTipoTarea(string $nombre): int
    {
        $stmt = $this->db->prepare(
            "INSERT INTO TipoTarea (nombre) VALUES (?)"
        );
        $stmt->execute([trim($nombre)]);
        return (int) $this->db->lastInsertId();
    }

    public function editTipoTarea(int $id, string $nombre): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE TipoTarea SET nombre = ? WHERE id_tipo = ?"
        );
        return $stmt->execute([trim($nombre), $id]);
    }

    public function deleteTipoTarea(int $id): bool
    {
        // Desvincula tareas que usaban este tipo antes de borrar
        $this->db->prepare(
            "UPDATE Tarea SET id_tipo = NULL WHERE id_tipo = ?"
        )->execute([$id]);

        return $this->db->prepare(
            "DELETE FROM TipoTarea WHERE id_tipo = ?"
        )->execute([$id]);
    }
}
