<div>

  <!-- MOBILE TOPBAR (FIXED: NOT DARK ANYMORE) -->
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

  <!-- DESKTOP SIDEBAR -->
  <div class="sidebar d-none d-lg-flex flex-column justify-content-between">

    <div>

      <h4 class="p-3">Alcanalytics</h4>

      <!-- DASHBOARD -->
      <a href="<?= RUTA_URL ?>/home/index"
         class="<?= ($_SERVER['REQUEST_URI'] == '/home/index') ? 'active' : '' ?>">
        <i class="bi bi-house me-2"></i>Inicio
      </a>

      <!-- FICHAJES -->
      <a href="<?= RUTA_URL ?>/Fichaje/index"
         class="<?= ($_SERVER['REQUEST_URI'] == '/Fichaje/index') ? 'active' : '' ?>">
        <i class="bi bi-clock-history me-2"></i>Fichaje
      </a>

      <!-- DETALLE -->
      <a href="<?= RUTA_URL ?>/Fichaje/detalle"
         class="<?= ($_SERVER['REQUEST_URI'] == '/Fichaje/detalle') ? 'active' : '' ?>">
        <i class="bi bi-list-check me-2"></i>Detalle de Fichajes
      </a>

      <!-- ADMIN -->
      <?php if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'Administrador'): ?>

        <a href="<?= RUTA_URL ?>/empleado/index"
           class="<?= ($_SERVER['REQUEST_URI'] == '/empleado/index') ? 'active' : '' ?>">
          <i class="bi bi-people me-2"></i>Empleados
        </a>

      <?php endif; ?>

      <!-- JORNADAS -->
      <a data-bs-toggle="collapse" href="#jornadasMenu" role="button">
        <i class="bi bi-kanban me-2"></i>Jornadas
        <i class="bi bi-chevron-down float-end"></i>
      </a>

      <div class="collapse ps-3" id="jornadasMenu">

        <a href="<?= RUTA_URL ?>/Jornadas/index"
           class="<?= ($_SERVER['REQUEST_URI'] == '/Jornadas/index') ? 'active' : '' ?>">
          <i class="bi bi-list-check me-2"></i>Gestión de jornadas
        </a>

        <?php if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'Administrador'): ?>

          <a href="<?= RUTA_URL ?>/AsignarJornadas/index"
             class="<?= ($_SERVER['REQUEST_URI'] == '/AsignarJornadas/index') ? 'active' : '' ?>">
            <i class="bi bi-person-check me-2"></i>Asignar jornadas
          </a>

        <?php endif; ?>

      </div>

      <!-- INFORMES -->
      <a href="<?= RUTA_URL ?>/Informes/index"
         class="<?= ($_SERVER['REQUEST_URI'] == '/Informes/index') ? 'active' : '' ?>">
        <i class="bi bi-file-text me-2"></i>Informes
      </a>

      <!-- TAREAS -->
      <a href="<?= RUTA_URL ?>/RegistroTareas/index"
         class="<?= ($_SERVER['REQUEST_URI'] == '/RegistroTareas/index') ? 'active' : '' ?>">
        <i class="bi bi-list-check me-2"></i>Registro de Tareas
      </a>

      <!-- VACACIONES -->
      <a href="<?= RUTA_URL ?>/Vacaciones/index"
         class="<?= ($_SERVER['REQUEST_URI'] == '/Vacaciones/index') ? 'active' : '' ?>">
        <i class="bi bi-calendar me-2"></i>Vacaciones
      </a>

      <!-- AUSENCIAS -->
      <a data-bs-toggle="collapse" href="#bajasMenu" role="button">
        <i class="bi bi-person-dash me-2"></i>Ausencias
        <i class="bi bi-chevron-down float-end"></i>
      </a>

      <div class="collapse ps-3" id="bajasMenu">

        <a href="<?= RUTA_URL ?>/Ausencias/index"
           class="<?= ($_SERVER['REQUEST_URI'] == '/Ausencias/index') ? 'active' : '' ?>">
          <i class="bi bi-calendar-x me-2"></i>Ausencias
        </a>

        <a href="<?= RUTA_URL ?>/Bajas/visualizar"
           class="<?= ($_SERVER['REQUEST_URI'] == '/Bajas/visualizar') ? 'active' : '' ?>">
          <i class="bi bi-eye me-2"></i>Gestión
        </a>

      </div>

      <!-- ESTADISTICAS -->
      <?php if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'Administrador'): ?>

        <a data-bs-toggle="collapse" href="#reportesMenu" role="button">
          <i class="bi bi-bar-chart me-2"></i>Estadísticas
          <i class="bi bi-chevron-down float-end"></i>
        </a>

        <div class="collapse ps-3" id="reportesMenu">

          <a href="#"><i class="bi bi-speedometer2 me-2"></i>Resumen general</a>
          <a href="#"><i class="bi bi-clock me-2"></i>Fichajes</a>
          <a href="#"><i class="bi bi-stopwatch me-2"></i>Horas trabajadas</a>
          <a href="#"><i class="bi bi-exclamation-circle me-2"></i>Retrasos</a>
          <a href="#"><i class="bi bi-pie-chart me-2"></i>Actividad</a>

        </div>

      <?php endif; ?>

      <!-- INCIDENCIAS -->
      <a href="<?= RUTA_URL ?>/incidencias/index"
         class="<?= ($_SERVER['REQUEST_URI'] == '/incidencias/index') ? 'active' : '' ?>">
        <i class="bi bi-exclamation-triangle me-2"></i>Incidencias
      </a>

      <!-- CONFIG -->
      <a href="#">
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

  <!-- MOBILE SIDEBAR (FIXED: SAME MENU INCLUDED) -->
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

      <!-- SAME EXACT MENU (NO ITEMS REMOVED) -->

      <h4 class="p-3">Alcanalytics</h4>

      <a href="<?= RUTA_URL ?>/home/index">Inicio</a>
      <a href="<?= RUTA_URL ?>/Fichaje/index">Fichaje</a>
      <a href="<?= RUTA_URL ?>/Fichaje/detalle">Detalle de Fichajes</a>

      <?php if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'Administrador'): ?>
        <a href="<?= RUTA_URL ?>/empleado/index">Empleados</a>
      <?php endif; ?>

      <a href="<?= RUTA_URL ?>/Jornadas/index">Jornadas</a>

      <a href="<?= RUTA_URL ?>/Informes/index">Informes</a>

      <a href="<?= RUTA_URL ?>/RegistroTareas/index">Registro de Tareas</a>

      <a href="<?= RUTA_URL ?>/Vacaciones/index">Vacaciones</a>

      <a href="<?= RUTA_URL ?>/Ausencias/index">Ausencias</a>

      <a href="<?= RUTA_URL ?>/Bajas/visualizar">Gestión Ausencias</a>

      <?php if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'Administrador'): ?>
        <a href="#">Estadísticas</a>
      <?php endif; ?>

      <a href="<?= RUTA_URL ?>/incidencias/index">Incidencias</a>

      <a href="#">Configuración</a>

      <div class="p-3">
        <a href="<?= RUTA_URL ?>/login/logout" class="text-danger">
          Cerrar sesión
        </a>
      </div>

    </div>

  </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>