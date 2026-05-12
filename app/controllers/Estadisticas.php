<?php

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/EstadisticasModel.php';

class Estadisticas extends Controller {

    private function checkAdmin() {
        session_start();
        if (!isset($_SESSION['id_usuario'])) {
            header("Location: " . RUTA_URL . "/login");
            exit;
        }
        if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'Administrador') {
            header("Location: " . RUTA_URL . "/home/index");
            exit;
        }
    }

    private function getModel() {
        $db = new Database();
        $conexion = $db->conectar();
        return new EstadisticasModel($conexion);
    }

    private function getFiltroFechas() {
        $desde = $_GET['desde'] ?? date('Y-m-01');
        $hasta = $_GET['hasta'] ?? date('Y-m-d');
        return [$desde, $hasta];
    }

    // ============================================================
    // RESUMEN
    // ============================================================
    public function resumen() {
        $this->checkAdmin();
        $modelo = $this->getModel();

        $datos = [
            'title'            => 'Estadísticas - Resumen',
            'resumen'          => $modelo->getResumenGeneral(),
            'fichajesMeses'    => $modelo->getFichajesUltimosMeses(6),
            'diaSemana'        => $modelo->getFichajesPorDiaSemana(),
            'topEmpleados'     => $modelo->getTopEmpleadosHoras(5),
        ];

        $this->load_view('estadisticasResumen', $datos);
    }

    // ============================================================
    // FICHAJES
    // ============================================================
    public function fichajes() {
        $this->checkAdmin();
        [$desde, $hasta] = $this->getFiltroFechas();
        $modelo = $this->getModel();

        $datos = [
            'title'             => 'Estadísticas - Fichajes',
            'desde'             => $desde,
            'hasta'             => $hasta,
            'resumenFichajes'   => $modelo->getResumenFichajes($desde, $hasta),
            'porEstado'         => $modelo->getFichajesPorEstado($desde, $hasta),
            'diarios'           => $modelo->getFichajesDiarios($desde, $hasta),
            'porEmpleado'       => $modelo->getFichajesPorEmpleado($desde, $hasta),
        ];

        $this->load_view('estadisticasFichajes', $datos);
    }

    // ============================================================
    // HORAS
    // ============================================================
    public function horas() {
        $this->checkAdmin();
        [$desde, $hasta] = $this->getFiltroFechas();
        $modelo = $this->getModel();

        $datos = [
            'title'          => 'Estadísticas - Horas',
            'desde'          => $desde,
            'hasta'          => $hasta,
            'resumenHoras'   => $modelo->getResumenHoras($desde, $hasta),
            'horasDiarias'   => $modelo->getHorasPorDia($desde, $hasta),
            'horasEmpleado'  => $modelo->getHorasPorEmpleado($desde, $hasta),
        ];

        $this->load_view('estadisticasHoras', $datos);
    }

    // ============================================================
    // RETRASOS
    // ============================================================
    public function retrasos() {
        $this->checkAdmin();
        [$desde, $hasta] = $this->getFiltroFechas();
        $modelo = $this->getModel();

        $datos = [
            'title'            => 'Estadísticas - Retrasos',
            'desde'            => $desde,
            'hasta'            => $hasta,
            'resumenRetrasos'  => $modelo->getResumenRetrasos($desde, $hasta),
            'retrasosDiarios'  => $modelo->getRetrasosPorDia($desde, $hasta),
            'retrasosEmpleado' => $modelo->getRetrasosPorEmpleado($desde, $hasta),
            'horaEntrada'      => $modelo->getHoraEntradaMedia($desde, $hasta),
        ];

        $this->load_view('estadisticasRetrasos', $datos);
    }

    // ============================================================
    // ACTIVIDAD
    // ============================================================
    public function actividad() {
        $this->checkAdmin();
        [$desde, $hasta] = $this->getFiltroFechas();
        $modelo = $this->getModel();

        $datos = [
            'title'            => 'Estadísticas - Actividad',
            'desde'            => $desde,
            'hasta'            => $hasta,
            'resumenActividad' => $modelo->getResumenActividad($desde, $hasta),
            'actividadDiaria'  => $modelo->getActividadDiaria($desde, $hasta),
            'porHora'          => $modelo->getActividadPorHora($desde, $hasta),
            'ausenciasVacas'   => $modelo->getAusenciasYVacaciones($desde, $hasta),
            'presencia'        => $modelo->getPresenciaPorEmpleado($desde, $hasta),
        ];

        $this->load_view('estadisticasActividad', $datos);
    }
}
