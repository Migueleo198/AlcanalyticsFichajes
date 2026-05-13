<?php
// Strip query string for reliable active detection
$currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?? '';

$jornadasActivo     = strpos($currentPath, '/Jornadas') === 0 || strpos($currentPath, '/AsignarJornadas') === 0;
$ausenciasActivo    = strpos($currentPath, '/Ausencias') === 0 || strpos($currentPath, '/Bajas') === 0;
$estadisticasActivo = strpos($currentPath, '/Estadisticas') === 0;

function sideActive(string $path, string $currentPath): string {
    return $currentPath === $path ? 'active' : '';
}
?>
<div>

  <!-- ===================== -->
  <!-- MOBILE TOPBAR -->
  <!-- ===================== -->
  <nav class="mobile-topbar d-lg-none px-3">

    <a class="navbar-brand fw-bold" href="#">
      Alcanalytics
    </a>

    <button type="button"
            data-bs-toggle="offcanvas"
            data-bs-target="#mobileSidebar"
            aria-controls="mobileSidebar">
      <i class="bi bi-list"></i>
    </button>

  </nav>

  <!-- ===================== -->
  <!-- DESKTOP SIDEBAR -->
  <!-- ===================== -->
  <div class="sidebar d-none d-lg-flex flex-column justify-content-between">

    <div>

      <h4 class="p-3">Alcanalytics</h4>

      <!-- DASHBOARD -->
      <a href="<?= RUTA_URL ?>/home/index"
         class="<?= sideActive('/home/index', $currentPath) ?>">
        <i class="bi bi-house me-2"></i>Inicio
      </a>

      <!-- FICHAJES -->
      <a href="<?= RUTA_URL ?>/Fichaje/index"
         class="<?= sideActive('/Fichaje/index', $currentPath) ?>">
        <i class="bi bi-clock-history me-2"></i>Fichaje
      </a>

      <!-- DETALLE -->
      <a href="<?= RUTA_URL ?>/Fichaje/detalle"
         class="<?= sideActive('/Fichaje/detalle', $currentPath) ?>">
        <i class="bi bi-list-check me-2"></i>Detalle de Fichajes
      </a>

      <!-- ADMIN -->
      <?php if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'Administrador'): ?>
        <a href="<?= RUTA_URL ?>/empleado/index"
           class="<?= sideActive('/empleado/index', $currentPath) ?>">
          <i class="bi bi-people me-2"></i>Empleados
        </a>
      <?php endif; ?>

      <!-- JORNADAS -->
      <a <?= !$jornadasActivo ? 'data-bs-toggle="collapse" href="#jornadasMenu"' : '' ?>
         role="button"
         aria-expanded="<?= $jornadasActivo ? 'true' : 'false' ?>"
         class="<?= $jornadasActivo ? 'parent-active' : '' ?>">
        <i class="bi bi-kanban me-2"></i>Jornadas
        <i class="bi bi-chevron-down float-end"></i>
      </a>

      <div class="collapse ps-3 <?= $jornadasActivo ? 'show' : '' ?>"
           id="jornadasMenu"
           <?= $jornadasActivo ? 'style="display:block"' : '' ?>>

        <a href="<?= RUTA_URL ?>/Jornadas/index"
           class="<?= sideActive('/Jornadas/index', $currentPath) ?>">
          <i class="bi bi-calendar-week me-2"></i>Gestión de jornadas
        </a>

        <?php if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'Administrador'): ?>
          <a href="<?= RUTA_URL ?>/AsignarJornadas/index"
             class="<?= sideActive('/AsignarJornadas/index', $currentPath) ?>">
            <i class="bi bi-person-check me-2"></i>Asignar jornadas
          </a>
        <?php endif; ?>

      </div>

      <!-- INFORMES -->
      <a href="<?= RUTA_URL ?>/Informes/index"
         class="<?= sideActive('/Informes/index', $currentPath) ?>">
        <i class="bi bi-file-earmark-text me-2"></i>Informes
      </a>

      <!-- TAREAS -->
      <a href="<?= RUTA_URL ?>/RegistroTareas/index"
         class="<?= sideActive('/RegistroTareas/index', $currentPath) ?>">
        <i class="bi bi-check2-square me-2"></i>Tareas
      </a>

      <!-- VACACIONES -->
      <a href="<?= RUTA_URL ?>/Vacaciones/index"
         class="<?= sideActive('/Vacaciones/index', $currentPath) ?>">
        <i class="bi bi-airplane me-2"></i>Vacaciones
      </a>

      <!-- AUSENCIAS -->
      <a <?= !$ausenciasActivo ? 'data-bs-toggle="collapse" href="#bajasMenu"' : '' ?>
         role="button"
         aria-expanded="<?= $ausenciasActivo ? 'true' : 'false' ?>"
         class="<?= $ausenciasActivo ? 'parent-active' : '' ?>">
        <i class="bi bi-person-dash me-2"></i>Ausencias
        <i class="bi bi-chevron-down float-end"></i>
      </a>

      <div class="collapse ps-3 <?= $ausenciasActivo ? 'show' : '' ?>"
           id="bajasMenu"
           <?= $ausenciasActivo ? 'style="display:block"' : '' ?>>

        <a href="<?= RUTA_URL ?>/Ausencias/index"
           class="<?= sideActive('/Ausencias/index', $currentPath) ?>">
          <i class="bi bi-calendar-x me-2"></i>Ausencias
        </a>

        <a href="<?= RUTA_URL ?>/Bajas/visualizar"
           class="<?= ($currentPath === '/Bajas/visualizar' || $currentPath === '/Bajas/gestionar') ? 'active' : '' ?>">
          <i class="bi bi-clipboard-data me-2"></i>Gestión
        </a>

      </div>

      <!-- ESTADÍSTICAS -->
      <?php if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'Administrador'): ?>

        <a <?= !$estadisticasActivo ? 'data-bs-toggle="collapse" href="#reportesMenu"' : '' ?>
           role="button"
           aria-expanded="<?= $estadisticasActivo ? 'true' : 'false' ?>"
           class="<?= $estadisticasActivo ? 'parent-active' : '' ?>">
          <i class="bi bi-bar-chart me-2"></i>Estadísticas
          <i class="bi bi-chevron-down float-end"></i>
        </a>

        <div class="collapse ps-3 <?= $estadisticasActivo ? 'show' : '' ?>"
             id="reportesMenu"
             <?= $estadisticasActivo ? 'style="display:block"' : '' ?>>

          <a href="<?= RUTA_URL ?>/Estadisticas/resumen"
             class="<?= sideActive('/Estadisticas/resumen', $currentPath) ?>">
            <i class="bi bi-grid-fill me-2"></i>Resumen
          </a>

          <a href="<?= RUTA_URL ?>/Estadisticas/fichajes"
             class="<?= sideActive('/Estadisticas/fichajes', $currentPath) ?>">
            <i class="bi bi-clock-history me-2"></i>Fichajes
          </a>

          <a href="<?= RUTA_URL ?>/Estadisticas/horas"
             class="<?= sideActive('/Estadisticas/horas', $currentPath) ?>">
            <i class="bi bi-hourglass-split me-2"></i>Horas
          </a>

          <a href="<?= RUTA_URL ?>/Estadisticas/retrasos"
             class="<?= sideActive('/Estadisticas/retrasos', $currentPath) ?>">
            <i class="bi bi-alarm me-2"></i>Retrasos
          </a>

          <a href="<?= RUTA_URL ?>/Estadisticas/actividad"
             class="<?= sideActive('/Estadisticas/actividad', $currentPath) ?>">
            <i class="bi bi-activity me-2"></i>Actividad
          </a>

        </div>

      <?php endif; ?>

      <!-- INCIDENCIAS -->
      <a href="<?= RUTA_URL ?>/incidencias/index"
         class="<?= sideActive('/incidencias/index', $currentPath) ?>">
        <i class="bi bi-exclamation-triangle me-2"></i>Incidencias
      </a>

      <!-- CONFIG -->
      <a href="<?= RUTA_URL ?>/Configuracion/index"
         class="<?= sideActive('/Configuracion/index', $currentPath) ?>">
        <i class="bi bi-gear me-2"></i>Configuración
      </a>

    </div>

    <!-- LOGOUT -->
    <div class="p-3">
      <a href="<?= RUTA_URL ?>/login/logout" class="text-danger">
        <i class="bi bi-box-arrow-right me-2"></i>Cerrar sesión
      </a>
    </div>

  </div>

  <!-- ===================== -->
  <!-- MOBILE OFFCANVAS -->
  <!-- ===================== -->
  <div class="offcanvas offcanvas-start"
       tabindex="-1"
       id="mobileSidebar">

    <div class="offcanvas-header">
      <h5 class="offcanvas-title">Alcanalytics</h5>

      <button type="button"
              class="btn-close"
              data-bs-dismiss="offcanvas"></button>
    </div>

    <div class="offcanvas-body sidebar-mobile">

      <!-- DASHBOARD -->
      <a href="<?= RUTA_URL ?>/home/index"
         class="<?= sideActive('/home/index', $currentPath) ?>">
        <i class="bi bi-house me-2"></i>Inicio
      </a>

      <!-- FICHAJE -->
      <a href="<?= RUTA_URL ?>/Fichaje/index"
         class="<?= sideActive('/Fichaje/index', $currentPath) ?>">
        <i class="bi bi-clock-history me-2"></i>Fichaje
      </a>

      <!-- DETALLE -->
      <a href="<?= RUTA_URL ?>/Fichaje/detalle"
         class="<?= sideActive('/Fichaje/detalle', $currentPath) ?>">
        <i class="bi bi-list-check me-2"></i>Detalle de Fichajes
      </a>

      <!-- ADMIN -->
      <?php if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'Administrador'): ?>
        <a href="<?= RUTA_URL ?>/empleado/index"
           class="<?= sideActive('/empleado/index', $currentPath) ?>">
          <i class="bi bi-people me-2"></i>Empleados
        </a>
      <?php endif; ?>

      <!-- JORNADAS -->
      <a <?= !$jornadasActivo ? 'data-bs-toggle="collapse" href="#mobileJornadasMenu"' : '' ?>
         role="button"
         aria-expanded="<?= $jornadasActivo ? 'true' : 'false' ?>"
         class="<?= $jornadasActivo ? 'parent-active' : '' ?>">
        <i class="bi bi-kanban me-2"></i>Jornadas
        <i class="bi bi-chevron-down float-end"></i>
      </a>

      <div class="collapse ps-3 <?= $jornadasActivo ? 'show' : '' ?>"
           id="mobileJornadasMenu"
           <?= $jornadasActivo ? 'style="display:block"' : '' ?>>

        <a href="<?= RUTA_URL ?>/Jornadas/index"
           class="<?= sideActive('/Jornadas/index', $currentPath) ?>">
          <i class="bi bi-calendar-week me-2"></i>Gestión de jornadas
        </a>

        <?php if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'Administrador'): ?>
          <a href="<?= RUTA_URL ?>/AsignarJornadas/index"
             class="<?= sideActive('/AsignarJornadas/index', $currentPath) ?>">
            <i class="bi bi-person-check me-2"></i>Asignar jornadas
          </a>
        <?php endif; ?>

      </div>

      <!-- INFORMES -->
      <a href="<?= RUTA_URL ?>/Informes/index"
         class="<?= sideActive('/Informes/index', $currentPath) ?>">
        <i class="bi bi-file-earmark-text me-2"></i>Informes
      </a>

      <!-- TAREAS -->
      <a href="<?= RUTA_URL ?>/RegistroTareas/index"
         class="<?= sideActive('/RegistroTareas/index', $currentPath) ?>">
        <i class="bi bi-check2-square me-2"></i>Tareas
      </a>

      <!-- VACACIONES -->
      <a href="<?= RUTA_URL ?>/Vacaciones/index"
         class="<?= sideActive('/Vacaciones/index', $currentPath) ?>">
        <i class="bi bi-airplane me-2"></i>Vacaciones
      </a>

      <!-- AUSENCIAS -->
      <a <?= !$ausenciasActivo ? 'data-bs-toggle="collapse" href="#mobileBajasMenu"' : '' ?>
         role="button"
         aria-expanded="<?= $ausenciasActivo ? 'true' : 'false' ?>"
         class="<?= $ausenciasActivo ? 'parent-active' : '' ?>">
        <i class="bi bi-person-dash me-2"></i>Ausencias
        <i class="bi bi-chevron-down float-end"></i>
      </a>

      <div class="collapse ps-3 <?= $ausenciasActivo ? 'show' : '' ?>"
           id="mobileBajasMenu"
           <?= $ausenciasActivo ? 'style="display:block"' : '' ?>>

        <a href="<?= RUTA_URL ?>/Ausencias/index"
           class="<?= sideActive('/Ausencias/index', $currentPath) ?>">
          <i class="bi bi-calendar-x me-2"></i>Ausencias
        </a>

        <a href="<?= RUTA_URL ?>/Bajas/visualizar"
           class="<?= ($currentPath === '/Bajas/visualizar' || $currentPath === '/Bajas/gestionar') ? 'active' : '' ?>">
          <i class="bi bi-clipboard-data me-2"></i>Gestión
        </a>

      </div>

      <!-- ESTADÍSTICAS -->
      <?php if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'Administrador'): ?>

        <a <?= !$estadisticasActivo ? 'data-bs-toggle="collapse" href="#mobileReportesMenu"' : '' ?>
           role="button"
           aria-expanded="<?= $estadisticasActivo ? 'true' : 'false' ?>"
           class="<?= $estadisticasActivo ? 'parent-active' : '' ?>">
          <i class="bi bi-bar-chart me-2"></i>Estadísticas
          <i class="bi bi-chevron-down float-end"></i>
        </a>

        <div class="collapse ps-3 <?= $estadisticasActivo ? 'show' : '' ?>"
             id="mobileReportesMenu"
             <?= $estadisticasActivo ? 'style="display:block"' : '' ?>>

          <a href="<?= RUTA_URL ?>/Estadisticas/resumen"
             class="<?= sideActive('/Estadisticas/resumen', $currentPath) ?>">
            <i class="bi bi-grid-fill me-2"></i>Resumen
          </a>

          <a href="<?= RUTA_URL ?>/Estadisticas/fichajes"
             class="<?= sideActive('/Estadisticas/fichajes', $currentPath) ?>">
            <i class="bi bi-clock-history me-2"></i>Fichajes
          </a>

          <a href="<?= RUTA_URL ?>/Estadisticas/horas"
             class="<?= sideActive('/Estadisticas/horas', $currentPath) ?>">
            <i class="bi bi-hourglass-split me-2"></i>Horas
          </a>

          <a href="<?= RUTA_URL ?>/Estadisticas/retrasos"
             class="<?= sideActive('/Estadisticas/retrasos', $currentPath) ?>">
            <i class="bi bi-alarm me-2"></i>Retrasos
          </a>

          <a href="<?= RUTA_URL ?>/Estadisticas/actividad"
             class="<?= sideActive('/Estadisticas/actividad', $currentPath) ?>">
            <i class="bi bi-activity me-2"></i>Actividad
          </a>

        </div>

      <?php endif; ?>

      <!-- INCIDENCIAS -->
      <a href="<?= RUTA_URL ?>/incidencias/index"
         class="<?= sideActive('/incidencias/index', $currentPath) ?>">
        <i class="bi bi-exclamation-triangle me-2"></i>Incidencias
      </a>

      <!-- CONFIG -->
      <a href="<?= RUTA_URL ?>/Configuracion/index"
         class="<?= sideActive('/Configuracion/index', $currentPath) ?>">
        <i class="bi bi-gear me-2"></i>Configuración
      </a>

      <hr>

      <!-- LOGOUT -->
      <a href="<?= RUTA_URL ?>/login/logout" class="text-danger">
        <i class="bi bi-box-arrow-right me-2"></i>Cerrar sesión
      </a>

    </div>

  </div>

</div>
