<?php require_once RUTA_APP . '/views/inc/headerHome.php'; ?>

<div class="main-wrapper">

<?php require_once RUTA_APP . '/views/inc/sidebar.php'; ?>

<div class="content">

  <!-- TOPBAR -->
  <div class="topbar d-flex justify-content-between align-items-center">
    <input type="text" id="buscadorTabla" class="form-control w-50" placeholder="Buscar informes...">

    <div class="d-flex align-items-center">

      <!-- NOTIFICACIONES -->
      <div class="dropdown me-3 position-relative">
        <i class="bi bi-bell fs-5 shake-loop" id="notificacionesIcon"
           data-bs-toggle="dropdown"
           style="cursor:pointer;"></i>

        <span id="contadorNotificaciones"
              class="position-absolute top-0 start-100 translate-middle bg-danger text-white badge-fix">
          3
        </span>

        <ul class="dropdown-menu dropdown-menu-end p-2" style="width:300px;" id="listaNotificaciones">
          <li class="fw-bold mb-2">Notificaciones</li>
          <li class="dropdown-item">Nuevo fichaje registrado</li>
          <li class="dropdown-item">Contrato por vencer</li>
          <li class="dropdown-item">Actualización completada</li>
        </ul>
      </div>

      <!-- PERFIL -->
      <i class="bi bi-person-circle fs-5 profile"
         data-user-id="<?= $_SESSION['id_usuario'] ?>"></i>
    </div>
  </div>

  <!-- HEADER -->
  <h3>Generación de Informes</h3>
  <p class="text-muted">Descarga informes de fichajes por usuario y rango de fechas</p>

  <!-- CARDS -->
 
<div class="row g-3 mb-4">

  <?php if ($_SESSION['rol'] === 'Administrador') { ?>
    <div class="col-md-4">
      <div class="card card-custom p-3 cardhov">
        <h6>Panel Admin</h6>
      </div>
    </div>
 

  <div class="col-md-4">
    <div class="card card-custom p-3 cardhov">
      <h6>Total empleados</h6>
      <h3><?= count($datos['usuarios']) ?></h3>
    </div>
  </div>
 <?php } ?>
  <div class="col-md-4">
    <div class="card card-custom p-3 cardhov">
      <h6>Exportación</h6>
      <h3>PDF</h3>
    </div>
  </div>

</div>

  <!-- FORM -->
  <div class="card card-custom p-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
      <h5 class="mb-0">Generar informe</h5>
      <button class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-file-earmark-text me-1"></i>Opciones
      </button>
    </div>

    <div class="row g-3">

      <div class="col-md-4">
        <label class="form-label fw-semibold">Desde <span class="text-danger">*</span></label>
        <input type="date" id="desde" class="form-control">
        <div class="mt-1 d-flex gap-1">
          <button id="btnSemanal" class="btn btn-outline-secondary btn-sm flex-fill">
            <i class="bi bi-calendar-week me-1"></i>Esta semana
          </button>
          <button id="btnMensual" class="btn btn-outline-secondary btn-sm flex-fill">
            <i class="bi bi-calendar-month me-1"></i>Este mes
          </button>
        </div>
      </div>

      <div class="col-md-4">
        <label class="form-label fw-semibold">Hasta <span class="text-danger">*</span></label>
        <input type="date" id="hasta" class="form-control">
        <small id="diasSeleccionados" class="text-muted mt-1 d-block"></small>
      </div>

      <div class="col-md-4">
        <label class="form-label fw-semibold">
          <?= $_SESSION['rol'] === 'Administrador' ? 'Empleados' : 'Empleado' ?>
        </label>
        <select id="usuario" class="form-select" multiple size="5">
          <?php if ($_SESSION['rol'] === 'Administrador'): ?>
            <?php foreach ($datos['usuarios'] as $u): ?>
              <option value="<?= $u['id_usuario'] ?>">
                <?= htmlspecialchars($u['nombre'] . ' ' . $u['apellidos']) ?>
              </option>
            <?php endforeach; ?>
          <?php else: ?>
            <option value="<?= htmlspecialchars($_SESSION['id_usuario']) ?>" selected>
              <?= htmlspecialchars($_SESSION['nombre'] ?? 'Mi usuario') ?>
            </option>
          <?php endif; ?>
        </select>
        <?php if ($_SESSION['rol'] === 'Administrador'): ?>
        <small class="text-muted">Ctrl / Cmd para seleccionar varios</small>
        <?php endif; ?>
      </div>

    </div>

    <!-- BOTONES DE ACCIÓN -->
    <div class="mt-4 d-flex gap-2 align-items-center flex-wrap">
      <button id="btnGenerarInforme" class="btn btn-primary px-4">
        <i class="bi bi-play-fill me-1"></i>Generar
      </button>
      <button id="btnLimpiarFiltros" class="btn btn-outline-secondary">
        <i class="bi bi-x me-1"></i>Limpiar
      </button>
      <span id="infoModo" class="text-muted small ms-2"></span>
    </div>

  </div>

</div>

</div>

<script>
    const USER_ROL = "<?= $_SESSION['rol'] ?? '' ?>";
    const USER_ID = "<?= $_SESSION['id_usuario'] ?? '' ?>";
</script>

<script>
    var RUTA_URL = "<?= RUTA_URL ?>";
</script>

<script type="module" src="<?= RUTA_URL ?>/js/infrastructure/infrastructureInformes.js"></script>

<?php require_once RUTA_APP . '/views/inc/footer.php'; ?>