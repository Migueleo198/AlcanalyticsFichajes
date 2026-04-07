<?php

class Database {

    private $host = 'localhost';
    private $port = '3306';
    private $dbname = 'Fichajes2';
    private $user = 'admin_fichajes2';
    private $pass = '6%DfdlBrud5$jVg8';

    public $conexion;

    public function conectar() {
        $this->conexion = null;

        try {
            $dsn = "mysql:host={$this->host};port={$this->port};dbname={$this->dbname};charset=utf8mb4";

            $this->conexion = new PDO($dsn, $this->user, $this->pass);

            // Configuración PRO
            $this->conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conexion->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            die("Error de conexión: " . $e->getMessage());
        }

        return $this->conexion;
    }
}