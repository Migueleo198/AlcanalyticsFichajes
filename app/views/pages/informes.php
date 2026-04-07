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
      <i class="bi bi-bell fs-5" id="notificacionesIcon" data-bs-toggle="dropdown" style="cursor:pointer;"></i>
      
      <!-- Badge -->
      <span id="contadorNotificaciones" 
      class="position-absolute top-0 start-100 translate-middle bg-danger text-white badge-fix">
       3
      </span>

      <!-- Dropdown -->
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
    <div class="col-md-4">
      <div class="card card-custom p-3 cardhov">
        <h6>Total empleados</h6>
        <h3><?= count($datos['usuarios']) ?></h3>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card card-custom p-3 cardhov">
        <h6>Informes disponibles</h6>
        <h3>3</h3>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card card-custom p-3 cardhov">
        <h6>Exportación</h6>
        <h3>PDF</h3>
      </div>
    </div>
  </div>

  <!-- FORM -->
  <div class="card card-custom p-4">

    <div class="d-flex justify-content-between mb-3">
      <h5>Generar informe</h5>
      <button class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-file-earmark-text"></i> Opciones
      </button>
    </div>

    <div class="row g-3">

      <div class="col-md-4">
        <label class="form-label">Desde</label>
        <input type="date" id="desde" class="form-control">
      </div>

      <div class="col-md-4">
        <label class="form-label">Hasta</label>
        <input type="date" id="hasta" class="form-control">
      </div>

      <div class="col-md-4">
        <label class="form-label">Usuario</label>
        <select id="usuario" class="form-select">
          <option value="">Todos</option>

          <?php foreach ($datos['usuarios'] as $u): ?>
            <option value="<?= $u['id_usuario'] ?>">
              <?= $u['nombre'] . " " . $u['apellidos'] ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

    </div>

    <!-- BOTONES -->
    <div class="mt-4 d-flex gap-2">
      <button id="btnGenerarInforme" class="btn btn-primary">
        <i class="bi bi-download"></i> Generar informe
      </button>

      <button id="btnLimpiarFiltros" class="btn btn-outline-secondary">
        Limpiar
      </button>
    </div>

  </div>

</div>

</div>


<script>
    const RUTA_URL = "<?= RUTA_URL ?>";
</script>
<!-- JS EXTERNO -->
<script type='module' src="<?= RUTA_URL ?>/js/infrastructure/infrastructureInformes.js"></script>

<?php require_once RUTA_APP . '/views/inc/footer.php'; ?>