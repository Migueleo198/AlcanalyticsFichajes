<?php require_once RUTA_APP . '/views/inc/headerHome.php' ?>

<!-- FULLCALENDAR -->
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css' rel='stylesheet' />

<div class="main-wrapper">

<?php require_once RUTA_APP . '/views/inc/sidebar.php'; ?>

<div class="content">

  <!-- TOPBAR -->
  <div class="topbar d-flex justify-content-between align-items-center">
    <h5 class="mb-0">Calendario de Vacaciones</h5>

    <div class="d-flex align-items-center">
      <button class="btn btn-primary btn-sm me-3" id="btnNuevaVacacion">
        <i class="bi bi-plus"></i> Solicitar vacaciones
      </button>

      <i class="bi bi-person-circle fs-5 profile"></i>
    </div>
  </div>

  <!-- CALENDARIO -->
  <div class="card card-custom p-3">
    <div id='calendar'></div>
  </div>

</div>
</div>

<!-- MODAL VACACIONES -->
<div class="modal fade" id="vacacionesModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">Solicitar vacaciones</h5>
        <button class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <form action="<?= RUTA_URL ?>/vacaciones/add" method="POST">

        <div class="modal-body">

          <div class="mb-3">
            <label>Fecha inicio</label>
            <input type="date" name="fecha_inicio" id="fecha_inicio" class="form-control" required>
          </div>

          <div class="mb-3">
            <label>Fecha fin</label>
            <input type="date" name="fecha_fin" id="fecha_fin" class="form-control" required>
          </div>

          <div class="mb-3">
            <label>Comentario</label>
            <textarea name="comentario" class="form-control"></textarea>
          </div>

        </div>

        <div class="modal-footer">
          <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button class="btn btn-primary">Guardar</button>
        </div>

      </form>

    </div>
  </div>
</div>

<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js'></script>
<script>
const RUTA_URL = "<?= RUTA_URL ?>";
</script>
<script type="module" src="<?= RUTA_URL ?>/js/infrastructure/infrastructureVacaciones.js"></script>

<?php require_once RUTA_APP . '/views/inc/footer.php' ?>