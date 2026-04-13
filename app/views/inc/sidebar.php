<div class="sidebar d-flex flex-column justify-content-between">

  <div>

    <h4 class="p-3">Alcanalytics</h4>

    <!-- ===================== -->
    <!-- DASHBOARD -->
    <!-- ===================== -->
    <a href="<?= RUTA_URL ?>/home/index"
       class="<?= ($_SERVER['REQUEST_URI'] == '/home/index') ? 'active' : '' ?>">
      <i class="bi bi-house me-2"></i>Inicio
    </a>

    <!-- ===================== -->
    <!-- FICHAJES -->
    <!-- ===================== -->
    <a href="<?= RUTA_URL ?>/Fichaje/index"
       class="<?= ($_SERVER['REQUEST_URI'] == '/Fichaje/index') ? 'active' : '' ?>">
      <i class="bi bi-clock-history me-2"></i>Fichaje
    </a>

    <!-- NUEVO: DETALLE (PAUSAS) -->
    <a href="<?= RUTA_URL ?>/Fichaje/detalle"
       class="<?= ($_SERVER['REQUEST_URI'] == '/Fichaje/detalle') ? 'active' : '' ?>">
      <i class="bi bi-list-check me-2"></i>Detalle de Jornadas
    </a>

    <!-- ===================== -->
    <!-- ADMIN -->
    <!-- ===================== -->
    <?php if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'Administrador'): ?>

      <a href="<?= RUTA_URL ?>/empleado/index"
         class="<?= ($_SERVER['REQUEST_URI'] == '/empleado/index') ? 'active' : '' ?>">
        <i class="bi bi-people me-2"></i>Empleados
      </a>

    <?php endif; ?>

    <!-- ===================== -->
    <!-- GESTIÓN -->
    <!-- ===================== -->
    <a href="<?= RUTA_URL ?>/Jornadas/index"
       class="<?= ($_SERVER['REQUEST_URI'] == '/Jornadas/index') ? 'active' : '' ?>">
      <i class="bi bi-kanban me-2"></i>Gestión de Jornadas
    </a>

    <a href="<?= RUTA_URL ?>/Informes/index"
       class="<?= ($_SERVER['REQUEST_URI'] == '/Informes/index') ? 'active' : '' ?>">
      <i class="bi bi-file-text me-2"></i>Informes
    </a>

    <a href="<?= RUTA_URL ?>/RegistroTareas/index"
       class="<?= ($_SERVER['REQUEST_URI'] == '/RegistroTareas/index') ? 'active' : '' ?>">
      <i class="bi bi-list-check me-2"></i>Registro de Tareas
    </a>

    <a href="<?= RUTA_URL ?>/Vacaciones/index"
       class="<?= ($_SERVER['REQUEST_URI'] == '/Vacaciones/index') ? 'active' : '' ?>">
      <i class="bi bi-calendar me-2"></i>Vacaciones
    </a>

    <!-- ===================== -->
    <!-- AUSENCIAS -->
    <!-- ===================== -->
    <a data-bs-toggle="collapse" href="#bajasMenu" role="button"
       aria-expanded="false" aria-controls="bajasMenu">
      <i class="bi bi-person-dash me-2"></i>Ausencias y Bajas
      <i class="bi bi-chevron-down float-end"></i>
    </a>

    <div class="collapse ps-3" id="bajasMenu">

      <a href="<?= RUTA_URL ?>/Bajas/index"
         class="<?= ($_SERVER['REQUEST_URI'] == '/Bajas/index') ? 'active' : '' ?>">
        <i class="bi bi-plus-circle me-2"></i>Bajas
      </a>

      <a href="<?= RUTA_URL ?>/Ausencias/index"
         class="<?= ($_SERVER['REQUEST_URI'] == '/Ausencias/index') ? 'active' : '' ?>">
        <i class="bi bi-calendar-x me-2"></i>Ausencias
      </a>

      <a href="<?= RUTA_URL ?>/Bajas/visualizar"
         class="<?= ($_SERVER['REQUEST_URI'] == '/Bajas/visualizar') ? 'active' : '' ?>">
        <i class="bi bi-eye me-2"></i>Gestión
      </a>

    </div>

    <!-- ===================== -->
    <!-- ESTADÍSTICAS (ADMIN) -->
    <!-- ===================== -->
    <?php if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'Administrador'): ?>

      <a data-bs-toggle="collapse" href="#reportesMenu" role="button"
         aria-expanded="false" aria-controls="reportesMenu">
        <i class="bi bi-bar-chart me-2"></i>Estadísticas
        <i class="bi bi-chevron-down float-end"></i>
      </a>

      <div class="collapse ps-3" id="reportesMenu">

        <a href="#">
          <i class="bi bi-speedometer2 me-2"></i>Resumen general
        </a>

        <a href="#">
          <i class="bi bi-clock me-2"></i>Fichajes
        </a>

        <a href="#">
          <i class="bi bi-stopwatch me-2"></i>Horas trabajadas
        </a>

        <a href="#">
          <i class="bi bi-exclamation-circle me-2"></i>Retrasos
        </a>

        <a href="#">
          <i class="bi bi-pie-chart me-2"></i>Actividad
        </a>

      </div>

    <?php endif; ?>

    <!-- ===================== -->
    <!-- INCIDENCIAS -->
    <!-- ===================== -->
    <a href="<?= RUTA_URL ?>/incidencias/index"
       class="<?= ($_SERVER['REQUEST_URI'] == '/incidencias/index') ? 'active' : '' ?>">
      <i class="bi bi-exclamation-triangle me-2"></i>Incidencias
    </a>

    <!-- ===================== -->
    <!-- CONFIG -->
    <!-- ===================== -->
    <a href="#">
      <i class="bi bi-gear me-2"></i>Configuración
    </a>

  </div>

  <!-- ===================== -->
  <!-- LOGOUT -->
  <!-- ===================== -->
  <div class="p-3">
    <a href="<?= RUTA_URL ?>/login/logout" class="text-danger">
      <i class="bi bi-box-arrow-right me-2"></i>Cerrar sesión
    </a>
  </div>

</div>