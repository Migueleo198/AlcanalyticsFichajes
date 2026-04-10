<div class="sidebar d-flex flex-column justify-content-between">

  <div>
    <h4 class="p-3">Alcanalytics</h4>

    <a href="<?= RUTA_URL ?>/home/index" class="<?= ($_SERVER['REQUEST_URI'] == '/home/index') ? 'active' : '' ?>">
      <i class="bi bi-house me-2"></i>Inicio
    </a>

    <a href="<?= RUTA_URL ?>/fichaje/index" class="<?= ($_SERVER['REQUEST_URI'] == '/fichaje/index') ? 'active' : '' ?>">
      <i class="bi bi-clock-history me-2"></i>Fichajes
    </a>

    <?php if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'Administrador'): ?>
    <a href="<?= RUTA_URL ?>/empleado/index" class="<?= ($_SERVER['REQUEST_URI'] == '/empleado/index') ? 'active' : '' ?>">
    <i class="bi bi-people me-2"></i>Empleados
      </a>
    <?php endif; ?>

     <a href="<?= RUTA_URL ?>/Jornadas/index" class="<?= ($_SERVER['REQUEST_URI'] == '/Jornadas/index') ? 'active' : '' ?>">
       <i class="bi bi-kanban me-2"></i>Gestión de Jornadas
     </a>

     <a href="<?= RUTA_URL ?>/Informes/index" class="<?= ($_SERVER['REQUEST_URI'] == '/Informes/index') ? 'active' : '' ?>">
       <i class="bi bi-file-text me-2"></i>Informes
     </a>

    
      <a href="<?= RUTA_URL ?>/RegistroTareas/index" class="<?= ($_SERVER['REQUEST_URI'] == '/RegistroTareas/index') ? 'active' : '' ?>">
       <i class="bi bi-list-check me-2"></i>Registro de Tareas
      </a>

      <a href="<?= RUTA_URL ?>/Vacaciones/index" class="<?= ($_SERVER['REQUEST_URI'] == '/Vacaciones/index') ? 'active' : '' ?>">
       <i class="bi bi-calendar me-2"></i>Gestión de Vacaciones
      </a>

       <a data-bs-toggle="collapse" href="#bajasMenu" role="button"
   aria-expanded="false" aria-controls="bajasMenu">
  <i class="bi bi-person-dash me-2"></i>Ausencias y Bajas
  <i class="bi bi-chevron-down float-end"></i>
</a>

<div class="collapse ps-3" id="bajasMenu">

  <a href="<?= RUTA_URL ?>/Bajas/index" class="<?= ($_SERVER['REQUEST_URI'] == '/Bajas/index') ? 'active' : '' ?>">
    <i class="bi bi-plus-circle me-2"></i>Solicitar baja
  </a>

  <a href="<?= RUTA_URL ?>/Ausencias/index" class="<?= ($_SERVER['REQUEST_URI'] == '/Ausencias/index') ? 'active' : '' ?>">
    <i class="bi bi-plus-circle me-2"></i>Solicitar Ausencia
  </a>

  <a href="<?= RUTA_URL ?>/Bajas/visualizar" class="<?= ($_SERVER['REQUEST_URI'] == '/Bajas/gestionar') ? 'active' : '' ?>">
    <i class="bi bi-eye me-2"></i>Gestionar
  </a>

</div>

    <?php if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'Administrador'): ?>
    <a data-bs-toggle="collapse" href="#reportesMenu" role="button"
       aria-expanded="false" aria-controls="reportesMenu">
      <i class="bi bi-bar-chart me-2"></i>Estadísticas y Métricas
      <i class="bi bi-chevron-down float-end"></i>
    </a>

    <div class="collapse ps-3" id="reportesMenu">

      <a href="#">
        <i class="bi bi-speedometer2 me-2"></i>Resumen general
      </a>

      <a href="#">
        <i class="bi bi-clock me-2"></i>Estadísticas de Fichajes
      </a>

      <a href="#">
        <i class="bi bi-stopwatch me-2"></i>Horas trabajadas
      </a>

      <a href="#">
        <i class="bi bi-exclamation-circle me-2"></i>Retrasos
      </a>

      <a href="#">
        <i class="bi bi-laptop me-2"></i>Actividad por dispositivo
      </a>

      <a href="#">
        <i class="bi bi-person-lines-fill me-2"></i>Estadísticas de empleados
      </a>
     
    </div>
 <?php endif; ?>
    <a href="<?= RUTA_URL ?>/incidencias/index" class="<?= ($_SERVER['REQUEST_URI'] == '/incidencias/index') ? 'active' : '' ?>">
      <i class="bi bi-exclamation-triangle me-2"></i>Registro de Incidencias
    </a>

    <a href="#">
      <i class="bi bi-gear me-2"></i>Configuración
    </a>
  </div>

  <div class="p-3">
    <a href="<?= RUTA_URL ?>/login/logout" class="text-danger">
      <i class="bi bi-box-arrow-right me-2"></i>Cerrar sesión
    </a>
  </div>

</div>